<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * ═══════════════════════════════════════════════════════════════
 *  ANNOUNCEMENT CONTROLLER — KETUA
 * ═══════════════════════════════════════════════════════════════
 *  Kelola pengumuman untuk seluruh anggota UKM.
 *
 *  Fitur:
 *   - List pengumuman dengan search, filter, pagination
 *   - Buat pengumuman baru (draft / published)
 *   - Kirim notifikasi broadcast ke target user (chunked, safe)
 *   - View detail dengan auto-increment read_count
 *   - Hapus dengan authorization (creator / owner / superadmin / ketua)
 *   - Cache statistik
 *   - Audit log untuk setiap aksi
 * ═══════════════════════════════════════════════════════════════
 */
class AnnouncementController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     |  CONSTANTS
     ═══════════════════════════════════════════════════════════ */
    private const PER_PAGE        = 15;
    private const BROADCAST_CHUNK = 200;
    private const PRIORITIES      = ['low', 'normal', 'high', 'urgent'];
    private const TARGET_ROLES    = ['anggota', 'pengurus', 'ketua', 'superadmin', 'owner'];
    private const CACHE_TTL       = 300; // 5 menit

    public function __construct(
        protected NotificationService $notifier
    ) {}

    /* ═══════════════════════════════════════════════════════════
     |  INDEX — List pengumuman
     ═══════════════════════════════════════════════════════════ */

    public function index(Request $request)
    {
        try {
            $query = Announcement::query()
                ->with(['author:id,name,avatar_url', 'updater:id,name']);

            /* ── Search ── */
            if ($request->filled('q')) {
                $q = trim($request->q);
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            }

            /* ── Filter prioritas ── */
            if ($request->filled('priority') && in_array($request->priority, self::PRIORITIES, true)) {
                $query->where('priority', $request->priority);
            }

            /* ── Filter target role ── */
            if ($request->filled('target_role') && in_array($request->target_role, self::TARGET_ROLES, true)) {
                $query->whereJsonContains('target_roles', $request->target_role);
            }

            /* ── Filter rentang waktu ── */
            if ($request->filled('range')) {
                match ($request->range) {
                    'today' => $query->whereDate('published_at', today()),
                    '7d'    => $query->where('published_at', '>=', now()->subDays(7)),
                    '30d'   => $query->where('published_at', '>=', now()->subDays(30)),
                    default => null,
                };
            }

            /* ── Sort ── */
            $query->orderByDesc('published_at')->orderByDesc('id');

            $announcements = $query->paginate(self::PER_PAGE)->withQueryString();

            /* ── Stats (cached) ── */
            $stats = Cache::remember('ketua.announcements.stats', self::CACHE_TTL, function () {
                try {
                    $base = Announcement::query();
                    return [
                        'total'     => (clone $base)->count(),
                        'published' => (clone $base)->whereNotNull('published_at')
                            ->where('published_at', '<=', now())->count(),
                        'today'     => (clone $base)->whereDate('published_at', today())->count(),
                        'urgent'    => (clone $base)->whereIn('priority', ['high', 'urgent'])->count(),
                        'total_read' => (int) (clone $base)->sum('read_count'),
                    ];
                } catch (Throwable $e) {
                    return [
                        'total' => 0, 'published' => 0,
                        'today' => 0, 'urgent' => 0, 'total_read' => 0,
                    ];
                }
            });

            return view('ketua.pengumuman.index', compact('announcements', 'stats'));

        } catch (Throwable $e) {
            Log::error('[Announcement] index failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            /* Fallback: empty collection */
            $announcements = new \Illuminate\Pagination\LengthAwarePaginator([], 0, self::PER_PAGE);
            $stats = ['total' => 0, 'published' => 0, 'today' => 0, 'urgent' => 0, 'total_read' => 0];

            return view('ketua.pengumuman.index', compact('announcements', 'stats'))
                ->with('error', 'Gagal memuat daftar pengumuman.');
        }
    }

    /* ═══════════════════════════════════════════════════════════
     |  CREATE — Form
     ═══════════════════════════════════════════════════════════ */

    public function create()
    {
        $priorities = [
            'low'    => 'Rendah',
            'normal' => 'Normal',
            'high'   => 'Tinggi',
            'urgent' => 'Mendesak',
        ];

        $targetRoles = [
            'anggota'    => 'Anggota',
            'pengurus'   => 'Pengurus',
            'ketua'      => 'Ketua',
            'superadmin' => 'Superadmin',
            'owner'      => 'Owner',
        ];

        return view('ketua.pengumuman.create', compact('priorities', 'targetRoles'));
    }

    /* ═══════════════════════════════════════════════════════════
     |  STORE — Simpan pengumuman baru
     ═══════════════════════════════════════════════════════════ */

    public function store(Request $request)
    {
        /* ── Normalize input (dukung judul/isi dari form legacy) ── */
        $request->merge([
            'title'   => $request->input('title', $request->input('judul')),
            'content' => $request->input('content', $request->input('isi')),
        ]);

        /* ── Validasi ── */
        $validated = $request->validate([
            'title'          => ['required', 'string', 'min:3', 'max:255'],
            'content'        => ['required', 'string', 'min:10', 'max:5000'],
            'priority'       => ['nullable', 'in:' . implode(',', self::PRIORITIES)],
            'target_roles'   => ['nullable', 'array', 'max:6'],
            'target_roles.*' => ['string', 'in:' . implode(',', self::TARGET_ROLES)],
            'publish_now'    => ['nullable', 'boolean'],
        ], [
            'title.required'   => 'Judul pengumuman wajib diisi.',
            'title.min'        => 'Judul minimal 3 karakter.',
            'content.required' => 'Isi pengumuman wajib diisi.',
            'content.min'      => 'Isi minimal 10 karakter.',
            'content.max'      => 'Isi maksimal 5000 karakter.',
        ]);

        try {
            /* ── Simpan di transaction ── */
            $announcement = DB::transaction(function () use ($validated, $request) {
                return Announcement::create([
                    'title'        => $validated['title'],
                    'content'      => $validated['content'],
                    'priority'     => $validated['priority'] ?? 'normal',
                    'target_roles' => ! empty($validated['target_roles']) ? $validated['target_roles'] : null,
                    'published_at' => $request->boolean('publish_now', true) ? now() : null,
                    'created_by'   => Auth::id(),
                ]);
            });

            /* ── Invalidate cache ── */
            Cache::forget('ketua.announcements.stats');

            /* ── Broadcast notifikasi (di luar transaction) ── */
            $sent = 0;
            if ($announcement->published_at && $announcement->published_at->isPast()) {
                try {
                    $sent = $this->broadcastToUsers(
                        $announcement,
                        $validated['target_roles'] ?? []
                    );
                } catch (Throwable $e) {
                    Log::warning('[Announcement] Broadcast failed', [
                        'announcement_id' => $announcement->id,
                        'error'           => $e->getMessage(),
                    ]);
                }
            }

            /* ── Audit log ── */
            Log::channel('security')->info('[Announcement] Created', [
                'user_id'         => Auth::id(),
                'announcement_id' => $announcement->id,
                'priority'        => $announcement->priority,
                'target_roles'    => $announcement->target_roles,
                'notif_sent'      => $sent,
            ]);

            $message = $sent > 0
                ? "✅ Pengumuman berhasil dibuat & terkirim ke {$sent} user."
                : "✅ Pengumuman berhasil dibuat.";

            return redirect()
                ->route('ketua.pengumuman.index')
                ->with('success', $message);

        } catch (Throwable $e) {
            Log::error('[Announcement] Store failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', '❌ Gagal membuat pengumuman: ' . (
                    config('app.debug') ? $e->getMessage() : 'Silakan coba lagi.'
                ));
        }
    }

    /* ═══════════════════════════════════════════════════════════
     |  SHOW — Detail pengumuman + auto increment read_count
     ═══════════════════════════════════════════════════════════ */

    public function show(int $id)
    {
        $announcement = Announcement::with([
            'author:id,name,avatar_url,email',
            'updater:id,name',
        ])->findOrFail($id);

        /* ── Auto increment read_count (hanya sekali per user per hari) ── */
        try {
            $cacheKey = "announcement.{$id}.read.user." . Auth::id();
            if (! Cache::has($cacheKey)) {
                $announcement->increment('read_count');
                Cache::put($cacheKey, true, now()->addHours(24));
            }
        } catch (Throwable $e) {
            /* Silent — jangan gagalkan halaman karena counter */
        }

        return view('ketua.pengumuman.show', compact('announcement'));
    }

    /* ═══════════════════════════════════════════════════════════
     |  EDIT — Form edit
     ═══════════════════════════════════════════════════════════ */

    public function edit(int $id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->authorizeModify($announcement);

        $priorities = [
            'low' => 'Rendah', 'normal' => 'Normal',
            'high' => 'Tinggi', 'urgent' => 'Mendesak',
        ];
        $targetRoles = [
            'anggota' => 'Anggota', 'pengurus' => 'Pengurus',
            'ketua' => 'Ketua', 'superadmin' => 'Superadmin', 'owner' => 'Owner',
        ];

        return view('ketua.pengumuman.edit', compact('announcement', 'priorities', 'targetRoles'));
    }

    /* ═══════════════════════════════════════════════════════════
     |  UPDATE — Simpan perubahan
     ═══════════════════════════════════════════════════════════ */

    public function update(Request $request, int $id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->authorizeModify($announcement);

        $validated = $request->validate([
            'title'          => ['required', 'string', 'min:3', 'max:255'],
            'content'        => ['required', 'string', 'min:10', 'max:5000'],
            'priority'       => ['nullable', 'in:' . implode(',', self::PRIORITIES)],
            'target_roles'   => ['nullable', 'array', 'max:6'],
            'target_roles.*' => ['string', 'in:' . implode(',', self::TARGET_ROLES)],
        ]);

        try {
            $announcement->update([
                'title'        => $validated['title'],
                'content'      => $validated['content'],
                'priority'     => $validated['priority'] ?? 'normal',
                'target_roles' => ! empty($validated['target_roles']) ? $validated['target_roles'] : null,
                'updated_by'   => Auth::id(),
            ]);

            Cache::forget('ketua.announcements.stats');

            return redirect()
                ->route('ketua.pengumuman.show', $announcement->id)
                ->with('success', '✅ Pengumuman berhasil diperbarui.');

        } catch (Throwable $e) {
            Log::error('[Announcement] Update failed', [
                'user_id'         => Auth::id(),
                'announcement_id' => $id,
                'error'           => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', '❌ Gagal memperbarui pengumuman.');
        }
    }

    /* ═══════════════════════════════════════════════════════════
     |  DESTROY — Hapus pengumuman
     ═══════════════════════════════════════════════════════════ */

    public function destroy(int $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $this->authorizeModify($announcement);

            $title = $announcement->title;
            $announcement->delete();

            Cache::forget('ketua.announcements.stats');

            Log::channel('security')->warning('[Announcement] Deleted', [
                'user_id'         => Auth::id(),
                'announcement_id' => $id,
                'title'           => $title,
            ]);

            return back()->with('success', '✅ Pengumuman berhasil dihapus.');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()->with('error', '⛔ ' . $e->getMessage());
        } catch (Throwable $e) {
            Log::warning('[Announcement] Destroy failed', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', '❌ Gagal menghapus pengumuman.');
        }
    }

    /* ═══════════════════════════════════════════════════════════
     |  INTERNAL — Broadcast (chunked, safe, anti-loop)
     ═══════════════════════════════════════════════════════════ */

    /**
     * Kirim notifikasi ke user yang sesuai target.
     * Menggunakan chunk untuk mencegah memory exhaustion.
     *
     * @return int Jumlah notif yang berhasil dikirim
     */
    protected function broadcastToUsers(Announcement $announcement, array $roles = []): int
    {
        $sent = 0;
        $myId = Auth::id();

        $query = User::query()
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->select(['id', 'name', 'email', 'user_uuid', 'level_id']);

        /* ── Jangan kirim ke diri sendiri ── */
        if ($myId) {
            $query->where('id', '!=', $myId);
        }

        /* ── Filter target role ── */
        if (! empty($roles)) {
            $query->whereHas('roles', fn ($q) => $q->whereIn('name', $roles));
        }

        $preview = Str::limit(strip_tags((string) $announcement->content), 140);
        $title   = (string) $announcement->title;
        $actionUrl = route('ketua.pengumuman.show', $announcement->id);

        $query->chunkById(self::BROADCAST_CHUNK, function ($users) use (
            $announcement, &$sent, $title, $preview, $actionUrl
        ) {
            foreach ($users as $user) {
                try {
                    $notif = $this->notifier->sendToUser(
                        user:    $user,
                        title:   '📢 ' . Str::limit($title, 80),
                        message: $preview,
                        source:  'announcement',
                        options: [
                            'type_slug'   => 'announcement.created',
                            'category'    => 'announcement',
                            'priority'    => $announcement->priority ?? 'normal',
                            'icon'        => 'fa-bullhorn',
                            'actor_id'    => $announcement->created_by,
                            'action_url'  => $actionUrl,
                            'entity_type' => Announcement::class,
                            'entity_id'   => $announcement->id,
                            'dedup_key'   => 'announcement:' . $announcement->id . ':user:' . $user->id,
                            'data'        => [
                                'announcement_id' => $announcement->id,
                                'title'           => $title,
                                'author_name'     => optional($announcement->author)->name,
                                'priority'        => $announcement->priority,
                            ],
                        ]
                    );

                    if ($notif) $sent++;

                } catch (Throwable $e) {
                    Log::debug('[Announcement] Notif skip', [
                        'user_id' => $user->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }
        });

        return $sent;
    }

    /* ═══════════════════════════════════════════════════════════
     |  INTERNAL — Authorization Helper
     ═══════════════════════════════════════════════════════════ */

    /**
     * Cek apakah user berhak mengubah/menghapus pengumuman ini.
     * Allowed: creator, owner, superadmin, ketua.
     */
    protected function authorizeModify(Announcement $announcement): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthenticated.');
        }

        $isCreator  = (int) $announcement->created_by === (int) $user->id;
        $isPrivileged = method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['owner', 'superadmin', 'ketua']);

        if (! $isCreator && ! $isPrivileged) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'Anda tidak berhak mengubah pengumuman ini.'
            );
        }
    }
}
<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreItemRequest;
use App\Models\Lms\LmsGroup;
use App\Models\Lms\LmsItem;
use App\Models\Level;
use App\Models\User;
use App\Services\Lms\LmsAuditService;
use App\Services\Lms\LmsItemNotificationService;
use App\Services\Lms\RecipientResolver;
use App\Services\Lms\VideoUrlValidator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ItemController extends Controller
{
    /** Batas ukuran & jenis file */
    protected const COVER_MAX_SIZE      = 4 * 1024 * 1024;      // 4 MB
    protected const COVER_ALLOWED_MIME  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    protected const ATTACHMENT_MAX_SIZE = 10 * 1024 * 1024;     // 10 MB
    protected const ATTACHMENT_MAX_FILES = 3;
    protected const ATTACHMENT_ALLOWED_EXT = ['pdf','doc','docx','xls','xlsx','ppt','pptx','txt','csv'];

    public function __construct(
        protected RecipientResolver          $resolver,
        protected VideoUrlValidator          $videoValidator,
        protected LmsAuditService            $audit,
        protected LmsItemNotificationService $notifService,
    ) {}

    /* =========================================================
     | LISTING
     * ========================================================= */

    public function all(Request $request): View       { return $this->listByType($request, null); }
    public function tasks(Request $request): View     { return $this->listByType($request, 'task'); }
    public function materials(Request $request): View { return $this->listByType($request, 'material'); }

    protected function listByType(Request $request, ?string $type): View
    {
        $user = Auth::user();

        $query = LmsItem::query()
            ->with([
                'group:id,name,code',
                'creator:id,name,avatar_url,level_id',
                'creator.level:id,name',
            ])
            ->whereIn('status', ['published', 'draft']);

        if ($type) $query->where('type', $type);

        if ($user->level_id === 5) {
            $query->whereHas('recipients', fn ($q) => $q->where('user_id', $user->id));
        } elseif (in_array($user->level_id, [3, 4], true)) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('recipients', fn ($r) => $r->where('user_id', $user->id));
            });
        }

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', (int) $request->input('group_id'));
        }

        match ($request->input('sort', 'latest')) {
            'oldest'   => $query->orderBy('published_at'),
            'deadline' => $query->orderByRaw('deadline_at IS NULL, deadline_at ASC'),
            'title'    => $query->orderBy('title'),
            default    => $query->orderByDesc('published_at'),
        };

        $items = $query->paginate(12)->withQueryString();

        return view('owner.lms.items.index', compact('items', 'type'));
    }

    public function myCreated(): View
    {
        $items = LmsItem::query()
            ->with(['group:id,name,code'])
            ->withCount(['recipients', 'submissions'])
            ->where('created_by', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('owner.lms.items.my-created', compact('items'));
    }

    /* =========================================================
     | CREATE — FORM
     * ========================================================= */

    public function create(Request $request, ?LmsGroup $group = null): View|RedirectResponse
    {
        $user = Auth::user();

        if ($group) {
            $this->ensureCanManageGroup($group);
            $groups = collect([$group]);
        } else {
            $groups = LmsGroup::query()
                ->where('status', 'active')
                ->when($user->level_id > 2, fn ($q) => $q->where('creator_id', $user->id))
                ->orderBy('name')
                ->get(['id', 'name', 'code']);
        }

        if ($groups->isEmpty()) {
            return redirect()
                ->route('owner.lms.groups.create')
                ->with('warning', 'Kamu belum memiliki Pengelompokan aktif. Buat minimal satu terlebih dahulu.');
        }

        $roles = Level::orderBy('id')->get(['id', 'name']);

        $selectedGroupId = old('group_id', $group->id ?? $request->input('group_id'));

        return view('owner.lms.items.create', compact('groups', 'roles', 'group', 'selectedGroupId'));
    }

    /* =========================================================
     | STORE
     * ========================================================= */

    public function store(StoreItemRequest $request, ?LmsGroup $group = null): RedirectResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        /* ---------- 1. Tentukan group target ---------- */
        $targetGroupId = $group?->id ?? (int) $data['group_id'];
        $targetGroup = LmsGroup::query()
            ->where('status', 'active')
            ->findOrFail($targetGroupId);

        $this->ensureCanManageGroup($targetGroup);

        /* ---------- 2. Validasi video URL ---------- */
        $videoCheck = $this->videoValidator->validate($data['video_url'] ?? null);
        if (! $videoCheck['ok']) {
            return back()->withInput()->withErrors(['video_url' => $videoCheck['message']]);
        }

        /* ---------- 3. Validasi deadline ---------- */
        $deadlineMode = $data['deadline_mode'] ?? 'unlimited';
        $deadlineAt   = null;

        if ($deadlineMode === 'limited') {
            if (empty($data['deadline_at'])) {
                return back()->withInput()->withErrors(['deadline_at' => 'Waktu deadline wajib diisi.']);
            }
            $deadlineAt = Carbon::parse($data['deadline_at'], 'Asia/Jakarta');
            if ($deadlineAt->lessThanOrEqualTo(Carbon::now('Asia/Jakarta'))) {
                return back()->withInput()->withErrors(['deadline_at' => 'Deadline harus setelah waktu server.']);
            }
        }

        /* ---------- 4. Validasi target ---------- */
        $targetMode = $data['target_mode'] ?? 'role';
        if ($targetMode === 'role' && empty($data['target_roles'])) {
            return back()->withInput()->withErrors(['target_roles' => 'Pilih minimal satu role penerima.']);
        }
        if ($targetMode === 'individual' && empty($data['target_users'])) {
            return back()->withInput()->withErrors(['target_users' => 'Pilih minimal satu user penerima.']);
        }
        if ($targetMode === 'both' && empty($data['target_roles']) && empty($data['target_users'])) {
            return back()->withInput()->withErrors(['target_roles' => 'Pilih minimal satu target.']);
        }

        /* ---------- 5. Cek duplikat judul ---------- */
        $dup = LmsItem::query()
            ->where('group_id', $targetGroup->id)
            ->where('title', $data['title'])
            ->whereIn('status', ['published', 'draft'])
            ->exists();

        if ($dup) {
            return back()->withInput()->withErrors(['title' => 'Judul ini sudah dipakai di pengelompokan yang sama.']);
        }

        /* ---------- 6. Proses cover ---------- */
        [$coverPath, $coverImages, $coverMeta] = $this->processCoverUpload($request);

        /* ---------- 7. Proses attachments ---------- */
        [$attachments, $attachmentMeta] = $this->processAttachmentsUpload($request, []);

        /* ---------- 8. Simpan ---------- */
        DB::beginTransaction();

        try {
            $item = LmsItem::create([
                'group_id'        => $targetGroup->id,
                'type'            => $data['type'],
                'title'           => $data['title'],
                'description'     => $data['description'] ?? null,

                /* COVER */
                'cover_path'      => $coverPath,
                'cover_images'    => $coverImages,
                'cover_meta'      => $coverMeta,

                /* ATTACHMENTS */
                'attachments'     => $attachments,
                'attachment_meta' => $attachmentMeta,

                'video_url'       => $data['video_url'] ?? null,
                'video_provider'  => $videoCheck['provider'] ?? null,

                'created_by'      => $user->id,
                'published_at'    => now(),
                'deadline_mode'   => $deadlineMode,
                'deadline_at'     => $deadlineAt,
                'timezone'        => 'Asia/Jakarta',

                'target_mode'     => $targetMode,
                'target_roles'    => $data['target_roles'] ?? null,
                'target_user_ids' => $data['target_users'] ?? null,

                'status'          => 'published',
                'visibility'      => 'public',
            ]);

            /* ---------- 9. Simpan target role (history) ---------- */
            if (in_array($targetMode, ['role', 'both'], true) && ! empty($data['target_roles'])) {
                foreach ($data['target_roles'] as $levelId) {
                    $item->targets()->create([
                        'target_type' => 'role',
                        'target_id'   => $levelId,
                        'level_id'    => $levelId,
                    ]);
                }
            }

            /* ---------- 10. Snapshot recipients ---------- */
            $userIds = $this->resolver->resolve(
                $item,
                $data['target_roles'] ?? [],
                $data['target_users'] ?? []
            );
            $this->resolver->assign($item, $userIds, $targetMode);

            $item->updateQuietly([
                'recipients_count' => count($userIds),
                'recipient_count'  => count($userIds),
            ]);

            /* ---------- 11. Audit ---------- */
            $this->audit->log('item_published', $item, [
                'title'         => $item->title,
                'type'          => $item->type,
                'group_id'      => $targetGroup->id,
                'target_mode'   => $targetMode,
                'deadline_mode' => $deadlineMode,
                'has_cover'     => (bool) $coverPath,
                'attachments'   => count($attachments),
                'recipients'    => count($userIds),
                'ip'            => $request->ip(),
            ]);

            /* ---------- 12. Refresh counter group ---------- */
            if (method_exists($targetGroup, 'refreshCounters')) {
                $targetGroup->refreshCounters();
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            /* Bersihkan file yang sudah diupload kalau gagal */
            $this->cleanupFiles($coverPath, $attachments);

            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        $item->refresh();

        /* ---------- 13. Notifikasi ---------- */
        try {
            $this->notifService->notifyPublished($item);
        } catch (Throwable $e) {
            report($e);
        }

        /* ---------- 14. Redirect ---------- */
        $successMsg = $item->type === 'task'
            ? 'Tugas berhasil dipublikasikan ke ' . $item->recipients_count . ' penerima.'
            : 'Materi berhasil dipublikasikan ke ' . $item->recipients_count . ' penerima.';

        return redirect()
            ->route('owner.lms.groups.show', $item->group_id)
            ->with('success', $successMsg);
    }

    /* =========================================================
     | SHOW / EDIT / UPDATE / DESTROY
     * ========================================================= */

    public function show(LmsItem $item): View
    {
        $item->load([
            'group:id,name,code,creator_id',
            'creator:id,name,avatar_url,level_id',
            'creator.level:id,name',
            'targets',
            'recipients.user:id,name,nim,avatar_url,level_id',
            'recipients.user.level:id,name',
            'submissions' => fn ($q) => $q->latest()->limit(20),
            'submissions.user:id,name,avatar_url',
        ]);

        $stats = [
            'total_recipients' => $item->recipients->count(),
            'total_submitted'  => $item->submissions->count(),
            'total_pending'    => max(0, $item->recipients->count() - $item->submissions->count()),
        ];

        return view('owner.lms.items.show', compact('item', 'stats'));
    }

    public function edit(LmsItem $item): View
    {
        $this->ensureCanManageItem($item);

        $groups = LmsGroup::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $roles = Level::orderBy('id')->get(['id', 'name']);

        return view('owner.lms.items.edit', compact('item', 'groups', 'roles'));
    }

    public function update(StoreItemRequest $request, LmsItem $item): RedirectResponse
    {
        $this->ensureCanManageItem($item);

        $data = $request->validated();

        /* ---------- 1. Video URL ---------- */
        $videoCheck = ['ok' => true, 'provider' => $item->video_provider];
        if (! empty($data['video_url'])) {
            $videoCheck = $this->videoValidator->validate($data['video_url']);
            if (! $videoCheck['ok']) {
                return back()->withInput()->withErrors(['video_url' => $videoCheck['message']]);
            }
        }

        /* ---------- 2. Deadline ---------- */
        $deadlineMode = $data['deadline_mode'] ?? 'unlimited';
        $deadlineAt   = null;

        if ($deadlineMode === 'limited') {
            if (empty($data['deadline_at'])) {
                return back()->withInput()->withErrors(['deadline_at' => 'Waktu deadline wajib diisi.']);
            }
            $deadlineAt = Carbon::parse($data['deadline_at'], 'Asia/Jakarta');
        }

        /* ---------- 3. Snapshot before (audit) ---------- */
        $before = $item->only([
            'title', 'description', 'deadline_at', 'deadline_mode',
            'target_mode', 'target_roles', 'target_user_ids',
            'cover_path', 'attachments',
        ]);

        /* ---------- 4. COVER — handle replace / remove ---------- */
        [$coverPath, $coverImages, $coverMeta] = $this->processCoverUpdate($request, $item);

        /* ---------- 5. ATTACHMENTS — handle keep / add / remove ---------- */
        $removeIdx = (array) $request->input('remove_attachments', []);
        [$attachments, $attachmentMeta] = $this->processAttachmentsUpdate(
            $request,
            $item->attachments ?? [],
            $item->attachment_meta ?? [],
            $removeIdx
        );

        /* ---------- 6. Update DB ---------- */
        DB::beginTransaction();

        try {
            $item->update([
                'title'           => $data['title'],
                'description'     => $data['description'] ?? null,
                'deadline_mode'   => $deadlineMode,
                'deadline_at'     => $deadlineAt,
                'video_url'       => $data['video_url'] ?? $item->video_url,
                'video_provider'  => $videoCheck['provider'] ?? $item->video_provider,
                'target_mode'     => $data['target_mode'] ?? $item->target_mode,
                'target_roles'    => $data['target_roles'] ?? $item->target_roles,
                'target_user_ids' => $data['target_users'] ?? $item->target_user_ids,

                /* COVER & ATTACHMENTS */
                'cover_path'      => $coverPath,
                'cover_images'    => $coverImages,
                'cover_meta'      => $coverMeta,
                'attachments'     => $attachments,
                'attachment_meta' => $attachmentMeta,
            ]);

            /* ---------- 7. Refresh target roles (history) ---------- */
            if (isset($data['target_roles'])) {
                $item->targets()->where('target_type', 'role')->delete();

                foreach ($data['target_roles'] as $levelId) {
                    $item->targets()->create([
                        'target_type' => 'role',
                        'target_id'   => $levelId,
                        'level_id'    => $levelId,
                    ]);
                }
            }

            /* ---------- 8. Re-resolve recipients ---------- */
            $userIds = $this->resolver->resolve(
                $item,
                $data['target_roles'] ?? $item->target_roles ?? [],
                $data['target_users'] ?? $item->target_user_ids ?? []
            );
            $this->resolver->assign($item, $userIds, $item->target_mode);

            $item->updateQuietly([
                'recipients_count' => count($userIds),
                'recipient_count'  => count($userIds),
            ]);

            /* ---------- 9. Audit ---------- */
            $this->audit->log('item_updated', $item, [
                'before' => $before,
                'after'  => $item->only([
                    'title', 'description', 'deadline_at', 'deadline_mode',
                    'target_mode', 'target_roles', 'target_user_ids',
                    'cover_path', 'attachments',
                ]),
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }

        return redirect()
            ->route('owner.lms.items.show', $item->id)
            ->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Request $request, LmsItem $item): RedirectResponse
    {
        $this->ensureCanManageItem($item);

        $reason = $request->input('reason', 'Tidak ada alasan');

        /* Simpan path dulu sebelum delete */
        $coverPath   = $item->cover_path;
        $attachments = $item->attachments ?? [];

        $item->update([
            'status'        => 'deleted',
            'deleted_by'    => Auth::id(),
            'delete_reason' => $reason,
        ]);

        $item->delete();

        /* Hapus file fisik (opsional — kalau mau restore, comment baris ini) */
        // $this->cleanupFiles($coverPath, $attachments);

        $this->audit->log('item_deleted', $item, ['reason' => $reason], $reason);

        return redirect()
            ->route('owner.lms.all')
            ->with('success', 'Item berhasil dihapus.');
    }

    /* =========================================================
     | SEARCH USERS
     * ========================================================= */

    public function searchUsers(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! in_array($user->level_id, [1, 2, 3, 4, 5], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $q       = trim((string) $request->input('q', ''));
        $role    = $request->input('role');
        $limit   = min((int) $request->input('limit', 20), 50);
        $exclude = (array) $request->input('exclude', []);

        $query = User::query()
            ->select(['id', 'name', 'nim', 'username', 'level_id', 'is_active'])
            ->with('level:id,name')
            ->where('is_active', true)
            ->whereNull('deleted_at');

        if (! empty($exclude))       $query->whereNotIn('id', $exclude);
        if ($role && is_numeric($role)) $query->where('level_id', (int) $role);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('nim', 'like', "%{$q}%")
                  ->orWhere('username', 'like', "%{$q}%");
            });
        }

        $users = $query->orderBy('name')->limit($limit)->get();

        return response()->json([
            'ok'    => true,
            'count' => $users->count(),
            'users' => $users->map(fn ($u) => [
                'id'        => $u->id,
                'name'      => $u->name,
                'nim'       => $u->nim,
                'role_id'   => $u->level_id,
                'role_name' => $u->level->name ?? '-',
                'initials'  => strtoupper(mb_substr($u->name ?? 'U', 0, 2)),
            ]),
        ]);
    }

    /* =========================================================
     | FILE HANDLING HELPERS
     * ========================================================= */

    /**
     * Proses upload cover baru (STORE).
     * @return array{0:?string, 1:?array, 2:?array}  [path, images, meta]
     */
    protected function processCoverUpload(Request $request): array
    {
        if (! $request->hasFile('cover')) return [null, null, null];

        $file = $request->file('cover');
        if (! $file->isValid()) {
            Log::warning('[LMS Cover] file invalid');
            return [null, null, null];
        }
        if ($file->getSize() > self::COVER_MAX_SIZE) {
            Log::warning('[LMS Cover] file terlalu besar', ['size' => $file->getSize()]);
            return [null, null, null];
        }
        if (! in_array($file->getMimeType(), self::COVER_ALLOWED_MIME, true)) {
            Log::warning('[LMS Cover] mime tidak didukung', ['mime' => $file->getMimeType()]);
            return [null, null, null];
        }

        $path = $file->store('lms/items/covers', 'public');

        return [
            $path,
            [$path],
            [
                'original_name' => $file->getClientOriginalName(),
                'mime'          => $file->getClientMimeType(),
                'size'          => $file->getSize(),
                'uploaded_at'   => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Proses cover saat update — replace / hapus.
     * @return array{0:?string, 1:?array, 2:?array}
     */
    protected function processCoverUpdate(Request $request, LmsItem $item): array
    {
        /* Default: pertahankan cover lama */
        $coverPath   = $item->cover_path;
        $coverImages = $item->cover_images;
        $coverMeta   = $item->cover_meta;

        /* Hapus cover manual (checkbox remove_cover=1) */
        if ($request->input('remove_cover') === '1') {
            if ($coverPath) Storage::disk('public')->delete($coverPath);
            return [null, null, null];
        }

        /* Ganti dengan file baru */
        if ($request->hasFile('cover')) {
            if ($coverPath) Storage::disk('public')->delete($coverPath);

            [$newPath, $newImages, $newMeta] = $this->processCoverUpload($request);
            if ($newPath) return [$newPath, $newImages, $newMeta];
        }

        return [$coverPath, $coverImages, $coverMeta];
    }

    /**
     * Proses upload attachments baru (STORE).
     * @return array{0:array, 1:array}  [attachments[], metas[]]
     */
    protected function processAttachmentsUpload(Request $request, array $existing = []): array
    {
        $attachments = $existing;
        $metas       = [];

        if (! $request->hasFile('attachments')) return [$attachments, $metas];

        $files = $request->file('attachments');
        $files = is_array($files) ? $files : [$files];

        $count = 0;
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) continue;
            if ($count >= self::ATTACHMENT_MAX_FILES) break;
            if ($file->getSize() > self::ATTACHMENT_MAX_SIZE) {
                Log::warning('[LMS Attachment] melebihi 10 MB', ['name' => $file->getClientOriginalName()]);
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, self::ATTACHMENT_ALLOWED_EXT, true)) {
                Log::warning('[LMS Attachment] ext tidak didukung', ['ext' => $ext]);
                continue;
            }

            $path = $file->store('lms/items/attachments', 'public');
            $attachments[] = $path;
            $metas[] = [
                'original_name' => $file->getClientOriginalName(),
                'mime'          => $file->getMimeType(),
                'size'          => $file->getSize(),
                'path'          => $path,
                'uploaded_at'   => now()->toIso8601String(),
            ];
            $count++;
        }

        return [$attachments, $metas];
    }

    /**
     * Proses attachments saat UPDATE — keep / add / remove.
     * @param  array $existingPaths
     * @param  array $existingMeta
     * @param  array $removeIdx  array index yang dicentang "hapus"
     * @return array{0:array, 1:array}
     */
    protected function processAttachmentsUpdate(
        Request $request,
        array $existingPaths,
        array $existingMeta,
        array $removeIdx
    ): array {
        /* Map meta by path untuk lookup cepat */
        $metaByPath = [];
        foreach ($existingMeta as $m) {
            $p = is_array($m) ? ($m['path'] ?? null) : null;
            if ($p) $metaByPath[$p] = $m;
        }

        /* 1. Buang yang di-remove */
        $keptPaths = [];
        $keptMeta  = [];
        foreach ($existingPaths as $idx => $path) {
            if (in_array((string) $idx, array_map('strval', $removeIdx), true)) {
                Storage::disk('public')->delete($path);
                continue;
            }
            $keptPaths[] = $path;
            if (isset($metaByPath[$path])) $keptMeta[] = $metaByPath[$path];
        }

        /* 2. Tambah file baru */
        [$finalPaths, $newMetas] = $this->processAttachmentsUpload($request, $keptPaths);

        /* Gabung meta lama + baru */
        $finalMeta = array_merge($keptMeta, $newMetas);

        return [$finalPaths, $finalMeta];
    }

    /**
     * Bersihkan file jika transaksi gagal.
     */
    protected function cleanupFiles(?string $coverPath, array $attachments = []): void
    {
        if ($coverPath) Storage::disk('public')->delete($coverPath);
        foreach ($attachments as $p) {
            if (is_string($p)) Storage::disk('public')->delete($p);
        }
    }

    /* =========================================================
     | AUTHORIZATION HELPERS
     * ========================================================= */

    protected function ensureCanManageGroup(LmsGroup $group): void
    {
        $user = Auth::user();

        if (! $user) abort(401);
        if ($user->level_id <= 2) return; // Owner / Superadmin

        abort_unless(
            $group->creator_id === $user->id,
            403,
            'Kamu tidak boleh menambahkan item ke pengelompokan ini.'
        );
    }

    protected function ensureCanManageItem(LmsItem $item): void
    {
        $user = Auth::user();

        if (! $user) abort(401);
        if ($user->level_id <= 2) return;

        abort_unless(
            $item->created_by === $user->id,
            403,
            'Kamu tidak boleh mengelola item ini.'
        );
    }
}
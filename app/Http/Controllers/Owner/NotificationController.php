<?php

namespace App\Http\Controllers\Owner;

use App\Enums\NotificationSource;
use App\Http\Controllers\Controller;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $svc) {}

   public function index(Request $request)
{
    $user  = $request->user();
    $query = $user->notifications();

    $tab = $request->input('tab', 'all');

    // Filter per tab
    match ($tab) {
        'unread'   => $query->whereNull('read_at')->whereNull('archived_at'),
        'action'   => $query->where('requires_action', true)
                            ->whereNull('actioned_at')
                            ->whereNull('archived_at'),
        'starred'  => $query->whereNotNull('starred_at')->whereNull('archived_at'),
        'archived' => $query->whereNotNull('archived_at'),
        default    => $query->whereNull('archived_at'),
    };

    // Source filter
    if ($src = $request->input('source')) {
        if (in_array($src, NotificationSource::values(), true)) {
            $query->where('source', $src);
        }
    }

    // Search
    if ($q = $request->input('q')) {
        $query->where(function ($w) use ($q) {
            $w->where('title', 'like', "%{$q}%")
              ->orWhere('message', 'like', "%{$q}%")
              ->orWhere('data', 'like', "%{$q}%");
        });
    }

    // Range
    if ($range = $request->input('range')) {
        match ($range) {
            'today' => $query->where('created_at', '>=', now()->startOfDay()),
            '7d'    => $query->where('created_at', '>=', now()->subDays(7)),
            '30d'   => $query->where('created_at', '>=', now()->subDays(30)),
            default => null,
        };
    }

    // ✅ SORTING PINTAR — prioritas dulu, baru terbaru
    $query->orderByRaw("
        CASE priority
            WHEN 'urgent'    THEN 1
            WHEN 'high'      THEN 2
            WHEN 'important' THEN 3
            ELSE 4
        END ASC
    ")
    ->orderByDesc('created_at');

    $notifications = $query->paginate(20)->withQueryString();

    // Counts per tab
    $counts = [
        'all'      => $user->notifications()->whereNull('archived_at')->count(),
        'unread'   => $user->unreadNotifications()->whereNull('archived_at')->count(),
        'action'   => $user->notifications()->where('requires_action', true)
                        ->whereNull('actioned_at')->whereNull('archived_at')->count(),
        'starred'  => $user->notifications()->whereNotNull('starred_at')
                        ->whereNull('archived_at')->count(),
        'archived' => $user->notifications()->whereNotNull('archived_at')->count(),
    ];

    $unreadCount = $counts['unread'];

    // Mark seen
    $user->notifications()->whereNull('seen_at')->update(['seen_at' => now()]);

    return view('owner.notifications.index', compact(
        'notifications', 'unreadCount', 'tab', 'counts'
    ));
}
    public function archived(Request $request)
    {
        return $this->index($request->merge(['tab' => 'archived']));
    }

public function show(Request $request, string $id)
{
    $notif = $this->findOwned($request, $id);
    $this->svc->markRead($notif);

    // Auto-redirect kalau ada action_url valid DAN bukan self-reference
    $url = $notif->action_url ?: $notif->deep_link;

    if ($url && $url !== '#' && ! str_contains($url, "/owner/notifications/{$notif->id}")) {
        // Kalau action_url berisi __ID__ placeholder, ganti dengan id asli
        $url = str_replace('__ID__', $notif->id, $url);
        return redirect()->to($url);
    }

    return view('owner.notifications.show', compact('notif'));
}
    /* ==========================================================
     |  POLL / BADGE
     * ========================================================== */

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

public function poll(Request $request): JsonResponse
{
    $user    = $request->user();
    $sinceId = (string) $request->input('since_id', '');

    // 🔥 Auto-prune notif dari entity yang sudah hilang
    if (random_int(1, 20) === 1) { // 5% chance tiap poll
        app(\App\Services\Notifications\NotificationService::class)->pruneOrphans();
    }

    $unread = $user->notifications()
        ->whereNull('read_at')
        ->whereNull('archived_at')
        ->count();

    $latest = $user->notifications()
        ->whereNull('archived_at')
        ->latest('updated_at')     // 🔥 urut by updated_at, bukan created_at
        ->limit(10)
        ->get();

    $latestId = optional($latest->first())->id;

    // Deteksi perubahan: id baru ATAU version naik
    $hasNew = $sinceId !== '' && $latestId && $latestId !== $sinceId;

    return response()->json([
        'unread_count' => $unread,
        'latest_id'    => $latestId,
        'has_new'      => $hasNew,
        'items'        => $latest->map(fn ($n) => $this->serializeItem($n))->values(),
        'server_time'  => now()->toIso8601String(),
    ]);
}

    public function dropdown(Request $request): JsonResponse
    {
        $user  = $request->user();
        $items = $user->notifications()
            ->whereNull('archived_at')
            ->latest()
            ->limit(config('unicode_notifications.dropdown_size', 8))
            ->get();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $items->map(fn ($n) => $this->serializeItem($n))->values(),
        ]);
    }

    /* ==========================================================
     |  ACTIONS
     * ========================================================== */

    public function read(Request $request, string $id): JsonResponse
    {
        $notif = $this->findOwned($request, $id);
        $this->svc->markRead($notif);
        return $this->jsonRefresh($request);
    }

    public function seen(Request $request, string $id): JsonResponse
    {
        $notif = $this->findOwned($request, $id);
        $this->svc->markSeen($notif);
        return $this->jsonRefresh($request);
    }

    public function actioned(Request $request, string $id): JsonResponse
    {
        $notif = $this->findOwned($request, $id);
        $this->svc->markActioned($notif);
        $this->svc->markRead($notif);
        return $this->jsonRefresh($request);
    }

    public function readAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->jsonRefresh($request, 'Semua notifikasi ditandai dibaca.');
    }

    public function archive(Request $request, string $id): JsonResponse
    {
        $notif = $this->findOwned($request, $id);
        $notif->forceFill(['archived_at' => now()])->save();
        return $this->jsonRefresh($request);
    }

    public function unarchive(Request $request, string $id): JsonResponse
    {
        $notif = $this->findOwned($request, $id);
        $notif->forceFill(['archived_at' => null])->save();
        return $this->jsonRefresh($request);
    }

    public function toggleStar(Request $request, string $id): JsonResponse
    {
        $notif = $this->findOwned($request, $id);
        $notif->forceFill([
            'starred_at' => $notif->starred_at ? null : now(),
        ])->save();

        return $this->jsonRefresh(
            $request,
            $notif->starred_at ? 'Ditambahkan ke Favorit.' : 'Dihapus dari Favorit.',
            ['is_starred' => ! is_null($notif->starred_at)]
        );
    }

    /**
     * Hapus notifikasi — HANYA yang sudah dibaca.
     */
    public function destroy(Request $request, string $id)
    {
        $notif = $this->findOwned($request, $id);

        if (is_null($notif->read_at)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buka notifikasi dulu sebelum menghapus.',
                ], 422);
            }
            return back()->with('error', 'Buka notifikasi dulu sebelum menghapus.');
        }

        $notif->delete();
        return $this->jsonRefresh($request, 'Notifikasi dihapus.');
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $deleted = $request->user()->notifications()
            ->whereNotNull('read_at')
            ->delete();

        return $this->jsonRefresh($request, "{$deleted} notifikasi dibersihkan.");
    }

    /* ==========================================================
     |  INTERNAL
     * ========================================================== */

    protected function findOwned(Request $request, string $id)
    {
        return $request->user()->notifications()->whereKey($id)->firstOrFail();
    }

    protected function serializeItem($n): array
{
    return [
        'id'          => $n->id,
        'source'      => $n->source,
        'title'       => $n->title,
        'message'     => $n->data['message'] ?? '',
        'priority'    => $n->priority,
        'is_read'     => ! is_null($n->read_at),
        'is_starred'  => ! is_null($n->starred_at),
        'is_archived' => ! is_null($n->archived_at),
        // 🔥 Field baru untuk UI
        'event_type'  => $n->event_type ?? 'created',   // created|updated|deleted|restored
        'version'     => (int) ($n->version ?? 1),
        'is_edited'   => ($n->version ?? 1) > 1,
        'source_gone' => ! is_null($n->source_deleted_at),
        'time_ago'    => optional($n->updated_at ?? $n->created_at)->diffForHumans(),
        'created_at'  => optional($n->created_at)->toIso8601String(),
        'updated_at'  => optional($n->updated_at)->toIso8601String(),
        'action_url'  => $n->action_url,
        'source_meta' => [
            'label' => config("unicode_notifications.sources.{$n->source}.label", $n->source),
            'emoji' => config("unicode_notifications.sources.{$n->source}.emoji", '🔔'),
        ],
    ];
}

    protected function jsonRefresh(Request $request, ?string $message = null, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success'      => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'message'      => $message,
        ], $extra));
    }
}
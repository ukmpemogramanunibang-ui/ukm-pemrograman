<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     | INDEX — Halaman Notification Center
     ═══════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $tab    = $request->get('tab', 'all');

        $baseQuery = UserNotification::forUser($userId);

        /* ── Tab logic ── */
        $query = match($tab) {
            'unread'   => (clone $baseQuery)->notArchived()->unread(),
            'starred'  => (clone $baseQuery)->notArchived()->starred(),
            'archived' => (clone $baseQuery)->archived(),
            default    => (clone $baseQuery)->notArchived(),
        };

        /* ── Search ── */
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('message', 'like', "%{$q}%");
            });
        }

        /* ── Filter kategori ── */
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        /* ── Filter range waktu ── */
        if ($request->filled('range')) {
            match($request->range) {
                'today' => $query->whereDate('created_at', today()),
                '7d'    => $query->where('created_at', '>=', now()->subDays(7)),
                '30d'   => $query->where('created_at', '>=', now()->subDays(30)),
                default => null,
            };
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();

        /* ── Grouping by date (Hari Ini / Kemarin / Minggu Ini / Lebih Lama) ── */
        $notifications->getCollection()->transform(function ($n) {
            $n->group_label = $this->groupLabel($n->created_at);
            return $n;
        });

        $grouped = $notifications->getCollection()->groupBy('group_label');

        /* ── Stats ── */
        $stats = [
            'total'    => (clone $baseQuery)->notArchived()->count(),
            'unread'   => (clone $baseQuery)->notArchived()->unread()->count(),
            'today'    => (clone $baseQuery)->notArchived()->whereDate('created_at', today())->count(),
            'important' => (clone $baseQuery)->notArchived()
                ->whereIn('priority', ['high', 'urgent'])->count(),
        ];

        /* ── Counts per tab ── */
        $counts = [
            'all'      => (clone $baseQuery)->notArchived()->count(),
            'unread'   => (clone $baseQuery)->notArchived()->unread()->count(),
            'starred'  => (clone $baseQuery)->notArchived()->starred()->count(),
            'archived' => (clone $baseQuery)->archived()->count(),
        ];

        /* ── Categories ── */
        $categories = [
            'all'       => ['label' => 'Semua',   'icon' => 'fa-inbox'],
            'system'    => ['label' => 'Sistem',  'icon' => 'fa-cog'],
            'member'    => ['label' => 'Anggota', 'icon' => 'fa-users'],
            'event'     => ['label' => 'Kegiatan','icon' => 'fa-calendar'],
            'financial' => ['label' => 'Keuangan','icon' => 'fa-coins'],
            'approval'  => ['label' => 'Approval','icon' => 'fa-check-double'],
            'general'   => ['label' => 'Umum',    'icon' => 'fa-bell'],
        ];

        return view('ketua.notifications', compact(
            'notifications', 'grouped', 'stats', 'counts', 'categories', 'tab'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     | UNREAD COUNT — Untuk badge header & sidebar
     ═══════════════════════════════════════════════════════════ */
    public function unreadCount()
    {
        return response()->json([
            'count' => UserNotification::forUser(auth()->id())
                ->notArchived()->unread()->count(),
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | LATEST — Untuk dropdown header
     ═══════════════════════════════════════════════════════════ */
    public function latest()
    {
        $userId = auth()->id();

        $items = UserNotification::forUser($userId)
            ->notArchived()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id'           => $n->id,
                'title'        => $n->title,
                'message'      => Str::limit($n->message, 90),
                'icon'         => $n->iconOrDefault(),
                'type'         => $n->type,
                'category'     => $n->category,
                'priority'     => $n->priority,
                'action_url'   => $n->action_url,
                'action_label' => $n->action_label,
                'is_unread'    => $n->isUnread(),
                'time'         => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'items'  => $items,
            'unread' => UserNotification::forUser($userId)->notArchived()->unread()->count(),
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | POLL — Cek notifikasi baru (untuk toast live)
     ═══════════════════════════════════════════════════════════ */
    public function poll(Request $request)
    {
        $userId = auth()->id();
        $lastId = (int) $request->get('last_id', 0);

        $query = UserNotification::forUser($userId)->notArchived();

        $latest = (clone $query)->latest()->first();
        $newItems = $lastId > 0
            ? (clone $query)->where('id', '>', $lastId)->latest()->limit(5)->get()
            : collect();

        return response()->json([
            'unread_count' => (clone $query)->unread()->count(),
            'latest_id'    => $latest->id ?? 0,
            'has_new'      => $newItems->isNotEmpty(),
            'items'        => $newItems->map(fn ($n) => [
                'id'      => $n->id,
                'title'   => $n->title,
                'message' => Str::limit($n->message, 80),
            ]),
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | SHOW — Detail notifikasi
     ═══════════════════════════════════════════════════════════ */
    public function show(UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->route('ketua.notifications.index');
    }

    /* ═══════════════════════════════════════════════════════════
     | MARK READ — Tandai 1 dibaca
     ═══════════════════════════════════════════════════════════ */
    public function markRead(Request $request, UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        $unread = UserNotification::forUser(auth()->id())
            ->notArchived()->unread()->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'     => true,
                'unread' => $unread,
            ]);
        }

        return $notification->action_url
            ? redirect($notification->action_url)
            : back();
    }

    /* ═══════════════════════════════════════════════════════════
     | MARK UNREAD — Tandai 1 belum dibaca
     ═══════════════════════════════════════════════════════════ */
    public function markUnread(UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update(['read_at' => null]);

        return response()->json([
            'ok'     => true,
            'unread' => UserNotification::forUser(auth()->id())
                ->notArchived()->unread()->count(),
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | MARK ALL READ
     ═══════════════════════════════════════════════════════════ */
    public function markAllRead()
    {
        UserNotification::forUser(auth()->id())
            ->notArchived()
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true, 'unread' => 0]);
    }

    /* ═══════════════════════════════════════════════════════════
     | TOGGLE STAR
     ═══════════════════════════════════════════════════════════ */
    public function toggleStar(UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update([
            'starred_at' => $notification->starred_at ? null : now(),
        ]);

        return response()->json([
            'ok'      => true,
            'starred' => $notification->starred_at !== null,
            'message' => $notification->starred_at
                ? 'Ditambahkan ke Favorit'
                : 'Dihapus dari Favorit',
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | ARCHIVE / UNARCHIVE
     ═══════════════════════════════════════════════════════════ */
    public function archive(UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update(['archived_at' => now()]);

        return response()->json(['ok' => true, 'message' => 'Notifikasi diarsipkan.']);
    }

    public function unarchive(UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->update(['archived_at' => null]);

        return response()->json(['ok' => true, 'message' => 'Notifikasi dikembalikan.']);
    }

    /* ═══════════════════════════════════════════════════════════
     | DESTROY — Hapus 1
     ═══════════════════════════════════════════════════════════ */
    public function destroy(UserNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->delete();

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Notifikasi dihapus.',
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | DESTROY READ — Hapus semua yang sudah dibaca
     ═══════════════════════════════════════════════════════════ */
    public function destroyAll()
    {
        $deleted = UserNotification::forUser(auth()->id())
            ->whereNotNull('read_at')
            ->delete();

        return response()->json([
            'ok'      => true,
            'success' => true,
            'deleted' => $deleted,
            'message' => "{$deleted} notifikasi dibersihkan.",
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPER — Grouping label tanggal
     ═══════════════════════════════════════════════════════════ */
    private function groupLabel(\Carbon\Carbon $date): string
    {
        if ($date->isToday())    return 'Hari Ini';
        if ($date->isYesterday()) return 'Kemarin';
        if ($date->greaterThanOrEqualTo(now()->startOfWeek())) return 'Minggu Ini';
        if ($date->greaterThanOrEqualTo(now()->subDays(30)))   return 'Bulan Ini';
        return 'Lebih Lama';
    }
}
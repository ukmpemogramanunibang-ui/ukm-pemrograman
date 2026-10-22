<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\LmsGroup;
use App\Models\Lms\LmsItem;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // ═══════════════════════════════════════════════════════
        // 1. QUERY BUILDER — anti-N+1, semua dihitung 1x
        // ═══════════════════════════════════════════════════════
        $query = LmsItem::query()
            ->with(['group:id,name,code', 'creator:id,name'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->q);
                $q->where(function ($w) use ($term) {
                    $w->where('title', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('group_id'), fn ($q) => $q->where('group_id', $request->group_id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), function ($q) use ($request) {
                match ($request->status) {
                    'draft'       => $q->whereNull('published_at'),
                    'published'   => $q->whereNotNull('published_at')->where(function ($w) {
                        $w->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    }),
                    'overdue'     => $q->whereNotNull('deadline_at')->where('deadline_at', '<', now()),
                    'in_progress' => $q->whereNotNull('published_at')->where(function ($w) {
                        $w->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    }),
                    default       => null,
                };
            })
            ->when($request->filled('only_mine') && $request->only_mine === '1', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        // Kalau user bukan owner, hanya tampilkan item dia
        if (method_exists($user, 'hasRole') && ! $user->hasRole('owner')) {
            $query->where('created_by', $user->id);
        }

      $items = (clone $query)
    ->withCount([
        'recipients as metric_total',
        'recipients as metric_viewed'    => fn ($q) => $q->whereNotNull('viewed_at'),
        'recipients as metric_started'   => fn ($q) => $q->whereNotNull('started_at'),
        'recipients as metric_submitted' => fn ($q) => $q->whereNotNull('submitted_at'),
        'recipients as metric_completed' => fn ($q) => $q->whereIn('status', ['completed', 'done']),
        'recipients as metric_late'      => fn ($q) => $q->where('status', 'late'),
    ])
    ->paginate(15)
    ->withQueryString();

        // ═══════════════════════════════════════════════════════
        // 2. SUMMARY — KPI global (di luar pagination)
        // ═══════════════════════════════════════════════════════
        $allItems = (clone $query)
    ->withCount([
        'recipients as metric_total',
        'recipients as metric_viewed'    => fn ($q) => $q->whereNotNull('viewed_at'),
        'recipients as metric_started'   => fn ($q) => $q->whereNotNull('started_at'),
        'recipients as metric_submitted' => fn ($q) => $q->whereNotNull('submitted_at'),
        'recipients as metric_completed' => fn ($q) => $q->whereIn('status', ['completed', 'done']),
        'recipients as metric_late'      => fn ($q) => $q->where('status', 'late'),
    ])
    ->get();

$summary = [
    'total_items'       => $allItems->count(),
    'total_tasks'       => $allItems->where('type', 'task')->count(),
    'total_materials'   => $allItems->where('type', 'material')->count(),
    'total_recipients'  => 0,
    'total_done'        => 0,
    'total_late'        => 0,
    'total_missing'     => 0,
    'total_overdue'     => 0,
    'avg_completion'    => 0,
];

foreach ($allItems as $item) {
    $m = $item->metric; // pakai fast path karena sudah di-preload
    $summary['total_recipients'] += $m['total'];
    $summary['total_done']       += $m['completed'];
    $summary['total_late']       += $m['late'];
    $summary['total_missing']    += $m['missing'];

    if ($item->deadline_at && now()->gt($item->deadline_at)) {
        $summary['total_overdue']++;
    }
}

        // ═══════════════════════════════════════════════════════
        // 3. GROUPS untuk filter dropdown
        // ═══════════════════════════════════════════════════════
        $groups = LmsGroup::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('owner.lms.monitoring', compact('items', 'groups', 'summary'));
    }
    /**
 * GET /owner/lms/items/{item}/recipients
 * Return JSON daftar penerima + status submission masing-masing.
 * Dipakai oleh modal "Lihat Penerima" di halaman monitoring.
 */
public function recipients(LmsItem $item, Request $request)
{
    // Otorisasi: hanya pembuat atau owner yang boleh lihat detail
    $user = auth()->user();
    if ((int) $item->created_by !== (int) $user->id && ! $user->hasRole('owner')) {
        abort(403, 'Anda tidak berhak melihat data ini.');
    }

    $filter = $request->get('filter', 'all'); // all | done | late | missing | review

    $query = $item->recipients()
        ->with(['user:id,name,nim,email,avatar_url']);

    // Filter logic
    match ($filter) {
        'done'    => $query->whereNotNull('submitted_at')->whereIn('status', ['completed', 'done']),
        'review'  => $query->whereNotNull('submitted_at')->whereNull('graded_at'),
        'late'    => $query->where('status', 'late'),
        'missing' => $query->whereNull('submitted_at')->whereNull('started_at'),
        default   => null,
    };

    $recipients = $query->get()->map(function ($r) {
        $u = $r->user;
        return [
            'id'           => $r->id,
            'user_id'      => $u->id ?? null,
            'name'         => $u->name ?? 'Pengguna dihapus',
            'nim'          => $u->nim ?? null,
            'email'        => $u->email ?? null,
            'avatar_url'   => $u->avatar_url ?? null,
            'status'       => $r->status ?? 'new',
            'viewed_at'    => optional($r->viewed_at)->toIso8601String(),
            'started_at'   => optional($r->started_at)->toIso8601String(),
            'submitted_at' => optional($r->submitted_at)->toIso8601String(),
            'graded_at'    => optional($r->graded_at)->toIso8601String(),
            'score'        => $r->score ?? null,
            'attempts'     => (int) ($r->attempts ?? 1),
            'is_late'      => $r->submitted_at && $item->deadline_at
                              && $r->submitted_at->gt($item->deadline_at),
            // Anti-manipulasi: durasi kerja dari server time
            'work_duration_sec' => ($r->started_at && $r->submitted_at)
                ? $r->submitted_at->diffInSeconds($r->started_at)
                : null,
        ];
    });

    return response()->json([
        'ok'         => true,
        'item'       => [
            'id'          => $item->id,
            'title'       => $item->title,
            'type'        => $item->type,
            'deadline_at' => optional($item->deadline_at)->toIso8601String(),
            'metric'      => $item->metric,
        ],
        'filter'     => $filter,
        'count'      => $recipients->count(),
        'recipients' => $recipients,
    ]);
}
public function remind(LmsItem $item)
{
    $user = auth()->user();

    // Authorization
    if ((int) $item->created_by !== (int) $user->id
        && method_exists($user, 'hasRole') && ! $user->hasRole('owner')) {
        abort(403);
    }

    // ═══════════════════════════════════════════════════════
    // Deteksi kolom "penerima" di tabel notifications
    // (karena struktur tabel kamu sudah dimodifikasi)
    // ═══════════════════════════════════════════════════════
    $recipientColumn = null;
    foreach (['notifiable_id', 'user_id', 'recipient_id', 'to_user_id', 'target_user_id'] as $col) {
        if (\Schema::hasColumn('notifications', $col)) {
            $recipientColumn = $col;
            break;
        }
    }

    // Ambil penerima yang BELUM submit
    $missing = $item->recipients()
        ->whereNull('submitted_at')
        ->with('user')
        ->get();

    $sent   = 0;
    $skipped = 0;
    $failed  = [];

    foreach ($missing as $r) {
        if (! $r->user) continue;

        // ---------- Anti-duplicate reminder (max 1x / 6 jam) ----------
        if ($recipientColumn) {
            try {
                $recent = \DB::table('notifications')
                    ->where($recipientColumn, $r->user->id)
                    ->where(function ($w) use ($item) {
                        // Cek JSON item_id kalau kolom data ada
                        if (\Schema::hasColumn('notifications', 'data')) {
                            $w->where('data->item_id', $item->id);
                        } else {
                            // Fallback: cek kolom item_id kalau ada
                            if (\Schema::hasColumn('notifications', 'item_id')) {
                                $w->where('item_id', $item->id);
                            }
                        }
                    })
                    ->where('created_at', '>=', now()->subHours(6))
                    ->exists();

                if ($recent) { $skipped++; continue; }
            } catch (\Throwable $e) {
                // Kalau query cek duplicate gagal → lanjut saja (jangan block)
                \Log::warning('[LMS Remind] dedup check failed', [
                    'user_id' => $r->user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        // ---------- Kirim notifikasi ----------
        try {
            // Cek dulu apakah User pakai Notifiable trait & class notif ada
            if (class_exists(\App\Notifications\LmsReminderNotification::class)) {
                $r->user->notify(new \App\Notifications\LmsReminderNotification($item));
            } else {
                // Fallback: insert langsung ke tabel notifications
                \DB::table('notifications')->insert([
                    $recipientColumn ?? 'user_id' => $r->user->id,
                    'type'       => 'lms_reminder',
                    'title'      => 'Pengingat Tugas',
                    'message'    => 'Jangan lupa mengumpulkan: ' . $item->title,
                    'data'       => json_encode([
                        'item_id'    => $item->id,
                        'item_title' => $item->title,
                        'group_id'   => $item->group_id,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $sent++;
        } catch (\Throwable $e) {
            $failed[] = [
                'user_id' => $r->user->id,
                'name'    => $r->user->name,
                'error'   => $e->getMessage(),
            ];
            \Log::error('[LMS Remind] failed to notify user', [
                'user_id' => $r->user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    return response()->json([
        'ok'      => true,
        'sent'    => $sent,
        'skipped' => $skipped,
        'failed'  => count($failed),
        'total'   => $missing->count(),
        'details' => $failed,
    ]);
}
}
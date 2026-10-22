<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AttendancePenalty;
use App\Models\AttendancePenaltyDispute;
use App\Models\AttendancePenaltyRule;
use App\Models\Group;
use App\Models\User;
use App\Services\Attendance\PenaltyEngine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendancePenaltyController extends Controller
{
    public function __construct(protected PenaltyEngine $engine) {}

    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AttendancePenalty::class);

        [$from, $to] = $this->resolvePeriod($request);
        $filters     = $this->resolveFilters($request);

        $query = AttendancePenalty::query()
            ->with(['user:id,name,nim', 'group:id,name', 'session:id,title', 'rule:id,code,name,severity'])
            ->whereBetween('created_at', [$from, $to]);

        // ---- Filters ----
        if ($filters['q']) {
            $query->search($filters['q']);
        }
        if ($filters['status'])     $query->whereIn('status', $filters['status']);
        if ($filters['type'])       $query->whereIn('penalty_type', $filters['type']);
        if ($filters['violation'])  $query->whereIn('violation_type', $filters['violation']);
        if ($filters['severity'])   $query->whereIn('severity', $filters['severity']);
        if ($filters['payment'])    $query->whereIn('payment_status', $filters['payment']);
        if ($filters['group_id'])   $query->whereIn('group_id', $filters['group_id']);
        if ($filters['user_id'])    $query->whereIn('user_id', $filters['user_id']);
        if ($filters['min_amount']) $query->where('amount', '>=', (float) $filters['min_amount']);
        if ($filters['max_amount']) $query->where('amount', '<=', (float) $filters['max_amount']);

        $penalties = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Auto-update overdue status (side-effect ringan saat index dibuka)
        $this->refreshOverdueBatch($from, $to);

        return view('owner.attendance.penalties', [
            'penalties'    => $penalties,
            'stats'        => $this->computeStatistics($from, $to),
            'topPenalties' => $this->computeTopOffenders($from, $to),
            'distribution' => [
                'by_type'      => $this->distributionByType($from, $to),
                'by_severity'  => $this->distributionBySeverity($from, $to),
                'by_violation' => $this->distributionByViolation($from, $to),
            ],
            'trend'     => $this->computeTrend(),
            'alerts'    => $this->computeAlerts(),
            'reference' => $this->referenceData(),
            'meta'      => $this->meta($from, $to, $filters),
            'filters'   => $filters,
            'from'      => $from,
            'to'        => $to,
            'rules'     => AttendancePenaltyRule::active()->orderBy('priority')->get(),
        ]);
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show(AttendancePenalty $penalty)
    {
        Gate::authorize('view', $penalty);

        $penalty->load([
            'user:id,name,nim,avatar_url',
            'group:id,name',
            'session:id,title,scheduled_start_at',
            'record',
            'rule',
            'creator:id,name',
            'approver:id,name',
            'canceller:id,name',
            'disputes.user:id,name',
            'disputes.reviewer:id,name',
            'audits.actor:id,name',
        ]);

        return response()->json([
            'penalty'  => $this->serializeDetail($penalty),
            'timeline' => $penalty->audits->map(fn($a) => [
                'action'      => $a->action,
                'description' => $a->description,
                'actor'       => $a->actor->name ?? 'System',
                'at'          => $a->created_at->toIso8601String(),
                'at_human'    => $a->created_at->diffForHumans(),
                'before'      => $a->before,
                'after'       => $a->after,
            ]),
            'disputes' => $penalty->disputes->map(fn($d) => [
                'id'          => $d->id,
                'reason'      => $d->reason,
                'status'      => $d->status,
                'user'        => $d->user->name ?? null,
                'reviewer'    => $d->reviewer->name ?? null,
                'review_notes'=> $d->review_notes,
                'reviewed_at' => $d->reviewed_at?->toIso8601String(),
            ]),
        ]);
    }

    // =========================================================
    // ACTIONS
    // =========================================================
    public function approve(Request $r, AttendancePenalty $penalty)
    {
        Gate::authorize('approve', $penalty);
        $this->engine->approve($penalty, $r->user()->id);
        return back()->with('success', "Denda {$penalty->case_number} disetujui.");
    }

    public function reject(Request $r, AttendancePenalty $penalty)
    {
        Gate::authorize('approve', $penalty);
        $r->validate(['reason' => 'required|string|min:5|max:500']);
        $this->engine->reject($penalty, $r->reason);
        return back()->with('success', 'Denda ditolak.');
    }

    public function cancel(Request $r, AttendancePenalty $penalty)
    {
        Gate::authorize('cancel', $penalty);
        $r->validate(['reason' => 'required|string|min:5|max:500']);
        $this->engine->cancel($penalty, $r->reason);
        return back()->with('success', 'Denda dibatalkan.');
    }

    public function waive(Request $r, AttendancePenalty $penalty)
    {
        Gate::authorize('cancel', $penalty);
        $r->validate(['reason' => 'required|string|min:5|max:500']);
        $this->engine->waive($penalty, $r->reason);
        return back()->with('success', 'Denda dibebaskan.');
    }

    public function reverse(Request $r, AttendancePenalty $penalty)
    {
        Gate::authorize('cancel', $penalty);
        $r->validate(['reason' => 'required|string|min:5|max:500']);
        $this->engine->reverse($penalty, $r->reason);
        return back()->with('success', 'Reversal berhasil dicatat.');
    }

    public function pay(Request $r, AttendancePenalty $penalty)
    {
        Gate::authorize('update', $penalty);
        $r->validate([
            'amount' => 'required|numeric|min:1|max:' . $penalty->outstanding,
            'method' => 'nullable|string|max:32',
        ]);
        $this->engine->pay($penalty, (float) $r->amount, $r->method);
        return back()->with('success', 'Pembayaran tercatat.');
    }

    // =========================================================
    // DISPUTE
    // =========================================================
    public function disputeStore(Request $r, AttendancePenalty $penalty)
    {
        $r->validate(['reason' => 'required|string|min:10|max:1000']);

        DB::transaction(function () use ($r, $penalty) {
            AttendancePenaltyDispute::create([
                'penalty_id' => $penalty->id,
                'user_id'    => $r->user()->id,
                'reason'     => $r->reason,
                'status'     => 'open',
            ]);
            $penalty->update(['status' => 'disputed']);
            $penalty->addAudit('disputed', 'Keberatan diajukan oleh user.');
        });

        return back()->with('success', 'Keberatan dikirim. Menunggu review.');
    }

    public function disputeReview(Request $r, AttendancePenaltyDispute $dispute)
    {
        Gate::authorize('approve', $dispute->penalty);
        $r->validate([
            'decision' => 'required|in:accepted,rejected',
            'notes'    => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($r, $dispute) {
            $dispute->update([
                'status'       => $r->decision,
                'reviewed_by'  => $r->user()->id,
                'review_notes' => $r->notes,
                'reviewed_at'  => now(),
            ]);

            $penalty = $dispute->penalty;
            if ($r->decision === 'accepted') {
                $this->engine->waive($penalty, "Dispute accepted: {$r->notes}");
            } else {
                $penalty->update(['status' => 'active']);
                $penalty->addAudit('dispute_rejected', "Keberatan ditolak: {$r->notes}");
            }
        });

        return back()->with('success', 'Keberatan direview.');
    }

    // =========================================================
    // BULK ACTION
    // =========================================================
    public function bulk(Request $r)
    {
        $r->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:attendance_penalties,id',
            'action' => 'required|in:approve,cancel,waive,reject',
            'reason' => 'nullable|string|max:500',
        ]);

        $ids  = $r->ids;
        $done = 0;

        foreach (AttendancePenalty::whereIn('id', $ids)->get() as $p) {
            try {
                match ($r->action) {
                    'approve' => $this->engine->approve($p),
                    'cancel'  => $this->engine->cancel($p, $r->reason ?: 'Bulk cancel'),
                    'waive'   => $this->engine->waive($p, $r->reason ?: 'Bulk waive'),
                    'reject'  => $this->engine->reject($p, $r->reason ?: 'Bulk reject'),
                };
                $done++;
            } catch (\Throwable $e) {
                \Log::warning('Bulk action failed', ['id' => $p->id, 'error' => $e->getMessage()]);
            }
        }

        return back()->with('success', "{$done} kasus berhasil diproses.");
    }

    // =========================================================
    // EXPORT CSV
    // =========================================================
    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolvePeriod($request);
        $filters     = $this->resolveFilters($request);

        $q = AttendancePenalty::with(['user:id,name,nim', 'group:id,name', 'session:id,title'])
            ->whereBetween('created_at', [$from, $to]);

        if ($filters['status'])     $q->whereIn('status', $filters['status']);
        if ($filters['type'])       $q->whereIn('penalty_type', $filters['type']);
        if ($filters['severity'])   $q->whereIn('severity', $filters['severity']);
        if ($filters['payment'])    $q->whereIn('payment_status', $filters['payment']);
        if ($filters['q'])          $q->search($filters['q']);

        $filename = 'penalties-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($q) {
            $fh = fopen('php://output', 'w');
            fwrite($fh, "\xEF\xBB\xBF"); // BOM untuk Excel

            fputcsv($fh, [
                'Case Number','Tanggal','User','NIM','Kelompok','Sesi',
                'Pelanggaran','Jenis','Severity','Status','Poin',
                'Denda','Dibayar','Sisa','Jatuh Tempo','Dibuat Oleh',
            ]);

            $q->orderBy('created_at')->chunk(500, function ($rows) use ($fh) {
                foreach ($rows as $p) {
                    fputcsv($fh, [
                        $p->case_number,
                        $p->created_at->format('Y-m-d H:i'),
                        $p->user->name ?? '-',
                        $p->user->nim  ?? '-',
                        $p->group->name ?? '-',
                        $p->session->title ?? '-',
                        self::VIOLATION_LABELS[$p->violation_type]['label'] ?? $p->violation_type,
                        self::TYPE_LABELS[$p->penalty_type]['label'] ?? $p->penalty_type,
                        self::SEVERITY_LABELS[$p->severity]['label'] ?? $p->severity,
                        self::STATUS_LABELS[$p->status]['label'] ?? $p->status,
                        $p->points,
                        $p->amount,
                        $p->amount_paid,
                        $p->outstanding,
                        $p->due_at?->format('Y-m-d') ?? '-',
                        $p->creator->name ?? 'System',
                    ]);
                }
            });

            fclose($fh);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // =========================================================
    // RECONCILE LEDGER
    // =========================================================
    public function reconcile(): \Illuminate\Http\RedirectResponse
    {
        $count = 0;
        AttendancePenalty::unpaid()->each(function ($p) use (&$count) {
            if ($p->is_overdue && $p->status !== 'overdue') {
                $p->update(['status' => 'overdue']);
                $p->addAudit('overdue', 'Status diubah otomatis (jatuh tempo).');
                $count++;
            }
        });
        return back()->with('success', "{$count} kasus ditandai overdue.");
    }

    // =========================================================
    // INTERNAL
    // =========================================================
    protected function resolvePeriod(Request $r): array
    {
        $period = $r->input('period', 'this_month');
        return match ($period) {
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'this_year'  => [now()->startOfYear(), now()->endOfYear()],
            'all_time'   => [now()->subYears(5)->startOfYear(), now()->endOfYear()],
            'custom'     => [
                Carbon::parse($r->input('from', now()->startOfMonth()))->startOfDay(),
                Carbon::parse($r->input('to', now()->endOfMonth()))->endOfDay(),
            ],
            default      => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    protected function resolveFilters(Request $r): array
    {
        return [
            'period'    => $r->input('period', 'this_month'),
            'q'         => trim((string) $r->input('q', '')),
            'status'    => (array) $r->input('status', []),
            'type'      => (array) $r->input('type', []),
            'violation' => (array) $r->input('violation', []),
            'severity'  => (array) $r->input('severity', []),
            'payment'   => (array) $r->input('payment', []),
            'group_id'  => (array) $r->input('group_id', []),
            'user_id'   => (array) $r->input('user_id', []),
            'min_amount'=> $r->input('min_amount'),
            'max_amount'=> $r->input('max_amount'),
        ];
    }

    protected function meta(Carbon $from, Carbon $to, array $filters): array
    {
        return [
            'from'    => $from->toDateString(),
            'to'      => $to->toDateString(),
            'period'  => $filters['period'],
            'periods' => [
                'this_month' => 'Bulan Ini',
                'last_month' => 'Bulan Lalu',
                'this_year'  => 'Tahun Ini',
                'all_time'   => 'Semua Waktu',
                'custom'     => 'Custom',
            ],
        ];
    }

    protected function referenceData(): array
    {
        return [
            'status_labels'    => self::STATUS_LABELS,
            'type_labels'      => self::TYPE_LABELS,
            'severity_labels'  => self::SEVERITY_LABELS,
            'violation_labels' => self::VIOLATION_LABELS,
            'payment_labels'   => self::PAYMENT_LABELS,
            'groups'           => Group::select('id', 'name')->orderBy('name')->get(),
        ];
    }

    protected function refreshOverdueBatch(Carbon $from, Carbon $to): void
    {
        // Hanya update status di memory-level query kalau ada yang lewat due
        AttendancePenalty::where('status', 'active')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->where('amount', '>', 0)
            ->update(['status' => 'overdue']);
    }

    protected function computeStatistics(Carbon $from, Carbon $to): array
    {
        $rows = AttendancePenalty::forPeriod($from, $to)->get();

        return [
            'total_cases'   => $rows->count(),
            'active_cases'  => $rows->whereIn('status', ['active','partial','overdue','disputed'])->count(),
            'pending_cases' => $rows->whereIn('status', ['pending','review'])->count(),
            'resolved'      => $rows->whereIn('status', ['paid','waived','reversed','cancelled'])->count(),

            'total_amount'  => (float) $rows->sum('amount'),
            'total_paid'    => (float) $rows->sum('amount_paid'),
            'total_unpaid'  => (float) $rows->sum(fn($p) => $p->outstanding),
            'total_overdue' => (float) $rows->where('status', 'overdue')->sum(fn($p) => $p->outstanding),

            'total_points'  => (int) $rows->sum('points'),
            'total_warning' => (int) $rows->where('warning_level', '>', 0)->count(),
            'critical'      => $rows->where('severity', 'critical')->count(),
            'high'          => $rows->where('severity', 'high')->count(),

            'this_month'    => AttendancePenalty::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'prev_month'    => AttendancePenalty::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->count(),
        ];
    }

    protected function computeTopOffenders(Carbon $from, Carbon $to): array
    {
        return DB::table('attendance_penalties')
            ->select('user_id',
                DB::raw('COUNT(*) as total_cases'),
                DB::raw('SUM(points) as total_points'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(amount_paid) as total_paid'),
                DB::raw('MAX(warning_level) as max_warning'))
            ->whereBetween('created_at', [$from, $to])
            ->whereNull('deleted_at')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 0')
            ->orderByDesc('total_cases')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->user = User::select('id', 'name', 'nim')->find($row->user_id);
                return $row;
            })
            ->filter(fn($row) => $row->user)
            ->values()
            ->all();
    }

    protected function distributionByType(Carbon $from, Carbon $to): array
    {
        return AttendancePenalty::select('penalty_type', DB::raw('COUNT(*) as c'), DB::raw('SUM(amount) as a'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('penalty_type')
            ->get()
            ->map(fn($r) => [
                'key'   => $r->penalty_type,
                'label' => self::TYPE_LABELS[$r->penalty_type]['label'] ?? ucfirst($r->penalty_type),
                'color' => self::TYPE_LABELS[$r->penalty_type]['color'] ?? 'slate',
                'icon'  => self::TYPE_LABELS[$r->penalty_type]['icon']  ?? 'fa-circle',
                'count' => (int) $r->c,
                'amount'=> (float) $r->a,
            ])
            ->values()->all();
    }

    protected function distributionBySeverity(Carbon $from, Carbon $to): array
    {
        return AttendancePenalty::select('severity', DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('severity')
            ->get()
            ->map(fn($r) => [
                'key'   => $r->severity,
                'label' => self::SEVERITY_LABELS[$r->severity]['label'] ?? ucfirst($r->severity),
                'color' => self::SEVERITY_LABELS[$r->severity]['color'] ?? 'slate',
                'count' => (int) $r->c,
            ])
            ->values()->all();
    }

    protected function distributionByViolation(Carbon $from, Carbon $to): array
    {
        return AttendancePenalty::select('violation_type', DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('violation_type')
            ->get()
            ->map(fn($r) => [
                'key'   => $r->violation_type,
                'label' => self::VIOLATION_LABELS[$r->violation_type]['label'] ?? ucfirst($r->violation_type),
                'color' => self::VIOLATION_LABELS[$r->violation_type]['color'] ?? 'slate',
                'count' => (int) $r->c,
            ])
            ->values()->all();
    }

    protected function computeTrend(): array
    {
        $rows = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $q = AttendancePenalty::whereMonth('created_at', $m->month)
                                  ->whereYear('created_at', $m->year);
            $rows[] = [
                'period' => $m->translatedFormat('M'),
                'year'   => $m->year,
                'count'  => (clone $q)->count(),
                'amount' => (float) (clone $q)->sum('amount'),
                'paid'   => (float) (clone $q)->sum('amount_paid'),
            ];
        }
        return $rows;
    }

    protected function computeAlerts(): array
{
    return [
        'overdue'  => AttendancePenalty::where('status', 'overdue')->count(),
        'disputed' => AttendancePenalty::where('status', 'disputed')->count(),
        'pending'  => AttendancePenalty::whereIn('status', ['pending','review'])->count(),
        'critical' => AttendancePenalty::where('severity', 'critical')->active()->count(),
        'repeat'   => DB::table('attendance_penalties')
                            ->select('user_id')                    // ✅ FIX: hanya select kolom yang di-group
                            ->whereYear('created_at', now()->year)
                            ->whereNull('deleted_at')
                            ->groupBy('user_id')
                            ->havingRaw('COUNT(*) >= 3')
                            ->get()
                            ->count(),
    ];
}

    protected function serializeDetail(AttendancePenalty $p): array
    {
        return [
            'id'              => $p->id,
            'case_number'     => $p->case_number,
            'user'            => $p->user ? ['name' => $p->user->name, 'nim' => $p->user->nim] : null,
            'group'           => $p->group?->name,
            'session'         => $p->session?->title,
            'violation'       => $p->violation_type,
            'violation_label' => self::VIOLATION_LABELS[$p->violation_type]['label'] ?? $p->violation_type,
            'penalty_type'    => $p->penalty_type,
            'severity'        => $p->severity,
            'points'          => $p->points,
            'amount'          => (float) $p->amount,
            'amount_paid'     => (float) $p->amount_paid,
            'outstanding'     => $p->outstanding,
            'currency'        => $p->currency,
            'status'          => $p->status,
            'status_label'    => self::STATUS_LABELS[$p->status]['label'] ?? $p->status,
            'payment_status'  => $p->payment_status,
            'warning_level'   => $p->warning_level,
            'reason'          => $p->reason,
            'issued_at'       => $p->issued_at?->toIso8601String(),
            'due_at'          => $p->due_at?->toIso8601String(),
            'paid_at'         => $p->paid_at?->toIso8601String(),
            'creator'         => $p->creator?->name,
            'approver'        => $p->approver?->name,
            'canceller'       => $p->canceller?->name,
            'cancellation_reason' => $p->cancellation_reason,
        ];
    }

    // =========================================================
    // LABELS
    // =========================================================
    public const STATUS_LABELS = [
        'pending'   => ['label' => 'Menunggu',   'color' => 'amber',   'icon' => 'fa-hourglass-half'],
        'review'    => ['label' => 'Direview',   'color' => 'blue',    'icon' => 'fa-eye'],
        'approved'  => ['label' => 'Disetujui',  'color' => 'indigo',  'icon' => 'fa-check'],
        'active'    => ['label' => 'Aktif',      'color' => 'red',     'icon' => 'fa-fire'],
        'disputed'  => ['label' => 'Keberatan',  'color' => 'fuchsia', 'icon' => 'fa-gavel'],
        'waived'    => ['label' => 'Dibebaskan', 'color' => 'cyan',    'icon' => 'fa-shield'],
        'paid'      => ['label' => 'Lunas',      'color' => 'emerald', 'icon' => 'fa-check-double'],
        'partial'   => ['label' => 'Sebagian',   'color' => 'lime',    'icon' => 'fa-circle-half-stroke'],
        'overdue'   => ['label' => 'Terlambat',  'color' => 'rose',    'icon' => 'fa-clock'],
        'cancelled' => ['label' => 'Dibatalkan', 'color' => 'slate',   'icon' => 'fa-ban'],
        'reversed'  => ['label' => 'Reversal',   'color' => 'zinc',    'icon' => 'fa-rotate-left'],
        'expired'   => ['label' => 'Kadaluarsa', 'color' => 'stone',   'icon' => 'fa-hourglass-end'],
    ];

    public const TYPE_LABELS = [
        'warning'        => ['label' => 'Peringatan',  'color' => 'amber',    'icon' => 'fa-triangle-exclamation'],
        'point'          => ['label' => 'Poin',        'color' => 'red',      'icon' => 'fa-minus-circle'],
        'financial'      => ['label' => 'Finansial',   'color' => 'rose',     'icon' => 'fa-money-bill-wave'],
        'administrative' => ['label' => 'Administratif','color' => 'violet',  'icon' => 'fa-folder'],
        'disciplinary'   => ['label' => 'Disiplin',    'color' => 'fuchsia',  'icon' => 'fa-gavel'],
        'combined'       => ['label' => 'Kombinasi',   'color' => 'purple',   'icon' => 'fa-layer-group'],
    ];

    public const SEVERITY_LABELS = [
        'low'      => ['label' => 'Ringan',  'color' => 'emerald'],
        'medium'   => ['label' => 'Sedang',  'color' => 'amber'],
        'high'     => ['label' => 'Berat',   'color' => 'orange'],
        'critical' => ['label' => 'Kritis',  'color' => 'red'],
    ];

    public const VIOLATION_LABELS = [
        'unexcused_absence' => ['label' => 'Bolos Tanpa Keterangan', 'color' => 'red'],
        'late_arrival'      => ['label' => 'Terlambat',              'color' => 'amber'],
        'early_leave'       => ['label' => 'Pulang Awal',            'color' => 'orange'],
        'attendance_fraud'  => ['label' => 'Manipulasi Absensi',     'color' => 'rose'],
        'no_permit'         => ['label' => 'Tanpa Izin',             'color' => 'rose'],
    ];

    public const PAYMENT_LABELS = [
        'unpaid'   => ['label' => 'Belum Bayar', 'color' => 'red'],
        'partial'  => ['label' => 'Sebagian',    'color' => 'amber'],
        'paid'     => ['label' => 'Lunas',       'color' => 'emerald'],
        'refunded' => ['label' => 'Dikembalikan','color' => 'slate'],
    ];
}
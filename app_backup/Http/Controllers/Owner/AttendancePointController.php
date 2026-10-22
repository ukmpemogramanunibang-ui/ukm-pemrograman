<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\PointAccount;
use App\Models\PointTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendancePointController extends Controller
{
    // ============================================================
    // KONSTANTA — SINGLE SOURCE OF TRUTH
    // ============================================================
    public const TYPE_LABELS = [
        'ATTENDANCE_ON_TIME'         => ['label' => 'Hadir Tepat Waktu',     'color' => 'emerald', 'icon' => 'fa-check-circle'],
        'ATTENDANCE_LATE_APPROVED'   => ['label' => 'Terlambat + Izin',      'color' => 'teal',    'icon' => 'fa-clock'],
        'ATTENDANCE_LATE_UNAPPROVED' => ['label' => 'Terlambat Tanpa Izin',  'color' => 'amber',   'icon' => 'fa-hourglass-half'],
        'EXCUSED_ABSENCE'            => ['label' => 'Izin Tidak Hadir',      'color' => 'sky',     'icon' => 'fa-file-medical'],
        'LATE_EXCUSED_ABSENCE'       => ['label' => 'Izin Terlambat',        'color' => 'orange',  'icon' => 'fa-file-signature'],
        'UNEXCUSED_ABSENCE'          => ['label' => 'Bolos',                 'color' => 'red',     'icon' => 'fa-user-slash'],
        'MANUAL_ADJUSTMENT'          => ['label' => 'Koreksi Admin',         'color' => 'violet',  'icon' => 'fa-pen'],
        'BONUS'                      => ['label' => 'Bonus',                 'color' => 'fuchsia', 'icon' => 'fa-gift'],
        'REVERSAL'                   => ['label' => 'Pembatalan Transaksi',  'color' => 'slate',   'icon' => 'fa-rotate-left'],
        'CORRECTION'                 => ['label' => 'Koreksi Poin',          'color' => 'indigo',  'icon' => 'fa-wrench'],
    ];

    public const STATUS_LABELS = [
        'pending'  => ['label' => 'Menunggu Review', 'color' => 'amber',   'icon' => 'fa-hourglass-half'],
        'final'    => ['label' => 'Final',           'color' => 'emerald', 'icon' => 'fa-lock'],
        'reversed' => ['label' => 'Dibatalkan',      'color' => 'slate',   'icon' => 'fa-rotate-left'],
        'void'     => ['label' => 'Void',            'color' => 'gray',    'icon' => 'fa-ban'],
    ];

    public const PERIOD_PRESETS = [
        'today'    => 'Hari Ini',
        'week'     => 'Minggu Ini',
        'month'    => 'Bulan Ini',
        'quarter'  => 'Kuartal Ini',
        'semester' => 'Semester Ini',
        'year'     => 'Tahun Ini',
        'all'      => 'Semua',
        'custom'   => 'Custom',
    ];

    // Cache kolom per request (menghindari Schema::hasColumn berulang)
    protected ?array $columnCache = null;

    // ============================================================
    // ============  AUTHORIZATION  ===============================
    // ============================================================
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================================================
    // ============  ENTRY — DASHBOARD UTAMA  =====================
    // ============================================================
    public function index(Request $request)
    {
        $filters = $this->resolveFilters($request);
        [$from, $to] = $this->resolvePeriod($filters);

        $ledgerQ    = $this->baseLedgerQuery($from, $to, $filters);
        $statistics = $this->computeStatistics($from, $to, $filters);

        $ledgers = (clone $ledgerQ)
            ->with($this->ledgerEagerLoads())
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $topUsers     = $this->topUsers($from, $to, $filters, 10);
        $distribution = $this->typeDistribution($from, $to, $filters);
        $periodTotals = $this->periodTotals($from, $to, $filters);
        $integrity    = $this->checkIntegrity($from, $to);
        $pendingCount = $this->pendingCount();

        $meta = $this->buildMeta($filters, $from, $to);

        return view('owner.attendance.points', compact(
            'ledgers', 'statistics', 'topUsers', 'distribution', 'periodTotals',
            'integrity', 'pendingCount', 'meta', 'filters'
        ));
    }

    // ============================================================
    // ============  JSON ENDPOINT — realtime / chart  ============
    // ============================================================
    public function data(Request $request)
    {
        $filters = $this->resolveFilters($request);
        [$from, $to] = $this->resolvePeriod($filters);

        return response()->json([
            'range'        => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'statistics'   => $this->computeStatistics($from, $to, $filters),
            'top_users'    => $this->topUsers($from, $to, $filters, 10),
            'distribution' => $this->typeDistribution($from, $to, $filters),
            'period'       => $this->periodTotals($from, $to, $filters),
        ]);
    }

    // ============================================================
    // ============  SCORECARD PER USER  ==========================
    // ============================================================
    public function user(Request $request, $userId)
    {
        $user = User::with(['level', 'groups'])->findOrFail($userId);

        $filters = $this->resolveFilters($request);
        [$from, $to] = $this->resolvePeriod($filters);

        $scorecard = $this->userScorecard($user, $from, $to);

        $userLedger = $this->baseLedgerQuery($from, $to, $filters)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $monthlyTrend = $this->userMonthlyTrend($user, 12);

        return view('owner.attendance.points-user', compact(
            'user', 'scorecard', 'userLedger', 'monthlyTrend', 'filters'
        ));
    }

    // ============================================================
    // ============  REKAP PER KELOMPOK  ==========================
    // ============================================================
    public function groups(Request $request)
    {
        $filters = $this->resolveFilters($request);
        [$from, $to] = $this->resolvePeriod($filters);

        $groups = $this->groupRecap($from, $to, $filters);

        return view('owner.attendance.points-groups', compact('groups', 'filters'));
    }

    // ============================================================
    // ============  REVERSAL TRANSAKSI  ==========================
    // ============================================================
    public function reverse(Request $request, $transactionId)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:500',
        ]);

        $tx = $this->pointTransactionModel()::findOrFail($transactionId);

        if (($tx->status ?? 'final') !== 'final') {
            return back()->with('error', 'Transaksi ini tidak dapat dibatalkan (status: ' . ($tx->status ?? 'final') . ').');
        }

        try {
            DB::transaction(function () use ($tx, $request) {
                // Pakai method dari model — otomatis handle point_account_id,
                // user_id, type, amount, points, dan recalculate balance.
                $tx->createReversal($request->reason, auth()->id());
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal reversal: ' . $e->getMessage());
        }

        return back()->with('success', 'Transaksi berhasil dibatalkan dengan reversal log.');
    }

    // ============================================================
    // ============  KOREKSI POIN  ================================
    // ============================================================
    public function correct(Request $request, $transactionId)
    {
        $request->validate([
            'new_points' => 'required|integer|between:-100,100',
            'reason'     => 'required|string|min:5|max:500',
        ]);

        $tx = $this->pointTransactionModel()::findOrFail($transactionId);

        if (($tx->status ?? 'final') === 'reversed') {
            return back()->with('error', 'Transaksi sudah dibatalkan — buat transaksi baru sebagai gantinya.');
        }

        $delta = (int) $request->new_points - (int) $tx->points;

        if ($delta === 0) {
            return back()->with('info', 'Tidak ada perubahan poin.');
        }

        try {
            DB::transaction(function () use ($tx, $request, $delta) {
                $model = $this->pointTransactionModel();

                // Model akan otomatis sync type/amount/points/user_id/account_id
                $model::create([
                    'point_account_id'      => $tx->point_account_id,
                    'user_id'               => $tx->user_id,
                    'entry_type'            => 'CORRECTION',
                    'points'                => $delta,
                    'reason'                => $request->reason,
                    'status'                => 'final',
                    'corrected_by'          => auth()->id(),
                    'attendance_session_id' => $tx->attendance_session_id,
                    'attendance_record_id'  => $tx->attendance_record_id,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal koreksi: ' . $e->getMessage());
        }

        return back()->with('success', 'Koreksi poin berhasil dicatat sebagai transaksi baru.');
    }

    // ============================================================
    // ============  APPROVAL — PENDING → FINAL  ==================
    // ============================================================
    public function approvePending(Request $request, $transactionId)
    {
        $tx = $this->pointTransactionModel()::findOrFail($transactionId);

        if (($tx->status ?? 'final') !== 'pending') {
            return back()->with('error', 'Transaksi bukan dalam status menunggu.');
        }

        $tx->update([
            'status'       => 'final',
            'finalized_at' => now(),
            'corrected_by' => auth()->id(),
        ]);

        // Recalculate balance akun
        if ($tx->point_account_id) {
            optional($tx->account)->recalculateBalance();
        }

        return back()->with('success', 'Transaksi disetujui dan difinalkan.');
    }

    public function rejectPending(Request $request, $transactionId)
    {
        $request->validate(['reason' => 'required|string|min:5|max:500']);

        $tx = $this->pointTransactionModel()::findOrFail($transactionId);

        if (($tx->status ?? 'final') !== 'pending') {
            return back()->with('error', 'Transaksi bukan dalam status menunggu.');
        }

        $tx->update([
            'status'          => 'void',
            'reversal_reason' => $request->reason,
            'corrected_by'    => auth()->id(),
        ]);

        if ($tx->point_account_id) {
            optional($tx->account)->recalculateBalance();
        }

        return back()->with('success', 'Transaksi ditolak dan di-void.');
    }

    // ============================================================
    // ============  SIMULASI POIN  ===============================
    // ============================================================
    public function simulate(Request $request)
    {
        $input = $request->validate([
            'on_time'      => 'integer|min:0|max:100',
            'late_permit'  => 'integer|min:0|max:100',
            'late'         => 'integer|min:0|max:100',
            'permit'       => 'integer|min:0|max:100',
            'late_excused' => 'integer|min:0|max:100',
            'absent'       => 'integer|min:0|max:100',
        ]);

        $rules = $this->ruleMap();
        $breakdown = [];
        $total = 0;

        $map = [
            'on_time'      => 'ATTENDANCE_ON_TIME',
            'late_permit'  => 'ATTENDANCE_LATE_APPROVED',
            'late'         => 'ATTENDANCE_LATE_UNAPPROVED',
            'permit'       => 'EXCUSED_ABSENCE',
            'late_excused' => 'LATE_EXCUSED_ABSENCE',
            'absent'       => 'UNEXCUSED_ABSENCE',
        ];

        foreach ($map as $key => $type) {
            $count = (int) ($input[$key] ?? 0);
            $pts   = (int) ($rules[$type] ?? 0);
            $sub   = $count * $pts;
            $total += $sub;

            $breakdown[] = [
                'type'   => $type,
                'label'  => self::TYPE_LABELS[$type]['label'] ?? $type,
                'count'  => $count,
                'points' => $pts,
                'sub'    => $sub,
            ];
        }

        return response()->json(['total' => $total, 'breakdown' => $breakdown]);
    }

    // ============================================================
    // ============  INTEGRITY CHECK  =============================
    // ============================================================
    public function integrity(Request $request)
    {
        [$from, $to] = $this->resolvePeriod($this->resolveFilters($request));
        return response()->json($this->checkIntegrity($from, $to));
    }

    // ============================================================
    // ============  EXPORT CSV  ==================================
    // ============================================================
    public function export(Request $request): StreamedResponse
    {
        $filters = $this->resolveFilters($request);
        [$from, $to] = $this->resolvePeriod($filters);

        $rows = $this->baseLedgerQuery($from, $to, $filters)
            ->orderByDesc('created_at')
            ->limit(10000)
            ->get();

        $filename = 'points-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Waktu', 'User', 'Tipe', 'Status', 'Poin', 'Alasan', 'Sesi', 'Rule']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    optional($r->created_at)->toDateTimeString(),
                    $r->user->name ?? ('#' . $r->user_id),
                    $r->entry_type ?? '',
                    $r->status ?? 'final',
                    $r->points,
                    $r->reason ?? '',
                    $r->attendance_session_id ?? '',
                    $r->point_rule_id ?? '',
                ]);
            }
            fclose($out);
        }, $filename);
    }

    // ============================================================
    // ==================  QUERY BUILDERS  ========================
    // ============================================================
    protected function baseLedgerQuery(Carbon $from, Carbon $to, array $filters)
    {
        $q = $this->pointTransactionModel()::query()
            ->whereBetween('created_at', [$from, $to]);

        // Hanya tampilkan yang punya user_id (skip orphan kalau ada)
        if ($this->hasColumn('user_id')) {
            $q->whereNotNull('user_id');
        }

        if ($filters['q'] !== '') {
            $q->where(function ($w) use ($filters) {
                $w->where('reason', 'like', '%' . $filters['q'] . '%')
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', '%' . $filters['q'] . '%'));
            });
        }

        if (!empty($filters['entry_type']) && $this->hasColumn('entry_type')) {
            $q->whereIn('entry_type', $filters['entry_type']);
        }

        if (!empty($filters['status']) && $this->hasColumn('status')) {
            $q->whereIn('status', $filters['status']);
        }

        if (!empty($filters['user_id']) && $this->hasColumn('user_id')) {
            $q->whereIn('user_id', $filters['user_id']);
        }

        if (!empty($filters['group_id'])) {
            $q->whereHas('user.groups', fn ($gq) => $gq->whereIn('groups.id', $filters['group_id']));
        }

        if (!empty($filters['role_id'])) {
            $q->whereHas('user', fn ($uq) => $uq->whereIn('level_id', $filters['role_id']));
        }

        if ($filters['points_min'] !== null && $this->hasColumn('points')) {
            $q->where('points', '>=', (int) $filters['points_min']);
        }
        if ($filters['points_max'] !== null && $this->hasColumn('points')) {
            $q->where('points', '<=', (int) $filters['points_max']);
        }

        return $q;
    }

    protected function ledgerEagerLoads(): array
    {
        return ['user:id,name,nim,level_id'];
    }

    protected function pointTransactionModel(): string
    {
        if (class_exists(PointTransaction::class)) {
            return PointTransaction::class;
        }
        return \App\Models\AttendancePointTransaction::class;
    }

    // ============================================================
    // =============  KOLOM CHECK (dengan cache)  =================
    // ============================================================
    protected function hasColumn(string $column): bool
    {
        if ($this->columnCache === null) {
            $this->columnCache = Schema::getColumnListing('point_transactions');
        }
        return in_array($column, $this->columnCache, true);
    }

    // ============================================================
    // =============  PERIODE & FILTER  ===========================
    // ============================================================
    protected function resolveFilters(Request $request): array
    {
        return [
            'q'          => trim((string) $request->input('q', '')),
            'period'     => $request->input('period', 'month'),
            'from'       => $request->input('from'),
            'to'         => $request->input('to'),
            'entry_type' => (array) $request->input('entry_type', []),
            'status'     => (array) $request->input('status', []),
            'user_id'    => (array) $request->input('user_id', []),
            'group_id'   => (array) $request->input('group_id', []),
            'role_id'    => (array) $request->input('role_id', []),
            'points_min' => $request->filled('points_min') ? (int) $request->input('points_min') : null,
            'points_max' => $request->filled('points_max') ? (int) $request->input('points_max') : null,
        ];
    }

    /**
     * @return array{0:Carbon, 1:Carbon}
     */
    protected function resolvePeriod(array $filters): array
    {
        $now = Carbon::now();

        return match ($filters['period']) {
            'today'    => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week'     => [$now->copy()->startOfWeek(Carbon::MONDAY), $now->copy()->endOfWeek(Carbon::SUNDAY)],
            'quarter'  => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()],
            'semester' => $now->month <= 6
                            ? [Carbon::create($now->year, 1, 1)->startOfDay(), Carbon::create($now->year, 6, 30)->endOfDay()]
                            : [Carbon::create($now->year, 7, 1)->startOfDay(), Carbon::create($now->year, 12, 31)->endOfDay()],
            'year'     => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all'      => [Carbon::create(2000, 1, 1)->startOfDay(), $now->copy()->endOfYear()],
            'custom'   => [
                $filters['from'] ? Carbon::parse($filters['from'])->startOfDay() : $now->copy()->startOfMonth(),
                $filters['to']   ? Carbon::parse($filters['to'])->endOfDay()     : $now->copy()->endOfMonth(),
            ],
            default    => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    // ============================================================
    // =============  STATISTIK  ==================================
    // ============================================================
    protected function computeStatistics(Carbon $from, Carbon $to, array $filters): array
    {
        $base = $this->baseLedgerQuery($from, $to, $filters);

        // Transaksi yang dihitung untuk saldo: final (dan null demi kompat data lama)
        // Reversed TIDAK dihitung dua kali — reversal record sendiri sudah final.
        $finalOnly = (clone $base)->where(function ($q) {
            $q->whereNull('status')->orWhere('status', 'final');
        });

        $earned = (int) (clone $finalOnly)->where('points', '>', 0)->sum('points');
        $lost   = (int) (clone $finalOnly)->where('points', '<', 0)->sum('points');

        return [
            'total_entries'  => (clone $base)->count(),
            'total_earned'   => $earned,
            'total_penalty'  => $lost,
            'total_balance'  => $earned + $lost,
            'pending_count'  => (clone $base)->where('status', 'pending')->count(),
            'reversed_count' => (clone $base)->where('status', 'reversed')->count(),
            'unique_users'   => (clone $base)->distinct('user_id')->count('user_id'),
        ];
    }

    protected function pendingCount(): int
    {
        return $this->pointTransactionModel()::where('status', 'pending')->count();
    }

    protected function topUsers(Carbon $from, Carbon $to, array $filters, int $limit): array
    {
        return $this->baseLedgerQuery($from, $to, $filters)
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'final');
            })
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit($limit)
            ->with('user:id,name,nim,level_id,avatar_url')
            ->get()
            ->toArray();
    }

    protected function typeDistribution(Carbon $from, Carbon $to, array $filters): array
    {
        $rows = $this->baseLedgerQuery($from, $to, $filters)
            ->select('entry_type',
                     DB::raw('COUNT(*) as total'),
                     DB::raw('SUM(points) as points'))
            ->groupBy('entry_type')
            ->orderByDesc('total')
            ->get();

        return $rows->map(function ($r) {
            $meta = self::TYPE_LABELS[$r->entry_type]
                 ?? ['label' => Str::headline((string) $r->entry_type), 'color' => 'gray', 'icon' => 'fa-circle'];

            return [
                'type'   => $r->entry_type,
                'label'  => $meta['label'],
                'color'  => $meta['color'],
                'icon'   => $meta['icon'],
                'count'  => (int) $r->total,
                'points' => (int) $r->points,
            ];
        })->toArray();
    }

    protected function periodTotals(Carbon $from, Carbon $to, array $filters): array
    {
        $rows = $this->baseLedgerQuery($from, $to, $filters)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw('SUM(CASE WHEN points > 0 THEN points ELSE 0 END) as earned'),
                DB::raw('SUM(CASE WHEN points < 0 THEN points ELSE 0 END) as lost')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        return $rows->map(fn ($r) => [
            'period' => $r->ym,
            'earned' => (int) $r->earned,
            'lost'   => (int) $r->lost,
            'net'    => (int) ($r->earned + $r->lost),
        ])->toArray();
    }

    // ============================================================
    // =============  SCORECARD & TREND  ==========================
    // ============================================================
    protected function userScorecard(User $user, Carbon $from, Carbon $to): array
    {
        $base = $this->pointTransactionModel()::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'final');
            });

        $sumByType = (clone $base)
            ->select('entry_type', DB::raw('SUM(points) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('entry_type')
            ->get()
            ->keyBy('entry_type');

        $present = (int) ($sumByType['ATTENDANCE_ON_TIME']->count ?? 0);
        $late    = (int) ($sumByType['ATTENDANCE_LATE_APPROVED']->count ?? 0)
                 + (int) ($sumByType['ATTENDANCE_LATE_UNAPPROVED']->count ?? 0);
        $permit  = (int) ($sumByType['EXCUSED_ABSENCE']->count ?? 0)
                 + (int) ($sumByType['LATE_EXCUSED_ABSENCE']->count ?? 0);
        $absent  = (int) ($sumByType['UNEXCUSED_ABSENCE']->count ?? 0);

        $totalSessions = max(1, $present + $late + $permit + $absent);

        return [
            'total_points'    => (int) (clone $base)->sum('points'),
            'attendance_rate' => round((($present + $late) / $totalSessions) * 100, 1),
            'on_time_rate'    => round(($present / $totalSessions) * 100, 1),
            'late_rate'       => round(($late    / $totalSessions) * 100, 1),
            'excused_rate'    => round(($permit  / $totalSessions) * 100, 1),
            'unexcused_rate'  => round(($absent  / $totalSessions) * 100, 1),
            'counts'          => [
                'present' => $present,
                'late'    => $late,
                'permit'  => $permit,
                'absent'  => $absent,
            ],
            'consistency'     => round(
                ($present * 1.0 + $late * 0.6 + $permit * 0.4 - $absent * 0.8) / $totalSessions * 100,
                1
            ),
        ];
    }

    protected function userMonthlyTrend(User $user, int $months): array
    {
        $start = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $rows = $this->pointTransactionModel()::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw('SUM(points) as net')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $out = [];
        for ($i = 0; $i < $months; $i++) {
            $d = $start->copy()->addMonths($i);
            $key = $d->format('Y-m');
            $out[] = ['period' => $key, 'net' => (int) ($rows[$key]->net ?? 0)];
        }
        return $out;
    }

    // ============================================================
    // =============  GROUP RECAP  ================================
    // ============================================================
    protected function groupRecap(Carbon $from, Carbon $to, array $filters): array
    {
        $groupModel = null;
        if (class_exists(\App\Models\Group::class)) {
            $groupModel = \App\Models\Group::class;
        } elseif (class_exists(\App\Models\Kelompok::class)) {
            $groupModel = \App\Models\Kelompok::class;
        }
        if (!$groupModel) return [];

        return $groupModel::query()
            ->withCount('members')
            ->get()
            ->map(function ($g) use ($from, $to) {
                $userIds = $g->members()->pluck('users.id');

                $total = $userIds->isEmpty()
                    ? 0
                    : (int) $this->pointTransactionModel()::whereIn('user_id', $userIdps ?? $userIds)
                        ->whereBetween('created_at', [$from, $to])
                        ->where(function ($q) {
                            $q->whereNull('status')->orWhere('status', 'final');
                        })
                        ->sum('points');

                return [
                    'id'      => $g->id,
                    'name'    => $g->name,
                    'members' => $g->members_count,
                    'total'   => $total,
                    'average' => $g->members_count ? round($total / $g->members_count, 1) : 0,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    // ============================================================
    // =============  INTEGRITY CHECK  ============================
    // ============================================================
    protected function checkIntegrity(Carbon $from, Carbon $to): array
    {
        // 1. Cek kolom users.points
        if (!Schema::hasColumn('users', 'points')) {
            return ['ok' => true, 'reason' => 'Kolom users.points tidak ada — memakai ledger saja.'];
        }

        // 2. Cek ledger vs kolom
        $mismatch = DB::table('users')
            ->leftJoin('point_transactions', function ($join) {
                $join->on('users.id', '=', 'point_transactions.user_id')
                     ->where(function ($q) {
                         $q->whereNull('point_transactions.status')
                           ->orWhere('point_transactions.status', 'final');
                     });
            })
            ->groupBy('users.id', 'users.points', 'users.name')
            ->havingRaw('COALESCE(SUM(point_transactions.points),0) <> users.points')
            ->select(
                'users.id', 'users.name', 'users.points',
                DB::raw('COALESCE(SUM(point_transactions.points),0) as ledger_total')
            )
            ->limit(20)
            ->get();

        // 3. Cek tabel point_accounts (kalau ada)
        $accountMismatch = [];
        if (Schema::hasTable('point_accounts') && Schema::hasColumn('point_accounts', 'balance')) {
            $accountMismatch = DB::table('point_accounts')
                ->leftJoin('point_transactions', function ($join) {
                    $join->on('point_accounts.id', '=', 'point_transactions.point_account_id')
                         ->where(function ($q) {
                             $q->whereNull('point_transactions.status')
                               ->orWhere('point_transactions.status', 'final');
                         });
                })
                ->groupBy('point_accounts.id', 'point_accounts.balance', 'point_accounts.user_id')
                ->havingRaw('COALESCE(SUM(point_transactions.points),0) <> point_accounts.balance')
                ->select(
                    'point_accounts.id', 'point_accounts.user_id', 'point_accounts.balance',
                    DB::raw('COALESCE(SUM(point_transactions.points),0) as ledger_total')
                )
                ->limit(20)
                ->get()
                ->toArray();
        }

        return [
            'ok'                => $mismatch->isEmpty() && empty($accountMismatch),
            'mismatch'          => $mismatch->toArray(),
            'account_mismatch'  => $accountMismatch,
        ];
    }

    // ============================================================
    // =============  RULE MAP  ===================================
    // ============================================================
    protected function ruleMap(): array
    {
        $default = [
            'ATTENDANCE_ON_TIME'         =>  3,
            'ATTENDANCE_LATE_APPROVED'   =>  2,
            'ATTENDANCE_LATE_UNAPPROVED' =>  1,
            'EXCUSED_ABSENCE'            =>  0,
            'LATE_EXCUSED_ABSENCE'       => -1,
            'UNEXCUSED_ABSENCE'          => -2,
        ];

        if (!Schema::hasTable('attendance_point_rules')) return $default;

        $rows = DB::table('attendance_point_rules')
            ->where('active', true)
            ->where(function ($q) {
                $q->whereNull('effective_from')->orWhere('effective_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', now());
            })
            ->orderByDesc('version')
            ->get()
            ->keyBy('code');

        foreach ($rows as $code => $r) {
            $default[$code] = (int) $r->points;
        }

        return $default;
    }

    // ============================================================
    // =============  META  =======================================
    // ============================================================
    protected function buildMeta(array $filters, Carbon $from, Carbon $to): array
    {
        return [
            'from'          => $from->toDateString(),
            'to'            => $to->toDateString(),
            'period'        => $filters['period'],
            'periods'       => self::PERIOD_PRESETS,
            'type_labels'   => self::TYPE_LABELS,
            'status_labels' => self::STATUS_LABELS,
            'rule_map'      => $this->ruleMap(),
        ];
    }
}
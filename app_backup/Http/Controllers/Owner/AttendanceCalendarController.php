<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AttendanceCalendarController extends Controller
{
    public const STATUS_COLORS = [
        'draft'     => 'bg-gray-400',
        'scheduled' => 'bg-blue-500',
        'ready'     => 'bg-indigo-500',
        'open'      => 'bg-emerald-500',
        'paused'    => 'bg-amber-500',
        'extended'  => 'bg-purple-500',
        'closed'    => 'bg-slate-500',
        'expired'   => 'bg-red-500',
        'cancelled' => 'bg-rose-700',
    ];

    public const STATUS_LABELS = [
        'draft'     => 'Draft',
        'scheduled' => 'Dijadwalkan',
        'ready'     => 'Siap',
        'open'      => 'Berlangsung',
        'paused'    => 'Ditunda',
        'extended'  => 'Diperpanjang',
        'closed'    => 'Selesai',
        'expired'   => 'Kadaluarsa',
        'cancelled' => 'Dibatalkan',
    ];
public const STATUS_GRADIENTS = [
    'draft'     => 'from-slate-500 to-slate-700',
    'scheduled' => 'from-blue-500 to-indigo-600',
    'ready'     => 'from-indigo-500 to-violet-600',
    'open'      => 'from-emerald-400 to-teal-600',
    'paused'    => 'from-amber-400 to-orange-600',
    'extended'  => 'from-purple-500 to-fuchsia-600',
    'closed'    => 'from-zinc-500 to-slate-700',
    'expired'   => 'from-red-500 to-rose-600',
    'cancelled' => 'from-rose-600 to-red-800',
];

public const STATUS_RING = [
    'draft'     => 'ring-slate-400/40',
    'scheduled' => 'ring-blue-400/50',
    'ready'     => 'ring-indigo-400/50',
    'open'      => 'ring-emerald-400/60',
    'paused'    => 'ring-amber-400/60',
    'extended'  => 'ring-purple-400/60',
    'closed'    => 'ring-slate-400/40',
    'expired'   => 'ring-red-400/60',
    'cancelled' => 'ring-rose-400/60',
];
    /**
     * Kandidat nama relasi yang mungkin dipakai di model.
     * Nanti dipilih yang pertama ada (method_exists).
     */
    protected const REL_CANDIDATES = [
        'room'         => ['room', 'ruangan'],
        'groups'       => ['groups', 'kelompoks', 'kelompok', 'attendanceGroups'],
        'officer'      => ['officer', 'user', 'creator', 'openedBy', 'petugas'],
        'participants' => ['participants', 'attendances', 'peserta', 'details'],
    ];

    // ============================================================
    // ENTRY
    // ============================================================
    public function index(Request $request)
    {
        $this->authorizeViewAny();

        $view    = $request->input('view', 'month');
        $cursor  = $this->resolveCursor($request);
        $filters = $this->resolveFilters($request);

        [$gridStart, $gridEnd] = $this->resolveRange($view, $cursor);

        $sessions = $this->querySessions($gridStart, $gridEnd, $filters)
            ->map(fn ($s) => $this->decorateSession($s));

        $conflicts = $this->detectConflicts($sessions);

        $byDate    = $sessions->groupBy(fn ($s) => optional($s->scheduled_start_at)->format('Y-m-d'));
        $stats     = $this->computeStatistics($sessions);
        $today     = $this->buildTodayAgenda();
        $readiness = $this->checkReadiness($sessions);
        $meta      = $this->buildMeta($view, $cursor, $gridStart, $gridEnd);

        return view('owner.attendance.calendar', compact(
            'sessions', 'byDate', 'stats', 'conflicts', 'today',
            'readiness', 'meta', 'filters', 'cursor', 'view'
        ));
    }

    public function data(Request $request)
    {
        $this->authorizeViewAny();

        $view    = $request->input('view', 'month');
        $cursor  = $this->resolveCursor($request);
        $filters = $this->resolveFilters($request);
        [$gridStart, $gridEnd] = $this->resolveRange($view, $cursor);

        $sessions = $this->querySessions($gridStart, $gridEnd, $filters)
            ->map(fn ($s) => $this->serializeSession($this->decorateSession($s)));

        return response()->json([
            'range'    => ['start' => $gridStart->toDateString(), 'end' => $gridEnd->toDateString()],
            'view'     => $view,
            'sessions' => $sessions->values(),
            'stats'    => $this->computeStatistics(
                $sessions->map(fn ($s) => (object) $s)
            ),
        ]);
    }

    // ============================================================
    // AUTHORIZATION
    // ============================================================
    protected function authorizeViewAny(): void
    {
        if (!class_exists(\App\Policies\AttendanceSessionPolicy::class)) {
            return;
        }
        Gate::authorize('viewAny', AttendanceSession::class);
    }

    // ============================================================
    // RELASI — deteksi yang benar-benar ada di model
    // ============================================================
    /** @var array<string,string|null> */
    protected static array $resolvedRelations = [];

    /**
     * Kembalikan nama relasi yang VALID untuk sebuah key logis.
     * Contoh: relationName('groups') → 'kelompoks' kalau model tidak punya 'groups'.
     */
    protected function relationName(string $key): ?string
    {
        if (array_key_exists($key, self::$resolvedRelations)) {
            return self::$resolvedRelations[$key];
        }

        $model = new AttendanceSession;
        $found = null;

        foreach (self::REL_CANDIDATES[$key] ?? [$key] as $candidate) {
            if (method_exists($model, $candidate)) {
                $found = $candidate;
                break;
            }
        }

        return self::$resolvedRelations[$key] = $found;
    }

    /** Daftar relasi valid untuk dipakai di ->with([...]) */
    protected function eagerRelations(): array
    {
        $map = [
            'room'         => 'room:id,name,room_code',
            'groups'       => 'groups:id,name',
            'officer'      => 'officer:id,name',
        ];

        $out = [];
        foreach ($map as $key => $spec) {
            $rel = $this->relationName($key);
            if (!$rel) continue;

            // ganti prefix relasi dengan nama relasi yang sebenarnya
            [$name, $cols] = array_pad(explode(':', $spec, 2), 2, null);
            $out[] = $cols ? "{$rel}:{$cols}" : $rel;
        }
        return $out;
    }

    // ============================================================
    // RANGE / CURSOR
    // ============================================================
    protected function resolveCursor(Request $request): Carbon
    {
        if ($request->filled('date')) {
            return Carbon::parse($request->input('date'))->startOfDay();
        }
        if ($request->filled('month') && $request->filled('year')) {
            return Carbon::create((int) $request->year, (int) $request->month, 1)->startOfDay();
        }
        return Carbon::today();
    }

    protected function resolveRange(string $view, Carbon $cursor): array
    {
        return match ($view) {
            'week'  => [
                $cursor->copy()->startOfWeek(Carbon::SUNDAY),
                $cursor->copy()->endOfWeek(Carbon::SATURDAY),
            ],
            'day'   => [$cursor->copy()->startOfDay(), $cursor->copy()->endOfDay()],
            'agenda'=> [
                $cursor->copy()->startOfDay(),
                $cursor->copy()->addDays(60)->endOfDay(),
            ],
            default => [
                $cursor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY),
                $cursor->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY),
            ],
        };
    }

    // ============================================================
    // FILTER
    // ============================================================
    protected function resolveFilters(Request $request): array
    {
        return [
            'q'          => trim((string) $request->input('q', '')),
            'status'     => (array) $request->input('status', []),
            'group_id'   => (array) $request->input('group_id', []),
            'room_id'    => (array) $request->input('room_id', []),
            'mode'       => (array) $request->input('mode', []),
            'method'     => (array) $request->input('method', []),
            'officer_id' => (array) $request->input('officer_id', []),
            'date_from'  => $request->input('date_from'),
            'date_to'    => $request->input('date_to'),
        ];
    }

    // ============================================================
    // QUERY
    // ============================================================
    protected function querySessions(Carbon $start, Carbon $end, array $filters)
    {
        $q = AttendanceSession::query()
            ->whereBetween('scheduled_start_at', [$start, $end]);

        $eager = $this->eagerRelations();
        if (!empty($eager)) {
            $q->with($eager);
        }

        // withCount participants hanya jika relasinya ada
        $participantsRel = $this->relationName('participants');
        if ($participantsRel) {
            $q->withCount([
                "{$participantsRel} as participants_total",
                "{$participantsRel} as participants_present" => fn ($qq) => $qq->where('status', 'present'),
                "{$participantsRel} as participants_late"    => fn ($qq) => $qq->where('status', 'late'),
                "{$participantsRel} as participants_permit"  => fn ($qq) => $qq->where('status', 'permit'),
                "{$participantsRel} as participants_absent"  => fn ($qq) => $qq->where('status', 'absent'),
            ]);
        }

        if ($filters['q'] !== '') {
            $q->where('title', 'like', '%' . $filters['q'] . '%');
        }
        if (!empty($filters['status']) && Schema::hasColumn((new AttendanceSession)->getTable(), 'session_status')) {
            $q->whereIn('session_status', $filters['status']);
        }
        if (!empty($filters['group_id'])) {
            $groupRel = $this->relationName('groups');
            if ($groupRel) {
                $q->whereHas($groupRel, fn ($qq) => $qq->whereIn('id', $filters['group_id']));
            }
        }
        if (!empty($filters['room_id']) && Schema::hasColumn((new AttendanceSession)->getTable(), 'room_id')) {
            $q->whereIn('room_id', $filters['room_id']);
        }
        if (!empty($filters['mode']) && Schema::hasColumn((new AttendanceSession)->getTable(), 'mode')) {
            $q->whereIn('mode', $filters['mode']);
        }
        if (!empty($filters['method']) && Schema::hasColumn((new AttendanceSession)->getTable(), 'method')) {
            $q->whereIn('method', $filters['method']);
        }
        if (!empty($filters['officer_id']) && Schema::hasColumn((new AttendanceSession)->getTable(), 'officer_id')) {
            $q->whereIn('officer_id', $filters['officer_id']);
        }
        if ($filters['date_from']) {
            $q->where('scheduled_start_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if ($filters['date_to']) {
            $q->where('scheduled_start_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        return $q->orderBy('scheduled_start_at')->get();
    }

    // ============================================================
    // DECORATE
    // ============================================================
    protected function decorateSession(AttendanceSession $s): AttendanceSession
    {
        $now   = now();
        $auto  = $s->session_status ?? 'draft';
        $open  = $s->attendance_open_at     ?? null;
        $dead  = $s->attendance_deadline_at ?? null;
        $close = $s->attendance_close_at    ?? null;
        $start = $s->scheduled_start_at     ?? null;
        $end   = $s->scheduled_end_at       ?? null;
        $s->status_gradient = self::STATUS_GRADIENTS[$auto] ?? 'from-gray-400 to-gray-600';
$s->status_ring     = self::STATUS_RING[$auto]     ?? 'ring-gray-400/40';

        if (in_array($auto, ['scheduled', 'ready', 'draft'], true)) {
            if ($open && $now->greaterThanOrEqualTo($open) && (!$dead || $now->lessThan($dead))) {
                $auto = 'open';
            }
        }
        if ($auto === 'open' && $dead && $now->greaterThanOrEqualTo($dead)) {
            $auto = 'closed';
        }
        if ($auto === 'open' && $close && $now->greaterThanOrEqualTo($close)) {
            $auto = 'closed';
        }
        if (in_array($auto, ['scheduled', 'ready'], true) && $end && $now->greaterThan($end)) {
            $auto = 'expired';
        }

        $s->auto_status  = $auto;
        $s->status_color = self::STATUS_COLORS[$auto] ?? 'bg-gray-400';
        $s->status_label = self::STATUS_LABELS[$auto] ?? ucfirst((string) $auto);

        if ($open && $now->lessThan($open)) {
            $s->countdown_seconds = (int) $now->diffInSeconds($open, false);
            $s->countdown_label   = 'Mulai dalam';
        } elseif ($auto === 'open' && $dead) {
            $s->countdown_seconds = (int) $now->diffInSeconds($dead, false);
            $s->countdown_label   = 'Sisa waktu absensi';
        } else {
            $s->countdown_seconds = null;
            $s->countdown_label   = null;
        }

        $s->is_started_early = (bool) ($open && $start && $open->lt($start));
        $s->has_changes      = (bool) ($s->updated_at && $s->created_at
                                && $s->updated_at->gt($s->created_at->copy()->addMinutes(5)));
        $s->is_conflicting   = false;

        return $s;
    }

    protected function serializeSession(AttendanceSession $s): array
    {
        // Ambil koleksi relasi dengan aman
        $roomRel    = $this->relationName('room');
        $groupsRel  = $this->relationName('groups');
        $officerRel = $this->relationName('officer');

        $room    = $roomRel    && $s->relationLoaded($roomRel)    ? $s->{$roomRel}    : null;
        $officer = $officerRel && $s->relationLoaded($officerRel) ? $s->{$officerRel} : null;
        $groups  = $groupsRel  && $s->relationLoaded($groupsRel)  ? $s->{$groupsRel}  : collect();

        return [
            'id'           => $s->id,
            'title'        => $s->title,
            'status'       => $s->auto_status,
            'status_label' => $s->status_label,
            'status_color' => $s->status_color,
            'status_gradient' => $s->status_gradient ?? 'from-gray-400 to-gray-600',
'status_ring'     => $s->status_ring     ?? 'ring-gray-400/40',
            'start'        => optional($s->scheduled_start_at)->toIso8601String(),
            'end'          => optional($s->scheduled_end_at)->toIso8601String(),
            'open_at'      => optional($s->attendance_open_at ?? null)->toIso8601String(),
            'deadline_at'  => optional($s->attendance_deadline_at ?? null)->toIso8601String(),
            'room'         => $room    ? ['id' => $room->id,    'name' => $room->name]    : null,
            'officer'      => $officer ? ['id' => $officer->id, 'name' => $officer->name] : null,
            'groups'       => $groups instanceof \Illuminate\Support\Collection
                                ? $groups->map(fn ($g) => ['id' => $g->id, 'name' => $g->name])->values()->all()
                                : [],
            'mode'         => $s->mode   ?? null,
            'method'       => $s->method ?? null,
            'participants' => [
                'total'   => $s->participants_total   ?? 0,
                'present' => $s->participants_present ?? 0,
                'late'    => $s->participants_late    ?? 0,
                'permit'  => $s->participants_permit  ?? 0,
                'absent'  => $s->participants_absent  ?? 0,
            ],
            'countdown_seconds'=> $s->countdown_seconds,
            'countdown_label'  => $s->countdown_label,
            'is_started_early' => (bool) $s->is_started_early,
            'has_changes'      => (bool) $s->has_changes,
            'is_conflicting'   => (bool) $s->is_conflicting,
            'url_show'         => route('owner.attendance.show', $s),
        ];
    }

    // ============================================================
    // STATISTIK
    // ============================================================
    protected function computeStatistics($sessions): array
    {
        $base = [
            'total' => $sessions->count(),
            'upcoming' => 0, 'ongoing' => 0, 'paused' => 0,
            'closed' => 0, 'expired' => 0, 'cancelled' => 0, 'conflicts' => 0,
            'attendance' => ['target' => 0, 'present' => 0, 'late' => 0, 'permit' => 0, 'absent' => 0],
        ];

        foreach ($sessions as $s) {
            $st = $s->auto_status ?? ($s->session_status ?? null);
            match ($st) {
                'scheduled', 'ready', 'draft' => $base['upcoming']++,
                'open'                        => $base['ongoing']++,
                'paused'                      => $base['paused']++,
                'closed'                      => $base['closed']++,
                'expired'                     => $base['expired']++,
                'cancelled'                   => $base['cancelled']++,
                default                       => null,
            };
            $base['attendance']['target']  += $s->participants_total   ?? 0;
            $base['attendance']['present'] += $s->participants_present ?? 0;
            $base['attendance']['late']    += $s->participants_late    ?? 0;
            $base['attendance']['permit']  += $s->participants_permit  ?? 0;
            $base['attendance']['absent']  += $s->participants_absent  ?? 0;
        }

        return $base;
    }

    // ============================================================
    // KONFLIK
    // ============================================================
    protected function detectConflicts($sessions): array
    {
        $roomConflicts    = [];
        $officerConflicts = [];

        $roomCol    = Schema::hasColumn((new AttendanceSession)->getTable(), 'room_id')    ? 'room_id'    : null;
        $officerCol = Schema::hasColumn((new AttendanceSession)->getTable(), 'officer_id') ? 'officer_id' : null;

        if ($roomCol) {
            foreach ($sessions->groupBy($roomCol) as $id => $list) {
                if (!$id) continue;
                $sorted = $list->sortBy('scheduled_start_at')->values();
                for ($i = 0; $i < $sorted->count(); $i++) {
                    for ($j = $i + 1; $j < $sorted->count(); $j++) {
                        $a = $sorted[$i]; $b = $sorted[$j];
                        if ($a->scheduled_start_at < $b->scheduled_end_at
                            && $b->scheduled_start_at < $a->scheduled_end_at) {
                            $a->is_conflicting = true;
                            $b->is_conflicting = true;
                            $roomConflicts[] = ['a' => $a->id, 'b' => $b->id, 'room_id' => $id];
                        }
                    }
                }
            }
        }

        if ($officerCol) {
            foreach ($sessions->groupBy($officerCol) as $id => $list) {
                if (!$id) continue;
                $sorted = $list->sortBy('scheduled_start_at')->values();
                for ($i = 0; $i < $sorted->count(); $i++) {
                    for ($j = $i + 1; $j < $sorted->count(); $j++) {
                        $a = $sorted[$i]; $b = $sorted[$j];
                        if ($a->scheduled_start_at < $b->scheduled_end_at
                            && $b->scheduled_start_at < $a->scheduled_end_at) {
                            $a->is_conflicting = true;
                            $b->is_conflicting = true;
                            $officerConflicts[] = ['a' => $a->id, 'b' => $b->id, 'officer_id' => $id];
                        }
                    }
                }
            }
        }

        return [
            'rooms'    => $roomConflicts,
            'officers' => $officerConflicts,
            'total'    => count($roomConflicts) + count($officerConflicts),
        ];
    }

    // ============================================================
    // READINESS
    // ============================================================
    protected function checkReadiness($sessions): array
    {
        $issues = [];
        $groupsRel = $this->relationName('groups');

        foreach ($sessions as $s) {
            if (in_array($s->auto_status ?? null, ['closed','expired','cancelled'], true)) continue;

            if (Schema::hasColumn((new AttendanceSession)->getTable(), 'room_id') && !$s->room_id) {
                $issues[] = ['session_id' => $s->id, 'title' => $s->title, 'issue' => 'Ruangan belum ditentukan'];
            }
            if ($groupsRel) {
                $groups = $s->relationLoaded($groupsRel) ? $s->{$groupsRel} : collect();
                if ($groups instanceof \Illuminate\Support\Collection && $groups->isEmpty()) {
                    $issues[] = ['session_id' => $s->id, 'title' => $s->title, 'issue' => 'Belum ada kelompok target'];
                }
            }
            $hasOpenCol = Schema::hasColumn((new AttendanceSession)->getTable(), 'attendance_open_at');
            $hasDeadCol = Schema::hasColumn((new AttendanceSession)->getTable(), 'attendance_deadline_at');
            if ($hasOpenCol && $hasDeadCol && ((!$s->attendance_open_at) || (!$s->attendance_deadline_at))) {
                $issues[] = ['session_id' => $s->id, 'title' => $s->title, 'issue' => 'Jendela absensi belum lengkap'];
            }
            if ((int) ($s->participants_total ?? 0) === 0 && $this->relationName('participants')) {
                $issues[] = ['session_id' => $s->id, 'title' => $s->title, 'issue' => 'Tidak ada peserta terdaftar'];
            }
        }
        return $issues;
    }

    // ============================================================
    // AGENDA HARI INI
    // ============================================================
    protected function buildTodayAgenda(): array
    {
        $q = AttendanceSession::query()
            ->whereDate('scheduled_start_at', Carbon::today())
            ->orderBy('scheduled_start_at');

        $eager = $this->eagerRelations();
        if (!empty($eager)) {
            $q->with($eager);
        }

        return $q->get()
            ->map(fn ($s) => $this->serializeSession($this->decorateSession($s)))
            ->all();
    }

    // ============================================================
    // META
    // ============================================================
    protected function buildMeta(string $view, Carbon $cursor, Carbon $start, Carbon $end): array
    {
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        return [
            'view'        => $view,
            'cursor'      => $cursor->toDateString(),
            'range_start' => $start->toDateString(),
            'range_end'   => $end->toDateString(),
            'label'       => match ($view) {
                'week'   => 'Minggu ' . $start->translatedFormat('d M') . ' – ' . $end->translatedFormat('d M Y'),
                'day'    => $cursor->translatedFormat('l, d F Y'),
                'agenda' => 'Agenda 60 Hari',
                default  => $months[$cursor->month - 1] . ' ' . $cursor->year,
            },
            'prev'         => $this->shiftCursor($view, $cursor, -1),
            'next'         => $this->shiftCursor($view, $cursor, +1),
            'today'        => ['date' => Carbon::today()->toDateString()],
            'statuses'     => self::STATUS_COLORS,
            'status_labels'=> self::STATUS_LABELS,
        ];
    }

    protected function shiftCursor(string $view, Carbon $cursor, int $dir): array
    {
        $c = $cursor->copy();
        match ($view) {
            'week' => $c->addWeeks($dir),
            'day'  => $c->addDays($dir),
            default => $c->addMonths($dir),
        };
        return [
            'month' => $c->month,
            'year'  => $c->year,
            'date'  => $c->toDateString(),
            'view'  => $view,
        ];
    }
}
<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceTarget;
use App\Models\AttendanceRecord;
use App\Models\AttendancePermission;
use App\Models\AttendancePointLedger;
use App\Models\AttendanceAuditLog;
use App\Models\AttendanceSessionChange;
use App\Models\RoomSchedule;
use App\Models\Room;
use App\Models\User;
use App\Models\Group;
use App\Services\Attendance\AttendanceEngine;
use App\Services\Attendance\TargetResolver;
use App\Services\Attendance\ReconciliationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Audit\AuditService;   // ← IMPORT di atas

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceEngine $engine,
        protected TargetResolver $targetResolver,
        protected ReconciliationService $reconciliation
    ) {}

    /* ══════════════════════════════════════════════════════════════
     * 1. INDEX — Command Center
     * ══════════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $this->autoSyncSessions();

        $query = AttendanceSession::with(['room', 'schedule', 'creator', 'records', 'permissions'])
            ->withCount('records');

        if ($request->filled('status')) {
            $query->where('session_status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('scheduled_start_at', $request->date);
        } else {
            $query->where(function ($q) {
                $q->whereDate('scheduled_start_at', today())
                  ->orWhereIn('session_status', ['open', 'scheduled', 'extended', 'paused']);
            });
        }
        if ($request->filled('attendance_type')) {
            $query->where('attendance_type', $request->attendance_type);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"));
        }

        $sessions = $query
            ->orderByRaw("FIELD(session_status, 'open', 'extended', 'paused', 'scheduled', 'closed', 'expired', 'cancelled')")
            ->orderBy('scheduled_start_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $sessions->getCollection()->transform(function ($s) {
            $s->stats             = $s->computeStats();
            $s->time_status       = $s->time_status;
            $s->time_status_label = $s->time_status_label;
            $s->attention         = $s->needsAttention();
            return $s;
        });

        $todaySessions = AttendanceSession::with('records')->whereDate('scheduled_start_at', today())->get();
        $todayIds      = $todaySessions->pluck('id');

        $stats = [
            'total'     => $todaySessions->count(),
            'aktif'     => $todaySessions->where('session_status', 'open')->count(),
            'scheduled' => $todaySessions->where('session_status', 'scheduled')->count(),
            'selesai'   => $todaySessions->whereIn('session_status', ['closed', 'expired'])->count(),
            'hadir'     => AttendanceRecord::whereIn('session_id', $todayIds)
                                ->whereIn('status', ['present','late','late_excused','manual'])->count(),
            'terlambat' => AttendanceRecord::whereIn('session_id', $todayIds)
                                ->whereIn('status', ['late','late_excused'])->count(),
            'izin'      => AttendanceRecord::whereIn('session_id', $todayIds)
                                ->whereIn('status', ['excused_absent','absent_excused'])->count(),
            'bolos'     => AttendanceRecord::whereIn('session_id', $todayIds)
                                ->where('status', 'absent')->count(),
            'target'    => $todaySessions->sum('target_count'),
        ];
        $stats['belum'] = max(0, $stats['target'] - ($stats['hadir'] + $stats['izin'] + $stats['bolos']));

        $alerts = $this->buildAlerts($sessions->getCollection());

        $timeline = $todaySessions->sortBy('attendance_open_at')->map(fn ($s) => [
            'id'          => $s->id,
            'title'       => $s->title,
            'status'      => $s->session_status,
            'time_status' => $s->time_status,
            'open_at'     => $s->attendance_open_at,
            'close_at'    => $s->attendance_close_at,
            'event_at'    => $s->scheduled_start_at,
            'target'      => $s->target_count,
        ])->values();

        return view('owner.attendance.index', compact('sessions', 'stats', 'alerts', 'timeline'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 2. CREATE — Form buat sesi baru
     * ══════════════════════════════════════════════════════════════ */
public function create(Request $request)
{
    /* ─────────── 1. PREFILL dari ?schedule_id= ─────────── */
    $prefillSchedule = null;
    if ($request->filled('schedule_id')) {
        $prefillSchedule = \App\Models\RoomSchedule::with('room')
            ->find($request->schedule_id);
    }

    /* ─────────── 2. DAFTAR JADWAL (RoomSchedule) ─────────── */
    $schedules = \App\Models\RoomSchedule::query()
        ->with('room:id,name,building')
        ->orderBy('start_datetime', 'desc')
        ->limit(100)
        ->get([
            'id', 'title', 'start_datetime', 'end_datetime',
            'room_id', 'status', 'activity_type',
        ]);

    /* ─────────── 3. DAFTAR RUANGAN ─────────── */
    $rooms = \App\Models\Room::query()
        ->orderBy('name')
        ->get(['id', 'name', 'building', 'floor', 'capacity_normal']);

    /* ─────────── 4. KELOMPOK ─────────── */
    $groups = \App\Models\Group::query()
        ->orderBy('name')
        ->get(['id', 'name', 'code']);

    /* ─────────── 5. DIVISI ─────────── */
    $divisions = \App\Models\Division::query()
        ->orderBy('name')
        ->get(['id', 'name', 'code']);

    /* ─────────── 6. GENERASI (namespace Financial) ─────────── */
    $generations = collect();
    if (class_exists(\App\Models\Financial\Generation::class)) {
        $generations = \App\Models\Financial\Generation::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'entry_year']);
    }

    /* ─────────── 7. RETURN VIEW ─────────── */
    return view('owner.attendance.create', compact(
        'schedules',
        'rooms',
        'prefillSchedule',
        'groups',
        'divisions',
        'generations'
    ));
}

/* ══════════════════════════════════════════════════════════════
 * 3. STORE — Simpan sesi baru (LENGKAP + AUDIT + TARGET DATA)
 * ══════════════════════════════════════════════════════════════ */
public function store(Request $request)
{
    /* ─────────── VALIDASI ─────────── */
    $validated = $request->validate([
        'title'                 => 'required|string|max:150',
        'description'           => 'nullable|string|max:500',
        'attendance_type'       => 'required|in:regular,event,competition,meeting,training,workshop,seminar,tournament,external,custom',
        'attendance_mode'       => 'required|in:offline,online,hybrid',
        'schedule_id'           => 'nullable|exists:room_schedules,id',
        'room_id'               => 'nullable|exists:rooms,id',
        'shift_name'            => 'nullable|string|max:100',

        'target_type'           => 'required|in:all,group,division,generation,mixed',
        'target_roles'          => 'nullable|array',
        'target_roles.*'        => 'string|max:50',
        'target_groups'         => 'nullable|array',
        'target_groups.*'       => 'integer|exists:groups,id',
        'target_divisions'      => 'nullable|array',
        'target_divisions.*'    => 'integer|exists:divisions,id',
        'target_generations'    => 'nullable|array',
        'target_generations.*'  => 'integer',

        'allowed_methods'       => 'required|array|min:1',
        'allowed_methods.*'     => 'in:manual,nfc,qr',

        'scheduled_start_at'    => 'required|date',
        'scheduled_end_at'      => 'required|date|after:scheduled_start_at',
        'attendance_open_at'    => 'required|date',
        'attendance_close_at'   => 'required|date|after:attendance_open_at',
    ], [
        'title.required'              => 'Judul sesi wajib diisi.',
        'title.max'                   => 'Judul maksimal 150 karakter.',
        'attendance_type.required'    => 'Tipe kegiatan wajib dipilih.',
        'attendance_mode.required'    => 'Mode kehadiran wajib dipilih.',
        'allowed_methods.required'    => 'Pilih minimal satu metode absensi.',
        'allowed_methods.min'         => 'Pilih minimal satu metode absensi.',
        'scheduled_end_at.after'      => 'Waktu selesai harus setelah waktu mulai.',
        'attendance_close_at.after'   => 'Waktu tutup absensi harus setelah waktu buka.',
        'schedule_id.exists'          => 'Jadwal yang dipilih tidak valid.',
        'room_id.exists'              => 'Ruangan yang dipilih tidak valid.',
        'target_groups.*.exists'      => 'Salah satu kelompok tidak valid.',
        'target_divisions.*.exists'   => 'Salah satu divisi tidak valid.',
    ]);

    /* ─────────── PERSIAPAN TARGET DATA ─────────── */
    $targetRoles       = array_values(array_filter($validated['target_roles']       ?? []));
    $targetGroups      = array_values(array_filter($validated['target_groups']      ?? []));
    $targetDivisions   = array_values(array_filter($validated['target_divisions']   ?? []));
    $targetGenerations = array_values(array_filter($validated['target_generations'] ?? []));
    $allowedMethods    = array_values(array_unique($validated['allowed_methods']));

    $targetData = [
        'roles'       => $targetRoles,
        'groups'      => $targetGroups,
        'divisions'   => $targetDivisions,
        'generations' => $targetGenerations,
    ];

    /* ─────────── TRANSAKSI ─────────── */
    try {
        $session = DB::transaction(function () use (
            $validated, $targetData, $allowedMethods
        ) {
            $session = AttendanceSession::create([
                'title'                 => $validated['title'],
                'description'           => $validated['description'] ?? null,
                'attendance_type'       => $validated['attendance_type'],
                'attendance_mode'       => $validated['attendance_mode'],
                'schedule_id'           => $validated['schedule_id'] ?? null,
                'room_id'               => $validated['room_id'] ?? null,
                'shift_name'            => $validated['shift_name'] ?? null,

                'target_type'           => $validated['target_type'],
                'target_data'           => $targetData,
                'target_count'          => 0,

                'allowed_methods'       => $allowedMethods,

                'scheduled_start_at'    => $validated['scheduled_start_at'],
                'scheduled_end_at'      => $validated['scheduled_end_at'],
                'attendance_open_at'    => $validated['attendance_open_at'],
                'attendance_close_at'   => $validated['attendance_close_at'],

                'session_status'        => 'scheduled',
                'created_by'            => Auth::id(),
                'updated_by'            => Auth::id(),
            ]);

            /* Resolve target (populate attendance_targets) */
            try {
                $this->targetResolver->resolve($session);

                // Update target_count setelah resolver
                $count = \App\Models\AttendanceTarget::where('session_id', $session->id)
                    ->where('is_excluded', false)
                    ->count();

                $session->update(['target_count' => $count]);
            } catch (\Throwable $e) {
                \Log::warning('[AttendanceStore] TargetResolver gagal', [
                    'session_id' => $session->id,
                    'error'      => $e->getMessage(),
                ]);
            }

            /* Audit log */
            AttendanceAuditLog::create([
                'session_id'  => $session->id,
                'actor_id'    => Auth::id(),
                'action'      => 'session_created',
                'description' => "Sesi absensi \"{$session->title}\" dibuat",
                'after_data'  => $session->fresh()->toArray(),
            ]);

            return $session;
        });

        /* ─── SUCCESS RESPONSE ─── */
        $message = "✅ Sesi absensi <strong>{$session->title}</strong> berhasil dibuat. "
                 . "Target: <strong>{$session->target_count}</strong> peserta.";

        // Kalau session request JSON (AJAX), kembalikan JSON
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'session' => [
                    'id'    => $session->id,
                    'uuid'  => $session->uuid,
                    'title' => $session->title,
                ],
                'redirect' => route('owner.attendance.show', $session),
            ]);
        }

        return redirect()
            ->route('owner.attendance.show', $session)
            ->with('success', $message);

    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e;
    } catch (\Throwable $e) {
        \Log::error('[AttendanceStore] Gagal menyimpan sesi', [
            'trace' => $e->getTraceAsString(),
            'input' => $request->except(['_token']),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat sesi: ' . $e->getMessage(),
            ], 500);
        }

        return back()
            ->withInput()
            ->with('error', '❌ Gagal membuat sesi: ' . $e->getMessage());
    }
}

    /* ══════════════════════════════════════════════════════════════
     * 4. SHOW — Detail sesi
     * ══════════════════════════════════════════════════════════════ */
    public function show(AttendanceSession $session)
    {
        $session->load(['room', 'schedule', 'creator', 'records.user', 'targets.user', 'permissions.user']);

        $targets = AttendanceTarget::where('session_id', $session->id)
            ->where('is_excluded', false)
            ->with(['user', 'record'])
            ->paginate(50);

        return view('owner.attendance.show', compact('session', 'targets'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 5. UPDATE — Update sesi
     * ══════════════════════════════════════════════════════════════ */
    public function update(Request $request, AttendanceSession $session)
    {
        $validated = $request->validate([
            'title'               => 'sometimes|string|max:255',
            'description'         => 'nullable|string',
            'room_id'             => 'nullable|exists:rooms,id',
            'scheduled_start_at'  => 'sometimes|date',
            'scheduled_end_at'    => 'sometimes|date',
            'attendance_open_at'  => 'sometimes|date',
            'attendance_close_at' => 'sometimes|date',
            'grace_period_minutes'=> 'nullable|integer|min:0',
            'allowed_methods'     => 'sometimes|array',
            'reason'              => 'nullable|string',
        ]);

        $before = $session->only(['room_id','scheduled_start_at','scheduled_end_at','attendance_open_at','attendance_close_at']);

        DB::transaction(function () use ($session, $validated, $before) {
            $session->update(array_merge($validated, ['updated_by' => Auth::id()]));

            AttendanceSessionChange::create([
                'session_id'  => $session->id,
                'change_type' => 'time',
                'before_data' => $before,
                'after_data'  => $session->only(['room_id','scheduled_start_at','scheduled_end_at','attendance_open_at','attendance_close_at']),
                'reason'      => $validated['reason'] ?? 'Perubahan oleh pengurus',
                'created_by'  => Auth::id(),
            ]);

            AttendanceAuditLog::create([
                'session_id'  => $session->id,
                'actor_id'    => Auth::id(),
                'action'      => 'session_updated',
                'before_data' => $before,
                'after_data'  => $session->toArray(),
                'reason'      => $validated['reason'] ?? null,
            ]);
        });

        return redirect()->route('owner.attendance.show', $session)->with('success', 'Sesi berhasil diperbarui.');
    }

    /* ══════════════════════════════════════════════════════════════
     * 6. OPEN NOW
     * ══════════════════════════════════════════════════════════════ */
    public function reschedule(Request $request, AttendanceSession $session)
    {
        // 1. Ambil data LAMA sebelum diubah
        $before = [
            'scheduled_start_at' => $session->scheduled_start_at?->format('Y-m-d H:i'),
            'scheduled_end_at'   => $session->scheduled_end_at?->format('Y-m-d H:i'),
            'room_id'            => $session->room_id,
        ];

        // 2. Lakukan update
        $session->update([
            'scheduled_start_at' => $request->scheduled_start_at,
            'scheduled_end_at'   => $request->scheduled_end_at,
            'room_id'            => $request->room_id,
        ]);

        // 3. Ambil data BARU sesudah diubah
        $after = [
            'scheduled_start_at' => $session->scheduled_start_at?->format('Y-m-d H:i'),
            'scheduled_end_at'   => $session->scheduled_end_at?->format('Y-m-d H:i'),
            'room_id'            => $session->room_id,
        ];

        // 4. ⭐ REKAM AUDIT — di sini!
        AuditService::record([
            'action'         => 'SESSION_RESCHEDULED',
            'severity'       => 'high',
            'auditable_type' => AttendanceSession::class,
            'auditable_id'   => $session->id,
            'session_id'     => $session->id,
            'description'    => "Mengubah jadwal sesi {$session->title}",
            'before'         => $before,
            'after'          => $after,
            'reason'         => $request->reason,
            'permission'     => 'attendance.reschedule',
            'authorization_result' => 'granted',
        ]);

        return redirect()
            ->route('owner.attendance.show', $session)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Buka sesi (openNow)
     */
    public function openNow(AttendanceSession $session)
    {
        if ($session->session_status === 'open') {
            // ⭐ LOG GAGAL — penting untuk security
            AuditService::record([
                'action'   => 'SESSION_OPEN_ATTEMPT',
                'severity' => 'notice',
                'result'   => 'failed',
                'reason'   => 'Sesi sudah dalam status OPEN',
                'session_id' => $session->id,
            ]);

            return back()->with('error', 'Sesi sudah dibuka.');
        }

        $before = ['session_status' => $session->session_status];
        $session->update([
            'session_status'    => 'open',
            'attendance_open_at'=> now(),
        ]);
        $after = ['session_status' => $session->session_status];

        AuditService::record([
            'action'         => 'SESSION_OPENED',
            'severity'       => 'info',
            'auditable_type' => AttendanceSession::class,
            'auditable_id'   => $session->id,
            'session_id'     => $session->id,
            'description'    => "Sesi {$session->title} dibuka",
            'before'         => $before,
            'after'          => $after,
            'is_sensitive'   => false,
        ]);

        return back()->with('success', 'Sesi dibuka.');
    }


    /* ══════════════════════════════════════════════════════════════
     * 7. CLOSE
     * ══════════════════════════════════════════════════════════════ */
    public function close(AttendanceSession $session)
    {$engine = app(\App\Services\Attendance\PenaltyEngine::class);
    $session->records()->violation()->each(fn($rec) => $engine->evaluate($rec));
        $session->update([
            'session_status' => 'closed',
            'closed_at'      => now(),
            'updated_by'     => Auth::id(),
        ]);

        $resolved = 0;
        try {
            $result = $this->reconciliation->reconcile($session);
            $resolved = $result['resolved'] ?? 0;
        } catch (\Throwable $e) {
            \Log::warning('Reconcile failed: ' . $e->getMessage());
        }

        return redirect()->route('owner.attendance.show', $session)
        ->with('success', 'Sesi ditutup. Denda otomatis diproses.');
    }

    /* ══════════════════════════════════════════════════════════════
     * 8. EXTEND
     * ══════════════════════════════════════════════════════════════ */
    public function extend(Request $request, AttendanceSession $session)
    {
        $request->validate(['minutes' => 'required|integer|min:1|max:180', 'reason' => 'nullable|string']);

        $before = $session->attendance_close_at;
        $session->update([
            'attendance_close_at' => $session->attendance_close_at
                ? $session->attendance_close_at->addMinutes($request->minutes)
                : now()->addMinutes($request->minutes),
            'session_status'    => 'extended',
            'extended_at'       => now(),
            'extension_minutes' => ($session->extension_minutes ?? 0) + $request->minutes,
        ]);

        AttendanceSessionChange::create([
            'session_id'  => $session->id,
            'change_type' => 'extend',
            'before_data' => ['attendance_close_at' => $before],
            'after_data'  => ['attendance_close_at' => $session->attendance_close_at],
            'reason'      => $request->reason ?? 'Diperpanjang oleh pengurus',
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', "Sesi diperpanjang {$request->minutes} menit.");
    }

    /* ══════════════════════════════════════════════════════════════
     * 9. CANCEL
     * ══════════════════════════════════════════════════════════════ */
    public function cancel(Request $request, AttendanceSession $session)
    {
        $request->validate(['reason' => 'required|string|min:5']);

        $session->update([
            'session_status' => 'cancelled',
            'cancelled_at'   => now(),
            'cancel_reason'  => $request->reason,
        ]);

        AttendanceAuditLog::create([
            'session_id'  => $session->id,
            'actor_id'    => Auth::id(),
            'action'      => 'session_cancelled',
            'description' => 'Sesi dibatalkan: ' . $request->reason,
        ]);

        return back()->with('success', 'Sesi dibatalkan.');
    }

    /* ══════════════════════════════════════════════════════════════
     * 10. MANUAL (by officer)
     * ══════════════════════════════════════════════════════════════ */
    public function manualForm(AttendanceSession $session)
    {
        $targets = AttendanceTarget::where('session_id', $session->id)
            ->where('is_excluded', false)
            ->whereDoesntHave('record')
            ->with('user')
            ->get();

        return view('owner.attendance.manual', compact('session', 'targets'));
    }

    public function manualStore(Request $request, AttendanceSession $session)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'reason'        => 'required|string|min:5',
            'failed_method' => 'nullable|in:manual,nfc,qr',
            'evidence'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = User::findOrFail($validated['user_id']);

        $result = $this->engine->process($user, $session, 'manual_by_officer', [
            'officer_id'    => Auth::id(),
            'manual_reason' => $validated['reason'],
            'failed_method' => $validated['failed_method'] ?? 'nfc',
        ]);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        $result['record']->update(['status' => 'manual', 'is_manual_by_officer' => true]);

        return back()->with('success', "Absensi manual untuk {$user->name} berhasil dicatat.");
    }

    /* ══════════════════════════════════════════════════════════════
     * 11. MONITOR — Live Monitor
     * ══════════════════════════════════════════════════════════════ */
    public function monitor(AttendanceSession $session)
    {
        $session->load(['room']);

        $records = AttendanceRecord::where('session_id', $session->id)
            ->with('user')
            ->orderBy('recorded_at', 'desc')
            ->get();

        $targets = AttendanceTarget::where('session_id', $session->id)
            ->where('is_excluded', false)
            ->with(['user', 'record'])
            ->get();

        $stats = [
            'target'    => $session->target_count,
            'hadir'     => $records->whereIn('status', ['present','late','late_excused','manual'])->count(),
            'tepat'     => $records->where('status', 'present')->count(),
            'terlambat' => $records->whereIn('status', ['late','late_excused'])->count(),
            'izin'      => $records->whereIn('status', ['excused_absent','absent_excused'])->count(),
            'bolos'     => $records->where('status', 'absent')->count(),
            'pending'   => $records->where('status', 'pending')->count(),
            'belum'     => max(0, $session->target_count - $records->count()),
            'offline'   => $records->where('attendance_mode', 'offline')->count(),
            'online'    => $records->where('attendance_mode', 'online')->count(),
            'nfc'       => $records->where('method', 'nfc')->count(),
            'qr'        => $records->where('method', 'qr')->count(),
            'manual'    => $records->where('method', 'manual')->count(),
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'stats'   => $stats,
                'records' => $records->map(fn($r) => [
                    'id'     => $r->id,
                    'name'   => $r->snapshot_name,
                    'role'   => $r->snapshot_role,
                    'group'  => $r->snapshot_group_name,
                    'status' => $r->status,
                    'method' => $r->method,
                    'mode'   => $r->attendance_mode,
                    'time'   => $r->recorded_at?->format('H:i:s'),
                    'points' => $r->points,
                ]),
                'belum_hadir' => $targets->whereNull('record')->map(fn($t) => [
                    'name'  => $t->snapshot_name,
                    'nim'   => $t->snapshot_nim,
                    'group' => $t->snapshot_group_name,
                    'role'  => $t->snapshot_role,
                    'mode'  => $t->target_mode,
                ])->values(),
            ]);
        }

        return view('owner.attendance.monitor', compact('session', 'stats', 'targets', 'records'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 12. CALENDAR — Kalender Absensi
     * ══════════════════════════════════════════════════════════════ */
    public function calendar(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $sessions = AttendanceSession::with(['room', 'creator'])
            ->whereBetween('scheduled_start_at', [$start, $end])
            ->orderBy('scheduled_start_at')
            ->get();

        $byDate = $sessions->groupBy(fn($s) => optional($s->scheduled_start_at)->format('Y-m-d'));

        $stats = [
            'total'     => $sessions->count(),
            'open'      => $sessions->where('session_status', 'open')->count(),
            'closed'    => $sessions->whereIn('session_status', ['closed','expired'])->count(),
            'cancelled' => $sessions->where('session_status', 'cancelled')->count(),
        ];

        $gridStart = $start->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd   = $end->copy()->endOfWeek(Carbon::SATURDAY);

        return view('owner.attendance.calendar', compact(
            'sessions', 'byDate', 'stats', 'month', 'year', 'start', 'end', 'gridStart', 'gridEnd'
        ));
    }

    /* ══════════════════════════════════════════════════════════════
     * 13. HISTORY — Riwayat Absensi
     * ══════════════════════════════════════════════════════════════ */
    public function history(Request $request)
    {
        $query = AttendanceSession::with(['room', 'creator'])
            ->withCount(['records', 'targets']);

        if ($request->filled('status')) {
            $query->where('session_status', $request->status);
        } else {
            $query->whereIn('session_status', ['closed','expired','cancelled','archived']);
        }

        if ($request->filled('attendance_type')) {
            $query->where('attendance_type', $request->attendance_type);
        }
        if ($request->filled('from')) {
            $query->whereDate('scheduled_start_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('scheduled_start_at', '<=', $request->to);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $sessions = $query->orderBy('scheduled_start_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total_closed'  => AttendanceSession::whereIn('session_status', ['closed','expired'])->count(),
            'total_records' => AttendanceRecord::count(),
            'total_present' => AttendanceRecord::whereIn('status', ['present','late','late_excused','manual'])->count(),
            'total_absent'  => AttendanceRecord::where('status', 'absent')->count(),
        ];

        return view('owner.attendance.history', compact('sessions', 'stats'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 14. RECAP — Rekap Absensi
     * ══════════════════════════════════════════════════════════════ */
    public function recap(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $sessions = AttendanceSession::whereBetween('scheduled_start_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('scheduled_start_at', 'desc')
            ->get();

        $sessionIds = $sessions->pluck('id');

        $records = AttendanceRecord::whereIn('session_id', $sessionIds)
            ->select('user_id', 'snapshot_name', 'snapshot_nim', 'snapshot_role',
                     DB::raw('COUNT(*) as total'),
                     DB::raw('SUM(CASE WHEN status IN ("present","late","late_excused","manual") THEN 1 ELSE 0 END) as hadir'),
                     DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as tepat'),
                     DB::raw('SUM(CASE WHEN status IN ("late","late_excused") THEN 1 ELSE 0 END) as terlambat'),
                     DB::raw('SUM(CASE WHEN status IN ("excused_absent","absent_excused") THEN 1 ELSE 0 END) as izin'),
                     DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as bolos'),
                     DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id', 'snapshot_name', 'snapshot_nim', 'snapshot_role')
            ->orderByDesc('total_points')
            ->paginate(30);

        $stats = [
            'total_sessions' => $sessions->count(),
            'total_records'  => AttendanceRecord::whereIn('session_id', $sessionIds)->count(),
            'total_hadir'    => AttendanceRecord::whereIn('session_id', $sessionIds)
                                    ->whereIn('status', ['present','late','late_excused','manual'])->count(),
            'total_izin'     => AttendanceRecord::whereIn('session_id', $sessionIds)
                                    ->whereIn('status', ['excused_absent','absent_excused'])->count(),
            'total_bolos'    => AttendanceRecord::whereIn('session_id', $sessionIds)
                                    ->where('status', 'absent')->count(),
        ];

        return view('owner.attendance.recap', compact('records', 'stats', 'from', 'to', 'sessions'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 15. RECAP PAST — Rekap Masa Lalu
     * ══════════════════════════════════════════════════════════════ */
    public function recapPast(Request $request)
    {
        $year = (int) $request->input('year', now()->year - 1);

        $sessions = AttendanceSession::whereYear('scheduled_start_at', $year)
            ->whereIn('session_status', ['closed', 'expired', 'archived'])
            ->orderBy('scheduled_start_at', 'desc')
            ->paginate(30);

        $byMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::create($year, $m, 1)->startOfMonth();
            $monthEnd   = $monthStart->copy()->endOfMonth();
            $monthSessions = AttendanceSession::whereBetween('scheduled_start_at', [$monthStart, $monthEnd])->pluck('id');
            $byMonth[$m] = [
                'sessions' => $monthSessions->count(),
                'records'  => AttendanceRecord::whereIn('session_id', $monthSessions)->count(),
                'hadir'    => AttendanceRecord::whereIn('session_id', $monthSessions)
                                ->whereIn('status', ['present','late','late_excused','manual'])->count(),
            ];
        }

        $availableYears = AttendanceSession::selectRaw('YEAR(scheduled_start_at) as y')
            ->distinct()->orderByDesc('y')->pluck('y')->filter()->values();

        return view('owner.attendance.recap-past', compact('sessions', 'byMonth', 'year', 'availableYears'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 16. POINTS — Poin Absensi
     * ══════════════════════════════════════════════════════════════ */
    public function points(Request $request)
    {
        $query = AttendancePointLedger::with(['user', 'session', 'record'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        $ledgers = $query->paginate(30)->withQueryString();

        $topUsers = AttendancePointLedger::select('user_id',
                DB::raw('SUM(points) as total_points'),
                DB::raw('COUNT(*) as total_entries'))
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get();

        $stats = [
            'total_entries'  => AttendancePointLedger::count(),
            'total_earned'   => AttendancePointLedger::where('points', '>', 0)->sum('points'),
            'total_penalty'  => AttendancePointLedger::where('points', '<', 0)->sum('points'),
            'total_balance'  => AttendancePointLedger::sum('points'),
        ];

        return view('owner.attendance.points', compact('ledgers', 'topUsers', 'stats'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 17. PENALTIES — Denda Absensi
     * ══════════════════════════════════════════════════════════════ */
    public function penalties(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $penalties = AttendancePointLedger::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('points', '<', 0)
            ->with(['user', 'session'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $topPenalties = AttendancePointLedger::where('points', '<', 0)
            ->select('user_id',
                DB::raw('SUM(points) as total_points'),
                DB::raw('COUNT(*) as total_cases'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('total_points')
            ->limit(10)
            ->get();

        $stats = [
            'total_cases'    => AttendancePointLedger::where('points', '<', 0)->count(),
            'total_penalty'  => AttendancePointLedger::where('points', '<', 0)->sum('points'),
            'this_month'     => AttendancePointLedger::where('points', '<', 0)
                                    ->whereMonth('created_at', now()->month)->count(),
        ];

        return view('owner.attendance.penalties', compact('penalties', 'topPenalties', 'stats', 'from', 'to'));
    }

    /* ══════════════════════════════════════════════════════════════
     * 18. AUDIT — Audit Log
     * ══════════════════════════════════════════════════════════════ */
    public function audit(Request $request)
    {
        $query = AttendanceAuditLog::with(['actor', 'session'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }
        if ($request->filled('actor_id')) {
            $query->where('actor_id', $request->actor_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(30)->withQueryString();

        $actions = AttendanceAuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        $stats = [
            'total'        => AttendanceAuditLog::count(),
            'today'        => AttendanceAuditLog::whereDate('created_at', today())->count(),
            'this_week'    => AttendanceAuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month'   => AttendanceAuditLog::whereMonth('created_at', now()->month)->count(),
        ];

        return view('owner.attendance.audit', compact('logs', 'actions', 'stats'));
    }

    /* ══════════════════════════════════════════════════════════════
     * INTERNAL HELPERS
     * ══════════════════════════════════════════════════════════════ */
    protected function buildAlerts($sessions): array
    {
        $alerts = [];

        foreach ($sessions as $s) {
            if (empty($s->attention)) continue;
            foreach ($s->attention as $a) {
                $alerts[] = array_merge($a, [
                    'session_id'    => $s->id,
                    'session_title' => $s->title,
                    'status'        => $s->session_status,
                    'scheduled_at'  => $s->scheduled_start_at,
                    'time_status'   => $s->time_status,
                ]);
            }
        }

        $openByRoom = $sessions
            ->whereIn('session_status', ['open','extended'])
            ->groupBy('room_id')
            ->filter(fn ($g) => $g->count() > 1 && $g->first()->room_id);

        foreach ($openByRoom as $roomId => $group) {
            $alerts[] = [
                'type'          => 'room_conflict',
                'level'         => 'urgent',
                'text'          => "{$group->count()} sesi aktif di ruangan yang sama",
                'session_id'    => $group->first()->id,
                'session_title' => $group->pluck('title')->join(' • '),
                'status'        => 'open',
                'scheduled_at'  => now(),
                'time_status'   => 'open',
            ];
        }

        $priority = ['urgent' => 0, 'warning' => 1, 'info' => 2];
        usort($alerts, fn ($a, $b) =>
            ($priority[$a['level']] ?? 99) <=> ($priority[$b['level']] ?? 99)
        );

        return $alerts;
    }

    protected function autoSyncSessions(): void
    {
        try {
            AttendanceSession::where('session_status', 'scheduled')
                ->where('attendance_open_at', '<=', now())
                ->where('attendance_close_at', '>', now())
                ->update(['session_status' => 'open', 'opened_at' => now()]);

            AttendanceSession::whereIn('session_status', ['open','extended'])
                ->where('attendance_close_at', '<=', now())
                ->update(['session_status' => 'expired']);
        } catch (\Throwable $e) {
            \Log::warning('autoSyncSessions failed: ' . $e->getMessage());
        }
    }
}
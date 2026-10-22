<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomSchedule;
use App\Models\ScheduleActivity;      // ✅ TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    /**
     * Daftar hari dalam seminggu.
     */
    protected array $days = [
        'monday'    => 'Senin',
        'tuesday'   => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday'  => 'Kamis',
        'friday'    => 'Jumat',
        'saturday'  => 'Sabtu',
        'sunday'    => 'Minggu',
    ];

    /**
     * Tampilkan semua jadwal.
     */
    public function index(Request $request)
    {
        $query = RoomSchedule::with(['room', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('activity_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $schedules = $query->orderBy('start_datetime', 'desc')->paginate(15);
        $rooms = Room::whereNull('deleted_at')->orderBy('name')->get();

        return view('owner.schedules.index', compact('schedules', 'rooms'));
    }

    /**
     * Tampilkan form upload jadwal.
     */
public function uploadForm()
{
    $rooms = Room::whereNull('deleted_at')->orderBy('name')->get();

    // ═══════════════════════════════════════════════════════════
    // Helper: coba beberapa candidate namespace untuk model
    // ═══════════════════════════════════════════════════════════
    $tryLoad = function (array $candidates, array $columns = ['id', 'name']) {
        foreach ($candidates as $class) {
            if (! class_exists($class)) continue;

            try {
                return $class::query()
                    ->orderBy('name')
                    ->get($columns);
            } catch (\Throwable $e) {
                \Log::warning('[ScheduleUpload] Gagal load ' . $class, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
        return collect();
    };

    // ── Kelompok (coba beberapa namespace)
    $kelompokOptions = $tryLoad([
        \App\Models\Group::class,
        'App\Models\Kelompok',
        'App\Models\Groups\Group',
    ]);

    // ── Generasi
    $generasiOptions = $tryLoad([
        \App\Models\Generation::class,
        'App\Models\Generations\Generation',
        'App\Models\Financial\Generation',
    ]);

    // ── Pengurus / Level (dari 2,3,4)
    $pengurusOptions = collect();
    if (class_exists(\App\Models\Level::class)) {
        try {
            $pengurusOptions = \App\Models\Level::query()
                ->whereIn('id', [2, 3, 4])
                ->orderBy('id')
                ->get(['id', 'name']);
        } catch (\Throwable $e) {
            \Log::warning('[ScheduleUpload] Gagal load Level (pengurus)', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ── LMS Group
    $lmsGroupOptions = $tryLoad([
        \App\Models\Lms\LmsGroup::class,
        'App\Models\LmsGroup',
    ]);

    // ── Role (semua level)
    $roleOptions = collect();
    if (class_exists(\App\Models\Level::class)) {
        try {
            $roleOptions = \App\Models\Level::query()
                ->orderBy('id')
                ->get(['id', 'name']);
        } catch (\Throwable $e) {
            \Log::warning('[ScheduleUpload] Gagal load Level (role)', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    return view('owner.schedules.upload', compact(
        'rooms',
        'kelompokOptions',
        'generasiOptions',
        'pengurusOptions',
        'lmsGroupOptions',
        'roleOptions'
    ));
}
    /**
     * Simpan jadwal dari form upload.
     */
public function uploadStore(Request $request)
{
    /* ── 1. VALIDASI ────────────────────────────────────────── */
    $validated = $request->validate([
        'room_id'          => 'required|exists:rooms,id',
        'title'            => 'required|string|max:255',
        'academic_year'    => 'nullable|string|max:20',
        'schedule_type'    => 'required|in:weekly,special,event',
        'semester_start'   => 'required|string|max:50',
        'semester_end'     => 'required|string|max:50',
        'notes'            => 'nullable|string',
        'outside_operational_reason' => 'nullable|string',

        'audience_type'    => 'required|in:all,kelompok,generasi,pengurus,lms_group,role',
        'audience_ids'     => 'nullable|array',
        'audience_ids.*'   => 'integer',

        'attendance_mode'         => 'required|in:none,digital,upload',
        'auto_create_attendance'  => 'nullable|boolean',
        'requires_review'         => 'nullable|boolean',

        // ── Multi-tahap ──
        'schedules'                                  => 'required|array|min:1',
        'schedules.*.enabled'                        => 'nullable|boolean',
        'schedules.*.activities'                     => 'nullable|array',
        'schedules.*.activities.*.stage_name'        => 'nullable|string|max:255',
        'schedules.*.activities.*.activity_type'     => 'required|string|max:50',
        'schedules.*.activities.*.start_time'        => 'required|date_format:H:i',
        'schedules.*.activities.*.end_time'          => 'required|date_format:H:i',
        'schedules.*.activities.*.person'            => 'nullable|string|max:255',
        'schedules.*.activities.*.generation_ids'    => 'nullable|array',
        'schedules.*.activities.*.generation_ids.*'  => 'integer',
        'schedules.*.activities.*.kelompok_ids'      => 'nullable|array',
        'schedules.*.activities.*.kelompok_ids.*'    => 'integer',
        'schedules.*.activities.*.description'       => 'nullable|string|max:2000',

        'schedule_images'   => 'nullable|array|max:4',
        'schedule_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    /* ── 2. AUDIENCE CHECK ──────────────────────────────────── */
    $audienceType = $validated['audience_type'];
    $audienceIds  = $validated['audience_ids'] ?? [];

    if ($audienceType !== 'all' && empty($audienceIds)) {
        return back()->withInput()->withErrors([
            'audience_ids' => 'Pilih minimal satu target untuk audience "' . $audienceType . '".',
        ]);
    }

    $audienceCount = $this->countAudience($audienceType, $audienceIds);

    /* ── 3. UPLOAD GAMBAR ───────────────────────────────────── */
    $imagePaths = [];
    if ($request->hasFile('schedule_images')) {
        foreach ($request->file('schedule_images') as $image) {
            $filename = \Illuminate\Support\Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePaths[] = $image->storeAs('schedules/images', $filename, 'public');
        }
    }

    /* ── 4. AMBIL SEMUA KEGIATAN DARI SEMUA HARI ────────────── */
    $allActivities = [];        // ['monday' => [ [act], [act] ], ...]
    $firstActivity = null;

    foreach ($validated['schedules'] as $dayKey => $day) {
        if (empty($day['enabled']) || empty($day['activities'])) {
            continue;
        }
        foreach ($day['activities'] as $act) {
            $allActivities[$dayKey][] = $act;
            if (! $firstActivity) {
                $firstActivity = ['day' => $dayKey] + $act;
            }
        }
    }

    if (empty($allActivities) || ! $firstActivity) {
        return back()->withInput()
            ->with('warning', 'Minimal 1 hari aktif dengan 1 kegiatan wajib diisi.');
    }

    /* ── 5. TENTUKAN start_datetime & end_datetime UTAMA ────── */
    // Pakai kegiatan paling awal untuk start, paling akhir untuk end
    $dayOffset = fn ($d) => [
        'monday' => 0, 'tuesday' => 1, 'wednesday' => 2,
        'thursday' => 3, 'friday' => 4, 'saturday' => 5, 'sunday' => 6,
    ][$d] ?? 0;

    $weekStart = now()->startOfWeek();

    $earliest = null; $latest = null;
    foreach ($allActivities as $dayKey => $acts) {
        foreach ($acts as $a) {
            $date = $weekStart->copy()->addDays($dayOffset($dayKey));
            $s = $date->copy()->setTimeFromTimeString($a['start_time']);
            $e = $date->copy()->setTimeFromTimeString($a['end_time']);
            if ($e->lt($s)) $e->addDay();
            if (! $earliest || $s->lt($earliest)) $earliest = $s;
            if (! $latest   || $e->gt($latest))   $latest   = $e;
        }
    }

    /* ── 6. TRANSAKSI ───────────────────────────────────────── */
    \DB::beginTransaction();
    try {
        $schedule = RoomSchedule::create([
            'room_id'         => $validated['room_id'],
            'created_by'      => auth()->id(),
            'title'           => $validated['title'],

            // Ambil dari kegiatan pertama sebagai ringkasan
            'activity_type'   => $firstActivity['activity_type'] ?? 'Umum',
            'description'     => $this->buildDescription($validated, $firstActivity, $allActivities),

            'recurrence_type' => 'Mingguan',
            'recurrence_days' => array_keys($allActivities),
            'start_datetime'  => $earliest,
            'end_datetime'    => $latest,
            'end_date'        => now()->addMonths(6),
            'status'          => 'Aktif',
            'images'          => $imagePaths ?: null,

            'semester'        => $validated['semester_start'],   // kompat
            'semester_start'  => $validated['semester_start'],
            'semester_end'    => $validated['semester_end'],
            'academic_year'   => $validated['academic_year'] ?? null,
            'schedule_type'   => $validated['schedule_type'],

            'audience_type'   => $audienceType,
            'audience_ids'    => $audienceIds,
            'audience_count'  => $audienceCount,

            'attendance_mode'         => $validated['attendance_mode'],
            'auto_create_attendance'  => (bool) ($validated['auto_create_attendance'] ?? false),
            'requires_review'         => (bool) ($validated['requires_review'] ?? true),

            'outside_operational_reason' => $validated['outside_operational_reason'] ?? null,
            'notes'                      => $validated['notes'] ?? null,
        ]);

        /* ── 6a. Simpan semua kegiatan (multi-tahap) ── */
        foreach ($allActivities as $dayKey => $acts) {
            foreach ($acts as $idx => $act) {
                $date = $weekStart->copy()->addDays($dayOffset($dayKey));
                $s = $date->copy()->setTimeFromTimeString($act['start_time']);
                $e = $date->copy()->setTimeFromTimeString($act['end_time']);
                if ($e->lt($s)) $e->addDay();

                ScheduleActivity::create([
                    'schedule_id'    => $schedule->id,
                    'day'            => $dayKey,
                    'stage'          => $idx + 1,
                    'stage_name'     => $act['stage_name'] ?? null,
                    'activity_type'  => $act['activity_type'],
                    'start_time'     => $act['start_time'],
                    'end_time'       => $act['end_time'],
                    'start_datetime' => $s,
                    'end_datetime'   => $e,
                    'person'         => $act['person'] ?? null,
                    'generation_ids' => $act['generation_ids'] ?? [],
                    'kelompok_ids'   => $act['kelompok_ids'] ?? [],
                    'description'    => $act['description'] ?? null,
                ]);
            }
        }

        /* ── 6b. Auto create attendance session ── */
        if ($schedule->auto_create_attendance && $schedule->attendance_mode !== 'none') {
            $sessionId = $this->createAttendanceSession($schedule);
            if ($sessionId) {
                $schedule->update(['attendance_session_id' => $sessionId]);
            }
        }

        \DB::commit();
    } catch (\Throwable $e) {
        \DB::rollBack();
        \Log::error('[ScheduleStore] Gagal', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);
        return back()->withInput()
            ->with('warning', 'Gagal menyimpan jadwal: ' . $e->getMessage());
    }

    $msg = "✅ Jadwal '{$schedule->title}' berhasil dipublikasikan";
    $msg .= " ({$schedule->activities()->count()} kegiatan)";
    if ($audienceCount > 0) $msg .= " untuk {$audienceCount} penerima";
    $msg .= '.';

    return redirect()->route('owner.schedules.index')->with('success', $msg);
}

/* ── Helper: gabungkan info tambahan ke description ── */
private function buildDescription(array $validated, array $first, array $all): string
{
    $parts = [];
    if (! empty($validated['academic_year'])) $parts[] = "Tahun Akademik: {$validated['academic_year']}";
    if (! empty($validated['semester_start'])) $parts[] = "Semester: {$validated['semester_start']} s.d. {$validated['semester_end']}";
    if (! empty($validated['notes']))           $parts[] = "Catatan: {$validated['notes']}";
    if (! empty($validated['outside_operational_reason']))
        $parts[] = "Alasan di luar jam: {$validated['outside_operational_reason']}";

    $dayLabel = [
        'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
        'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu',
    ];

    $parts[] = "Total kegiatan: " . array_sum(array_map('count', $all)) . " tahap";
    $parts[] = "Hari aktif: " . implode(', ', array_map(fn ($d) => $dayLabel[$d] ?? $d, array_keys($all)));

    return implode("\n", $parts);
}

/* ═══════════════════════════════════════════════════════════
 | HELPER: Count audience
 * ═══════════════════════════════════════════════════════════ */
private function countAudience(string $type, array $ids): int
{
    // Base query
    $base = \App\Models\User::query()
        ->where('is_active', true)
        ->whereNull('deleted_at');

    if ($type === 'all' || empty($ids)) {
        return (clone $base)->where('level_id', '!=', 1)->count();
    }

    try {
        match ($type) {
            'kelompok' => method_exists(\App\Models\User::class, 'groups')
                ? $base->whereHas('groups', fn ($x) => $x->whereIn('groups.id', $ids))->count()
                : 0,

            'generasi' => \Schema::hasColumn('users', 'generation_id')
                ? $base->whereIn('generation_id', $ids)->count()
                : 0,

            'pengurus', 'role' => $base->whereIn('level_id', $ids)->count(),

            'lms_group' => method_exists(\App\Models\User::class, 'lmsGroups')
                ? $base->whereHas('lmsGroups', fn ($x) => $x->whereIn('lms_groups.id', $ids))->count()
                : 0,

            default => 0,
        };
    } catch (\Throwable $e) {
        \Log::warning('[ScheduleUpload] countAudience gagal', [
            'type'  => $type,
            'error' => $e->getMessage(),
        ]);
        return 0;
    }

    // Return default (di-match sebenarnya sudah return)
    return 0;
}
/* ═══════════════════════════════════════════════════════════
 | HELPER: Create attendance session from schedule
 * ═══════════════════════════════════════════════════════════ */
private function createAttendanceSession(RoomSchedule $schedule): ?int
{
    // Cek class ada
    $sessionClass = 'App\Models\Attendance\AttendanceSession';
    if (! class_exists($sessionClass)) {
        $sessionClass = 'App\Models\AttendanceSession';
        if (! class_exists($sessionClass)) {
            \Log::warning('[ScheduleStore] AttendanceSession model tidak ada');
            return null;
        }
    }

    // Cek tabel attendance_sessions ada
    if (! \Schema::hasTable('attendance_sessions')) {
        \Log::warning('[ScheduleStore] Tabel attendance_sessions tidak ada');
        return null;
    }

    try {
        $payload = [
            'title'              => $schedule->title,
            'room_id'            => $schedule->room_id,
            'scheduled_start_at' => $schedule->start_datetime,
            'scheduled_end_at'   => $schedule->end_datetime,
            'attendance_type'    => 'session',
            'status'             => 'scheduled',
            'created_by'         => auth()->id(),
            'timezone'           => 'Asia/Jakarta',
        ];

        // Tambahkan kolom opsional kalau ada
        if (\Schema::hasColumn('attendance_sessions', 'room_schedule_id')) {
            $payload['room_schedule_id'] = $schedule->id;
        }
        if (\Schema::hasColumn('attendance_sessions', 'audience_type')) {
            $payload['audience_type'] = $schedule->audience_type;
        }
        if (\Schema::hasColumn('attendance_sessions', 'audience_ids')) {
            $payload['audience_ids'] = $schedule->audience_ids;
        }
        if (\Schema::hasColumn('attendance_sessions', 'attendance_mode')) {
            $payload['attendance_mode'] = $schedule->attendance_mode;
        }
        if (\Schema::hasColumn('attendance_sessions', 'source')) {
            $payload['source'] = 'schedule';
        }

        $session = $sessionClass::create($payload);

        // Pre-assign target users (kalau tabelnya ada)
        if (\Schema::hasTable('attendance_session_targets')) {
            $users = $schedule->resolveAudience();
            foreach ($users as $user) {
                try {
                    \DB::table('attendance_session_targets')->insert([
                        'session_id' => $session->id,
                        'user_id'    => $user->id,
                        'status'     => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    // skip individual, jangan block
                }
            }
        }

        return $session->id;

    } catch (\Throwable $e) {
        \Log::warning('[ScheduleStore] Auto-create attendance gagal', [
            'schedule_id' => $schedule->id,
            'error'       => $e->getMessage(),
        ]);
        return null;
    }
}
    /**
     * Tampilkan form edit jadwal.
     */
    public function edit(RoomSchedule $schedule)
    {
        $rooms = Room::whereNull('deleted_at')->orderBy('name')->get();
        return view('owner.schedules.edit', compact('schedule', 'rooms'));
    }

    /**
     * Update jadwal.
     */
    public function update(Request $request, RoomSchedule $schedule)
    {
        $validated = $request->validate([
            'room_id'       => 'required|exists:rooms,id',
            'title'         => 'required|string|max:255',
            'activity_type' => 'nullable|string|max:100',
            'description'   => 'nullable|string',
            'start_datetime'=> 'required|date',
            'end_datetime'  => 'required|date|after:start_datetime',
            'status'        => ['required', Rule::in(['Aktif', 'Selesai', 'Dibatalkan'])],
        ]);

        $schedule->update($validated);

        return redirect()->route('owner.schedules.index')
            ->with('success', '✅ Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus (soft delete) jadwal.
     */
    public function destroy(RoomSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('owner.schedules.index')
            ->with('success', '🗑️ Jadwal berhasil dihapus.');
    }

    /**
     * Tampilkan jadwal publik (aktif dan hari ini ke depan).
     */
    public function publicIndex(Request $request)
    {
        $query = RoomSchedule::with(['room', 'creator'])
            ->where('status', 'Aktif')
            ->where('start_datetime', '>=', now()->startOfDay());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('activity_type', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('start_datetime')->paginate(15);
        return view('owner.schedules.public', compact('schedules'));
    }

    /**
     * Riwayat jadwal (selesai atau sudah lewat).
     */
    public function history(Request $request)
    {
        $query = RoomSchedule::with(['room', 'creator'])
            ->where(function ($q) {
                $q->where('status', 'Selesai')
                  ->orWhere('end_datetime', '<', now());
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('activity_type', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('end_datetime', 'desc')->paginate(15);
        return view('owner.schedules.history', compact('schedules'));
    }

    /**
     * Cek apakah jadwal di luar jam operasional ruangan.
     */
    private function isOutsideOperational(int $roomId, string $dayKey, string $startTime, string $endTime): bool
    {
        $room = Room::find($roomId);
        if (!$room) {
            return true;
        }

        $hours = $room->operational_hours ?? [];
        if (empty($hours) || !is_array($hours)) {
            return false; // jika tidak ada jam operasional, anggap selalu bisa
        }

        $dayOp = $hours[$dayKey] ?? null;
        if (!$dayOp) {
            return true; // hari tidak beroperasi
        }

        // Parsing operasional
        $opStart = $opEnd = null;
        if (is_array($dayOp)) {
            $opStart = $dayOp['start'] ?? $dayOp['open'] ?? null;
            $opEnd   = $dayOp['end']   ?? $dayOp['close'] ?? null;
        } elseif (is_string($dayOp) && str_contains($dayOp, '-')) {
            [$opStart, $opEnd] = explode('-', $dayOp);
        }

        if (!$opStart || !$opEnd) {
            return true;
        }

        return $startTime < $opStart || $endTime > $opEnd;
    }
    public function show(RoomSchedule $schedule)
{
    $schedule->load(['room', 'creator']);
    
    // Parse data dari description (jika ada)
    $desc = $schedule->description ?? '';
    
    // Ekstrak data tambahan dari description
    $schedule->person = '';
    $schedule->academic_year = '';
    $schedule->semester = '';
    $schedule->notes = '';
    $schedule->outside_reason = '';
    
    if (preg_match('/Pengguna:\s*([^\n]+)/', $desc, $match)) {
        $schedule->person = trim($match[1]);
    }
    if (preg_match('/Tahun Akademik:\s*([^\n]+)/', $desc, $match)) {
        $schedule->academic_year = trim($match[1]);
    }
    if (preg_match('/Semester:\s*([^\n]+)/', $desc, $match)) {
        $schedule->semester = trim($match[1]);
    }
    if (preg_match('/Catatan:\s*([^\n]+)/', $desc, $match)) {
        $schedule->notes = trim($match[1]);
    }
    if (preg_match('/Alasan di luar jam:\s*([^\n]+)/', $desc, $match)) {
        $schedule->outside_reason = trim($match[1]);
    }
    
    return view('owner.schedules.show', compact('schedule'));
}
}
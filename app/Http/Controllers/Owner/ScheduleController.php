<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomSchedule;
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
        return view('owner.schedules.upload', compact('rooms'));
    }

    /**
     * Simpan jadwal dari form upload.
     */
    public function uploadStore(Request $request)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'title'            => 'required|string|max:255',
            'academic_year'    => 'nullable|string|max:20',
            'semester'         => 'nullable|string|max:50',
            'schedule_type'    => 'nullable|string|max:50',
            'notes'            => 'nullable|string',
            'outside_operational_reason' => 'nullable|string|required_if:has_outside,true',
            'schedules'        => 'required|array|min:1',
            'schedules.*.enabled' => 'nullable|boolean',
            'schedules.*.person' => 'nullable|string|max:255',
            'schedules.*.title'   => 'nullable|string|max:255',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time'   => 'nullable|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.activity_type' => 'nullable|string|max:100',
            'schedules.*.description'   => 'nullable|string',
            'schedule_images'    => 'nullable|array|max:4',
            'schedule_images.*'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $roomId = $validated['room_id'];
        $defaultTitle = $validated['title'];
        $notes = $validated['notes'] ?? '';
        $outsideReason = $validated['outside_operational_reason'] ?? '';
        $academicYear = $validated['academic_year'] ?? '';
        $semester = $validated['semester'] ?? '';
        $scheduleType = $validated['schedule_type'] ?? 'weekly';

        // Upload gambar
        $imagePaths = [];
        if ($request->hasFile('schedule_images')) {
            foreach ($request->file('schedule_images') as $image) {
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('schedules/images', $filename, 'public');
                $imagePaths[] = $path;
            }
        }

        $created = 0;
        $errors = [];

        foreach ($request->input('schedules', []) as $dayKey => $dayData) {
            if (empty($dayData['enabled']) || $dayData['enabled'] != 1) {
                continue;
            }

            $title = !empty($dayData['title']) ? $dayData['title'] : $defaultTitle;
            $person = $dayData['person'] ?? '';
            $startTime = $dayData['start_time'] ?? null;
            $endTime = $dayData['end_time'] ?? null;
            $activityType = $dayData['activity_type'] ?? 'Umum';
            $description = $dayData['description'] ?? '';

            if (empty($title) || empty($startTime) || empty($endTime)) {
                $errors[] = "Hari {$this->days[$dayKey]}: Judul, jam mulai, dan jam selesai wajib diisi.";
                continue;
            }

            // Konversi ke datetime
            $dayNumber = array_search($dayKey, array_keys($this->days)) + 1;
            $date = now()->startOfWeek()->addDays($dayNumber - 1);
            $startDateTime = $date->copy()->setTimeFromTimeString($startTime);
            $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

            if ($endDateTime->lt($startDateTime)) {
                $endDateTime->addDay();
            }

            // Cek apakah di luar jam operasional (jika ada alasan, tetap bisa)
            $isOutside = $this->isOutsideOperational($roomId, $dayKey, $startTime, $endTime);
            if ($isOutside && empty($outsideReason)) {
                $errors[] = "Hari {$this->days[$dayKey]}: Jadwal di luar jam operasional. Alasan wajib diisi.";
                continue;
            }

            $data = [
                'room_id'            => $roomId,
                'created_by'         => auth()->id(),
                'title'              => $title,
                'activity_type'      => $activityType,
                'description'        => trim($description . 
                    ($person ? " (Pengguna: $person)" : '') . 
                    ($notes ? "\nCatatan: $notes" : '') . 
                    ($outsideReason ? "\nAlasan di luar jam: $outsideReason" : '') . 
                    ($academicYear ? "\nTahun Akademik: $academicYear" : '') . 
                    ($semester ? "\nSemester: $semester" : '') . 
                    ($scheduleType ? "\nTipe Jadwal: $scheduleType" : '')),
                'start_datetime'     => $startDateTime,
                'end_datetime'       => $endDateTime,
                'recurrence_type'    => 'Mingguan',
                'recurrence_days'    => [$dayKey],
                'end_date'           => now()->addMonths(6),
                'requires_attendance'=> false,
                'status'             => 'Aktif',
                'images'             => $imagePaths ? json_encode($imagePaths) : null, // opsional, jika kolom ada
            ];

            try {
                RoomSchedule::create($data);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Hari {$this->days[$dayKey]}: Gagal menyimpan - " . $e->getMessage();
            }
        }

        if ($created === 0) {
            return redirect()->back()
                ->withErrors($errors ?: ['Tidak ada jadwal yang berhasil disimpan. Pastikan minimal satu hari diaktifkan dan diisi dengan benar.'])
                ->withInput();
        }

        if (!empty($errors)) {
            return redirect()->route('owner.schedules.index')
                ->with('warning', "⚠️ $created jadwal berhasil dibuat, tetapi ada beberapa masalah: " . implode('; ', $errors));
        }

        return redirect()->route('owner.schedules.index')
            ->with('success', "✅ $created jadwal berhasil ditambahkan.");
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
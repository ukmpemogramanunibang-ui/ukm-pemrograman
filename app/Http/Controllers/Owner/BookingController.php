<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\RoomSchedule;

class BookingController extends Controller
{
    /**
     * Daftar booking dengan filter, pencarian, dan statistik
     */
    public function index(Request $request)
    {
        $query = Booking::with(['room', 'approver']);

        // Filter status (gunakan bahasa Indonesia)
        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('requester_name', 'LIKE', "%{$search}%")
                  ->orWhere('requester_phone', 'LIKE', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik (gunakan status bahasa Indonesia)
        $stats = [
            'total'     => Booking::count(),
            'menunggu'  => Booking::where('status', 'Menunggu Persetujuan')->count(),
            'konflik'   => Booking::where('status', 'Konflik')->count(),
            'disetujui' => Booking::where('status', 'Disetujui')->count(),
            'selesai'   => Booking::where('status', 'Selesai')->count(),
            'ditolak'   => Booking::where('status', 'Ditolak')->count(),
        ];

        // Role untuk menentukan hak akses
        $user = Auth::user();
        $canApprove = in_array($user->level_id, [1, 2, 3]); // Owner, Superadmin, Ketua

        return view('owner.bookings.index', compact('bookings', 'stats', 'canApprove'));
    }

    /**
     * Cek status ruangan secara realtime (untuk AJAX)
     */
    public function getStatus(Room $room)
    {
        $now = now();

        // Booking sedang berlangsung
        $currentBooking = Booking::where('room_id', $room->id)
            ->where('status', 'Disetujui')
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>=', $now)
            ->first();

        if ($currentBooking) {
            return response()->json([
                'status'    => 'Sedang Digunakan',
                'booking'   => $currentBooking->title,
                'end'       => $currentBooking->end_datetime->toIso8601String(),
                'remaining' => $currentBooking->end_datetime->diffInMinutes($now),
            ]);
        }

        // Booking yang akan datang
        $nextBooking = Booking::where('room_id', $room->id)
            ->where('status', 'Disetujui')
            ->where('start_datetime', '>', $now)
            ->orderBy('start_datetime')
            ->first();

        if ($nextBooking) {
            return response()->json([
                'status' => 'Akan Digunakan',
                'next'   => $nextBooking->start_datetime->toIso8601String(),
                'title'  => $nextBooking->title,
                'in'     => $nextBooking->start_datetime->diffInMinutes($now),
            ]);
        }

        // Maintenance
        $maintenance = Maintenance::where('room_id', $room->id)
            ->where('status', 'aktif')
            ->where('start_datetime', '<=', $now)
            ->where('end_datetime', '>=', $now)
            ->first();

        if ($maintenance) {
            return response()->json(['status' => 'Maintenance', 'title' => $maintenance->title]);
        }

        return response()->json(['status' => 'Tersedia']);
    }

    /**
     * Form tambah booking (dari halaman ruangan)
     */
    public function create(Room $room)
    {
        $rooms = Room::where('status', '!=', 'Dinonaktifkan')->get();
        return view('owner.bookings.create', compact('room', 'rooms'));
    }

    /**
     * Simpan booking baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id'           => 'required|exists:rooms,id',
            'title'             => 'required|string|max:255',
            'requester_name'    => 'required|string|max:255',
            'requester_phone'   => 'nullable|string|max:20',
            'start_datetime'    => 'required|date|after:now',
            'end_datetime'      => 'required|date|after:start_datetime',
            'participants'      => 'required|integer|min:1',
            'facilities'        => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $targetRoom = Room::findOrFail($data['room_id']);

        // Validasi kapasitas
        $maxCapacity = $targetRoom->capacity_max ?? $targetRoom->capacity_normal;
        if ($data['participants'] > $maxCapacity) {
            return redirect()->back()
                ->with('error', "Jumlah peserta melebihi kapasitas ruangan (maksimal {$maxCapacity} orang)")
                ->withInput();
        }

        // Cek konflik
        $conflicts = $this->checkConflicts($targetRoom->id, $data['start_datetime'], $data['end_datetime']);

        // Tentukan status
        $status = empty($conflicts) ? 'Menunggu Persetujuan' : 'Konflik';

        // Simpan booking
        $booking = Booking::create([
            'room_id'           => $targetRoom->id,
            'title'             => $data['title'],
            'requester_name'    => $data['requester_name'],
            'requester_phone'   => $data['requester_phone'] ?? null,
            'start_datetime'    => $data['start_datetime'],
            'end_datetime'      => $data['end_datetime'],
            'participant_count' => $data['participants'],
            'status'            => $status,
            'user_id'           => Auth::id(),
            'description'       => $request->description ?? null,
        ]);

        // Simpan fasilitas jika ada
        if (!empty($data['facilities'])) {
            $booking->facilities()->sync($data['facilities']);
        }

        // Simpan konflik jika ada
        if (!empty($conflicts)) {
            $booking->conflicts()->create(['details' => json_encode($conflicts)]);
        }

        $message = $status === 'Menunggu Persetujuan'
            ? 'Booking berhasil diajukan, menunggu persetujuan.'
            : 'Booking diajukan dengan konflik, harap tinjau oleh Owner.';

        return redirect()->route('owner.bookings.show', $booking)->with('success', $message);
    }

    /**
     * Detail booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['room', 'conflicts', 'approver']);
        $rooms = Room::where('status', '!=', 'Dinonaktifkan')->get();

        $user = Auth::user();
        $canApprove = in_array($user->level_id, [1, 2, 3]);

        // Ambil gambar ruangan untuk carousel
        $images = $booking->room->images ?? [];
        $totalImages = is_array($images) ? count($images) : 0;

        return view('owner.bookings.show', compact('booking', 'rooms', 'canApprove', 'images', 'totalImages'));
    }

    /**
     * Setujui booking (hanya Owner, Superadmin, Ketua)
     */
    public function approve($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $user = Auth::user();

        // Cek izin
        if (!in_array($user->level_id, [1, 2, 3])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyetujui booking. Hanya Owner, Superadmin, dan Ketua yang diizinkan.');
        }

        // Cek status
        if (!in_array($booking->status, ['Menunggu Persetujuan', 'Konflik'])) {
            return redirect()->back()->with('error', 'Booking tidak dapat disetujui karena statusnya sudah "' . $booking->status . '".');
        }

        // Cek konflik
        $conflicts = $this->checkConflicts($booking->room_id, $booking->start_datetime, $booking->end_datetime);

        if (!empty($conflicts) && $booking->status !== 'Konflik') {
            $booking->status = 'Konflik';
            $booking->save();
            return redirect()->back()->with('error', 'Terjadi konflik. Status diubah menjadi Konflik. Silakan periksa dan gunakan Override jika perlu.');
        }

        if (!empty($conflicts)) {
            return redirect()->back()->with('error', 'Masih terdapat konflik: ' . implode(', ', $conflicts) . '. Selesaikan terlebih dahulu atau gunakan Override.');
        }

        // Proses setujui
        $booking->status = 'Disetujui';
        $booking->approved_by = Auth::id();
        $booking->approved_at = now();
        $booking->save();

        // Buat jadwal dari booking
        $this->createScheduleFromBooking($booking);

        return redirect()->route('owner.bookings.index')->with('success', '✅ Booking berhasil disetujui.');
    }

    /**
     * Tolak booking (hanya Owner, Superadmin, Ketua)
     */
    public function reject($bookingId, Request $request)
    {
        $booking = Booking::findOrFail($bookingId);
        $user = Auth::user();

        if (!in_array($user->level_id, [1, 2, 3])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menolak booking.');
        }

        if (in_array($booking->status, ['Disetujui', 'Selesai'])) {
            return redirect()->back()->with('error', 'Booking yang sudah disetujui atau selesai tidak dapat ditolak.');
        }

        $booking->status = 'Ditolak';
        $booking->rejected_by = Auth::id();
        $booking->rejected_at = now();
        $booking->rejection_reason = $request->input('reason', 'Tidak ada alasan');
        $booking->save();

        return redirect()->route('owner.bookings.index')->with('success', 'Booking ditolak.');
    }

    /**
     * Override konflik (Owner, Superadmin, Ketua)
     */
    public function overrideConflict($bookingId, Request $request)
    {
        $booking = Booking::findOrFail($bookingId);
        $user = Auth::user();

        if (!in_array($user->level_id, [1, 2, 3])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan override.');
        }

        if ($booking->status !== 'Konflik') {
            return redirect()->back()->with('error', 'Booking tidak dalam status konflik.');
        }

        $request->validate([
            'override_reason' => 'required|string|min:5',
        ]);

        $booking->status = 'Disetujui';
        $booking->approved_by = Auth::id();
        $booking->approved_at = now();
        $booking->override_reason = $request->override_reason;
        $booking->save();

        // Resolve konflik
        $conflict = $booking->conflicts()->first();
        if ($conflict) {
            $conflict->resolved = true;
            $conflict->resolution_notes = $request->override_reason;
            $conflict->resolved_by = Auth::id();
            $conflict->resolved_at = now();
            $conflict->save();
        }

        $this->createScheduleFromBooking($booking);

        return redirect()->route('owner.bookings.index')->with('success', 'Konflik dioverride, booking disetujui.');
    }

    /**
     * Ubah waktu booking (Owner, Superadmin, Ketua)
     */
    public function updateTime(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $user = Auth::user();

        if (!in_array($user->level_id, [1, 2, 3])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah waktu.');
        }

        $validator = Validator::make($request->all(), [
            'start_datetime' => 'required|date|after:now',
            'end_datetime'   => 'required|date|after:start_datetime',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $conflicts = $this->checkConflicts($booking->room_id, $request->start_datetime, $request->end_datetime);
        if (!empty($conflicts)) {
            return redirect()->back()->with('error', 'Waktu baru masih bertabrakan: ' . implode(', ', $conflicts));
        }

        $booking->start_datetime = $request->start_datetime;
        $booking->end_datetime = $request->end_datetime;
        $booking->save();

        // Update jadwal jika sudah disetujui
        if ($booking->status === 'Disetujui') {
            $schedule = RoomSchedule::where('booking_id', $booking->id)->first();
            if ($schedule) {
                $schedule->start_datetime = $booking->start_datetime;
                $schedule->end_datetime = $booking->end_datetime;
                $schedule->save();
            }
        }

        return redirect()->route('owner.bookings.show', $booking)->with('success', '⏰ Waktu booking berhasil diubah.');
    }

    /**
     * Pindah ruangan (Owner, Superadmin, Ketua)
     */
    public function moveRoom(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $user = Auth::user();

        if (!in_array($user->level_id, [1, 2, 3])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk memindahkan ruangan.');
        }

        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id|different:room_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $newRoom = Room::find($request->room_id);
        $conflicts = $this->checkConflicts($newRoom->id, $booking->start_datetime, $booking->end_datetime);

        if (!empty($conflicts)) {
            return redirect()->back()->with('error', 'Ruangan baru memiliki konflik: ' . implode(', ', $conflicts));
        }

        $booking->room_id = $newRoom->id;
        $booking->save();

        // Update jadwal
        if ($booking->status === 'Disetujui') {
            $schedule = RoomSchedule::where('booking_id', $booking->id)->first();
            if ($schedule) {
                $schedule->room_id = $newRoom->id;
                $schedule->save();
            }
        }

        return redirect()->route('owner.bookings.show', $booking)->with('success', '📦 Booking berhasil dipindahkan ke ruangan "' . $newRoom->name . '".');
    }

    /**
     * Check-in
     */
    public function checkin($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->status !== 'Disetujui') {
            return redirect()->back()->with('error', 'Booking belum disetujui.');
        }

        if ($booking->checkin_at) {
            return redirect()->back()->with('error', 'Sudah check-in sebelumnya.');
        }

        $now = now();
        if ($now->lt($booking->start_datetime)) {
            return redirect()->back()->with('error', 'Belum waktunya check-in.');
        }

        $booking->checkin_at = $now;
        $booking->save();

        return redirect()->back()->with('success', '✅ Check-in berhasil.');
    }

    /**
     * Check-out
     */
    public function checkout($bookingId, Request $request)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->status !== 'Disetujui') {
            return redirect()->back()->with('error', 'Booking belum disetujui.');
        }

        if (!$booking->checkin_at) {
            return redirect()->back()->with('error', 'Belum melakukan check-in.');
        }

        if ($booking->checkout_at) {
            return redirect()->back()->with('error', 'Sudah check-out.');
        }

        $booking->checkout_at = now();
        $booking->status = 'Selesai';
        $booking->save();

        // Catat laporan kondisi jika ada
        if ($request->has('condition_report')) {
            $booking->condition_report = $request->condition_report;
            $booking->save();
        }

        return redirect()->back()->with('success', '✅ Check-out berhasil, booking selesai.');
    }

    // ===================== FUNGSI BANTU =====================

    /**
     * Cek konflik dengan jadwal, booking, dan maintenance
     */
    protected function checkConflicts($roomId, $start, $end)
    {
        $conflicts = [];
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $day = strtolower($start->format('l'));

        $room = Room::find($roomId);
        if (!$room) {
            return ['Ruangan tidak ditemukan.'];
        }

        // 1. Cek jam operasional
        if ($room->operational_hours && is_array($room->operational_hours)) {
            $hours = $room->operational_hours[$day] ?? null;
            if ($hours && $hours !== 'Tutup' && str_contains($hours, '-')) {
                [$open, $close] = explode('-', $hours);
                $openTime = Carbon::parse($open);
                $closeTime = Carbon::parse($close);
                if ($start->lt($openTime) || $end->gt($closeTime)) {
                    $conflicts[] = "Di luar jam operasional ({$open} - {$close})";
                }
            } elseif ($hours === 'Tutup') {
                $conflicts[] = "Ruangan tutup pada hari " . ucfirst($day);
            }
        }

        // 2. Cek jadwal rutin
        $schedules = RoomSchedule::where('room_id', $roomId)
            ->where('status', 'Aktif')
            ->where(function ($q) use ($start, $end) {
                $q->where('start_datetime', '<', $end)
                  ->where('end_datetime', '>', $start);
            })
            ->get();

        foreach ($schedules as $schedule) {
            // Cek recurrence
            if ($schedule->recurrence_type && $schedule->recurrence_type !== 'Sekali') {
                $days = is_array($schedule->recurrence_days) ? $schedule->recurrence_days : json_decode($schedule->recurrence_days, true);
                if (!in_array($day, $days)) {
                    continue;
                }
            }
            $sStart = Carbon::parse($schedule->start_datetime);
            $sEnd = Carbon::parse($schedule->end_datetime);
            if ($start->lt($sEnd) && $end->gt($sStart)) {
                $conflicts[] = "Jadwal rutin: {$schedule->title} ({$sStart->format('H:i')} - {$sEnd->format('H:i')})";
            }
        }

        // 3. Cek booking disetujui
        $bookings = Booking::where('room_id', $roomId)
            ->where('status', 'Disetujui')
            ->where(function ($q) use ($start, $end) {
                $q->where('start_datetime', '<', $end)
                  ->where('end_datetime', '>', $start);
            })
            ->get();

        foreach ($bookings as $booking) {
            $bStart = Carbon::parse($booking->start_datetime);
            $bEnd = Carbon::parse($booking->end_datetime);
            $conflicts[] = "Booking: {$booking->title} ({$bStart->format('H:i')} - {$bEnd->format('H:i')})";
        }

        // 4. Cek maintenance
        $maintenance = Maintenance::where('room_id', $roomId)
            ->where('status', 'aktif')
            ->where('start_datetime', '<', $end)
            ->where('end_datetime', '>', $start)
            ->first();

        if ($maintenance) {
            $conflicts[] = "Maintenance: {$maintenance->title}";
        }

        return $conflicts;
    }

    /**
     * Buat jadwal dari booking yang disetujui
     */
    protected function createScheduleFromBooking($booking)
{
    $existing = RoomSchedule::where('booking_id', $booking->id)->first();
    if ($existing) {
        return;
    }

    $schedule = new RoomSchedule();
    $schedule->room_id = $booking->room_id;
    $schedule->title = $booking->title;
    $schedule->description = $booking->description ?? 'Booking: ' . $booking->title;
    $schedule->start_datetime = $booking->start_datetime;
    $schedule->end_datetime = $booking->end_datetime;
    $schedule->activity_type = 'Booking';
    $schedule->status = 'Aktif';
    $schedule->booking_id = $booking->id;
    $schedule->save();

    if ($booking->images) {
        $schedule->images = $booking->images;
        $schedule->save();
    }
}
}
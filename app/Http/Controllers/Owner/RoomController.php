<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\Booking;
use App\Models\Notification;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\RoomBookingClassifier;
use App\Services\RoomStatusService;

class RoomController extends Controller
{
    public function check(Request $request)
    {
        $field = $request->field;
        $value = $request->value;

        if ($field === 'name') {
            return response()->json(['exists' => Room::where('name', $value)->exists()]);
        }
        if ($field === 'room_code') {
            return response()->json(['exists' => Room::where('room_code', $value)->exists()]);
        }
        return response()->json(['error' => 'Invalid field'], 400);
    }

    public function index(Request $request)
    {
        $query = Room::with(['facilities', 'bookings' => function ($q) {
            $q->where('status', 'Disetujui')->whereDate('start_datetime', today());
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%")
                  ->orWhere('floor', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('building')) $query->where('building', $request->building);
        if ($request->filled('floor')) $query->where('floor', $request->floor);
        if ($request->filled('min_capacity')) {
            $query->where('capacity_normal', '>=', (int) $request->min_capacity);
        }

        $rooms = $query->orderBy('created_at', 'desc')->paginate(15);

        $allRooms = Room::with(['bookings' => function ($q) {
            $q->where('status', 'Disetujui')
              ->where('start_datetime', '<=', now())
              ->where('end_datetime', '>=', now());
        }])->get();

        $closedCount = 0;
        $overtimeCount = 0;
        foreach ($allRooms as $room) {
            if (!$room->isCurrentlyOpen()) {
                $closedCount++;
                if ($room->bookings->isNotEmpty()) $overtimeCount++;
            }
        }

        $stats = [
            'total'        => Room::count(),
            'available'    => Room::where('status', 'Tersedia')->count(),
            'in_use'       => Room::where('status', 'Sedang Digunakan')->count(),
            'maintenance'  => Room::where('status', 'Maintenance')->count(),
            'closed'       => $closedCount,
            'overtime'     => $overtimeCount,
            'qr_active'    => Room::where('has_qr', true)->count(),
            'qr_inactive'  => Room::where('has_qr', false)->count(),
        ];

        $pendingBookings = Booking::where('status', 'Menunggu Persetujuan')->count();
        $buildings = Room::select('building')->distinct()->whereNotNull('building')->pluck('building')->toArray();
        $floors = Room::select('floor')->distinct()->whereNotNull('floor')->pluck('floor')->toArray();

        return view('owner.rooms.index', compact('rooms', 'stats', 'pendingBookings', 'buildings', 'floors'));
    }

    public function create()
    {
        $commonFacilities = [
            'AC', 'WiFi', 'Proyektor', 'Whiteboard', 'Sound System', 'Smart TV',
            'Kamera', 'Mikrofon', 'Komputer', 'Printer', 'CCTV', 'Meja', 'Kursi',
            'Lemari', 'Papan Tulis', 'Speaker', 'Dispenser', 'Kipas Angin',
            'Pemanas', 'Pendingin', 'Rak Buku', 'Panggung', 'Layar', 'Podium'
        ];
        return view('owner.rooms.create', compact('commonFacilities'));
    }

  public function store(Request $request)
{
    Log::info('Room store started', $request->all());

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|unique:rooms,name',
        'category' => 'nullable|string|max:100',
        'description' => 'nullable|string|max:1000',
        'building' => 'required|string|max:100',
        'floor' => 'required|string|max:50',
        'address' => 'nullable|string|max:500',
        'capacity_normal' => 'required|integer|min:1|max:9999',
        'capacity_max' => 'nullable|integer|min:0|max:9999',
        'status' => ['required', Rule::in(['Tersedia', 'Sedang Dibangun', 'Direnovasi', 'Maintenance'])],
        'condition' => ['required', Rule::in(['Baik', 'Perlu Perbaikan', 'Rusak', 'Renovasi', 'Bersih'])],
        'responsible_person' => 'nullable|string|max:255',
        'responsible_phone' => 'nullable|string|max:20',
        'has_qr' => 'nullable|boolean',
        'images' => 'nullable|array|max:5',
        'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        'documents' => 'nullable|array|max:2',
        'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xlsx|max:10240',
    ], [
        'name.required' => 'Nama ruangan wajib diisi.',
        'name.unique' => 'Nama ruangan sudah digunakan.',
        'building.required' => 'Gedung wajib diisi.',
        'floor.required' => 'Lantai wajib dipilih.',
        'capacity_normal.required' => 'Kapasitas normal wajib diisi.',
        'capacity_normal.min' => 'Kapasitas minimal 1 orang.',
        'images.*.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
        'images.*.max' => 'Ukuran gambar maksimal 10MB.',
        'documents.*.mimes' => 'Format dokumen harus PDF, DOC, DOCX, atau XLSX.',
        'documents.*.max' => 'Ukuran dokumen maksimal 10MB.',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $data = $validator->validated();

    // 🔥 SET DEFAULT VALUES
    $data['capacity_max'] = $data['capacity_max'] ?? 0;
    $data['operational_hours'] = $data['operational_hours'] ?? [];
    $data['images'] = $data['images'] ?? [];
    $data['documents'] = $data['documents'] ?? [];

    // Proses jam operasional
    $operationalHours = [];
    if ($request->has('operational_days')) {
        foreach ($request->operational_days as $day) {
            $start = $request->input("operational_hours.$day", '08:00');
            $end = $request->input("operational_hours_end.$day", '20:00');
            $operationalHours[$day] = $start . '-' . $end;
        }
    }
    $data['operational_hours'] = $operationalHours;
    unset($data['operational_days']);

    // Upload gambar
    $imagePaths = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $img) {
            try {
                $imagePaths[] = $img->store('rooms/images', 'public');
            } catch (\Exception $e) {
                Log::error('Upload gambar gagal: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengupload gambar.')->withInput();
            }
        }
    }
    $data['images'] = $imagePaths;

    // Upload dokumen
    $documentPaths = [];
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $doc) {
            try {
                $documentPaths[] = $doc->store('rooms/documents', 'public');
            } catch (\Exception $e) {
                Log::error('Upload dokumen gagal: ' . $e->getMessage());
                foreach ($imagePaths as $p) Storage::disk('public')->delete($p);
                return back()->with('error', 'Gagal mengupload dokumen.')->withInput();
            }
        }
    }
    $data['documents'] = $documentPaths;

    DB::beginTransaction();
    try {
        $room = Room::create($data);

        // Fasilitas
        if ($request->has('facilities')) {
            foreach ($request->facilities as $fac) {
                if (!empty($fac['name'])) {
                    RoomFacility::create([
                        'room_id' => $room->id,
                        'name' => trim($fac['name']),
                        'quantity' => $fac['quantity'] ?? 1,
                        'condition' => 'Baik',
                    ]);
                }
            }
        }

        // QR Code (hanya jika library tersedia)
        if ($room->has_qr) {
            try {
                $qrService = app(QrCodeService::class);
                $qrPath = $qrService->generateForRoom($room);
                if ($qrPath) $room->update(['qr_code' => $qrPath]);
            } catch (\Exception $e) {
                Log::error('QR generate error: ' . $e->getMessage());
                // Tidak perlu gagal total hanya karena QR
            }
        }

        DB::commit();

        return redirect()->route('owner.rooms.index')
            ->with('success', "🎉 Ruangan <strong>{$room->name}</strong> berhasil ditambahkan!");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Room store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

        foreach ($imagePaths as $p) Storage::disk('public')->delete($p);
        foreach ($documentPaths as $p) Storage::disk('public')->delete($p);

        return back()->with('error', '❌ Gagal menyimpan ruangan: ' . $e->getMessage())->withInput();
    }
}
    // ============================================================
    // SHOW – Detail Ruangan
    // ============================================================
// ============================================================
// SHOW – Room Command Detail
// ============================================================
public function show(Room $room)
{
    $room->load(['facilities']);
    $room->loadCount(['schedules', 'bookings']);

    // Jadwal terbaru (5)
    $schedules = $room->schedules()
        ->orderByDesc('start_datetime')
        ->limit(5)
        ->get();

    // Booking terbaru (5) + relasi ringan
    $bookings = $room->bookings()
        ->with(['approver:id,name'])
        ->orderByDesc('start_datetime')
        ->limit(5)
        ->get();

    // Next schedule
    $nextSchedule = $room->schedules()
        ->where('start_datetime', '>=', now())
        ->orderBy('start_datetime')
        ->first();

    // Next approved booking
    $nextBooking = $room->bookings()
        ->where('status', 'Disetujui')
        ->where('start_datetime', '>=', now())
        ->orderBy('start_datetime')
        ->first();

    // Status prioritas: Unavailable > Maintenance > Occupied > Booked > Available
    $statusInfo = $this->resolveRoomStatus($room, $nextBooking);

    // QR valid
    $qrValid = false;
    try {
        $qrService = app(\App\Services\QrCodeService::class);
        $qrValid = method_exists($qrService, 'isQrValid')
            ? (bool) $qrService->isQrValid($room)
            : ($room->has_qr && $room->qr_code && Storage::disk('public')->exists($room->qr_code));
    } catch (\Throwable $e) {
        $qrValid = $room->has_qr && $room->qr_code && Storage::disk('public')->exists($room->qr_code);
    }

    return view('owner.rooms.show', compact(
        'room', 'schedules', 'bookings',
        'nextSchedule', 'nextBooking', 'statusInfo', 'qrValid'
    ));
}

// ============================================================
// STATUS RESOLVER — prioritas kondisi ruangan
// ============================================================
protected function resolveRoomStatus(Room $room, $nextBooking = null): array
{
    $status    = $room->status;
    $condition = $room->condition;

    // 1. Tidak tersedia
    if ($condition === 'Rusak' || in_array($status, ['Dinonaktifkan', 'Ditutup'], true)) {
        return [
            'key'   => 'unavailable',
            'label' => 'Tidak Tersedia',
            'tone'  => 'red',
            'note'  => 'Ruangan tidak dapat digunakan.',
        ];
    }

    // 2. Maintenance
    if ($status === 'Maintenance' || $condition === 'Renovasi') {
        return [
            'key'   => 'maintenance',
            'label' => 'Maintenance',
            'tone'  => 'orange',
            'note'  => 'Sedang dalam perawatan.',
        ];
    }

    // 3. Sedang digunakan
    if ($status === 'Sedang Digunakan') {
        $until = $room->bookings()
            ->where('status', 'Disetujui')
            ->where('start_datetime', '<=', now())
            ->where('end_datetime', '>=', now())
            ->orderByDesc('end_datetime')
            ->value('end_datetime');

        return [
            'key'   => 'occupied',
            'label' => 'Sedang Digunakan',
            'tone'  => 'blue',
            'note'  => $until
                ? 'Hingga ' . \Carbon\Carbon::parse($until)->format('H:i')
                : 'Sedang berlangsung.',
        ];
    }

    // 4. Dipesan (dalam 2 jam ke depan)
    if ($status === 'Dipesan' || ($nextBooking &&
        \Carbon\Carbon::parse($nextBooking->start_datetime)->diffInHours(now()) <= 2)) {
        return [
            'key'   => 'booked',
            'label' => 'Dipesan',
            'tone'  => 'yellow',
            'note'  => $nextBooking
                ? 'Mulai ' . \Carbon\Carbon::parse($nextBooking->start_datetime)->format('H:i')
                : 'Ada jadwal mendatang.',
        ];
    }

    // 5. Default
    return [
        'key'   => 'available',
        'label' => 'Tersedia',
        'tone'  => 'emerald',
        'note'  => 'Siap digunakan.',
    ];
}

// ============================================================
// PRIVATE DOCUMENT DOWNLOAD — cek whitelist
// ============================================================
public function downloadDocument(Room $room, string $filename)
{
    $documents = is_array($room->documents) ? $room->documents : [];
    $decoded   = urldecode($filename);

    if (! in_array($decoded, $documents, true)) {
        abort(404, 'Dokumen tidak ditemukan.');
    }

    if (! Storage::disk('public')->exists($decoded)) {
        abort(404, 'File tidak tersedia.');
    }

    return Storage::disk('public')->download($decoded, basename($decoded));
}

    // ============================================================
    // EDIT – Form Edit Ruangan
    // ============================================================
    public function edit(Room $room)
    {
        $commonFacilities = ['AC', 'WiFi', 'Proyektor', 'Whiteboard', 'Sound System', 'Smart TV', 'Kamera', 'Mikrofon', 'Komputer', 'Printer', 'CCTV', 'Meja', 'Kursi', 'Lemari', 'Papan Tulis', 'Speaker', 'Dispenser', 'Kipas Angin', 'Pemanas', 'Pendingin', 'Rak Buku', 'Panggung', 'Layar', 'Podium'];
        return view('owner.rooms.edit', compact('room', 'commonFacilities'));
    }

    // ============================================================
    // UPDATE – Update Ruangan
    // ============================================================
    public function update(Request $request, Room $room)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
        'category' => 'nullable|string|max:100',
        'description' => 'nullable|string|max:1000',
        'building' => 'required|string|max:100',
        'floor' => 'required|string|max:50',
        'address' => 'nullable|string|max:500',
        'capacity_normal' => 'required|integer|min:1|max:9999',
        'capacity_max' => 'nullable|integer|min:0|max:9999',
        'status' => ['required', Rule::in(['Tersedia','Sedang Digunakan','Sedang Dibangun','Direnovasi','Maintenance','Dipesan','Menunggu Approval','Ditutup','Dinonaktifkan'])],
        'condition' => ['required', Rule::in(['Baik','Perlu Perbaikan','Rusak','Renovasi','Bersih'])],
        'responsible_person' => 'nullable|string|max:255',
        'responsible_phone' => 'nullable|string|max:20',
        'has_qr' => 'nullable|boolean',
        'images' => 'nullable|array|max:5',
        'images.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
        'documents' => 'nullable|array|max:2',
        'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xlsx|max:10240',
    ]);

    // Format operational hours
    $operationalHours = [];
    if ($request->has('operational_days')) {
        foreach ($request->operational_days as $day) {
            $start = $request->input("operational_hours.$day", '08:00');
            $end   = $request->input("operational_hours_end.$day", '20:00');
            $operationalHours[$day] = $start . '-' . $end;
        }
    }
    $validated['operational_hours'] = $operationalHours;
    $validated['has_qr'] = $request->boolean('has_qr');

    // Upload gambar baru
    if ($request->hasFile('images')) {
        $paths = is_array($room->images) ? $room->images : [];
        foreach ($request->file('images') as $img) {
            $paths[] = $img->store('rooms/images', 'public');
        }
        $validated['images'] = $paths;
    }

    // Upload dokumen baru
    if ($request->hasFile('documents')) {
        $paths = is_array($room->documents) ? $room->documents : [];
        foreach ($request->file('documents') as $doc) {
            $paths[] = $doc->store('rooms/documents', 'public');
        }
        $validated['documents'] = $paths;
    }

    DB::beginTransaction();
    try {
        $room->update($validated);

        // Update fasilitas: hapus dulu, lalu insert ulang
        if ($request->has('facilities')) {
            $room->facilities()->delete();
            foreach ($request->facilities as $fac) {
                if (!empty($fac['name'])) {
                    RoomFacility::create([
                        'room_id'   => $room->id,
                        'name'      => trim($fac['name']),
                        'quantity'  => $fac['quantity'] ?? 1,
                        'condition' => 'Baik',
                    ]);
                }
            }
        }

        // Generate QR jika diaktifkan dan belum ada
        if ($room->has_qr && !$room->qr_code) {
            try {
                $qrService = app(QrCodeService::class);
                $qrPath = $qrService->generateForRoom($room);
                if ($qrPath) $room->update(['qr_code' => $qrPath]);
            } catch (\Exception $e) {
                Log::error('QR generate error: ' . $e->getMessage());
            }
        }

        DB::commit();

        return redirect()
            ->route('owner.rooms.show', $room)
            ->with('success', "✅ Ruangan <strong>{$room->name}</strong> berhasil diupdate!");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Room update failed: ' . $e->getMessage());
        return back()->with('error', '❌ Gagal mengupdate ruangan: ' . $e->getMessage())->withInput();
    }
}
    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('owner.rooms.index')->with('success', '📦 Ruangan diarsipkan.');
    }

    // ============================================================
    // CALENDAR – Kalender Booking
    // ============================================================
public function calendar(Request $request, RoomBookingClassifier $classifier)
{
    // ---- View mode ----
    $view = $request->query('view', 'month');
    if (!in_array($view, ['month', 'week', 'day', 'agenda'], true)) {
        $view = 'month';
    }

    // ---- Month / Year ----
    $month = (int) $request->query('month', now()->month);
    $year  = (int) $request->query('year', now()->year);
    $month = max(1, min(12, $month));
    $year  = max(2000, min(2100, $year));

    $cursor = \Carbon\Carbon::create($year, $month, 1);

    // ---- Rentang waktu berdasarkan view ----
    if ($view === 'day') {
        $rangeStart = $cursor->copy();
        $rangeEnd   = $cursor->copy()->endOfDay();
    } elseif ($view === 'week') {
        $rangeStart = $cursor->copy()->startOfMonth()->startOfWeek(\Carbon\WeekDay::Monday);
        $rangeEnd   = $rangeStart->copy()->addDays(6)->endOfDay();
    } elseif ($view === 'agenda') {
        $rangeStart = $cursor->copy()->startOfMonth();
        $rangeEnd   = $cursor->copy()->addMonths(3)->endOfMonth();
    } else {
        $rangeStart = $cursor->copy()->startOfMonth();
        $rangeEnd   = $cursor->copy()->endOfMonth();
    }

    // ---- Query ----
    $query = Booking::with(['room'])
        ->whereBetween('start_datetime', [$rangeStart, $rangeEnd]);

    if ($request->filled('building')) {
        $query->whereHas('room', fn ($q) => $q->where('building', $request->building));
    }
    if ($request->filled('room_id')) {
        $query->where('room_id', (int) $request->room_id);
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(fn ($qq) => $qq
            ->where('title', 'like', "%{$q}%")
            ->orWhere('requester_name', 'like', "%{$q}%"));
    }

    $bookings = $query->orderBy('start_datetime')->get();

    // ---- Klasifikasi sekali di service ----
    $classified = $classifier->classifyMany($bookings);
    $conflicts  = $classifier->detectConflicts($bookings);

    // ---- Group per hari untuk kalender ----
    $bookingsByDay = $bookings->groupBy(fn ($b) =>
        optional($b->start_datetime)->format('Y-m-d')
    );

    // ---- Data pendukung untuk filter ----
    $buildings = Room::query()->select('building')->distinct()
        ->whereNotNull('building')->orderBy('building')->pluck('building');
    $rooms = Room::query()->orderBy('name')->get(['id','name','room_code','building','floor']);

    return view('owner.rooms.calendar', [
        'view'          => $view,
        'month'         => $month,
        'year'          => $year,
        'cursor'        => $cursor,
        'rangeStart'    => $rangeStart,
        'rangeEnd'      => $rangeEnd,
        'bookings'      => $bookings,
        'bookingsByDay' => $bookingsByDay,
        'classified'    => $classified,
        'conflicts'     => $conflicts,
        'buildings'     => $buildings,
        'rooms'         => $rooms,
        'classifier'    => $classifier,
    ]);
}


    // ============================================================
    // BOOKING – Ajukan Booking
    // ============================================================
    public function storeBooking(Request $request, Room $room)
    {
        $booking = Booking::create([
            'room_id' => $room->id,
            'requester_name' => $request->requester_name,
            'requester_phone' => $request->requester_phone,
            'title' => $request->title,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'status' => 'Menunggu Persetujuan',
            'public_token' => Str::random(32),
        ]);

        foreach (\App\Models\User::where('level_id', 1)->get() as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => '📅 Booking Ruangan Baru',
                'message' => "{$booking->requester_name} mengajukan booking {$room->name}.",
                'icon' => 'fas fa-door-open',
                'link' => route('owner.rooms.show', $room->id),
                'data' => json_encode(['booking_id' => $booking->id]),
            ]);
        }

        return redirect()->back()->with('success', '✅ Booking diajukan!');
    }

    // ============================================================
    // REQUESTS – Daftar Permintaan Booking
    // ============================================================
    public function requests()
{
    $pendingBookings = Booking::with('room')             // ✅ hapus 'requester'
        ->where('status', 'Menunggu Persetujuan')
        ->orderBy('created_at', 'asc')
        ->paginate(15);

    return view('owner.rooms.requests', compact('pendingBookings'));
}

    public function approveBooking(Booking $booking)
    {
        $booking->update([
            'status' => 'Disetujui',
            'approval_status' => 'Approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', '✅ Booking disetujui.');
    }

    public function rejectBooking(Booking $booking)
    {
        $booking->update([
            'status' => 'Ditolak',
            'approval_status' => 'Rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', '❌ Booking ditolak.');
    }

    // ============================================================
    // EXPORT – Export Data Ruangan
    // ============================================================
    public function export($format)
    {
        $rooms = Room::with(['facilities', 'bookings'])->get();

        if ($format === 'excel') {
            return (new \App\Exports\RoomsExport($rooms))->download('rooms.xlsx');
        }

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.rooms.export-pdf', compact('rooms'));
            return $pdf->download('rooms.pdf');
        }

        if ($format === 'csv') {
            return $this->exportCsv($rooms);
        }

        return back()->with('error', 'Format tidak didukung.');
    }

    private function exportCsv($rooms)
    {
        $filename = 'rooms_' . date('Ymd_His') . '.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Kode', 'Nama', 'Gedung', 'Lantai', 'Kapasitas', 'Status', 'Kondisi']);

        foreach ($rooms as $room) {
            fputcsv($handle, [
                $room->room_code,
                $room->name,
                $room->building,
                $room->floor,
                $room->capacity_normal,
                $room->status,
                $room->condition,
            ]);
        }
        fclose($handle);

        return response()->stream(
            fn() => null,
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]
        );
    }
    // ============================================================
// KELOLA FASILITAS
// ============================================================
public function facilitiesIndex(Room $room)
{
    $room->load('facilities');
    return view('owner.rooms.facilities', compact('room'));
}

// ============================================================
// LIHAT JADWAL
// ============================================================
public function schedulesIndex(Room $room)
{
    $room->load('schedules');
    return view('owner.rooms.schedules', compact('room'));
}

public function bookingCreate(Room $room)
{
    $room->load([
        'schedules' => function ($q) {
            $q->orderBy('start_datetime', 'desc')->limit(10);
        },
    ]);

    // Pastikan selalu array
    $allImages = is_array($room->images) ? $room->images : [];

    foreach ($room->schedules as $schedule) {
        // ✅ Akses images secara aman
        $rawImages = $schedule->getAttributes()['images'] ?? null;

        if (is_array($rawImages)) {
            $scheduleImages = $rawImages;
        } elseif (is_string($rawImages) && $rawImages !== '') {
            $scheduleImages = json_decode($rawImages, true) ?: [];
        } else {
            $scheduleImages = [];
        }

        if (is_array($scheduleImages)) {
            $allImages = array_merge($allImages, $scheduleImages);
        }
    }

    $allImages = array_values(array_unique($allImages));

    return view('owner.rooms.booking-create', compact('room', 'allImages'));
}
// ============================================================
// BUAT BOOKING - SIMPAN
// ============================================================
public function bookingStore(Request $request, Room $room)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'start_datetime' => 'required|date',
        'end_datetime' => 'required|date|after:start_datetime',
        'requester_name' => 'required|string|max:255',
        'requester_phone' => 'nullable|string|max:20',
    ]);

    $booking = Booking::create([
        'room_id' => $room->id,
        'title' => $validated['title'],
        'start_datetime' => $validated['start_datetime'],
        'end_datetime' => $validated['end_datetime'],
        'requester_name' => $validated['requester_name'],
        'requester_phone' => $validated['requester_phone'] ?? null,
        'status' => 'Menunggu Persetujuan',
        'public_token' => Str::random(32),
    ]);

    // Kirim notifikasi ke Owner
    foreach (\App\Models\User::where('level_id', 1)->get() as $user) {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => '📅 Booking Ruangan Baru',
            'message' => "{$booking->requester_name} mengajukan booking {$room->name}.",
            'icon' => 'fas fa-door-open',
            'link' => route('owner.rooms.show', $room->id),
            'data' => json_encode(['booking_id' => $booking->id]),
        ]);
    }

    return redirect()->route('owner.rooms.show', $room)
        ->with('success', '✅ Booking berhasil diajukan!');
}

// ============================================================
// GENERATE QR CODE
// ============================================================
public function generateQr(Request $request, Room $room)
{
    try {
        // Reset status jika QR code null tapi has_qr true
        if ($room->has_qr && is_null($room->qr_code)) {
            $room->update(['has_qr' => false, 'qr_code' => null]);
            $room->refresh();
        }

        $qrService = app(\App\Services\QrCodeService::class);
        $qrPath = $qrService->generateForRoom($room);

        if ($qrPath) {
            $room->update([
                'has_qr' => true,
                'qr_code' => $qrPath
            ]);

            return redirect()->route('owner.rooms.show', $room)
                ->with('success', '✅ QR Code berhasil dibuat!');
        }

        return redirect()->route('owner.rooms.show', $room)
            ->with('error', '❌ Gagal membuat QR Code. Cek log untuk detail.');
    } catch (\Exception $e) {
        Log::error('Generate QR error: ' . $e->getMessage());
        return redirect()->route('owner.rooms.show', $room)
            ->with('error', '❌ Error: ' . $e->getMessage());
    }
}

// ============================================================
// MAINTENANCE - TAMPILKAN FORM
// ============================================================
public function maintenance(Room $room)
{
    return view('owner.rooms.maintenance', compact('room'));
}

// ============================================================
// RIWAYAT BOOKING & AKTIVITAS
// ============================================================
public function history(Room $room)
{
    $room->load(['bookings' => function ($q) {
        $q->orderBy('created_at', 'desc');
    }]);
    return view('owner.rooms.history', compact('room'));
}
// ============================================================
// BULK ACTION UNTUK RUANGAN
// ============================================================
public function bulkAction(Request $request)
{
    $action = $request->input('action');
    $roomIds = $request->input('selected_rooms', []);

    if (empty($roomIds)) {
        return redirect()->back()->with('error', 'Pilih minimal satu ruangan.');
    }

    $rooms = Room::whereIn('id', $roomIds)->get();

    switch ($action) {
        case 'export':
            // Ekspor data ruangan terpilih (CSV sederhana)
            return $this->exportSelected($rooms);

        case 'generate-qr':
            // Generate QR untuk ruangan terpilih
            return $this->bulkGenerateQr($rooms);

        case 'delete':
            // Arsipkan ruangan terpilih (soft delete)
            $count = 0;
            foreach ($rooms as $room) {
                $room->delete();
                $count++;
            }
            return redirect()->back()->with('success', "✅ $count ruangan berhasil diarsipkan.");

        default:
            return redirect()->back()->with('error', 'Aksi tidak dikenal.');
    }
}

private function exportSelected($rooms)
{
    $filename = 'rooms_export_' . date('Ymd_His') . '.csv';
    $handle = fopen('php://output', 'w');
    fputcsv($handle, ['Kode', 'Nama', 'Gedung', 'Lantai', 'Kapasitas', 'Status', 'Kondisi']);

    foreach ($rooms as $room) {
        fputcsv($handle, [
            $room->room_code,
            $room->name,
            $room->building,
            $room->floor,
            $room->capacity_normal,
            $room->status,
            $room->condition,
        ]);
    }
    fclose($handle);

    return response()->stream(
        fn() => null,
        200,
        [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]
    );
}

private function bulkGenerateQr($rooms)
{
    $qrService = app(\App\Services\QrCodeService::class);
    $success = 0;
    $failed = 0;
    $errors = [];

    foreach ($rooms as $room) {
        try {
            // Cek apakah QR sudah ada (jika sudah, skip)
            if ($room->has_qr && $room->qr_code && Storage::disk('public')->exists($room->qr_code)) {
                $success++;
                continue;
            }

            $qrPath = $qrService->generateForRoom($room);
            if ($qrPath) {
                $room->update(['has_qr' => true, 'qr_code' => $qrPath]);
                $success++;
            } else {
                $failed++;
                $errors[] = "Ruangan {$room->name} gagal generate.";
            }
        } catch (\Exception $e) {
            $failed++;
            Log::error('Bulk QR failed for room ' . $room->id . ': ' . $e->getMessage());
            $errors[] = "Ruangan {$room->name}: " . $e->getMessage();
        }
    }

    $message = "✅ QR berhasil dibuat untuk $success ruangan.";
    if ($failed > 0) {
        $message .= " ⚠️ $failed ruangan gagal: " . implode('; ', $errors);
    }
    return redirect()->back()->with('success', $message);
}
// ============================================================
// DOWNLOAD QR CODE DALAM BERBAGAI FORMAT
// ============================================================
public function downloadQr(Request $request, Room $room, $format)
{
    $format = strtolower($format);
    $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp', 'pdf'];
    if (!in_array($format, $allowed)) {
        return redirect()->back()->with('error', 'Format tidak didukung.');
    }

    // Jika QR tidak valid, generate ulang
    $qrService = app(\App\Services\QrCodeService::class);
    if (!$qrService->isQrValid($room)) {
        // Hapus status lama
        if ($room->has_qr) {
            $room->update(['has_qr' => false, 'qr_code' => null]);
        }
        // Generate baru
        $qrPath = $qrService->generateForRoom($room);
        if ($qrPath) {
            $room->update(['has_qr' => true, 'qr_code' => $qrPath]);
        } else {
            return redirect()->back()->with('error', 'Gagal membuat QR Code.');
        }
    }

    // Ambil file QR
    $fullPath = Storage::disk('public')->path($room->qr_code);
    if (!file_exists($fullPath)) {
        return redirect()->back()->with('error', 'File QR tidak ditemukan.');
    }

    if ($format === 'pdf') {
        // Generate PDF
        $qrBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath));
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.rooms.qr-pdf', compact('room', 'qrBase64'));
        return $pdf->download('qr_' . $room->room_code . '.pdf');
    }

    // Gambar
    $mime = match ($format) {
        'jpg', 'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        default => 'image/png',
    };

    // Jika format PNG, kirim asli
    if ($format === 'png') {
        return response()->download($fullPath, 'qr_' . $room->room_code . '.png', ['Content-Type' => 'image/png']);
    }

    // Konversi ke format lain (JPG, WEBP) menggunakan GD
    $img = imagecreatefrompng($fullPath);
    if (!$img) {
        return redirect()->back()->with('error', 'Gagal memproses gambar QR.');
    }
    ob_start();
    switch ($format) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($img, null, 90);
            break;
        case 'webp':
            imagewebp($img, null, 80);
            break;
        default:
            imagedestroy($img);
            return redirect()->back()->with('error', 'Format tidak didukung untuk konversi.');
    }
    $imageData = ob_get_clean();
    imagedestroy($img);
    return response($imageData, 200)
        ->header('Content-Type', $mime)
        ->header('Content-Disposition', 'attachment; filename="qr_' . $room->room_code . '.' . $format . '"');
}

// ============================================================
// TESTING QR CODE - TAMPILKAN HALAMAN
// ============================================================
public function testingIndex()
{
    return view('owner.rooms.testing');
}

// ============================================================
// TESTING QR CODE - SCAN (AJAX dari kamera)
// ============================================================
public function testingScan(Request $request)
{
    $token = $request->input('token');
    $room = Room::where('room_token', $token)->first();

    if (!$room) {
        return response()->json([
            'valid' => false,
            'message' => 'QR tidak ditemukan'
        ]);
    }

    if (!$room->has_qr) {
        return response()->json([
            'valid' => false,
            'message' => 'QR tidak aktif'
        ]);
    }

    return response()->json([
        'valid' => true,
        'room' => [
            'name' => $room->name,
            'code' => $room->room_code,
            'building' => $room->building,
            'floor' => $room->floor,
            'capacity' => $room->capacity_normal,
        ],
        'token' => $room->room_token,
        'qr_id' => 'QR-ROOM-' . str_pad($room->id, 6, '0', STR_PAD_LEFT),
    ]);
}
// ============================================================
// PUBLIKASI QR – TAMPILKAN HALAMAN
// ============================================================
public function publicIndex(Request $request)
{
    $query = Room::query();

    // Filter pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('room_code', 'like', "%{$search}%");
        });
    }

    // Filter status publikasi
    if ($request->filled('public_status')) {
        $status = $request->public_status;
        if ($status === 'none') {
            $query->whereNull('public_status');
        } else {
            $query->where('public_status', $status);
        }
    }

    $rooms = $query->orderBy('created_at', 'desc')->paginate(15);

    // Statistik publikasi
    $stats = [
        'total'          => Room::count(),
        'public'         => Room::where('public_status', 'public')->count(),
        'login'          => Room::where('public_status', 'login')->count(),
        'both'           => Room::where('public_status', 'both')->count(),
        'not_published'  => Room::whereNull('public_status')->count(),
    ];

    return view('owner.rooms.public', compact('rooms', 'stats'));
}

// ============================================================
// PUBLIKASI QR – RIWAYAT
// ============================================================
public function publicHistory()
{
    // Ambil riwayat perubahan publikasi (misalnya dari log atau model audit)
    // Jika belum ada tabel khusus, kita bisa ambil dari room yang sudah dipublikasi
    $histories = Room::whereNotNull('public_status')
        ->orderBy('updated_at', 'desc')
        ->take(50)
        ->get();

    return view('owner.rooms.public-history', compact('histories'));
}
// ============================================================
// PUBLIKASI QR – EXPORT DATA
// ============================================================
public function publicExport(Request $request)
{
    // Ambil data ruangan dengan filter yang sama seperti di index (opsional)
    $query = Room::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('room_code', 'like', "%{$search}%");
        });
    }

    if ($request->filled('public_status')) {
        $status = $request->public_status;
        if ($status === 'none') {
            $query->whereNull('public_status');
        } else {
            $query->where('public_status', $status);
        }
    }

    $rooms = $query->orderBy('created_at', 'desc')->get();

    // Nama file
    $filename = 'publikasi_ruangan_' . date('Ymd_His') . '.csv';

    // Buat response CSV
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename={$filename}",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0',
    ];

    $callback = function () use ($rooms) {
        $handle = fopen('php://output', 'w');
        // Tambahkan BOM untuk UTF-8 (agar Excel tidak error)
        fputs($handle, "\xEF\xBB\xBF");

        // Header CSV
        fputcsv($handle, [
            'ID',
            'Nama Ruangan',
            'Kode Ruangan',
            'Gedung',
            'Lantai',
            'Kapasitas',
            'Status Ruangan',
            'Kondisi',
            'Status Publikasi',
            'Tanggal Publikasi',
            'Role yang Diizinkan',
            'Field yang Ditampilkan',
            'Dibuat pada',
        ]);

        foreach ($rooms as $room) {
            $publicStatus = $room->public_status ?? 'Belum Publik';
            $roles = $room->public_roles ? json_decode($room->public_roles, true) : [];
            $fields = $room->public_fields ? json_decode($room->public_fields, true) : [];

            fputcsv($handle, [
                $room->id,
                $room->name,
                $room->room_code,
                $room->building ?? '-',
                $room->floor ?? '-',
                $room->capacity_normal,
                $room->status,
                $room->condition,
                $publicStatus,
                $room->published_at ? $room->published_at->format('d/m/Y H:i') : '-',
                implode(', ', $roles),
                implode(', ', $fields),
                $room->created_at->format('d/m/Y H:i'),
            ]);
        }

        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}
// ============================================================
// PUBLIKASI QR – BULK ACTION
// ============================================================
public function bulkPublicAction(Request $request)
{
    $action = $request->input('action');
    $roomIds = $request->input('room_ids', []);

    if (empty($roomIds)) {
        return redirect()->back()->with('error', 'Pilih minimal satu ruangan.');
    }

    $rooms = Room::whereIn('id', $roomIds)->get();

    switch ($action) {
        case 'publish':
            foreach ($rooms as $room) {
                // Set default publikasi (misal: publik + login, dengan semua role)
                $room->update([
                    'public_status' => 'both',
                    'published_at' => now(),
                    'public_roles' => json_encode(['Owner', 'Superadmin', 'Ketua', 'Pengurus', 'Anggota UKM']),
                    'public_fields' => json_encode(['Nama Ruangan', 'Kode Ruangan', 'Gedung', 'Lantai', 'Kapasitas', 'Fasilitas']),
                ]);
            }
            $message = "✅ {$rooms->count()} ruangan berhasil dipublikasikan.";
            break;

        case 'unpublish':
            foreach ($rooms as $room) {
                $room->update([
                    'public_status' => null,
                    'published_at' => null,
                    'public_roles' => null,
                    'public_fields' => null,
                ]);
            }
            $message = "🔒 {$rooms->count()} ruangan dinonaktifkan publikasinya.";
            break;

        case 'export':
            // Ekspor data ruangan terpilih
            return $this->exportSelectedPublic($rooms);

        default:
            return redirect()->back()->with('error', 'Aksi tidak dikenal.');
    }

    return redirect()->route('owner.rooms.public.index')->with('success', $message);
}

private function exportSelectedPublic($rooms)
{
    $filename = 'publikasi_terpilih_' . date('Ymd_His') . '.csv';
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename={$filename}",
    ];

    $callback = function () use ($rooms) {
        $handle = fopen('php://output', 'w');
        fputs($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['Nama', 'Kode', 'Gedung', 'Lantai', 'Kapasitas', 'Status Publikasi']);

        foreach ($rooms as $room) {
            fputcsv($handle, [
                $room->name,
                $room->room_code,
                $room->building ?? '-',
                $room->floor ?? '-',
                $room->capacity_normal,
                $room->public_status ?? 'Belum Publik',
            ]);
        }
        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}
// ============================================================
// PUBLIKASI QR – TOGGLE (AKTIFKAN / NONAKTIFKAN)
// ============================================================
// ============================================================
// PUBLIKASI QR – TOGGLE (AKTIFKAN / NONAKTIFKAN)
// ============================================================
public function togglePublic(Request $request, Room $room)
{
    $validated = $request->validate([
        'allow_public' => 'nullable|boolean',
        'allow_login'  => 'nullable|boolean',
        'roles'        => 'nullable|array',
        'roles.*'      => 'string|in:Owner,Superadmin,Ketua,Pengurus,Anggota UKM,Anggota Umum,Pengunjung',
        'fields'       => 'nullable|array',
        'fields.*'     => 'string|in:Nama Ruangan,Kode Ruangan,Foto Ruangan,Alamat,Gedung,Lantai,Kapasitas,Fasilitas,Status,Jadwal',
    ]);

    $allowPublic = $request->boolean('allow_public');
    $allowLogin  = $request->boolean('allow_login');

    try {
        if (!$allowPublic && !$allowLogin) {
            // Nonaktifkan publikasi
            $updated = $room->update([
                'public_status' => null,
                'published_at'  => null,
                'public_roles'  => null,
                'public_fields' => null,
            ]);

            if (!$updated) {
                throw new \Exception('Gagal menyimpan perubahan. Tidak ada data yang terupdate.');
            }

            $message = "🔒 Publikasi untuk <strong>{$room->name}</strong> dinonaktifkan.";
        } else {
            // Tentukan status
            if ($allowPublic && $allowLogin) {
                $status = 'both';
            } elseif ($allowPublic) {
                $status = 'public';
            } else {
                $status = 'login';
            }

            $roles = $request->filled('roles') ? json_encode($request->roles) : null;
            $fields = $request->filled('fields') ? json_encode($request->fields) : null;

            $updated = $room->update([
                'public_status' => $status,
                'published_at'  => now(),
                'public_roles'  => $roles,
                'public_fields' => $fields,
            ]);

            if (!$updated) {
                // Cek apakah data sudah sesuai (tidak ada perubahan)
                $fresh = $room->fresh();
                if ($fresh->public_status === $status && $fresh->published_at !== null) {
                    $message = "ℹ️ Publikasi untuk <strong>{$room->name}</strong> sudah sesuai, tidak ada perubahan yang diperlukan.";
                } else {
                    throw new \Exception('Gagal menyimpan perubahan. Silakan coba lagi.');
                }
            } else {
                $message = "✅ Publikasi untuk <strong>{$room->name}</strong> berhasil diupdate.";
            }
        }

        return redirect()->route('owner.rooms.public.index')->with('success', $message);

    } catch (\Exception $e) {
        // Log error untuk debugging
        \Log::error('Error saat toggle publikasi ruangan ID ' . $room->id . ': ' . $e->getMessage(), [
            'request' => $request->all(),
            'room'    => $room->toArray(),
        ]);

        return redirect()->route('owner.rooms.public.index')
            ->with('error', '❌ Gagal memperbarui publikasi: ' . $e->getMessage());
    }
}
// app/Http/Controllers/Owner/RoomController.php

public function maintenanceIndex()
{
    // Ambil ruangan yang sedang dalam maintenance (status = 'Maintenance')
    // atau yang memiliki jadwal maintenance aktif
    $rooms = Room::where('status', 'Maintenance')
                 ->orWhereHas('maintenances', function ($q) {
                     $q->where('status', 'aktif')
                       ->where('start_datetime', '<=', now())
                       ->where('end_datetime', '>=', now());
                 })
                 ->with('maintenances')
                 ->paginate(15);

    return view('owner.rooms.maintenance-index', compact('rooms'));
}
public function checkConflicts(Request $request)
{
    $roomId = $request->room_id;
    $start = $request->start;
    $end = $request->end;

    $room = Room::find($roomId);
    if (!$room) {
        return response()->json(['conflicts' => ['Ruangan tidak ditemukan']]);
    }

    $conflicts = $this->checkConflicts($roomId, $start, $end); // gunakan method yang sudah ada di BookingController atau buat sendiri
    $schedules = Schedule::where('room_id', $roomId)
                ->where(function($q) use ($start, $end) {
                    $q->where('start_datetime', '<', $end)
                      ->where('end_datetime', '>', $start);
                })
                ->get(['title', 'start_datetime as start', 'end_datetime as end']);

    return response()->json([
        'conflicts' => $conflicts,
        'schedules' => $schedules
    ]);
}
// ============================================================
// TESTING QR CODE - MANUAL (POST)
// ============================================================
public function testingManual(Request $request)
{
    $input = $request->input('input_value');
    $type = $request->input('input_type', 'token');

    // Ekstrak token dari URL jika tipe url
    if ($type === 'url') {
        // Ambil bagian terakhir dari URL
        $segments = explode('/', $input);
        $token = end($segments);
    } else {
        $token = $input;
    }

    // Cari room berdasarkan token
    $room = Room::where('room_token', $token)->first();

    $result = [];
    if ($room) {
        $result['valid'] = true;
        $result['room'] = [
            'name' => $room->name,
            'code' => $room->room_code,
            'building' => $room->building,
            'floor' => $room->floor,
            'capacity' => $room->capacity_normal,
        ];
        $result['token'] = $room->room_token;
        $result['qr_id'] = 'QR-ROOM-' . str_pad($room->id, 6, '0', STR_PAD_LEFT);
    } else {
        $result['valid'] = false;
        $result['message'] = 'Token/URL tidak ditemukan di database.';
    }

    return view('owner.rooms.testing', compact('result'));
}
}
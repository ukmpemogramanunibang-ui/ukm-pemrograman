<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoomAccessController extends Controller
{
    /**
     * DAFTAR ROLE YANG DIIZINKAN MENGAKSES PRIVATE ROOM
     * Sesuaikan dengan nama level di database Anda.
     */
    private array $allowedRoles = [
    'owner',
    'superadmin',
    'ketua',
    'pengurus',
    'anggota',
];

    // ============================================================
    // 1. HALAMAN PUBLIK – TANPA LOGIN
    // ============================================================
public function publicIndex(Request $request)
{
    $roomToken = $request->query('room_token');

    $query = Room::query()
        ->whereIn('public_status', ['public', 'both'])
        ->with([
            'facilities',
            'bookings' => fn ($q) => $q
                ->where('status', 'Disetujui')
                ->whereDate('start_datetime', today())
                ->orderBy('start_datetime'),
            'schedules' => fn ($q) => $q
                ->orderBy('start_datetime')
                ->limit(8),
        ]);

    if ($roomToken)                    $query->where('room_token', $roomToken);
    if ($request->filled('floor'))     $query->where('floor', $request->floor);
    if ($request->filled('category'))  $query->where('category', $request->category);
    if ($request->filled('status'))    $query->where('status', $request->status);
    if ($request->filled('condition')) $query->where('condition', $request->condition);

    if ($request->boolean('booking_today')) {
        $query->whereHas('bookings', fn ($q) => $q
            ->where('status', 'Disetujui')
            ->whereDate('start_datetime', today()));
    }

    $rooms = $query->latest('id')->paginate(12)->withQueryString();

    // ✅ SIDEBAR: semua ruangan publik, dikelompokkan per lantai
    $roomsByFloor = Room::query()
        ->whereIn('public_status', ['public', 'both'])
        ->when($roomToken, fn ($q) => $q->where('room_token', $roomToken))
        ->orderBy('name')
        ->get()
        ->groupBy('floor')
        ->sortBy(function ($group, $floor) {
            if (preg_match('/Lantai\s+(\d+)/i', (string) $floor, $m)) return 100 + (int) $m[1];
            if (preg_match('/Basement\s+(\d+)/i', (string) $floor, $m)) return 50 + (int) $m[1];
            if (stripos((string) $floor, 'loteng') !== false) return 500;
            if (stripos((string) $floor, 'aula')   !== false) return 510;
            return 999;
        });

    $floors     = Room::whereIn('public_status', ['public','both'])->whereNotNull('floor')->distinct()->pluck('floor');
    $categories = Room::whereIn('public_status', ['public','both'])->whereNotNull('category')->distinct()->pluck('category');
    $statuses   = Room::whereIn('public_status', ['public','both'])->whereNotNull('status')->distinct()->pluck('status');
    $conditions = Room::whereIn('public_status', ['public','both'])->whereNotNull('condition')->distinct()->pluck('condition');

    return view('room_publik.index', compact(
        'rooms', 'roomsByFloor', 'floors', 'categories', 'statuses', 'conditions'
    ));
}

public function publicRealtime(string $token)
{
    $room = \App\Models\Room::query()
        ->where('room_token', $token)
        ->whereIn('public_status', ['public', 'both']) // ✅ Hanya publik
        ->with('facilities')
        ->first();

    if (!$room) {
        return response()->json([
            'success' => false,
            'message' => 'Ruangan tidak ditemukan atau tidak dipublikasikan.',
        ], 404);
    }

    $now = now();

    // Booking hari ini (disetujui)
    $bookings = $room->bookings()
        ->where('status', 'Disetujui')
        ->where(function ($query) use ($now) {
            $query->where('start_datetime', '<=', $now->copy()->endOfDay())
                  ->where('end_datetime', '>=', $now->copy()->startOfDay());
        })
        ->orderBy('start_datetime')
        ->get();

    // Jadwal (dari model RoomSchedule)
    $schedules = \App\Models\RoomSchedule::query()
        ->where('room_id', $room->id)
        ->whereIn('status', ['Aktif', 'Selesai'])
        ->orderBy('start_datetime')
        ->get();

    $currentBooking = $bookings->first(function ($item) use ($now) {
        return $now->between($item->start_datetime, $item->end_datetime);
    });

    $nextBooking = $bookings->first(function ($item) use ($now) {
        return $item->start_datetime->gt($now);
    });

    // Status live
    if ($room->condition !== null && in_array(strtolower($room->condition), [
        'renovated', 'sedang direnovasi', 'dalam perbaikan', 'under repair', 'under construction'
    ], true)) {
        $liveStatus = $room->condition;
    } elseif ($currentBooking) {
        $liveStatus = 'Sedang Digunakan';
    } elseif ($nextBooking) {
        $liveStatus = 'Dipesan';
    } else {
        $liveStatus = 'Tersedia';
    }

    return response()->json([
        'success' => true,
        'server_time' => now()->toIso8601String(),
        'room' => [
            'id' => $room->id,
            'name' => $room->name,
            'room_code' => $room->room_code,
            'floor' => $room->floor,
            'category' => $room->category,
            'capacity' => $room->capacity_normal,
            'condition' => $room->condition,
            'status' => $room->status,
            'live_status' => $liveStatus,
            'images' => is_array($room->images) ? $room->images : [],
            'operational_hours' => $room->operational_hours ?? [],

            'facilities' => $room->facilities->map(fn($f) => [
                'name' => $f->name,
                'quantity' => $f->quantity,
            ])->values(),

            'booking' => $bookings->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title ?? 'Booking Ruangan',
                'start' => optional($b->start_datetime)->format('Y-m-d H:i:s'),
                'end' => optional($b->end_datetime)->format('Y-m-d H:i:s'),
                'status' => $b->status,
            ])->values(),

            'current_booking' => $currentBooking ? [
                'id' => $currentBooking->id,
                'title' => $currentBooking->title ?? 'Sedang digunakan',
                'start' => $currentBooking->start_datetime->format('Y-m-d H:i:s'),
                'end' => $currentBooking->end_datetime->format('Y-m-d H:i:s'),
            ] : null,

            'next_booking' => $nextBooking ? [
                'id' => $nextBooking->id,
                'title' => $nextBooking->title ?? 'Booking berikutnya',
                'start' => $nextBooking->start_datetime->format('Y-m-d H:i:s'),
                'end' => $nextBooking->end_datetime->format('Y-m-d H:i:s'),
            ] : null,

            'schedules' => $schedules->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'description' => $s->description,
                'activity_type' => $s->activity_type,
                'recurrence_type' => $s->recurrence_type,
                'start' => optional($s->start_datetime)->format('Y-m-d H:i:s'),
                'end' => optional($s->end_datetime)->format('Y-m-d H:i:s'),
                'day' => optional($s->start_datetime)->isoFormat('dddd'),
                'status' => $s->status,
            ])->values(),
        ],
    ]);
}
    // ============================================================
    // 2. NIM LOOKUP – PUBLIC (dengan rate limiting)
    // ============================================================
    public function publicNimLookup(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:30|regex:/^[A-Za-z0-9._-]+$/',
        ]);

        $nim = trim($request->nim);
        $key = 'room-public-nim:' . sha1($request->ip() . '|' . Str::lower($nim));

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Coba lagi nanti.',
            ], 429);
        }

        RateLimiter::hit($key, 60);

        $user = User::with('level')
            ->where('nim', $nim)
            ->where('is_active', true)
            ->first();

        // Jangan bocorkan apakah NIM terdaftar atau tidak
        if (!$user) {
            return response()->json([
                'success' => true,
                'rooms' => [],
                'message' => 'Tidak ada ruangan publik yang sedang berlangsung.',
            ]);
        }

        // Hanya tampilkan ruangan publik yang sedang digunakan/dipesan
        $rooms = Room::with(['facilities'])
            ->whereIn('public_status', ['public', 'both'])
            ->whereIn('status', ['Sedang Digunakan', 'Dipesan'])
            ->get()
            ->filter(fn($room) => $room->isCurrentlyOpen())
            ->values();

        return response()->json([
            'success' => true,
            'rooms' => $rooms->map(fn($room) => $this->publicRoomPayload($room)),
        ]);
    }

    // ============================================================
    // 3. HALAMAN PRIVATE – WAJIB LOGIN + ROLE
    // ============================================================
    public function privateIndex(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk mengakses Room Private.');
        }

        if (!$this->userCanAccessPrivateRoom($user)) {
            Log::warning('Unauthorized private room access attempt', [
                'user_id'   => $user->id,
                'level'     => optional($user->level)->name,
                'ip'        => $request->ip(),
                'url'       => $request->fullUrl(),
            ]);

            return redirect('/')->with('error', 'Akun Anda tidak memiliki izin untuk melihat ruangan private.');
        }

        $query = Room::with(['facilities', 'bookings' => function ($q) {
            $q->where('status', 'Disetujui')
              ->whereDate('start_datetime', today())
              ->orderBy('start_datetime');
        }])->whereNull('deleted_at');

        // Filter (sama seperti public, tapi hanya untuk room yang terlihat oleh user)
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->boolean('booking_today')) {
            $query->whereHas('bookings', function ($q) {
                $q->where('status', 'Disetujui')
                  ->whereDate('start_datetime', today());
            });
        }

        // Ambil semua, lalu filter berdasarkan role di server
        $rooms = $query->get()->filter(function ($room) use ($user) {
            return $this->roomVisibleForUser($room, $user);
        })->values();

        $floors = Room::whereNotNull('floor')->distinct()->pluck('floor')->sort()->values();
        $categories = Room::whereNotNull('category')->distinct()->pluck('category')->sort()->values();

        return view('owner.rooms.room_private', [
            'rooms'      => $rooms,
            'floors'     => $floors,
            'categories' => $categories,
            'statuses'   => ['Tersedia', 'Sedang Digunakan', 'Dipesan', 'Menunggu Approval', 'Maintenance', 'Ditutup', 'Dinonaktifkan'],
            'conditions' => ['Baik', 'Perlu Perbaikan', 'Rusak', 'Renovasi', 'Bersih'],
            'user'       => $user,
        ]);
    }

    // ============================================================
    // 4. DETAIL PRIVATE ROOM
    // ============================================================
    public function privateShow(Request $request, Room $room)
{
    $user = $request->user();
    $images = is_array($room->images) ? $room->images : [];
    
    if (!$user || !$this->userCanAccessPrivateRoom($user)) {
        Log::warning('Unauthorized private detail access', [
            'user_id' => $user?->id,
            'room_id' => $room->id,
            'ip'      => $request->ip(),
        ]);
        return redirect('/')->with('error', 'Akses ditolak.');
    }

    if (!$this->roomVisibleForUser($room, $user)) {
        return redirect('/')->with('error', 'Anda tidak memiliki izin melihat ruangan ini.');
    }

    // ✅ Load semua relasi yang dibutuhkan
    $room->load([
        'facilities',
        'bookings' => function ($q) {
            $q->where('status', 'Disetujui')
              ->orderBy('start_datetime', 'desc')
              ->limit(20);
        },
        'schedules' => function ($q) {
            $q->orderBy('start_datetime');
        }
    ]);

    // ===== HITUNG STATUS LIVE =====
    $now = now();
    $currentBooking = $room->bookings()
        ->where('status', 'Disetujui')
        ->where('start_datetime', '<=', $now)
        ->where('end_datetime', '>=', $now)
        ->first();

    $nextBooking = $room->bookings()
        ->where('status', 'Disetujui')
        ->where('start_datetime', '>', $now)
        ->orderBy('start_datetime')
        ->first();

    if ($room->condition && in_array(strtolower($room->condition), [
        'renovated', 'sedang direnovasi', 'dalam perbaikan', 'under repair', 'under construction'
    ])) {
        $liveStatus = $room->condition;
    } elseif ($currentBooking) {
        $liveStatus = 'Sedang Digunakan';
    } elseif ($nextBooking) {
        $liveStatus = 'Dipesan';
    } else {
        $liveStatus = 'Tersedia';
    }

    return view('owner.rooms.room_private_show', compact('room', 'liveStatus'));
}

    // ============================================================
    // 5. AUTHORIZATION HELPERS
    // ============================================================

    /**
     * Cek apakah user secara umum boleh mengakses private room.
     */
   private function userCanAccessPrivateRoom(User $user): bool
{
    if (!$user->is_active) return false;
    if ($user->locked_until && now()->lt($user->locked_until)) return false;

    $roleName = strtolower(optional($user->level)->name);
    return in_array($roleName, $this->allowedRoles, true);
}

private function roomVisibleForUser(Room $room, User $user): bool
{
    if (!in_array($room->public_status, ['login', 'both'], true)) {
        return false;
    }

    $roleName = strtolower(optional($user->level)->name);

    if ($roleName === 'owner') {
        return true;
    }

    if (!$room->public_roles) {
        return false;
    }

    $roles = is_array($room->public_roles)
        ? $room->public_roles
        : json_decode($room->public_roles, true);

    if (is_array($roles)) {
        $roles = array_map('strtolower', $roles);
    }

    return is_array($roles) && in_array($roleName, $roles, true);
}

    /**
     * Payload aman untuk publik (hanya field yang diizinkan).
     */
    private function publicRoomPayload(Room $room): array
    {
        return [
            'id'            => $room->id,
            'name'          => $room->name,
            'room_code'     => $room->room_code,
            'building'      => $room->building,
            'floor'         => $room->floor,
            'category'      => $room->category,
            'capacity'      => $room->capacity_normal,
            'status'        => $room->status,
            'condition'     => $room->condition,
            'images'        => $room->images ?? [],
            'facilities'    => $room->facilities->map(fn($f) => [
                'name'     => $f->name,
                'quantity' => $f->quantity,
            ])->values(),
            'operational_hours' => $room->operational_hours ?? [],
        ];
    }
}
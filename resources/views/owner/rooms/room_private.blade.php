@extends('layouts.roles.owner')

@section('title', 'Room Private – UNICODE')

@section('content')
<div class="p-3 md:p-6 space-y-6">

    {{-- HERO --}}
    <div class="rounded-3xl overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-cyan-800 text-white shadow-xl">
        <div class="p-6 md:p-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-xs font-bold mb-4">
                        <i class="fas fa-lock"></i> Private Room
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black">Selamat Datang di Halaman Room</h1>
                    <p class="mt-3 text-white/70 max-w-2xl">
                        Silakan scan QR atau masukkan NIM untuk melihat ruangan yang sedang berlangsung sesuai hak akses akun Anda.
                    </p>
                </div>
                <div class="shrink-0">
                    <div class="px-5 py-4 rounded-2xl bg-white/10 border border-white/10">
                        <p class="text-xs text-white/50">Login sebagai</p>
                        <p class="font-black">{{ optional($user->level)->name ?? 'User' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-4 md:p-5">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
            <select name="floor" class="rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                <option value="">Semua Lantai</option>
                @foreach($floors as $floor)
                    <option value="{{ $floor }}" @selected(request('floor') === $floor)>{{ $floor }}</option>
                @endforeach
            </select>
            <select name="category" class="rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                <option value="">Semua Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <select name="condition" class="rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                <option value="">Semua Kondisi</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition }}" @selected(request('condition') === $condition)>{{ $condition }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 px-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-sm">
                <input type="checkbox" name="booking_today" value="1" @checked(request('booking_today'))>
                <span><i class="fas fa-calendar-day text-cyan-500"></i> Booking hari ini</span>
            </label>
            <button class="rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold transition">
                <i class="fas fa-filter mr-2"></i> Terapkan
            </button>
        </form>
    </div>

    {{-- ROOM CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-5">
        @forelse($rooms as $room)
            @php
                $images = is_array($room->images) ? $room->images : [];
                $image = $images[0] ?? null;
                $todayBooking = $room->bookings->first();
                $allowedRoles = $room->public_roles;
                if (is_string($allowedRoles)) $allowedRoles = json_decode($allowedRoles, true);
            @endphp

            <article class="room-private-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition cursor-pointer" data-room-id="{{ $room->id }}">
                {{-- IMAGE --}}
                <div class="relative">
                    @if($image)
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $room->name }}" class="w-full h-40 md:h-52 object-cover">
                    @else
                        <div class="h-40 md:h-52 bg-gradient-to-br from-cyan-100 to-blue-100 dark:from-cyan-900/20 dark:to-blue-900/20 flex items-center justify-center">
                            <i class="fas fa-door-open text-5xl text-cyan-500"></i>
                        </div>
                    @endif
                    <div class="absolute top-3 left-3">
                        @switch($room->status)
                            @case('Tersedia') <span class="px-2 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold">🟢 Tersedia</span> @break
                            @case('Sedang Digunakan') <span class="px-2 py-1 rounded-full bg-blue-600 text-white text-[10px] font-bold">🔵 Sedang Digunakan</span> @break
                            @case('Dipesan') <span class="px-2 py-1 rounded-full bg-yellow-500 text-white text-[10px] font-bold">🟡 Dipesan</span> @break
                            @default <span class="px-2 py-1 rounded-full bg-gray-700 text-white text-[10px] font-bold">{{ $room->status }}</span>
                        @endswitch
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h2 class="font-black text-sm md:text-lg text-gray-800 dark:text-white truncate">{{ $room->name }}</h2>
                            <p class="font-mono text-[9px] text-gray-400">{{ $room->room_code }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-lg bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 text-[9px] font-bold">{{ $room->category ?: 'Umum' }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-2">
                            <p class="text-[9px] text-gray-400">Kapasitas</p>
                            <p class="font-bold text-xs md:text-sm">{{ $room->capacity_normal }} orang</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-2">
                            <p class="text-[9px] text-gray-400">Lantai</p>
                            <p class="font-bold text-xs md:text-sm truncate">{{ $room->floor }}</p>
                        </div>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="mt-3">
                        <p class="text-[9px] text-gray-400 mb-1">Fasilitas</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($room->facilities->take(4) as $facility)
                                <span class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-[9px] text-gray-600 dark:text-gray-300">{{ $facility->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Jam Operasional --}}
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-clock text-cyan-500"></i>
                            <span>@if(is_array($room->operational_hours)) {{ count($room->operational_hours) }} hari operasional @else Jam operasional tersedia @endif</span>
                        </div>
                    </div>

                    {{-- Booking hari ini --}}
                    <div class="mt-3">
                        @if($todayBooking)
                            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-2.5">
                                <p class="text-[9px] text-blue-600 dark:text-blue-300 font-bold"><i class="fas fa-calendar-check mr-1"></i> BOOKING HARI INI</p>
                                <p class="text-xs font-bold text-blue-700 dark:text-blue-300">{{ $todayBooking->start_datetime->format('H:i') }} – {{ $todayBooking->end_datetime->format('H:i') }}</p>
                            </div>
                        @else
                            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-2.5">
                                <p class="text-[10px] text-emerald-700 dark:text-emerald-300 font-semibold"><i class="fas fa-calendar-check mr-1"></i> Tidak ada booking hari ini</p>
                            </div>
                        @endif
                    </div>

                    {{-- Akses Role --}}
                    <div class="mt-3">
                        <div class="flex items-center gap-2 text-[10px] text-gray-400">
                            <i class="fas fa-user-shield"></i>
                            <span>
                                @if(is_array($allowedRoles) && count($allowedRoles))
                                    {{ implode(', ', $allowedRoles) }}
                                @else
                                    Akses sesuai izin sistem
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        {{-- Tombol Show --}}
                        <a href="{{ route('owner.rooms.private.show', $room) }}" class="show-private-room inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 hover:bg-cyan-600 text-white py-2.5 text-xs font-bold transition">
                            <i class="fas fa-eye"></i> Show
                        </a>

                        {{-- 🔥 TOMBOL BOOKING SEKARANG (URL manual) --}}
                        <a href="{{ url('/owner/rooms/' . $room->id . '/booking/create') }}" 
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white py-2.5 text-xs font-bold transition shadow-md hover:shadow-lg transform hover:scale-[1.02]"
                           title="Ajukan booking untuk ruangan ini">
                            <i class="fas fa-calendar-plus"></i> Booking Sekarang
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-3xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-lock text-3xl text-gray-300"></i>
                </div>
                <h3 class="mt-4 font-black text-lg text-gray-800 dark:text-white">Tidak ada ruangan yang dapat Anda lihat</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar ruangan disaring berdasarkan hak akses akun Anda.</p>
            </div>
        @endforelse
    </div>

</div>

<script>
    document.querySelectorAll('.room-private-card').forEach(card => {
        const showButton = card.querySelector('.show-private-room');
        const bookingButton = card.querySelector('a[href*="booking/create"]');

        // Mobile: sekali sentuh card
        card.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !e.target.closest('a')) {
                showButton?.click();
            }
        });

        // Desktop: double click
        card.addEventListener('dblclick', () => {
            if (window.innerWidth > 768) {
                showButton?.click();
            }
        });
    });
</script>
@endsection
@extends('layouts.roles.owner')

@section('title', 'Semua Ruangan – Owner')

@section('content')
<div class="p-3 md:p-6 space-y-6">

    {{-- ===================== HEADER ===================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-door-open text-cyan-500"></i>
                Semua Ruangan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Kelola seluruh ruangan, fasilitas, kapasitas, jadwal dan status penggunaan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-3 md:mt-0">
    <a href="{{ route('owner.rooms.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-2">
        <i class="fas fa-plus"></i> Tambah Ruangan
    </a>

    {{-- 🔽 TAMBAHKAN INI 🔽 --}}
    <a href="{{ route('owner.rooms.testing') }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-2">
        <i class="fas fa-qrcode"></i> Testing QR
    </a>
    <a href="{{ route('owner.rooms.public.index') }}" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-2">
        <i class="fas fa-globe"></i> Publikasi
    </a>
            <a href="#"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i> Kalender
            </a>
            <a href="#"
               class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2 relative">
                <i class="fas fa-bell"></i> Permintaan
                @if($pendingBookings > 0)
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">{{ $pendingBookings }}</span>
                @endif
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-file-export"></i> Export
                </button>
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-file-excel text-green-600"></i> Excel
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-file-pdf text-red-600"></i> PDF
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-file-csv text-blue-600"></i> CSV
                    </a>
                </div>
            </div>
            <a href="{{ route('owner.settings') }}"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition flex items-center gap-2">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>

    {{-- ===================== STATISTIK ===================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-9 gap-3 md:gap-4">
        @php
            $statCards = [
                ['label' => 'Total Ruangan', 'value' => $stats['total'], 'icon' => 'fa-building', 'color' => 'blue'],
                ['label' => 'QR Aktif', 'value' => $stats['qr_active'], 'icon' => 'fa-qrcode', 'color' => 'teal'],
                ['label' => 'QR Tidak Aktif', 'value' => $stats['qr_inactive'], 'icon' => 'fa-qrcode-slash', 'color' => 'gray'],
                ['label' => 'Tersedia', 'value' => $stats['available'], 'icon' => 'fa-check-circle', 'color' => 'emerald'],
                ['label' => 'Sedang Digunakan', 'value' => $stats['in_use'], 'icon' => 'fa-clock', 'color' => 'blue'],
                ['label' => 'Maintenance', 'value' => $stats['maintenance'], 'icon' => 'fa-tools', 'color' => 'orange'],
                ['label' => 'Menunggu Approval', 'value' => $pendingBookings, 'icon' => 'fa-hourglass-half', 'color' => 'purple'],
                ['label' => 'Tutup', 'value' => $stats['closed'], 'icon' => 'fa-clock', 'color' => 'gray', 'sub' => 'Di luar jam'],
                ['label' => 'Overtime', 'value' => $stats['overtime'], 'icon' => 'fa-exclamation-triangle', 'color' => 'red', 'sub' => '⚠️ Digunakan di luar jam'],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 border border-gray-200 dark:border-gray-700 transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 truncate">{{ $card['label'] }}</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $card['value'] }}</p>
                        @if(isset($card['sub']))
                            <p class="text-[9px] text-gray-400 truncate">{{ $card['sub'] }}</p>
                        @endif
                    </div>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                        @if($card['color'] == 'blue') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                        @elseif($card['color'] == 'teal') bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400
                        @elseif($card['color'] == 'gray') bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                        @elseif($card['color'] == 'emerald') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400
                        @elseif($card['color'] == 'orange') bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400
                        @elseif($card['color'] == 'purple') bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                        @elseif($card['color'] == 'red') bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400
                        @endif
                    ">
                        <i class="fas {{ $card['icon'] }} text-sm"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===================== FILTER ===================== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('owner.rooms.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">🔍 Cari Ruangan</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Kode, nama, gedung..."
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500">
                    <option value="">Semua Status</option>
                    @foreach(['Tersedia', 'Sedang Digunakan', 'Dipesan', 'Menunggu Approval', 'Maintenance', 'Ditutup', 'Dinonaktifkan'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Gedung</label>
                <select name="building" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building }}" {{ request('building') == $building ? 'selected' : '' }}>{{ $building }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lantai</label>
                <select name="floor" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500">
                    <option value="">Semua Lantai</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>{{ $floor }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kapasitas ≥</label>
                <input type="number" name="min_capacity" value="{{ request('min_capacity') }}" placeholder="0"
                       class="w-24 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('owner.rooms.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ===================== BULK ACTION ===================== --}}
    <form id="bulkActionForm" action="{{ route('owner.rooms.bulk-action') }}" method="POST">
        @csrf
       <div class="flex flex-wrap items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-700">
    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aksi Massal:</span>
    <button type="submit" name="action" value="export" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
        <i class="fas fa-file-export"></i> Export Terpilih
    </button>
    <button type="submit" name="action" value="generate-qr" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
        <i class="fas fa-qrcode"></i> Generate QR
    </button>

    {{-- 🔽 TAMBAHKAN INI 🔽 --}}
    <button type="submit" name="action" value="publish" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
        <i class="fas fa-globe"></i> Publikasikan
    </button>

    <button type="submit" name="action" value="delete" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2"
            onclick="return confirm('Arsipkan ruangan terpilih?')">
        <i class="fas fa-archive"></i> Arsipkan
    </button>
    <span id="selectedCount" class="text-sm text-gray-500 ml-auto">0 ruangan dipilih</span>
</div>

        {{-- ===================== TABEL ===================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden border border-gray-200 dark:border-gray-700 mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-3 py-3 w-10">
                                <input type="checkbox" id="selectAll"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-3 py-3 min-w-[140px]">Ruangan</th>
                            <th class="px-3 py-3 min-w-[100px]">Lokasi</th>
                            <th class="px-3 py-3 text-center w-20">Kapasitas</th>
                            <th class="px-3 py-3 min-w-[120px]">Fasilitas</th>
                            <th class="px-3 py-3 w-28">Kondisi</th>
                            <th class="px-3 py-3 w-32">Status</th>
                            <th class="px-3 py-3 text-center w-16">QR</th>
                            <th class="px-3 py-3 text-center w-28">Booking Hari Ini</th>
                            <th class="px-3 py-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($rooms as $room)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition cursor-pointer" data-href="{{ route('owner.rooms.show', $room) }}">
                            {{-- Checkbox --}}
                            <td class="px-3 py-3">
                                <input type="checkbox" name="selected_rooms[]" value="{{ $room->id }}"
                                       class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>

                            {{-- Ruangan --}}
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 dark:from-cyan-900/30 dark:to-blue-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400 flex-shrink-0">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white text-sm truncate max-w-[120px]">{{ $room->name }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">{{ $room->room_code }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Lokasi --}}
                            <td class="px-3 py-3">
                                <p class="text-sm truncate max-w-[100px]">{{ $room->building ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500">Lt. {{ $room->floor ?? '-' }}</p>
                            </td>

                            {{-- Kapasitas --}}
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-users text-gray-400 text-xs"></i>
                                    {{ $room->capacity_normal }}
                                </span>
                                @if($room->capacity_max && $room->capacity_max > $room->capacity_normal)
                                    <span class="text-[9px] text-gray-400 block">max {{ $room->capacity_max }}</span>
                                @endif
                            </td>

                            {{-- Fasilitas --}}
                            <td class="px-3 py-3">
                                @php
                                    $facilities = $room->facilities->take(3);
                                    $remaining = $room->facilities->count() - 3;
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach($facilities as $facility)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full text-[9px] text-gray-600 dark:text-gray-300">
                                            {{ $facility->name }}
                                            @if($facility->quantity > 1) ×{{ $facility->quantity }} @endif
                                        </span>
                                    @endforeach
                                    @if($remaining > 0)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full text-[9px] text-gray-400">+{{ $remaining }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Kondisi --}}
                            <td class="px-3 py-3">
                                <span class="px-2 py-1 rounded-full text-[10px] font-medium
                                    {{ $room->condition == 'Baik' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                    {{ $room->condition == 'Perlu Perbaikan' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $room->condition == 'Rusak' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                ">
                                    {{ $room->condition }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-3">
                                <span class="px-2 py-1 rounded-full text-[10px] font-medium whitespace-nowrap
                                    @switch($room->status)
                                        @case('Tersedia') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @break
                                        @case('Sedang Digunakan') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @break
                                        @case('Dipesan') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 @break
                                        @case('Menunggu Approval') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 @break
                                        @case('Maintenance') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 @break
                                        @case('Ditutup') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @break
                                        @case('Dinonaktifkan') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    @switch($room->status)
                                        @case('Tersedia') 🟢 @break
                                        @case('Sedang Digunakan') 🔵 @break
                                        @case('Dipesan') 🟡 @break
                                        @case('Menunggu Approval') 🟣 @break
                                        @case('Maintenance') 🟠 @break
                                        @case('Ditutup') ⚪ @break
                                        @case('Dinonaktifkan') 🔴 @break
                                    @endswitch
                                    {{ $room->status }}
                                </span>
                            </td>

                           {{-- QR --}}
<td class="px-3 py-3 text-center">
    @if($room->has_qr && $room->qr_code && Storage::disk('public')->exists($room->qr_code))
        <img src="{{ asset('storage/' . $room->qr_code) }}" alt="QR" class="w-10 h-10 mx-auto object-cover rounded">
    @else
        <span class="text-xs text-gray-400">-</span>
    @endif
</td>

                            {{-- Booking Hari Ini --}}
                            <td class="px-3 py-3 text-center">
                                @php
                                    $todayBookings = $room->bookings()
                                        ->whereDate('start_datetime', today())
                                        ->where('status', 'Disetujui')
                                        ->count();
                                @endphp
                                @if($todayBookings > 0)
                                    <span class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 dark:text-blue-400">
                                        {{ $todayBookings }}
                                        <span class="text-[9px] font-normal text-gray-400">jadwal</span>
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-1" x-data="{ open: false }">
                                    <a href="{{ route('owner.rooms.show', $room) }}"
                                       class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition"
                                       title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('owner.rooms.edit', $room) }}"
                                       class="p-1.5 text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded transition"
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button @click="open = !open"
                                            class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition"
                                            title="Lainnya">
                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                    </button>

                                    {{-- Dropdown --}}
                                    <div x-show="open" @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden origin-top-right"
                                         style="position: fixed; transform: translateX(-90%);">
                                        <div class="py-1">
                                            <a href="{{ route('owner.rooms.show', $room) }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-info-circle w-5 text-blue-500"></i> Lihat Detail
                                            </a>
                                            <a href="{{ route('owner.rooms.edit', $room) }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-edit w-5 text-yellow-500"></i> Edit Ruangan
                                            </a>
                                            <a href="{{ route('owner.rooms.facilities', $room) }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-couch w-5 text-purple-500"></i> Kelola Fasilitas
                                            </a>
                                            <a href="{{ route('owner.rooms.schedules', $room) }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-calendar-check w-5 text-green-500"></i> Lihat Jadwal
                                            </a>
                                            <a href="{{ url('/owner/rooms/' . $room->id . '/booking/create') }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-book w-5 text-cyan-500"></i> Buat Booking
                                            </a>
                                            <a href="#"
                                               onclick="event.preventDefault(); document.getElementById('generate-qr-form-{{ $room->id }}').submit();"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-qrcode w-5 text-teal-500"></i> Generate QR
                                            </a>
                                            <form id="generate-qr-form-{{ $room->id }}"
                                                  action="{{ route('owner.rooms.generate-qr', $room) }}"
                                                  method="POST" style="display:none;">
                                                @csrf
                                            </form>
                                            <a href="{{ route('owner.rooms.maintenance', $room) }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-tools w-5 text-orange-500"></i> Maintenance
                                            </a>
                                            <a href="{{ route('owner.rooms.history', $room) }}"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-history w-5 text-gray-500"></i> Riwayat
                                            </a>
                                            <div class="border-t border-gray-200 dark:border-gray-700"></div>
                                            <a href="#"
                                               onclick="event.preventDefault(); if(confirm('Arsipkan ruangan ini?')) document.getElementById('archive-form-{{ $room->id }}').submit();"
                                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fas fa-archive w-5 text-gray-500"></i> Arsipkan
                                            </a>
                                            <form id="archive-form-{{ $room->id }}"
                                                  action="{{ route('owner.rooms.destroy', $room) }}"
                                                  method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-door-open text-5xl mb-4 block opacity-30"></i>
                                <p class="text-base">Belum ada ruangan.</p>
                                <a href="{{ route('owner.rooms.create') }}" class="text-cyan-600 hover:underline text-sm mt-2 inline-block">
                                    Tambah ruangan sekarang
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $rooms->appends(request()->query())->links() }}
            </div>
        </div>
    </form>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ======================== CHECKBOX SELECT ALL & COUNTER ========================
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const selectedCount = document.getElementById('selectedCount');

        function updateCounter() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            selectedCount.textContent = checked + ' ruangan dipilih';
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateCounter();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateCounter();
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                if (selectAll) selectAll.checked = allChecked;
            });
        });

        updateCounter();

        // ======================== FLASH MESSAGES AUTO HIDE ========================
        setTimeout(() => {
            document.querySelectorAll('.flash-message').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    });
    // ======================== KLIK BARIS UNTUK MENUJU DETAIL ========================
document.querySelectorAll('tbody tr').forEach(row => {
    // Pastikan hanya baris yang memiliki data-href
    if (row.dataset.href) {
        row.addEventListener('click', function(e) {
            // Jangan jika klik di dalam elemen yang memiliki aksi sendiri (tombol, checkbox, link)
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('input[type="checkbox"]')) {
                return;
            }
            window.location.href = this.dataset.href;
        });
    }
});
</script>

@endsection
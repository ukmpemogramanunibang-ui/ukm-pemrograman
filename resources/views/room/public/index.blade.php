@extends('layouts.public') {{-- sesuaikan layout publik kamu --}}

@section('title', 'Portal Ruangan Kampus')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-slate-900 py-10">
    <div class="container mx-auto px-4">

        {{-- ================ HERO ================ --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-800 dark:text-white mb-3">
                <i class="fas fa-door-open text-cyan-500 mr-2"></i>
                Ruangan Kampus
            </h1>
            <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Lihat ketersediaan dan lakukan <strong>booking ruangan</strong> untuk kegiatan akademik maupun organisasi.
            </p>
        </div>

        {{-- ================ FILTER ================ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4 md:p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔎 Cari ruangan..."
                       class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                              bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white
                              focus:ring-2 focus:ring-cyan-500 focus:border-transparent">

                <select name="building" class="px-4 py-2.5 rounded-lg border ...">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b }}" @selected(request('building') == $b)>{{ $b }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-4 py-2.5 rounded-lg border ...">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" @selected(request('status') == 'Tersedia')>🟢 Tersedia</option>
                    <option value="Sedang Digunakan" @selected(request('status') == 'Sedang Digunakan')>🔵 Dipakai</option>
                    <option value="Maintenance" @selected(request('status') == 'Maintenance')>🟠 Maintenance</option>
                </select>

                <button class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-lg transition">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
            </form>
        </div>

        {{-- ================ GRID RUANGAN ================ --}}
        @if($rooms->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($rooms as $room)
                    @php
                        $isAvailable = $room->status === 'Tersedia';
                        $statusColor = match($room->status) {
                            'Tersedia'          => 'bg-green-600',
                            'Sedang Digunakan'  => 'bg-blue-600',
                            'Maintenance'       => 'bg-orange-500',
                            default             => 'bg-gray-500',
                        };
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1">

                        {{-- Header hijau dengan kode ruangan --}}
                        <div class="bg-green-600 text-white text-center py-3 font-bold text-lg tracking-wider">
                            {{ $room->room_code }}
                        </div>

                        {{-- Foto --}}
                        <div class="h-48 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <img src="{{ $room->photo ? asset('storage/'.$room->photo) : asset('images/room-placeholder.jpg') }}"
                                 alt="{{ $room->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        </div>

                        {{-- Info --}}
                        <div class="p-4 text-center">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $room->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-users mr-1"></i>{{ $room->capacity_normal }} orang
                                &nbsp;•&nbsp;
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $room->building ?? '-' }}
                            </p>

                            <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-bold
                                         {{ $isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $isAvailable ? '🟢 AVAILABLE' : '🔴 '.strtoupper($room->status) }}
                            </span>
                        </div>

                        {{-- Tombol Detail & Pinjam --}}
                        <div class="flex border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('room.public.show', $room) }}"
                               class="flex-1 text-center py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold transition">
                                <i class="fas fa-info-circle mr-1"></i> Detail
                            </a>
                            @if($isAvailable)
                                <a href="{{ route('room.public.booking.form', $room) }}"
                                   class="flex-1 text-center py-3 bg-green-500 hover:bg-green-600 text-white font-semibold transition">
                                    <i class="fas fa-bookmark mr-1"></i> Pinjam
                                </a>
                            @else
                                <span class="flex-1 text-center py-3 bg-gray-300 text-gray-600 font-semibold cursor-not-allowed">
                                    <i class="fas fa-ban mr-1"></i> Tidak Tersedia
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $rooms->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-16 text-gray-500">
                <i class="fas fa-door-closed text-6xl mb-4 opacity-30"></i>
                <p class="text-lg">Belum ada ruangan yang tersedia.</p>
            </div>
        @endif
    </div>
</div>
@endsection
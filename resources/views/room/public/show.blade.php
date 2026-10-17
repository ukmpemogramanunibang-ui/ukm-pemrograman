@extends('layouts.public')

@section('title', $room->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10">
    <div class="container mx-auto px-4 max-w-5xl">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('room.public.index') }}" class="hover:text-cyan-600">
                <i class="fas fa-door-open mr-1"></i> Ruangan
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ $room->name }}</span>
        </nav>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="grid md:grid-cols-2 gap-0">

                {{-- Foto --}}
                <div class="h-72 md:h-auto bg-gray-100 dark:bg-gray-700">
                    <img src="{{ $room->photo ? asset('storage/'.$room->photo) : asset('images/room-placeholder.jpg') }}"
                         alt="{{ $room->name }}"
                         class="w-full h-full object-cover">
                </div>

                {{-- Informasi --}}
                <div class="p-6 md:p-8">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">
                        {{ $room->name }}
                    </h1>
                    <p class="text-sm text-gray-500 font-mono mb-4">{{ $room->room_code }}</p>

                    <table class="w-full text-sm">
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-gray-500">Gedung</td>
                            <td class="py-2 text-right font-medium text-gray-800 dark:text-white">{{ $room->building ?? '-' }}</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-gray-500">Lantai</td>
                            <td class="py-2 text-right font-medium text-gray-800 dark:text-white">{{ $room->floor ?? '-' }}</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-gray-500">Kapasitas</td>
                            <td class="py-2 text-right font-medium text-gray-800 dark:text-white">{{ $room->capacity_normal }} orang</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-gray-500">Kondisi</td>
                            <td class="py-2 text-right font-medium text-gray-800 dark:text-white">{{ $room->condition }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-500">Status</td>
                            <td class="py-2 text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $room->status === 'Tersedia' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $room->status }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    {{-- Fasilitas --}}
                    <h3 class="mt-6 font-bold text-gray-800 dark:text-white mb-2">Fasilitas</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($room->facilities as $f)
                            <span class="px-3 py-1 bg-cyan-50 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300 rounded-full text-xs">
                                ✓ {{ $f->name }} @if($f->quantity > 1) ×{{ $f->quantity }} @endif
                            </span>
                        @empty
                            <span class="text-sm text-gray-400">Belum ada data fasilitas.</span>
                        @endforelse
                    </div>

                    @if($room->status === 'Tersedia')
                        <a href="{{ route('room.public.booking.form', $room) }}"
                           class="mt-6 block text-center py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg">
                            <i class="fas fa-calendar-plus mr-2"></i> BOOKING RUANGAN
                        </a>
                    @else
                        <div class="mt-6 text-center py-3 bg-gray-200 text-gray-500 font-bold rounded-xl">
                            <i class="fas fa-ban mr-2"></i> Ruangan tidak tersedia
                        </div>
                    @endif
                </div>
            </div>

            {{-- Jadwal Hari Ini --}}
            <div class="border-t dark:border-gray-700 p-6 md:p-8">
                <h3 class="font-bold text-gray-800 dark:text-white mb-3">
                    <i class="fas fa-clock text-cyan-500 mr-1"></i> Jadwal Hari Ini
                </h3>
                @if($todayBookings->count())
                    <ul class="space-y-2">
                        @foreach($todayBookings as $b)
                            <li class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="font-mono text-sm text-blue-800 dark:text-blue-300">
                                    {{ $b->start_datetime->format('H:i') }} – {{ $b->end_datetime->format('H:i') }}
                                </span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $b->purpose }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">Tidak ada jadwal hari ini.</p>
                @endif
            </div>

            {{-- Riwayat Penggunaan --}}
            <div class="border-t dark:border-gray-700 p-6 md:p-8">
                <h3 class="font-bold text-gray-800 dark:text-white mb-3">
                    <i class="fas fa-history text-cyan-500 mr-1"></i> Riwayat Penggunaan
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500">
                            <tr>
                                <th class="px-3 py-2 text-left">No</th>
                                <th class="px-3 py-2 text-left">Pemesan</th>
                                <th class="px-3 py-2 text-left">Keperluan</th>
                                <th class="px-3 py-2 text-left">Waktu</th>
                                <th class="px-3 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $i => $h)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-3 py-2">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 capitalize">{{ $h->pemesan }}</td>
                                    <td class="px-3 py-2">{{ $h->keperluan }}</td>
                                    <td class="px-3 py-2 font-mono text-xs">
                                        {{ $h->mulai->format('d M H:i') }} – {{ $h->selesai->format('H:i') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                            {{ strtoupper($h->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-6 text-gray-400">Belum ada riwayat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
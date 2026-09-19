@extends('layouts.roles.owner')

@section('title', 'Riwayat – ' . $room->name)

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-history text-gray-500"></i>
                Riwayat – {{ $room->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: {{ $room->room_code }}</p>
        </div>
        <a href="{{ route('owner.rooms.show', $room) }}" class="mt-3 md:mt-0 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold mb-4">📋 Daftar Booking</h2>
        @if($room->bookings->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-2">Judul</th>
                            <th class="px-4 py-2">Pemesan</th>
                            <th class="px-4 py-2">Mulai</th>
                            <th class="px-4 py-2">Selesai</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($room->bookings as $booking)
                            <tr>
                                <td class="px-4 py-2">{{ $booking->title ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $booking->requester_name }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->end_datetime)->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        @if($booking->status == 'Disetujui') bg-green-100 text-green-800
                                        @elseif($booking->status == 'Menunggu Persetujuan') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif
                                    ">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-400 text-center py-4">Belum ada booking untuk ruangan ini.</p>
        @endif
    </div>
</div>
@endsection
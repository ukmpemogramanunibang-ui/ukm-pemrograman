@extends('layouts.roles.owner')

@section('title', 'Jadwal – ' . $room->name)

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-calendar-alt text-green-500"></i>
                Jadwal – {{ $room->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: {{ $room->room_code }}</p>
        </div>
        <a href="{{ route('owner.rooms.show', $room) }}" class="mt-3 md:mt-0 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        @if($room->schedules->count())
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($room->schedules as $schedule)
                    <li class="py-3">
                        <div class="flex justify-between">
                            <span class="font-medium">{{ $schedule->title }}</span>
                            <span class="text-sm text-gray-500">{{ $schedule->activity_type }}</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($schedule->start_datetime)->format('d/m/Y H:i') }}
                            – {{ \Carbon\Carbon::parse($schedule->end_datetime)->format('H:i') }}
                            @if($schedule->status) <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ $schedule->status == 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $schedule->status }}</span> @endif
                        </div>
                        @if($schedule->description)
                            <p class="text-xs text-gray-400 mt-1">{{ $schedule->description }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-400 text-center py-4">Belum ada jadwal.</p>
        @endif
    </div>
</div>
@endsection
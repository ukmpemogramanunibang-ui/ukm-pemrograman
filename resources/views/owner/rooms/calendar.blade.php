@extends('layouts.roles.owner')

@section('title', 'Kalender Ruangan – Owner')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-calendar-alt text-cyan-500"></i>
            Kalender Ruangan
        </h1>
        <a href="{{ route('owner.rooms.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4">
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                <div>Sen</div>
                <div>Sel</div>
                <div>Rab</div>
                <div>Kam</div>
                <div>Jum</div>
                <div>Sab</div>
                <div>Min</div>
            </div>
            <div class="grid grid-cols-7 gap-1 mt-2">
                @php
                    $firstDay = now()->startOfMonth()->startOfWeek('monday');
                    $lastDay = now()->endOfMonth()->endOfWeek('sunday');
                @endphp
                @for($day = $firstDay; $day <= $lastDay; $day->addDay())
                    @php
                        $isCurrentMonth = $day->month == now()->month;
                        $dateKey = $day->format('Y-m-d');
                        $dayBookings = $bookings->get($dateKey, collect());
                    @endphp
                    <div class="min-h-20 p-1 border border-gray-200 dark:border-gray-700 rounded-lg {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900/30' }}">
                        <span class="text-sm font-semibold {{ $isCurrentMonth ? 'text-gray-800 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $day->format('d') }}
                        </span>
                        @if($dayBookings->isNotEmpty())
                            <div class="mt-1 space-y-0.5">
                                @foreach($dayBookings->take(2) as $booking)
                                    <div class="text-[10px] bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-1 rounded truncate">
                                        {{ $booking->room->name }} ({{ $booking->start_datetime->format('H:i') }})
                                    </div>
                                @endforeach
                                @if($dayBookings->count() > 2)
                                    <div class="text-[10px] text-gray-400">+{{ $dayBookings->count() - 2 }} lagi</div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection
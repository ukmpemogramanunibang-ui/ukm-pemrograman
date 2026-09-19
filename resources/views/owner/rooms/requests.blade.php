@extends('layouts.roles.owner')

@section('title', 'Permintaan Booking – Owner')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-bell text-yellow-500"></i>
            Permintaan Booking
        </h1>
        <a href="{{ route('owner.rooms.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-4 py-3 rounded shadow-sm mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($pendingBookings->isEmpty())
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-check-circle text-4xl mb-3 block text-green-500"></i>
                <p class="text-sm">Tidak ada permintaan booking yang menunggu persetujuan.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($pendingBookings as $booking)
                    <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $booking->title }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-door-open mr-1"></i> {{ $booking->room->name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-user mr-1"></i> {{ $booking->requester_name }}
                                @if($booking->requester_phone) · {{ $booking->requester_phone }} @endif
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $booking->start_datetime->format('d M Y H:i') }} – {{ $booking->end_datetime->format('H:i') }}
                            </p>
                            <p class="text-xs text-gray-400">Diajukan {{ $booking->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('owner.rooms.requests.approve', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition" onclick="return confirm('Setujui booking ini?')">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('owner.rooms.requests.reject', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition" onclick="return confirm('Tolak booking ini?')">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $pendingBookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
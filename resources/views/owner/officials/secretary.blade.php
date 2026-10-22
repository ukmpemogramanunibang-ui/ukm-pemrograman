@extends('layouts.roles.' . auth()->user()->getRoleLayout())
@section('title', 'Dashboard Sekretaris')
@section('page-title', 'Dashboard Sekretaris')
@section('content')

{{-- Statistik Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white p-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-80">Total Anggota</p>
                <p class="text-2xl font-bold">{{ $totalMembers ?? 0 }}</p>
            </div>
            <i class="fas fa-users text-3xl opacity-50"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-700 text-white p-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-80">Total Pengurus</p>
                <p class="text-2xl font-bold">{{ $totalOfficials ?? 0 }}</p>
            </div>
            <i class="fas fa-user-tie text-3xl opacity-50"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 text-white p-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-80">Total Ruangan</p>
                <p class="text-2xl font-bold">{{ $totalRooms ?? 0 }}</p>
            </div>
            <i class="fas fa-door-open text-3xl opacity-50"></i>
        </div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-700 text-white p-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-80">Pemesanan Hari Ini</p>
                <p class="text-2xl font-bold">{{ $totalBookingsToday ?? 0 }}</p>
            </div>
            <i class="fas fa-calendar-check text-3xl opacity-50"></i>
        </div>
    </div>
</div>

{{-- Statistik Tambahan + Notifikasi --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Persetujuan</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $pendingCount ?? 0 }}</p>
        </div>
        <i class="fas fa-clock text-3xl text-red-400 opacity-60"></i>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Event Aktif</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalEvents ?? 0 }}</p>
        </div>
        <i class="fas fa-calendar-alt text-3xl text-blue-400 opacity-60"></i>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Notifikasi Belum Dibaca</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $unreadNotifications ?? 0 }}</p>
        </div>
        <i class="fas fa-bell text-3xl text-yellow-400 opacity-60"></i>
    </div>
</div>

{{-- Dua Kolom: Permintaan Pending & Event Terdekat --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Permintaan Pending --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-lg flex items-center">
                <i class="fas fa-hourglass-half text-orange-500 mr-2"></i> Permintaan Booking Menunggu
            </h4>
            <a href="#" class="text-sm text-blue-500 hover:underline">Lihat semua</a> {{-- SEMENTARA PAKAI # --}}
        </div>
        @if(isset($pendingRequests) && $pendingRequests->count())
            <ul class="space-y-3">
                @foreach($pendingRequests as $booking)
                    <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="truncate">{{ $booking->title }}</span>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">
                            {{ $booking->start_datetime->format('d M H:i') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 dark:text-gray-400">Tidak ada permintaan pending.</p>
        @endif
    </div>

    {{-- Event Terdekat --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-lg flex items-center">
                <i class="fas fa-calendar-check text-green-500 mr-2"></i> Event Terdekat
            </h4>
            <a href="#" class="text-sm text-blue-500 hover:underline">Lihat semua</a> {{-- SEMENTARA PAKAI # --}}
        </div>
        @if(isset($upcomingEvents) && $upcomingEvents->count())
            <ul class="space-y-3">
                @foreach($upcomingEvents as $event)
                    <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="font-medium">{{ $event->name }}</span>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                            {{ Carbon\Carbon::parse($event->start_date)->format('d M') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 dark:text-gray-400">Tidak ada event mendatang.</p>
        @endif
    </div>
</div>

{{-- Tugas Mendatang & Event Terbaru --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-lg flex items-center">
                <i class="fas fa-tasks text-blue-500 mr-2"></i> Tugas Mendatang
            </h4>
            <a href="#" class="text-sm text-blue-500 hover:underline">Lihat semua</a>
        </div>
        @if(isset($upcomingTasks) && $upcomingTasks->count())
            <ul class="space-y-3">
                @foreach($upcomingTasks as $task)
                    <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span>{{ $task->title }}</span>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                            {{ $task->deadline->isToday() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $task->deadline->diffForHumans() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Tidak ada tugas mendatang.</p>
        @endif
    </div>

    {{-- Event Terbaru --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-lg flex items-center">
                <i class="fas fa-newspaper text-purple-500 mr-2"></i> Event Terbaru
            </h4>
            <a href="#" class="text-sm text-blue-500 hover:underline">Lihat semua</a> {{-- SEMENTARA PAKAI # --}}
        </div>
        @if(isset($recentEvents) && $recentEvents->count())
            <ul class="space-y-3">
                @foreach($recentEvents as $event)
                    <li class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span>{{ $event->name }}</span>
                        <span class="text-xs text-gray-500">{{ $event->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Belum ada event.</p>
        @endif
    </div>
</div>

{{-- Quick Access / Drive Sekretaris --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('owner.officials.secretary.incoming-mails.index') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition text-center group">
        <i class="fas fa-inbox text-3xl text-blue-500 group-hover:scale-110 transition"></i>
        <p class="mt-2 text-sm font-medium">Surat Masuk</p>
    </a>
    <a href="{{ route('owner.officials.secretary.outgoing-mails.index') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition text-center group">
        <i class="fas fa-paper-plane text-3xl text-green-500 group-hover:scale-110 transition"></i>
        <p class="mt-2 text-sm font-medium">Surat Keluar</p>
    </a>
    <a href="{{ route('owner.officials.secretary.minutes.index') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition text-center group">
        <i class="fas fa-sticky-note text-3xl text-yellow-500 group-hover:scale-110 transition"></i>
        <p class="mt-2 text-sm font-medium">Notulen</p>
    </a>
    <a href="{{ route('owner.officials.secretary.archives.index') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition text-center group">
        <i class="fas fa-archive text-3xl text-purple-500 group-hover:scale-110 transition"></i>
        <p class="mt-2 text-sm font-medium">Arsip</p>
    </a>
</div>

{{-- Footer Info --}}
<div class="mt-6 text-xs text-gray-400 dark:text-gray-500 text-center border-t border-gray-200 dark:border-gray-700 pt-4">
    Data diperbaharui secara real-time • {{ now()->format('d F Y H:i') }}
</div>

@endsection
@extends('layouts.roles.ketua')

@section('title', 'Ketua Dashboard – UNICODE')
@section('page-title', 'Ketua Dashboard')

@section('content')
@php
    if (!isset($stats)) {
        $stats = [
            'total_anggota' => \App\Models\User::where('level_id', 5)->count(),
            'total_pengurus' => \App\Models\User::where('level_id', 4)->count(),
            'total_event' => \App\Models\Event::count(),
            'total_poin' => \App\Models\PointHistory::where('status', 'approved')->sum('points'),
        ];
    }
    if (!isset($activities)) {
        $activities = [];
    }
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Anggota</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_anggota'] }}</p>
                <span class="text-[8px] md:text-xs text-purple-500">Level Anggota</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fas fa-users text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Pengurus</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_pengurus'] }}</p>
                <span class="text-[8px] md:text-xs text-green-500">Level Pengurus</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-green-600 dark:text-green-400">
                <i class="fas fa-user-tie text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Event</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_event'] }}</p>
                <span class="text-[8px] md:text-xs text-yellow-500">Event aktif</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                <i class="fas fa-calendar-check text-base md:text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] md:text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Total Poin</p>
                <p class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_poin'], 0, ',', '.') }}</p>
                <span class="text-[8px] md:text-xs text-cyan-500">Poin keseluruhan</span>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                <i class="fas fa-star text-base md:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-4">Aktivitas Terbaru</h3>
        <ul class="space-y-2 md:space-y-3">
            @forelse($activities as $act)
                <li class="flex items-center gap-2 md:gap-3 text-xs md:text-sm">
                    <span class="w-1.5 h-1.5 md:w-2 md:h-2 bg-{{ $act['color'] ?? 'purple' }}-500 rounded-full flex-shrink-0"></span>
                    <span class="text-gray-600 dark:text-gray-300">
                        <strong>{{ $act['user'] }}</strong> {{ $act['action'] }}
                    </span>
                    <span class="ml-auto text-[8px] md:text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $act['time'] }}</span>
                </li>
            @empty
                <li class="text-gray-400 text-sm">Belum ada aktivitas.</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white mb-3 md:mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-2 md:gap-4">
            <a href="{{ route('ketua.manajemen-anggota.index') }}" class="p-3 md:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-center hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                <i class="fas fa-user-plus text-lg md:text-2xl text-blue-600 dark:text-blue-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Manajemen Anggota</p>
            </a>
            <a href="{{ route('ketua.event-kegiatan') }}" class="p-3 md:p-4 bg-green-50 dark:bg-green-900/20 rounded-xl text-center hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                <i class="fas fa-calendar-plus text-lg md:text-2xl text-green-600 dark:text-green-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Event & Kegiatan</p>
            </a>
            <a href="{{ route('ketua.poin-prestasi') }}" class="p-3 md:p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl text-center hover:bg-purple-100 dark:hover:bg-purple-900/40 transition">
                <i class="fas fa-trophy text-lg md:text-2xl text-purple-600 dark:text-purple-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Poin & Prestasi</p>
            </a>
            <a href="{{ route('ketua.broadcast-pengumuman') }}" class="p-3 md:p-4 bg-pink-50 dark:bg-pink-900/20 rounded-xl text-center hover:bg-pink-100 dark:hover:bg-pink-900/40 transition">
                <i class="fas fa-bullhorn text-lg md:text-2xl text-pink-600 dark:text-pink-400"></i>
                <p class="text-[10px] md:text-sm font-medium mt-1 md:mt-2 text-gray-700 dark:text-gray-300">Broadcast</p>
            </a>
        </div>
    </div>
</div>
@endsection
@extends('layouts.roles.pengurus')

@section('title', 'Dashboard Pengurus – UNICODE')
@section('page-title', 'Pengurus Dashboard')

@section('content')
@php
    $user       = $user       ?? auth()->user();
    $stats      = $stats      ?? [];
    $activities = $activities ?? collect();
    $safeRoute  = fn ($name, $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
@endphp

<div class="space-y-5 md:space-y-6">

    {{-- ════════════════ WELCOME BANNER ════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-r from-indigo-500 via-purple-500 to-fuchsia-500
                dark:from-indigo-700 dark:via-purple-700 dark:to-fuchsia-700
                p-5 md:p-7 text-white shadow-2xl">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-white/20 backdrop-blur
                        flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-tie text-2xl md:text-3xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg md:text-2xl font-extrabold drop-shadow-lg truncate">
                    Halo, {{ $user->name }}! 👋
                </h1>
                <p class="text-xs md:text-sm text-white/90 mt-1">
                    Kelola tugas, misi, event, dan kontribusi Anda di UKM UNICODE.
                </p>
                @if ($user->jabatans->count())
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach ($user->jabatans->take(3) as $j)
                            <span class="px-2 py-0.5 rounded-full bg-white/20 backdrop-blur text-[10px] font-bold border border-white/20">
                                <i class="fas fa-briefcase mr-1"></i>{{ $j->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════ STAT CARDS ════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        @php
            $statCards = [
                ['label' => 'Tugas Saya',  'value' => $stats['total_tugas']   ?? 0, 'icon' => 'fa-tasks',          'color' => 'indigo', 'sub' => 'Aktif'],
                ['label' => 'Misi',        'value' => $stats['total_misi']    ?? 0, 'icon' => 'fa-flag',           'color' => 'purple', 'sub' => 'Dalam proses'],
                ['label' => 'Event',       'value' => $stats['total_event']   ?? 0, 'icon' => 'fa-calendar-check', 'color' => 'pink',   'sub' => 'Diikuti'],
                ['label' => 'Anggota UKM', 'value' => $stats['total_anggota'] ?? 0, 'icon' => 'fa-users',          'color' => 'green',  'sub' => 'Terdaftar'],
            ];
        @endphp
        @foreach ($statCards as $s)
            <div class="relative overflow-hidden rounded-2xl p-4 md:p-5
                        bg-white dark:bg-gray-800
                        border border-gray-200 dark:border-gray-700
                        shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider truncate">
                            {{ $s['label'] }}
                        </p>
                        <p class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mt-1">
                            {{ number_format($s['value']) }}
                        </p>
                        <p class="text-[10px] md:text-xs text-{{ $s['color'] }}-500 mt-0.5">{{ $s['sub'] }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl flex-shrink-0
                                bg-{{ $s['color'] }}-100 dark:bg-{{ $s['color'] }}-900/30
                                text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400
                                flex items-center justify-center">
                        <i class="fas {{ $s['icon'] }} text-base md:text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ════════════════ AKTIVITAS + AKSI CEPAT ════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5">

        {{-- Aktivitas --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> Aktivitas Terbaru
                </h3>
                <span class="text-[10px] text-gray-400">{{ $activities->count() }} aktivitas</span>
            </div>
            <ul class="space-y-2.5">
                @forelse ($activities as $act)
                    <li class="flex items-center gap-3 text-xs md:text-sm py-2 px-3 rounded-lg
                               hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                        <span class="text-gray-600 dark:text-gray-300 truncate flex-1">
                            <strong>{{ data_get($act, 'user') }}</strong> {{ data_get($act, 'action') }}
                        </span>
                        <span class="text-[10px] text-gray-400 flex-shrink-0">{{ data_get($act, 'time') }}</span>
                    </li>
                @empty
                    <li class="text-gray-400 text-sm text-center py-6">
                        <i class="fas fa-inbox text-2xl block mb-1"></i>
                        Belum ada aktivitas
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Aksi Cepat --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i> Aksi Cepat
            </h3>
            <div class="grid grid-cols-2 gap-3">
                @php
                    $actions = [
                        ['route' => 'pengurus.tugas',      'icon' => 'fa-tasks',          'label' => 'Tugas Saya',  'color' => 'blue'],
                        ['route' => 'pengurus.misi',       'icon' => 'fa-flag',           'label' => 'Misi',        'color' => 'purple'],
                        ['route' => 'pengurus.pengumuman', 'icon' => 'fa-bullhorn',       'label' => 'Pengumuman',  'color' => 'green'],
                        ['route' => 'pengurus.data-diri',  'icon' => 'fa-user-circle',    'label' => 'Data Diri',   'color' => 'pink'],
                    ];
                @endphp
                @foreach ($actions as $a)
                    <a href="{{ $safeRoute($a['route']) }}"
                       class="flex flex-col items-center gap-2 p-4 rounded-xl
                              bg-{{ $a['color'] }}-50 dark:bg-{{ $a['color'] }}-900/20
                              hover:bg-{{ $a['color'] }}-100 dark:hover:bg-{{ $a['color'] }}-900/30
                              transition group">
                        <div class="w-10 h-10 rounded-full bg-{{ $a['color'] }}-500 text-white
                                    flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas {{ $a['icon'] }}"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center leading-tight">
                            {{ $a['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ════════════════ FOLDER SHORTCUT ════════════════ --}}
    @include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 dark:text-gray-500
                border-t border-gray-200 dark:border-gray-700 pt-4
                flex flex-col sm:flex-row items-center justify-between gap-2">
        <p>“Kolaborasi adalah kunci kesuksesan.” — UNICODE</p>
        <span class="flex items-center gap-2">
            <i class="fas fa-user-tie text-indigo-400"></i> Pengurus · {{ date('Y') }}
        </span>
    </div>
</div>
@include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])

@endsection
@extends('layouts.roles.superadmin')

@section('title', 'Dashboard Superadmin – UNICODE')
@section('page-title', 'Superadmin Dashboard')

@section('content')
@php
    $user       = $user       ?? auth()->user();
    $stats      = $stats      ?? [];
    $activities = $activities ?? collect();
    $progress   = $progress   ?? [];
    $safeRoute  = fn ($name, $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
@endphp

<div class="space-y-5 md:space-y-6 w-full">

    {{-- ════════════════ WELCOME BANNER ════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-purple-500 via-indigo-500 to-blue-600
                dark:from-purple-700 dark:via-indigo-700 dark:to-blue-800
                p-5 md:p-7 shadow-2xl text-white">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-16 -bottom-16 w-52 h-52 bg-white/5 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-extrabold drop-shadow-lg">
                        👋 Halo, {{ $user->name ?? 'Superadmin' }}!
                    </h1>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full
                                 text-[11px] font-bold uppercase border border-white/20">
                        🛡️ Superadmin
                    </span>
                </div>
                <p class="text-xs md:text-sm text-white/90 mt-2 max-w-2xl">
                    Panel kontrol untuk pengumuman, laporan, kontak, dan notifikasi organisasi UNICODE.
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-[11px] font-semibold border border-white/20">
                        <i class="fas fa-envelope mr-1"></i> {{ $user->email ?? '-' }}
                    </span>
                    @if (!empty($user->nim))
                        <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-[11px] font-semibold border border-white/20">
                            <i class="fas fa-id-card mr-1"></i> NIM: {{ $user->nim }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="hidden lg:block text-6xl opacity-20 select-none">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    {{-- ════════════════ STAT CARDS ════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        @php
            $statCards = [
                ['label' => 'Total Users',   'value' => $stats['total_users']    ?? 0, 'icon' => 'fa-users',         'color' => 'purple', 'sub' => 'Semua role'],
                ['label' => 'Pengumuman',    'value' => $stats['total_announce'] ?? 0, 'icon' => 'fa-bullhorn',      'color' => 'indigo', 'sub' => 'Aktif'],
                ['label' => 'Laporan',       'value' => $stats['total_reports']  ?? 0, 'icon' => 'fa-chart-line',    'color' => 'cyan',   'sub' => 'Bulan ini'],
                ['label' => 'Notifikasi',    'value' => $stats['total_notifs']   ?? 0, 'icon' => 'fa-bell',          'color' => 'amber',  'sub' => 'Belum dibaca'],
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
                        <p class="text-[10px] md:text-xs text-{{ $s['color'] }}-500 mt-0.5">
                            {{ $s['sub'] }}
                        </p>
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

    {{-- ════════════════ PROGRESS + ACTIVITY GRID ════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5">

        {{-- Progress bars --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-simple text-purple-500"></i> Ringkasan Kinerja
            </h3>
            @php
                $bars = [
                    ['label' => 'User Aktif',        'value' => $progress['active_users']    ?? 0, 'color' => 'green'],
                    ['label' => 'Pengumuman Bulan',  'value' => $progress['announce_month']  ?? 0, 'color' => 'purple'],
                    ['label' => 'Laporan Ready',     'value' => $progress['report_ready']    ?? 0, 'color' => 'cyan'],
                    ['label' => 'Coverage Kontak',   'value' => $progress['contact_coverage'] ?? 0, 'color' => 'amber'],
                ];
            @endphp
            <div class="space-y-3">
                @foreach ($bars as $bar)
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                            <span>{{ $bar['label'] }}</span>
                            <span class="font-bold">{{ $bar['value'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $bar['color'] }}-500 rounded-full transition-all duration-700"
                                 style="width: {{ $bar['value'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Aktivitas --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
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
                        <span class="w-2 h-2 rounded-full bg-purple-500 flex-shrink-0"></span>
                        <span class="text-gray-600 dark:text-gray-300 truncate flex-1">
                            <strong>{{ $act->user }}</strong> {{ $act->action }}
                        </span>
                        <span class="text-[10px] text-gray-400 flex-shrink-0">{{ $act->time }}</span>
                    </li>
                @empty
                    <li class="text-gray-400 text-sm text-center py-6">
                        <i class="fas fa-inbox text-2xl block mb-1"></i>
                        Belum ada aktivitas
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ════════════════ AKSI CEPAT ════════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-amber-500"></i> Aksi Cepat
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @php
                $actions = [
                    ['route' => 'superadmin.announcements.create', 'icon' => 'fa-plus',         'label' => 'Buat Pengumuman', 'color' => 'purple'],
                    ['route' => 'superadmin.reports.analysis',     'icon' => 'fa-chart-line',   'label' => 'Lihat Laporan',   'color' => 'indigo'],
                    ['route' => 'superadmin.contacts.all',         'icon' => 'fa-address-book', 'label' => 'Kelola Kontak',   'color' => 'cyan'],
                    ['route' => 'superadmin.notifications',        'icon' => 'fa-bell',         'label' => 'Notifikasi',      'color' => 'amber'],
                ];
            @endphp
            @foreach ($actions as $a)
                <a href="{{ $safeRoute($a['route']) }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl
                          bg-{{ $a['color'] }}-50 dark:bg-{{ $a['color'] }}-900/20
                          hover:bg-{{ $a['color'] }}-100 dark:hover:bg-{{ $a['color'] }}-900/30
                          border border-{{ $a['color'] }}-200 dark:border-{{ $a['color'] }}-800/50
                          transition group">
                    <div class="w-10 h-10 rounded-full bg-{{ $a['color'] }}-500 text-white
                                flex items-center justify-center
                                group-hover:scale-110 transition-transform">
                        <i class="fas {{ $a['icon'] }}"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center leading-tight">
                        {{ $a['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ════════════════ FOLDER SHORTCUT ════════════════ --}}
    @include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 dark:text-gray-500
                border-t border-gray-200 dark:border-gray-700 pt-4
                flex flex-col sm:flex-row items-center justify-between gap-2">
        <p>“Kepemimpinan adalah pelayanan.” — UNICODE</p>
        <span class="flex items-center gap-2">
            <i class="fas fa-user-shield text-purple-400"></i> Superadmin · {{ date('Y') }}
        </span>
    </div>
</div>
@endsection
@extends('layouts.roles.ketua')
@section('title', 'Laporan & Analitik – UNICODE')
@section('page-title', 'Laporan & Analitik')

@include('partials.roles.ketua.sidebar-aware')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;

    $totalAnggota   = \App\Models\User::where('level_id', 5)->whereNull('deleted_at')->count();
    $totalPengurus  = \App\Models\User::where('level_id', 4)->whereNull('deleted_at')->count();
    $totalEvent     = class_exists(\App\Models\Event::class)
                        ? \App\Models\Event::count()
                        : 0;
    $anggotaBulanIni = \App\Models\User::where('level_id', 5)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count();
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    <div>
        <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
            Reports & Analytics
        </p>
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
            <i class="fas fa-chart-line text-purple-500 mr-2"></i>Laporan & Analitik
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Overview data dan analitik organisasi.
        </p>
    </div>

    {{-- KPI GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Total Anggota',    'value' => $totalAnggota,    'icon' => 'fa-users',       'color' => 'purple'],
            ['label' => 'Total Pengurus',   'value' => $totalPengurus,   'icon' => 'fa-user-tie',    'color' => 'cyan'],
            ['label' => 'Total Event',      'value' => $totalEvent,      'icon' => 'fa-calendar',    'color' => 'amber'],
            ['label' => 'Anggota Baru',     'value' => $anggotaBulanIni, 'icon' => 'fa-user-plus',   'color' => 'emerald'],
        ] as $s)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">{{ $s['label'] }}</p>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($s['value']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-{{ $s['color'] }}-500/15 text-{{ $s['color'] }}-500
                                flex items-center justify-center">
                        <i class="fas {{ $s['icon'] }} text-lg"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- REPORT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php
            $reports = [
                ['label' => 'Laporan Keuangan',  'desc' => 'Ringkasan pemasukan & pengeluaran',  'icon' => 'fa-file-invoice-dollar', 'color' => 'emerald', 'route' => 'owner.financial.reports.index'],
                ['label' => 'Laporan Kehadiran',  'desc' => 'Rekap presensi & kehadiran',        'icon' => 'fa-fingerprint',         'color' => 'purple',  'route' => 'owner.attendance.recap'],
                ['label' => 'Laporan Anggota',    'desc' => 'Statistik & demografi anggota',     'icon' => 'fa-users',               'color' => 'cyan',    'route' => 'owner.management.dashboard'],
                ['label' => 'Laporan Event',      'desc' => 'Rekap kegiatan & event',            'icon' => 'fa-calendar-check',      'color' => 'amber',   'route' => 'owner.reports.analysis'],
            ];
        @endphp

        @foreach ($reports as $r)
            <a href="{{ $safeRoute($r['route']) }}"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                      p-6 shadow-sm hover:shadow-md hover:border-purple-400/40 transition group">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-{{ $r['color'] }}-500/15 text-{{ $r['color'] }}-500
                                flex items-center justify-center flex-shrink-0
                                group-hover:scale-110 transition">
                        <i class="fas {{ $r['icon'] }} text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-base text-gray-900 dark:text-white">{{ $r['label'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $r['desc'] }}</p>
                        <span class="inline-flex items-center gap-1 mt-3 text-xs font-semibold
                                     text-purple-600 dark:text-purple-400 group-hover:gap-2 transition-all">
                            Buka Laporan <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
</div>
@endsection
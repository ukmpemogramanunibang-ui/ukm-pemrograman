@extends('layouts.roles.anggota-umum')

@section('title', 'Dashboard Anggota Umum – UNICODE')
@section('breadcrumb', 'Dashboard')
@section('breadcrumb-sub', 'Selamat datang, anggota umum')

@section('content')
@php
    $user = $exploreTarget ?? auth()->user();

    $stats = [
        ['label' => 'Poin Saya',     'value' => number_format($totalPoin ?? 0), 'sub' => 'Kumpulkan terus!',   'icon' => 'fa-medal',           'accent' => 'amber'],
        ['label' => 'Event Diikuti', 'value' => $totalEvent ?? 0,               'sub' => 'Total keikutsertaan', 'icon' => 'fa-calendar-check',  'accent' => 'purple'],
        ['label' => 'Presensi',      'value' => $totalPresensi ?? 0,            'sub' => 'Kehadiran tercatat',  'icon' => 'fa-clipboard-check', 'accent' => 'emerald'],
        ['label' => 'Sertifikat',    'value' => $totalSertifikat ?? 0,          'sub' => 'Koleksi sertifikat',  'icon' => 'fa-certificate',     'accent' => 'cyan'],
    ];

    $quickActions = [
        ['route' => 'anggota.event',      'icon' => 'fa-calendar-check', 'label' => 'Lihat Event',   'accent' => 'purple'],
        ['route' => 'anggota.pengumuman', 'icon' => 'fa-bullhorn',       'label' => 'Pengumuman',    'accent' => 'cyan'],
        ['route' => 'anggota.jadwal',     'icon' => 'fa-calendar-alt',   'label' => 'Jadwal',        'accent' => 'sky'],
        ['route' => 'anggota.pengaturan', 'icon' => 'fa-cog',            'label' => 'Pengaturan',    'accent' => 'slate'],
    ];
@endphp

<div class="space-y-6 w-full animate-fade-in-up">

    {{-- ═══ HERO ═══ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-slate-500 via-slate-600 to-slate-800
                p-6 md:p-8 shadow-2xl text-white group">

        <div class="absolute inset-0 opacity-15"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="absolute -right-24 -top-24 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse-slow"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight leading-tight">
                        👋 Halo, {{ $user->name ?? 'Anggota Umum' }}!
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1
                                 bg-emerald-400/30 backdrop-blur rounded-full
                                 text-xs font-bold border border-emerald-300/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Online
                    </span>
                </div>
                <p class="text-sm md:text-base text-white/90 mt-2 max-w-2xl">
                    Selamat datang di area anggota umum. Ikuti event dan kumpulkan poin untuk membuka lebih banyak fitur!
                </p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                 bg-white/15 backdrop-blur rounded-full text-xs font-bold
                                 border border-white/20">
                        <i class="fas fa-user text-[10px]"></i>
                        Anggota Umum
                    </span>
                    @if(!empty($user->nim))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                     bg-white/15 backdrop-blur rounded-full text-xs font-bold
                                     border border-white/20">
                            <i class="fas fa-id-card text-[10px]"></i>
                            NIM: {{ $user->nim }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="hidden lg:block text-7xl opacity-20 select-none
                        group-hover:opacity-30 group-hover:scale-110 group-hover:rotate-6
                        transition-all duration-500">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </div>

    {{-- ═══ INFO BERGABUNG ═══ --}}
    <div class="rounded-2xl p-4 md:p-5
                bg-gradient-to-r from-cyan-50 to-blue-50
                dark:from-cyan-900/20 dark:to-blue-900/20
                border border-cyan-200 dark:border-cyan-800
                flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl flex-shrink-0
                    bg-cyan-500 text-white flex items-center justify-center shadow-md">
            <i class="fas fa-circle-info text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-cyan-900 dark:text-cyan-200 text-sm">
                Ingin bergabung menjadi Anggota UKM?
            </p>
            <p class="text-cyan-800/80 dark:text-cyan-300/80 text-xs mt-1 leading-relaxed">
                Hubungi pengurus untuk mendaftarkan Anda ke kelompok atau divisi.
                Setelah disetujui, Anda akan otomatis mendapatkan akses ke menu <strong>Kelompok</strong>
                dan <strong>Divisi</strong>.
            </p>
        </div>
    </div>

    {{-- ═══ STATS ═══ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($stats as $s)
            <div class="group relative overflow-hidden rounded-2xl p-5
                        bg-white/70 dark:bg-gray-900/60 backdrop-blur-xl
                        border border-gray-200 dark:border-gray-800
                        shadow-xl hover:shadow-2xl hover:-translate-y-1
                        hover:border-{{ $s['accent'] }}-400/50
                        transition-all duration-300">

                <div class="absolute top-0 right-0 w-24 h-24 rounded-full blur-2xl
                            bg-{{ $s['accent'] }}-500/0 group-hover:bg-{{ $s['accent'] }}-500/10
                            transition-all duration-500"></div>

                <div class="relative flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest
                                  text-gray-400 dark:text-gray-500 truncate">
                            {{ $s['label'] }}
                        </p>
                        <h3 class="text-2xl md:text-3xl font-black mt-1
                                   text-gray-900 dark:text-white tabular-nums">
                            {{ $s['value'] }}
                        </h3>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 truncate">
                            {{ $s['sub'] }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex-shrink-0
                                bg-{{ $s['accent'] }}-500/15 text-{{ $s['accent'] }}-500
                                flex items-center justify-center shadow-lg
                                group-hover:scale-110 group-hover:rotate-6
                                transition-transform duration-300">
                        <i class="fas {{ $s['icon'] }} text-lg"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ QUICK ACTIONS ═══ --}}
    <div class="rounded-2xl p-6 bg-white/70 dark:bg-gray-900/60 backdrop-blur-xl
                border border-gray-200 dark:border-gray-800 shadow-xl">
        <h2 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2 mb-4">
            <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700
                         text-white flex items-center justify-center shadow-md">
                <i class="fas fa-bolt text-xs"></i>
            </span>
            Aksi Cepat
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($quickActions as $a)
                @if(\Illuminate\Support\Facades\Route::has($a['route']))
                    <a href="{{ route($a['route']) }}"
                       class="group flex items-center gap-3 p-4 rounded-xl
                              bg-gray-50 dark:bg-gray-800/40
                              hover:bg-{{ $a['accent'] }}-50 dark:hover:bg-{{ $a['accent'] }}-900/20
                              border border-gray-100 dark:border-gray-800
                              hover:border-{{ $a['accent'] }}-300 dark:hover:border-{{ $a['accent'] }}-700
                              transition-all duration-200">
                        <span class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                     bg-{{ $a['accent'] }}-500 text-white shadow-md
                                     group-hover:scale-110 transition-transform">
                            <i class="fas {{ $a['icon'] }} text-sm"></i>
                        </span>
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200 truncate">
                            {{ $a['label'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2
                border-t border-gray-200 dark:border-gray-800 pt-4
                text-[11px] text-gray-400 dark:text-gray-500">
        <p>“Setiap perjalanan dimulai dari satu langkah.” — UNICODE</p>
        <span class="flex items-center gap-1">
            <i class="fas fa-user text-slate-500"></i>
            {{ date('Y') }} Anggota Umum
        </span>
    </div>
</div>

@push('styles')
<style>
    .animate-fade-in-up { animation: fadeInUp .6s ease-out forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px);} to { opacity:1; transform: translateY(0);} }
    .animate-pulse-slow { animation: pulseSlow 3s ease-in-out infinite; }
    @keyframes pulseSlow { 0%,100% { transform: scale(1); opacity: .6;} 50% { transform: scale(1.05); opacity: 1;} }
</style>
@endpush
@endsection
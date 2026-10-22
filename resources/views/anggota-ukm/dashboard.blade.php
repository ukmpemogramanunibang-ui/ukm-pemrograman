@extends('layouts.roles.anggota-ukm')

@section('title', 'Dashboard Anggota – UNICODE')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $user = Auth::user();

    /* ── Statistik (aman jika null) ── */
    $stats = [
        [
            'label'   => 'Poin Saya',
            'value'   => number_format($totalPoin ?? 0),
            'sub'     => 'Peringkat #' . ($rank ?? '-'),
            'icon'    => 'fa-medal',
            'accent'  => 'cyan',
            'route'   => 'anggota.poin',
        ],
        [
            'label'   => 'Event Diikuti',
            'value'   => $totalEvent ?? 0,
            'sub'     => 'Total keikutsertaan',
            'icon'    => 'fa-calendar-check',
            'accent'  => 'purple',
            'route'   => 'anggota.event',
        ],
        [
            'label'   => 'Presensi',
            'value'   => $totalPresensi ?? 0,
            'sub'     => 'Kehadiran tercatat',
            'icon'    => 'fa-clipboard-check',
            'accent'  => 'emerald',
            'route'   => 'anggota.presensi',
        ],
        [
            'label'   => 'Sertifikat',
            'value'   => $totalSertifikat ?? 0,
            'sub'     => 'Koleksi sertifikat',
            'icon'    => 'fa-certificate',
            'accent'  => 'amber',
            'route'   => 'anggota.sertifikat',
        ],
    ];

    /* ── Next milestone poin (contoh: tiap 500 poin) ── */
    $poinSaat   = (int) ($totalPoin ?? 0);
    $step       = 500;
    $nextTarget = (int) (ceil(($poinSaat + 1) / $step) * $step);
    $progress   = $nextTarget > 0 ? min(100, (int) round($poinSaat / $nextTarget * 100)) : 0;
    $sisa       = max(0, $nextTarget - $poinSaat);

    /* ── Quick actions ── */
    $quickActions = [
        ['route' => 'anggota.event',      'icon' => 'fa-calendar-check', 'label' => 'Event',        'accent' => 'purple'],
        ['route' => 'anggota.presensi',   'icon' => 'fa-qrcode',         'label' => 'Presensi',     'accent' => 'emerald'],
        ['route' => 'anggota.pengumuman', 'icon' => 'fa-bullhorn',       'label' => 'Pengumuman',   'accent' => 'cyan'],
        ['route' => 'anggota.leaderpoint','icon' => 'fa-trophy',         'label' => 'Leaderboard',  'accent' => 'amber'],
        ['route' => 'anggota.misi',       'icon' => 'fa-bullseye',       'label' => 'Misi',         'accent' => 'rose'],
        ['route' => 'anggota.sertifikat', 'icon' => 'fa-certificate',    'label' => 'Sertifikat',   'accent' => 'indigo'],
        ['route' => 'anggota.jadwal',     'icon' => 'fa-calendar-alt',   'label' => 'Jadwal',       'accent' => 'sky'],
        ['route' => 'anggota.pengaturan', 'icon' => 'fa-cog',            'label' => 'Pengaturan',   'accent' => 'slate'],
    ];
@endphp

<div class="space-y-6 w-full animate-fade-in-up">

    {{-- ═══ HERO ═══ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-cyan-400 via-blue-500 to-indigo-600
                dark:from-cyan-600 dark:via-blue-700 dark:to-indigo-900
                p-6 md:p-8 shadow-2xl shadow-blue-500/20 text-white group">

        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="absolute -right-24 -top-24 w-72 h-72 bg-white/15 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute -left-24 -bottom-24 w-72 h-72 bg-cyan-300/25 rounded-full blur-3xl animate-pulse-slow"
             style="animation-delay:1.5s"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight leading-tight">
                        👋 Halo, {{ $user->name }}!
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1
                                 bg-emerald-400/30 backdrop-blur rounded-full
                                 text-xs font-bold border border-emerald-300/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Online
                    </span>
                </div>
                <p class="text-sm md:text-base text-white/90 mt-2 max-w-2xl">
                    Selamat datang di dashboard Anda. Tetap semangat berkarya dan berinovasi!
                </p>

                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                 bg-white/15 backdrop-blur rounded-full text-xs font-bold
                                 border border-white/20">
                        <i class="fas fa-user-tag text-[10px]"></i>
                        {{ $user->roles->pluck('name')->implode(', ') ?: ($user->level->name ?? 'Anggota') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                 bg-white/15 backdrop-blur rounded-full text-xs font-bold
                                 border border-white/20">
                        <i class="fas fa-id-card text-[10px]"></i>
                        NIM: {{ $user->nim ?? '-' }}
                    </span>
                    @if($user->jabatans && $user->jabatans->count())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                     bg-white/15 backdrop-blur rounded-full text-xs font-bold
                                     border border-white/20">
                            <i class="fas fa-briefcase text-[10px]"></i>
                            {{ $user->jabatans->pluck('name')->implode(', ') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Progress ring poin --}}
            <div class="hidden lg:flex flex-col items-center gap-2 flex-shrink-0">
                <div class="relative w-28 h-28">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42"
                                fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="10"/>
                        <circle cx="50" cy="50" r="42"
                                fill="none" stroke="white" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 42 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $progress / 100) }}"
                                class="transition-all duration-1000"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black">{{ $progress }}%</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest opacity-80">Progress</span>
                    </div>
                </div>
                <p class="text-[11px] text-white/80 text-center max-w-[140px]">
                    {{ $sisa }} poin lagi ke {{ number_format($nextTarget) }}
                </p>
            </div>
        </div>
    </div>

    {{-- ═══ STATS ═══ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($stats as $s)
            <a href="{{ \Illuminate\Support\Facades\Route::has($s['route']) ? route($s['route']) : '#' }}"
               class="group relative overflow-hidden rounded-2xl p-5
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
            </a>
        @endforeach
    </div>

    {{-- ═══ QUICK ACTIONS ═══ --}}
    <div class="rounded-2xl p-6 bg-white/70 dark:bg-gray-900/60 backdrop-blur-xl
                border border-gray-200 dark:border-gray-800 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600
                             text-white flex items-center justify-center shadow-md">
                    <i class="fas fa-bolt text-xs"></i>
                </span>
                Aksi Cepat
            </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($quickActions as $a)
                @if(\Illuminate\Support\Facades\Route::has($a['route']))
                    <a href="{{ route($a['route']) }}"
                       class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl
                              bg-gray-50 dark:bg-gray-800/40
                              hover:bg-{{ $a['accent'] }}-50 dark:hover:bg-{{ $a['accent'] }}-900/20
                              border border-gray-100 dark:border-gray-800
                              hover:border-{{ $a['accent'] }}-300 dark:hover:border-{{ $a['accent'] }}-700
                              transition-all duration-200">

                        <span class="w-11 h-11 rounded-xl flex items-center justify-center
                                     bg-{{ $a['accent'] }}-500/15 text-{{ $a['accent'] }}-500
                                     group-hover:scale-110 transition-transform duration-200">
                            <i class="fas {{ $a['icon'] }}"></i>
                        </span>
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 text-center
                                     group-hover:text-{{ $a['accent'] }}-600 dark:group-hover:text-{{ $a['accent'] }}-400
                                     transition-colors">
                            {{ $a['label'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ═══ PROFIL + AKTIVITAS ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- PROFIL SINGKAT --}}
        <div class="rounded-2xl p-6 bg-white/70 dark:bg-gray-900/60 backdrop-blur-xl
                    border border-gray-200 dark:border-gray-800 shadow-xl
                    lg:col-span-1 group">

            <h2 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2
                       border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600
                             text-white flex items-center justify-center shadow-md">
                    <i class="fas fa-user-circle text-xs"></i>
                </span>
                Profil Saya
            </h2>

            <div class="space-y-2.5 text-sm">
                @php
                    $rows = [
                        ['label' => 'Nama',    'value' => $user->name],
                        ['label' => 'Email',   'value' => $user->email],
                        ['label' => 'NIM',     'value' => $user->nim ?? '-'],
                        ['label' => 'Level',   'value' => $user->level->name ?? '-'],
                        ['label' => 'Jabatan', 'value' => $user->jabatans->pluck('name')->implode(', ') ?: '-'],
                    ];
                @endphp

                @foreach($rows as $row)
                    <div class="flex justify-between items-center gap-2
                                px-2 py-1.5 rounded-lg
                                hover:bg-gray-50 dark:hover:bg-gray-800/50
                                transition-colors">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ $row['label'] }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white text-xs
                                     truncate max-w-[160px] text-right">
                            {{ $row['value'] }}
                        </span>
                    </div>
                @endforeach

                <div class="flex justify-between items-center gap-2 px-2 py-1.5">
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Status</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5
                                 bg-emerald-100 dark:bg-emerald-900/30
                                 text-emerald-700 dark:text-emerald-400
                                 rounded-full text-[10px] font-bold">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        {{ $user->status->name ?? 'Aktif' }}
                    </span>
                </div>
            </div>

            <a href="{{ route('anggota.profil') }}"
               class="mt-5 inline-flex items-center gap-2 text-cyan-600 dark:text-cyan-400
                      hover:text-cyan-700 dark:hover:text-cyan-300
                      font-bold text-xs transition-all group/link">
                Lihat profil lengkap
                <i class="fas fa-arrow-right text-[10px] transition-transform
                          group-hover/link:translate-x-1"></i>
            </a>
        </div>

        {{-- AKTIVITAS --}}
        <div class="rounded-2xl p-6 bg-white/70 dark:bg-gray-900/60 backdrop-blur-xl
                    border border-gray-200 dark:border-gray-800 shadow-xl lg:col-span-2">

            <h2 class="text-base font-black text-gray-900 dark:text-white flex items-center gap-2
                       border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600
                             text-white flex items-center justify-center shadow-md">
                    <i class="fas fa-history text-xs"></i>
                </span>
                Aktivitas Terbaru
            </h2>

            @if($activities && $activities->count())
                <ul class="space-y-2 max-h-[320px] overflow-y-auto pr-1">
                    @foreach($activities as $act)
                        @php
                            $isPlus = ($act->type ?? 'plus') === 'plus';
                        @endphp
                        <li class="flex items-start gap-3 p-2.5 rounded-xl
                                   hover:bg-gray-50 dark:hover:bg-gray-800/50
                                   transition-colors group/item">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                                         bg-{{ $isPlus ? 'emerald' : 'rose' }}-500/15
                                         text-{{ $isPlus ? 'emerald' : 'rose' }}-500
                                         group-hover/item:scale-110 transition-transform">
                                <i class="fas fa-{{ $isPlus ? 'plus' : 'minus' }} text-xs"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                                    {{ $act->description ?? 'Aktivitas' }}
                                </p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                    <i class="far fa-clock"></i>
                                    {{ optional($act->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black flex-shrink-0
                                         bg-{{ $isPlus ? 'emerald' : 'rose' }}-50
                                         dark:bg-{{ $isPlus ? 'emerald' : 'rose' }}-900/20
                                         text-{{ $isPlus ? 'emerald' : 'rose' }}-600
                                         dark:text-{{ $isPlus ? 'emerald' : 'rose' }}-400">
                                {{ $isPlus ? '+' : '-' }}{{ $act->points ?? 0 }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800
                                flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="font-bold text-gray-500 dark:text-gray-400 text-sm">Belum ada aktivitas</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Mulai ikuti event dan kumpulkan poin!
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2
                border-t border-gray-200 dark:border-gray-800 pt-4
                text-[11px] text-gray-400 dark:text-gray-500">
        <p>“Kode adalah puisi, dan kita adalah penyairnya.” — UNICODE</p>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1"><i class="fas fa-code text-cyan-400"></i> v1.0</span>
            <span class="opacity-40">|</span>
            <span>{{ date('Y') }} UNICODE</span>
        </div>
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

@include('partials.dashboard.builder', ['dashboardData' => $dashboardData ?? []])

@endsection
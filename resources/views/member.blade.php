@extends('layouts.app')

@section('title', 'Anggota – UKM Pemrograman UNICODE')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    :root {
        --neon-cyan: #22d3ee;
        --neon-blue: #3b82f6;
        --neon-violet: #8b5cf6;
        --neon-pink: #ec4899;
    }

    /* =========================================================
       RESPONSIVE SHELL
       ========================================================= */
    .unicode-shell {
        width: min(100% - 1rem, 100%);
        margin-inline: auto;
    }
    @media (min-width: 641px)  { .unicode-shell { width: min(100% - 1.5rem, 1000px); } }
    @media (min-width: 1025px) { .unicode-shell { width: min(100% - 2rem,   1400px); } }
    @media (min-width: 1536px) { .unicode-shell { width: min(100% - 3rem,   1800px); } }
    @media (min-width: 2000px) { .unicode-shell { width: min(100% - 5rem,   2100px); } }

    /* =========================================================
       REVEAL
       ========================================================= */
    .unicode-reveal {
        opacity: 0;
        transform: translateY(18px);
        transition: opacity .5s cubic-bezier(.22,.61,.36,1),
                    transform .5s cubic-bezier(.22,.61,.36,1);
        will-change: opacity, transform;
    }
    .unicode-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* =========================================================
       HERO
       ========================================================= */
    .unicode-hero {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 32%),
            radial-gradient(circle at bottom left, rgba(124,58,237,.16), transparent 30%),
            linear-gradient(135deg, #020617, #172554 55%, #1d4ed8);
    }
    .unicode-orb {
        position: absolute;
        width: 280px; height: 280px;
        border-radius: 9999px;
        filter: blur(70px);
        opacity: .18;
        pointer-events: none;
    }
    .unicode-orb.one { top: -100px; right: -70px; background: #06b6d4; }
    .unicode-orb.two { bottom: -130px; left: -70px; background: #7c3aed; }

    /* =========================================================
       STAT CARD
       ========================================================= */
    .stat-card {
        position: relative;
        border-radius: 1.25rem;
        padding: 1.25rem 1rem;
        background: rgba(255,255,255,.85);
        border: 1px solid rgba(148,163,184,.2);
        box-shadow: 0 8px 24px rgba(15,23,42,.06);
        text-align: center;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 36px rgba(15,23,42,.10);
    }
    .dark .stat-card {
        background: rgba(15,23,42,.75);
        border-color: rgba(148,163,184,.12);
        box-shadow: 0 10px 30px rgba(0,0,0,.25);
    }

    /* =========================================================
       MEMBER CARD
       ========================================================= */
    .member-card {
        position: relative;
        border-radius: 1.5rem;
        padding: 1.5px;
        background: linear-gradient(135deg,
                    rgba(34,211,238,.45),
                    rgba(59,130,246,.28) 45%,
                    rgba(139,92,246,.40) 75%,
                    rgba(236,72,153,.30));
        transition: transform .35s ease, box-shadow .35s ease;
        will-change: transform;
    }
    .member-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 65px -20px rgba(34,211,238,.35);
    }

    .card-inner {
        position: relative;
        border-radius: calc(1.5rem - 1.5px);
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 60%, #0a0f1d 100%);
        padding: 1.6rem 1.25rem 1.35rem;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: .55rem;
        min-height: 380px;
    }

    .card-inner::before {
        content: '';
        position: absolute;
        top: -40%; right: -30%;
        width: 240px; height: 240px;
        background: radial-gradient(circle, rgba(34,211,238,.28), transparent 70%);
        filter: blur(45px);
        pointer-events: none;
        opacity: .85;
        transition: opacity .35s ease;
    }
    .member-card:hover .card-inner::before { opacity: 1; }

    .card-inner::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(148,163,184,.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(148,163,184,.05) 1px, transparent 1px);
        background-size: 28px 28px;
        mask-image: radial-gradient(circle at 50% 30%, black, transparent 75%);
        -webkit-mask-image: radial-gradient(circle at 50% 30%, black, transparent 75%);
        pointer-events: none;
    }

    /* Avatar */
    .avatar-wrap {
        position: relative;
        width: 92px; height: 92px;
        margin: .25rem auto .35rem;
        z-index: 1;
    }
    .avatar-ring {
        position: absolute;
        inset: -5px;
        border-radius: 9999px;
        background: conic-gradient(from 180deg,
            var(--neon-cyan),
            var(--neon-violet),
            var(--neon-pink),
            var(--neon-cyan));
        animation: spin 6s linear infinite;
        opacity: .8;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .avatar-inner {
        position: relative;
        width: 100%; height: 100%;
        border-radius: 9999px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border: 3px solid #0b1220;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800;
        font-size: 1.6rem;
        color: #fff;
        letter-spacing: .5px;
        z-index: 1;
        overflow: hidden;
    }
    .avatar-inner img {
        width: 100%; height: 100%;
        object-fit: cover;
    }

    .verified-mark {
        position: absolute;
        top: 2px; left: 2px;
        width: 22px; height: 22px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #3b82f6, #06b6d4);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        font-weight: 900;
        border: 2px solid #0b1220;
        z-index: 3;
        box-shadow: 0 4px 12px rgba(6,182,212,.55);
    }

    .status-dot {
        position: absolute;
        bottom: 4px; right: 4px;
        width: 15px; height: 15px;
        border-radius: 9999px;
        background: #10b981;
        border: 3px solid #0b1220;
        box-shadow: 0 0 10px rgba(16,185,129,.7);
        z-index: 2;
    }
    .status-dot.inactive {
        background: #64748b;
        box-shadow: 0 0 10px rgba(100,116,139,.5);
    }

    /* Name + handle */
    .member-name {
        font-weight: 800;
        font-size: 1rem;
        line-height: 1.3;
        color: #f8fafc;
        z-index: 1;
        margin-top: .15rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.6em;
    }
    @media (min-width: 1025px) {
        .member-name { font-size: 1.05rem; }
    }
    .member-handle {
        font-size: .72rem;
        color: #64748b;
        z-index: 1;
        margin-top: -.15rem;
    }

    /* Role badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .75rem;
        border-radius: 9999px;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,.35);
        z-index: 1;
    }
    .role-badge .dot {
        width: 6px; height: 6px;
        border-radius: 9999px;
        background: #fff;
        display: inline-block;
    }

    /* Poin */
    .stat-poin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        margin-top: .2rem;
        padding: .45rem .85rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, rgba(34,211,238,.12), rgba(139,92,246,.12));
        border: 1px solid rgba(34,211,238,.25);
        z-index: 1;
    }
    .stat-poin .icon { font-size: .95rem; }
    .stat-poin .value {
        font-weight: 900;
        font-size: .95rem;
        color: #67e8f9;
        letter-spacing: .3px;
    }
    .stat-poin .label {
        font-size: .62rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    /* IG button */
    .ig-btn {
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .55rem 1rem;
        border-radius: 9999px;
        font-size: .78rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        box-shadow: 0 10px 25px -8px rgba(236,72,153,.6);
        transition: transform .2s ease, box-shadow .2s ease;
        z-index: 1;
        width: 100%;
        max-width: 180px;
    }
    .ig-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px rgba(236,72,153,.75);
    }
    .ig-btn svg { width: 14px; height: 14px; }

    /* =========================================================
       FILTER BUTTONS
       ========================================================= */
    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem .9rem;
        border-radius: 9999px;
        font-size: .78rem;
        font-weight: 700;
        background: rgba(148,163,184,.15);
        color: #475569;
        transition: all .2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .dark .filter-btn {
        background: rgba(148,163,184,.12);
        color: #cbd5e1;
    }
    .filter-btn:hover { transform: translateY(-1px); }
    .filter-btn.active {
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        box-shadow: 0 8px 20px -6px rgba(6,182,212,.6);
    }
    .filter-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 9999px;
        background: rgba(255,255,255,.25);
        font-size: .68rem;
        font-weight: 800;
    }
    .filter-btn:not(.active) .filter-count {
        background: rgba(148,163,184,.25);
    }

    /* Search clear */
    .search-clear {
        position: absolute;
        right: .75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 26px; height: 26px;
        border-radius: 9999px;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(148,163,184,.25);
        color: #475569;
        font-size: .8rem;
        cursor: pointer;
        transition: background .2s;
    }
    .search-clear:hover { background: rgba(148,163,184,.4); }
    .search-clear.visible { display: inline-flex; }

    /* =========================================================
       PAGINATION
       ========================================================= */
    .pagination-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: .4rem;
        margin-top: 2.5rem;
    }
    .pagination-info {
        width: 100%;
        text-align: center;
        font-size: .8rem;
        color: #64748b;
        margin-bottom: .75rem;
    }
    .dark .pagination-info { color: #94a3b8; }

    .page-btn {
        min-width: 40px;
        height: 40px;
        padding: 0 .75rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        font-weight: 700;
        background: rgba(148,163,184,.15);
        color: #475569;
        cursor: pointer;
        transition: all .2s ease;
        border: 1px solid transparent;
        user-select: none;
    }
    .dark .page-btn {
        background: rgba(148,163,184,.12);
        color: #cbd5e1;
    }
    .page-btn:hover:not(:disabled):not(.active) {
        background: rgba(148,163,184,.3);
        transform: translateY(-1px);
    }
    .page-btn.active {
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        box-shadow: 0 8px 20px -6px rgba(6,182,212,.6);
    }
    .page-btn:disabled {
        opacity: .4;
        cursor: not-allowed;
    }
    .page-btn.nav {
        background: linear-gradient(135deg, rgba(34,211,238,.15), rgba(139,92,246,.15));
        border-color: rgba(34,211,238,.3);
        color: #06b6d4;
    }
    .dark .page-btn.nav { color: #67e8f9; }
    .page-ellipsis {
        min-width: 32px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-weight: 800;
    }

    /* =========================================================
       MOBILE OPTIMIZATION
       ========================================================= */
    @media (max-width: 640px) {
        .avatar-ring { animation: none; opacity: .4; }
        .card-inner::before { display: none; }
        .card-inner::after { display: none; }
        .member-card { transition: none; }
        .member-card:hover { transform: none; box-shadow: none; }

        /* Card lebih compact supaya 2 kolom muat nyaman */
        .card-inner {
            min-height: 300px;
            padding: 1.15rem .75rem 1rem;
            gap: .4rem;
            border-radius: calc(1.25rem - 1.5px);
        }
        .member-card { border-radius: 1.25rem; }
        .avatar-wrap { width: 68px; height: 68px; margin-bottom: .15rem; }
        .avatar-inner { font-size: 1.15rem; border-width: 2px; }
        .verified-mark { width: 18px; height: 18px; font-size: 9px; }
        .status-dot { width: 13px; height: 13px; border-width: 2px; bottom: 2px; right: 2px; }
        .member-name { font-size: .8rem; min-height: 2.4em; }
        .member-handle { font-size: .65rem; }
        .role-badge { font-size: .58rem; padding: .25rem .55rem; letter-spacing: .03em; }
        .role-badge .dot { width: 5px; height: 5px; }
        .stat-poin { padding: .35rem .6rem; gap: .3rem; }
        .stat-poin .icon { font-size: .8rem; }
        .stat-poin .value { font-size: .8rem; }
        .stat-poin .label { font-size: .55rem; }
        .ig-btn { padding: .45rem .75rem; font-size: .7rem; max-width: 140px; gap: .3rem; }
        .ig-btn svg { width: 12px; height: 12px; }

        /* Pagination lebih kompak */
        .page-btn {
            min-width: 36px;
            height: 36px;
            font-size: .8rem;
            padding: 0 .55rem;
            border-radius: 10px;
        }
        .pagination-wrap { gap: .3rem; margin-top: 1.75rem; }
        .pagination-info { font-size: .72rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .avatar-ring { animation: none; }
        .unicode-reveal {
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush

@section('content')

@php
    $roleMap = [
        2 => ['label' => 'Super Admin',  'grad' => 'linear-gradient(135deg,#f43f5e,#f97316)'],
        3 => ['label' => 'Ketua',        'grad' => 'linear-gradient(135deg,#fbbf24,#f59e0b)'],
        4 => ['label' => 'Pengurus',     'grad' => 'linear-gradient(135deg,#8b5cf6,#d946ef)'],
        5 => ['label' => 'Anggota UKM',  'grad' => 'linear-gradient(135deg,#06b6d4,#3b82f6)'],
        6 => ['label' => 'Anggota Umum', 'grad' => 'linear-gradient(135deg,#64748b,#334155)'],
    ];

    $defaultIg = 'https://www.instagram.com/ukm_programming/';

    $members = $members ?? collect();

    $getInitials = function ($name) {
        $parts = preg_split('/\s+/', trim((string) $name));
        $init  = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $init .= strtoupper(substr($p, 0, 1));
        }
        return $init ?: '?';
    };

    $getRole = function ($member) use ($roleMap) {
        $lvl = (int) ($member->level_id ?? 0);
        return $roleMap[$lvl]
            ?? ['label' => 'Anggota', 'grad' => 'linear-gradient(135deg,#64748b,#334155)'];
    };

    $getAvatar = function ($member) {
        $url = $member->avatar_url ?? null;
        if (empty($url)) return null;

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        if (str_starts_with($url, 'avatars/') || str_starts_with($url, 'storage/')) {
            return asset('storage/' . ltrim($url, '/'));
        }
        return asset(ltrim($url, '/'));
    };

    $getIg = function ($member) use ($defaultIg) {
        $soc = $member->social_media ?? null;

        if (is_string($soc)) {
            $decoded = json_decode($soc, true);
            if (is_array($decoded) && !empty($decoded['instagram'])) {
                return $decoded['instagram'];
            }
        }
        if (is_array($soc) && !empty($soc['instagram'])) return $soc['instagram'];
        if (is_object($soc) && !empty($soc->instagram))   return $soc->instagram;

        return $defaultIg;
    };

    $getPoints = function ($member) {
        if (isset($member->approved_points) && $member->approved_points !== null) {
            return (int) $member->approved_points;
        }
        return (int) ($member->total_points ?? 0);
    };

    $statCounts = [
        'all' => $members->count(),
        '2'   => $members->where('level_id', 2)->count(),
        '3'   => $members->where('level_id', 3)->count(),
        '4'   => $members->where('level_id', 4)->count(),
        '5'   => $members->where('level_id', 5)->count(),
        '6'   => $members->where('level_id', 6)->count(),
    ];
@endphp

<div class="unicode-shell py-5 md:py-8">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="unicode-hero rounded-[2rem] p-6 sm:p-8 md:p-12 lg:p-16 text-white shadow-2xl unicode-reveal">
        <div class="unicode-orb one"></div>
        <div class="unicode-orb two"></div>

        <div class="relative z-10 text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-xs md:text-sm font-bold uppercase tracking-widest">
                Our Team
            </span>

            <h1 class="mt-5 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                Anggota
                <span class="bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 bg-clip-text text-transparent">
                    UNICODE
                </span>
            </h1>

            <p class="mt-5 text-base md:text-lg lg:text-xl text-white/80 leading-relaxed">
                Talenta digital masa depan Universitas Bhakti Asih —
                dari super admin, ketua, pengurus, hingga anggota UKM dan anggota umum.
            </p>
        </div>
    </section>

    {{-- =====================================================
         STATISTIK
    ====================================================== --}}
    <section class="mt-8 md:mt-12 unicode-reveal">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="stat-card">
                <div class="text-3xl">👥</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-slate-900 dark:text-white">{{ $statCounts['all'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total Anggota</p>
            </div>
            <div class="stat-card">
                <div class="text-3xl">🛡️</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-rose-500">{{ $statCounts['2'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Super Admin</p>
            </div>
            <div class="stat-card">
                <div class="text-3xl">👑</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-amber-500">{{ $statCounts['3'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ketua</p>
            </div>
            <div class="stat-card">
                <div class="text-3xl">⚡</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-cyan-500">{{ $statCounts['4'] + $statCounts['5'] + $statCounts['6'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Anggota Aktif</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         SEARCH & FILTER
    ====================================================== --}}
    <section class="mt-8 md:mt-12 unicode-reveal">
        <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">

            <div class="relative w-full lg:w-96">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari nama, username, atau NIM..."
                    autocomplete="off"
                    aria-label="Cari anggota"
                    class="w-full pl-11 pr-11 py-3 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-sm focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition"
                >
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
                <button type="button" id="searchClear" class="search-clear" aria-label="Bersihkan pencarian">✕</button>
            </div>

            <div class="flex flex-wrap gap-2 justify-start lg:justify-end" id="filterBar">
                <button type="button" class="filter-btn active" data-filter="all">
                    Semua <span class="filter-count">{{ $statCounts['all'] }}</span>
                </button>
                <button type="button" class="filter-btn" data-filter="2">
                    Super Admin <span class="filter-count">{{ $statCounts['2'] }}</span>
                </button>
                <button type="button" class="filter-btn" data-filter="3">
                    Ketua <span class="filter-count">{{ $statCounts['3'] }}</span>
                </button>
                <button type="button" class="filter-btn" data-filter="4">
                    Pengurus <span class="filter-count">{{ $statCounts['4'] }}</span>
                </button>
                <button type="button" class="filter-btn" data-filter="5">
                    Anggota UKM <span class="filter-count">{{ $statCounts['5'] }}</span>
                </button>
                <button type="button" class="filter-btn" data-filter="6">
                    Anggota Umum <span class="filter-count">{{ $statCounts['6'] }}</span>
                </button>
            </div>

        </div>
    </section>

    {{-- =====================================================
         GRID ANGGOTA
         2 kolom di HP, 2 tablet, 3 laptop, 4 desktop/TV
    ====================================================== --}}
    <section class="mt-8 md:mt-10 unicode-reveal">

        @if($members->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 p-12 text-center">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Belum ada anggota terdaftar</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Data anggota dengan level superadmin, ketua, pengurus, anggota UKM,
                    dan anggota umum belum tersedia.
                </p>
            </div>
        @else
            <div
                id="memberGrid"
                class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-5"
            >
                @foreach($members as $member)
                    @php
                        $role      = $getRole($member);
                        $initials  = $getInitials($member->name);
                        $avatar    = $getAvatar($member);
                        $ig        = $getIg($member);
                        $points    = $getPoints($member);
                        $isActive  = (bool) ($member->is_active ?? true);
                        $isVerified = (bool) ($member->is_verified ?? false);

                        $searchBlob = strtolower(implode(' ', array_filter([
                            $member->name,
                            $member->username ?? null,
                            $member->nim ?? null,
                        ])));
                    @endphp

                    <article
                        class="member-card unicode-reveal"
                        data-name="{{ $searchBlob }}"
                        data-level="{{ (int) $member->level_id }}"
                    >
                        <div class="card-inner">

                            <div class="avatar-wrap">
                                <div class="avatar-ring"></div>

                                @if($isVerified)
                                    <span class="verified-mark" title="Terverifikasi">✓</span>
                                @endif

                                <div class="avatar-inner">
                                    @if($avatar)
                                        <img
                                            src="{{ $avatar }}"
                                            alt="Avatar {{ $member->name }}"
                                            loading="lazy"
                                            decoding="async"
                                            onerror="this.onerror=null;this.style.display='none';this.parentNode.textContent='{{ $initials }}';"
                                        >
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>

                                <span class="status-dot {{ $isActive ? '' : 'inactive' }}"
                                      title="{{ $isActive ? 'Aktif' : 'Tidak Aktif' }}"></span>
                            </div>

                            <h3 class="member-name">{{ $member->name }}</h3>

                            @if($member->username)
                                <p class="member-handle">@{{ $member->username }}</p>
                            @endif

                            <span class="role-badge" style="background: {{ $role['grad'] }};">
                                <span class="dot"></span>
                                {{ $role['label'] }}
                            </span>

                            <div class="stat-poin" title="Total poin dari aktivitas UKM">
                                <span class="icon">⚡</span>
                                <span class="value">{{ number_format($points, 0, ',', '.') }}</span>
                                <span class="label">Poin</span>
                            </div>

                            <a
                                href="{{ $ig }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ig-btn"
                                aria-label="Instagram {{ $member->name }}"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.5.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .5 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.5 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.5-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.5-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.5-2.2-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.3-1.8.5-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.5 1.3-.1 1.7-.1 4.9-.1zM12 0C8.7 0 8.3 0 7 .1 5.8.2 4.9.4 4.2.7c-.8.3-1.4.7-2.1 1.4C1.4 2.8 1 3.4.7 4.2.4 4.9.2 5.8.1 7 0 8.3 0 8.7 0 12s0 3.7.1 5c.1 1.2.3 2.1.6 2.8.3.8.7 1.4 1.4 2.1.7.7 1.3 1.1 2.1 1.4.7.3 1.6.5 2.8.6 1.3.1 1.7.1 5 .1s3.7 0 5-.1c1.2-.1 2.1-.3 2.8-.6.8-.3 1.4-.7 2.1-1.4.7-.7 1.1-1.3 1.4-2.1.3-.7.5-1.6.6-2.8.1-1.3.1-1.7.1-5s0-3.7-.1-5c-.1-1.2-.3-2.1-.6-2.8-.3-.8-.7-1.4-1.4-2.1-.7-.7-1.3-1.1-2.1-1.4-.7-.3-1.6-.5-2.8-.6C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 100 12.4 6.2 6.2 0 000-12.4zm0 10.2a4 4 0 110-8 4 4 0 010 8zm6.4-10.5a1.4 1.4 0 11-2.8 0 1.4 1.4 0 012.8 0z"/>
                                </svg>
                                Instagram
                            </a>

                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Empty state untuk filter/pencarian --}}
            <div id="emptyState" class="hidden mt-12 text-center">
                <div class="inline-flex flex-col items-center gap-3 px-8 py-10 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40">
                    <div class="text-5xl">🔍</div>
                    <p class="font-semibold text-slate-700 dark:text-slate-200">Tidak ada anggota yang cocok.</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Coba ubah kata kunci atau pilih filter lain.</p>
                </div>
            </div>

            {{-- PAGINATION --}}
            <div id="paginationWrap" class="hidden">
                <div class="pagination-info" id="paginationInfo"></div>
                <div class="pagination-wrap" id="pagination"></div>
            </div>
        @endif

    </section>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
       1. REVEAL ANIMATION
       ===================================================== */
    const reveals = document.querySelectorAll('.unicode-reveal');

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -40px 0px', threshold: 0.06 });

        reveals.forEach(el => io.observe(el));
    } else {
        reveals.forEach(el => el.classList.add('is-visible'));
    }

    /* =====================================================
       2. SEARCH + FILTER + PAGINATION
       ===================================================== */
    const PER_PAGE = 12;

    const searchInput    = document.getElementById('searchInput');
    const searchClear    = document.getElementById('searchClear');
    const filterButtons  = document.querySelectorAll('.filter-btn');
    const memberCards    = Array.from(document.querySelectorAll('#memberGrid .member-card'));
    const emptyState     = document.getElementById('emptyState');
    const paginationWrap = document.getElementById('paginationWrap');
    const paginationEl   = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');

    if (!memberCards.length) return;

    let currentFilter = 'all';
    let searchTerm    = '';
    let currentPage   = 1;

    /* Ambil kartu yang lolos filter + search */
    function getFilteredCards() {
        return memberCards.filter(card => {
            const name  = card.dataset.name || '';
            const level = card.dataset.level || '';

            const matchFilter = currentFilter === 'all' || level === currentFilter;
            const matchSearch = searchTerm === '' || name.includes(searchTerm);

            return matchFilter && matchSearch;
        });
    }

    /* Render halaman */
    function render() {
        const filtered = getFilteredCards();
        const total    = filtered.length;
        const pages    = Math.max(1, Math.ceil(total / PER_PAGE));

        /* Kalau halaman aktif > total halaman, mundur */
        if (currentPage > pages) currentPage = pages;

        const start = (currentPage - 1) * PER_PAGE;
        const end   = start + PER_PAGE;

        /* Sembunyikan semua, tampilkan hanya yang masuk rentang halaman */
        memberCards.forEach(card => card.style.display = 'none');
        filtered.slice(start, end).forEach(card => card.style.display = '');

        /* Empty state */
        if (emptyState) {
            emptyState.classList.toggle('hidden', total > 0);
        }

        /* Pagination */
        if (total <= PER_PAGE) {
            /* Cukup 1 halaman → sembunyikan pagination */
            if (paginationWrap) paginationWrap.classList.add('hidden');
        } else {
            if (paginationWrap) paginationWrap.classList.remove('hidden');
            renderPagination(pages);
        }

        /* Info */
        if (paginationInfo) {
            if (total === 0) {
                paginationInfo.textContent = '';
            } else {
                const showingFrom = start + 1;
                const showingTo   = Math.min(end, total);
                paginationInfo.textContent = `Menampilkan ${showingFrom}–${showingTo} dari ${total} anggota`;
            }
        }

        /* Update reveal visibility supaya kartu yang baru tampil langsung terlihat */
        filtered.slice(start, end).forEach(card => {
            card.classList.add('is-visible');
        });
    }

    /* Bikin tombol pagination dengan pola: 1 2 3 … N */
    function renderPagination(totalPages) {
        if (!paginationEl) return;
        paginationEl.innerHTML = '';

        const makeBtn = (label, page, opts = {}) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'page-btn' + (opts.nav ? ' nav' : '') + (opts.active ? ' active' : '');
            btn.textContent = label;
            if (opts.disabled) btn.disabled = true;
            if (!opts.disabled && !opts.active) {
                btn.addEventListener('click', () => {
                    currentPage = page;
                    render();
                    /* Scroll halus ke atas grid */
                    const grid = document.getElementById('memberGrid');
                    if (grid) {
                        const y = grid.getBoundingClientRect().top + window.scrollY - 100;
                        window.scrollTo({ top: y, behavior: 'smooth' });
                    }
                });
            }
            return btn;
        };

        /* Prev */
        paginationEl.appendChild(
            makeBtn('‹', Math.max(1, currentPage - 1), {
                nav: true,
                disabled: currentPage === 1,
            })
        );

        /* Nomor dengan ellipsis */
        const pages = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            pages.push(1);
            const left  = Math.max(2, currentPage - 1);
            const right = Math.min(totalPages - 1, currentPage + 1);

            if (left > 2) pages.push('…');
            for (let i = left; i <= right; i++) pages.push(i);
            if (right < totalPages - 1) pages.push('…');

            pages.push(totalPages);
        }

        pages.forEach(p => {
            if (p === '…') {
                const span = document.createElement('span');
                span.className = 'page-ellipsis';
                span.textContent = '…';
                paginationEl.appendChild(span);
            } else {
                paginationEl.appendChild(
                    makeBtn(String(p), p, { active: p === currentPage })
                );
            }
        });

        /* Next */
        paginationEl.appendChild(
            makeBtn('›', Math.min(totalPages, currentPage + 1), {
                nav: true,
                disabled: currentPage === totalPages,
            })
        );
    }

    /* Search debounce */
    let searchTimer;
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            searchTerm  = (searchInput.value || '').toLowerCase().trim();
            currentPage = 1;
            if (searchClear) {
                searchClear.classList.toggle('visible', searchTerm.length > 0);
            }
            render();
        }, 120);
    });

    /* Clear */
    searchClear?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        searchTerm = '';
        currentPage = 1;
        searchClear.classList.remove('visible');
        searchInput?.focus();
        render();
    });

    /* Filter */
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter || 'all';
            currentPage = 1;
            render();
        });
    });

    /* Keyboard shortcut */
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && document.activeElement !== searchInput) {
            e.preventDefault();
            searchInput?.focus();
        }
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.blur();
        }
    });

    /* Render pertama */
    render();

});
</script>
@endpush
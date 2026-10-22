@extends('layouts.app')

@section('title', 'Eksplor Profil – UNICODE')

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
       SHELL
       ========================================================= */
    .u-shell { width: min(100% - 1rem, 100%); margin-inline: auto; }
    @media (min-width: 641px)  { .u-shell { width: min(100% - 1.5rem, 1200px); } }
    @media (min-width: 1025px) { .u-shell { width: min(100% - 2rem,   1400px); } }
    @media (min-width: 1536px) { .u-shell { width: min(100% - 3rem,   1700px); } }
    @media (min-width: 2000px) { .u-shell { width: min(100% - 5rem,   1950px); } }

    /* =========================================================
       REVEAL
       ========================================================= */
    .u-reveal {
        opacity: 0;
        transform: translateY(14px);
        transition: opacity .55s cubic-bezier(.22,.61,.36,1),
                    transform .55s cubic-bezier(.22,.61,.36,1);
        will-change: opacity, transform;
    }
    .u-reveal.is-visible { opacity: 1; transform: translateY(0); }

    /* =========================================================
       ICON WRAPPER
       ========================================================= */
    .ico {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1em; height: 1em;
        flex-shrink: 0; line-height: 1;
        vertical-align: middle;
    }
    .ico svg { width: 100%; height: 100%; display: block; }
    .ico-sm { font-size: 13px; }
    .ico-md { font-size: 16px; }
    .ico-lg { font-size: 20px; }

    /* =========================================================
       HERO
       ========================================================= */
    .u-hero {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.18), transparent 32%),
            radial-gradient(circle at bottom left, rgba(139,92,246,.16), transparent 30%),
            linear-gradient(135deg, #020617, #172554 55%, #1d4ed8);
    }
    .u-orb {
        position: absolute;
        width: 240px; height: 240px;
        border-radius: 9999px;
        filter: blur(70px);
        opacity: .18;
        pointer-events: none;
    }
    .u-orb.one { top: -80px; right: -60px; background: #06b6d4; }
    .u-orb.two { bottom: -110px; left: -60px; background: #7c3aed; }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .45rem 1rem;
        border-radius: 9999px;
        background: rgba(34,211,238,.10);
        border: 1px solid rgba(34,211,238,.30);
        color: #67e8f9;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
        line-height: 1;
    }

    /* =========================================================
       SEARCH
       ========================================================= */
    .search-shell {
        position: relative;
        width: 100%;
        max-width: 480px;
        margin-inline: auto;
    }
    .search-input {
        width: 100%;
        padding: .85rem 1.1rem .85rem 2.75rem;
        border-radius: 9999px;
        background: rgba(15,23,42,.75);
        border: 1.5px solid rgba(148,163,184,.2);
        color: #f8fafc;
        font-size: .88rem;
        outline: none;
        transition: border-color .3s cubic-bezier(.22,.61,.36,1),
                    box-shadow .3s cubic-bezier(.22,.61,.36,1),
                    background .3s ease;
    }
    .search-input::placeholder { color: #64748b; }
    .search-input:focus {
        border-color: #22d3ee;
        background: rgba(15,23,42,.95);
        box-shadow: 0 0 0 4px rgba(34,211,238,.15);
    }
    .search-icon {
        position: absolute;
        left: 1rem; top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
    }

    /* =========================================================
       USER CARD — default (mobile-first)
       ========================================================= */
    .user-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 1.1rem;
        padding: 1.25rem 1.35rem;
        border-radius: 1.4rem;
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        border: 1.5px solid rgba(148,163,184,.15);
        text-decoration: none;
        overflow: hidden;
        transition: transform .4s cubic-bezier(.22,.61,.36,1),
                    border-color .35s ease,
                    box-shadow .4s ease;
        will-change: transform;
        isolation: isolate;
        min-height: 96px;
    }
    .user-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1.5px;
        background: linear-gradient(135deg,
                    rgba(34,211,238,.65),
                    rgba(59,130,246,.4) 50%,
                    rgba(139,92,246,.6));
        -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
                mask-composite: exclude;
        opacity: 0;
        transition: opacity .35s ease;
        pointer-events: none;
        z-index: 1;
    }
    .user-card:hover {
        transform: translateY(-6px);
        border-color: transparent;
        box-shadow: 0 28px 60px -22px rgba(34,211,238,.5),
                    0 0 35px -10px rgba(139,92,246,.4);
    }
    .user-card:hover::before { opacity: 1; }

    .user-card::after {
        content: '';
        position: absolute;
        top: -50%; right: -30%;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(34,211,238,.25), transparent 70%);
        filter: blur(45px);
        pointer-events: none;
        opacity: 0;
        transition: opacity .35s ease;
    }
    .user-card:hover::after { opacity: 1; }

    /* Avatar */
    .user-avatar {
        position: relative;
        width: 64px; height: 64px;
        border-radius: 9999px;
        flex-shrink: 0;
        padding: 2.5px;
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
        z-index: 2;
    }
    .user-avatar img {
        width: 100%; height: 100%;
        border-radius: 9999px;
        object-fit: cover;
        display: block;
        border: 2.5px solid #0b1220;
    }
    .user-avatar .initials {
        width: 100%; height: 100%;
        border-radius: 9999px;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border: 2.5px solid #0b1220;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: 1.2rem;
        letter-spacing: .5px;
    }

    /* Status dot dengan pulse */
    .user-status {
        position: absolute;
        bottom: 1px; right: 1px;
        width: 16px; height: 16px;
        border-radius: 9999px;
        background: #10b981;
        border: 3px solid #0b1220;
        box-shadow: 0 0 12px rgba(16,185,129,.85);
        z-index: 3;
    }
    .user-status::after {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 9999px;
        background: #10b981;
        opacity: .6;
        animation: statusPulse 2s ease-out infinite;
        z-index: -1;
    }
    @keyframes statusPulse {
        0%   { transform: scale(1);   opacity: .6; }
        100% { transform: scale(2.2); opacity: 0; }
    }

    /* Info */
    .user-info {
        flex: 1;
        min-width: 0;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: .45rem;
    }
    .user-name {
        font-weight: 800;
        font-size: 1rem;
        line-height: 1.35;
        color: #f8fafc;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }
    .user-role {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-size: .7rem;
        font-weight: 800;
        color: #67e8f9;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        align-self: flex-start;
        padding: .25rem .65rem;
        border-radius: 9999px;
        background: rgba(34,211,238,.10);
        border: 1px solid rgba(34,211,238,.28);
        line-height: 1;
    }
    .user-role .dot {
        width: 6px; height: 6px;
        border-radius: 9999px;
        background: #22d3ee;
        flex-shrink: 0;
        box-shadow: 0 0 8px rgba(34,211,238,.9);
    }

    /* Action */
    .user-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .55rem 1rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        line-height: 1;
        flex-shrink: 0;
        z-index: 2;
        box-shadow: 0 10px 24px -8px rgba(6,182,212,.75);
        transition: transform .3s cubic-bezier(.22,.61,.36,1),
                    box-shadow .3s ease;
    }
    .user-card:hover .user-action {
        transform: scale(1.06);
        box-shadow: 0 14px 32px -8px rgba(6,182,212,.95);
    }

    /* =========================================================
       GRID
       HP: 2 · Tablet: 2 · Laptop: 3 · PC: 3 · TV: 4
       ========================================================= */
    .user-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }
    @media (min-width: 641px)  { .user-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; } }
    @media (min-width: 900px)  { .user-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.15rem; } }
    @media (min-width: 1100px) { .user-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; } }
    @media (min-width: 1536px) { .user-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem; } }
    @media (min-width: 2000px) { .user-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.6rem; } }

    /* =========================================================
       LAPTOP/PC/TV — CARD LEBIH BESAR
       ========================================================= */
    @media (min-width: 1100px) {
        .user-card {
            padding: 1.6rem 1.75rem;
            gap: 1.35rem;
            min-height: 120px;
            border-radius: 1.6rem;
        }
        .user-avatar { width: 78px; height: 78px; padding: 3px; }
        .user-avatar img,
        .user-avatar .initials { border-width: 3px; }
        .user-avatar .initials { font-size: 1.5rem; }
        .user-status { width: 18px; height: 18px; }

        .user-name { font-size: 1.12rem; line-height: 1.4; }
        .user-role { font-size: .74rem; padding: .3rem .75rem; }
        .user-action { padding: .65rem 1.15rem; font-size: .78rem; }
    }

    @media (min-width: 1536px) {
        .user-card { padding: 1.8rem 2rem; gap: 1.5rem; min-height: 132px; }
        .user-avatar { width: 88px; height: 88px; }
        .user-avatar .initials { font-size: 1.65rem; }
        .user-status { width: 20px; height: 20px; border-width: 3.5px; }
        .user-name { font-size: 1.2rem; }
        .user-role { font-size: .78rem; }
        .user-action { padding: .7rem 1.25rem; font-size: .82rem; }
    }

    @media (min-width: 2000px) {
        .user-card { padding: 2rem 2.25rem; gap: 1.65rem; min-height: 145px; }
        .user-avatar { width: 96px; height: 96px; }
        .user-avatar .initials { font-size: 1.8rem; }
        .user-name { font-size: 1.28rem; }
        .user-action { padding: .75rem 1.35rem; font-size: .85rem; }
    }

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
        margin-bottom: .85rem;
    }
    .dark .pagination-info { color: #94a3b8; }

    .page-btn {
        min-width: 42px;
        height: 42px;
        padding: 0 .85rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .88rem;
        font-weight: 700;
        background: rgba(148,163,184,.15);
        color: #475569;
        transition: all .3s cubic-bezier(.22,.61,.36,1);
        border: 1px solid transparent;
        text-decoration: none;
        user-select: none;
    }
    .dark .page-btn {
        background: rgba(148,163,184,.12);
        color: #cbd5e1;
    }
    .page-btn:hover:not(.disabled):not(.active) {
        background: rgba(148,163,184,.3);
        transform: translateY(-1px);
    }
    .page-btn.active {
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        box-shadow: 0 8px 20px -6px rgba(6,182,212,.6);
    }
    .page-btn.disabled {
        opacity: .35;
        cursor: not-allowed;
        pointer-events: none;
    }
    .page-btn.nav {
        background: linear-gradient(135deg, rgba(34,211,238,.15), rgba(139,92,246,.15));
        border-color: rgba(34,211,238,.3);
        color: #06b6d4;
    }
    .dark .page-btn.nav { color: #67e8f9; }

    /* =========================================================
       EMPTY
       ========================================================= */
    .empty-state {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 3.5rem 1.5rem;
        border-radius: 1.5rem;
        border: 1.5px dashed rgba(148,163,184,.3);
        background: rgba(15,23,42,.4);
        text-align: center;
        color: #94a3b8;
    }
    .empty-state .icon-big { font-size: 3rem; color: #475569; }

    /* =========================================================
       MOBILE — 2 kolom, SEMUA CENTER
       ========================================================= */
    @media (max-width: 640px) {
        .u-orb { display: none; }

        /* Card jadi vertikal + center total */
        .user-card {
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: .65rem;
            padding: 1.15rem .85rem 1rem;
            border-radius: 1.15rem;
            min-height: 0;
            transition: none;
        }
        .user-card:hover {
            transform: none;
            border-color: rgba(148,163,184,.15);
            box-shadow: none;
        }
        .user-card::before,
        .user-card::after { display: none; }

        .user-avatar {
            width: 58px; height: 58px;
            margin-inline: auto;
        }
        .user-avatar .initials { font-size: 1.05rem; }
        .user-status {
            width: 13px; height: 13px;
            border-width: 2px;
        }
        .user-status::after { animation: none; }

        /* Info center total */
        .user-info {
            width: 100%;
            align-items: center;
            text-align: center;
            gap: .4rem;
        }
        .user-name {
            font-size: .82rem;
            line-height: 1.35;
            text-align: center;
            -webkit-line-clamp: 2;
        }

        /* Role badge — center, tidak melebar */
        .user-role {
            align-self: center;
            font-size: .58rem;
            gap: .28rem;
            padding: .26rem .6rem;
        }
        .user-role .dot { width: 5px; height: 5px; }

        /* Button full width */
        .user-action {
            width: 100%;
            padding: .48rem .6rem;
            font-size: .65rem;
            gap: .3rem;
            margin-inline: auto;
        }

        /* Pagination compact */
        .page-btn {
            min-width: 38px;
            height: 38px;
            font-size: .82rem;
            border-radius: 10px;
        }
        .pagination-wrap { gap: .3rem; margin-top: 1.75rem; }
        .pagination-info { font-size: .72rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .u-reveal {
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
        .user-status::after { animation: none; }
    }
</style>
@endpush

@section('content')

@php
    $icons = [
        'compass' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
        'search'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>',
        'eye'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>',
        'users'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'chev-l'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
        'chev-r'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
    ];

    $getInitials = function ($name) {
        $parts = preg_split('/\s+/', trim((string) $name));
        $init = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $init .= strtoupper(substr($p, 0, 1));
        }
        return $init ?: '?';
    };

    $getAvatar = function ($user) {
        $url = $user->avatar_url ?? null;
        if (empty($url)) return null;
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) return $url;
        if (str_starts_with($url, 'avatars/') || str_starts_with($url, 'storage/')) {
            return asset('storage/' . ltrim($url, '/'));
        }
        return asset(ltrim($url, '/'));
    };
@endphp

<div class="u-shell py-6 md:py-10">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="u-hero rounded-[2rem] p-6 sm:p-8 md:p-12 lg:p-16 text-white shadow-2xl u-reveal">
        <div class="u-orb one"></div>
        <div class="u-orb two"></div>

        <div class="relative z-10 text-center max-w-3xl mx-auto">
            <span class="hero-badge">
                <span class="ico ico-md">{!! $icons['compass'] !!}</span>
                Eksplor Anggota
            </span>

            <h1 class="mt-5 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                Eksplorv Anggota UNICODE
                <span class="bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 bg-clip-text text-transparent">
                   
                </span>
            </h1>

            <p class="mt-5 text-base md:text-lg lg:text-xl text-white/80 leading-relaxed">
                Temukan dan lihat profil anggota UKM Pemrograman UNICODE.
            </p>

            <div class="search-shell mt-8">
                <span class="search-icon ico ico-md">{!! $icons['search'] !!}</span>
                <input
                    type="text"
                    id="searchInput"
                    class="search-input"
                    placeholder="Cari nama anggota..."
                    autocomplete="off"
                    aria-label="Cari anggota"
                >
            </div>
        </div>
    </section>

    {{-- =====================================================
         INFO CHIP
    ====================================================== --}}
    <section class="mt-8 u-reveal">
        <div class="text-center text-slate-500 dark:text-slate-400 text-sm">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/60 px-4 py-2">
                <span class="ico ico-sm text-cyan-500">{!! $icons['users'] !!}</span>
                Menampilkan <strong class="text-slate-800 dark:text-white" id="visibleCount">{{ $users->count() }}</strong>
                dari <strong class="text-slate-800 dark:text-white">{{ $users->total() }}</strong> anggota
            </span>
        </div>
    </section>

    {{-- =====================================================
         GRID
    ====================================================== --}}
    <section class="mt-8 u-reveal">
        <div id="userGrid" class="user-grid">
            @forelse($users as $user)
                @php
                    $avatar   = $getAvatar($user);
                    $initials = $getInitials($user->name);
                    $role     = $user->level->name ?? 'Anggota';
                    $isActive = (bool) ($user->is_active ?? true);
                @endphp

                <a
                    href="{{ route('profile.public', $user->id) }}"
                    class="user-card u-reveal"
                    data-name="{{ strtolower($user->name) }}"
                >
                    <div class="user-avatar">
                        @if($avatar)
                            <img
                                src="{{ $avatar }}"
                                alt="Avatar {{ $user->name }}"
                                loading="lazy"
                                decoding="async"
                                onerror="this.onerror=null;this.style.display='none';this.parentNode.insertAdjacentHTML('beforeend','<div class=\'initials\'>{{ $initials }}</div>');"
                            >
                        @else
                            <div class="initials">{{ $initials }}</div>
                        @endif
                        @if($isActive)
                            <span class="user-status" title="Aktif"></span>
                        @endif
                    </div>

                    <div class="user-info">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-role">
                            <span class="dot"></span>
                            <span>{{ $role }}</span>
                        </div>
                    </div>

                    <span class="user-action">
                        <span class="ico ico-sm">{!! $icons['eye'] !!}</span>
                        Lihat
                    </span>
                </a>
            @empty
                <div class="empty-state">
                    <div class="icon-big">{!! $icons['users'] !!}</div>
                    <p class="text-lg font-semibold">Belum ada anggota terdaftar.</p>
                </div>
            @endforelse
        </div>

        <div id="emptySearch" class="hidden mt-12 text-center">
            <div class="inline-flex flex-col items-center gap-3 px-8 py-10 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40">
                <div class="text-5xl">🔍</div>
                <p class="font-semibold text-slate-700 dark:text-slate-200">Tidak ada anggota yang cocok.</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Coba ubah kata kunci pencarian.</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         PAGINATION
    ====================================================== --}}
    @if($users->hasPages())
        <section class="u-reveal">
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} anggota
                </div>

                @if($users->onFirstPage())
                    <span class="page-btn nav disabled" aria-disabled="true">
                        <span class="ico ico-sm">{!! $icons['chev-l'] !!}</span>
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-btn nav" rel="prev" aria-label="Halaman sebelumnya">
                        <span class="ico ico-sm">{!! $icons['chev-l'] !!}</span>
                    </a>
                @endif

                @php
                    $current = $users->currentPage();
                    $last    = $users->lastPage();
                    $pages   = [];

                    if ($last <= 7) {
                        for ($i = 1; $i <= $last; $i++) $pages[] = $i;
                    } else {
                        $pages[] = 1;
                        $left  = max(2, $current - 1);
                        $right = min($last - 1, $current + 1);

                        if ($left > 2) $pages[] = '…';
                        for ($i = $left; $i <= $right; $i++) $pages[] = $i;
                        if ($right < $last - 1) $pages[] = '…';

                        $pages[] = $last;
                    }
                @endphp

                @foreach($pages as $p)
                    @if($p === '…')
                        <span class="page-btn disabled" aria-hidden="true">…</span>
                    @elseif($p == $current)
                        <span class="page-btn active" aria-current="page">{{ $p }}</span>
                    @else
                        <a href="{{ $users->url($p) }}" class="page-btn">{{ $p }}</a>
                    @endif
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-btn nav" rel="next" aria-label="Halaman berikutnya">
                        <span class="ico ico-sm">{!! $icons['chev-r'] !!}</span>
                    </a>
                @else
                    <span class="page-btn nav disabled" aria-disabled="true">
                        <span class="ico ico-sm">{!! $icons['chev-r'] !!}</span>
                    </span>
                @endif
            </div>
        </section>
    @endif

    <div class="mt-10 text-center text-sm text-slate-500 dark:text-slate-400 u-reveal">
        Butuh bantuan?
        <a href="{{ route('kontak') }}" class="font-semibold text-cyan-500 hover:text-violet-500 transition-colors hover:underline">
            Hubungi kami
        </a>
    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* REVEAL */
    const reveals = document.querySelectorAll('.u-reveal');

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

    /* SEARCH */
    const searchInput  = document.getElementById('searchInput');
    const cards        = document.querySelectorAll('#userGrid .user-card');
    const emptySearch  = document.getElementById('emptySearch');
    const visibleCount = document.getElementById('visibleCount');

    if (!searchInput || !cards.length) return;

    let searchTimer;

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const term = (searchInput.value || '').toLowerCase().trim();
            let visible = 0;

            cards.forEach(card => {
                const name  = card.dataset.name || '';
                const match = term === '' || name.includes(term);

                if (match) {
                    card.style.display = '';
                    card.classList.add('is-visible');
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptySearch) {
                emptySearch.classList.toggle('hidden', visible > 0);
            }
            if (visibleCount) {
                visibleCount.textContent = visible;
            }
        }, 120);
    });

});
</script>
@endpush
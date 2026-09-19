@extends('layouts.app')

@section('title', 'Portofolio – UKM Pemrograman UNICODE')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    /* =========================================================
       SHELL
       ========================================================= */
    .u-shell { width: min(100% - 1rem, 100%); margin-inline: auto; }
    @media (min-width: 641px)  { .u-shell { width: min(100% - 1.5rem, 1000px); } }
    @media (min-width: 1025px) { .u-shell { width: min(100% - 2rem,   1400px); } }
    @media (min-width: 1536px) { .u-shell { width: min(100% - 3rem,   1800px); } }

    /* =========================================================
       REVEAL
       ========================================================= */
    .u-reveal {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity .55s cubic-bezier(.22,.61,.36,1),
                    transform .55s cubic-bezier(.22,.61,.36,1);
        will-change: opacity, transform;
    }
    .u-reveal.is-visible { opacity: 1; transform: translateY(0); }

    /* =========================================================
       ICON WRAPPER — bikin semua ikon SAMA UKURAN & SEJAJAR
       ========================================================= */
    .ico {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1em;
        height: 1em;
        flex-shrink: 0;
        line-height: 1;
        vertical-align: middle;
    }
    .ico svg { width: 100%; height: 100%; display: block; }
    .ico-sm { font-size: 12px; }
    .ico-md { font-size: 14px; }
    .ico-lg { font-size: 16px; }

    /* =========================================================
       HERO
       ========================================================= */
    .u-hero {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.20), transparent 32%),
            radial-gradient(circle at bottom left, rgba(139,92,246,.18), transparent 30%),
            linear-gradient(135deg, #020617, #172554 55%, #1d4ed8);
    }
    .u-orb {
        position: absolute;
        width: 280px; height: 280px;
        border-radius: 9999px;
        filter: blur(70px);
        opacity: .18;
        pointer-events: none;
    }
    .u-orb.one { top: -100px; right: -70px; background: #06b6d4; }
    .u-orb.two { bottom: -130px; left: -70px; background: #7c3aed; }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem 1rem;
        border-radius: 9999px;
        background: rgba(34,211,238,.10);
        border: 1px solid rgba(34,211,238,.30);
        color: #67e8f9;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
        line-height: 1;
    }

    /* =========================================================
       STAT CARD
       ========================================================= */
    .u-stat {
        position: relative;
        border-radius: 1.25rem;
        padding: 1.25rem 1rem;
        background: rgba(255,255,255,.85);
        border: 1px solid rgba(148,163,184,.2);
        box-shadow: 0 8px 24px rgba(15,23,42,.06);
        text-align: center;
        transition: transform .3s cubic-bezier(.22,.61,.36,1),
                    box-shadow .3s cubic-bezier(.22,.61,.36,1);
    }
    .u-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(15,23,42,.10);
    }
    .dark .u-stat {
        background: rgba(15,23,42,.75);
        border-color: rgba(148,163,184,.12);
        box-shadow: 0 10px 30px rgba(0,0,0,.25);
    }
    .u-stat .stat-emoji {
        font-size: 1.75rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem; height: 2.5rem;
        border-radius: 9999px;
        margin-bottom: .5rem;
    }
    .u-stat .stat-emoji.cyan    { background: rgba(6,182,212,.12); }
    .u-stat .stat-emoji.blue    { background: rgba(59,130,246,.12); }
    .u-stat .stat-emoji.violet  { background: rgba(139,92,246,.12); }
    .u-stat .stat-emoji.emerald { background: rgba(16,185,129,.12); }
    .u-stat .stat-emoji.amber   { background: rgba(245,158,11,.12); }
    .u-stat .stat-emoji.pink    { background: rgba(236,72,153,.12); }

    /* =========================================================
       PORTFOLIO CARD
       ========================================================= */
    .pf-card {
        position: relative;
        border-radius: 1.25rem;
        padding: 1.5px;
        background: linear-gradient(135deg,
                    rgba(34,211,238,.45),
                    rgba(59,130,246,.28) 45%,
                    rgba(139,92,246,.40) 75%,
                    rgba(236,72,153,.28));
        transition: transform .38s cubic-bezier(.22,.61,.36,1),
                    box-shadow .38s cubic-bezier(.22,.61,.36,1);
        will-change: transform;
        overflow: hidden;
    }
    .pf-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 65px -20px rgba(34,211,238,.35);
    }
    .pf-inner {
        position: relative;
        border-radius: calc(1.25rem - 1.5px);
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 60%, #0a0f1d 100%);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Thumb */
    .pf-thumb {
        position: relative;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #0f172a;
    }
    .pf-thumb img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .7s cubic-bezier(.22,.61,.36,1);
    }
    .pf-card:hover .pf-thumb img { transform: scale(1.08); }
    .pf-thumb::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 45%, rgba(10,15,29,.95) 100%);
        pointer-events: none;
    }

    /* Category badge */
    .pf-cat {
        position: absolute;
        top: .65rem; left: .65rem;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .65rem;
        border-radius: 9999px;
        font-size: .6rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #fff;
        line-height: 1;
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.25);
        z-index: 2;
    }
    .pf-cat.website { background: linear-gradient(135deg, #06b6d4, #3b82f6); }
    .pf-cat.mobile  { background: linear-gradient(135deg, #8b5cf6, #d946ef); }
    .pf-cat.ai      { background: linear-gradient(135deg, #10b981, #059669); }
    .pf-cat.game    { background: linear-gradient(135deg, #f59e0b, #ea580c); }
    .pf-cat.desain  { background: linear-gradient(135deg, #ec4899, #f43f5e); }

    /* Author chip */
    .pf-author {
        position: absolute;
        top: .65rem; right: .65rem;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .55rem;
        border-radius: 9999px;
        background: rgba(255,255,255,.94);
        color: #0b1220;
        font-size: .6rem;
        font-weight: 800;
        line-height: 1;
        box-shadow: 0 8px 20px rgba(0,0,0,.3);
        z-index: 2;
        max-width: 50%;
    }
    .pf-author span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Body */
    .pf-body {
        padding: 1rem 1.05rem 1.2rem;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        flex: 1;
    }

    .pf-date {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .05em;
        color: #94a3b8;
        text-transform: uppercase;
        line-height: 1;
    }

    .pf-title {
        font-weight: 800;
        font-size: 1rem;
        line-height: 1.35;
        color: #f8fafc;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }
    .pf-desc {
        font-size: .8rem;
        line-height: 1.55;
        color: #94a3b8;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }

    /* Tech chips */
    .pf-tech {
        display: flex;
        flex-wrap: wrap;
        gap: .3rem;
        margin-top: auto;
    }
    .tech-tag {
        display: inline-flex;
        align-items: center;
        padding: .25rem .6rem;
        border-radius: 9999px;
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .03em;
        line-height: 1;
        background: rgba(34,211,238,.10);
        border: 1px solid rgba(34,211,238,.30);
        color: #67e8f9;
    }

    /* Buttons */
    .pf-actions {
        display: flex;
        gap: .5rem;
        margin-top: .7rem;
    }
    .btn-demo, .btn-code {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .5rem .7rem;
        border-radius: 9999px;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .02em;
        line-height: 1;
        transition: transform .25s cubic-bezier(.22,.61,.36,1),
                    box-shadow .25s cubic-bezier(.22,.61,.36,1),
                    background .25s ease;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-demo {
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        box-shadow: 0 10px 25px -8px rgba(6,182,212,.7);
    }
    .btn-code {
        background: rgba(148,163,184,.15);
        border: 1px solid rgba(148,163,184,.3);
        color: #cbd5e1;
    }
    .btn-demo:hover, .btn-code:hover { transform: translateY(-2px); }
    .btn-demo:hover { box-shadow: 0 15px 30px -8px rgba(6,182,212,.85); }
    .btn-code:hover { background: rgba(148,163,184,.28); }

    /* =========================================================
       FILTER
       ========================================================= */
    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem .9rem;
        border-radius: 9999px;
        font-size: .78rem;
        font-weight: 700;
        line-height: 1;
        background: rgba(148,163,184,.15);
        color: #475569;
        transition: all .25s cubic-bezier(.22,.61,.36,1);
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
        font-size: .66rem;
        font-weight: 800;
        line-height: 1;
    }
    .filter-btn:not(.active) .filter-count { background: rgba(148,163,184,.25); }

    /* =========================================================
       HIGHLIGHT
       ========================================================= */
    .hl-card {
        position: relative;
        border-radius: 1.5rem;
        padding: 1.5px;
        background: linear-gradient(135deg,
                    rgba(34,211,238,.65),
                    rgba(139,92,246,.4) 50%,
                    rgba(236,72,153,.55));
        box-shadow: 0 30px 65px -25px rgba(6,182,212,.35);
    }
    .hl-inner {
        border-radius: calc(1.5rem - 1.5px);
        background: linear-gradient(180deg, #0b1220, #0f172a);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
    }
    @media (min-width: 768px) {
        .hl-inner {
            flex-direction: row;
            align-items: center;
            padding: 2rem;
            gap: 2rem;
        }
    }
    .hl-img {
        position: relative;
        flex-shrink: 0;
        width: 100%;
        border-radius: 1.15rem;
        overflow: hidden;
        aspect-ratio: 4 / 3;
    }
    @media (min-width: 768px) {
        .hl-img { width: 300px; aspect-ratio: 1; }
    }
    .hl-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .hl-badge {
        position: absolute;
        top: .7rem; left: .7rem;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .35rem .7rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        font-weight: 900;
        font-size: .65rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        line-height: 1;
        box-shadow: 0 10px 30px rgba(6,182,212,.55);
    }

    /* =========================================================
       CTA
       ========================================================= */
    .cta-block {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        padding: 2.5rem 1.5rem;
        text-align: center;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.28), transparent 40%),
            radial-gradient(circle at bottom left, rgba(139,92,246,.28), transparent 40%),
            linear-gradient(135deg, #020617, #172554 55%, #1d4ed8);
        box-shadow: 0 30px 65px -25px rgba(6,182,212,.4);
    }
    @media (min-width: 768px) {
        .cta-block { padding: 4rem 3rem; }
    }

    /* =========================================================
       MOBILE — 2 KOLOM CARD + compact
       ========================================================= */
    @media (max-width: 640px) {
        .u-orb { display: none; }
        .pf-card, .hl-card, .u-stat { transition: none; }
        .pf-card:hover, .u-stat:hover { transform: none; box-shadow: none; }
        .pf-thumb img { transition: none; }

        /* Card lebih compact supaya 2 kolom muat nyaman */
        .pf-inner { border-radius: calc(1.1rem - 1.5px); }
        .pf-card  { border-radius: 1.1rem; }
        .pf-body  { padding: .75rem .8rem .95rem; gap: .4rem; }

        .pf-thumb { aspect-ratio: 4 / 3; }
        .pf-cat   { font-size: .55rem; padding: .25rem .5rem; top: .5rem; left: .5rem; }
        .pf-author{ font-size: .55rem; padding: .25rem .45rem; top: .5rem; right: .5rem; max-width: 55%; }
        .pf-title { font-size: .82rem; -webkit-line-clamp: 2; }
        .pf-desc  { font-size: .7rem; -webkit-line-clamp: 2; }
        .pf-date  { font-size: .58rem; }

        .tech-tag { font-size: .55rem; padding: .2rem .5rem; }

        .pf-actions { gap: .35rem; margin-top: .55rem; }
        .btn-demo, .btn-code {
            padding: .42rem .5rem;
            font-size: .64rem;
            gap: .25rem;
        }

        /* Stat compact */
        .u-stat { padding: 1rem .5rem; border-radius: 1rem; }
        .u-stat .stat-emoji { width: 2rem; height: 2rem; font-size: 1.25rem; margin-bottom: .35rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .u-reveal {
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush

@section('content')

@php
    /* =========================================================
       FALLBACK URL WAJIB
       ========================================================= */
    $FALLBACK_URL = 'https://maulanaihza091-ship-it.github.io/portofolioihza/index_portofolio';

    $safeUrl = function ($url = null) use ($FALLBACK_URL) {
        if (empty($url) || $url === '#' || $url === 'javascript:void(0)') {
            return $FALLBACK_URL;
        }
        return $url;
    };

    /* =========================================================
       DATA PROYEK
       ========================================================= */
    $projects = [
        ['title'=>'Website Profil UKM','kategori'=>'website','date'=>'10 Jan 2025','desc'=>'Website resmi UKM Pemrograman dengan HTML, CSS, JS, dan Tailwind.','tech'=>['HTML','CSS','JS','Tailwind'],'img'=>'https://placehold.co/800x500/06b6d4/fff?text=Web+Profile','demo'=>null,'github'=>null,'author'=>'Tim Pengembang'],
        ['title'=>'Aplikasi Catatan Harian','kategori'=>'mobile','date'=>'5 Feb 2025','desc'=>'Aplikasi Android sederhana untuk mencatat kegiatan harian.','tech'=>['Java','SQLite','Android'],'img'=>'https://placehold.co/800x500/8b5cf6/fff?text=Note+App','demo'=>null,'github'=>null,'author'=>'Budi Santoso'],
        ['title'=>'Sistem Prediksi Cuaca','kategori'=>'ai','date'=>'20 Mar 2025','desc'=>'Machine learning model untuk memprediksi cuaca menggunakan Python.','tech'=>['Python','Scikit-learn','Pandas'],'img'=>'https://placehold.co/800x500/10b981/fff?text=AI+Weather','demo'=>null,'github'=>null,'author'=>'Tim AI'],
        ['title'=>'Game Edukasi Matematika','kategori'=>'game','date'=>'1 Apr 2025','desc'=>'Game 2D untuk belajar matematika dasar dengan skor dan level.','tech'=>['Unity','C#'],'img'=>'https://placehold.co/800x500/f59e0b/fff?text=Math+Game','demo'=>null,'github'=>null,'author'=>'Ahmad Rizki'],
        ['title'=>'Desain UI E-Commerce','kategori'=>'desain','date'=>'15 Apr 2025','desc'=>'Desain antarmuka aplikasi e-commerce modern menggunakan Figma.','tech'=>['Figma','Adobe XD'],'img'=>'https://placehold.co/800x500/ec4899/fff?text=UI+Design','demo'=>null,'github'=>null,'author'=>'Siti Nurhaliza'],
        ['title'=>'Sistem Informasi Perpustakaan','kategori'=>'website','date'=>'10 Mei 2025','desc'=>'Sistem manajemen perpustakaan kampus berbasis web.','tech'=>['PHP','MySQL','Bootstrap'],'img'=>'https://placehold.co/800x500/3b82f6/fff?text=Perpustakaan','demo'=>null,'github'=>null,'author'=>'Tim Backend'],
        ['title'=>'Chatbot Layanan Kampus','kategori'=>'ai','date'=>'22 Jun 2025','desc'=>'Chatbot Telegram yang menjawab FAQ seputar kampus.','tech'=>['Python','Flask','Bot API'],'img'=>'https://placehold.co/800x500/6366f1/fff?text=Chatbot','demo'=>null,'github'=>null,'author'=>'Dewi Lestari'],
        ['title'=>'Platform Belajar Coding','kategori'=>'website','date'=>'3 Jul 2025','desc'=>'Platform interaktif untuk belajar dasar-dasar pemrograman.','tech'=>['React','Node.js','MongoDB'],'img'=>'https://placehold.co/800x500/14b8a6/fff?text=Coding+Platform','demo'=>null,'github'=>null,'author'=>'Tim Fullstack'],
        ['title'=>'Aplikasi Pencatat Keuangan','kategori'=>'mobile','date'=>'18 Jul 2025','desc'=>'Aplikasi mobile untuk mencatat pemasukan dan pengeluaran.','tech'=>['Flutter','Dart','Firebase'],'img'=>'https://placehold.co/800x500/ef4444/fff?text=Finance+App','demo'=>null,'github'=>null,'author'=>'Eko Prasetyo'],
    ];

    $catLabels = [
        'website' => 'Website',
        'mobile'  => 'Mobile',
        'ai'      => 'AI/ML',
        'game'    => 'Game',
        'desain'  => 'UI/UX',
    ];

    $statCounts = [
        'all'     => count($projects),
        'website' => count(array_filter($projects, fn($p) => $p['kategori'] === 'website')),
        'mobile'  => count(array_filter($projects, fn($p) => $p['kategori'] === 'mobile')),
        'ai'      => count(array_filter($projects, fn($p) => $p['kategori'] === 'ai')),
        'game'    => count(array_filter($projects, fn($p) => $p['kategori'] === 'game')),
        'desain'  => count(array_filter($projects, fn($p) => $p['kategori'] === 'desain')),
    ];

    /* =========================================================
       ICON SET — semua SVG di-set width/height 100% di dalam wrapper
       ========================================================= */
    $icons = [
        'folder'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2z"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
        'user'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2"/></svg>',
        'external' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M10 14 21 3M21 14v7H3V3h7"/></svg>',
        'github'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2.2c-3.3.7-4-1.6-4-1.6-.6-1.4-1.4-1.8-1.4-1.8-1.1-.7.1-.7.1-.7 1.2.1 1.9 1.2 1.9 1.2 1.1 1.9 2.8 1.4 3.5 1 .1-.8.4-1.4.8-1.7-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2a11 11 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.7-2.8 5.7-5.5 6 .4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3z"/></svg>',
        'rocket'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
    ];
@endphp

<div class="u-shell py-5 md:py-8">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="u-hero rounded-[2rem] p-6 sm:p-8 md:p-12 lg:p-16 text-white shadow-2xl u-reveal">
        <div class="u-orb one"></div>
        <div class="u-orb two"></div>

        <div class="relative z-10 text-center max-w-3xl mx-auto">
            <span class="hero-badge">
                <span class="ico ico-md">{!! $icons['folder'] !!}</span>
                Karya Kami
            </span>

            <h1 class="mt-5 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                Portofolio
                <span class="block bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 bg-clip-text text-transparent">
                    Proyek & Karya Nyata
                </span>
            </h1>

            <p class="mt-5 text-base md:text-lg lg:text-xl text-white/80 leading-relaxed">
                Jelajahi hasil karya anggota UKM Pemrograman UNICODE.
                Dari website, aplikasi, hingga proyek machine learning —
                semua lahir dari kolaborasi dan semangat belajar.
            </p>
        </div>
    </section>

    {{-- =====================================================
         STATISTIK
    ====================================================== --}}
    <section class="mt-8 md:mt-12 u-reveal">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="u-stat">
                <div class="stat-emoji cyan">💻</div>
                <div class="text-2xl md:text-3xl font-black text-cyan-500">{{ $statCounts['all'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total Proyek</p>
            </div>
            <div class="u-stat">
                <div class="stat-emoji blue">🌐</div>
                <div class="text-2xl md:text-3xl font-black text-blue-500">{{ $statCounts['website'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Website</p>
            </div>
            <div class="u-stat">
                <div class="stat-emoji violet">📱</div>
                <div class="text-2xl md:text-3xl font-black text-violet-500">{{ $statCounts['mobile'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Aplikasi Mobile</p>
            </div>
            <div class="u-stat">
                <div class="stat-emoji emerald">🤖</div>
                <div class="text-2xl md:text-3xl font-black text-emerald-500">{{ $statCounts['ai'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">AI/ML</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         FILTER
    ====================================================== --}}
    <section class="mt-8 md:mt-12 u-reveal">
        <div class="flex flex-wrap gap-2 justify-center" id="filterBar">
            <button type="button" class="filter-btn active" data-filter="all">Semua <span class="filter-count">{{ $statCounts['all'] }}</span></button>
            <button type="button" class="filter-btn" data-filter="website">Website <span class="filter-count">{{ $statCounts['website'] }}</span></button>
            <button type="button" class="filter-btn" data-filter="mobile">Mobile <span class="filter-count">{{ $statCounts['mobile'] }}</span></button>
            <button type="button" class="filter-btn" data-filter="ai">AI/ML <span class="filter-count">{{ $statCounts['ai'] }}</span></button>
            <button type="button" class="filter-btn" data-filter="game">Game <span class="filter-count">{{ $statCounts['game'] }}</span></button>
            <button type="button" class="filter-btn" data-filter="desain">UI/UX <span class="filter-count">{{ $statCounts['desain'] }}</span></button>
        </div>
    </section>

    {{-- =====================================================
         GRID PORTOFOLIO — 2 kolom di HP
    ====================================================== --}}
    <section class="mt-8 md:mt-10 u-reveal">
        <div
            id="portfolioGrid"
            class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-5"
        >
            @foreach($projects as $p)
                @php
                    $demoUrl   = $safeUrl($p['demo']   ?? null);
                    $githubUrl = $safeUrl($p['github'] ?? null);
                @endphp

                <article class="pf-card u-reveal" data-kategori="{{ $p['kategori'] }}">
                    <div class="pf-inner">

                        <div class="pf-thumb">
                            <img
                                src="{{ $p['img'] }}"
                                alt="{{ $p['title'] }}"
                                loading="lazy"
                                decoding="async"
                                onerror="this.onerror=null;this.src='https://placehold.co/800x500/06b6d4/fff?text=Proyek';"
                            >
                            <span class="pf-cat {{ $p['kategori'] }}">{{ $catLabels[$p['kategori']] ?? $p['kategori'] }}</span>
                            <span class="pf-author" title="{{ $p['author'] }}">
                                <span class="ico ico-sm">{!! $icons['user'] !!}</span>
                                <span>{{ $p['author'] }}</span>
                            </span>
                        </div>

                        <div class="pf-body">
                            <span class="pf-date">
                                <span class="ico ico-sm">{!! $icons['calendar'] !!}</span>
                                {{ $p['date'] }}
                            </span>

                            <h3 class="pf-title">{{ $p['title'] }}</h3>
                            <p class="pf-desc">{{ $p['desc'] }}</p>

                            <div class="pf-tech">
                                @foreach($p['tech'] as $tech)
                                    <span class="tech-tag">{{ $tech }}</span>
                                @endforeach
                            </div>

                            <div class="pf-actions">
                                <a href="{{ $demoUrl }}" target="_blank" rel="noopener noreferrer" class="btn-demo">
                                    <span class="ico ico-sm">{!! $icons['external'] !!}</span>
                                    Demo
                                </a>
                                <a href="{{ $githubUrl }}" target="_blank" rel="noopener noreferrer" class="btn-code">
                                    <span class="ico ico-sm">{!! $icons['github'] !!}</span>
                                    Code
                                </a>
                            </div>
                        </div>

                    </div>
                </article>
            @endforeach
        </div>

        <div id="emptyState" class="hidden mt-12 text-center">
            <div class="inline-flex flex-col items-center gap-3 px-8 py-10 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40">
                <div class="text-5xl">🔍</div>
                <p class="font-semibold text-slate-700 dark:text-slate-200">Belum ada proyek di kategori ini.</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Coba pilih kategori lain.</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         PROYEK UNGGULAN
    ====================================================== --}}
    <section class="mt-12 md:mt-20 u-reveal">
        <div class="text-center mb-8">
            <span class="text-sm font-bold tracking-widest text-cyan-500">FEATURED</span>
            <h2 class="mt-2 text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                🚀 Proyek Unggulan
            </h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">
                Karya terbaik yang menjadi kebanggaan kami.
            </p>
        </div>

        <div class="hl-card">
            <div class="hl-inner">

                <div class="hl-img">
                    <img
                        src="https://placehold.co/600x600/06b6d4/fff?text=Featured+Project"
                        alt="Proyek Unggulan"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="hl-badge">⭐ Pilihan Editor</span>
                </div>

                <div class="flex-1">
                    <span class="inline-block text-xs font-bold uppercase tracking-widest text-cyan-400 mb-2">
                        React • Node.js • MongoDB
                    </span>
                    <h3 class="text-2xl md:text-3xl font-black text-white leading-tight">
                        Platform Belajar Coding Interaktif
                    </h3>
                    <p class="mt-3 text-slate-300 leading-relaxed text-sm md:text-base">
                        Dibangun dengan React dan Node.js, platform ini membantu mahasiswa
                        belajar dasar pemrograman melalui tantangan koding interaktif
                        dan materi terstruktur. Telah digunakan oleh lebih dari 100 pengguna.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full bg-cyan-500/15 border border-cyan-500/30 text-cyan-300 text-xs font-bold">React</span>
                        <span class="px-3 py-1 rounded-full bg-violet-500/15 border border-violet-500/30 text-violet-300 text-xs font-bold">Node.js</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-bold">MongoDB</span>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $FALLBACK_URL }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 font-black text-white text-sm"
                           style="background:linear-gradient(135deg,#06b6d4,#3b82f6);box-shadow:0 10px 25px -8px rgba(6,182,212,.7);">
                            <span class="ico ico-md">{!! $icons['external'] !!}</span>
                            Live Demo
                        </a>
                        <a href="{{ $FALLBACK_URL }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 font-black text-white text-sm"
                           style="background:rgba(148,163,184,.15);border:1px solid rgba(148,163,184,.3);">
                            <span class="ico ico-md">{!! $icons['github'] !!}</span>
                            Source Code
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =====================================================
         CTA
    ====================================================== --}}
    <section class="mt-12 md:mt-20 u-reveal">
        <div class="cta-block">
            <h3 class="text-2xl md:text-5xl font-black mb-4 leading-tight">
                Punya Ide Proyek Keren?
            </h3>
            <p class="text-base md:text-xl max-w-2xl mx-auto text-white/80 leading-relaxed">
                Bergabunglah dengan UKM Pemrograman UNICODE dan wujudkan
                idemu bersama tim yang solid!
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('landing') }}#pendaftaran"
                   class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 font-black text-blue-700 shadow-xl transition hover:-translate-y-0.5">
                    <span class="ico ico-md">{!! $icons['rocket'] !!}</span>
                    Gabung Sekarang
                </a>
                <a href="{{ $FALLBACK_URL }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-7 py-3.5 font-black text-white transition hover:bg-white/20">
                    <span class="ico ico-md">{!! $icons['external'] !!}</span>
                    Lihat Portofolio Pribadi
                </a>
            </div>
        </div>
    </section>

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

    /* FILTER */
    const filterButtons = document.querySelectorAll('#filterBar .filter-btn');
    const cards         = document.querySelectorAll('#portfolioGrid .pf-card');
    const emptyState    = document.getElementById('emptyState');

    if (!cards.length) return;

    let currentFilter = 'all';

    function applyFilters() {
        let visible = 0;

        cards.forEach(card => {
            const kat   = card.dataset.kategori || '';
            const match = currentFilter === 'all' || kat === currentFilter;

            if (match) {
                card.style.display = '';
                card.classList.add('is-visible');
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) emptyState.classList.toggle('hidden', visible > 0);
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter || 'all';
            applyFilters();
        });
    });

});
</script>
@endpush
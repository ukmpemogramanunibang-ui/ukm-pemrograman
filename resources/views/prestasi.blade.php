@extends('layouts.app')

@section('title', 'Prestasi – UKM Pemrograman UNICODE')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    :root {
        --neon-cyan: #22d3ee;
        --neon-blue: #3b82f6;
        --neon-violet: #8b5cf6;
        --neon-pink: #ec4899;
        --neon-yellow: #facc15;
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
            radial-gradient(circle at top right, rgba(250,204,21,.16), transparent 32%),
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
    .unicode-orb.one { top: -100px; right: -70px; background: #facc15; }
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
       ACHIEVEMENT CARD
       ========================================================= */
    .achievement-card {
        position: relative;
        border-radius: 1.5rem;
        padding: 1.5px;
        background: linear-gradient(135deg,
                    rgba(250,204,21,.45),
                    rgba(34,211,238,.30) 45%,
                    rgba(139,92,246,.40) 75%,
                    rgba(236,72,153,.28));
        transition: transform .35s ease, box-shadow .35s ease;
        will-change: transform;
        overflow: hidden;
    }
    .achievement-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 65px -20px rgba(250,204,21,.30);
    }

    .ac-inner {
        position: relative;
        border-radius: calc(1.5rem - 1.5px);
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 60%, #0a0f1d 100%);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Thumb */
    .ac-thumb {
        position: relative;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #0f172a;
    }
    .ac-thumb img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .6s cubic-bezier(.22,.61,.36,1);
        display: block;
    }
    .achievement-card:hover .ac-thumb img {
        transform: scale(1.08);
    }
    .ac-thumb::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 40%, rgba(10,15,29,.95) 100%);
        pointer-events: none;
    }

    /* Category badge on image */
    .ac-cat {
        position: absolute;
        top: .75rem; left: .75rem;
        padding: .32rem .7rem;
        border-radius: 9999px;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #fff;
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.25);
        z-index: 2;
    }
    .ac-cat.lomba         { background: linear-gradient(135deg, #f59e0b, #ea580c); }
    .ac-cat.sertifikasi   { background: linear-gradient(135deg, #06b6d4, #3b82f6); }
    .ac-cat.proyek        { background: linear-gradient(135deg, #8b5cf6, #d946ef); }
    .ac-cat.penghargaan   { background: linear-gradient(135deg, #ec4899, #f43f5e); }

    /* Icon floating */
    .ac-icon {
        position: absolute;
        top: .75rem; right: .75rem;
        width: 36px; height: 36px;
        border-radius: 9999px;
        background: rgba(255,255,255,.94);
        display: flex; align-items: center; justify-content: center;
        color: #0f172a;
        box-shadow: 0 10px 30px rgba(0,0,0,.35);
        z-index: 2;
    }
    .ac-icon svg { width: 18px; height: 18px; }

    /* Content */
    .ac-body {
        padding: 1.15rem 1.2rem 1.35rem;
        display: flex;
        flex-direction: column;
        gap: .55rem;
        flex: 1;
    }
    .ac-date {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        color: #94a3b8;
        text-transform: uppercase;
    }
    .ac-date svg { width: 12px; height: 12px; }

    .ac-title {
        font-weight: 800;
        font-size: 1.02rem;
        line-height: 1.35;
        color: #f8fafc;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ac-desc {
        font-size: .82rem;
        line-height: 1.55;
        color: #94a3b8;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

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

    /* =========================================================
       HIGHLIGHT / PRESTASI UNGGULAN
       ========================================================= */
    .highlight-card {
        position: relative;
        border-radius: 1.5rem;
        padding: 1.5px;
        background: linear-gradient(135deg,
                    rgba(250,204,21,.65),
                    rgba(236,72,153,.4) 50%,
                    rgba(34,211,238,.55));
        box-shadow: 0 30px 65px -25px rgba(250,204,21,.35);
    }
    .highlight-inner {
        border-radius: calc(1.5rem - 1.5px);
        background: linear-gradient(180deg, #0b1220, #0f172a);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    @media (min-width: 768px) {
        .highlight-inner {
            flex-direction: row;
            align-items: center;
            padding: 2rem;
            gap: 2rem;
        }
    }
    .highlight-img {
        position: relative;
        flex-shrink: 0;
        width: 100%;
        border-radius: 1.25rem;
        overflow: hidden;
        aspect-ratio: 4 / 3;
    }
    @media (min-width: 768px) {
        .highlight-img { width: 280px; aspect-ratio: 1; }
    }
    .highlight-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    .highlight-badge {
        position: absolute;
        top: .75rem; left: .75rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .8rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #facc15, #f59e0b);
        color: #0b1220;
        font-weight: 900;
        font-size: .7rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        box-shadow: 0 10px 30px rgba(250,204,21,.5);
    }

    /* =========================================================
       SERTIFIKAT GALERI
       ========================================================= */
    .cert-card {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        border: 1px solid rgba(148,163,184,.2);
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .cert-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -15px rgba(6,182,212,.4);
        border-color: rgba(34,211,238,.5);
    }
    .cert-card img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .5s ease;
    }
    .cert-card:hover img { transform: scale(1.08); }
    .cert-label {
        position: absolute;
        inset-inline: 0; bottom: 0;
        padding: .75rem;
        background: linear-gradient(180deg, transparent, rgba(10,15,29,.9));
        font-size: .72rem;
        font-weight: 700;
        color: #fff;
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
            radial-gradient(circle at top right, rgba(250,204,21,.25), transparent 40%),
            radial-gradient(circle at bottom left, rgba(139,92,246,.25), transparent 40%),
            linear-gradient(135deg, #020617, #172554 55%, #1d4ed8);
        box-shadow: 0 30px 65px -25px rgba(6,182,212,.4);
    }
    @media (min-width: 768px) {
        .cta-block { padding: 4rem 3rem; }
    }

    /* =========================================================
       MOBILE OPTIMIZATION
       ========================================================= */
    @media (max-width: 640px) {
        .unicode-orb { display: none; }
        .achievement-card,
        .highlight-card,
        .cert-card,
        .stat-card { transition: none; }
        .achievement-card:hover,
        .cert-card:hover { transform: none; }
        .ac-thumb img { transition: none; }
        .ac-title { font-size: .92rem; }
        .ac-desc { font-size: .78rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
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
    $achievements = [
        [
            'title'    => 'Juara 1 Lomba UI/UX Tingkat Nasional',
            'kategori' => 'lomba',
            'date'     => '15 Februari 2025',
            'desc'     => 'Tim UNICODE meraih juara pertama dalam kompetisi desain UI/UX yang diadakan oleh Kementerian Kominfo.',
            'icon'     => 'trophy',
            'img'      => 'https://placehold.co/800x500/f59e0b/fff?text=UI%2FUX+Winner',
        ],
        [
            'title'    => 'Sertifikasi Internasional Python',
            'kategori' => 'sertifikasi',
            'date'     => '10 Maret 2025',
            'desc'     => 'Tiga anggota berhasil memperoleh sertifikasi Python dari Python Institute.',
            'icon'     => 'certificate',
            'img'      => 'https://placehold.co/800x500/06b6d4/fff?text=Python+Cert',
        ],
        [
            'title'    => 'Aplikasi Manajemen Tugas Kampus',
            'kategori' => 'proyek',
            'date'     => '22 April 2025',
            'desc'     => 'Proyek kolaboratif yang digunakan oleh mahasiswa untuk mengelola tugas dan jadwal harian.',
            'icon'     => 'layers',
            'img'      => 'https://placehold.co/800x500/8b5cf6/fff?text=Proyek+App',
        ],
        [
            'title'    => 'Juara 2 Hackathon Kampus Merdeka',
            'kategori' => 'lomba',
            'date'     => '5 Mei 2025',
            'desc'     => 'Mengembangkan solusi digital untuk UMKM dalam waktu 48 jam penuh inovasi.',
            'icon'     => 'trophy',
            'img'      => 'https://placehold.co/800x500/f59e0b/fff?text=Hackathon',
        ],
        [
            'title'    => 'Sertifikasi AWS Cloud Practitioner',
            'kategori' => 'sertifikasi',
            'date'     => '20 Juni 2025',
            'desc'     => 'Anggota Divisi Pengembang lulus ujian AWS Cloud Practitioner dengan predikat tinggi.',
            'icon'     => 'certificate',
            'img'      => 'https://placehold.co/800x500/06b6d4/fff?text=AWS+Cert',
        ],
        [
            'title'    => 'Sistem Informasi Perpustakaan',
            'kategori' => 'proyek',
            'date'     => '1 Juli 2025',
            'desc'     => 'Proyek pengembangan sistem informasi perpustakaan untuk kebutuhan kampus.',
            'icon'     => 'layers',
            'img'      => 'https://placehold.co/800x500/8b5cf6/fff?text=Perpustakaan',
        ],
        [
            'title'    => 'Penghargaan Inovasi Digital dari Rektor',
            'kategori' => 'penghargaan',
            'date'     => '17 Agustus 2025',
            'desc'     => 'UKM UNICODE menerima penghargaan atas kontribusi dalam transformasi digital kampus.',
            'icon'     => 'award',
            'img'      => 'https://placehold.co/800x500/ec4899/fff?text=Penghargaan',
        ],
        [
            'title'    => 'Juara 3 Kompetisi Web Design',
            'kategori' => 'lomba',
            'date'     => '10 September 2025',
            'desc'     => 'Memenangkan lomba desain web tingkat regional dengan karya inovatif.',
            'icon'     => 'trophy',
            'img'      => 'https://placehold.co/800x500/f59e0b/fff?text=Web+Design',
        ],
        [
            'title'    => 'Sertifikasi Google Data Analytics',
            'kategori' => 'sertifikasi',
            'date'     => '3 Oktober 2025',
            'desc'     => 'Anggota Divisi Pengembang menyelesaikan program sertifikasi data analytics dari Google.',
            'icon'     => 'certificate',
            'img'      => 'https://placehold.co/800x500/06b6d4/fff?text=Data+Analytics',
        ],
    ];

    /* Statistik */
    $statCounts = [
        'all'         => count($achievements),
        'lomba'       => count(array_filter($achievements, fn($a) => $a['kategori'] === 'lomba')),
        'sertifikasi' => count(array_filter($achievements, fn($a) => $a['kategori'] === 'sertifikasi')),
        'proyek'      => count(array_filter($achievements, fn($a) => $a['kategori'] === 'proyek')),
        'penghargaan' => count(array_filter($achievements, fn($a) => $a['kategori'] === 'penghargaan')),
    ];

    /* Label kategori */
    $catLabels = [
        'lomba'       => 'Lomba',
        'sertifikasi' => 'Sertifikasi',
        'proyek'      => 'Proyek',
        'penghargaan' => 'Penghargaan',
    ];
@endphp

{{-- Icon set (inline SVG, hemat request) --}}
@php
    $icons = [
        'trophy' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4z"/><path d="M17 5h3v3a3 3 0 0 1-3 3M7 5H4v3a3 3 0 0 0 3 3"/></svg>',
        'certificate' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="5"/><path d="M8.5 13.5 7 22l5-3 5 3-1.5-8.5"/></svg>',
        'layers' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>',
        'award' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
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
            <span class="inline-flex items-center gap-2 rounded-full border border-yellow-300/30 bg-yellow-300/10 px-4 py-2 text-xs md:text-sm font-bold uppercase tracking-widest text-yellow-200">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                </svg>
                Prestasi Kami
            </span>

            <h1 class="mt-5 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                Bukti Nyata
                <span class="bg-gradient-to-r from-yellow-300 via-cyan-300 to-violet-300 bg-clip-text text-transparent">
                    Karya & Inovasi
                </span>
            </h1>

            <p class="mt-5 text-base md:text-lg lg:text-xl text-white/80 leading-relaxed">
                Kumpulan prestasi, penghargaan, dan pencapaian yang diraih
                anggota UKM Pemrograman UNICODE di berbagai ajang kompetisi,
                sertifikasi, dan proyek nyata.
            </p>
        </div>
    </section>

    {{-- =====================================================
         STATISTIK
    ====================================================== --}}
    <section class="mt-8 md:mt-12 unicode-reveal">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="stat-card">
                <div class="text-3xl">🏆</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-yellow-500">{{ $statCounts['all'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total Prestasi</p>
            </div>
            <div class="stat-card">
                <div class="text-3xl">🥇</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-amber-500">{{ $statCounts['lomba'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Lomba</p>
            </div>
            <div class="stat-card">
                <div class="text-3xl">📜</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-cyan-500">{{ $statCounts['sertifikasi'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Sertifikasi</p>
            </div>
            <div class="stat-card">
                <div class="text-3xl">💡</div>
                <div class="mt-2 text-2xl md:text-3xl font-black text-violet-500">{{ $statCounts['proyek'] + $statCounts['penghargaan'] }}</div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Proyek & Penghargaan</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         FILTER
    ====================================================== --}}
    <section class="mt-8 md:mt-12 unicode-reveal">
        <div class="flex flex-wrap gap-2 justify-center" id="filterBar">
            <button type="button" class="filter-btn active" data-filter="all">
                Semua <span class="filter-count">{{ $statCounts['all'] }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="lomba">
                Lomba <span class="filter-count">{{ $statCounts['lomba'] }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="sertifikasi">
                Sertifikasi <span class="filter-count">{{ $statCounts['sertifikasi'] }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="proyek">
                Proyek <span class="filter-count">{{ $statCounts['proyek'] }}</span>
            </button>
            <button type="button" class="filter-btn" data-filter="penghargaan">
                Penghargaan <span class="filter-count">{{ $statCounts['penghargaan'] }}</span>
            </button>
        </div>
    </section>

    {{-- =====================================================
         GRID PRESTASI
    ====================================================== --}}
    <section class="mt-8 md:mt-10 unicode-reveal">
        <div
            id="achievementGrid"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5"
        >
            @foreach($achievements as $a)
                <article
                    class="achievement-card unicode-reveal"
                    data-kategori="{{ $a['kategori'] }}"
                >
                    <div class="ac-inner">

                        <div class="ac-thumb">
                            <img
                                src="{{ $a['img'] }}"
                                alt="{{ $a['title'] }}"
                                loading="lazy"
                                decoding="async"
                                onerror="this.onerror=null;this.src='https://placehold.co/800x500/06b6d4/fff?text=Prestasi';"
                            >
                            <span class="ac-cat {{ $a['kategori'] }}">{{ $catLabels[$a['kategori']] ?? $a['kategori'] }}</span>
                            <span class="ac-icon">{!! $icons[$a['icon']] ?? $icons['trophy'] !!}</span>
                        </div>

                        <div class="ac-body">
                            <span class="ac-date">
                                {!! $icons['calendar'] !!}
                                {{ $a['date'] }}
                            </span>
                            <h3 class="ac-title">{{ $a['title'] }}</h3>
                            <p class="ac-desc">{{ $a['desc'] }}</p>
                        </div>

                    </div>
                </article>
            @endforeach
        </div>

        <div id="emptyState" class="hidden mt-12 text-center">
            <div class="inline-flex flex-col items-center gap-3 px-8 py-10 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40">
                <div class="text-5xl">🔍</div>
                <p class="font-semibold text-slate-700 dark:text-slate-200">Belum ada prestasi di kategori ini.</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Coba pilih kategori lain.</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         PRESTASI UNGGULAN
    ====================================================== --}}
    <section class="mt-12 md:mt-20 unicode-reveal">
        <div class="text-center mb-8">
            <span class="text-sm font-bold tracking-widest text-yellow-500">HIGHLIGHT</span>
            <h2 class="mt-2 text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                🌟 Prestasi Unggulan
            </h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">
                Pencapaian terbaik yang membanggakan UKM Pemrograman UNICODE.
            </p>
        </div>

        <div class="highlight-card">
            <div class="highlight-inner">

                <div class="highlight-img">
                    <img
                        src="https://placehold.co/600x600/f59e0b/fff?text=Juara+1"
                        alt="Juara 1 Lomba UI/UX Nasional"
                        loading="lazy"
                        decoding="async"
                    >
                    <span class="highlight-badge">🏆 Nasional</span>
                </div>

                <div class="flex-1">
                    <span class="inline-block text-xs font-bold uppercase tracking-widest text-yellow-400 mb-2">
                        Kominfo • Februari 2025
                    </span>
                    <h3 class="text-2xl md:text-3xl font-black text-white leading-tight">
                        Juara 1 Lomba UI/UX Nasional
                    </h3>
                    <p class="mt-3 text-slate-300 leading-relaxed text-sm md:text-base">
                        Tim UNICODE berhasil menyisihkan 150 peserta dari berbagai universitas
                        dan meraih juara pertama dengan inovasi desain aplikasi kesehatan mental.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full bg-cyan-500/15 border border-cyan-500/30 text-cyan-300 text-xs font-bold">
                            Desain UI/UX
                        </span>
                        <span class="px-3 py-1 rounded-full bg-violet-500/15 border border-violet-500/30 text-violet-300 text-xs font-bold">
                            Tingkat Nasional
                        </span>
                        <span class="px-3 py-1 rounded-full bg-yellow-500/15 border border-yellow-500/30 text-yellow-300 text-xs font-bold">
                            Juara 1
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =====================================================
         GALERI SERTIFIKAT
    ====================================================== --}}
    <section class="mt-12 md:mt-20 unicode-reveal">
        <div class="text-center mb-8">
            <span class="text-sm font-bold tracking-widest text-cyan-500">GALERI</span>
            <h2 class="mt-2 text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                Sertifikat Anggota
            </h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">
                Koleksi sertifikat keahlian yang diraih anggota.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            @for ($i = 1; $i <= 8; $i++)
                <div class="cert-card">
                    <img
                        src="https://placehold.co/400x300/06b6d4/fff?text=Sertifikat+{{ $i }}"
                        alt="Sertifikat {{ $i }}"
                        loading="lazy"
                        decoding="async"
                        onerror="this.onerror=null;this.src='https://placehold.co/400x300/06b6d4/fff?text=Cert';"
                    >
                    <span class="cert-label">Sertifikat {{ $i }}</span>
                </div>
            @endfor
        </div>
    </section>

    {{-- =====================================================
         CTA
    ====================================================== --}}
    <section class="mt-12 md:mt-20 unicode-reveal">
        <div class="cta-block">
            <h3 class="text-2xl md:text-5xl font-black mb-4 leading-tight">
                Siap Mengukir Prestasi Bersama?
            </h3>
            <p class="text-base md:text-xl max-w-2xl mx-auto text-white/80 leading-relaxed">
                Bergabunglah dengan UKM Pemrograman UNICODE dan wujudkan potensimu
                di dunia teknologi!
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a
                    href="{{ route('landing') }}#pendaftaran"
                    class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 font-black text-blue-700 shadow-xl transition hover:-translate-y-0.5"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                        <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                        <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/>
                        <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                    </svg>
                    Gabung Sekarang
                </a>
                <a
                    href="{{ route('kontak') }}"
                    class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-7 py-3.5 font-black text-white transition hover:bg-white/20"
                >
                    Hubungi Kami
                </a>
            </div>
        </div>
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
       2. FILTER PRESTASI
       ===================================================== */
    const filterButtons = document.querySelectorAll('#filterBar .filter-btn');
    const cards         = document.querySelectorAll('#achievementGrid .achievement-card');
    const emptyState    = document.getElementById('emptyState');

    if (!cards.length) return;

    let currentFilter = 'all';

    function applyFilters() {
        let visible = 0;

        cards.forEach(card => {
            const kat = card.dataset.kategori || '';
            const match = currentFilter === 'all' || kat === currentFilter;

            if (match) {
                card.style.display = '';
                card.classList.add('is-visible');
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('hidden', visible > 0);
        }
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


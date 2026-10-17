@extends('layouts.app')

@section('title', 'Keputusan UNICODE — Roda Keberuntungan')

{{-- ═══════════════════════════════════════════════════════════
 | STYLES — Custom (mengikuti pola kontak)
 * ═══════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
    /* ── HERO GRADIENT — hanya di section hero, bukan seluruh halaman ── */
    .keputusan-hero {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.22), transparent 32%),
            radial-gradient(circle at bottom left, rgba(139,92,246,.20), transparent 30%),
            linear-gradient(135deg, #020617, #172554 55%, #1d4ed8);
    }

    /* Orb dekoratif di dalam hero */
    .keputusan-orb {
        position: absolute;
        border-radius: 9999px;
        filter: blur(70px);
        opacity: .18;
        pointer-events: none;
    }
    .keputusan-orb.one { top: -100px; right: -70px; background: #06b6d4; width: 280px; height: 280px; }
    .keputusan-orb.two { bottom: -130px; left: -70px; background: #7c3aed; width: 280px; height: 280px; }

    /* ── CTA BUTTON ── */
    .keputusan-cta {
        background: linear-gradient(135deg, #06b6d4 0%, #8b5cf6 100%);
        color: #ffffff;
        transition: all .3s cubic-bezier(.22,.61,.36,1);
    }
    .keputusan-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 45px -12px rgba(6, 182, 212, .7);
    }

    /* ── CARD HOVER ── */
    .keputusan-card {
        transition: all .3s cubic-bezier(.22,.61,.36,1);
    }
    .keputusan-card:hover {
        transform: translateY(-4px);
    }

    /* ── WHEEL ── */
    #demo-wheel {
        transform-origin: 200px 200px;
        backface-visibility: hidden;
        will-change: transform;
    }

    /* ── MOBILE: kurangi animasi ── */
    @media (max-width: 640px) {
        .keputusan-orb { display: none; }
        .keputusan-card { transition: none; }
        .keputusan-card:hover { transform: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        #demo-wheel { transition-duration: 800ms !important; }
        .keputusan-cta:hover { transform: none; }
        .keputusan-card:hover { transform: none; }
    }
</style>
@endpush

@section('content')

{{-- Skip link (a11y) --}}
<a href="#main-content"
   class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100]
          focus:rounded-lg focus:bg-cyan-500 focus:px-4 focus:py-2
          focus:font-bold focus:text-white focus:shadow-2xl">
    Lompat ke konten utama
</a>

<div class="container mx-auto px-4 py-6 md:py-10">

    <main id="main-content">

    {{-- ═══════════════════════════════════════════════════════════
     | ① HERO
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="keputusan-hero mb-16 p-8 text-white shadow-2xl md:p-12 lg:p-16">
        <div class="keputusan-orb one" aria-hidden="true"></div>
        <div class="keputusan-orb two" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-2 lg:gap-16">

            {{-- ── KIRI ── --}}
            <div class="text-center lg:text-left">
                {{-- Eyebrow --}}
                <div class="mb-6 inline-flex items-center gap-2 rounded-full
                            border border-cyan-400/30 bg-cyan-500/10 px-3 py-1.5 backdrop-blur-sm">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-cyan-400"></span>
                    </span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-cyan-200">
                        Keputusan UNICODE
                    </span>
                </div>

                {{-- Judul --}}
                <h1 class="mb-6 text-4xl font-black leading-[1.05] tracking-tight md:text-5xl lg:text-6xl">
                    <span class="text-white">Roda</span>
                    <span class="ml-2 bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 bg-clip-text text-transparent">
                        Keberuntungan
                    </span>
                    <br>
                    <span class="text-white/90">yang cepat &amp; interaktif.</span>
                </h1>

                {{-- Deskripsi --}}
                <p class="mx-auto mb-8 max-w-xl text-base leading-relaxed text-white/70 md:text-lg lg:mx-0">
                    Masukkan peserta, atur sistem pemilihan, lalu biarkan
                    sistem menentukan hasil secara <span class="font-semibold text-white">real-time</span>.
                    Cocok untuk kelas, presentasi, pemilihan kelompok, dan pengundian.
                </p>

                {{-- CTA --}}
                <div class="flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                    <a href="{{ route('keputusan.roda.create') }}"
                       aria-label="Buat roda keberuntungan baru"
                       class="keputusan-cta group inline-flex items-center justify-center gap-2 rounded-2xl px-7 py-4
                              text-base font-bold
                              shadow-[0_10px_40px_-10px_rgba(34,211,238,0.55)]
                              focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300">
                        <span class="text-xl" aria-hidden="true">🎡</span>
                        <span>Buat Roda Sekarang</span>
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    @auth
                        <a href="{{ route('keputusan.roda.mine') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl
                                  border border-white/20 bg-white/10 px-6 py-4 text-base font-semibold
                                  text-white backdrop-blur
                                  transition-all duration-300
                                  hover:bg-white/20
                                  focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300">
                            <span aria-hidden="true">📁</span>
                            <span>Roda Saya</span>
                        </a>
                    @endauth
                </div>

                {{-- Trust badges --}}
                <ul class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 list-none text-sm text-white/60 lg:justify-start">
                    @foreach(['Tanpa login', 'Maks. 50 peserta', 'Bisa dibagikan'] as $badge)
                        <li class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="3" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $badge }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- ── KANAN: Demo Wheel ── --}}
            <div class="relative flex items-center justify-center">
                <div class="relative w-full max-w-[420px]">

                    {{-- Glow --}}
                    <div class="absolute inset-0 scale-90 rounded-full
                                bg-gradient-to-br from-cyan-500/30 to-violet-600/30 blur-3xl"
                         aria-hidden="true"></div>

                    {{-- Badge DEMO --}}
                    <div class="absolute -top-2 left-1/2 z-20 inline-flex -translate-x-1/2 items-center gap-2
                                rounded-full border border-cyan-400/40 bg-black/70 px-3 py-1 backdrop-blur
                                text-[11px] font-bold tracking-wider text-cyan-300">
                        <span class="relative flex h-1.5 w-1.5" aria-hidden="true">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                        </span>
                        DEMO INTERAKTIF
                    </div>

                    {{-- Wheel container --}}
                    <div class="relative flex aspect-square w-full items-center justify-center">
                        {{-- Pointer --}}
                        <div class="absolute -top-3 left-1/2 z-10 h-0 w-0 -translate-x-1/2
                                    border-l-[12px] border-r-[12px] border-t-[22px]
                                    border-l-transparent border-r-transparent border-t-cyan-400
                                    drop-shadow-[0_0_10px_rgba(34,211,238,0.9)]"
                             aria-hidden="true"></div>

                        {{-- Wheel SVG --}}
                        <svg id="demo-wheel"
                             viewBox="0 0 400 400"
                             role="img"
                             aria-label="Demo roda keberuntungan dengan 5 peserta"
                             class="h-full w-full drop-shadow-[0_20px_60px_rgba(34,211,238,0.3)]">
                            <defs>
                                <linearGradient id="ringOuter" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#22d3ee"/>
                                    <stop offset="100%" stop-color="#8b5cf6"/>
                                </linearGradient>
                            </defs>

                            <circle cx="200" cy="200" r="195" fill="none"
                                    stroke="url(#ringOuter)" stroke-width="6"/>
                            <circle cx="200" cy="200" r="178" fill="none"
                                    stroke="rgba(255,255,255,0.15)" stroke-width="1"/>

                            <g id="demo-wheel-segments"><!-- JS inject --></g>

                            {{-- Center hub --}}
                            <circle cx="200" cy="200" r="34" fill="#0b0d1a"/>
                            <circle cx="200" cy="200" r="34" fill="none"
                                    stroke="url(#ringOuter)" stroke-width="3"/>
                            <circle cx="200" cy="200" r="22" fill="#0b0d1a"/>
                            <text x="200" y="208" text-anchor="middle"
                                  font-family="ui-sans-serif, system-ui" font-weight="900"
                                  font-size="16" fill="#22d3ee" aria-hidden="true">🎡</text>
                        </svg>
                    </div>

                    {{-- Result --}}
                    <div id="demo-result"
                         role="status" aria-live="polite" aria-atomic="true"
                         class="mt-6 flex min-h-[52px] flex-col items-center justify-center gap-1">
                        <p id="demo-result-label"
                           class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/40">
                            Siap diputar
                        </p>
                        <p id="demo-result-name"
                           class="bg-gradient-to-r from-cyan-300 to-violet-300 bg-clip-text text-xl font-black text-transparent">
                            Klik tombol di bawah
                        </p>
                    </div>

                    {{-- Spin button --}}
                    <div class="mt-4 flex items-center justify-center">
                        <button type="button" id="demo-spin-btn"
                                aria-label="Putar demo roda keberuntungan"
                                aria-controls="demo-wheel demo-result"
                                class="group inline-flex items-center gap-2 rounded-xl
                                       border border-white/20 bg-white/10 px-5 py-2.5
                                       text-sm font-bold text-white backdrop-blur
                                       transition-all duration-200
                                       hover:border-cyan-400/40 hover:bg-white/20
                                       focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300
                                       active:scale-[0.97]
                                       disabled:cursor-not-allowed disabled:opacity-40">
                            <span id="demo-spin-icon" aria-hidden="true">🎯</span>
                            <span id="demo-spin-text">Coba Putar Demo</span>
                        </button>
                    </div>

                    <p class="mt-3 text-center text-[11px] tracking-wide text-white/40">
                        Demo di halaman ini hanya simulasi. Sistem asli pakai server-side random.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     | ② CONFIG PREVIEW
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="mb-16">
        <div class="mx-auto max-w-6xl">
            <div class="mb-12 text-center">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Konfigurasi</p>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white md:text-4xl">
                    Hanya butuh 30 detik untuk setup.
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-gray-600 dark:text-gray-300">
                    Isi judul, peserta, sistem, dan durasi. Sistem lainnya berjalan otomatis.
                </p>
            </div>

            <div class="grid items-center gap-8 lg:grid-cols-2">
                {{-- Left: list --}}
                <div class="space-y-4">
                    @php
                        $configs = [
                            ['📝', 'Judul & Template Hasil', 'Contoh: "Sukses kepada {name}"'],
                            ['👥', 'Peserta 1–50',             'Ketik manual atau tempel dari Excel'],
                            ['⚖️', 'Sistem Pemilihan',         'Bobot Peluang atau Acak Rahasia'],
                            ['⏱️', 'Durasi Putaran',            'Pilih 10, 30, 60, atau 150 detik'],
                            ['🔊', 'Audio & Efek',              'Suara sistem, custom, atau tanpa suara'],
                        ];
                    @endphp

                    @foreach($configs as [$icon, $title, $desc])
                        <div class="keputusan-card flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                                    hover:border-cyan-500/40 hover:shadow-md
                                    dark:border-white/[0.08] dark:bg-white/[0.04] dark:shadow-none dark:backdrop-blur">
                            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl
                                        border border-cyan-200 bg-gradient-to-br from-cyan-50 to-violet-50 text-xl
                                        dark:border-cyan-400/20 dark:from-cyan-500/15 dark:to-violet-500/15">
                                {{ $icon }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $title }}</p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: mockup --}}
                <div class="relative">
                    <div class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-cyan-500/10 to-violet-500/10 blur-2xl"></div>
                    <div class="relative rounded-3xl border border-gray-200 bg-white p-6 shadow-xl
                                dark:border-white/[0.1] dark:bg-[#0a0d1a]/90 dark:shadow-2xl dark:backdrop-blur-sm">

                        {{-- Mockup header --}}
                        <div class="mb-5 flex items-center gap-2 border-b border-gray-200 pb-4 dark:border-white/[0.06]">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500/70"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500/70"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/70"></span>
                            <span class="ml-3 font-mono text-xs text-gray-500 dark:text-white/40">/keputusan/roda/create</span>
                        </div>

                        <div class="space-y-4">
                            {{-- Field: template --}}
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-white/40">
                                    Template Hasil
                                </label>
                                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-sm text-gray-800
                                            dark:border-white/[0.08] dark:bg-white/[0.04] dark:text-white/80">
                                    Sukses kepada <span class="text-cyan-600 dark:text-cyan-300">{name}</span>!
                                </div>
                            </div>

                            {{-- Field: peserta --}}
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-white/40">
                                    Peserta
                                </label>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(['Ahmad', 'Bima', 'Citra', 'Dimas', 'Eka'] as $name)
                                        <span class="rounded-md border border-cyan-300 bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-800
                                                     dark:border-cyan-400/20 dark:bg-cyan-500/10 dark:text-cyan-200">
                                            {{ $name }}
                                        </span>
                                    @endforeach
                                    <span class="rounded-md border border-gray-200 bg-gray-100 px-2.5 py-1 text-xs text-gray-500
                                                 dark:border-white/[0.08] dark:bg-white/[0.04] dark:text-white/40">
                                        +45 lainnya
                                    </span>
                                </div>
                            </div>

                            {{-- Field: sistem --}}
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-white/40">
                                    Sistem
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-lg border-2 border-cyan-500 bg-cyan-50 px-3 py-2 text-center text-xs font-bold text-cyan-800
                                                dark:border-cyan-400/40 dark:bg-cyan-500/15 dark:text-white">
                                        ⚖️ Bobot Peluang
                                    </div>
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-center text-xs text-gray-500
                                                dark:border-white/[0.08] dark:bg-white/[0.04] dark:text-white/40">
                                        🎲 Acak Rahasia
                                    </div>
                                </div>
                            </div>

                            {{-- Field: durasi --}}
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-white/40">
                                    Durasi
                                </label>
                                <div class="grid grid-cols-4 gap-1.5">
                                    @foreach(['10s', '30s', '60s', '150s'] as $i => $d)
                                        <div class="rounded-md border px-2 py-1.5 text-center font-mono text-[11px]
                                                    {{ $i === 2
                                                        ? 'border-violet-500 bg-violet-50 font-bold text-violet-800 dark:border-violet-400/40 dark:bg-violet-500/20 dark:text-white'
                                                        : 'border-gray-200 bg-gray-50 text-gray-500 dark:border-white/[0.08] dark:bg-white/[0.04] dark:text-white/40' }}">
                                            {{ $d }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- CTA mockup --}}
                            <div class="pt-2">
                                <div class="keputusan-cta w-full rounded-xl py-3 text-center text-sm font-bold
                                            shadow-[0_10px_30px_-10px_rgba(34,211,238,0.5)]">
                                    🎡 Simpan &amp; Buat Link
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     | ③ CARA KERJA
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="mb-16">
        <div class="mx-auto max-w-6xl">
            <div class="mb-12 text-center">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Cara Kerja</p>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white md:text-4xl">
                    Empat langkah, satu keputusan.
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                @php
                    $steps = [
                        ['01', '👥', 'Masukkan Nama', 'Tambahkan 1–50 peserta ke dalam roda.'],
                        ['02', '⚙️', 'Atur Sistem',   'Bobot Peluang atau Acak Rahasia.'],
                        ['03', '🎯', 'Putar',         'Atur durasi 10–150 detik dan mulai putaran.'],
                        ['04', '🏆', 'Hasil',         'Jarum berhenti tepat pada nama terpilih.'],
                    ];
                @endphp

                @foreach($steps as [$num, $icon, $title, $desc])
                    <div class="keputusan-card group relative rounded-2xl border border-gray-200 bg-white p-6 shadow-sm
                                hover:border-cyan-500/50 hover:shadow-lg
                                dark:border-white/[0.08] dark:bg-white/[0.04] dark:shadow-none dark:backdrop-blur">
                        <div class="mb-5 flex items-center justify-between">
                            <span class="text-3xl" aria-hidden="true">{{ $icon }}</span>
                            <span class="font-mono text-xs font-bold text-cyan-600 dark:text-cyan-400">{{ $num }}</span>
                        </div>
                        <h3 class="mb-2 text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     | ④ FITUR
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="mb-16">
        <div class="mx-auto max-w-5xl">
            <div class="mb-10 text-center">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-violet-600 dark:text-violet-400">Fitur Utama</p>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white md:text-3xl">
                    Semua yang Anda butuhkan, tanpa kerumitan.
                </h2>
            </div>

            @php
                $chips = [
                    '⚖️ Bobot Peluang', '🎲 Acak Rahasia', '👥 Hingga 50 Peserta',
                    '⏱ 10–150 Detik', '🔊 Efek Suara', '📱 Mobile Friendly',
                    '🔗 Link Publik', '👤 Satu Operator',
                ];
            @endphp

            <ul class="flex flex-wrap items-center justify-center gap-3 list-none">
                @foreach($chips as $chip)
                    <li class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-4 py-2
                               text-sm font-medium text-gray-700 shadow-sm
                               transition-all duration-200
                               hover:border-cyan-500/50 hover:bg-cyan-50 hover:text-cyan-800
                               dark:border-white/[0.1] dark:bg-white/[0.04] dark:text-white/80 dark:shadow-none
                               dark:hover:border-cyan-400/30 dark:hover:bg-white/[0.08] dark:hover:text-white">
                        {{ $chip }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     | ⑤ ONE OPERATOR
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="mb-16">
        <div class="mx-auto max-w-5xl">
            <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-8 shadow-sm
                        dark:border-white/[0.08] dark:bg-white/[0.04] dark:shadow-none dark:backdrop-blur
                        md:p-12">
                <div class="absolute left-0 right-0 top-0 h-px bg-gradient-to-r from-transparent via-cyan-500 to-transparent dark:via-cyan-400/60"></div>

                <div class="grid items-center gap-10 md:grid-cols-2">
                    <div>
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-50 px-3 py-1
                                    dark:border-cyan-400/20 dark:bg-cyan-500/10">
                            <span class="text-sm" aria-hidden="true">👥</span>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-800 dark:text-cyan-300">
                                One Operator
                            </span>
                        </div>

                        <h2 class="mb-4 text-2xl font-black text-gray-900 dark:text-white md:text-3xl">
                            Satu pengendali,
                            <span class="bg-gradient-to-r from-cyan-600 to-violet-600 bg-clip-text text-transparent
                                         dark:from-cyan-400 dark:to-violet-400">
                                banyak penonton.
                            </span>
                        </h2>

                        <p class="mb-6 leading-relaxed text-gray-600 dark:text-gray-300">
                            Hanya <span class="font-semibold text-gray-900 dark:text-white">satu orang</span> yang mengendalikan roda.
                            Peserta lain cukup membuka link dan melihat roda berputar
                            secara <span class="font-semibold text-cyan-600 dark:text-cyan-300">real-time</span>.
                        </p>

                        <ul class="flex flex-wrap gap-2 list-none text-xs text-gray-600 dark:text-gray-400">
                            @foreach(['🏫 Kelas', '🎤 Presentasi', '🎮 Game', '🎁 Pengundian', '👥 Pemilihan Kelompok'] as $case)
                                <li class="rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1
                                           dark:border-white/[0.08] dark:bg-white/[0.05]">
                                    {{ $case }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="relative">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 shadow-sm
                                    dark:border-white/[0.08] dark:bg-black/40 dark:shadow-none">
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl
                                                border-2 border-cyan-500 bg-cyan-50 text-2xl
                                                dark:border-cyan-400/30 dark:bg-cyan-500/15"
                                         aria-hidden="true">🎛️</div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-300">Operator</p>
                                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Tekan PUTAR</p>
                                </div>
                                <div>
                                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl
                                                border-2 border-violet-500 bg-violet-50 text-2xl
                                                dark:border-violet-400/30 dark:bg-violet-500/15"
                                         aria-hidden="true">👁️</div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-300">Viewer</p>
                                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Lihat hasil</p>
                                </div>
                            </div>

                            <div class="my-4 flex items-center justify-center">
                                <div class="h-px flex-1 bg-gradient-to-r from-cyan-500/40 via-gray-300 to-violet-500/40 dark:via-white/20"></div>
                                <span class="px-3 text-xs font-bold tracking-widest text-gray-600 dark:text-gray-400">SYNC</span>
                                <div class="h-px flex-1 bg-gradient-to-r from-violet-500/40 via-gray-300 to-cyan-500/40 dark:via-white/20"></div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-gradient-to-r from-cyan-50 to-violet-50 p-3 text-center
                                        dark:border-white/[0.08] dark:from-cyan-500/10 dark:to-violet-500/10">
                                <p class="mb-1 text-[11px] uppercase tracking-wider text-gray-600 dark:text-gray-400">Hasil</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">🎉 Pemenang tampil serentak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     | ⑥ HASIL PREVIEW
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="mb-16">
        <div class="mx-auto max-w-3xl text-center">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-400">Hasil Akhir</p>
            <h2 class="mb-10 text-2xl font-black text-gray-900 dark:text-white md:text-3xl">
                Ketika roda berhenti, semua jelas.
            </h2>

            <div class="relative mx-auto max-w-md overflow-hidden rounded-3xl border border-gray-200 bg-white p-10 shadow-xl
                        dark:border-white/[0.1] dark:bg-white/[0.04] dark:shadow-none dark:backdrop-blur">
                <div class="absolute -inset-1 -z-10 bg-gradient-to-r from-cyan-500/20 via-violet-500/20 to-pink-500/20 opacity-50 blur-2xl"></div>

                <div id="result-emoji" class="mb-4 text-6xl" aria-hidden="true">🎉</div>

                <p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-cyan-600 dark:text-cyan-300">
                    Sukses Kepada
                </p>

                <h3 id="result-name-display"
                    class="mb-6 bg-gradient-to-r from-cyan-600 via-gray-800 to-violet-600 bg-clip-text text-4xl font-black text-transparent
                           transition-all duration-300
                           dark:from-cyan-300 dark:via-white dark:to-violet-300
                           md:text-5xl">
                    BIMA
                </h3>

                <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/40 bg-emerald-50 px-3 py-1.5
                            dark:border-emerald-400/30 dark:bg-emerald-500/10">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400" aria-hidden="true"></span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                        Terpilih
                    </span>
                </div>
            </div>

            <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                Nama pemenang akan muncul di posisi ini setelah roda selesai berputar.
            </p>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     | ⑦ FINAL CTA
     * ═══════════════════════════════════════════════════════════ --}}
    <section class="mb-8">
        <div class="mx-auto max-w-4xl">
            <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-gradient-to-br from-cyan-50 via-white to-violet-50 px-8 py-14 text-center shadow-sm
                        dark:border-white/[0.1] dark:from-cyan-500/10 dark:via-transparent dark:to-violet-500/10 dark:bg-transparent dark:shadow-none
                        md:py-16">
                <div class="absolute -top-24 left-1/2 h-[400px] w-[400px] -translate-x-1/2 rounded-full bg-cyan-500/20 blur-[100px]" aria-hidden="true"></div>

                <div class="relative">
                    <h2 class="mb-4 text-3xl font-black leading-tight text-gray-900 dark:text-white md:text-4xl">
                        Siap menentukan pilihan?
                    </h2>
                    <p class="mx-auto mb-8 max-w-md text-gray-600 dark:text-gray-300">
                        Buat roda pertama Anda sekarang. Tanpa login, tanpa biaya.
                    </p>

                    <a href="{{ route('keputusan.roda.create') }}"
                       aria-label="Mulai buat roda keberuntungan sekarang"
                       class="keputusan-cta group inline-flex items-center gap-2 rounded-2xl px-8 py-4
                              text-base font-bold
                              shadow-[0_10px_40px_-10px_rgba(34,211,238,0.6)]
                              focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300">
                        <span class="text-xl" aria-hidden="true">🎡</span>
                        <span>Mulai Sekarang</span>
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <p class="mt-5 text-xs tracking-wide text-gray-500 dark:text-gray-400">
                        ⚡ Gratis • 🚪 Tanpa Login • 🎯 Maks. 50 Peserta
                    </p>
                </div>
            </div>
        </div>
    </section>

    </main>
</div>

@endsection

{{-- ═══════════════════════════════════════════════════════════
 | SCRIPTS — Interactive Demo Wheel (mengikuti pola kontak)
 * ═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
(function () {
    'use strict';

    const WHEEL_NAMES  = ['Ahmad', 'Bima', 'Citra', 'Dimas', 'Eka'];
    const WHEEL_COLORS = ['#0891b2', '#7c3aed', '#db2777', '#f59e0b', '#10b981'];
    const SPIN_DURATION_MS = 4500;
    const PREFERS_REDUCED  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const svg       = document.getElementById('demo-wheel');
    const segGroup  = document.getElementById('demo-wheel-segments');
    const spinBtn   = document.getElementById('demo-spin-btn');
    const spinText  = document.getElementById('demo-spin-text');
    const spinIcon  = document.getElementById('demo-spin-icon');
    const resLabel  = document.getElementById('demo-result-label');
    const resName   = document.getElementById('demo-result-name');
    const resDisplay = document.getElementById('result-name-display');
    const resEmoji  = document.getElementById('result-emoji');

    if (!svg || !segGroup || !spinBtn) return;

    const CX = 200, CY = 200, RADIUS = 175;
    let currentRotation = 0;
    let isSpinning = false;

    function polarToCartesian(cx, cy, r, angleDeg) {
        const a = (angleDeg - 90) * Math.PI / 180;
        return { x: cx + r * Math.cos(a), y: cy + r * Math.sin(a) };
    }

    /* ── Render wheel ── */
    function renderWheel() {
        const n = WHEEL_NAMES.length;
        const anglePerSeg = 360 / n;
        let html = '';

        for (let i = 0; i < n; i++) {
            const startA = i * anglePerSeg;
            const endA   = (i + 1) * anglePerSeg;
            const midA   = startA + anglePerSeg / 2;

            const p1 = polarToCartesian(CX, CY, RADIUS, startA);
            const p2 = polarToCartesian(CX, CY, RADIUS, endA);
            const largeArc = anglePerSeg > 180 ? 1 : 0;

            const path = `M ${CX} ${CY} L ${p1.x} ${p1.y} A ${RADIUS} ${RADIUS} 0 ${largeArc} 1 ${p2.x} ${p2.y} Z`;
            html += `<path d="${path}" fill="${WHEEL_COLORS[i % WHEEL_COLORS.length]}"
                           stroke="rgba(255,255,255,0.35)" stroke-width="2"/>`;

            // Label dengan rotasi tangensial (flip untuk readability)
            const labelR = RADIUS * 0.62;
            const labelPt = polarToCartesian(CX, CY, labelR, midA);
            let rotation = midA;
            if (midA > 90 && midA < 270) rotation -= 180;

            html += `<text x="${labelPt.x}" y="${labelPt.y}"
                           transform="rotate(${rotation} ${labelPt.x} ${labelPt.y})"
                           text-anchor="middle"
                           dominant-baseline="central"
                           font-family="ui-sans-serif, system-ui, -apple-system, sans-serif"
                           font-weight="800"
                           font-size="18"
                           fill="#ffffff"
                           style="pointer-events:none; text-shadow: 0 1px 3px rgba(0,0,0,0.35);">
                           ${WHEEL_NAMES[i]}
                    </text>`;
        }

        segGroup.innerHTML = html;
    }

    /* ── Spin ── */
    function spin() {
        if (isSpinning) return;

        const winnerIdx = Math.floor(Math.random() * WHEEL_NAMES.length);
        const n = WHEEL_NAMES.length;
        const anglePerSeg = 360 / n;

        const extraTurns = 6 + Math.floor(Math.random() * 3);
        const jitter = (Math.random() - 0.5) * anglePerSeg * 0.5;
        const targetRotation = currentRotation
            + extraTurns * 360
            + (360 - (winnerIdx * anglePerSeg + anglePerSeg / 2))
            - (currentRotation % 360)
            + jitter;

        const delta = ((targetRotation - currentRotation) % 360 + 360) % 360;
        currentRotation += extraTurns * 360 + delta;

        isSpinning = true;
        spinBtn.disabled = true;
        spinText.textContent = 'Memutar...';
        spinIcon.textContent = '⏳';
        resLabel.textContent = 'Sedang memutar...';
        resName.textContent = '...';
        resEmoji.textContent = '🎡';

        const duration = PREFERS_REDUCED ? 800 : SPIN_DURATION_MS;
        svg.style.transition = `transform ${duration}ms cubic-bezier(0.17, 0.67, 0.16, 1)`;
        svg.style.transform = `rotate(${currentRotation}deg)`;

        const onFinish = () => {
            isSpinning = false;
            spinBtn.disabled = false;
            spinText.textContent = 'Putar Lagi';
            spinIcon.textContent = '🔄';

            const winner = WHEEL_NAMES[winnerIdx];
            resLabel.textContent = 'Pemenang:';
            resName.textContent = winner;

            resDisplay.textContent = winner.toUpperCase();
            resEmoji.textContent = '🎉';

            playChime();
            svg.removeEventListener('transitionend', onFinish);
        };

        svg.addEventListener('transitionend', onFinish, { once: true });
    }

    /* ── Sound ── */
    let audioCtx = null;
    function playChime() {
        try {
            if (PREFERS_REDUCED) return;
            audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
            const now = audioCtx.currentTime;

            [523.25, 659.25].forEach((freq, i) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0, now + i * 0.12);
                gain.gain.linearRampToValueAtTime(0.15, now + i * 0.12 + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.12 + 0.5);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(now + i * 0.12);
                osc.stop(now + i * 0.12 + 0.5);
            });
        } catch (e) {}
    }

    renderWheel();
    spinBtn.addEventListener('click', spin);
})();
</script>
@endpush
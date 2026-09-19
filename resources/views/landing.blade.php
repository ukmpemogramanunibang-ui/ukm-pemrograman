@extends('layouts.app')

@section('title', 'UKM Pemrograman UNICODE – Komunitas Teknologi Kampus')

@push('styles')
<style>
    html {
        scroll-behavior: smooth;
    }

    :root {
        --unicode-cyan: #06b6d4;
        --unicode-blue: #2563eb;
        --unicode-violet: #7c3aed;
        --unicode-dark: #0f172a;
    }

    body {
        overflow-x: hidden;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    img, iframe {
        -webkit-user-drag: none;
        user-drag: none;
    }

    /* =========================================================
       PERFORMANCE
       ========================================================= */
    .unicode-section {
        content-visibility: auto;
        contain-intrinsic-size: 700px;
    }

    .unicode-card {
        background: rgba(255, 255, 255, .82);
        border: 1px solid rgba(148, 163, 184, .22);
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
    }

    .dark .unicode-card {
        background: rgba(15, 23, 42, .78);
        border-color: rgba(148, 163, 184, .14);
        box-shadow: 0 15px 40px rgba(0, 0, 0, .22);
    }

    /* =========================================================
       REVEAL
       ========================================================= */
    .unicode-reveal {
        opacity: 0;
        transform: translateY(18px);
        transition:
            opacity 500ms cubic-bezier(.22,.61,.36,1),
            transform 500ms cubic-bezier(.22,.61,.36,1);
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
        width: 280px;
        height: 280px;
        border-radius: 9999px;
        filter: blur(70px);
        opacity: .18;
        pointer-events: none;
    }

    .unicode-orb.one { top: -100px; right: -70px; background: #06b6d4; }
    .unicode-orb.two { bottom: -130px; left: -70px; background: #7c3aed; }

    /* Container GIF hero — simetris di semua device */
    .hero-visual {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0.5rem;
    }

    .hero-visual-inner {
        position: relative;
        width: 100%;
        max-width: 220px;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.75rem;
        background: linear-gradient(135deg, rgba(6,182,212,.15), rgba(124,58,237,.15));
        border: 1px solid rgba(255,255,255,.12);
        box-shadow: 0 20px 60px rgba(2,6,23,.45);
        overflow: hidden;
    }

    .hero-visual-inner img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    @media (min-width: 640px) {
        .hero-visual-inner { max-width: 300px; }
    }

    @media (min-width: 1025px) {
        .hero-visual-inner { max-width: 380px; border-radius: 2rem; }
    }

    @media (min-width: 1280px) {
        .hero-visual-inner { max-width: 420px; }
    }

    .unicode-float {
        animation: unicodeFloat 4s ease-in-out infinite;
    }

    @keyframes unicodeFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-8px); }
    }

    /* =========================================================
       AUTO-RESPONSIVE SHELL
       ========================================================= */
    .unicode-shell {
        width: min(100% - 1rem, 100%);
        margin-inline: auto;
    }

    @media (min-width: 641px) {
        .unicode-shell { width: min(100% - 2rem, 960px); }
    }

    @media (min-width: 1025px) {
        .unicode-shell { width: min(100% - 2.5rem, 1200px); }
    }

    @media (min-width: 1536px) {
        .unicode-shell { width: min(100% - 4rem, 1600px); }
    }

    @media (min-width: 2000px) {
        .unicode-shell { width: min(100% - 6rem, 1900px); }
    }

    .unicode-section { margin-bottom: 3rem; }
    @media (min-width: 641px)  { .unicode-section { margin-bottom: 4rem; } }
    @media (min-width: 1025px) { .unicode-section { margin-bottom: 5rem; } }
    @media (min-width: 1536px) { .unicode-section { margin-bottom: 6rem; } }

    /* =========================================================
       BANNER SLIDER
       ========================================================= */
    .banner-slider {
        position: relative;
        overflow: hidden;
    }

    .banner-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 900ms ease-in-out;
        will-change: opacity;
    }

    .banner-slide:first-child {
        position: relative;
    }

    .banner-slide.is-active {
        opacity: 1;
    }

    .banner-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .banner-slide figcaption {
        transition: opacity 600ms ease, transform 600ms ease;
    }

    .banner-dot {
        transition: transform 220ms ease, background-color 220ms ease, width 220ms ease;
    }

    .banner-dot:hover {
        transform: scale(1.15);
    }

    .banner-dot.is-active {
        width: 1.75rem;
        background-color: rgba(255,255,255,.95);
    }

    /* =========================================================
       MAP CARD
       ========================================================= */
    .map-card iframe {
        pointer-events: none;
    }

    .map-card .map-overlay {
        transition: background-color 300ms ease, opacity 300ms ease;
    }

    .map-card:hover .map-overlay {
        background-color: rgba(0, 0, 0, .35);
    }

    .map-card .map-badge {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 260ms ease, transform 260ms ease;
    }

    .map-card:hover .map-badge {
        opacity: 1;
        transform: translateY(0);
    }

    /* =========================================================
       MEDIA FACADE (video)
       ========================================================= */
    .media-facade {
        position: relative;
        overflow: hidden;
        background: #020617;
        cursor: pointer;
    }

    .media-facade img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .media-play {
        position: absolute;
        inset: 50% auto auto 50%;
        transform: translate(-50%, -50%);
        width: 72px;
        height: 72px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.94);
        color: #0f172a;
        box-shadow: 0 15px 40px rgba(0,0,0,.28);
        transition:
            transform 180ms ease,
            box-shadow 180ms ease;
        z-index: 2;
    }

    .media-facade:hover .media-play {
        transform: translate(-50%, -50%) scale(1.06);
        box-shadow: 0 18px 50px rgba(0,0,0,.35);
    }

    /* =========================================================
       Reduced Motion
       ========================================================= */
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }

        .unicode-float,
        .unicode-reveal {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        .banner-slide {
            transition: none !important;
        }
    }
</style>
@endpush

@section('content')

<div class="unicode-shell py-5 md:py-8 lg:py-10">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section
        class="unicode-section unicode-hero rounded-[2rem] p-6 sm:p-8 md:p-12 lg:p-16 xl:p-20 text-white shadow-2xl unicode-reveal"
    >
        <div class="unicode-orb one"></div>
        <div class="unicode-orb two"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-5 gap-8 md:gap-10 items-center">

            {{-- Teks Hero --}}
            <div class="lg:col-span-3 text-center lg:text-left flex flex-col items-center lg:items-start">

                <div class="flex flex-wrap justify-center lg:justify-start gap-2 mb-5">
                    <span class="inline-flex items-center rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-xs md:text-sm font-bold">
                        UKM PEMROGRAMAN
                    </span>

                    <span class="inline-flex items-center rounded-full border border-yellow-300/30 bg-yellow-300/10 px-4 py-2 text-xs md:text-sm font-bold text-yellow-200">
                        UNICODE
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                    Belajar.
                    <span class="text-cyan-300">Berkarya.</span>
                    <br>
                    Berkolaborasi.
                    <span class="text-violet-300">Berprestasi.</span>
                </h1>

                <p class="mt-6 max-w-2xl text-base md:text-lg lg:text-xl text-white/80 leading-relaxed">
                    UKM Pemrograman UNICODE menjadi wadah mahasiswa
                    Universitas Bhakti Asih untuk belajar teknologi,
                    mengembangkan proyek, dan membangun pengalaman digital
                    bersama.
                </p>

                <div class="flex flex-wrap justify-center lg:justify-start gap-3 mt-8">
                    <a href="#pendaftaran"
                       class="inline-flex items-center justify-center rounded-full bg-cyan-500 px-6 py-3.5 font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-cyan-400">
                        Gabung UNICODE
                    </a>

                    <a href="#tentang"
                       class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-6 py-3.5 font-bold text-white transition hover:bg-white/15">
                        Tentang Kami
                    </a>
                </div>
            </div>

            {{-- Visual GIF — simetris di semua device --}}
            <div class="lg:col-span-2 hero-visual">
                <div class="hero-visual-inner unicode-float">
                    <img
                        src="{{ asset('images/gim.gif') }}"
                        alt="Coding UNICODE"
                        width="420"
                        height="420"
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                    >
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         BANNER SLIDER (2 banner, caption berbeda tiap slide)
    ====================================================== --}}
    <section class="unicode-section unicode-reveal">

        <div
            id="banner-slider"
            class="banner-slider rounded-3xl shadow-2xl aspect-[16/9] sm:aspect-[16/7] md:aspect-[16/6]"
        >
            {{-- Slide 1 --}}
            <figure class="banner-slide is-active" data-slide="0">
                <img
                    src="{{ asset('images/banner1.jpeg') }}"
                    alt="Banner UNICODE 1"
                    width="1600"
                    height="666"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
                <figcaption class="absolute inset-0 flex items-end bg-gradient-to-t from-black/75 via-black/15 to-transparent p-6 md:p-10 lg:p-14 z-10">
                    <div class="text-white">
                        <span class="text-xs md:text-base font-bold uppercase tracking-[.25em] text-cyan-300">
                            #UNICODE
                        </span>
                        <h2 class="mt-2 text-xl sm:text-2xl md:text-4xl lg:text-5xl font-black leading-tight">
                            Teknologi dimulai dari satu baris kode.
                        </h2>
                    </div>
                </figcaption>
            </figure>

            {{-- Slide 2 --}}
            <figure class="banner-slide" data-slide="1">
                <img
                    src="{{ asset('images/banner.jpeg') }}"
                    alt="Banner UNICODE 2"
                    width="1600"
                    height="666"
                    loading="lazy"
                    decoding="async"
                >
                <figcaption class="absolute inset-0 flex items-end bg-gradient-to-t from-black/75 via-black/15 to-transparent p-6 md:p-10 lg:p-14 z-10">
                    <div class="text-white">
                        <span class="text-xs md:text-base font-bold uppercase tracking-[.25em] text-violet-300">
                            #UNICODE
                        </span>
                        <h2 class="mt-2 text-xl sm:text-2xl md:text-4xl lg:text-5xl font-black leading-tight">
                            Belajar, berkarya, dan tumbuh bersama.
                        </h2>
                    </div>
                </figcaption>
            </figure>

            {{-- Indicator dots --}}
            <div class="absolute bottom-4 right-4 z-20 flex gap-2">
                <button
                    type="button"
                    class="banner-dot is-active h-3 w-3 rounded-full bg-white/40"
                    data-index="0"
                    aria-label="Slide 1"
                ></button>
                <button
                    type="button"
                    class="banner-dot h-3 w-3 rounded-full bg-white/40"
                    data-index="1"
                    aria-label="Slide 2"
                ></button>
            </div>
        </div>

       
    </section>

    {{-- =====================================================
         TENTANG
    ====================================================== --}}
    <section id="tentang" class="unicode-section scroll-mt-24 unicode-reveal">

        <div class="max-w-5xl mx-auto text-center">
            <span class="inline-block rounded-full bg-cyan-100 px-4 py-2 text-xs font-bold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                TENTANG UNICODE
            </span>

            <h2 class="mt-4 text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white">
                Tempat mahasiswa berkembang lewat teknologi.
            </h2>

            <p class="mt-5 text-base md:text-lg lg:text-xl text-gray-600 dark:text-gray-300 leading-relaxed max-w-4xl mx-auto">
                UNICODE bukan sekadar tempat belajar coding.
                Kami membangun lingkungan untuk mencoba,
                gagal, memperbaiki, berkolaborasi, dan menghasilkan karya
                bersama mahasiswa Universitas Bhakti Asih.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="unicode-card rounded-2xl p-5 text-center">
                <div class="text-3xl">🤝</div>
                <h3 class="mt-3 font-bold text-slate-900 dark:text-white">Kolaborasi</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Berkarya bersama.</p>
            </div>
            <div class="unicode-card rounded-2xl p-5 text-center">
                <div class="text-3xl">💡</div>
                <h3 class="mt-3 font-bold text-slate-900 dark:text-white">Inovasi</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Berani mencoba.</p>
            </div>
            <div class="unicode-card rounded-2xl p-5 text-center">
                <div class="text-3xl">📈</div>
                <h3 class="mt-3 font-bold text-slate-900 dark:text-white">Pertumbuhan</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Skill meningkat.</p>
            </div>
            <div class="unicode-card rounded-2xl p-5 text-center">
                <div class="text-3xl">🚀</div>
                <h3 class="mt-3 font-bold text-slate-900 dark:text-white">Prestasi</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bawa karya lebih jauh.</p>
            </div>
        </div>
    </section>

    {{-- =====================================================
         DIVISI
    ====================================================== --}}
    <section class="unicode-section unicode-reveal">

        <div class="text-center">
            <span class="text-sm font-bold tracking-widest text-cyan-600 dark:text-cyan-400">
                ORGANISASI
            </span>
            <h2 class="mt-2 text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                Divisi UNICODE
            </h2>
        </div>

        <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">

            <article class="unicode-card rounded-3xl p-7 md:p-10 lg:p-12 min-h-[260px] sm:min-h-[300px] lg:min-h-[340px] flex flex-col justify-start">
                <div class="text-4xl md:text-5xl">💻</div>
                <h3 class="mt-5 text-xl md:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                    Divisi Pengembang
                </h3>
                <p class="mt-3 text-gray-600 dark:text-gray-300 leading-relaxed text-sm md:text-base lg:text-lg">
                    Berfokus pada coding, website, aplikasi,
                    eksplorasi teknologi, dan pengembangan proyek digital
                    bersama anggota UNICODE.
                </p>
            </article>

            <article class="unicode-card rounded-3xl p-7 md:p-10 lg:p-12 min-h-[260px] sm:min-h-[300px] lg:min-h-[340px] flex flex-col justify-start">
                <div class="text-4xl md:text-5xl">🎨</div>
                <h3 class="mt-5 text-xl md:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                    Divisi PDD
                </h3>
                <p class="mt-3 text-gray-600 dark:text-gray-300 leading-relaxed text-sm md:text-base lg:text-lg">
                    Mengelola publikasi, dokumentasi, desain visual,
                    media sosial, dan identitas digital UNICODE
                    agar setiap karya tersampaikan dengan baik.
                </p>
            </article>
        </div>
    </section>

    {{-- =====================================================
         VIDEO PROFIL
    ====================================================== --}}
    <section class="unicode-section unicode-reveal">

        <div class="text-center mb-8">
            <span class="text-sm font-bold tracking-widest text-red-500">VIDEO</span>
            <h2 class="mt-2 text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                Profil Fasilitas Unibang
            </h2>
        </div>

        <div class="video-shell">
            <div
                class="media-facade aspect-video rounded-3xl shadow-2xl"
                data-video="a0Xf12ACVeE"
                role="button"
                tabindex="0"
                aria-label="Putar video profil UNICODE"
            >
                <img
                    src="https://img.youtube.com/vi/a0Xf12ACVeE/maxresdefault.jpg"
                    alt="Thumbnail Video Profil UKM Pemrograman UNICODE"
                    loading="lazy"
                    decoding="async"
                    onerror="this.onerror=null;this.src='https://img.youtube.com/vi/a0Xf12ACVeE/hqdefault.jpg';"
                    class="absolute inset-0 w-full h-full object-cover"
                >

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/10 flex flex-col items-center justify-center text-center px-6">
                    <div class="media-play">▶</div>
                    <p class="mt-24 text-white font-bold text-lg md:text-xl drop-shadow-lg">
                        Tonton Video Profil
                    </p>
                    <p class="mt-1 text-sm text-white/80 drop-shadow">
                        Video dimuat hanya ketika Anda membutuhkannya.
                    </p>
                </div>
            </div>
        </div>
       
    </section>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
       ANTI-INSPECTION (klik kanan, F12, Ctrl+S/P/U, DevTools)
       ===================================================== */

   
    // 2. Blokir shortcut keyboard
    document.addEventListener('keydown', (e) => {
        const k = e.key ? e.key.toLowerCase() : '';

        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }

        // Ctrl+Shift+I / J / C  (DevTools, Console, Inspect)
        if (e.ctrlKey && e.shiftKey && (k === 'i' || k === 'j' || k === 'c')) {
            e.preventDefault();
            return false;
        }

        // Ctrl+U (View Source)
        if (e.ctrlKey && !e.shiftKey && k === 'u') {
            e.preventDefault();
            return false;
        }

        // Ctrl+S (Save Page)
        if (e.ctrlKey && !e.shiftKey && k === 's') {
            e.preventDefault();
            return false;
        }

        // Ctrl+P (Print)
        if (e.ctrlKey && !e.shiftKey && k === 'p') {
            e.preventDefault();
            return false;
        }

        // Ctrl+A (Select All) — opsional, aktifkan jika perlu
        // if (e.ctrlKey && !e.shiftKey && k === 'a') {
        //     e.preventDefault();
        //     return false;
        // }

        // Cmd+Opt+I / Cmd+Opt+J / Cmd+Opt+C (macOS DevTools)
        if (e.metaKey && e.altKey && (k === 'i' || k === 'j' || k === 'c')) {
            e.preventDefault();
            return false;
        }

        // Cmd+S / Cmd+P / Cmd+U (macOS)
        if (e.metaKey && !e.altKey && (k === 's' || k === 'p' || k === 'u')) {
            e.preventDefault();
            return false;
        }
    });

    // 3. Blokir drag gambar
    document.addEventListener('dragstart', (e) => {
        if (e.target && e.target.tagName === 'IMG') {
            e.preventDefault();
        }
    });


    /* =====================================================
       NATIVE REVEAL ANIMATION
       ===================================================== */
    const revealItems = document.querySelectorAll('.unicode-reveal');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        }, {
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.08
        });

        revealItems.forEach(item => observer.observe(item));
    } else {
        revealItems.forEach(item => item.classList.add('is-visible'));
    }


    /* =====================================================
       BANNER SLIDER — 2 banner bergantian tiap 3 detik
       Setiap slide punya caption sendiri yang ikut berganti
       ===================================================== */
    const bannerSlides = document.querySelectorAll('#banner-slider .banner-slide');
    const bannerDots   = document.querySelectorAll('#banner-slider .banner-dot');
    let bannerCurrent  = 0;
    let bannerTimer    = null;
    const BANNER_INTERVAL = 3000;

    function showBanner(index) {
        bannerSlides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === index);
        });

        bannerDots.forEach((dot, i) => {
            const active = i === index;
            dot.classList.toggle('is-active', active);
            dot.classList.toggle('bg-white/40', !active);
            dot.classList.toggle('bg-white/95', active);
        });

        bannerCurrent = index;
    }

    function nextBanner() {
        if (bannerSlides.length < 2) return;
        showBanner((bannerCurrent + 1) % bannerSlides.length);
    }

    function startBannerTimer() {
        stopBannerTimer();
        if (bannerSlides.length > 1) {
            bannerTimer = setInterval(nextBanner, BANNER_INTERVAL);
        }
    }

    function stopBannerTimer() {
        if (bannerTimer) {
            clearInterval(bannerTimer);
            bannerTimer = null;
        }
    }

    if (bannerSlides.length > 1) {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopBannerTimer();
            } else {
                startBannerTimer();
            }
        });

        bannerDots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const index = parseInt(dot.dataset.index, 10) || 0;
                showBanner(index);
                startBannerTimer();
            });
        });

        // inisialisasi
        showBanner(0);
        startBannerTimer();
    }


    /* =====================================================
       LAZY YOUTUBE (facade → embed on click)
       ===================================================== */
    const videoFacade = document.querySelector('[data-video]');
    if (videoFacade) {
        const playVideo = () => {
            const videoId = videoFacade.dataset.video;
            if (!videoId || videoId === 'VIDEO_ID_UNICODE') return;

            const iframe = document.createElement('iframe');
            iframe.src = `https://www.youtube.com/embed/${encodeURIComponent(videoId)}?rel=0&autoplay=1`;
            iframe.title = 'Video Profil UKM Pemrograman UNICODE';
            iframe.loading = 'lazy';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;
            iframe.className = 'absolute inset-0 h-full w-full border-0';

            videoFacade.innerHTML = '';
            videoFacade.appendChild(iframe);
        };

        videoFacade.addEventListener('click', playVideo);
        videoFacade.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                playVideo();
            }
        });
    }


    /* =====================================================
       SMOOTH ANCHOR
       ===================================================== */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', event => {
            const id = anchor.getAttribute('href');
            if (!id || id === '#') return;

            const target = document.querySelector(id);
            if (!target) return;

            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

});
</script>
@endpush
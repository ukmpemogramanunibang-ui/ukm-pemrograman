{{-- resources/views/dokumentasi.blade.php --}}
@extends('layouts.app')

@section('title', 'Dokumentasi – UKM Pemrograman UNICODE')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    /* =========================================================
       CONTAINER
       ========================================================= */
    .doc-shell {
        width: min(100% - 1rem, 100%);
        margin-inline: auto;
    }
    @media (min-width: 640px)  { .doc-shell { width: min(100% - 2rem, 1100px); } }
    @media (min-width: 1024px) { .doc-shell { width: min(100% - 3rem, 1380px); } }
    @media (min-width: 1536px) { .doc-shell { width: min(100% - 5rem, 1700px); } }

    /* =========================================================
       REVEAL
       ========================================================= */
    .doc-reveal {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 500ms cubic-bezier(.22,.61,.36,1),
                    transform 500ms cubic-bezier(.22,.61,.36,1);
    }
    .doc-reveal.is-visible { opacity: 1; transform: translateY(0); }

    /* =========================================================
       TOP BANNER
       ========================================================= */
    .doc-banner { position: relative; width: 100%; overflow: hidden; background: #020617; }
    .doc-banner img {
        display: block; width: 100%; min-height: 240px; max-height: 620px; object-fit: cover;
    }
    @media (max-width: 640px) { .doc-banner img { min-height: 230px; } }

    .doc-banner-overlay {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        padding: 1.5rem; text-align: center;
        background: linear-gradient(to bottom, rgba(2,6,23,.22), rgba(2,6,23,.62) 55%, rgba(2,6,23,.95));
    }
    .doc-banner-label {
        display: inline-flex; align-items: center; justify-content: center;
        padding: .5rem .9rem; border-radius: 9999px;
        border: 1px solid rgba(103,232,249,.22);
        background: rgba(8,145,178,.10);
        color: #67e8f9; font-size: .72rem; font-weight: 800;
        letter-spacing: .18em; text-transform: uppercase;
    }

    /* =========================================================
       FOLLOW INSTAGRAM
       ========================================================= */
    .follow-wrap { display: flex; justify-content: center; }
    .follow-ig {
        display: inline-flex; align-items: center; justify-content: center; gap: .6rem;
        min-height: 48px; padding: .75rem 1.4rem; border-radius: 9999px;
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        color: #fff; font-size: .9rem; font-weight: 800;
        box-shadow: 0 12px 35px rgba(139,92,246,.22);
        transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
    }
    .follow-ig:hover { transform: translateY(-2px); filter: brightness(1.05); box-shadow: 0 16px 42px rgba(139,92,246,.32); }
    .follow-ig:active { transform: translateY(0); }

    /* =========================================================
       DOCUMENTATION CARD
       ========================================================= */
    .documentation-card {
        position: relative; overflow: hidden; border-radius: 2rem;
        background: linear-gradient(145deg, rgba(15,23,42,.98), rgba(15,23,42,.92));
        border: 1px solid rgba(148,163,184,.13);
        box-shadow: 0 25px 70px rgba(0,0,0,.28);
    }
    .documentation-card::before {
        content: ""; position: absolute; inset: 0; pointer-events: none;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.10), transparent 34%),
            radial-gradient(circle at bottom left, rgba(124,58,237,.08), transparent 32%);
    }
    .documentation-inner { position: relative; z-index: 1; }
    .documentation-heading { max-width: 760px; margin-inline: auto; text-align: center; }
    .documentation-kicker {
        font-size: .72rem; font-weight: 800; letter-spacing: .22em;
        text-transform: uppercase; color: #67e8f9;
    }

    /* =========================================================
       GALLERY ROTATOR
       ========================================================= */
    .gallery-rotator {
        position: relative; width: 100%; aspect-ratio: 16 / 9;
        overflow: hidden; border-radius: 1.5rem; background: #020617; isolation: isolate;
    }
    @media (min-width: 768px)  { .gallery-rotator { aspect-ratio: 18 / 9; border-radius: 1.75rem; } }
    @media (min-width: 1536px) { .gallery-rotator { aspect-ratio: 21 / 9; } }

    .gallery-slide {
        position: absolute; inset: 0; opacity: 0; transform: scale(1.03); visibility: hidden;
        transition: opacity 850ms ease,
                    transform 1600ms cubic-bezier(.2,.65,.25,1),
                    visibility 850ms ease;
        will-change: opacity, transform;
    }
    .gallery-slide.is-active { opacity: 1; transform: scale(1); visibility: visible; }
    .gallery-slide img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .gallery-slide::after {
        content: ""; position: absolute; inset: 0; pointer-events: none;
        background: linear-gradient(to top, rgba(2,6,23,.86), rgba(2,6,23,.12) 54%, rgba(2,6,23,.10));
    }
    .gallery-caption {
        position: absolute; left: 0; right: 0; bottom: 0; z-index: 2;
        padding: 3rem 1.25rem 1.25rem; color: #fff;
    }
    @media (min-width: 768px) { .gallery-caption { padding: 5rem 2rem 2rem; } }
    .gallery-caption-title { font-size: 1rem; font-weight: 800; line-height: 1.35; }
    @media (min-width: 768px) { .gallery-caption-title { font-size: 1.35rem; } }
    .gallery-caption-number {
        margin-top: .35rem; font-size: .72rem; color: rgba(255,255,255,.58);
        letter-spacing: .12em; text-transform: uppercase;
    }

    /* Dots */
    .gallery-dots {
        position: absolute; z-index: 5; left: 50%; bottom: 1rem; transform: translateX(-50%);
        display: flex; align-items: center; gap: .45rem;
        padding: .45rem .65rem; border-radius: 9999px;
        background: rgba(2,6,23,.45);
        border: 1px solid rgba(255,255,255,.08);
        backdrop-filter: blur(6px);
    }
    .gallery-dot {
        width: .45rem; height: .45rem; padding: 0; border: 0; border-radius: 9999px;
        background: rgba(255,255,255,.34); cursor: pointer;
        transition: width 220ms ease, background-color 220ms ease, transform 220ms ease;
    }
    .gallery-dot.is-active { width: 1.4rem; background: rgba(255,255,255,.94); }
    .gallery-dot:hover { transform: scale(1.12); }

    /* =========================================================
       ABOUT CARD
       ========================================================= */
    .about-card {
        position: relative; overflow: hidden; border-radius: 2rem;
        background: linear-gradient(145deg, rgba(15,23,42,.96), rgba(17,24,39,.97));
        border: 1px solid rgba(148,163,184,.13);
        box-shadow: 0 22px 60px rgba(0,0,0,.25);
    }
    .about-line {
        width: 60px; height: 4px; border-radius: 9999px;
        background: linear-gradient(90deg, #06b6d4, #2563eb, #7c3aed);
    }

    /* Layout: HP → kolom (teks atas, gambar bawah)
              Laptop → 2 kolom (teks kiri, gambar kanan) */
    .about-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        align-items: start;
    }
    @media (min-width: 1024px) {
        .about-grid {
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            gap: 3rem;
            align-items: center;
        }
    }

    /* Poin-poin (bullet) */
    .about-points {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .about-points li {
        position: relative;
        padding-left: 2rem;
        padding-top: .65rem;
        padding-bottom: .65rem;
        line-height: 1.7;
        border-bottom: 1px solid rgba(148,163,184,.10);
    }
    .about-points li:last-child { border-bottom: 0; }
    .about-points li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 1.15rem;
        width: 1.15rem;
        height: 1.15rem;
        border-radius: 9999px;
        background:
            radial-gradient(circle at 30% 30%, #67e8f9, #06b6d4 60%, #0891b2);
        box-shadow: 0 0 0 3px rgba(6,182,212,.14);
    }
    .about-points li::after {
        content: "";
        position: absolute;
        left: .42rem;
        top: 1.62rem;
        width: .32rem;
        height: .32rem;
        border-radius: 9999px;
        background: #020617;
    }

    /* Card gambar / QR di dalam about */
    .about-media-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        background: linear-gradient(145deg, rgba(2,6,23,.95), rgba(15,23,42,.95));
        border: 1px solid rgba(148,163,184,.14);
        box-shadow: 0 18px 50px rgba(0,0,0,.35);
        padding: 1.25rem;
    }
    @media (min-width: 1024px) {
        .about-media-card {
            position: sticky;
            top: 100px;
        }
    }

    .about-media-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 1rem;
        overflow: hidden;
        background: #0f172a;
    }
    .about-media-frame img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .about-media-caption {
        margin-top: .9rem;
        text-align: center;
        font-size: .72rem;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgba(148,163,184,.75);
        font-weight: 700;
    }

    /* =========================================================
       RETURN BUTTON
       ========================================================= */
    .home-button {
        display: inline-flex; align-items: center; justify-content: center; gap: .55rem;
        min-height: 48px; padding: .75rem 1.5rem; border-radius: 9999px;
        border: 1px solid rgba(6,182,212,.20);
        background: rgba(6,182,212,.08);
        color: #67e8f9; font-size: .9rem; font-weight: 800;
        transition: transform 180ms ease, background-color 180ms ease, color 180ms ease;
    }
    .home-button:hover { transform: translateY(-2px); background: rgba(6,182,212,.14); color: #a5f3fc; }

    /* =========================================================
       RESPONSIVE TWEAKS
       ========================================================= */
    @media (max-width: 640px) {
        .documentation-card, .about-card { border-radius: 1.35rem; }
        .documentation-card { padding: .75rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .doc-reveal, .gallery-slide, .follow-ig, .gallery-dot, .home-button {
            transition: none !important; animation: none !important; transform: none !important;
        }
        .doc-reveal { opacity: 1 !important; }
    }
</style>
@endpush


@section('content')

{{-- ============================================================
     BANNER UTAMA
============================================================ --}}
<section class="doc-banner doc-reveal">
    <img
        src="{{ asset('images/banner1.jpeg') }}"
        alt="Dokumentasi UKM Pemrograman UNICODE"
        width="1600" height="700"
        loading="eager" fetchpriority="high" decoding="async"
    >
    <div class="doc-banner-overlay">
        <div>
            <span class="doc-banner-label">Dokumentasi UNICODE</span>
            <h1 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl md:text-5xl lg:text-6xl">
                Momen yang menjadi cerita.
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base md:text-lg">
                Perjalanan, kebersamaan, dan berbagai momen
                UKM Pemrograman UNICODE Universitas Bhakti Asih.
            </p>
        </div>
    </div>
</section>


<div class="doc-shell space-y-8 py-8 sm:py-10 md:space-y-12 md:py-14 lg:space-y-16 lg:py-16">

    {{-- ========================================================
         FOLLOW INSTAGRAM
    ========================================================= --}}
    <section class="doc-reveal follow-wrap">
        <a href="https://www.instagram.com/ukm_programming?stkn=cDJ0cG9xa2kwdHpv"
           target="_blank" rel="noopener noreferrer"
           class="follow-ig"
           aria-label="Kunjungi Instagram UKM Pemrograman UNICODE">
            <i class="fab fa-instagram" aria-hidden="true"></i>
            <span>Follow Instagram Kami</span>
        </a>
    </section>


    {{-- ========================================================
         1 CARD DOKUMENTASI — 5 GAMBAR ROTASI
    ========================================================= --}}
    <section class="doc-reveal">
        <div class="documentation-card p-3 sm:p-4 md:p-6 lg:p-8">
            <div class="documentation-inner">

                <div class="documentation-heading">
                    <span class="documentation-kicker">Documentation</span>
                    <h2 class="mt-3 text-2xl font-black text-white sm:text-3xl md:text-4xl lg:text-5xl">
                        Dokumentasi UNICODE
                    </h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-400 md:text-base">
                        Lima momen pilihan yang berputar secara otomatis
                        setiap 3 detik dalam satu galeri.
                    </p>
                </div>

                <div class="mt-7 md:mt-9">
                    <div id="gallery-rotator" class="gallery-rotator"
                         role="region" aria-roledescription="carousel"
                         aria-label="Dokumentasi kegiatan UNICODE">

                        @php
                            $galeri = [
                                ['src' => 'banner1.jpeg', 'caption' => 'Momen kolaborasi anggota UNICODE'],
                                ['src' => 'banner.jpeg', 'caption' => 'Kegiatan dan semangat belajar bersama'],
                                ['src' => 'banner1.jpeg',  'caption' => 'Perjalanan dan kebersamaan tim UNICODE'],
                                ['src' => 'banner.jpeg', 'caption' => 'Dokumentasi aktivitas UKM Pemrograman'],
                                ['src' => 'banner1.jpeg', 'caption' => 'Satu perjalanan, banyak cerita'],
                            ];
                        @endphp

                        @foreach($galeri as $i => $item)
                            <figure class="gallery-slide {{ $i === 0 ? 'is-active' : '' }}"
                                    data-slide="{{ $i }}"
                                    aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                                <img src="{{ asset('images/' . basename($item['src'])) }}"
                                     alt="{{ $item['caption'] }}"
                                     width="1600" height="900"
                                     loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                     decoding="async">
                                <figcaption class="gallery-caption">
                                    <div class="gallery-caption-title">{{ $item['caption'] }}</div>
                                    <div class="gallery-caption-number">
                                        UNICODE • {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                </figcaption>
                            </figure>
                        @endforeach

                        <div class="gallery-dots" aria-label="Pilihan gambar dokumentasi">
                            @foreach($galeri as $i => $item)
                                <button type="button"
                                        class="gallery-dot {{ $i === 0 ? 'is-active' : '' }}"
                                        data-index="{{ $i }}"
                                        aria-label="Tampilkan gambar {{ $i + 1 }}"
                                        aria-current="{{ $i === 0 ? 'true' : 'false' }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex flex-col items-center justify-between gap-3 text-center sm:flex-row sm:text-left">
                    <p class="text-xs leading-relaxed text-slate-500 md:text-sm">
                        Galeri berpindah otomatis setiap
                        <span class="font-bold text-cyan-400">3 detik</span>.
                    </p>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-600">
                        5 Dokumentasi
                    </span>
                </div>
            </div>
        </div>
    </section>


    {{-- ========================================================
         CARD TENTANG KAMI (poin-poin + media card)
    ========================================================= --}}
    <section class="doc-reveal">
        <article class="about-card p-7 sm:p-8 md:p-12 lg:p-14">

            <span class="text-xs font-extrabold uppercase tracking-[0.24em] text-cyan-400">
                Tentang Kami
            </span>
            <div class="about-line mt-4"></div>

            <h2 class="mt-6 text-2xl font-black leading-tight text-white sm:text-3xl md:text-4xl lg:text-5xl">
                Lebih dari sekadar tempat belajar coding.
            </h2>

            {{-- Grid: teks kiri (laptop) + gambar kanan (laptop) --}}
            <div class="about-grid mt-8">

                {{-- Kolom teks: poin-poin --}}
                <div>
                    <ul class="about-points text-sm text-slate-300 md:text-base lg:text-lg">

                        <li>
                            <span class="font-semibold text-white">Wadah mahasiswa</span>
                            Universitas Bhakti Asih yang memiliki minat pada
                            pemrograman, teknologi, dan karya digital.
                        </li>

                        <li>
                            <span class="font-semibold text-white">Ruang tumbuh bersama</span>
                            untuk belajar, mencoba, berdiskusi, dan
                            mengembangkan kemampuan secara bertahap.
                        </li>

                        <li>
                            <span class="font-semibold text-white">Fokus pada proses</span>
                            — bukan hanya hasil akhir. Kami menghargai
                            keberanian mencoba, memperbaiki, dan
                            konsistensi belajar.
                        </li>

                        <li>
                            <span class="font-semibold text-white">Dua divisi utama:</span>
                            <span class="text-cyan-300 font-semibold">Divisi Pengembang</span>
                            untuk coding, website, aplikasi, dan eksplorasi teknologi;
                            <span class="text-violet-300 font-semibold">Divisi PDD</span>
                            untuk publikasi, dokumentasi, desain, dan komunikasi visual.
                        </li>

                        <li>
                            <span class="font-semibold text-white">Terbuka untuk semua jurusan.</span>
                            Tidak harus jago coding dulu untuk bergabung —
                            cukup punya rasa ingin tahu dan kemauan belajar.
                        </li>

                        <li>
                            <span class="font-semibold text-white">Aktif berkolaborasi</span>
                            dengan UKM lain, kegiatan kampus, serta
                            kegiatan sosial dan kemanusiaan.
                        </li>

                    </ul>

                    {{-- Quote --}}
                    <div class="mt-7 rounded-2xl border border-cyan-500/10 bg-cyan-500/5 p-5 md:p-6">
                        <p class="text-sm font-semibold leading-relaxed text-cyan-300 md:text-base">
                            “Tidak harus hebat untuk memulai.
                            Tetapi harus memulai untuk menjadi hebat.”
                        </p>
                    </div>
                </div>

                {{-- Kolom media: card gambar/QR --}}
                <aside class="about-media-card" aria-label="Media UNICODE">
                    <div class="about-media-frame">
                        {{-- Ganti src sesuai file gambar / QR Anda:
                             contoh: images/qr-unicode.png atau images/logo-ukm3.jpeg --}}
                        <img
                            src="{{ asset('images/qr1.jpg') }}"
                            alt="Logo / QR UKM Pemrograman UNICODE"
                            loading="lazy"
                            decoding="async"
                            onerror="this.onerror=null;this.src='{{ asset('images/banner2.jpeg') }}';"
                        >
                    </div>
                    <p class="about-media-caption">
                        Scan / Kenali UNICODE
                    </p>
                </aside>

            </div>

        </article>
    </section>


    {{-- ========================================================
         KEMBALI
    ========================================================= --}}
    <section class="doc-reveal flex justify-center">
        <a href="{{ url('/') }}" class="home-button">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </section>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       1. NATIVE REVEAL
       ========================================================= */
    const revealItems = document.querySelectorAll('.doc-reveal');

    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });

        revealItems.forEach(item => revealObserver.observe(item));
    } else {
        revealItems.forEach(item => item.classList.add('is-visible'));
    }


    /* =========================================================
       2. GALLERY ROTATOR — 5 gambar / 3 detik
       ========================================================= */
    const gallery = document.getElementById('gallery-rotator');
    if (!gallery) return;

    const slides = Array.from(gallery.querySelectorAll('.gallery-slide'));
    const dots   = Array.from(gallery.querySelectorAll('.gallery-dot'));
    const totalSlides = slides.length;
    if (totalSlides < 2) return;

    const INTERVAL = 3000;
    let currentIndex = 0;
    let timer = null;

    function updateGallery(index) {
        currentIndex = index;
        slides.forEach((slide, i) => {
            const active = i === index;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach((dot, i) => {
            const active = i === index;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    }

    function nextSlide() {
        updateGallery((currentIndex + 1) % totalSlides);
    }

    function stopRotation() {
        if (timer !== null) { clearInterval(timer); timer = null; }
    }

    function startRotation() {
        stopRotation();
        if (document.hidden) return;
        timer = setInterval(nextSlide, INTERVAL);
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            updateGallery(index);
            startRotation();
        });
    });

    document.addEventListener('visibilitychange', () => {
        document.hidden ? stopRotation() : startRotation();
    });

    gallery.addEventListener('mouseenter', stopRotation);
    gallery.addEventListener('mouseleave', startRotation);

    /* Touch swipe */
    let touchStartX = 0;
    let touchEndX = 0;
    gallery.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    gallery.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        const distance = touchEndX - touchStartX;
        if (Math.abs(distance) < 45) return;
        if (distance < 0) {
            updateGallery((currentIndex + 1) % totalSlides);
        } else {
            updateGallery((currentIndex - 1 + totalSlides) % totalSlides);
        }
        startRotation();
    }, { passive: true });

    updateGallery(0);
    startRotation();

    /* Reduced motion: jangan auto-rotate */
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        stopRotation();
    }
});
</script>
@endpush
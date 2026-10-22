{{-- resources/views/dokumentasi.blade.php --}}
@extends('layouts.app')

@section('title', 'Dokumentasi – UKM Pemrograman UNICODE')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    html { scroll-behavior: smooth; }
    body { background: #020617; overflow-x: hidden; }

    /* =========================================================
       CONTAINER
       ========================================================= */
    .doc-shell {
        width: min(100% - 1rem, 100%);
        margin-inline: auto;
    }
    @media (min-width: 640px)  { .doc-shell { width: min(100% - 2rem, 1100px); } }
    @media (min-width: 1024px) { .doc-shell { width: min(100% - 3rem, 1400px); } }
    @media (min-width: 1536px) { .doc-shell { width: min(100% - 5rem, 1700px); } }

    /* =========================================================
       REVEAL
       ========================================================= */
    .doc-reveal {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity 600ms cubic-bezier(.22,.61,.36,1),
                    transform 600ms cubic-bezier(.22,.61,.36,1);
        will-change: opacity, transform;
    }
    .doc-reveal.is-visible { opacity: 1; transform: translateY(0); }

    /* =========================================================
       HERO BANNER
       ========================================================= */
    .hero-banner {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        background:
            radial-gradient(circle at 85% 15%, rgba(6,182,212,.22), transparent 40%),
            radial-gradient(circle at 15% 85%, rgba(124,58,237,.20), transparent 42%),
            linear-gradient(135deg, #020617 0%, #0f172a 45%, #1e3a8a 100%);
        border: 1px solid rgba(148,163,184,.14);
        box-shadow: 0 30px 80px rgba(0,0,0,.45);
        padding: clamp(2rem, 5vw, 5rem) clamp(1.25rem, 4vw, 4rem);
    }

    .hero-orb {
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 9999px;
        filter: blur(80px);
        opacity: .35;
        pointer-events: none;
    }
    .hero-orb.a { top: -120px; right: -80px; background: #06b6d4; }
    .hero-orb.b { bottom: -140px; left: -80px; background: #7c3aed; }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .55rem 1.1rem;
        border-radius: 9999px;
        background: rgba(6,182,212,.12);
        border: 1px solid rgba(6,182,212,.28);
        color: #67e8f9;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .22em;
        text-transform: uppercase;
    }

    .hero-title {
        font-size: clamp(1.85rem, 5vw, 4.25rem);
        font-weight: 900;
        line-height: 1.05;
        letter-spacing: -.02em;
    }
    .hero-title .grad {
        background: linear-gradient(135deg, #67e8f9 0%, #60a5fa 50%, #a78bfa 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
    }

    /* Stats bar */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .6rem;
        margin-top: 2rem;
    }
    @media (min-width: 768px) { .stats-bar { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; } }

    .stat-card {
        padding: .9rem .8rem;
        border-radius: 1rem;
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.08);
        backdrop-filter: blur(6px);
        text-align: center;
        transition: transform 260ms ease, background-color 260ms ease, border-color 260ms ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        background: rgba(255,255,255,.07);
        border-color: rgba(6,182,212,.35);
    }
    .stat-value {
        font-size: clamp(1.25rem, 3vw, 2rem);
        font-weight: 900;
        background: linear-gradient(135deg, #67e8f9, #a78bfa);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
        line-height: 1;
    }
    .stat-label {
        display: block;
        margin-top: .45rem;
        font-size: .62rem;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: rgba(203,213,225,.65);
        font-weight: 700;
    }

    /* =========================================================
       SECTION HEADING
       ========================================================= */
    .sec-kicker {
        display: inline-block;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .24em;
        text-transform: uppercase;
        color: #67e8f9;
    }
    .sec-title {
        margin-top: .6rem;
        font-size: clamp(1.5rem, 3.5vw, 2.75rem);
        font-weight: 900;
        color: #fff;
        line-height: 1.15;
        letter-spacing: -.015em;
    }
    .sec-desc {
        margin-top: .9rem;
        color: rgba(148,163,184,.9);
        font-size: .92rem;
        line-height: 1.7;
    }

    /* =========================================================
       INSTAGRAM EMBED GRID
       ========================================================= */
    .ig-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: .85rem;
    }
    @media (min-width: 640px)  { .ig-grid { grid-template-columns: repeat(2, 1fr); gap: 1.1rem; } }
    @media (min-width: 1024px) { .ig-grid { grid-template-columns: repeat(3, 1fr); gap: 1.4rem; } }
    @media (min-width: 1536px) { .ig-grid { grid-template-columns: repeat(3, 1fr); gap: 1.6rem; } }

    .ig-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: linear-gradient(145deg, rgba(15,23,42,.98), rgba(15,23,42,.92));
        border: 1px solid rgba(148,163,184,.14);
        box-shadow: 0 20px 55px rgba(0,0,0,.35);
        transition: transform 380ms cubic-bezier(.22,.61,.36,1),
                    box-shadow 380ms ease,
                    border-color 300ms ease;
    }
    .ig-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 30px 80px rgba(0,0,0,.5);
        border-color: rgba(6,182,212,.35);
    }

    /* Instagram embed iframe */
    .ig-card iframe {
        display: block;
        width: 100% !important;
        min-height: 520px;
        background: #fff;
        border: 0;
        border-radius: 1.25rem;
    }
    @media (max-width: 640px) {
        .ig-card iframe {
            min-height: 480px;
            border-radius: 1rem;
        }
    }
    @media (min-width: 1024px) {
        .ig-card iframe { min-height: 580px; }
    }

    /* Fallback */
    .ig-fallback {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .85rem;
        padding: 3rem 1.5rem;
        min-height: 380px;
        text-align: center;
        background: linear-gradient(135deg, #fdf2f8, #fce7f3);
        color: #831843;
    }
    .ig-fallback i { font-size: 2.75rem; color: #db2777; }
    .ig-fallback h4 { font-weight: 800; font-size: 1rem; }
    .ig-fallback p  { font-size: .8rem; opacity: .8; }
    .ig-fallback a {
        margin-top: .5rem;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .7rem 1.4rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        color: #fff;
        font-weight: 800;
        font-size: .8rem;
        text-decoration: none;
        box-shadow: 0 10px 25px rgba(236,72,153,.3);
        transition: transform 200ms ease;
    }
    .ig-fallback a:hover { transform: translateY(-2px); }

    /* =========================================================
       ABOUT CARD
       ========================================================= */
    .about-card {
        border-radius: 2rem;
        padding: clamp(1.5rem, 4vw, 3rem);
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.10), transparent 40%),
            linear-gradient(145deg, rgba(15,23,42,.96), rgba(17,24,39,.97));
        border: 1px solid rgba(148,163,184,.13);
        box-shadow: 0 22px 60px rgba(0,0,0,.3);
    }
    .about-line {
        width: 64px;
        height: 4px;
        border-radius: 9999px;
        background: linear-gradient(90deg, #06b6d4, #2563eb, #7c3aed);
        margin-top: 1rem;
    }
    .about-points {
        list-style: none;
        padding: 0;
        margin: 1.75rem 0 0;
        display: grid;
        grid-template-columns: 1fr;
        gap: .85rem;
    }
    @media (min-width: 768px) { .about-points { grid-template-columns: repeat(2, 1fr); gap: 1rem; } }

    .about-points li {
        position: relative;
        padding: 1rem 1.1rem 1rem 3.1rem;
        border-radius: 1rem;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(148,163,184,.1);
        color: rgba(203,213,225,.92);
        font-size: .88rem;
        line-height: 1.65;
        transition: background-color 240ms ease, border-color 240ms ease;
    }
    .about-points li:hover {
        background: rgba(6,182,212,.06);
        border-color: rgba(6,182,212,.28);
    }
    .about-points li::before {
        content: attr(data-num);
        position: absolute;
        left: .95rem;
        top: 1rem;
        width: 1.6rem;
        height: 1.6rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .72rem;
        font-weight: 900;
        color: #020617;
        background: linear-gradient(135deg, #67e8f9, #60a5fa);
        box-shadow: 0 0 0 4px rgba(6,182,212,.14);
    }
    .about-points li b {
        color: #fff;
        font-weight: 800;
    }

    /* =========================================================
       CTA
       ========================================================= */
    .cta-card {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        padding: clamp(1.75rem, 5vw, 3.5rem);
        text-align: center;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #6d28d9 100%);
        border: 1px solid rgba(148,163,184,.16);
        box-shadow: 0 30px 80px rgba(0,0,0,.45);
    }
    .cta-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 20%, rgba(6,182,212,.18), transparent 42%),
            radial-gradient(circle at 80% 80%, rgba(124,58,237,.18), transparent 42%);
        pointer-events: none;
    }

    .btn-instagram {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .9rem 1.7rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #ec4899, #8b5cf6);
        color: #fff;
        font-weight: 800;
        font-size: .88rem;
        text-decoration: none;
        box-shadow: 0 14px 35px rgba(236,72,153,.35);
        transition: transform 200ms ease, box-shadow 200ms ease, filter 200ms ease;
    }
    .btn-instagram:hover {
        transform: translateY(-3px) scale(1.02);
        filter: brightness(1.08);
        box-shadow: 0 20px 45px rgba(236,72,153,.5);
    }

    .btn-home {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .85rem 1.6rem;
        border-radius: 9999px;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.16);
        color: #fff;
        font-weight: 700;
        font-size: .84rem;
        text-decoration: none;
        transition: background-color 200ms ease, transform 200ms ease;
    }
    .btn-home:hover { background: rgba(255,255,255,.12); transform: translateY(-2px); }

    /* =========================================================
       REDUCED MOTION
       ========================================================= */
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .doc-reveal, .ig-card, .stat-card, .btn-instagram, .btn-home {
            transition: none !important;
            animation: none !important;
            transform: none !important;
        }
        .doc-reveal { opacity: 1 !important; }
    }
</style>
@endpush

@section('content')

<div class="doc-shell py-8 md:py-12 space-y-14 md:space-y-20">

    {{-- HERO BANNER --}}
    <section class="hero-banner doc-reveal">
        <div class="hero-orb a"></div>
        <div class="hero-orb b"></div>

        <div class="relative z-10">
            <span class="hero-badge">
                <i class="fas fa-camera-retro"></i>
                Dokumentasi UKM
            </span>

            <h1 class="hero-title mt-6">
                Momen &amp; Karya
                <br>
                <span class="grad">UNICODE</span>
            </h1>

            <p class="mt-6 max-w-2xl text-base md:text-lg leading-relaxed" style="color:rgba(226,232,240,.78)">
                Kumpulan dokumentasi kegiatan, prestasi, dan proyek anggota
                UKM Pemrograman Universitas Bhakti Asih — langsung dari
                feed Instagram resmi kami.
            </p>

            <div class="stats-bar">
                <div class="stat-card">
                    <span class="stat-value">2025</span>
                    <span class="stat-label">Tahun Berdiri</span>
                </div>
                <div class="stat-card">
                    <span class="stat-value">12</span>
                    <span class="stat-label">Post Terbaru</span>
                </div>
                <div class="stat-card">
                    <span class="stat-value">2</span>
                    <span class="stat-label">Divisi Aktif</span>
                </div>
                <div class="stat-card">
                    <span class="stat-value">∞</span>
                    <span class="stat-label">Semangat Belajar</span>
                </div>
            </div>
        </div>
    </section>

    {{-- INSTAGRAM GALLERY --}}
    <section class="doc-reveal">
        <div class="max-w-3xl">
            <span class="sec-kicker">Instagram Feed</span>
            <h2 class="sec-title">Galeri Dokumentasi</h2>
            <p class="sec-desc">
                Setiap konten di bawah dimuat otomatis dari link Instagram —
                cukup tempel URL, dan gambar akan muncul sendiri tanpa perlu
                mengunduh manual.
            </p>
        </div>

        @php
            $instagramPosts = [
                ['url' => 'https://www.instagram.com/p/DZDQQjqj91s/', 'caption' => 'Momen kolaborasi anggota UNICODE'],
                ['url' => 'https://www.instagram.com/p/DZDLqvaD7K-/', 'caption' => 'Kegiatan dan semangat belajar bersama'],
                ['url' => 'https://www.instagram.com/p/DZCRVUGjxbQ/', 'caption' => 'Perjalanan dan kebersamaan tim UNICODE'],
                ['url' => 'https://www.instagram.com/p/DXlRxPtgXiX/', 'caption' => 'Konten ke-4'],
                ['url' => 'https://www.instagram.com/p/DYAQl3-Abfz/', 'caption' => 'Konten ke-5'],
                ['url' => 'https://www.instagram.com/p/DYARdfUATNL/', 'caption' => 'Konten ke-6'],
                ['url' => 'https://www.instagram.com/reel/DYukyXwP6xR/', 'caption' => 'Konten ke-7'],
                ['url' => 'https://www.instagram.com/reel/DYwm1CNvqrD/', 'caption' => 'Konten ke-8'],
                ['url' => 'https://www.instagram.com/reel/DYwwzbcvdTx/', 'caption' => 'Konten ke-9'],
                ['url' => 'https://www.instagram.com/reel/DYwy96dPPIp/', 'caption' => 'Konten ke-10'],
                ['url' => 'https://www.instagram.com/reel/DYxIXJwPW2x/', 'caption' => 'Konten ke-11'],
                ['url' => 'https://www.instagram.com/reel/DYxKZc7vNVF/', 'caption' => 'Konten ke-12'],
            ];

            $igShortcode = function (string $url): ?string {
                if (preg_match('~instagram\.com/(?:p|reel|tv)/([A-Za-z0-9_-]+)~', $url, $m)) {
                    return $m[1];
                }
                return null;
            };
        @endphp

        <div class="ig-grid mt-8 md:mt-10">
            @foreach($instagramPosts as $i => $post)
                @php $code = $igShortcode($post['url']); @endphp

                <div class="ig-card">
                    @if($code)
                        <iframe
                            src="https://www.instagram.com/p/{{ $code }}/embed/"
                            title="Konten Instagram UNICODE {{ $i + 1 }}"
                            scrolling="no"
                            allowtransparency="true"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    @else
                        <div class="ig-fallback">
                            <i class="fab fa-instagram"></i>
                            <h4>Konten tidak tersedia</h4>
                            <p>Link Instagram tidak valid atau post bersifat privat.</p>
                            <a href="{{ $post['url'] }}" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-external-link-alt"></i>
                                Buka di Instagram
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            <a href="https://instagram.com/ukm_pemrograman"
               target="_blank"
               rel="noopener noreferrer"
               class="btn-instagram">
                <i class="fab fa-instagram" style="font-size:1.1rem"></i>
                Lihat Semua di Instagram
            </a>
        </div>
    </section>

    {{-- TENTANG DOKUMENTASI --}}
    <section class="doc-reveal">
        <article class="about-card">
            <span class="sec-kicker">Tentang Dokumentasi</span>
            <div class="about-line"></div>

            <h2 class="sec-title mt-6">Momen yang menjadi cerita.</h2>

            <p class="sec-desc max-w-3xl">
                Setiap kegiatan UNICODE kami dokumentasikan sebagai jejak
                perjalanan. Berikut poin-poin yang kami jaga dalam setiap
                dokumentasi:
            </p>

            <ul class="about-points">
                <li data-num="1">
                    <b>Otentik &amp; apa adanya</b> — kami menampilkan
                    momen nyata dari kegiatan, bukan hanya hasil akhir.
                </li>
                <li data-num="2">
                    <b>Kolaboratif</b> — setiap dokumentasi melibatkan
                    anggota dari kedua divisi: Pengembang &amp; PDD.
                </li>
                <li data-num="3">
                    <b>Rapi &amp; terstruktur</b> — setiap konten dikurasi
                    agar mudah dipahami dan dicari kembali.
                </li>
                <li data-num="4">
                    <b>Terbuka</b> — publik dapat melihat langsung
                    perkembangan kami melalui feed Instagram resmi.
                </li>
                <li data-num="5">
                    <b>Konsisten</b> — dokumentasi diperbarui rutin
                    setiap kegiatan besar selesai digelar.
                </li>
                <li data-num="6">
                    <b>Menginspirasi</b> — kami ingin setiap momen
                    dapat memotivasi mahasiswa lain untuk berkarya.
                </li>
            </ul>
        </article>
    </section>

    {{-- CTA --}}
    <section class="doc-reveal">
        <div class="cta-card">
            <div class="relative z-10">
                <span class="hero-badge">
                    <i class="fas fa-handshake"></i>
                    Ikuti Kami
                </span>

                <h3 class="sec-title mt-5">Jangan lewatkan momen berikutnya.</h3>

                <p class="sec-desc max-w-2xl mx-auto">
                    Follow Instagram resmi UKM Pemrograman UNICODE untuk
                    mengikuti kegiatan, prestasi, dan proyek terbaru kami.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="https://instagram.com/ukm_pemrograman"
                       target="_blank" rel="noopener noreferrer"
                       class="btn-instagram">
                        <i class="fab fa-instagram" style="font-size:1.15rem"></i>
                        Follow @ukm_pemrograman
                    </a>

                    <a href="{{ url('/') }}" class="btn-home">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =========================================================
       REVEAL — IntersectionObserver native
       ========================================================= */
    const revealItems = document.querySelectorAll('.doc-reveal');

    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver((entries, o) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                o.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });

        revealItems.forEach(item => obs.observe(item));
    } else {
        revealItems.forEach(item => item.classList.add('is-visible'));
    }

});
</script>
@endpush
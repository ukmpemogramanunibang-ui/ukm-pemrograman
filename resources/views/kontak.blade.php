@extends('layouts.app')

@section('title', 'Kontak – UKM Pemrograman UNICODE')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    :root {
        --neon-cyan: #22d3ee;
        --neon-blue: #3b82f6;
        --neon-violet: #8b5cf6;
        --neon-pink: #ec4899;
        --neon-green: #10b981;
        --neon-amber: #f59e0b;
    }

    /* =========================================================
       SHELL
       ========================================================= */
    .u-shell { width: min(100% - 1rem, 100%); margin-inline: auto; }
    @media (min-width: 641px)  { .u-shell { width: min(100% - 1.5rem, 1000px); } }
    @media (min-width: 1025px) { .u-shell { width: min(100% - 2rem,   1400px); } }
    @media (min-width: 1536px) { .u-shell { width: min(100% - 3rem,   1800px); } }
    @media (min-width: 2000px) { .u-shell { width: min(100% - 5rem,   2100px); } }

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
    .ico-md { font-size: 15px; }
    .ico-lg { font-size: 22px; }
    .ico-xl { font-size: 28px; }

    /* =========================================================
       HERO
       ========================================================= */
    .u-hero {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(6,182,212,.22), transparent 32%),
            radial-gradient(circle at bottom left, rgba(139,92,246,.20), transparent 30%),
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
       GLOW ANIMATION (hemat GPU — pakai box-shadow, bukan blur)
       ========================================================= */
    @keyframes glowPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34,211,238,.45); }
        50%      { box-shadow: 0 0 0 14px rgba(34,211,238,0); }
    }
    @keyframes glowSoft {
        0%, 100% { box-shadow: 0 0 22px -4px rgba(6,182,212,.35), 0 8px 24px rgba(15,23,42,.08); }
        50%      { box-shadow: 0 0 30px -4px rgba(6,182,212,.55), 0 12px 28px rgba(15,23,42,.12); }
    }
    .glow-soft { animation: glowSoft 4s ease-in-out infinite; }
    .glow-pulse { animation: glowPulse 2.4s ease-out infinite; }

    /* =========================================================
       STAT CARD
       ========================================================= */
    .stat-card {
        position: relative;
        border-radius: 1.5rem;
        padding: 1.5rem 1.25rem;
        background: rgba(255,255,255,.85);
        border: 1px solid rgba(148,163,184,.2);
        box-shadow: 0 8px 24px rgba(15,23,42,.06);
        text-align: center;
        transition: transform .3s cubic-bezier(.22,.61,.36,1),
                    box-shadow .3s cubic-bezier(.22,.61,.36,1);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(15,23,42,.12);
    }
    .dark .stat-card {
        background: rgba(15,23,42,.75);
        border-color: rgba(148,163,184,.12);
        box-shadow: 0 10px 30px rgba(0,0,0,.25);
    }
    .stat-emoji {
        font-size: 1.75rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem; height: 3rem;
        border-radius: 9999px;
        margin-bottom: .75rem;
    }
    .stat-emoji.cyan    { background: rgba(6,182,212,.12); }
    .stat-emoji.violet  { background: rgba(139,92,246,.12); }
    .stat-emoji.pink    { background: rgba(236,72,153,.12); }
    .stat-emoji.amber   { background: rgba(245,158,11,.12); }

    /* =========================================================
       CONTACT CARD (lebih besar di laptop/PC/TV)
       ========================================================= */
    .contact-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1rem;
        padding: 2rem 1.5rem;
        border-radius: 1.75rem;
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        border: 1.5px solid rgba(34,211,238,.25);
        color: #f8fafc;
        text-decoration: none;
        overflow: hidden;
        transition: transform .35s cubic-bezier(.22,.61,.36,1),
                    border-color .35s ease,
                    box-shadow .35s ease;
        will-change: transform;
    }
    .contact-card::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: inherit;
        background: linear-gradient(135deg,
                    rgba(34,211,238,.5),
                    rgba(139,92,246,.35) 50%,
                    rgba(236,72,153,.45));
        opacity: 0;
        transition: opacity .35s ease;
        z-index: 0;
        padding: 1.5px;
        -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
                mask-composite: exclude;
        pointer-events: none;
    }
    .contact-card:hover {
        transform: translateY(-6px);
        border-color: transparent;
        box-shadow: 0 30px 60px -20px rgba(34,211,238,.45),
                    0 0 40px -8px rgba(139,92,246,.35);
    }
    .contact-card:hover::before { opacity: 1; }

    .contact-icon {
        position: relative;
        z-index: 1;
        width: 76px; height: 76px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
        transition: transform .35s cubic-bezier(.22,.61,.36,1);
    }
    .contact-card:hover .contact-icon { transform: scale(1.08) rotate(-4deg); }
    .contact-icon.green  { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 12px 32px -8px rgba(16,185,129,.7); }
    .contact-icon.pink   { background: linear-gradient(135deg, #ec4899, #8b5cf6); box-shadow: 0 12px 32px -8px rgba(236,72,153,.7); }
    .contact-icon.red    { background: linear-gradient(135deg, #ef4444, #f97316); box-shadow: 0 12px 32px -8px rgba(239,68,68,.7); }
    .contact-icon.slate  { background: linear-gradient(135deg, #475569, #1e293b); box-shadow: 0 12px 32px -8px rgba(71,85,105,.7); }

    .contact-label {
        position: relative; z-index: 1;
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: .01em;
    }
    .contact-value {
        position: relative; z-index: 1;
        font-size: .82rem;
        color: #94a3b8;
        word-break: break-word;
    }
    .contact-action {
        position: relative; z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        margin-top: .35rem;
        padding: .45rem .95rem;
        border-radius: 9999px;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        background: rgba(34,211,238,.12);
        border: 1px solid rgba(34,211,238,.35);
        color: #67e8f9;
        transition: background .3s ease, color .3s ease, gap .3s ease;
    }
    .contact-card:hover .contact-action {
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
        border-color: transparent;
        gap: .55rem;
    }

    /* =========================================================
       FORM + MAP
       ========================================================= */
    .form-card {
        position: relative;
        border-radius: 2rem;
        padding: 2rem;
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        border: 1.5px solid rgba(34,211,238,.22);
        box-shadow: 0 30px 65px -25px rgba(6,182,212,.35);
        overflow: hidden;
    }
    @media (min-width: 1025px) { .form-card { padding: 2.5rem 2.75rem; } }
    .form-card::before {
        content: '';
        position: absolute;
        top: -30%; right: -20%;
        width: 360px; height: 360px;
        background: radial-gradient(circle, rgba(34,211,238,.20), transparent 70%);
        filter: blur(50px);
        pointer-events: none;
    }

    .field-label {
        display: block;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: .5rem;
    }
    .field-input {
        width: 100%;
        padding: .9rem 1.1rem;
        border-radius: 1rem;
        background: rgba(15,23,42,.65);
        border: 1.5px solid rgba(148,163,184,.2);
        color: #f8fafc;
        font-size: .9rem;
        outline: none;
        transition: border-color .25s ease, box-shadow .25s ease, background .25s ease;
    }
    .field-input::placeholder { color: #64748b; }
    .field-input:focus {
        border-color: #22d3ee;
        background: rgba(15,23,42,.9);
        box-shadow: 0 0 0 4px rgba(34,211,238,.15);
    }
    .field-input.is-invalid { border-color: #f43f5e; }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        width: 100%;
        padding: 1rem 1.5rem;
        border-radius: 9999px;
        font-size: .95rem;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #fff;
        background: linear-gradient(135deg, #06b6d4, #3b82f6 60%, #8b5cf6);
        border: none;
        cursor: pointer;
        box-shadow: 0 20px 45px -12px rgba(6,182,212,.7);
        transition: transform .28s cubic-bezier(.22,.61,.36,1),
                    box-shadow .28s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 26px 55px -12px rgba(6,182,212,.9);
    }

    .map-shell {
        border-radius: 1.5rem;
        padding: 1.5px;
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
    }
    .map-inner {
        border-radius: calc(1.5rem - 1.5px);
        overflow: hidden;
        background: #0f172a;
        aspect-ratio: 16 / 11;
    }
    .map-inner iframe { width: 100%; height: 100%; border: 0; display: block; }

    .info-card {
        border-radius: 1.5rem;
        padding: 1.5rem;
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        border: 1.5px solid rgba(148,163,184,.15);
        color: #f8fafc;
    }

    /* =========================================================
       FAQ
       ========================================================= */
    .faq-item {
        border-radius: 1rem;
        overflow: hidden;
        background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        border: 1.5px solid rgba(148,163,184,.15);
        transition: border-color .3s ease, box-shadow .3s ease;
    }
    .faq-item:hover { border-color: rgba(34,211,238,.35); }
    .faq-item.open { border-color: rgba(34,211,238,.55); box-shadow: 0 12px 35px -12px rgba(34,211,238,.4); }

    .faq-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        width: 100%;
        padding: 1.1rem 1.25rem;
        background: transparent;
        border: none;
        cursor: pointer;
        text-align: left;
        color: #f8fafc;
        font-size: .95rem;
        font-weight: 700;
        letter-spacing: .01em;
        transition: background .25s ease;
    }
    .faq-toggle:hover { background: rgba(34,211,238,.05); }
    .faq-chevron {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px; height: 30px;
        border-radius: 9999px;
        background: rgba(34,211,238,.12);
        border: 1px solid rgba(34,211,238,.3);
        color: #67e8f9;
        transition: transform .35s cubic-bezier(.22,.61,.36,1), background .25s ease;
        flex-shrink: 0;
    }
    .faq-item.open .faq-chevron {
        transform: rotate(180deg);
        background: linear-gradient(135deg, #06b6d4, #3b82f6);
        color: #fff;
    }
    .faq-content {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows .38s cubic-bezier(.22,.61,.36,1);
    }
    .faq-item.open .faq-content { grid-template-rows: 1fr; }
    .faq-content > div {
        overflow: hidden;
        padding: 0 1.25rem;
        color: #94a3b8;
        font-size: .88rem;
        line-height: 1.7;
    }
    .faq-item.open .faq-content > div { padding: 0 1.25rem 1.2rem; }

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
    @media (min-width: 768px) { .cta-block { padding: 4rem 3rem; } }

    /* =========================================================
       RESPONSIVE (laptop/PC/TV: card lebih besar)
       ========================================================= */
    @media (min-width: 1025px) {
        .contact-card { padding: 2.35rem 1.85rem; gap: 1.15rem; }
        .contact-icon { width: 88px; height: 88px; }
        .contact-label { font-size: 1.2rem; }
        .contact-value { font-size: .92rem; }
        .stat-card { padding: 1.75rem 1.4rem; }
        .stat-emoji { width: 3.5rem; height: 3.5rem; font-size: 2rem; margin-bottom: .9rem; }
    }
    @media (min-width: 1536px) {
        .contact-card { padding: 2.75rem 2.1rem; gap: 1.3rem; }
        .contact-icon { width: 96px; height: 96px; }
        .contact-label { font-size: 1.3rem; }
    }

    /* =========================================================
       MOBILE
       ========================================================= */
    @media (max-width: 640px) {
        .u-orb { display: none; }
        .contact-card,
        .stat-card,
        .form-card,
        .faq-item { transition: none; }
        .contact-card:hover { transform: none; }
        .contact-card:hover::before { opacity: 0; }
        .contact-card:hover .contact-icon { transform: none; }
        .glow-soft, .glow-pulse { animation: none; }
        .form-card::before { display: none; }
        .form-card { padding: 1.5rem 1.25rem; }
        .contact-card { padding: 1.5rem 1.1rem; border-radius: 1.25rem; }
        .contact-icon { width: 62px; height: 62px; }
    }

    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
        .u-reveal {
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
        .glow-soft, .glow-pulse { animation: none !important; }
    }
</style>
@endpush

@section('content')

@php
    $contacts = [
        [
            'label'  => 'WhatsApp',
            'value'  => '+62 851-2394-2400',
            'href'   => 'https://wa.me/6285123942400',
            'icon'   => 'whatsapp',
            'color'  => 'green',
            'action' => 'Mulai Chat',
        ],
        [
            'label'  => 'Instagram',
            'value'  => '@ukm_pemrograman',
            'href'   => 'https://instagram.com/ukm_pemrograman',
            'icon'   => 'instagram',
            'color'  => 'pink',
            'action' => 'Follow',
        ],
        [
            'label'  => 'Email',
            'value'  => 'ukmpemogramanunibang@gmail.com',
            'href'   => 'mailto:ukmpemogramanunibang@gmail.com',
            'icon'   => 'mail',
            'color'  => 'red',
            'action' => 'Kirim Email',
        ],
        [
            'label'  => 'GitHub',
            'value'  => 'ukm-pemrograman',
            'href'   => 'https://github.com/ukm-pemrograman',
            'icon'   => 'github',
            'color'  => 'slate',
            'action' => 'Lihat Kode',
        ],
    ];

    $stats = [
        ['emoji' => '⚡', 'value' => '100+',  'label' => 'Pesan Masuk',       'color' => 'cyan',   'text' => 'text-cyan-500'],
        ['emoji' => '🤝', 'value' => '25',    'label' => 'Kolaborasi',         'color' => 'violet', 'text' => 'text-violet-500'],
        ['emoji' => '⏱️', 'value' => '< 1 Jam','label' => 'Rata-rata Respons', 'color' => 'pink',   'text' => 'text-pink-500'],
        ['emoji' => '⭐', 'value' => '4.9',   'label' => 'Rating Kepuasan',   'color' => 'amber',  'text' => 'text-amber-500'],
    ];

    $faqs = [
        ['q' => 'Berapa lama balasan pesan?',             'a' => 'Kami berusaha merespons dalam waktu kurang dari 1 jam pada jam operasional.'],
        ['q' => 'Apakah bisa berkunjung langsung?',       'a' => 'Tentu! Silakan datang ke sekretariat kami di Gedung A Lantai 2 saat jam kerja.'],
        ['q' => 'Bagaimana cara kerja sama proyek?',      'a' => 'Kirimkan detail proposal melalui form, dan tim kami akan meninjaunya dalam 2 hari kerja.'],
        ['q' => 'Apakah ada biaya untuk bergabung?',      'a' => 'Tidak ada biaya keanggotaan. Semua kegiatan gratis bagi mahasiswa aktif.'],
    ];

    /* Icon set — inline SVG */
    $icons = [
        'satellite' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10a12 12 0 0 1 14 0"/><path d="M7 14a7 7 0 0 1 8 0"/><path d="M10 18a3 3 0 0 1 2 0"/></svg>',
        'pen'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>',
        'whatsapp'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.81 11.81 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.366-8.421zM12 21.785h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884a9.82 9.82 0 0 1 6.988 2.898 9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884z"/></svg>',
        'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.5.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .5 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.5 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.5-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.5-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.5-2.2-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.3-1.8.5-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.5 1.3-.1 1.7-.1 4.9-.1zM12 0C8.7 0 8.3 0 7 .1 5.8.2 4.9.4 4.2.7c-.8.3-1.4.7-2.1 1.4C1.4 2.8 1 3.4.7 4.2.4 4.9.2 5.8.1 7 0 8.3 0 8.7 0 12s0 3.7.1 5c.1 1.2.3 2.1.6 2.8.3.8.7 1.4 1.4 2.1.7.7 1.3 1.1 2.1 1.4.7.3 1.6.5 2.8.6 1.3.1 1.7.1 5 .1s3.7 0 5-.1c1.2-.1 2.1-.3 2.8-.6.8-.3 1.4-.7 2.1-1.4.7-.7 1.1-1.3 1.4-2.1.3-.7.5-1.6.6-2.8.1-1.3.1-1.7.1-5s0-3.7-.1-5c-.1-1.2-.3-2.1-.6-2.8-.3-.8-.7-1.4-1.4-2.1-.7-.7-1.3-1.1-2.1-1.4-.7-.3-1.6-.5-2.8-.6C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 100 12.4 6.2 6.2 0 000-12.4zm0 10.2a4 4 0 110-8 4 4 0 010 8zm6.4-10.5a1.4 1.4 0 11-2.8 0 1.4 1.4 0 012.8 0z"/></svg>',
        'mail'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
        'github'    => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .3a12 12 0 0 0-3.8 23.4c.6.1.8-.3.8-.6v-2.2c-3.3.7-4-1.6-4-1.6-.6-1.4-1.4-1.8-1.4-1.8-1.1-.7.1-.7.1-.7 1.2.1 1.9 1.2 1.9 1.2 1.1 1.9 2.8 1.4 3.5 1 .1-.8.4-1.4.8-1.7-2.7-.3-5.5-1.3-5.5-6 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2a11 11 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.7-2.8 5.7-5.5 6 .4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6A12 12 0 0 0 12 .3z"/></svg>',
        'send'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4 20-7z"/></svg>',
        'map'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'clock'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
        'phone'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 9.8a16 16 0 0 0 6 6l1.1-1.1a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7A2 2 0 0 1 22 16.9z"/></svg>',
        'chevron'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>',
        'arrow'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>',
    ];
@endphp

<div class="u-shell py-5 md:py-8">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <section class="u-hero rounded-[2rem] p-6 sm:p-8 md:p-12 lg:p-16 xl:p-20 text-white shadow-2xl u-reveal">
        <div class="u-orb one"></div>
        <div class="u-orb two"></div>

        <div class="relative z-10 text-center max-w-3xl mx-auto">
            <span class="hero-badge">
                <span class="ico ico-md">{!! $icons['satellite'] !!}</span>
                Kontak Kami
            </span>

            <h1 class="mt-5 text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black leading-tight">
                Hubungkan Diri
                <span class="block bg-gradient-to-r from-cyan-300 via-blue-300 to-violet-300 bg-clip-text text-transparent">
                    Dengan UNICODE
                </span>
            </h1>

            <p class="mt-5 text-base md:text-lg lg:text-xl text-white/80 leading-relaxed">
                Jangan ragu untuk bertanya, berkolaborasi, atau memulai perjalanan
                teknologi bersama kami. Tim kami siap merespons dalam waktu singkat.
            </p>

            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="#form-section" class="glow-soft inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-black text-blue-700 shadow-xl transition hover:-translate-y-0.5">
                    <span class="ico ico-md">{!! $icons['pen'] !!}</span>
                    Tulis Pesan
                </a>
                <a href="https://wa.me/6285123942400" target="_blank" rel="noopener noreferrer"
                   class="glow-pulse inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-3 font-black text-white shadow-xl transition hover:-translate-y-0.5">
                    <span class="ico ico-md">{!! $icons['whatsapp'] !!}</span>
                    Chat Cepat
                </a>
            </div>
        </div>
    </section>

    {{-- =====================================================
         STATISTIK
    ====================================================== --}}
    <section class="mt-8 md:mt-12 u-reveal">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($stats as $s)
                <div class="stat-card">
                    <div class="stat-emoji {{ $s['color'] }}">{{ $s['emoji'] }}</div>
                    <div class="text-2xl md:text-3xl font-black {{ $s['text'] }}">{{ $s['value'] }}</div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $s['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- =====================================================
         KONTAK
    ====================================================== --}}
    <section class="mt-8 md:mt-12 u-reveal">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
            @foreach($contacts as $c)
                <a href="{{ $c['href'] }}" target="_blank" rel="noopener noreferrer" class="contact-card u-reveal">
                    <div class="contact-icon {{ $c['color'] }}">
                        <span class="ico ico-xl">{!! $icons[$c['icon']] !!}</span>
                    </div>
                    <div class="contact-label">{{ $c['label'] }}</div>
                    <div class="contact-value">{{ $c['value'] }}</div>
                    <span class="contact-action">
                        {{ $c['action'] }}
                        <span class="ico ico-sm">{!! $icons['arrow'] !!}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- =====================================================
         FORM + PETA
    ====================================================== --}}
    <section id="form-section" class="mt-12 md:mt-20 scroll-mt-24 u-reveal">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">

            {{-- FORM --}}
            <div class="form-card u-reveal">
                <h3 class="text-2xl md:text-3xl font-black bg-gradient-to-r from-cyan-300 to-violet-300 bg-clip-text text-transparent mb-2">
                    Kirim Pesan
                </h3>
                <p class="text-sm text-slate-400 mb-7">
                    Isi form di bawah dan kami akan merespons secepatnya.
                </p>

                @if(session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-5 py-4 text-emerald-300">
                        <span class="ico ico-md mt-0.5">✅</span>
                        <div>
                            <p class="font-bold text-sm">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('kontak.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="field-label" for="k-name">Nama Lengkap</label>
                        <input id="k-name" type="text" name="name" required placeholder="Nama Anda"
                               class="field-input @error('name') is-invalid @enderror">
                        @error('name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="k-email">Email</label>
                        <input id="k-email" type="email" name="email" required placeholder="email@example.com"
                               class="field-input @error('email') is-invalid @enderror">
                        @error('email')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="k-subject">Subjek</label>
                        <input id="k-subject" type="text" name="subject" required placeholder="Subjek pesan"
                               class="field-input @error('subject') is-invalid @enderror">
                        @error('subject')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label" for="k-message">Pesan</label>
                        <textarea id="k-message" name="message" rows="5" required placeholder="Tulis pesan Anda..."
                                  class="field-input @error('message') is-invalid @enderror"></textarea>
                        @error('message')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        <span class="ico ico-md">{!! $icons['send'] !!}</span>
                        Kirim Pesan Sekarang
                    </button>
                </form>
            </div>

            {{-- PETA + INFO --}}
            <div class="flex flex-col gap-5 u-reveal">
                <div class="map-shell">
                    <div class="map-inner">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.531239109446!2d106.629868!3d-6.198722!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f8c8b2e1c9f3%3A0x3f6b1e9e8c2b4d5a!2sUniversitas%20Bhakti%20Asih!5e0!3m2!1sid!2sid!4v1647567890123"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                            title="Lokasi Universitas Bhakti Asih">
                        </iframe>
                    </div>
                </div>

                <div class="info-card">
                    <h4 class="flex items-center gap-2 font-bold text-lg text-white">
                        <span class="ico ico-md text-cyan-400">{!! $icons['map'] !!}</span>
                        Alamat Sekretariat
                    </h4>
                    <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                        Universitas Bhakti Asih<br>
                        Jl. Raden Fatah No. 50, Sukasari, Tangerang, Banten 15118
                    </p>
                    <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-400">
                        <span class="inline-flex items-center gap-2">
                            <span class="ico ico-sm text-cyan-400">{!! $icons['clock'] !!}</span>
                            09.00 – 17.00
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span class="ico ico-sm text-cyan-400">{!! $icons['phone'] !!}</span>
                            (021) 5573-xxxx
                        </span>
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
                Siap Terhubung Lebih Dekat?
            </h3>
            <p class="text-base md:text-xl max-w-2xl mx-auto text-white/80 leading-relaxed">
                Jangan tunda lagi. Mulai kolaborasi atau dapatkan informasi langsung dari kami.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="#form-section"
                   class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 font-black text-blue-700 shadow-xl transition hover:-translate-y-0.5">
                    <span class="ico ico-md">{!! $icons['pen'] !!}</span>
                    Tulis Pesan
                </a>
                <a href="https://wa.me/6285123942400" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-500 to-green-600 px-7 py-3.5 font-black text-white shadow-xl transition hover:-translate-y-0.5">
                    <span class="ico ico-md">{!! $icons['whatsapp'] !!}</span>
                    Chat WhatsApp
                </a>
            </div>
        </div>
    </section>
 {{-- =====================================================
         FAQ
    ====================================================== --}}
    <section class="mt-12 md:mt-20 u-reveal">
        <div class="text-center mb-8">
            <span class="text-sm font-bold tracking-widest text-cyan-500">FAQ</span>
            <h2 class="mt-2 text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
                Pertanyaan Umum
            </h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">
                Hal yang sering ditanyakan sebelum menghubungi kami.
            </p>
        </div>

        <div class="max-w-3xl mx-auto space-y-3" id="faqContainer">
            @foreach($faqs as $i => $faq)
                <div class="faq-item u-reveal" data-faq>
                    <button type="button" class="faq-toggle" aria-expanded="false" aria-controls="faq-{{ $i }}">
                        <span>{{ $faq['q'] }}</span>
                        <span class="faq-chevron">
                            <span class="ico ico-sm">{!! $icons['chevron'] !!}</span>
                        </span>
                    </button>
                    <div id="faq-{{ $i }}" class="faq-content" role="region">
                        <div>{{ $faq['a'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
       1. REVEAL
       ===================================================== */
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

    /* =====================================================
       2. FAQ ACCORDION (satu terbuka sekaligus)
       ===================================================== */
    const faqItems = document.querySelectorAll('[data-faq]');

    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-toggle');
        if (!btn) return;

        btn.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            /* Tutup semua */
            faqItems.forEach(other => {
                other.classList.remove('open');
                const b = other.querySelector('.faq-toggle');
                if (b) b.setAttribute('aria-expanded', 'false');
            });

            /* Buka yang diklik (kalau sebelumnya tertutup) */
            if (!isOpen) {
                item.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

});
</script>
@endpush
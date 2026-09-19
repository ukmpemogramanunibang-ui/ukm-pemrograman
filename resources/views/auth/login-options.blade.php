@extends('layouts.app')

@section('title', 'Masuk – UKM Pemrograman UNICODE')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }

        /* ==================================================
         |  CSS VARIABLES — adaptive light & dark
         ================================================== */
        .login-shell {
            --card-bg:            rgba(255, 255, 255, 0.78);
            --card-border:        rgba(15, 23, 42, 0.08);
            --card-shadow:        0 18px 42px -20px rgba(15, 23, 42, 0.28);
            --card-shadow-hover:  0 28px 56px -20px rgba(15, 23, 42, 0.35);
            --ring-color:         rgba(6, 182, 212, 0.35);
            --text-primary:       #0f172a;
            --text-secondary:     #64748b;
        }
        .dark .login-shell {
            --card-bg:            rgba(17, 24, 39, 0.72);
            --card-border:        rgba(148, 163, 184, 0.16);
            --card-shadow:        0 20px 44px -22px rgba(0, 0, 0, 0.65);
            --card-shadow-hover:  0 30px 60px -22px rgba(0, 0, 0, 0.8);
            --text-primary:       #f1f5f9;
            --text-secondary:     #94a3b8;
        }

        /* ==================================================
         |  BACKGROUND
         ================================================== */
        .login-bg {
            background:
                radial-gradient(1200px 500px at 10% -10%, rgba(6, 182, 212, 0.10), transparent 60%),
                radial-gradient(1000px 500px at 100% 110%, rgba(139, 92, 246, 0.10), transparent 60%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .dark .login-bg {
            background:
                radial-gradient(1200px 500px at 10% -10%, rgba(6, 182, 212, 0.14), transparent 60%),
                radial-gradient(1000px 500px at 100% 110%, rgba(139, 92, 246, 0.14), transparent 60%),
                linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
        }

        .blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(90px);
            opacity: 0.5;
            pointer-events: none;
            z-index: 0;
        }
        .blob--cyan   { background: #67e8f9; }
        .blob--violet { background: #c4b5fd; }
        .dark .blob--cyan   { background: #0e7490; opacity: 0.3; }
        .dark .blob--violet { background: #6d28d9; opacity: 0.3; }

        /* ==================================================
         |  GLASS CARD
         ================================================== */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
        }

        /* ==================================================
         |  OPTION CARD
         ================================================== */
        .option-card {
            transition:
                transform        .38s cubic-bezier(.2, .8, .2, 1),
                box-shadow       .38s ease,
                border-color     .38s ease,
                background-color .38s ease;
            will-change: transform;
            -webkit-tap-highlight-color: transparent;
            position: relative;
        }
        .option-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-shadow-hover);
        }
        .option-card:active {
            transform: translateY(-2px) scale(0.99);
        }
        .option-card:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 3px var(--ring-color),
                var(--card-shadow-hover);
        }

        /* Nomor kartu */
        .card-number {
            position: absolute;
            top: 14px;
            right: 14px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em;
            padding: 4px 10px;
            border-radius: 9999px;
            font-family: ui-monospace, 'SF Mono', Menlo, monospace;
            backdrop-filter: blur(6px);
            z-index: 2;
        }

        /* ==================================================
         |  ICON TILE — konsisten & kontras tinggi
         |  Ikon putih SELALU terlihat di kedua mode karena
         |  background pakai gradient pekat + ring + shadow.
         ================================================== */
        .icon-tile {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            position: relative;
            transition: transform .38s cubic-bezier(.2, .8, .2, 1);
            isolation: isolate;
        }
        .option-card:hover .icon-tile {
            transform: scale(1.08) rotate(-4deg);
        }

        /* Tile varian warna */
        .tile--cyan {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            box-shadow:
                0 12px 24px -10px rgba(6, 182, 212, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }
        .tile--violet {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            box-shadow:
                0 12px 24px -10px rgba(139, 92, 246, 0.55),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }
        .tile--blue {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            box-shadow:
                0 12px 26px -10px rgba(59, 130, 246, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }

        /* Ikon di dalam tile — paksa putih solid dengan drop-shadow */
        .tile__icon {
            color: #ffffff !important;
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.18));
            font-size: 30px;
            line-height: 1;
        }

        /* ==================================================
         |  NFC WAVE ANIMATION
         ================================================== */
        .nfc-stage {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nfc-wave {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 52px;
            height: 52px;
            border-radius: 9999px;
            border: 2px solid rgba(6, 182, 212, 0.55);
            animation: nfcPulse 2.6s cubic-bezier(.4, 0, .2, 1) infinite;
            pointer-events: none;
        }
        .dark .nfc-wave { border-color: rgba(34, 211, 238, 0.65); }
        .nfc-wave.d-1 { animation-delay: .86s; }
        .nfc-wave.d-2 { animation-delay: 1.72s; }

        @keyframes nfcPulse {
            0%   { width: 52px;  height: 52px;  opacity: .9; }
            100% { width: 168px; height: 168px; opacity: 0;  }
        }

        /* ==================================================
         |  DISABLED STATE
         ================================================== */
        .nfc-disabled {
            opacity: .55;
            filter: grayscale(.6);
            cursor: not-allowed;
        }
        .nfc-disabled:hover,
        .nfc-disabled:active {
            transform: none !important;
            box-shadow: var(--card-shadow) !important;
        }
        .nfc-disabled .nfc-wave { animation: none; opacity: 0; }
        .nfc-disabled .icon-tile { transform: none !important; }

        /* ==================================================
         |  BRAND GRADIENT TEXT
         ================================================== */
        .brand-gradient {
            background: linear-gradient(135deg, #06b6d4 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* ==================================================
         |  ENTRANCE
         ================================================== */
        .fade-up {
            animation: fadeUp .7s cubic-bezier(.2, .8, .2, 1) both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        /* ==================================================
         |  RESPONSIVE TWEAKS
         ================================================== */
        @media (max-width: 640px) {
            .icon-tile,
            .nfc-stage { width: 62px; height: 62px; margin-bottom: 14px; }
            .tile__icon { font-size: 26px; }
            .nfc-wave { width: 46px; height: 46px; }
            @keyframes nfcPulse {
                0%   { width: 46px;  height: 46px;  opacity: .9; }
                100% { width: 140px; height: 140px; opacity: 0;  }
            }
        }

        /* ==================================================
         |  ACCESSIBILITY — reduced motion
         ================================================== */
        @media (prefers-reduced-motion: reduce) {
            .fade-up,
            .nfc-wave,
            .option-card,
            .icon-tile { animation: none !important; transition: none !important; }
            .option-card:hover { transform: none; }
            .option-card:hover .icon-tile { transform: none; }
        }
    </style>
@endpush

@section('content')
<div class="login-shell login-bg min-h-screen relative overflow-hidden flex items-center justify-center px-4 py-10 sm:py-14">

    {{-- Blob dekoratif --}}
    <div class="blob blob--cyan   w-[420px] h-[420px] -top-40 -left-40"></div>
    <div class="blob blob--violet w-[420px] h-[420px] -bottom-40 -right-40"></div>

    <div class="w-full max-w-3xl relative z-10">

        {{-- ========================================================== --}}
        {{-- HEADER --}}
        {{-- ========================================================== --}}
        <header class="text-center mb-10 sm:mb-12 fade-up" style="animation-delay:.05s;">
            <div class="relative inline-block">
                <img src="{{ asset('images/logo-unicode.png') }}"
                     alt="UNICODE"
                     class="w-20 h-20 mx-auto rounded-2xl shadow-lg ring-1 ring-black/5 dark:ring-white/10 object-cover"
                     onerror="this.onerror=null;this.src='https://placehold.co/100x100/06b6d4/white?text=UN'">
                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-400
                             border-2 border-white dark:border-slate-900 rounded-full"></span>
            </div>

            <h1 class="mt-5 text-3xl sm:text-4xl font-extrabold brand-gradient tracking-tight">
                Masuk ke UNICODE
            </h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Pilih metode masuk yang Anda inginkan
            </p>
        </header>

        {{-- ========================================================== --}}
        {{-- GRID 3 KARTU --}}
        {{-- ========================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">

            {{-- ===================== 01 · MANUAL ===================== --}}
            <a href="{{ route('login.manual.form') }}"
               aria-label="Login Manual dengan NIM dan password"
               class="option-card glass-card rounded-2xl
                      p-5 sm:p-6 text-center block fade-up
                      border-2 border-transparent
                      hover:border-cyan-400/50 dark:hover:border-cyan-500/40"
               style="animation-delay:.10s;">
                <span class="card-number bg-cyan-100/90 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-300">
                    01
                </span>

                <div class="icon-tile tile--cyan">
                    <i class="fas fa-id-card tile__icon"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                    Login Manual
                </h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Gunakan NIM &amp; password akun Anda
                </p>

                <span class="mt-4 inline-flex items-center gap-1 text-xs sm:text-sm
                             font-semibold text-cyan-600 dark:text-cyan-400">
                    Masuk
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </span>
            </a>

            {{-- ===================== 02 · QR ===================== --}}
            <a href="{{ route('login.qr.form') }}"
               aria-label="Login dengan scan QR Code"
               class="option-card glass-card rounded-2xl
                      p-5 sm:p-6 text-center block fade-up
                      border-2 border-transparent
                      hover:border-violet-400/50 dark:hover:border-violet-500/40"
               style="animation-delay:.20s;">
                <span class="card-number bg-violet-100/90 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300">
                    02
                </span>

                <div class="icon-tile tile--violet">
                    <i class="fas fa-qrcode tile__icon"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                    Login QR
                </h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Scan QR Code identitas anggota
                </p>

                <span class="mt-4 inline-flex items-center gap-1 text-xs sm:text-sm
                             font-semibold text-violet-600 dark:text-violet-400">
                    Scan
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </span>
            </a>

            {{-- ===================== 03 · NFC ===================== --}}
            <a href="{{ route('nfc.login.form') }}"
               id="nfc-login-card"
               aria-label="Login dengan tap kartu NFC"
               class="option-card glass-card rounded-2xl
                      p-5 sm:p-6 text-center block fade-up
                      border-2 border-transparent
                      hover:border-blue-400/50 dark:hover:border-blue-500/40"
               style="animation-delay:.30s;">
                <span class="card-number bg-blue-100/90 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                    03
                </span>

                {{-- Ikon NFC + gelombang --}}
                <div class="nfc-stage">
                    <span class="nfc-wave"></span>
                    <span class="nfc-wave d-1"></span>
                    <span class="nfc-wave d-2"></span>

                    <div class="icon-tile tile--blue">
                        <i class="fas fa-wifi tile__icon"></i>
                    </div>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">
                    Login NFC
                </h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Tap kartu NFC ke perangkat
                </p>

                {{-- Badge status --}}
                <div id="nfc-status-badge"
                     class="mt-3 inline-flex items-center gap-1.5
                            text-[10px] font-semibold px-2.5 py-1 rounded-full
                            bg-slate-100 text-slate-500
                            dark:bg-slate-700/60 dark:text-slate-300">
                    <span id="nfc-status-dot" class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    <span id="nfc-status-text">Memeriksa…</span>
                </div>

                <span id="nfc-action-text"
                      class="mt-3 inline-flex items-center gap-1 text-xs sm:text-sm
                             font-semibold text-blue-600 dark:text-blue-400">
                    Tap Kartu
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </span>
            </a>
        </div>

        {{-- ========================================================== --}}
        {{-- FALLBACK INFO NFC --}}
        {{-- ========================================================== --}}
        <div id="nfc-info-box"
             class="mt-6 hidden rounded-2xl border
                    border-amber-200 dark:border-amber-500/30
                    bg-amber-50/80 dark:bg-amber-500/10
                    px-4 py-3 text-xs text-amber-700 dark:text-amber-300
                    fade-up">
            <div class="flex items-start gap-3">
                <i class="fas fa-circle-info mt-0.5 text-base"></i>
                <div class="leading-relaxed">
                    <p class="font-semibold">Perangkat ini tidak mendukung Web NFC.</p>
                    <p class="mt-1 opacity-90">
                        Login NFC hanya berjalan di
                        <b>Chrome / Edge Android</b> melalui koneksi <b>HTTPS</b>.
                        Silakan gunakan
                        <a href="{{ route('login.manual.form') }}"
                           class="underline font-semibold hover:text-amber-900 dark:hover:text-amber-200">Login Manual</a>
                        atau
                        <a href="{{ route('login.qr.form') }}"
                           class="underline font-semibold hover:text-amber-900 dark:hover:text-amber-200">Login QR</a>.
                    </p>
                </div>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- LINK TAMBAHAN --}}
        {{-- ========================================================== --}}
        <div class="mt-10 text-center text-sm space-y-2 fade-up" style="animation-delay:.4s;">
            <div>
                <a href="{{ route('guest.register') }}"
                   class="text-slate-600 dark:text-slate-400
                          hover:text-cyan-500 dark:hover:text-cyan-400
                          transition-colors">
                    Belum punya akun?
                    <span class="font-semibold underline decoration-dotted underline-offset-4">
                        Daftar Pengunjung
                    </span>
                </a>
            </div>
            <div>
                <a href="{{ route('password.request') }}"
                   class="text-slate-600 dark:text-slate-400
                          hover:text-cyan-500 dark:hover:text-cyan-400
                          transition-colors">
                    Lupa password?
                    <span class="font-semibold underline decoration-dotted underline-offset-4">
                        Reset di sini
                    </span>
                </a>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- FOOTER --}}
        {{-- ========================================================== --}}
        <p class="mt-10 text-center text-[11px] text-slate-400 dark:text-slate-600 tracking-wide">
            &copy; {{ date('Y') }} UKM Pemrograman UNICODE &middot; All rights reserved
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 500, once: true });

    /* =============================================================
     | DETEKSI DUKUNGAN WEB NFC — untuk kartu ke-3
     | Status:
     |   • "NFC Aktif"      → hijau
     |   • "Butuh HTTPS"    → amber
     |   • "Tidak Didukung" → merah
     ============================================================= */
    document.addEventListener('DOMContentLoaded', () => {
        const card       = document.getElementById('nfc-login-card');
        const badge      = document.getElementById('nfc-status-badge');
        const badgeDot   = document.getElementById('nfc-status-dot');
        const badgeText  = document.getElementById('nfc-status-text');
        const actionText = document.getElementById('nfc-action-text');
        const infoBox    = document.getElementById('nfc-info-box');

        if (!card) return;

        const setBadge = (text, badgeClass, dotClass) => {
            badgeText.textContent = text;
            badge.className =
                'mt-3 inline-flex items-center gap-1.5 text-[10px] font-semibold ' +
                'px-2.5 py-1 rounded-full ' + badgeClass;
            badgeDot.className = 'w-1.5 h-1.5 rounded-full ' + dotClass;
        };

        const disableCard = (label, badgeClass, dotClass) => {
            setBadge(label, badgeClass, dotClass);
            card.classList.add('nfc-disabled');
            card.setAttribute('aria-disabled', 'true');
            card.addEventListener('click', e => e.preventDefault());
            actionText.innerHTML = 'Tidak Tersedia';
            infoBox.classList.remove('hidden');
        };

        // 1) Secure context?
        if (!window.isSecureContext) {
            return disableCard(
                'Butuh HTTPS',
                'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                'bg-amber-500'
            );
        }

        // 2) Dukungan Web NFC?
        if (!('NDEFReader' in window)) {
            return disableCard(
                'Tidak Didukung',
                'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300',
                'bg-red-500'
            );
        }

        // 3) Siap
        setBadge(
            'NFC Aktif',
            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
            'bg-emerald-500'
        );
        actionText.innerHTML = 'Tap Kartu <i class="fas fa-arrow-right text-[10px]"></i>';
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Masuk – UKM Pemrograman UNICODE')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }

        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-card {
            background: rgba(31, 41, 55, 0.75);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .option-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        /* Aksen khusus kartu NFC */
        .nfc-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(6,182,212,0.15), transparent 60%);
            pointer-events: none;
        }

        /* Animasi gelombang NFC */
        .nfc-wave {
            position: absolute;
            border-radius: 9999px;
            border: 2px solid rgba(6, 182, 212, 0.5);
            animation: nfcPulse 2s ease-out infinite;
            pointer-events: none;
        }
        .nfc-wave.delay-1 { animation-delay: 0.6s; }
        .nfc-wave.delay-2 { animation-delay: 1.2s; }

        @keyframes nfcPulse {
            0%   { width: 40px; height: 40px; opacity: 0.8; }
            100% { width: 140px; height: 140px; opacity: 0; }
        }

        /* Kartu NFC non-aktif */
        .nfc-disabled {
            opacity: 0.55;
            filter: grayscale(0.6);
            cursor: not-allowed;
        }
        .nfc-disabled:hover {
            transform: none !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        }

        /* Badge kecil di pojok kartu */
        .card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 9999px;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="w-full max-w-3xl" data-aos="fade-up" data-aos-duration="600">

        {{-- ============================= --}}
        {{-- LOGO & HEADER --}}
        {{-- ============================= --}}
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-unicode.png') }}"
                 alt="UNICODE"
                 class="w-20 h-20 mx-auto rounded-2xl shadow-lg"
                 onerror="this.onerror=null;this.src='https://placehold.co/100x100/06b6d4/white?text=UN'">
            <h1 class="mt-4 text-3xl font-extrabold bg-gradient-to-r from-cyan-500 to-violet-500 bg-clip-text text-transparent">
                Masuk ke UNICODE
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Pilih metode masuk yang Anda inginkan
            </p>
        </div>

        {{-- ============================= --}}
        {{-- 3 KARTU PILIHAN LOGIN --}}
        {{-- ============================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- ================= LOGIN MANUAL ================= --}}
            <a href="{{ route('login.manual.form') }}"
               class="option-card glass-card rounded-2xl shadow-xl p-6 text-center block group border-2 border-transparent hover:border-cyan-400/40">
                <span class="card-badge bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300">
                    01
                </span>
                <div class="w-16 h-16 mx-auto bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center text-3xl text-cyan-600 dark:text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Login Manual</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Gunakan NIM &amp; password
                </p>
                <span class="inline-block mt-4 text-sm text-cyan-600 dark:text-cyan-400 font-semibold group-hover:underline">
                    Masuk →
                </span>
            </a>

            {{-- ================= LOGIN QR ================= --}}
            <a href="{{ route('login.qr.form') }}"
               class="option-card glass-card rounded-2xl shadow-xl p-6 text-center block group border-2 border-transparent hover:border-violet-400/40">
                <span class="card-badge bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">
                    02
                </span>
                <div class="w-16 h-16 mx-auto bg-violet-100 dark:bg-violet-900/30 rounded-full flex items-center justify-center text-3xl text-violet-600 dark:text-violet-400 mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Login QR</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Scan QR Code anggota
                </p>
                <span class="inline-block mt-4 text-sm text-violet-600 dark:text-violet-400 font-semibold group-hover:underline">
                    Scan →
                </span>
            </a>

            {{-- ================= LOGIN NFC (BARU) ================= --}}
            <a href="{{ route('nfc.login.form') }}"
               id="nfc-login-card"
               class="option-card nfc-card glass-card rounded-2xl shadow-xl p-6 text-center block group border-2 border-transparent hover:border-cyan-400/40">
                <span class="card-badge bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300">
                    03
                </span>

                {{-- Ikon NFC + animasi gelombang --}}
                <div class="relative w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="nfc-wave"></span>
                        <span class="nfc-wave delay-1"></span>
                        <span class="nfc-wave delay-2"></span>
                    </div>
                    <div class="relative w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center text-3xl text-white shadow-lg shadow-cyan-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-wifi"></i>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Login NFC</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Tap kartu NFC ke perangkat
                </p>

                {{-- Status deteksi NFC --}}
                <div id="nfc-status-badge"
                     class="mt-2 inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full
                            bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                    <span id="nfc-status-dot"
                          class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    <span id="nfc-status-text">Memeriksa perangkat…</span>
                </div>

                <span id="nfc-action-text"
                      class="inline-block mt-3 text-sm text-cyan-600 dark:text-cyan-400 font-semibold group-hover:underline">
                    Tap Kartu →
                </span>
            </a>

        </div>

        {{-- ============================= --}}
        {{-- INFO TAMBAHAN UNTUK NFC --}}
        {{-- ============================= --}}
        <div id="nfc-info-box"
             class="mt-6 hidden rounded-xl border border-amber-200 dark:border-amber-700/40
                    bg-amber-50/80 dark:bg-amber-900/20 px-4 py-3 text-xs
                    text-amber-700 dark:text-amber-300">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle mt-0.5"></i>
                <div>
                    <p class="font-semibold">Perangkat ini tidak mendukung Web NFC.</p>
                    <p class="mt-0.5 opacity-90">
                        Login NFC hanya dapat digunakan melalui browser
                        <b>Chrome/Edge di Android</b> dengan koneksi
                        <b>HTTPS</b>. Silakan gunakan
                        <a href="{{ route('login.manual.form') }}" class="underline font-semibold">Login Manual</a>
                        atau
                        <a href="{{ route('login.qr.form') }}" class="underline font-semibold">Login QR</a>.
                    </p>
                </div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- LINK TAMBAHAN --}}
        {{-- ============================= --}}
        <div class="mt-8 text-center text-sm space-y-2">
            <a href="{{ route('guest.register') }}"
               class="text-gray-600 dark:text-gray-400 hover:text-cyan-500">
                Belum punya akun? <span class="font-semibold">Daftar Pengunjung</span>
            </a>
            <br>
            <a href="{{ route('password.request') }}"
               class="text-gray-600 dark:text-gray-400 hover:text-cyan-500">
                Lupa password? <span class="font-semibold">Reset di sini</span>
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 500, once: true });

    /* =========================================================
     | DETEKSI DUKUNGAN WEB NFC
     | Menampilkan status di kartu NFC:
     |  - READY          → perangkat & browser mendukung
     |  - UNSUPPORTED    → perangkat/browser tidak mendukung
     |  - UNSAFE         → bukan secure context (bukan HTTPS/localhost)
     | ========================================================= */
    document.addEventListener('DOMContentLoaded', function () {
        const card       = document.getElementById('nfc-login-card');
        const badgeText  = document.getElementById('nfc-status-text');
        const badgeDot   = document.getElementById('nfc-status-dot');
        const actionText = document.getElementById('nfc-action-text');
        const infoBox    = document.getElementById('nfc-info-box');

        function setBadge(text, colorClass, dotClass) {
            badgeText.textContent = text;
            badgeText.parentElement.className =
                'mt-2 inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full ' +
                colorClass;
            badgeDot.className = 'w-1.5 h-1.5 rounded-full ' + dotClass;
        }

        // 1. Cek secure context (HTTPS / localhost)
        if (!window.isSecureContext) {
            setBadge('Butuh HTTPS', 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300', 'bg-amber-500');
            card.classList.add('nfc-disabled');
            card.setAttribute('aria-disabled', 'true');
            card.addEventListener('click', e => e.preventDefault());
            actionText.textContent = 'Tidak Tersedia';
            infoBox.classList.remove('hidden');
            return;
        }

        // 2. Cek dukungan NDEFReader (Web NFC)
        if (!('NDEFReader' in window)) {
            setBadge('Tidak Didukung', 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300', 'bg-red-500');
            card.classList.add('nfc-disabled');
            card.setAttribute('aria-disabled', 'true');
            card.addEventListener('click', e => e.preventDefault());
            actionText.textContent = 'Tidak Tersedia';
            infoBox.classList.remove('hidden');
            return;
        }

        // 3. Perangkat mendukung
        setBadge('NFC Aktif', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300', 'bg-emerald-500');
        actionText.textContent = 'Tap Kartu →';
    });
</script>
@endpush
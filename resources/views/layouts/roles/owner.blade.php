{{-- resources/views/layouts/roles/owner.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">

    {{-- viewport-fit=cover → dukung iPhone notch / Android cutout --}}
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard Owner UKM Pemrograman UNICODE">

    {{-- Theme color adaptive --}}
    <meta name="theme-color" content="#0B1F6B" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#050A30" media="(prefers-color-scheme: dark)">

    {{-- ============================================================
         DEVICE DETECTION — jalan SEBELUM paint untuk hindari FOUC
         Set: html[data-device], html[data-bp], html[data-touch]
         CSS var: --vh (real viewport height untuk mobile browser bar)
    ============================================================ --}}
    <script>
        (function () {
            try {
                var w = window.innerWidth;
                var h = window.innerHeight;
                var ua = navigator.userAgent || '';
                var isTouch  = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
                var isMobile = /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);
                var isTablet = /iPad|Android(?!.*Mobile)|Tablet|PlayBook|Silk/i.test(ua);
                var bp =
                    w >= 1536 ? '2xl' :
                    w >= 1280 ? 'xl'  :
                    w >= 1024 ? 'lg'  :
                    w >= 768  ? 'md'  :
                    w >= 640  ? 'sm'  : 'xs';
                var dev = isMobile ? 'mobile' : (isTablet ? 'tablet' : 'desktop');
                var el = document.documentElement;
                el.setAttribute('data-device', dev);
                el.setAttribute('data-bp', bp);
                el.setAttribute('data-touch', isTouch ? '1' : '0');
                el.setAttribute('data-orientation', w > h ? 'landscape' : 'portrait');
                el.style.setProperty('--vh', (h * 0.01) + 'px');
                el.style.setProperty('--vw', (w * 0.01) + 'px');
                if (isTouch) document.body && document.body.classList.add('is-touch');
            } catch (e) { /* noop */ }
        })();
    </script>

    <title>@yield('title', 'Owner Dashboard – UNICODE')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
          rel="stylesheet">

    {{-- Vite (app.css & app.js) — produksi --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tailwind CDN — fallback dev (bisa dihapus saat produksi) --}}
    @env('local')
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            midnight: '#050A30',
                            deepblue: '#0B1F6B',
                            electric: '#2563EB',
                            cyan:     '#00D9FF',
                            violet:   '#9333EA',
                        },
                        fontFamily: { sans: ['Inter', 'sans-serif'] },
                        screens: {
                            '3xl': '1920px',
                        },
                    }
                }
            };
        </script>
    @endenv

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* =========================================================
         * 1. RESET ANTI OVERFLOW GLOBAL
         * ======================================================= */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }
        img, video, canvas, svg, iframe {
            max-width: 100%;
            height: auto;
        }
        table {
            max-width: 100%;
        }

        /* =========================================================
         * 2. SCROLLBAR
         * ======================================================= */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; height: 4px; }

        /* =========================================================
         * 3. SIDEBAR
         * ======================================================= */
        .sidebar-transition {
            transition: transform .3s cubic-bezier(.4,0,.2,1),
                        width     .3s cubic-bezier(.4,0,.2,1);
        }
                /* =========================================================
         * 3a. SIDEBAR CLOSED — FULLY HIDDEN (desktop)
         * ======================================================= */
        @media (min-width: 1024px) {
            .sidebar-transition.sidebar-collapsed {
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0 !important;
                overflow: hidden !important;
                border-right-width: 0 !important;
                opacity: 0;
                pointer-events: none;
                transition: width .3s cubic-bezier(.4,0,.2,1),
                            opacity .15s ease .1s;
            }
            .sidebar-transition.sidebar-collapsed > * {
                opacity: 0;
                transform: translateX(-8px);
                transition: opacity .15s ease, transform .15s ease;
            }
        }

        /* =========================================================
         * 3b. FLOATING TOGGLE BUTTON (muncul saat sidebar tertutup)
         * ======================================================= */
        #sidebar-open-btn {
            transition: opacity .2s ease, transform .2s ease;
        }
        #sidebar-open-btn.is-hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateX(-8px);
        }

        /* Mobile sidebar slide */
        .sidebar-mobile {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: min(280px, 85vw);
            transform: translateX(-100%);
            z-index: 1000;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            padding-bottom: env(safe-area-inset-bottom, 0);
        }
        .sidebar-mobile.open { transform: translateX(0); }

        .sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(2px);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* =========================================================
         * 4. LAYOUT
         * ======================================================= */
        .main-content-area {
            transition: margin-left .3s cubic-bezier(.4,0,.2,1);
            min-width: 0;
            max-width: 100%;
            width: 100%;
        }
        .main-content-area > main > * { min-width: 0; }

        @media (max-width: 1023.98px) {
            .main-content-area { margin-left: 0 !important; }
        }

        /* =========================================================
         * 5. SAFE AREA (iPhone notch / Android cutout)
         * ======================================================= */
        @supports (padding: max(0px)) {
            .safe-top    { padding-top:    max(env(safe-area-inset-top),    0px); }
            .safe-bottom { padding-bottom: max(env(safe-area-inset-bottom), 0px); }
            .safe-left   { padding-left:   max(env(safe-area-inset-left),   0px); }
            .safe-right  { padding-right:  max(env(safe-area-inset-right),  0px); }
            .safe-x      { padding-left:  max(env(safe-area-inset-left),  0px);
                           padding-right: max(env(safe-area-inset-right), 0px); }
            .safe-all    { padding: max(env(safe-area-inset-top),    0px)
                                    max(env(safe-area-inset-right),  0px)
                                    max(env(safe-area-inset-bottom), 0px)
                                    max(env(safe-area-inset-left),   0px); }
        }

        /* =========================================================
         * 6. RESPONSIVE TABLE → CARD (auto transform di mobile)
         * Pemakaian: <div class="responsive-table"><table>…
         * Pastikan setiap <td> punya data-label="Nama Kolom"
         * ======================================================= */
        @media (max-width: 767.98px) {
            .responsive-table { overflow: visible !important; }
            .responsive-table table,
            .responsive-table thead,
            .responsive-table tbody,
            .responsive-table tr,
            .responsive-table th,
            .responsive-table td { display: block; width: 100%; }

            .responsive-table thead {
                position: absolute;
                left: -9999px;
                top: -9999px;
                opacity: 0;
            }

            .responsive-table tbody { padding: 0; }

            .responsive-table tr {
                background: #ffffff;
                border-radius: .75rem;
                padding: .85rem .95rem;
                margin-bottom: .6rem;
                box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
                border: 1px solid rgb(226 232 240);
            }
            .dark .responsive-table tr {
                background: rgb(30 41 59 / .65);
                border-color: rgb(255 255 255 / .06);
                box-shadow: 0 1px 3px rgba(0,0,0,.4);
            }

            .responsive-table td {
                padding: .35rem 0 !important;
                border: none !important;
                text-align: left !important;
                font-size: .875rem;
                line-height: 1.4;
            }

            .responsive-table td::before {
                content: attr(data-label);
                display: block;
                font-size: .65rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .05em;
                color: #64748b;
                margin-bottom: .1rem;
            }
            .dark .responsive-table td::before { color: #94a3b8; }

            .responsive-table td[data-label=""]::before,
            .responsive-table td:not([data-label])::before { content: ""; }

            /* Baris empty-state / colspan jangan dibungkus card */
            .responsive-table tr:has(td[colspan]) {
                background: transparent !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
            .responsive-table tr:has(td[colspan]) td {
                padding: 2rem 0 !important;
                text-align: center !important;
            }
        }

        /* =========================================================
         * 7. DROPDOWN / MODAL / TOAST
         * ======================================================= */
        .dropdown-enter { animation: slideDown .2s ease forwards; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px) scale(.96); }
            to   { opacity: 1; transform: translateY(0)     scale(1); }
        }
        .badge-pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.15); }
        }
        .modal-backdrop {
            background: rgba(0, 0, 0, .6);
            backdrop-filter: blur(4px);
        }
        [x-cloak] { display: none !important; }

        /* =========================================================
         * 8. TOUCH TARGETS (aksesibilitas di perangkat sentuh)
         * ======================================================= */
        @media (pointer: coarse) {
            button, a[role="button"], [role="button"] {
                min-height: 40px;
            }
            input, select, textarea {
                font-size: 16px !important; /* cegah zoom otomatis iOS */
            }
        }

        /* =========================================================
         * 9. REDUCED MOTION
         * ======================================================= */
                @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* =========================================================
         * 10. LOGOUT MODAL — CUSTOM CSS (tidak bergantung Tailwind)
         * ======================================================= */
        .logout-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            opacity: 0;
            pointer-events: none;
            visibility: hidden;
            transition: opacity .25s ease, visibility 0s linear .25s;
        }
        .logout-modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
            visibility: visible;
            transition: opacity .25s ease, visibility 0s;
        }
        .logout-modal-box {
            transform: scale(.92) translateY(8px);
            transition: transform .3s cubic-bezier(.16, 1, .3, 1);
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }
        .logout-modal-overlay.is-open .logout-modal-box {
            transform: scale(1) translateY(0);
        }

                /* Mobile: modal tetap center, sedikit padding lebih kecil */
        @media (max-width: 640px) {
            .logout-modal-overlay {
                align-items: center;
                padding: 1rem;
            }
            .logout-modal-box {
                border-radius: 1.25rem;
                max-width: 100%;
                transform: scale(.92) translateY(8px);
            }
            .logout-modal-overlay.is-open .logout-modal-box {
                transform: scale(1) translateY(0);
            }
                        .logout-modal-box {
                border-radius: 1.25rem;
                max-width: 100%;
                transform: scale(.92) translateY(8px);
                max-height: calc(100vh - 2rem);
            }
        }

        /* =========================================================
         * 11. ICON HAMBURGER CONSISTENT
         * ======================================================= */
        .icon-hamburger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.25rem;
            height: 1.25rem;
            font-size: .875rem;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased">

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay"
         class="sidebar-overlay"
         onclick="closeMobileSidebar()"
         role="presentation"></div>

    {{-- Floating toggle button — muncul HANYA saat sidebar tertutup di desktop --}}
    <button id="sidebar-open-btn"
        type="button"
        onclick="toggleSidebar()"
        aria-label="Buka sidebar"
        title="Buka sidebar"
        class="hidden lg:flex fixed top-4 left-4 z-[1001]
               w-11 h-11 items-center justify-center
               rounded-xl bg-white dark:bg-gray-800
               shadow-lg shadow-gray-300/40 dark:shadow-black/40
               border border-gray-200/80 dark:border-gray-700/80
               text-gray-600 dark:text-gray-300
               hover:text-amber-600 dark:hover:text-amber-400
               hover:border-amber-300 dark:hover:border-amber-600
               hover:scale-105 active:scale-95
               backdrop-blur-sm is-hidden">
    <i class="fas fa-bars icon-hamburger"></i>
</button>

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        @include('partials.roles.owner.sidebar')

        {{-- MAIN CONTENT --}}
        <div id="main-content"
             class="main-content-area flex-1 flex flex-col h-screen overflow-hidden min-w-0">
            @include('partials.roles.owner.header')

            <main class="flex-1 overflow-y-auto overflow-x-hidden p-3 md:p-6 scroll-smooth
                         bg-gray-50 dark:bg-gray-900 safe-x">
                @yield('content')
            </main>

            @include('partials.roles.owner.footer')
        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
         LOGOUT MODAL
    ═════════════════════════════════════════════════════════════ --}}
     {{-- ═════════════════════════════════════════════════════════════
     LOGOUT MODAL — Custom CSS untuk positioning yang konsisten
═════════════════════════════════════════════════════════════ --}}
<div id="logout-confirm-modal"
     class="logout-modal-overlay"
     role="dialog"
     aria-modal="true"
     aria-labelledby="logout-modal-title"
     aria-hidden="true">

    <div id="logout-modal-content"
         class="logout-modal-box
                bg-white dark:bg-gray-800
                rounded-2xl sm:rounded-3xl
                shadow-2xl
                w-full
                max-w-sm sm:max-w-lg lg:max-w-2xl
                p-5 sm:p-8 lg:p-10
                border border-gray-100 dark:border-gray-700/60">

        <div class="text-center">
            {{-- Icon circle --}}
            <div class="mx-auto flex items-center justify-center
                        h-16 w-16 sm:h-20 sm:w-20 lg:h-24 lg:w-24
                        rounded-full bg-red-100 dark:bg-red-900/20
                        mb-4 sm:mb-5 lg:mb-6
                        ring-4 sm:ring-8 ring-red-50 dark:ring-red-900/10">
                <i class="fas fa-sign-out-alt
                          text-3xl sm:text-4xl lg:text-5xl
                          text-red-600 dark:text-red-400"></i>
            </div>

            {{-- Title --}}
            <h3 id="logout-modal-title"
                class="text-xl sm:text-2xl lg:text-3xl
                       font-bold text-gray-900 dark:text-white
                       tracking-tight">
                Konfirmasi Keluar
            </h3>

            {{-- Description --}}
            <p class="mt-2 sm:mt-3 lg:mt-4
                      text-sm sm:text-base lg:text-lg
                      text-gray-500 dark:text-gray-400
                      leading-relaxed
                      max-w-md lg:max-w-lg mx-auto">
                Apakah Anda yakin ingin keluar dari akun?
                Anda harus login kembali untuk mengakses dashboard.
            </p>
        </div>

        {{-- Actions --}}
        <div class="mt-6 sm:mt-8 lg:mt-10
                    flex flex-col-reverse sm:flex-row
                    gap-3 sm:gap-4
                    justify-center">
            <button type="button" onclick="closeLogoutModal()"
                    class="px-5 sm:px-8 lg:px-10
                           py-2.5 sm:py-3 lg:py-3.5
                           text-sm sm:text-base font-medium
                           text-gray-700 dark:text-gray-300
                           bg-gray-100 dark:bg-gray-700 rounded-xl
                           hover:bg-gray-200 dark:hover:bg-gray-600
                           active:scale-[0.98]
                           transition
                           min-h-[44px] sm:min-h-[48px]
                           sm:min-w-[140px] lg:min-w-[160px]">
                Batal
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                  class="inline-flex w-full sm:w-auto">
                @csrf
                <button type="submit"
                        class="px-5 sm:px-8 lg:px-10
                               py-2.5 sm:py-3 lg:py-3.5
                               text-sm sm:text-base font-semibold text-white
                               bg-red-600 rounded-xl
                               hover:bg-red-700
                               active:scale-[0.98]
                               transition shadow-md shadow-red-500/20
                               w-full sm:min-w-[140px] lg:min-w-[160px]
                               min-h-[44px] sm:min-h-[48px]
                               inline-flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>

    {{-- ═════════════════════════════════════════════════════════════
         TOAST
    ═════════════════════════════════════════════════════════════ --}}
    <div x-data="{ toasts: [] }"
         @notify.window="
            const id = Date.now();
            toasts.push({ id, ...$event.detail });
            setTimeout(() => toasts = toasts.filter(t => t.id !== id), 3500);
         "
         class="fixed z-[100] pointer-events-none
                top-3 right-3 left-3 sm:left-auto sm:max-w-sm
                space-y-2 safe-top">
        <template x-for="t in toasts" :key="t.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 :class="t.type === 'error'   ? 'bg-red-500'
                       : t.type === 'warning' ? 'bg-amber-500'
                                              : 'bg-emerald-500'"
                 class="pointer-events-auto text-white text-sm px-4 py-3 rounded-xl
                        shadow-lg flex items-center gap-2">
                <i class="fas"
                   :class="t.type === 'error'   ? 'fa-exclamation-circle'
                         : t.type === 'warning' ? 'fa-exclamation-triangle'
                                                : 'fa-check-circle'"></i>
                <span x-text="t.message" class="flex-1"></span>
            </div>
        </template>
    </div>

    {{-- ═════════════════════════════════════════════════════════════
         GLOBAL SCRIPTS
    ═════════════════════════════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        /* ---------- SIDEBAR ---------- */
        const sidebar     = document.querySelector('.sidebar-transition');
        const overlay     = document.getElementById('sidebar-overlay');
        const mainContent = document.getElementById('main-content');

                function toggleSidebar() {
            if (!sidebar) return;
            if (window.innerWidth < 1024) {
                sidebar.classList.toggle('open');
                overlay?.classList.toggle('active');
            } else {
                sidebar.classList.toggle('sidebar-collapsed');
                try {
                    localStorage.setItem(
                        'owner.sidebar.collapsed',
                        sidebar.classList.contains('sidebar-collapsed') ? '1' : '0'
                    );
                } catch (e) {}
                // Sinkronisasi floating button
                updateSidebarOpenButton();
            }
        }

        /* Tampilkan/sembunyikan floating button sesuai state sidebar */
        function updateSidebarOpenButton() {
            const btn = document.getElementById('sidebar-open-btn');
            if (!btn || !sidebar) return;

            const isDesktop  = window.innerWidth >= 1024;
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');

            if (isDesktop && isCollapsed) {
                btn.classList.remove('is-hidden');
            } else {
                btn.classList.add('is-hidden');
            }
        }

        function closeMobileSidebar() {
            if (!sidebar) return;
            if (window.innerWidth < 1024) {
                sidebar.classList.remove('open');
                overlay?.classList.remove('active');
            }
        }
                   // Restore state desktop
        if (window.innerWidth >= 1024 && sidebar) {
            try {
                if (localStorage.getItem('owner.sidebar.collapsed') === '1') {
                    sidebar.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
            // Sinkronkan floating button dengan state saat ini
            updateSidebarOpenButton();
        }

        // ESC untuk tutup
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeMobileSidebar();
                closeLogoutModal();
            }
        });

                window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) closeMobileSidebar();
            updateSidebarOpenButton();
        });

        /* ---------- LOGOUT MODAL ---------- */
               /* ---------- LOGOUT MODAL ---------- */
        const logoutModal = document.getElementById('logout-confirm-modal');

        function openLogoutModal() {
            if (!logoutModal) return;
            logoutModal.classList.add('is-open');
            logoutModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeLogoutModal() {
            if (!logoutModal) return;
            logoutModal.classList.remove('is-open');
            logoutModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        /* Klik backdrop → tutup */
        logoutModal?.addEventListener('click', (e) => {
            if (e.target === logoutModal) closeLogoutModal();
        });

        document.addEventListener('click', e => {
            const t = e.target.closest('[data-trigger-logout], [data-action="logout"]');
            if (t) { e.preventDefault(); openLogoutModal(); }
        });
        /* ==========================================================
         * EXPOSE — global functions
         * ======================================================== */
        window.toggleSidebar           = toggleSidebar;
        window.closeMobileSidebar      = closeMobileSidebar;
        window.openLogoutModal         = openLogoutModal;
        window.closeLogoutModal        = closeLogoutModal;
        window.updateSidebarOpenButton = updateSidebarOpenButton;

        /* ==========================================================
         * ALIAS — dukung pemanggilan lama
         * `showLogoutModal()` dari sidebar.blade.php → openLogoutModal()
         * ======================================================== */
        window.showLogoutModal = function (e) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            openLogoutModal();
        };

        /* ==========================================================
         * KEYBOARD SHORTCUT
         *  - Ctrl + B / Cmd + B  → toggle sidebar (desktop & mobile)
         *  - Esc                 → tutup modal + sidebar mobile
         * ======================================================== */
        document.addEventListener('keydown', (e) => {
            const isMod = e.ctrlKey || e.metaKey; // Ctrl (Win/Linux) atau Cmd (Mac)

            // Ctrl/Cmd + B → toggle sidebar
            if (isMod && (e.key === 'b' || e.key === 'B')) {
                e.preventDefault();
                toggleSidebar();
                return;
            }

            // Esc → tutup semua
            if (e.key === 'Escape') {
                closeMobileSidebar();
                if (typeof closeLogoutModal === 'function') closeLogoutModal();
            }
        });

        /* ==========================================================
         * RE-SET CSS VAR --vh saat resize / orientasi
         * ======================================================== */
        const setVH = () => {
            document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px');
        };
        window.addEventListener('resize', setVH, { passive: true });
        window.addEventListener('orientationchange', setVH, { passive: true });
    });
    </script>

    @stack('scripts')
</body>
</html>
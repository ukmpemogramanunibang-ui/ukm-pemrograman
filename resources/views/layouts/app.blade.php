<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'UKM Pemrograman UNICODE')</title>
    <meta name="description" content="UKM Pemrograman UNICODE – Komunitas mahasiswa pengembang teknologi, inovator, dan pembelajar sejati di Universitas Bhakti Asih." />
    <meta name="keywords" content="UKM Pemrograman, UNICODE, Universitas Bhakti Asih, programming, coding, IT, teknologi, komunitas" />
    <meta name="author" content="UKM Pemrograman UNICODE" />

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

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
                        cyan: '#00D9FF',
                        purple: '#7C3AED',
                        violet: '#9333EA',
                        unicode: {
                            50:  '#f0f4ff',
                            100: '#dbe4ff',
                            200: '#baceff',
                            300: '#8fb2ff',
                            400: '#5e8fff',
                            500: '#2563EB',
                            600: '#1a4dc4',
                            700: '#123a96',
                            800: '#0d2a6e',
                            900: '#050A30',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        marquee:      'marquee 25s linear infinite',
                        float:        'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'gradient-x': 'gradient-x 15s ease infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        marquee: {
                            '0%':   { transform: 'translateX(100%)' },
                            '100%': { transform: 'translateX(-100%)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%':      { transform: 'translateY(-12px)' },
                        },
                        'gradient-x': {
                            '0%, 100%': { 'background-position': '0% 50%' },
                            '50%':      { 'background-position': '100% 50%' },
                        },
                        fadeInUp: {
                            '0%':   { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                }
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-midnight text-gray-800 dark:text-gray-200 antialiased transition-colors duration-300">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    @hasSection('header')
        @yield('header')
    @else
        @include('partials.dashboard.header')
    @endif

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT --}}
    {{-- ============================================================ --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- ============================================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================================ --}}
    @hasSection('footer')
        @yield('footer')
    @else
        @include('partials.dashboard.footer')
    @endif

    {{-- ============================================================ --}}
    {{-- GLOBAL SCRIPTS --}}
    {{-- ============================================================ --}}
    <script src="{{ asset('js/theme.js') }}"></script>

    {{-- ============================================================ --}}
    {{-- AUTH HEARTBEAT — cek status tanpa auto-redirect --}}
    {{--                                                            --}}
    {{-- ⚠️  ATURAN:                                                 --}}
    {{--   1. TIDAK boleh redirect ke dashboard kalau user          --}}
    {{--      sudah login dan sedang di halaman lain.                --}}
    {{--   2. Redirect HANYA saat session habis (HTTP 401).         --}}
    {{--   3. Interval longgar (5 menit) → hemat resource.          --}}
    {{-- ============================================================ --}}
    <script>
    (function () {
        'use strict';

        // Prefix halaman yang butuh auth
        const PROTECTED_PREFIXES = [
            '/owner',
            '/superadmin',
            '/ketua',
            '/pengurus',
            '/anggota',
            '/guest',
            '/dashboard',
        ];

        // Halaman publik — jangan jalankan heartbeat
        function isPublicPage() {
            const p = window.location.pathname;
            return p === '/'
                || p.startsWith('/login')
                || p.startsWith('/cek-akun')
                || p.startsWith('/room_publik')
                || p.startsWith('/explore')
                || p.startsWith('/about')
                || p.startsWith('/kontak');
        }

        function isProtectedPage() {
            const p = window.location.pathname;
            return PROTECTED_PREFIXES.some(prefix => p.startsWith(prefix));
        }

        /**
         * Cek auth status.
         * - 401 → redirect ke /login (kalau di halaman protected)
         * - 200 → TIDAK redirect (biarkan user di halaman manapun)
         */
        function checkAuth() {
            if (isPublicPage()) return;

            fetch('/check-auth-status', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            })
            .then(response => {
                if (response.status === 401) {
                    // Session habis → redirect ke login
                    if (isProtectedPage() && !window.location.pathname.startsWith('/login')) {
                        window.location.replace('/login?reason=session_expired');
                    }
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                // ✅ Cukup simpan status internal — JANGAN redirect
                window.__unicodeAuth = {
                    authenticated: data.authenticated === true,
                    lastCheck: Date.now(),
                };
            })
            .catch(() => {
                // Network error → silent, jangan redirect
            });
        }

        // Cek sekali saat load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkAuth);
        } else {
            checkAuth();
        }

        // Heartbeat tiap 5 menit
        setInterval(() => {
            if (!document.hidden) checkAuth();
        }, 5 * 60 * 1000);

        // Cek ulang saat tab kembali aktif
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkAuth();
        });
    })();
    </script>

    {{-- ============================================================ --}}
    {{-- MODAL LOGOUT --}}
    {{-- ============================================================ --}}
    <div id="logout-confirm-modal"
         class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 text-red-600 dark:text-red-400 text-3xl">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Konfirmasi Keluar</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin keluar dari akun ini?</p>
                <div class="flex w-full gap-4">
                    <button onclick="closeLogoutModal()"
                            class="flex-1 py-2.5 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium">
                        Tidak
                    </button>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-md shadow-red-500/20 transition">
                            Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- LOGOUT MODAL SCRIPT --}}
    {{-- ============================================================ --}}
    <script>
    (function () {
        'use strict';

        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;

        window.showLogoutModal = function (event) {
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                const inner = modal.querySelector('.transform');
                if (inner) {
                    inner.classList.remove('scale-95');
                    inner.classList.add('scale-100');
                }
            }, 10);
        };

        window.closeLogoutModal = function () {
            modal.classList.add('opacity-0');
            const inner = modal.querySelector('.transform');
            if (inner) {
                inner.classList.remove('scale-100');
                inner.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        };

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                window.closeLogoutModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                window.closeLogoutModal();
            }
        });
    })();
    </script>

    {{-- Stack scripts per halaman --}}
    @stack('scripts')
</body>
</html>
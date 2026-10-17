{{-- resources/views/layouts/ketua.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ketua Dashboard') — UNICODE</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        midnight: '#050A30',
                        electric: '#2563EB',
                    },
                }
            }
        }
    </script>

    <script>
        (function () {
            try {
                var stored   = localStorage.getItem('sidebar_state');
                var isMobile = window.matchMedia('(max-width: 1023px)').matches;
                var state    = stored || (isMobile ? 'closed' : 'open');
                if (state === 'closed') document.documentElement.classList.add('sidebar-closed');

                var t = localStorage.getItem('theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (t === 'dark' || (!t && prefersDark)) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>

    <style>
        html {
            --sidebar-ease: cubic-bezier(.4, 0, .2, 1);
            --sidebar-duration: 280ms;
        }

        #sidebar {
            width: 16rem;
            transform: translate3d(0, 0, 0);
            transition: transform var(--sidebar-duration) var(--sidebar-ease);
            will-change: transform;
            backface-visibility: hidden;
        }

        #content-column {
            padding-left: 0;
            transition: padding-left var(--sidebar-duration) var(--sidebar-ease);
            will-change: padding-left;
        }

        @media (min-width: 1024px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100vh;
                z-index: 40;
            }
            #content-column { padding-left: 16rem; }
            html.sidebar-closed #sidebar { transform: translate3d(-100%, 0, 0); }
            html.sidebar-closed #content-column { padding-left: 0; }
        }

        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100vh;
                height: 100dvh;
                max-width: 88vw;
                z-index: 60;
                box-shadow: 4px 0 24px rgba(0, 0, 0, .18);
            }
            html.sidebar-closed #sidebar {
                transform: translate3d(-100%, 0, 0);
                box-shadow: none;
                pointer-events: none;
            }
            #content-column { padding-left: 0 !important; }
        }

        #sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity var(--sidebar-duration) var(--sidebar-ease);
            z-index: 50;
        }
        @media (min-width: 1024px) { #sidebar-overlay { display: none !important; } }
        @media (max-width: 1023px) {
            html:not(.sidebar-closed) #sidebar-overlay {
                opacity: 1;
                pointer-events: auto;
            }
        }

        #sidebar-toggle .icon-bars,
        #sidebar-toggle .icon-xmark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(1);
            transition: opacity .2s ease, transform .26s var(--sidebar-ease);
        }
        html:not(.sidebar-closed) #sidebar-toggle .icon-bars  { opacity: 0; transform: translate(-50%, -50%) scale(.55) rotate(-90deg); }
        html:not(.sidebar-closed) #sidebar-toggle .icon-xmark { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        html.sidebar-closed      #sidebar-toggle .icon-bars  { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        html.sidebar-closed      #sidebar-toggle .icon-xmark { opacity: 0; transform: translate(-50%, -50%) scale(.55) rotate(90deg); }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>
    @stack('styles')
</head>

@php
    $currentUserId = (int) (auth()->id() ?? 0);
    $sessionOwner  = (int) session('owner.explore.owner_id', 0);
    $sessionFlag   = (bool) session('owner.explore.active', false);

    $isExploring = $sessionFlag
        && $currentUserId > 0
        && $sessionOwner === $currentUserId
        && session('owner.explore.role_slug')
        && session('owner.explore.token')
        && session('owner.explore.user_id');

    $exploreTarget = null;
    if ($isExploring) {
        try {
            $exploreTarget = \App\Models\User::select('id', 'name', 'avatar_url', 'level_id')
                ->find(session('owner.explore.user_id'));
        } catch (\Throwable $e) {}
    }
    $exploreRole   = session('owner.explore.role_slug');
    $effectiveUser = $exploreTarget ?: auth()->user();
@endphp

<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 antialiased font-sans">

    <div id="sidebar-overlay" onclick="window.closeMobileSidebar && window.closeMobileSidebar()"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR (partial) --}}
        @includeIf('partials.roles.ketua.sidebar', ['user' => $effectiveUser])

        {{-- KOLOM KONTEN --}}
        <div id="content-column" class="flex-1 flex flex-col h-screen overflow-hidden w-full min-w-0">

            {{-- Banner explore --}}
            @if($isExploring && $exploreTarget)
                <div class="flex-shrink-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-600 text-white shadow-md">
                    <div class="flex items-center justify-between gap-3 px-4 py-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-eye text-xs"></i>
                            </span>
                            <div class="min-w-0 flex flex-wrap items-baseline gap-x-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider opacity-90">Mode Jelajah</span>
                                <span class="text-sm font-bold truncate">{{ $exploreTarget->name }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('owner.settings.explore.exit') }}" onsubmit="return confirm('Keluar dari mode jelajah?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/15 hover:bg-white/25 text-xs font-bold transition active:scale-95">
                                <i class="fas fa-sign-out-alt text-[11px]"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- HEADER --}}
            @includeIf('partials.roles.ketua.header', ['user' => $effectiveUser])

            {{-- FALLBACK HEADER kalau partial tidak ada --}}
            @unless(view()->exists('partials.roles.ketua.header'))
                <header class="flex-shrink-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800">
                    <div class="flex items-center justify-between px-4 py-3 gap-3">
                        <button id="sidebar-toggle" type="button" aria-label="Toggle sidebar"
                                class="relative w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                            <i class="fas fa-bars icon-bars text-gray-700 dark:text-gray-300"></i>
                            <i class="fas fa-times icon-xmark text-gray-700 dark:text-gray-300"></i>
                        </button>
                        <div class="flex items-center gap-2 ml-auto">
                            <button id="dark-toggle" type="button" aria-label="Toggle theme"
                                    class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                                <i class="fas fa-moon dark:hidden text-gray-700"></i>
                                <i class="fas fa-sun hidden dark:inline text-yellow-400"></i>
                            </button>
                            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-800">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($effectiveUser->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 truncate max-w-[120px]">
                                    {{ $effectiveUser->name ?? 'User' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </header>
            @endunless

            {{-- MAIN CONTENT --}}
            <main id="main-content" class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- LOGOUT MODAL --}}
    <div id="logout-modal" class="hidden fixed inset-0 z-[1100] bg-black/60 items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-3">
                <i class="fas fa-sign-out-alt text-2xl text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="font-bold text-lg mb-1 text-gray-900 dark:text-white">Keluar Akun?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Anda akan keluar dari sesi ini.</p>
            <div class="flex gap-2">
                <button type="button" onclick="hideLogoutModal()"
                    class="flex-1 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showLogoutModal(e) {
        if (e) e.preventDefault();
        const m = document.getElementById('logout-modal');
        if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
    }
    function hideLogoutModal() {
        const m = document.getElementById('logout-modal');
        if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
    }

    (function () {
        'use strict';
        if (window.__layoutControllerLoaded) return;
        window.__layoutControllerLoaded = true;

        const html = document.documentElement;
        const MOBILE_BREAKPOINT = 1024;
        const STORAGE_KEY = 'sidebar_state';
        const THEME_KEY = 'theme';

        function isMobile() { return window.innerWidth < MOBILE_BREAKPOINT; }
        function isOpen() { return !html.classList.contains('sidebar-closed'); }

        function setSidebar(open, persist = true) {
            if (isOpen() === open) return;
            html.classList.toggle('sidebar-closed', !open);
            const toggleBtn = document.getElementById('sidebar-toggle');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (persist) { try { localStorage.setItem(STORAGE_KEY, open ? 'open' : 'closed'); } catch (e) {} }
            document.dispatchEvent(new CustomEvent('sidebar:changed', { detail: { open } }));
        }

        window.closeMobileSidebar = function () { setSidebar(false); };

        document.addEventListener('click', function (e) {
            if (e.target.closest('#sidebar-toggle')) {
                e.preventDefault();
                setSidebar(!isOpen());
            }
        });

        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && !e.altKey && !e.shiftKey && (e.key === 'b' || e.key === 'B')) {
                e.preventDefault();
                setSidebar(!isOpen());
            }
            if (e.key === 'Escape' && isMobile() && isOpen()) window.closeMobileSidebar();
        });

        document.addEventListener('click', function (e) {
            if (!isMobile()) return;
            const link = e.target.closest('#sidebar a[href]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;
            setTimeout(window.closeMobileSidebar, 80);
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#dark-toggle');
            if (!btn) return;
            e.preventDefault();
            const isDark = html.classList.toggle('dark');
            try { localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light'); } catch (err) {}
        });

        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored === 'open' && html.classList.contains('sidebar-closed')) setSidebar(true, false);
            else if (stored === 'closed' && !html.classList.contains('sidebar-closed') && !isMobile()) setSidebar(false, false);
        } catch (e) {}
    })();
    </script>

    @stack('scripts')
</body>
</html>
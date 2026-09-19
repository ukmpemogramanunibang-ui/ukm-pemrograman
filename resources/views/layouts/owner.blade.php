<!DOCTYPE html>
<html lang="id" class="dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'UNICODE Owner Panel')</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Google Font Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 72px;
            --header-height: 64px;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f1f5f9;
            min-height: 100vh;
        }

        .dark body {
            background: #0f172a;
        }

        /* ===== SIDEBAR STYLES ===== */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
            border-right: 1px solid #e2e8f0;
        }

        .dark #sidebar {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            border-right-color: #1e293b;
        }

        #sidebar.sidebar-collapsed {
            width: var(--sidebar-collapsed);
        }

        #sidebar.sidebar-collapsed .menu-text,
        #sidebar.sidebar-collapsed .sidebar-subtitle,
        #sidebar.sidebar-collapsed .logo-text span:not(.logo-icon) {
            display: none !important;
        }

        #sidebar.sidebar-collapsed .menu-item {
            justify-content: center;
            padding: 0.75rem 0.5rem !important;
        }

        #sidebar.sidebar-collapsed .menu-icon-wrapper {
            margin-right: 0 !important;
        }

        #sidebar.sidebar-collapsed .dropdown-content {
            display: none !important;
        }

        /* ===== MOBILE SIDEBAR ===== */
        #sidebar.sidebar-mobile {
            position: fixed;
            left: -100%;
            top: 0;
            bottom: 0;
            width: 290px;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.15);
        }

        #sidebar.sidebar-mobile.open {
            left: 0;
        }

        #sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ===== SCROLLBAR ===== */
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }
        .dark #sidebar::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }

        @keyframes badge-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        .badge-pulse {
            animation: badge-pulse 2s ease-in-out infinite;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .dropdown-enter {
            animation: slideDown 0.2s ease-out forwards;
        }

        @keyframes rippleAnim {
            to { transform: translate(-50%, -50%) scale(1); opacity: 0; }
        }

        /* ===== MENU ITEM HOVER ===== */
        .menu-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .menu-item::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .menu-item:hover::after {
            opacity: 1;
        }

        /* ===== DROPDOWN ===== */
        .dropdown-content {
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            max-height: 0;
            opacity: 0;
        }

        .dropdown-content:not(.hidden) {
            max-height: 1500px;
            opacity: 1;
        }

        /* ===== GLASS EFFECT ===== */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass {
            background: rgba(15, 23, 42, 0.7);
            border-color: rgba(255, 255, 255, 0.05);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            #sidebar:not(.sidebar-mobile) {
                display: none;
            }
            #sidebar.sidebar-mobile {
                display: block !important;
            }
        }

        @media (min-width: 1025px) {
            #sidebar.sidebar-mobile {
                position: relative !important;
                left: 0 !important;
                width: var(--sidebar-width) !important;
            }
            #sidebar.sidebar-mobile.sidebar-collapsed {
                width: var(--sidebar-collapsed) !important;
            }
        }

        /* ===== UTILITY ===== */
        .safe-top {
            padding-top: env(safe-area-inset-top, 0);
        }

        .text-gradient {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .dark .text-gradient {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased">

    {{-- Overlay untuk mobile --}}
    <div id="sidebar-overlay"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        @include('partials.roles.owner.sidebar')

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            {{-- ===== HEADER ===== --}}
            @include('partials.roles.owner.header')

            {{-- ===== PAGE CONTENT ===== --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
                @yield('content')
            </main>

            {{-- ===== FOOTER ===== --}}
            @include('partials.roles.owner.footer')
        </div>
    </div>

    {{-- ===== LOGOUT MODAL ===== --}}
    <div id="logout-confirm-modal" class="hidden fixed inset-0 z-[1100] flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6 transform transition-all duration-300 scale-95">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center text-red-600 dark:text-red-400 text-3xl mb-4">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Konfirmasi Logout</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Apakah Anda yakin ingin keluar dari akun?</p>
            </div>
            <div class="flex gap-3 mt-6">
                <button onclick="closeLogoutModal()" class="flex-1 px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium text-sm">
                    Batal
                </button>
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout saya
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== SCRIPTS ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== LOGOUT MODAL =====
            window.showLogoutModal = function(event) {
                if (event) event.preventDefault();
                const modal = document.getElementById('logout-confirm-modal');
                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                    modal.querySelector('.transform').classList.add('scale-100');
                });
            };

            window.closeLogoutModal = function() {
                const modal = document.getElementById('logout-confirm-modal');
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-100');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 300);
            };

            document.getElementById('logout-confirm-modal')?.addEventListener('click', function(e) {
                if (e.target === this) closeLogoutModal();
            });

            // ===== KEYBOARD SHORTCUTS =====
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('logout-confirm-modal');
                    if (!modal.classList.contains('hidden')) closeLogoutModal();

                    const dropdowns = document.querySelectorAll('.dropdown-content:not(.hidden)');
                    dropdowns.forEach(d => d.classList.add('hidden'));
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
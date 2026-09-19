<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>@yield('title', 'Anggota Umum Dashboard – UNICODE')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                        teal: '#14B8A6',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }

        .sidebar-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-collapsed { width: 72px !important; }
        .sidebar-collapsed .menu-text { display: none !important; }
        .sidebar-collapsed .menu-icon { margin-right: 0 !important; }
        .sidebar-collapsed .menu-item { justify-content: center !important; padding: 0.7rem 0 !important; }
        .sidebar-collapsed .logo-text { display: none !important; }
        .sidebar-collapsed .logo-container { justify-content: center !important; }
        .sidebar-collapsed .sidebar-subtitle { display: none !important; }
        .sidebar-collapsed .menu-label { display: none !important; }

        .sidebar-mobile {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            transform: translateX(-100%);
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-mobile.open { transform: translateX(0); }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active { opacity: 1; pointer-events: auto; }

        .dropdown-enter {
            animation: slideDown 0.2s ease forwards;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .badge-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        @supports (padding: max(0px)) {
            .safe-top { padding-top: max(env(safe-area-inset-top), 0px); }
            .safe-bottom { padding-bottom: max(env(safe-area-inset-bottom), 0px); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased">

    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>

    <div class="flex h-screen overflow-hidden">
        @include('partials.roles.anggota-umum.sidebar')

        <div id="main-content" class="flex-1 flex flex-col h-screen overflow-hidden w-full min-w-0">
            @include('partials.roles.anggota-umum.header')
            <main class="flex-1 overflow-y-auto p-3 md:p-6 scroll-smooth bg-gray-50 dark:bg-gray-900">
                @yield('content')
            </main>
            @include('partials.roles.anggota-umum.footer')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
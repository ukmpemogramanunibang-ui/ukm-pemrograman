<!DOCTYPE html>
<html lang="id" class="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UNICODE – UKM Pemrograman')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Reset & Base */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }
        .dark body { background: #111827; }
        .bg-midnight { background: #0B1120; }
        .bg-deepblue { background: #1E2A4A; }
        .bg-electric { background: #3B82F6; }
        .text-midnight { color: #0B1120; }
        .from-cyan { --tw-gradient-from: #06b6d4; }
        .to-violet { --tw-gradient-to: #8b5cf6; }
        .to-electric { --tw-gradient-to: #3B82F6; }
         .bg-midnight { background: #0B1120; }
    .bg-deepblue { background: #1E2A4A; }
    .from-cyan { --tw-gradient-from: #06b6d4; }
    .to-violet { --tw-gradient-to: #8b5cf6; }
    .to-electric { --tw-gradient-to: #3B82F6; }
    .text-midnight { color: #0B1120; }

    /* Tambahan efek glassmorphism untuk card */
    .glass-card {
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .dark .glass-card {
        background: rgba(0,0,0,0.3);
    }
    </style>
</head>
<body>
    <!-- ===== HEADER PUBLIK ===== -->
    <header class="bg-gradient-to-r from-midnight via-deepblue to-electric shadow-2xl sticky top-0 z-40 border-b border-white/10">
        <div class="container mx-auto flex items-center justify-between p-4">
            <a href="/" class="flex items-center space-x-3 group">
                <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo" class="h-12 w-12 md:h-14 md:w-14 rounded-full border-2 border-white/30 shadow-lg group-hover:scale-110 transition-all duration-500">
                <div class="flex flex-col">
                    <span class="text-xl md:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan via-blue-400 to-violet drop-shadow-lg">UNICODE</span>
                    <span class="text-[9px] md:text-[10px] text-white/60 -mt-1">UKM Pemrograman</span>
                </div>
            </a>

            <nav class="hidden lg:flex space-x-8 text-white font-medium">
                <a href="{{ route('landing') }}" class="hover:text-cyan transition-colors">Beranda</a>
                <a href="{{ route('about') }}" class="hover:text-cyan transition-colors">Tentang</a>
                <a href="{{ route('dokumentasi') }}" class="hover:text-cyan transition-colors">Dokumentasi</a>
                <a href="{{ route('member') }}" class="hover:text-cyan transition-colors">Member</a>
                <a href="{{ route('prestasi') }}" class="hover:text-cyan transition-colors">Prestasi</a>
                <a href="{{ route('explore') }}" class="hover:text-cyan transition-colors font-bold text-cyan-300">Eksplor</a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="/login" class="relative inline-flex items-center px-5 md:px-6 py-2.5 bg-white text-midnight font-bold rounded-full overflow-hidden shadow-[0_0_15px_rgba(0,217,255,0.3)] hover:shadow-[0_0_30px_rgba(0,217,255,0.6)] transition-shadow duration-300 group">
                    <span class="absolute inset-0 bg-gradient-to-r from-cyan to-electric transform -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-in-out"></span>
                    <span class="relative z-10 flex items-center text-midnight group-hover:text-white transition-colors"><i class="fas fa-sign-in-alt mr-2"></i> Login</span>
                </a>
                <button id="mobile-menu-toggle" class="lg:hidden text-white text-2xl focus:outline-none hover:text-cyan transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="lg:hidden hidden bg-gradient-to-b from-midnight to-deepblue px-4 pb-4 text-white shadow-xl">
            <a href="{{ route('landing') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Beranda</a>
            <a href="{{ route('about') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Tentang</a>
            <a href="{{ route('dokumentasi') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Dokumentasi</a>
            <a href="{{ route('member') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Member</a>
            <a href="{{ route('prestasi') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Prestasi</a>
            <a href="{{ route('explore') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5 text-cyan-300 font-bold">Eksplor</a>
            <div class="mt-4 border-t border-white/20 pt-4">
                <a href="/login" class="block py-2 text-center bg-white/10 rounded-lg hover:bg-white/20 transition">Login</a>
            </div>
        </div>
    </header>

    <!-- ===== KONTEN ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== FOOTER SEDERHANA ===== -->
    <footer class="bg-midnight text-white/70 text-center py-6 text-sm border-t border-white/10 mt-8">
        <p>&copy; {{ date('Y') }} UKM Pemrograman UNICODE – Universitas Bhakti Asih</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('mobile-menu-toggle');
            const menu = document.getElementById('mobile-menu');
            if (toggle && menu) {
                toggle.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>
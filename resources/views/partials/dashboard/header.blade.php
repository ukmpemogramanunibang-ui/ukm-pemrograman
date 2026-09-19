{{-- resources/views/partials/header.blade.php --}}
@php
    $isLoginPage = Route::currentRouteName() == 'login';
@endphp

{{-- ============================================================ --}}
{{-- TOP BAR: Sosial Media & Toggle Dark --}}
{{-- ============================================================ --}}
<div class="bg-midnight/90 backdrop-blur-sm border-b border-white/10 py-2 relative z-50">
    <div class="container mx-auto px-4 flex flex-wrap items-center justify-between text-white text-xs md:text-sm">
        <div class="flex items-center space-x-4">
            <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan to-electric">
                <i class="fas fa-graduation-cap mr-1"></i> UKM Pemrograman UNICODE
            </span>
            <span class="hidden md:inline text-gray-400">|</span>
            <span class="hidden md:inline text-gray-300">Universitas Bhakti Asih – Tangerang</span>
        </div>
        <div class="flex items-center space-x-3 md:space-x-4">
            <button id="theme-toggle" class="text-gray-300 hover:text-yellow-300 transition-colors duration-300 text-lg focus:outline-none" title="Toggle Dark/Light Mode">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="https://instagram.com/ukm_pemrograman" target="_blank" class="text-gray-300 hover:text-pink-400 transition-colors text-base"><i class="fab fa-instagram"></i></a>
            <a href="https://github.com/ukm-pemrograman" target="_blank" class="text-gray-300 hover:text-white transition-colors text-base"><i class="fab fa-github"></i></a>
            <span class="text-gray-400 text-[10px] hidden lg:inline"> WEBSITE RESMI UKM PROGRAMMING UNICODE - UNIVERSITAS BHKATI ASIH TANGERANG</span>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MAIN HEADER --}}
{{-- ============================================================ --}}
<header class="relative bg-gradient-to-r from-midnight via-deepblue to-electric shadow-2xl sticky top-0 z-40 border-b border-white/10">
    <div class="container mx-auto flex items-center justify-between p-4">
        {{-- Logo --}}
        <a href="/" class="flex items-center space-x-3 group">
            <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo UKM" 
                 class="h-12 w-12 md:h-14 md:w-14 rounded-full border-2 border-white/30 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
            <div class="flex flex-col">
                <span class="text-xl md:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan via-electric to-violet drop-shadow-lg">
                    UNICODE
                </span>
                <span class="text-[9px] md:text-[10px] text-white/60 -mt-1">UKM Pemrograman</span>
            </div>
        </a>

        {{-- Navigasi Desktop --}}
        @if(!$isLoginPage)
        <nav class="hidden lg:flex space-x-8 text-white font-medium">
            <a href="{{ route('landing') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Beranda</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('about') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Tentang</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('dokumentasi') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Dokumentasi</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('member') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Member</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('prestasi') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Prestasi</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('portfolio') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Portofolio</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('kontak') }}" class="relative group">
                <span class="hover:text-cyan transition-colors">Kontak</span>
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
            </a>
            <a href="{{ route('account.check.index') }}" class="relative group">
    <span class="hover:text-cyan transition-colors font-bold text-cyan-300">
        <i class="fas fa-user-check mr-1"></i> Cek Akun
    </span>
    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
</a>
            <a href="{{ route('interaksi.ppt') }}" class="relative group">
    <span class="hover:text-cyan transition-colors font-bold text-cyan-300">PPT Interaktif</span>
    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
</a>
            <a href="{{ route('explore') }}" class="relative group">
    <span class="hover:text-cyan transition-colors font-bold text-cyan-300">Eksplor</span>
    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-cyan transition-all group-hover:w-full"></span>
</a>
        </nav>
        @else
        {{-- Di halaman login, tampilkan teks atau link kembali --}}
        <div class="hidden lg:block text-white/80 text-sm font-medium">
            <a href="{{ url()->previous() ?? route('landing') }}" class="hover:text-white transition flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        @endif

        {{-- Tombol Login / Profil Pengguna --}}
        <div class="flex items-center space-x-4">
            @auth
                {{-- Jika sudah login --}}
                <div class="relative hidden md:block" id="profile-wrapper">
                    <div class="flex items-center gap-1">
                        {{-- Avatar sebagai link ke dashboard --}}
                        <a href="{{ Auth::user()->getDashboardRoute() }}" 
                           class="flex items-center gap-2 focus:outline-none py-1 px-3 rounded-full bg-white/10 hover:bg-white/20 transition ring-2 ring-transparent hover:ring-cyan-400/50 focus:ring-cyan-400/50">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=32" 
                                 alt="Avatar" class="h-8 w-8 rounded-full border border-white/50 shrink-0">
                            <span class="text-white text-sm font-medium hidden lg:block max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                        </a>
                        {{-- Tombol panah untuk dropdown --}}
                        <button id="profile-dropdown-btn" class="text-white/60 hover:text-white transition p-1 focus:outline-none">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="profile-dropdown-icon"></i>
                        </button>
                    </div>

                    {{-- Dropdown Menu --}}
                    <div id="profile-dropdown-menu" class="hidden absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden transform origin-top-right transition-all duration-200 scale-95">
                        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/50">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-2 text-[10px] font-bold px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">
                                {{ Auth::user()->roles->pluck('name')->implode(', ') }}
                            </span>
                        </div>
                        <div class="py-1">
                            {{-- Link Dashboard (redundant, tetapi tetap ada) --}}
                            <a href="{{ Auth::user()->getDashboardRoute() }}" class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-tachometer-alt w-4 text-gray-400"></i> Dashboard
                            </a>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 py-1">
                            <button onclick="showLogoutModal(event)" class="flex items-center gap-3 w-full text-left px-5 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <i class="fas fa-sign-out-alt w-4"></i> Keluar Akun
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- Jika belum login --}}
                @if(!$isLoginPage)
                    {{-- Di halaman selain login, tampilkan tombol login --}}
                    <a href="/login" 
                       class="relative inline-flex items-center px-5 md:px-6 py-2.5 bg-white text-midnight font-bold rounded-full overflow-hidden shadow-[0_0_15px_rgba(0,217,255,0.3)] hover:shadow-[0_0_30px_rgba(0,217,255,0.6)] transition-shadow duration-300 group">
                        <span class="absolute inset-0 bg-gradient-to-r from-cyan to-electric transform -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-in-out"></span>
                        <span class="relative z-10 flex items-center text-midnight group-hover:text-white transition-colors"><i class="fas fa-sign-in-alt mr-2"></i> Login</span>
                    </a>
                @else
                    {{-- Di halaman login, tampilkan teks "Halaman Login" --}}
                    <span class="text-white/70 text-sm font-medium hidden md:inline">
                        <i class="fas fa-lock mr-1"></i> Halaman Login
                    </span>
                @endif
            @endauth

            {{-- Tombol hamburger untuk mobile (selalu tampil) --}}
            <button id="mobile-menu-toggle" class="lg:hidden text-white text-2xl focus:outline-none hover:text-cyan transition-colors">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="lg:hidden hidden bg-gradient-to-b from-midnight to-deepblue px-4 pb-4 text-white shadow-xl">
        @if(!$isLoginPage)
            <a href="{{ route('landing') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Beranda</a>
            <a href="{{ route('about') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Tentang</a>
            <a href="{{ route('dokumentasi') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Dokumentasi</a>
            <a href="{{ route('member') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Member</a>
            <a href="{{ route('prestasi') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Prestasi</a>
            <a href="{{ route('portfolio') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Portofolio</a>
            <a href="{{ route('kontak') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">Kontak</a>
            <a href="{{ route('account.check.index') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5 text-cyan-300 font-bold">
    <i class="fas fa-user-check mr-2"></i> Cek Akun
</a>
            <a href="{{ route('landing') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5">PPT Interaktif</a>
            <a href="{{ route('interaksi.ppt') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5 text-cyan-300 font-bold">
    PPT Interaktif
</a>
           <a href="{{ route('explore') }}" class="block py-3 hover:bg-white/10 rounded px-3 transition border-b border-white/5 text-cyan-300 font-bold">Eksplor</a>
        @else
            <div class="py-3 text-white/70 text-sm border-b border-white/5">
                <i class="fas fa-arrow-left mr-1"></i> <a href="{{ url()->previous() ?? route('landing') }}" class="hover:text-white transition">Kembali</a>
            </div>
        @endif
        
        @auth
            <div class="mt-4 border-t border-white/20 pt-4">
                <a href="{{ Auth::user()->getDashboardRoute() }}" class="block py-2 text-center bg-white/10 rounded-lg hover:bg-white/20 transition">Dashboard Saya</a>
                <button onclick="showLogoutModal(event)" class="block w-full mt-2 py-2 text-center bg-red-500/20 rounded-lg hover:bg-red-500/30 text-red-200 transition">Keluar</button>
            </div>
        @else
            @if(!$isLoginPage)
                <div class="mt-4 border-t border-white/20 pt-4">
                    <a href="/login" class="block py-2 text-center bg-white/10 rounded-lg hover:bg-white/20 transition">Login</a>
                </div>
            @endif
        @endauth
    </div>

    {{-- Marquee (hanya tampil di halaman selain login) --}}
    @if(!$isLoginPage)
    <div class="bg-black/40 backdrop-blur-sm py-2 overflow-hidden border-t border-white/10">
        <div class="animate-marquee whitespace-nowrap text-white text-xs md:text-sm font-semibold tracking-wider">
            🚀 Selamat datang di UKM Pemrograman UNICODE – Tempat berkembangnya inovator digital masa depan! 
            &nbsp;|&nbsp; Workshop Laravel • Hackathon • Bootcamp • Seminar AI • Kolaborasi Proyek 
            &nbsp;|&nbsp; Pendaftaran anggota baru dibuka hingga 30 April 2026!
        </div>
    </div>
    @endif
</header>

<style>
    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee {
        animation: marquee 30s linear infinite;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu = document.getElementById('mobile-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });
        }

        // Dark mode toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        themeToggle.addEventListener('click', function() {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        });

        // Profile Dropdown toggle (hanya untuk tombol panah)
        const dropdownBtn = document.getElementById('profile-dropdown-btn');
        const dropdownMenu = document.getElementById('profile-dropdown-menu');
        const dropdownIcon = document.getElementById('profile-dropdown-icon');

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = dropdownMenu.classList.contains('hidden');
                if (isHidden) {
                    dropdownMenu.classList.remove('hidden');
                    dropdownMenu.classList.add('scale-100');
                    dropdownMenu.classList.remove('scale-95');
                    dropdownIcon.style.transform = 'rotate(180deg)';
                } else {
                    dropdownMenu.classList.add('hidden');
                    dropdownMenu.classList.remove('scale-100');
                    dropdownMenu.classList.add('scale-95');
                    dropdownIcon.style.transform = 'rotate(0deg)';
                }
            });

            // Tutup dropdown saat klik di luar
            document.addEventListener('click', function(e) {
                const wrapper = document.getElementById('profile-wrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    dropdownMenu.classList.add('hidden');
                    dropdownMenu.classList.remove('scale-100');
                    dropdownMenu.classList.add('scale-95');
                    dropdownIcon.style.transform = 'rotate(0deg)';
                }
            });
        }
    });
</script>
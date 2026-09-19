{{-- Sidebar Superadmin --}}
@php
    $isCollapsed = session('sidebar_collapsed', false);
    $currentPath = request()->path();
@endphp

<aside id="sidebar"
       class="sidebar-transition
              bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700
              h-screen overflow-y-auto flex-shrink-0
              sidebar-mobile
              lg:relative lg:translate-x-0 lg:block
              {{ $isCollapsed ? 'sidebar-collapsed' : 'w-64' }}"
       style="z-index: 1000;">

    <div class="logo-container flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center space-x-2 logo-text">
            <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo" class="h-8 w-8 rounded-full">
            <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan to-blue-600">UNICODE</span>
        </a>
        <span class="menu-label text-[10px] font-semibold text-gray-400 dark:text-gray-500">Superadmin</span>
        <button id="sidebar-close-mobile" class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <nav class="p-3 space-y-1" id="sidebar-menu">
        <a href="{{ route('superadmin.dashboard') }}"
           class="menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400' : 'text-gray-700 dark:text-gray-300' }}">
            <i class="fas fa-home w-5 menu-icon text-center"></i>
            <span class="menu-text text-sm font-medium">Dashboard</span>
        </a>

        {{-- Pengumuman --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Pengumuman</p>
            <a href="{{ route('superadmin.announcements.all') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-bullhorn w-5 menu-icon text-center"></i><span class="menu-text text-sm">Semua</span>
            </a>
            <a href="{{ route('superadmin.announcements.officials') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-user-tie w-5 menu-icon text-center"></i><span class="menu-text text-sm">Pengurus</span>
            </a>
            <a href="{{ route('superadmin.announcements.members') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-users w-5 menu-icon text-center"></i><span class="menu-text text-sm">Anggota</span>
            </a>
        </div>

        {{-- Manajemen --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Manajemen</p>
            <a href="{{ route('superadmin.management.members') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-user-plus w-5 menu-icon text-center"></i><span class="menu-text text-sm">Anggota</span>
            </a>
            <a href="{{ route('superadmin.management.officials') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-user-cog w-5 menu-icon text-center"></i><span class="menu-text text-sm">Pengurus</span>
            </a>
        </div>

        {{-- Laporan --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Laporan</p>
            <a href="{{ route('superadmin.reports.analysis') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-chart-pie w-5 menu-icon text-center"></i><span class="menu-text text-sm">Analisis Laporan</span>
            </a>
        </div>

        {{-- Kontak --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Kontak</p>
            <a href="{{ route('superadmin.contacts.all') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-address-book w-5 menu-icon text-center"></i><span class="menu-text text-sm">Semua</span>
            </a>
            <a href="{{ route('superadmin.contacts.officials') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-user-tie w-5 menu-icon text-center"></i><span class="menu-text text-sm">Pengurus</span>
            </a>
            <a href="{{ route('superadmin.contacts.members') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-users w-5 menu-icon text-center"></i><span class="menu-text text-sm">Anggota</span>
            </a>
        </div>

        {{-- Data Anda --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Data Anda</p>
            <a href="{{ route('superadmin.data.profile') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-id-card w-5 menu-icon text-center"></i><span class="menu-text text-sm">Data Pribadi</span>
            </a>
            <a href="{{ route('superadmin.data.qr') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-qrcode w-5 menu-icon text-center"></i><span class="menu-text text-sm">QR Code</span>
            </a>
            <a href="{{ route('superadmin.data.testing') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-flask w-5 menu-icon text-center"></i><span class="menu-text text-sm">Testing</span>
            </a>
        </div>

        {{-- Pelanggaran --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Pelanggaran</p>
            <a href="{{ route('superadmin.violations.light') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-exclamation-triangle text-yellow-500 w-5 menu-icon text-center"></i><span class="menu-text text-sm">Ringan</span>
            </a>
            <a href="{{ route('superadmin.violations.medium') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-exclamation-circle text-orange-500 w-5 menu-icon text-center"></i><span class="menu-text text-sm">Sedang</span>
            </a>
            <a href="{{ route('superadmin.violations.heavy') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-times-circle text-red-500 w-5 menu-icon text-center"></i><span class="menu-text text-sm">Berat</span>
            </a>
            <a href="{{ route('superadmin.violations.block') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-ban text-gray-500 w-5 menu-icon text-center"></i><span class="menu-text text-sm">Blokir</span>
            </a>
        </div>

        {{-- Pengaturan --}}
        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Pengaturan</p>
            <a href="{{ route('superadmin.settings.profile') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-user-edit w-5 menu-icon text-center"></i><span class="menu-text text-sm">Data Diri</span>
            </a>
            <a href="{{ route('superadmin.settings.general') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-cog w-5 menu-icon text-center"></i><span class="menu-text text-sm">Pengaturan</span>
            </a>
            <a href="{{ route('superadmin.settings.explore') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-compass w-5 menu-icon text-center"></i><span class="menu-text text-sm">Jelajah</span>
            </a>
            <a href="{{ route('superadmin.settings.audit') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-history w-5 menu-icon text-center"></i><span class="menu-text text-sm">Audit History</span>
            </a>
        </div>

        {{-- Keluar --}}
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
            <button onclick="showLogoutModal(event)" class="menu-item flex items-center gap-3 w-full text-left px-4 py-2.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition">
                <i class="fas fa-sign-out-alt w-5 menu-icon text-center"></i>
                <span class="menu-text text-sm font-medium">Keluar Akun</span>
            </button>
        </div>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const closeMobileBtn = document.getElementById('sidebar-close-mobile');
        if (closeMobileBtn) {
            closeMobileBtn.addEventListener('click', window.closeMobileSidebar);
        }
        // Highlight menu
        const currentPath = window.location.pathname;
        document.querySelectorAll('#sidebar-menu a').forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || (href !== '#' && currentPath.startsWith(href))) {
                link.classList.add('bg-cyan-50', 'dark:bg-cyan-900/20', 'text-cyan-600', 'dark:text-cyan-400');
            }
        });
    });
</script>

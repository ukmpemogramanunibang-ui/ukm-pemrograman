@php
    $isCollapsed = session('sidebar_collapsed', false);
    $currentPath = request()->path();
@endphp

<aside id="sidebar"
       class="sidebar-transition bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 h-screen overflow-y-auto flex-shrink-0 sidebar-mobile lg:relative lg:translate-x-0 lg:block {{ $isCollapsed ? 'sidebar-collapsed' : 'w-64' }}"
       style="z-index:1000;">

    <div class="logo-container flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('ketua.dashboard') }}" class="flex items-center space-x-2 logo-text">
            <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo" class="h-8 w-8 rounded-full">
            <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">UNICODE</span>
        </a>
        <span class="sidebar-subtitle text-[10px] font-semibold text-gray-400 dark:text-gray-500">Ketua</span>
        <button id="sidebar-collapse-btn" class="hidden lg:block text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
            <i id="collapse-icon" class="fas fa-chevron-left text-sm"></i>
        </button>
        <button id="sidebar-close-mobile" class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <nav class="p-3 space-y-1" id="sidebar-menu">
        <a href="{{ route('ketua.dashboard') }}" class="menu-item flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('ketua.dashboard') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : 'text-gray-700 dark:text-gray-300' }}">
            <i class="fas fa-home w-5 menu-icon text-center"></i>
            <span class="menu-text text-sm font-medium">Dashboard</span>
        </a>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Manajemen</p>
            <a href="{{ route('ketua.manajemen-anggota.index') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-users w-5 menu-icon text-center"></i><span class="menu-text text-sm">Anggota</span>
            </a>
            <a href="{{ route('ketua.manajemen-pengurus') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-user-tie w-5 menu-icon text-center"></i><span class="menu-text text-sm">Pengurus</span>
            </a>
            <a href="{{ route('ketua.divisi-tim') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-users-cog w-5 menu-icon text-center"></i><span class="menu-text text-sm">Divisi & Tim</span>
            </a>
        </div>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Kegiatan</p>
            <a href="{{ route('ketua.event-kegiatan') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-calendar-check w-5 menu-icon text-center"></i><span class="menu-text text-sm">Event & Kegiatan</span>
            </a>
            <a href="{{ route('ketua.presensi-qr') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-qrcode w-5 menu-icon text-center"></i><span class="menu-text text-sm">Presensi QR</span>
            </a>
        </div>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Keuangan & Dokumen</p>
            <a href="{{ route('ketua.keuangan-kas') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-money-bill-wave w-5 menu-icon text-center"></i><span class="menu-text text-sm">Keuangan & Kas</span>
            </a>
            <a href="{{ route('ketua.dokumen-arsip') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-folder-open w-5 menu-icon text-center"></i><span class="menu-text text-sm">Dokumen & Arsip</span>
            </a>
        </div>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Poin & Laporan</p>
            <a href="{{ route('ketua.poin-prestasi') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-trophy w-5 menu-icon text-center"></i><span class="menu-text text-sm">Poin & Prestasi</span>
            </a>
            <a href="{{ route('ketua.laporan-analitik') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-chart-line w-5 menu-icon text-center"></i><span class="menu-text text-sm">Laporan & Analitik</span>
            </a>
        </div>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Komunikasi</p>
            <a href="{{ route('ketua.broadcast-pengumuman') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-bullhorn w-5 menu-icon text-center"></i><span class="menu-text text-sm">Broadcast</span>
            </a>
        </div>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Lainnya</p>
            <a href="{{ route('ketua.modul-belajar') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-book w-5 menu-icon text-center"></i><span class="menu-text text-sm">Modul Belajar</span>
            </a>
            <a href="{{ route('ketua.sop') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-clipboard-list w-5 menu-icon text-center"></i><span class="menu-text text-sm">SOP</span>
            </a>
            <a href="{{ route('ketua.jadwal') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-calendar-alt w-5 menu-icon text-center"></i><span class="menu-text text-sm">Jadwal</span>
            </a>
            <a href="{{ route('ketua.perizinan') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-file-signature w-5 menu-icon text-center"></i><span class="menu-text text-sm">Perizinan</span>
            </a>
        </div>

        <div class="pt-2">
            <p class="menu-label text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mb-1">Pengaturan</p>
           <a href="{{ route('ketua.pengaturan') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-cog w-5 menu-icon text-center"></i><span class="menu-text text-sm">Pengaturan</span>
            </a>
            <a href="{{ route('ketua.notifications') }}" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-700 dark:text-gray-300">
                <i class="fas fa-bell w-5 menu-icon text-center"></i><span class="menu-text text-sm">Notifikasi</span>
            </a>
        </div>

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
    const overlay = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeMobileBtn = document.getElementById('sidebar-close-mobile');
    const collapseBtn = document.getElementById('sidebar-collapse-btn');
    const collapseIcon = document.getElementById('collapse-icon');
    const isMobile = () => window.innerWidth < 1024;

    window.closeMobileSidebar = function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };
    function openMobileSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isMobile()) {
                if (sidebar.classList.contains('open')) closeMobileSidebar();
                else openMobileSidebar();
            }
        });
    }
    if (overlay) overlay.addEventListener('click', closeMobileSidebar);
    if (closeMobileBtn) closeMobileBtn.addEventListener('click', closeMobileSidebar);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) closeMobileSidebar();
    });

    if (collapseBtn) {
        collapseBtn.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-collapsed');
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
            if (collapseIcon) {
                collapseIcon.classList.toggle('fa-chevron-left', !isCollapsed);
                collapseIcon.classList.toggle('fa-chevron-right', isCollapsed);
            }
        });
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            sidebar.classList.add('sidebar-collapsed');
            if (collapseIcon) {
                collapseIcon.classList.remove('fa-chevron-left');
                collapseIcon.classList.add('fa-chevron-right');
            }
        }
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024 && sidebar.classList.contains('open')) closeMobileSidebar();
    });

    const currentPath = window.location.pathname;
    document.querySelectorAll('#sidebar-menu a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (href !== '#' && currentPath.startsWith(href))) {
            link.classList.add('bg-purple-50', 'dark:bg-purple-900/20', 'text-purple-600', 'dark:text-purple-400');
        }
    });
});
</script>
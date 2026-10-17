    {{-- Sidebar Owner - Luxury & Professional dengan Dropdown Lengkap --}}
    @php
        /* ============================================================
        | INISIALISASI AMAN
        * ============================================================ */
        $isCollapsed = session('sidebar_collapsed', false);
        $currentPath = request()->path();

        // Pastikan $menuCounts selalu array
        $menuCounts = is_array($menuCounts ?? null) ? $menuCounts : [];

        // Helper: ambil count dari menuCounts dengan fallback query yang aman
        // ✅ FIX: capture $menuCounts via `use (...)` — JANGAN pakai `global`
        $getCount = function (string $key, ?callable $fallback = null) use ($menuCounts): int {
            if (array_key_exists($key, $menuCounts)) {
                return (int) $menuCounts[$key];
            }
            if ($fallback) {
                try {
                    return (int) $fallback();
                } catch (\Throwable $e) {
                    return 0;
                }
            }
            return 0;
        };

        // Nilai badge
        $pendingCount       = $getCount('pending_bookings', fn () => \App\Models\Booking::where('status', 'Menunggu Persetujuan')->count());
        $pendingPermissions = $getCount('pending_permissions', fn () => \App\Models\AttendancePermission::where('status', 'pending')->count());
        $criticalCount      = $getCount('critical_attendance_count');
        $lmsPendingCount    = $getCount('lms_pending_submissions');
    @endphp
    @php
        /* Helper failsafe untuk semua route sidebar */
        if (! function_exists('safe_route')) {
            function safe_route(string $name, array $params = [], string $fallback = '#'): string {
                try {
                    return \Illuminate\Support\Facades\Route::has($name)
                        ? route($name, $params)
                        : $fallback;
                } catch (\Throwable $e) {
                    return $fallback;
                }
            }
        }
    @endphp
    <aside id="sidebar"
        class="sidebar-transition
                bg-gradient-to-b from-white via-gray-50/80 to-white
                dark:from-gray-900 dark:via-gray-800 dark:to-gray-900
                border-r border-gray-200/60 dark:border-gray-700/60
                h-screen overflow-y-auto flex-shrink-0
                sidebar-mobile
                lg:relative lg:translate-x-0 lg:block
                {{ $isCollapsed ? 'sidebar-collapsed' : 'w-72' }}
                shadow-2xl shadow-gray-200/50 dark:shadow-gray-900/50"
        style="z-index: 1000;">

        {{-- ============================================ --}}
        {{-- HEADER SIDEBAR - Logo & Brand --}}
        {{-- ============================================ --}}
        <div class="logo-container flex items-center justify-between p-5 border-b border-gray-200/60 dark:border-gray-700/60 bg-gradient-to-r from-white/80 via-gray-50/80 to-white/80 dark:from-gray-800/80 dark:via-gray-700/80 dark:to-gray-800/80">
            <a href="{{ route('owner.dashboard') }}" class="flex items-center space-x-3 logo-text group">
                <div class="relative">
                    <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo"
                        class="h-10 w-10 rounded-full border-2 border-amber-400/70 shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform duration-500 group-hover:rotate-6">
                    <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white dark:border-gray-800 animate-pulse"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-amber-700 dark:from-amber-400 dark:to-amber-500 tracking-tight logo-text">UNICODE</span>
                    <span class="text-[9px] text-gray-400 dark:text-gray-500 font-light tracking-widest sidebar-subtitle">OWNER PANEL</span>
                </div>
            </a>
            <div class="flex items-center gap-1">
                <span class="hidden lg:flex items-center gap-1.5 text-[10px] text-emerald-500/80 dark:text-emerald-400/80 sidebar-subtitle">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Online
                </span>
                <button id="sidebar-collapse-btn"
                        class="hidden lg:flex items-center justify-center
                            text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white
                            hover:scale-110 active:scale-95
                            transition-all duration-300 p-2 rounded-lg
                            hover:bg-gray-100 dark:hover:bg-gray-700
                            focus:outline-none focus:ring-2 focus:ring-cyan-500/40"
                        aria-label="Toggle sidebar"
                        title="Toggle sidebar">
                    <i class="fas fa-bars icon-hamburger"></i>
                </button>
                <button id="sidebar-close-mobile" class="lg:hidden text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:scale-110 transition-all duration-300 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- MENU NAVIGASI --}}
        {{-- ============================================ --}}
        <nav class="p-3 space-y-0.5" id="sidebar-menu">

            {{-- ============ DASHBOARD ============ --}}
            <a href="{{ route('owner.dashboard') }}"
            class="menu-item flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300
                    {{ request()->routeIs('owner.dashboard')
                        ? 'bg-gradient-to-r from-amber-100/80 to-amber-50/80 dark:from-amber-900/40 dark:to-amber-800/20 text-amber-700 dark:text-amber-300 shadow-sm shadow-amber-500/10 border border-amber-200/50 dark:border-amber-700/30'
                        : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                <div class="menu-icon-wrapper relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                            {{ request()->routeIs('owner.dashboard') ? 'bg-gradient-to-br from-amber-400 to-amber-600 shadow-md shadow-amber-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                    <i class="fas fa-home w-5 menu-icon text-center text-lg transition-all duration-300
                            {{ request()->routeIs('owner.dashboard') ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    @if(request()->routeIs('owner.dashboard'))
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
                    @endif
                </div>
                <span class="menu-text text-sm font-medium tracking-wide">Dashboard</span>
                <span class="ml-auto menu-text text-[10px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-600">Home</span>
            </a>

            {{-- ============ DROPDOWN: MANAJEMEN ============ --}}
            @php
                $isManagementActive = request()->routeIs('owner.management.*') ||
                                    request()->routeIs('owner.leaderboard') ||
                                    request()->routeIs('owner.assign-tasks') ||
                                    request()->routeIs('owner.promote-demote.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isManagementActive ? 'bg-amber-50/70 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isManagementActive ? 'bg-gradient-to-br from-amber-400 to-amber-600 shadow-md shadow-amber-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-crown w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isManagementActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Manajemen</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isManagementActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isManagementActive ? '' : 'hidden' }}">

                    <a href="{{ route('owner.leaderboard') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-trophy w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Leaderboard</span>
                    </a>
                    <a href="{{ route('owner.promote-demote.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-arrows-alt-v w-5 text-amber-500 dark:text-amber-400 text-center"></i>
                        <span class="menu-text">Naik / Turun Pangkat</span>
                    </a>

                    <div class="dropdown-sub-group mt-1">
                        <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1">Organisasi</div>

                        <a href="{{ route('owner.management.dashboard') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-sitemap w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                            <span class="menu-text">Dashboard Manajemen</span>
                        </a>

                        {{-- Sub: Testing --}}
                        <div class="dropdown-sub-group">
                            <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                                <i class="fas fa-flask w-5 text-orange-500 dark:text-orange-400 text-center"></i>
                                <span class="menu-text flex-1">Testing</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                            </button>
                            <div class="sub-content hidden space-y-0.5 pl-6">
                                <a href="{{ route('owner.management.testing.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                                <a href="{{ route('owner.management.testing.qr') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">QR</a>
                                <a href="{{ route('owner.management.testing.nim') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">NIM</a>
                                <a href="{{ route('owner.management.testing.identity') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Identity</a>
                                <a href="{{ route('owner.management.testing.validate') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Validate</a>
                            </div>
                        </div>

                        {{-- Sub: NFC --}}
                        <div class="dropdown-sub-group">
                            <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                                <i class="fas fa-wifi w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                                <span class="menu-text flex-1">NFC</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                            </button>
                            <div class="sub-content hidden space-y-0.5 pl-6">
                                <a href="{{ route('owner.management.nfc.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                                <a href="{{ route('owner.management.nfc.generate') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Generate</a>
                                <a href="{{ route('owner.management.nfc.history') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">History</a>
                                <a href="{{ route('owner.management.nfc.testing') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Testing</a>
                            </div>
                        </div>

                        {{-- Sub: QR Code --}}
                        <div class="dropdown-sub-group">
                            <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                                <i class="fas fa-qrcode w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                                <span class="menu-text flex-1">QR Code</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                            </button>
                            <div class="sub-content hidden space-y-0.5 pl-6">
                                <a href="{{ route('owner.management.qr.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                                <a href="{{ route('owner.management.qr.generate') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Generate</a>
                                <a href="{{ route('owner.management.qr.massal') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Massal</a>
                                <a href="{{ route('owner.management.qr.konversi') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Konversi</a>
                                <a href="{{ route('owner.management.qr.test') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Test</a>
                                <a href="{{ route('owner.management.qr.print') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Print</a>
                                <a href="{{ route('owner.management.qr.download') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Download</a>
                                <a href="{{ route('owner.management.qr.deactivate') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Deactivate</a>
                            </div>
                        </div>

                        {{-- Sub: Pengangkatan --}}
                        <div class="dropdown-sub-group">
                            <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                                <i class="fas fa-user-check w-5 text-green-500 dark:text-green-400 text-center"></i>
                                <span class="menu-text flex-1">Pengangkatan</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                            </button>
                            <div class="sub-content hidden space-y-0.5 pl-6">
                                <a href="{{ route('owner.management.appointments.approval') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Approval</a>
                                <a href="{{ route('owner.management.appointments.history') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">History</a>
                            </div>
                        </div>

                        {{-- Sub: Pelanggaran --}}
                        <div class="dropdown-sub-group">
                            <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                                <i class="fas fa-gavel w-5 text-red-500 dark:text-red-400 text-center"></i>
                                <span class="menu-text flex-1">Pelanggaran</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                            </button>
                            <div class="sub-content hidden space-y-0.5 pl-6">
                                <a href="{{ route('owner.management.violations.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                                <a href="{{ route('owner.management.violations.warning') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Warning</a>
                                <a href="{{ route('owner.management.violations.light') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Light</a>
                                <a href="{{ route('owner.management.violations.medium') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Medium</a>
                                <a href="{{ route('owner.management.violations.heavy') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Heavy</a>
                                <a href="{{ route('owner.management.violations.suspend') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Suspend</a>
                                <a href="{{ route('owner.management.violations.block') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Block</a>
                            </div>
                        </div>

                        {{-- Sub: Arsip --}}
                        <div class="dropdown-sub-group">
                            <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                                <i class="fas fa-archive w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                                <span class="menu-text flex-1">Arsip</span>
                                <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                            </button>
                            <div class="sub-content hidden space-y-0.5 pl-6">
                                <a href="{{ route('owner.management.archive.graduated') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Graduated</a>
                                <a href="{{ route('owner.management.archive.left') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Left</a>
                                <a href="{{ route('owner.management.archive.history') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ DROPDOWN: TUGAS & MATERI (LMS) ============ --}}
            @php
                $isLmsActive = request()->routeIs('owner.lms.*');
            @endphp

            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isLmsActive
                                    ? 'bg-violet-50/70 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'
                                    : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">

                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isLmsActive
                                    ? 'bg-gradient-to-br from-violet-500 to-fuchsia-500 shadow-md shadow-violet-500/20'
                                    : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-graduation-cap w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isLmsActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>

                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Tugas &amp; Materi</span>

                    @if($lmsPendingCount > 0)
                        <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $lmsPendingCount }}</span>
                    @endif

                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isLmsActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div class="dropdown-content space-y-0.5 pl-2 {{ $isLmsActive ? '' : 'hidden' }}">

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1">Utama</div>

                    <a href="{{ route('owner.lms.dashboard') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-tachometer-alt w-5 text-violet-500 dark:text-violet-400 text-center"></i>
                        <span class="menu-text">Dashboard LMS</span>
                    </a>
                    <a href="{{ route('owner.lms.all') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list-ul w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Semua Item</span>
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Konten</div>

                    <a href="{{ route('owner.lms.tasks') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-tasks w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Tugas</span>
                    </a>
                    <a href="{{ route('owner.lms.materials') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-book-open w-5 text-emerald-500 dark:text-emerald-400 text-center"></i>
                        <span class="menu-text">Materi</span>
                    </a>
                    <a href="{{ route('owner.lms.groups.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-folder-tree w-5 text-amber-500 dark:text-amber-400 text-center"></i>
                        <span class="menu-text">Pengelompokan</span>
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Pengumpulan</div>

                    <a href="{{ route('owner.lms.submissions.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-inbox w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text flex-1">Semua Pengumpulan</span>
                        @if($lmsPendingCount > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $lmsPendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('owner.lms.submissions.pending') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-clock w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Menunggu Review</span>
                    </a>
                    <a href="{{ route('owner.lms.submissions.late') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-exclamation-triangle w-5 text-orange-500 dark:text-orange-400 text-center"></i>
                        <span class="menu-text">Terlambat</span>
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Milik Saya</div>

                    <a href="{{ route('owner.lms.my-created') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-edit w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">Dibuat Oleh Saya</span>
                    </a>
                    <a href="{{ route('owner.lms.my-submissions') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-paper-plane w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                        <span class="menu-text">Pengumpulan Saya</span>
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Monitoring &amp; Audit</div>

                    <a href="{{ route('owner.lms.monitoring') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-chart-line w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Monitoring</span>
                    </a>
                    <a href="{{ route('owner.lms.activities') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-history w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Riwayat Aktivitas</span>
                    </a>
                    <a href="{{ route('owner.lms.audit') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-shield-halved w-5 text-rose-500 dark:text-rose-400 text-center"></i>
                        <span class="menu-text">Audit Log LMS</span>
                    </a>
                    <a href="{{ route('owner.lms.archived') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-box-archive w-5 text-slate-500 dark:text-slate-400 text-center"></i>
                        <span class="menu-text">Arsip &amp; Terhapus</span>
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Pengaturan</div>

                    <a href="{{ route('owner.lms.notifications') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-bell w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Notifikasi LMS</span>
                    </a>
                    <a href="{{ route('owner.lms.settings') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-sliders-h w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Pengaturan LMS</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: KONTAK ============ --}}
            @php
                $isContactsActive = request()->routeIs('owner.contacts.*') ||
                                    request()->routeIs('owner.chats.*') ||
                                    request()->routeIs('owner.groups.*') ||
                                    request()->routeIs('owner.messages.*') ||
                                    request()->routeIs('owner.security.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isContactsActive ? 'bg-blue-50/70 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isContactsActive ? 'bg-gradient-to-br from-blue-400 to-indigo-500 shadow-md shadow-blue-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-address-book w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isContactsActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Kontak</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isContactsActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isContactsActive ? '' : 'hidden' }}">

                    <a href="{{ route('owner.contacts.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-users w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Semua Akun</span>
                    </a>
                    <a href="{{ route('owner.contacts.owner') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-crown w-5 text-amber-500 dark:text-amber-400 text-center"></i>
                        <span class="menu-text">Owner</span>
                    </a>
                    <a href="{{ route('owner.contacts.superadmin') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-shield-alt w-5 text-red-500 dark:text-red-400 text-center"></i>
                        <span class="menu-text">Superadmin</span>
                    </a>
                    <a href="{{ route('owner.contacts.ketua') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-users-cog w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Ketua</span>
                    </a>
                    <a href="{{ route('owner.contacts.pengurus') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-tie w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">Pengurus</span>
                    </a>
                    <a href="{{ route('owner.contacts.anggota') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-graduate w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Anggota</span>
                    </a>
                    <a href="{{ route('owner.contacts.anggota-umum') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                        <span class="menu-text">Anggota Umum</span>
                    </a>
                    <a href="{{ route('owner.contacts.pengunjung') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-friends w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Pengunjung</span>
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                    <a href="{{ route('owner.contacts.active') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-circle text-green-500 w-5 text-center"></i>
                        <span class="menu-text">Akun Aktif</span>
                    </a>
                    <a href="{{ route('owner.contacts.offline') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-circle text-gray-400 w-5 text-center"></i>
                        <span class="menu-text">Akun Offline</span>
                    </a>
                    <a href="{{ route('owner.contacts.frozen') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-snowflake w-5 text-blue-300 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Akun Dibekukan</span>
                    </a>
                    <a href="{{ route('owner.contacts.blocked') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-ban w-5 text-red-500 dark:text-red-400 text-center"></i>
                        <span class="menu-text">Akun Diblokir</span>
                    </a>
                    <a href="{{ route('owner.contacts.deactivated') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-slash w-5 text-gray-600 dark:text-gray-500 text-center"></i>
                        <span class="menu-text">Akun Dinonaktifkan</span>
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                    {{-- Sub: Chat & Grup --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-comments w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                            <span class="menu-text flex-1">Chat &amp; Grup</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.chats.private') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-user-circle w-4 text-center"></i> Chat Privat
                            </a>
                            <a href="{{ route('owner.groups.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-layer-group w-4 text-center"></i> Grup Chat
                            </a>
                            <a href="{{ route('owner.groups.create') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-plus-circle w-4 text-center text-green-500"></i> Buat Grup
                            </a>
                        </div>
                    </div>

                    {{-- Sub: Pesan --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-envelope w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                            <span class="menu-text flex-1">Pesan</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.messages.inbox') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-inbox w-4 text-center"></i> Pesan Masuk
                            </a>
                            <a href="{{ route('owner.messages.outbox') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-paper-plane w-4 text-center"></i> Pesan Keluar
                            </a>
                            <a href="{{ route('owner.messages.rejected') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-times-circle w-4 text-center text-red-400"></i> Pesan Ditolak
                            </a>
                        </div>
                    </div>

                    {{-- Sub: Keamanan Chat --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-shield-alt w-5 text-red-500 dark:text-red-400 text-center"></i>
                            <span class="menu-text flex-1">Keamanan Chat</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.security.blocked-words') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-ban w-4 text-center"></i> Kata Terlarang
                            </a>
                            <a href="{{ route('owner.security.chat-logs') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-history w-4 text-center"></i> Log Keamanan
                            </a>
                        </div>
                    </div>

                    {{-- Notifikasi Chat → arahkan ke Notification Center --}}
                    <a href="{{ route('owner.notifications.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-bell w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Notifikasi Chat</span>
                        <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-700/30">Baru</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: RUANGAN ============ --}}
            @php
                $isRoomActive = request()->routeIs('owner.rooms.*') ||
                                request()->routeIs('owner.bookings.*') ||
                                request()->routeIs('owner.schedules.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isRoomActive ? 'bg-cyan-50/70 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isRoomActive ? 'bg-gradient-to-br from-cyan-400 to-blue-500 shadow-md shadow-cyan-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-door-open w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isRoomActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Ruangan</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isRoomActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isRoomActive ? '' : 'hidden' }}">

                    <a href="{{ route('owner.rooms.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text">Semua Ruangan</span>
                    </a>
                    <a href="{{ route('owner.rooms.create') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-plus-circle w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Tambah Ruangan</span>
                    </a>

                    {{-- Sub: Booking --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-calendar-check w-5 text-orange-500 dark:text-orange-400 text-center"></i>
                            <span class="menu-text flex-1">Booking</span>
                            @if($pendingCount > 0)
                                <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $pendingCount }}</span>
                            @endif
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.bookings.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-list w-4 text-center"></i> Semua Booking
                            </a>
                            <a href="{{ route('owner.bookings.index', ['status' => 'pending']) }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-clock w-4 text-center text-yellow-500"></i> Menunggu
                                @if($pendingCount > 0)
                                    <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('owner.bookings.index', ['status' => 'conflict']) }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-exclamation-triangle w-4 text-center text-red-500"></i> Konflik
                            </a>
                            <a href="{{ route('owner.bookings.index', ['status' => 'approved']) }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-check-circle w-4 text-center text-green-500"></i> Disetujui
                            </a>
                            <a href="{{ route('owner.bookings.index', ['status' => 'completed']) }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-flag-checkered w-4 text-center text-blue-500"></i> Selesai
                            </a>
                            <a href="{{ route('owner.bookings.index', ['status' => 'rejected']) }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">
                                <i class="fas fa-times-circle w-4 text-center text-gray-500"></i> Ditolak
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('owner.rooms.calendar') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-calendar-alt w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Kalender</span>
                    </a>
                    <a href="{{ route('owner.rooms.maintenance.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-tools w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Maintenance</span>
                    </a>
                    <a href="{{ route('owner.rooms.export', ['format' => 'xlsx']) }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-file-export w-5 text-emerald-500 dark:text-emerald-400 text-center"></i>
                        <span class="menu-text">Export Data</span>
                    </a>

                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                    <a href="{{ route('owner.rooms.testing') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-qrcode w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                        <span class="menu-text">Testing QR Code</span>
                    </a>
                    <a href="{{ route('owner.rooms.public.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-globe w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Publik QR Code</span>
                    </a>
                    <a href="{{ route('owner.rooms.public.history') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-history w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Riwayat Publikasi</span>
                    </a>
                    <a href="{{ route('owner.rooms.private') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-lock w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Lihat Ruangan Private</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: GAME CENTER ============ --}}
            @php
                $isGameActive = request()->routeIs('owner.game.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isGameActive ? 'bg-amber-50/70 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isGameActive ? 'bg-gradient-to-br from-amber-400 to-orange-500 shadow-md shadow-amber-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-gamepad w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isGameActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Game Center</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isGameActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isGameActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.game.dashboard') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-tachometer-alt w-5 text-amber-500 dark:text-amber-400 text-center"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                    <a href="{{ route('owner.game.games.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-gamepad w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Daftar Game</span>
                    </a>
                    <a href="{{ route('owner.game.competitions.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-trophy w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Kompetisi</span>
                    </a>
                    <a href="{{ route('owner.game.challenges.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-bolt w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Challenge</span>
                    </a>
                    <a href="{{ route('owner.game.leaderboard.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list-ol w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Leaderboard</span>
                    </a>
                    <a href="{{ route('owner.game.players.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-users w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">Manajemen Pemain</span>
                    </a>
                    <a href="{{ route('owner.game.settings.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-cog w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Pengaturan</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: KELOMPOK ============ --}}
            @php
                $isKelompokActive = request()->routeIs('owner.kelompok.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isKelompokActive ? 'bg-purple-50/70 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isKelompokActive ? 'bg-gradient-to-br from-purple-400 to-indigo-500 shadow-md shadow-purple-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-layer-group w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isKelompokActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Kelompok</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isKelompokActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isKelompokActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.kelompok.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Semua Kelompok</span>
                    </a>
                    <a href="{{ route('owner.kelompok.pengumuman') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-bullhorn w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Pengumuman Kelompok</span>
                    </a>
                    <a href="{{ route('owner.kelompok.anggota') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-users w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Anggota Kelompok</span>
                    </a>
                    <a href="{{ route('owner.kelompok.akses') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-key w-5 text-red-500 dark:text-red-400 text-center"></i>
                        <span class="menu-text">Hak Akses Kelompok</span>
                    </a>
                    <a href="{{ route('owner.kelompok.pesan') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-comment-dots w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Beri Pesan Kelompok</span>
                    </a>
                    <a href="{{ route('owner.kelompok.custom') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-cog w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text">Custom Kelompok</span>
                    </a>
                    <a href="{{ route('owner.kelompok.invite') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-plus w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Invite Anggota</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: ABSENSI ============ --}}
            @php
                $isAttendanceActive = request()->routeIs('owner.attendance.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isAttendanceActive ? 'bg-cyan-50/70 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isAttendanceActive ? 'bg-gradient-to-br from-cyan-400 to-teal-500 shadow-md shadow-cyan-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-fingerprint w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isAttendanceActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Absensi</span>
                    @if($pendingPermissions > 0)
                        <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $pendingPermissions }}</span>
                    @endif
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isAttendanceActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isAttendanceActive ? '' : 'hidden' }}">

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1">Operasional</div>
                    <a href="{{ route('owner.attendance.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-fingerprint w-5 text-cyan-500 text-center"></i>
                        <span class="menu-text">Kehadiran</span>
                    </a>
                    <a href="{{ route('owner.attendance.create') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-plus-circle w-5 text-green-500 text-center"></i>
                        <span class="menu-text">Buat Absensi</span>
                    </a>
                    <a href="{{ route('owner.attendance.calendar') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-calendar-alt w-5 text-purple-500 text-center"></i>
                        <span class="menu-text">Kalender Absensi</span>
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Administrasi</div>
                    <a href="{{ route('owner.attendance.history') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-history w-5 text-gray-500 text-center"></i>
                        <span class="menu-text">History</span>
                    </a>
                    <a href="{{ route('owner.attendance.recap') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-chart-bar w-5 text-blue-500 text-center"></i>
                        <span class="menu-text">Rekap</span>
                    </a>
                    <a href="{{ route('owner.attendance.recap.past') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-archive w-5 text-indigo-500 text-center"></i>
                        <span class="menu-text">Rekap Masa Lalu</span>
                    </a>
                    <a href="{{ route('owner.attendance.permissions.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-file-signature w-5 text-orange-500 text-center"></i>
                        <span class="menu-text flex-1">Permohonan Izin</span>
                        @if($pendingPermissions > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $pendingPermissions }}</span>
                        @endif
                    </a>

                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Penilaian</div>
                    <a href="{{ route('owner.attendance.points') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-star w-5 text-yellow-500 text-center"></i>
                        <span class="menu-text">Poin Absensi</span>
                    </a>
                    <a href="{{ route('owner.attendance.penalties.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-gavel w-5 text-red-500 text-center"></i>
                        <span class="menu-text">Denda Absensi</span>
                    </a>

                    {{-- AUDIT & KEAMANAN (SINGLE, TIDAK DUPLIKAT) --}}
                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 py-1 mt-1">Audit &amp; Keamanan</div>

                    <a href="{{ route('owner.attendance.audit.index') }}"
                    class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm
                            {{ request()->routeIs('owner.attendance.audit.index') ? 'bg-gray-100/80 dark:bg-gray-700/50 font-semibold' : '' }}">
                        <i class="fas fa-shield-halved w-5 text-violet-500 text-center"></i>
                        <span class="menu-text">Audit Center</span>
                        @if($criticalCount > 0)
                            <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $criticalCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('owner.attendance.audit.export') }}"
                    class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-file-export w-5 text-cyan-500 text-center"></i>
                        <span class="menu-text">Export Forensic</span>
                    </a>

                    <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('audit:verify'))"
                            class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-fingerprint w-5 text-emerald-500 text-center"></i>
                        <span class="menu-text">Verify Integrity</span>
                    </button>
                </div>
            </div>

            {{-- ============ DROPDOWN: KEUANGAN ============ --}}
            @php
                $isFinancialActive = request()->routeIs('owner.financial.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isFinancialActive ? 'bg-emerald-50/70 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isFinancialActive ? 'bg-gradient-to-br from-emerald-400 to-teal-500 shadow-md shadow-emerald-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-coins w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isFinancialActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Keuangan</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isFinancialActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isFinancialActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.financial.dashboard') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-tachometer-alt w-5 text-emerald-500 dark:text-emerald-400 text-center"></i>
                        <span class="menu-text">Dashboard Keuangan</span>
                    </a>
                    <a href="{{ route('owner.financial.fee-plans.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-file-invoice-dollar w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Skema Biaya</span>
                    </a>
                    <a href="{{ route('owner.financial.generations.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-layer-group w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">Generasi</span>
                    </a>
                    <a href="{{ route('owner.financial.invoices.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-file-invoice w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text">Tagihan</span>
                    </a>
                    <a href="{{ route('owner.financial.payments.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-credit-card w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Pembayaran</span>
                    </a>
                    <a href="{{ route('owner.financial.penalty-rules.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-gavel w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Aturan Denda</span>
                    </a>
                    <a href="{{ route('owner.financial.points.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-star w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Poin</span>
                    </a>
                    <a href="{{ route('owner.financial.campaigns.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-bullhorn w-5 text-orange-500 dark:text-orange-400 text-center"></i>
                        <span class="menu-text">Kampanye</span>
                    </a>
                    <a href="{{ route('owner.financial.reports.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-chart-pie w-5 text-pink-500 dark:text-pink-400 text-center"></i>
                        <span class="menu-text">Laporan</span>
                    </a>
                    <a href="{{ route('owner.financial.reactivations.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-plus w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                        <span class="menu-text">Reaktivasi</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: DIVISI ============ --}}
            @php
                $isDivisiActive = request()->routeIs('owner.divisi.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isDivisiActive ? 'bg-rose-50/70 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isDivisiActive ? 'bg-gradient-to-br from-rose-400 to-pink-500 shadow-md shadow-rose-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-sitemap w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isDivisiActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Divisi</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isDivisiActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isDivisiActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.divisi.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-rose-500 dark:text-rose-400 text-center"></i>
                        <span class="menu-text">Semua Divisi</span>
                    </a>
                    <a href="{{ route('owner.divisi.pengumuman') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-bullhorn w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Pengumuman Divisi</span>
                    </a>
                    <a href="{{ route('owner.divisi.anggota') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-users w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Anggota Divisi</span>
                    </a>
                    <a href="{{ route('owner.divisi.akses') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-key w-5 text-red-500 dark:text-red-400 text-center"></i>
                        <span class="menu-text">Hak Akses Divisi</span>
                    </a>
                    <a href="{{ route('owner.divisi.pesan') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-comment-dots w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Beri Pesan Divisi</span>
                    </a>
                    <a href="{{ route('owner.divisi.custom') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-cog w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text">Custom Divisi</span>
                    </a>
                    <a href="{{ route('owner.divisi.invite') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-plus w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Invite Anggota</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: JADWAL ============ --}}
            @php
                $isScheduleActive = request()->routeIs('owner.schedules.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isScheduleActive ? 'bg-green-50/70 dark:bg-green-900/20 text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isScheduleActive ? 'bg-gradient-to-br from-green-400 to-emerald-500 shadow-md shadow-green-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-calendar-alt w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isScheduleActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Jadwal</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isScheduleActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isScheduleActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.schedules.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Semua Jadwal</span>
                    </a>
                    <a href="{{ route('owner.schedules.upload') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-upload w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Upload Jadwal</span>
                    </a>
                    <a href="{{ route('owner.schedules.public') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-globe w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text">Jadwal Publik</span>
                    </a>
                    <a href="{{ route('owner.schedules.history') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-history w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Riwayat Upload</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: PENGURUS ============ --}}
            @php
                $isOfficialsActive = request()->routeIs('owner.officials.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isOfficialsActive ? 'bg-indigo-50/70 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isOfficialsActive ? 'bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-md shadow-indigo-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-users-cog w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isOfficialsActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Pengurus</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isOfficialsActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isOfficialsActive ? '' : 'hidden' }}">

                    <a href="{{ route('owner.officials.dashboard') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-tachometer-alt w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">Dashboard Pengurus</span>
                    </a>
                    <a href="{{ route('owner.officials.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Data Pengurus</span>
                    </a>
                    <a href="{{ route('owner.officials.core') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-star w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Pengurus Inti</span>
                    </a>
                    <a href="{{ route('owner.officials.secretary') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-pen-fancy w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Sekretaris</span>
                    </a>
                    <a href="{{ route('owner.officials.assistant-secretary') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-edit w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                        <span class="menu-text">Asisten Sekretaris</span>
                    </a>
                    <a href="{{ route('owner.officials.pj-division') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-tag w-5 text-cyan-500 dark:text-cyan-400 text-center"></i>
                        <span class="menu-text">PJ Divisi</span>
                    </a>
                    <a href="{{ route('owner.officials.assistant-division') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-friends w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Asisten Divisi</span>
                    </a>
                    <a href="{{ route('owner.officials.division-members') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-users w-5 text-pink-500 dark:text-pink-400 text-center"></i>
                        <span class="menu-text">Anggota Divisi</span>
                    </a>

                    {{-- Sub: Struktur --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-sitemap w-5 text-amber-500 dark:text-amber-400 text-center"></i>
                            <span class="menu-text flex-1">Struktur Organisasi</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.officials.structure.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                            <a href="{{ route('owner.officials.structure.owner') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Owner</a>
                            <a href="{{ route('owner.officials.structure.superadmin') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Superadmin</a>
                            <a href="{{ route('owner.officials.structure.ketua') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Ketua</a>
                            <a href="{{ route('owner.officials.structure.pengurus-inti') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Pengurus Inti</a>
                            <a href="{{ route('owner.officials.structure.sekretaris') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Sekretaris</a>
                            <a href="{{ route('owner.officials.structure.asisten-sekretaris') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Asisten Sekretaris</a>
                            <a href="{{ route('owner.officials.structure.pj-divisi') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">PJ Divisi</a>
                            <a href="{{ route('owner.officials.structure.asisten-divisi') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Asisten Divisi</a>
                            <a href="{{ route('owner.officials.structure.anggota-divisi') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Anggota Divisi</a>
                        </div>
                    </div>

                    {{-- Sub: Divisi --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-building w-5 text-pink-500 dark:text-pink-400 text-center"></i>
                            <span class="menu-text flex-1">Divisi</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.officials.divisions.pengembang') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Pengembang</a>
                            <a href="{{ route('owner.officials.divisions.pdd') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">PDD</a>
                            <a href="{{ route('owner.officials.divisions.show', ['id' => 1]) }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Show</a>
                        </div>
                    </div>

                    {{-- Sub: Jabatan --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-briefcase w-5 text-amber-500 dark:text-amber-400 text-center"></i>
                            <span class="menu-text flex-1">Jabatan</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.officials.positions.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                            <a href="{{ route('owner.officials.positions.appoint') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Appoint</a>
                            <a href="{{ route('owner.officials.positions.demote') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Demote</a>
                            <a href="{{ route('owner.officials.positions.transfer') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Transfer</a>
                            <a href="{{ route('owner.officials.positions.terminate') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Terminate</a>
                            <a href="{{ route('owner.officials.positions.history') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">History</a>
                        </div>
                    </div>

                    {{-- Sub: QR Pengurus --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-qrcode w-5 text-teal-500 dark:text-teal-400 text-center"></i>
                            <span class="menu-text flex-1">QR Pengurus</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.officials.qr.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                            <a href="{{ route('owner.officials.qr.generate') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Generate</a>
                            <a href="{{ route('owner.officials.qr.massal') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Massal</a>
                            <a href="{{ route('owner.officials.qr.testing') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Testing</a>
                            <a href="{{ route('owner.officials.qr.print-card') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Print Card</a>
                        </div>
                    </div>

                    {{-- Sub: Audit Pengurus --}}
                    <div class="dropdown-sub-group">
                        <button onclick="event.stopPropagation(); this.closest('.dropdown-sub-group').querySelector('.sub-content').classList.toggle('hidden');"
                                class="menu-item flex items-center gap-4 w-full text-left px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                            <i class="fas fa-history w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                            <span class="menu-text flex-1">Audit Pengurus</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <div class="sub-content hidden space-y-0.5 pl-6">
                            <a href="{{ route('owner.officials.audit.index') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Index</a>
                            <a href="{{ route('owner.officials.audit.changes') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Changes</a>
                            <a href="{{ route('owner.officials.audit.appointments') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Appointments</a>
                            <a href="{{ route('owner.officials.audit.demotions') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Demotions</a>
                            <a href="{{ route('owner.officials.audit.transfers') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Transfers</a>
                            <a href="{{ route('owner.officials.audit.terminations') }}" class="menu-item flex items-center gap-4 px-4 py-1.5 rounded-lg transition-all duration-300 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-xs">Terminations</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ DROPDOWN: APPROVAL CENTER ============ --}}
            @php
                $isApprovalsActive = request()->routeIs('owner.approvals.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isApprovalsActive ? 'bg-green-50/70 dark:bg-green-900/20 text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isApprovalsActive ? 'bg-gradient-to-br from-green-400 to-green-600 shadow-md shadow-green-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-check-double w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isApprovalsActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Approval Center</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isApprovalsActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isApprovalsActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.approvals.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-green-500 dark:text-green-400 text-center"></i>
                        <span class="menu-text">Index</span>
                    </a>
                    <a href="{{ route('owner.approvals.pending') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-clock w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Pending</span>
                    </a>
                    <a href="{{ route('owner.approvals.history') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-history w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">History</span>
                    </a>
                    <a href="{{ route('owner.approvals.show', ['id' => 1]) }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-eye w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Show Detail</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: AUDIT CENTER ============ --}}
            @php
                $isAuditActive = request()->routeIs('owner.audit.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isAuditActive ? 'bg-gray-100/70 dark:bg-gray-700/30 text-gray-700 dark:text-gray-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isAuditActive ? 'bg-gradient-to-br from-gray-500 to-gray-700 shadow-md shadow-gray-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-search w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isAuditActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Audit Center</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isAuditActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isAuditActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.audit.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-list w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Index</span>
                    </a>
                    <a href="{{ route('owner.audit.activity') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-activity w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Activity</span>
                    </a>
                    <a href="{{ route('owner.audit.data-changes') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-database w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Data Changes</span>
                    </a>
                    <a href="{{ route('owner.audit.role-changes') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-shield w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">Role Changes</span>
                    </a>
                    <a href="{{ route('owner.audit.security') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-lock w-5 text-red-500 dark:text-red-400 text-center"></i>
                        <span class="menu-text">Security</span>
                    </a>
                </div>
            </div>

            {{-- ============ NOTIFICATION CENTER ============ --}}
            <a href="{{ route('owner.notifications.index') }}"
            class="menu-item flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300
                    {{ request()->routeIs('owner.notifications.index')
                        ? 'bg-gradient-to-r from-cyan-100/80 to-cyan-50/80 dark:from-cyan-900/40 dark:to-cyan-800/20 text-cyan-700 dark:text-cyan-300 shadow-sm shadow-cyan-500/10 border border-cyan-200/50 dark:border-cyan-700/30'
                        : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                            {{ request()->routeIs('owner.notifications.index') ? 'bg-gradient-to-br from-cyan-400 to-blue-500 shadow-md shadow-cyan-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                    <i class="fas fa-bell w-5 menu-icon text-center text-lg transition-all duration-300
                            {{ request()->routeIs('owner.notifications.index') ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                </div>
                <span class="menu-text text-sm font-medium tracking-wide">Notification Center</span>
                <span class="ml-auto menu-text text-[10px] px-2 py-0.5 rounded-full bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 border border-cyan-200/50 dark:border-cyan-700/30">New</span>
            </a>

            {{-- ============ PEMULIHAN DATA ============ --}}
            @php
                $isRestoreActive = request()->routeIs('owner.restore.*');
            @endphp
            <a href="{{ Route::has('owner.restore.index') ? route('owner.restore.index') : '#' }}"
            class="menu-item flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300
                    {{ $isRestoreActive
                        ? 'bg-gradient-to-r from-emerald-100/80 to-emerald-50/80 dark:from-emerald-900/40 dark:to-emerald-800/20 text-emerald-700 dark:text-emerald-300 shadow-sm shadow-emerald-500/10 border border-emerald-200/50 dark:border-emerald-700/30'
                        : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                            {{ $isRestoreActive ? 'bg-gradient-to-br from-emerald-400 to-teal-500 shadow-md shadow-emerald-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                    <i class="fas fa-shield-alt w-5 menu-icon text-center text-lg transition-all duration-300
                            {{ $isRestoreActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                </div>
                <span class="menu-text text-sm font-medium tracking-wide">Pemulihan Data</span>
                <span class="ml-auto menu-text text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-700/30">
                    <i class="fas fa-undo-alt mr-1"></i> Restore
                </span>
            </a>

            {{-- ============ DROPDOWN: PENGATURAN ============ --}}
            @php
                $isSettingsActive = request()->routeIs('owner.settings.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isSettingsActive ? 'bg-blue-50/70 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isSettingsActive ? 'bg-gradient-to-br from-blue-400 to-cyan-500 shadow-md shadow-blue-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-cog w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isSettingsActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">Pengaturan</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isSettingsActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isSettingsActive ? '' : 'hidden' }}">
                    <a href="{{  safe_route('owner.settings.profile') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-user-edit w-5 text-blue-500 dark:text-blue-400 text-center"></i>
                        <span class="menu-text">Data Diri</span>
                    </a>
                    <a href="{{  safe_route('owner.settings.general') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-sliders-h w-5 text-purple-500 dark:text-purple-400 text-center"></i>
                        <span class="menu-text">Pengaturan</span>
                    </a>
                    <a href="{{ safe_route('owner.settings.explore.index') }}"
    class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm
            {{ request()->routeIs('owner.settings.explore.*') ? 'bg-emerald-50/80 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-semibold' : '' }}">
        <i class="fas fa-compass w-5 text-emerald-500 dark:text-emerald-400 text-center"></i>
        <span class="menu-text">Jelajah</span>
    </a>
                    <a href="{{  safe_route('owner.settings.audit') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-history w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Audit History</span>
                    </a>
                    <a href="{{  safe_route('owner.settings.index') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-cog w-5 text-indigo-500 dark:text-indigo-400 text-center"></i>
                        <span class="menu-text">General Settings</span>
                    </a>
                </div>
            </div>

            {{-- ============ DROPDOWN: SYSTEM ============ --}}
            @php
                $isSystemActive = request()->routeIs('owner.system.*');
            @endphp
            <div class="dropdown-group">
                <button onclick="toggleDropdown(this)"
                        class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300
                            {{ $isSystemActive ? 'bg-gray-100/70 dark:bg-gray-700/30 text-gray-700 dark:text-gray-300' : 'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 hover:shadow-sm' }}">
                    <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300
                                {{ $isSystemActive ? 'bg-gradient-to-br from-gray-600 to-gray-800 shadow-md shadow-gray-500/20' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="fas fa-server w-5 menu-icon text-center text-lg transition-all duration-300
                                {{ $isSystemActive ? 'text-white scale-110' : 'text-gray-500 dark:text-gray-400 group-hover:scale-110' }}"></i>
                    </div>
                    <span class="menu-text text-sm font-medium tracking-wide flex-1">System</span>
                    <i class="fas fa-chevron-down menu-text text-xs text-gray-400 dark:text-gray-500 transition-transform duration-300 {{ $isSystemActive ? 'rotate-180' : '' }}"></i>
                </button>
                <div class="dropdown-content space-y-0.5 pl-2 {{ $isSystemActive ? '' : 'hidden' }}">
                    <a href="{{ route('owner.system.config') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-cog w-5 text-gray-500 dark:text-gray-400 text-center"></i>
                        <span class="menu-text">Config</span>
                    </a>
                    <a href="{{ route('owner.system.backup') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-database w-5 text-yellow-500 dark:text-yellow-400 text-center"></i>
                        <span class="menu-text">Backup</span>
                    </a>
                    <a href="{{ route('owner.system.health') }}" class="menu-item flex items-center gap-4 px-4 py-2 rounded-lg transition-all duration-300 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-gray-700/50 text-sm">
                        <i class="fas fa-heartbeat w-5 text-red-500 dark:text-red-400 text-center"></i>
                        <span class="menu-text">Health</span>
                    </a>
                </div>
            </div>

        </nav>

        {{-- ============================================ --}}
        {{-- LOGOUT FOOTER — STICKY --}}
        {{-- ============================================ --}}
        <div class="flex-shrink-0 border-t border-gray-200/60 dark:border-gray-700/60 bg-gradient-to-t from-white via-white/95 to-white/80 dark:from-gray-900 dark:via-gray-900/95 dark:to-gray-900/80 backdrop-blur-sm p-3">
            <button type="button"
                    data-trigger-logout
                    class="menu-item flex items-center gap-4 w-full text-left px-4 py-3 rounded-xl transition-all duration-300 hover:bg-red-50/80 dark:hover:bg-red-900/20 hover:shadow-sm text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 group">
                <div class="menu-icon-wrapper w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-300 bg-gray-100 dark:bg-gray-700 group-hover:bg-red-100 dark:group-hover:bg-red-900/30 group-hover:shadow-md group-hover:shadow-red-500/10">
                    <i class="fas fa-sign-out-alt w-5 menu-icon text-center text-lg transition-all duration-300 text-red-400 dark:text-red-500 group-hover:text-red-600 dark:group-hover:text-red-400 group-hover:scale-110"></i>
                </div>
                <span class="menu-text text-sm font-medium tracking-wide">Keluar Akun</span>
                <span class="ml-auto menu-text text-[10px] px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-400 dark:text-red-400 border border-red-200/50 dark:border-red-700/30">Logout</span>
            </button>
        </div>
    </aside>

    {{-- ============================================ --}}
    {{-- SIDEBAR JAVASCRIPT --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar       = document.getElementById('sidebar');
            const overlay       = document.getElementById('sidebar-overlay');
            const toggleBtn     = document.getElementById('sidebar-toggle');
            const closeMobileBtn= document.getElementById('sidebar-close-mobile');
            const collapseBtn   = document.getElementById('sidebar-collapse-btn');
            const isMobile      = () => window.innerWidth < 1024;

            function openMobileSidebar() {
                sidebar.classList.add('open');
                overlay?.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            window.closeMobileSidebar = function () {
                sidebar.classList.remove('open');
                overlay?.classList.remove('active');
                document.body.style.overflow = '';
            };

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (isMobile()) {
                        if (sidebar.classList.contains('open')) window.closeMobileSidebar();
                        else openMobileSidebar();
                    }
                });
            }

            overlay?.addEventListener('click', window.closeMobileSidebar);
            closeMobileBtn?.addEventListener('click', window.closeMobileSidebar);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                    window.closeMobileSidebar();
                }
            });

            if (collapseBtn) {
                collapseBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('sidebar-collapsed');
                    const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                    try { localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false'); } catch (e) {}
                    if (typeof window.updateSidebarOpenButton === 'function') window.updateSidebarOpenButton();
                });

                try {
                    if (localStorage.getItem('sidebar_collapsed') === 'true') {
                        sidebar.classList.add('sidebar-collapsed');
                    }
                } catch (e) {}

                if (typeof window.updateSidebarOpenButton === 'function') window.updateSidebarOpenButton();
            }

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024 && sidebar.classList.contains('open')) {
                    window.closeMobileSidebar();
                }
            });

            // ---- Highlight menu aktif ----
            const currentPath = window.location.pathname;
            document.querySelectorAll('#sidebar-menu a, #sidebar-menu .menu-item:not(button)').forEach(link => {
                const href = link.getAttribute('href');
                if (href && (href === currentPath || (href !== '#' && currentPath.startsWith(href)))) {
                    link.classList.add('bg-gradient-to-r', 'from-amber-50', 'to-amber-100/70',
                        'dark:from-amber-900/40', 'dark:to-amber-800/20',
                        'text-amber-700', 'dark:text-amber-300',
                        'shadow-sm', 'shadow-amber-500/10',
                        'border', 'border-amber-200/50', 'dark:border-amber-700/30');
                }
            });

            // ---- Dropdown toggle ----
            window.toggleDropdown = function (button) {
                const group   = button.closest('.dropdown-group');
                const content = group.querySelector('.dropdown-content');
                const icon    = button.querySelector('.fa-chevron-down');

                document.querySelectorAll('.dropdown-group .dropdown-content').forEach(el => {
                    if (el !== content) {
                        el.classList.add('hidden');
                        const parentBtn  = el.closest('.dropdown-group').querySelector('button');
                        const parentIcon = parentBtn?.querySelector('.fa-chevron-down');
                        if (parentIcon) parentIcon.classList.remove('rotate-180');
                    }
                });

                content.classList.toggle('hidden');
                if (icon) icon.classList.toggle('rotate-180');
            };

            // ---- Ripple effect ----
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    if (this.closest('.dropdown-group') && this.tagName === 'BUTTON') return;
                    const rect = this.getBoundingClientRect();
                    const x    = e.clientX - rect.left;
                    const y    = e.clientY - rect.top;
                    const ripple = document.createElement('span');
                    ripple.className = 'ripple-effect';
                    ripple.style.left = x + 'px';
                    ripple.style.top  = y + 'px';
                    ripple.style.width  = '150px';
                    ripple.style.height = '150px';
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255,255,255,0.3)';
                    ripple.style.transform = 'translate(-50%, -50%) scale(0)';
                    ripple.style.animation = 'rippleAnim 0.6s ease-out forwards';
                    ripple.style.pointerEvents = 'none';
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 700);
                });
            });

            const style = document.createElement('style');
            style.textContent = `@keyframes rippleAnim { to { transform: translate(-50%, -50%) scale(1); opacity: 0; } }`;
            document.head.appendChild(style);
        });
    </script>
<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40 safe-top">
    <div class="flex items-center justify-between px-3 md:px-6 py-2 md:py-3">
        <div class="flex items-center gap-2 md:gap-4 min-w-0">
            <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none p-1.5 md:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-bars text-lg md:text-xl"></i>
            </button>
            <h1 class="text-sm md:text-xl font-semibold text-gray-800 dark:text-white truncate">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>

        <div class="flex items-center gap-1.5 md:gap-4 flex-shrink-0">
            {{-- Dark/Light Mode Toggle --}}
            <button id="theme-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1.5 md:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition focus:outline-none">
                <i id="theme-icon" class="fas fa-moon text-base md:text-xl"></i>
            </button>

            {{-- Notifikasi --}}
            <div class="relative" id="notification-wrapper">
                <button id="notification-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 relative p-1.5 md:p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition focus:outline-none">
                    <i class="fas fa-bell text-base md:text-xl"></i>
                    @php
                        $unreadCount = 0;
                        try {
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        } catch (\Exception $e) {}
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 md:-top-1 md:-right-1 w-4 h-4 md:w-5 md:h-5 bg-red-500 text-white text-[8px] md:text-[10px] rounded-full flex items-center justify-center font-bold badge-pulse">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>
                <div id="notification-dropdown" class="hidden absolute right-0 mt-1 md:mt-3 w-[calc(100vw-2rem)] max-w-[380px] md:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 max-h-[80vh] overflow-hidden">
                    <div class="p-3 md:p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center sticky top-0 bg-white dark:bg-gray-800 z-10">
                        <h3 class="font-bold text-sm md:text-base text-gray-800 dark:text-white">Notifikasi</h3>
                        <a href="{{ route('anggota-umum.notifications') }}" class="text-xs text-teal-600 dark:text-teal-400 hover:underline font-medium">Lihat Semua</a>
                    </div>
                    <div class="overflow-y-auto max-h-[60vh] divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $notifications = auth()->user()->notifications()->latest()->take(10)->get();
                        @endphp
                        @forelse($notifications as $notif)
                            <a href="{{ $notif->link ?? '#' }}" class="block px-3 md:px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $notif->read_at ? 'opacity-70' : '' }}" data-id="{{ $notif->id }}">
                                <div class="flex items-start gap-2 md:gap-3">
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-{{ $notif->type }}-100 dark:bg-{{ $notif->type }}-900/30 flex items-center justify-center text-{{ $notif->type }}-600 dark:text-{{ $notif->type }}-400 flex-shrink-0 text-sm md:text-base">
                                        <i class="fas {{ $notif->icon ?? 'fa-bell' }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs md:text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $notif->title }}</p>
                                        <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 truncate">{{ $notif->message }}</p>
                                        <p class="text-[8px] md:text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notif->read_at)
                                        <span class="w-1.5 h-1.5 md:w-2 md:h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="py-6 md:py-8 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-inbox text-2xl md:text-3xl mb-2 block"></i>
                                <p class="text-xs md:text-sm">Belum ada notifikasi</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Profil --}}
            <div class="relative" id="profile-wrapper">
                <button id="profile-toggle" class="flex items-center gap-1 md:gap-2 focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=14B8A6&color=fff&size=40"
                         alt="Avatar" class="h-7 w-7 md:h-10 md:w-10 rounded-full border-2 border-teal-500 shadow-md flex-shrink-0">
                    <span class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block max-w-[80px] md:max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-[10px] md:text-xs text-gray-400 hidden sm:inline"></i>
                </button>
                <div id="profile-dropdown" class="hidden absolute right-0 mt-1 md:mt-3 w-[calc(100vw-2rem)] max-w-[320px] md:w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden dropdown-enter">
                    <div class="px-4 md:px-5 py-3 md:py-4 bg-gradient-to-r from-teal-500/10 to-cyan-500/10 dark:from-teal-500/5 dark:to-cyan-500/5 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3 md:gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=14B8A6&color=fff&size=64"
                                 alt="Avatar" class="h-12 w-12 md:h-14 md:w-14 rounded-full border-2 border-teal-500 shadow-md flex-shrink-0">
                            <div class="min-w-0">
                                <p class="font-bold text-sm md:text-base text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] md:text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-0.5 text-[8px] md:text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200">
                                    {{ Auth::user()->roles->pluck('name')->implode(', ') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="py-1">
                        <a href="{{ Auth::user()->getDashboardRoute() }}" class="flex items-center gap-3 px-4 md:px-5 py-2 md:py-2.5 text-xs md:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-tachometer-alt w-4 md:w-5 text-gray-400"></i> Dashboard
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 md:px-5 py-2 md:py-2.5 text-xs md:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-y border-gray-100 dark:border-gray-700">
                            <i class="fas fa-user-cog w-4 md:w-5 text-gray-400"></i> Data Diri
                        </a>
                        <a href="{{ route('anggota-umum.pengaturan') }}" class="flex items-center gap-3 px-4 md:px-5 py-2 md:py-2.5 text-xs md:text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-cog w-4 md:w-5 text-gray-400"></i> Pengaturan
                        </a>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 py-1">
                        <button onclick="showLogoutModal(event)" class="flex items-center gap-3 w-full text-left px-4 md:px-5 py-2 md:py-2.5 text-xs md:text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-sign-out-alt w-4 md:w-5"></i> Keluar Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="logout-confirm-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 w-full max-w-[320px] md:max-w-md p-5 md:p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 md:w-16 md:h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-3 md:mb-4 text-red-600 dark:text-red-400 text-2xl md:text-3xl">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mb-1 md:mb-2">Konfirmasi Keluar</h3>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-4 md:mb-6">Apakah Anda yakin ingin keluar dari akun ini?</p>
            <div class="flex w-full gap-3 md:gap-4">
                <button onclick="closeLogoutModal()" class="flex-1 py-2 md:py-2.5 px-3 md:px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium text-sm md:text-base">Tidak</button>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2 md:py-2.5 px-3 md:px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-md shadow-red-500/20 transition text-sm md:text-base">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---- DARK MODE TOGGLE ----
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
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

        // ---- NOTIFICATION DROPDOWN ----
        const notifToggle = document.getElementById('notification-toggle');
        const notifDropdown = document.getElementById('notification-dropdown');
        if (notifToggle && notifDropdown) {
            notifToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                const wrapper = document.getElementById('notification-wrapper');
                if (wrapper && !wrapper.contains(e.target)) notifDropdown.classList.add('hidden');
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !notifDropdown.classList.contains('hidden')) notifDropdown.classList.add('hidden');
            });
        }

        // ---- PROFILE DROPDOWN ----
        const profileToggle = document.getElementById('profile-toggle');
        const profileDropdown = document.getElementById('profile-dropdown');
        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                const wrapper = document.getElementById('profile-wrapper');
                if (wrapper && !wrapper.contains(e.target)) profileDropdown.classList.add('hidden');
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !profileDropdown.classList.contains('hidden')) profileDropdown.classList.add('hidden');
            });
        }

        // ---- LOGOUT MODAL ----
        window.showLogoutModal = function(event) {
            event.preventDefault();
            const modal = document.getElementById('logout-confirm-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        };
        window.closeLogoutModal = function() {
            const modal = document.getElementById('logout-confirm-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };
        document.getElementById('logout-confirm-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLogoutModal();
        });
    });
</script>
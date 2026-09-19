{{-- resources/views/partials/role/anggota/sidebar.blade.php --}}
@php
    $currentRoute = Route::currentRouteName();
    $menus = [
        ['route' => 'anggota.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
        ['route' => 'anggota.profil', 'icon' => 'fa-user', 'label' => 'Profil'],
        ['route' => 'anggota.poin', 'icon' => 'fa-medal', 'label' => 'Poin & Prestasi'],
        ['route' => 'anggota.presensi', 'icon' => 'fa-clipboard-check', 'label' => 'Presensi'],
        ['route' => 'anggota.event', 'icon' => 'fa-calendar-alt', 'label' => 'Event'],
        ['route' => 'anggota.sertifikat', 'icon' => 'fa-certificate', 'label' => 'Sertifikat'],
        ['route' => 'anggota.portofolio', 'icon' => 'fa-briefcase', 'label' => 'Portofolio'],
        ['route' => 'anggota.pengaturan', 'icon' => 'fa-cog', 'label' => 'Pengaturan'],
    ];
@endphp

<aside id="sidebar"
       x-data="{
           isCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
           isMobileOpen: false,
           isDesktop: window.innerWidth >= 768
       }"
       x-init="() => {
           // Setup resize listener
           window.addEventListener('resize', () => {
               const wasDesktop = isDesktop;
               isDesktop = window.innerWidth >= 768;
               if (!wasDesktop && isDesktop) {
                   isMobileOpen = false;
               }
               if (wasDesktop && !isDesktop) {
                   // Saat pindah ke mobile, pastikan mobileOpen false
                   isMobileOpen = false;
               }
               // Dispatch event untuk update margin
               window.dispatchEvent(new CustomEvent('sidebarToggled', {
                   detail: { collapsed: isCollapsed, mobileOpen: isMobileOpen }
               }));
           });

           // Event toggle dari header
           window.addEventListener('toggleSidebar', () => {
               if (isDesktop) {
                   isCollapsed = !isCollapsed;
                   localStorage.setItem('sidebarCollapsed', isCollapsed);
                   window.dispatchEvent(new CustomEvent('sidebarToggled', {
                       detail: { collapsed: isCollapsed, mobileOpen: isMobileOpen }
                   }));
               } else {
                   isMobileOpen = !isMobileOpen;
                   window.dispatchEvent(new CustomEvent('sidebarToggled', {
                       detail: { collapsed: isCollapsed, mobileOpen: isMobileOpen }
                   }));
               }
           });

           // Sinkronisasi dari layout
           window.addEventListener('sidebarToggled', (e) => {
               if (e.detail && e.detail.collapsed !== undefined) {
                   isCollapsed = e.detail.collapsed;
                   localStorage.setItem('sidebarCollapsed', isCollapsed);
               }
               if (e.detail && e.detail.mobileOpen !== undefined) {
                   isMobileOpen = e.detail.mobileOpen;
               }
           });

           // Dispatch initial state
           setTimeout(() => {
               window.dispatchEvent(new CustomEvent('sidebarToggled', {
                   detail: { collapsed: isCollapsed, mobileOpen: isMobileOpen }
               }));
           }, 50);
       }"
       class="fixed md:sticky top-0 left-0 z-40 h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out flex flex-col overflow-hidden shadow-2xl md:shadow-none"
       :class="{
           'w-64': !isCollapsed && isDesktop,
           'w-20': isCollapsed && isDesktop,
           'translate-x-0': isMobileOpen,
           '-translate-x-full': !isMobileOpen && !isDesktop,
           'w-[85%] max-w-sm': isMobileOpen && !isDesktop
       }">

    {{-- Header sidebar: Logo + Close (mobile) --}}
    <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
        <div class="flex items-center gap-2 overflow-hidden">
            <img src="{{ asset('images/logo-unicode.png') }}" alt="UNICODE" class="w-8 h-8 rounded-lg flex-shrink-0" onerror="this.src='https://placehold.co/32x32/06b6d4/white?text=U'">
            <span class="text-lg font-bold bg-gradient-to-r from-cyan to-purple bg-clip-text text-transparent whitespace-nowrap transition-all duration-300"
                  :class="isCollapsed && isDesktop ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                UNICODE
            </span>
        </div>
        {{-- Tombol close hanya di mobile --}}
        <button class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-cyan hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                @click="isMobileOpen = false; window.dispatchEvent(new CustomEvent('sidebarToggled', { detail: { collapsed: isCollapsed, mobileOpen: false } }))">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

   {{-- User info --}}
@auth
<div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex-shrink-0 transition-all duration-300 overflow-hidden"
     :class="isCollapsed && isDesktop ? 'max-h-0 py-0 opacity-0' : 'max-h-40 opacity-100'">
    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ Auth::user()->email }}</p>
    <span class="inline-block mt-1 px-2 py-0.5 bg-cyan/10 text-cyan text-[10px] font-medium rounded-full">
        {{ optional(Auth::user()->roles)->pluck('name')->implode(', ') ?: 'Anggota' }}
    </span>
</div>
@else
<div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex-shrink-0 transition-all duration-300 overflow-hidden"
     :class="isCollapsed && isDesktop ? 'max-h-0 py-0 opacity-0' : 'max-h-40 opacity-100'">
    <p class="text-sm text-gray-500 dark:text-gray-400">Belum Login</p>
</div>
@endauth

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        @foreach ($menus as $menu)
            <a href="{{ route($menu['route']) }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-cyan dark:hover:text-cyan transition-all duration-200 {{ $currentRoute === $menu['route'] ? 'active' : '' }}"
               :title="isCollapsed && isDesktop ? '{{ $menu['label'] }}' : ''">
                <i class="fas {{ $menu['icon'] }} w-5 text-center flex-shrink-0 sidebar-icon text-base"></i>
                <span class="transition-all duration-300 whitespace-nowrap"
                      :class="isCollapsed && isDesktop ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    {{ $menu['label'] }}
                </span>
                @if ($menu['label'] === 'Poin & Prestasi')
                    <span class="ml-auto bg-cyan/20 text-cyan text-[10px] px-2 py-0.5 rounded-full transition-all duration-300"
                          :class="isCollapsed && isDesktop ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        NEW
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-400 dark:text-gray-500 transition-all duration-300 overflow-hidden"
         :class="isCollapsed && isDesktop ? 'max-h-0 py-0 opacity-0' : 'max-h-20 opacity-100'">
        <p class="text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }} UNICODE</p>
        <p class="text-[10px] text-gray-400 dark:text-gray-500">UKM Pemrograman</p>
    </div>
</aside>

{{-- Overlay untuk mobile --}}
<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 transition-opacity duration-300 md:hidden sidebar-overlay"
     x-data="{ show: false }"
     x-show="show"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="
         const sidebar = document.getElementById('sidebar');
         if (sidebar && sidebar.__x) {
             sidebar.__x.$data.isMobileOpen = false;
             window.dispatchEvent(new CustomEvent('sidebarToggled', {
                 detail: { collapsed: sidebar.__x.$data.isCollapsed, mobileOpen: false }
             }));
         }
         show = false;
     ">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        // Sinkronkan overlay dengan isMobileOpen
        if (sidebar && sidebar.__x) {
            sidebar.__x.$watch('isMobileOpen', (value) => {
                if (overlay && overlay.__x) {
                    overlay.__x.$data.show = value;
                }
                // Update status di layout
                window.dispatchEvent(new CustomEvent('sidebarToggled', {
                    detail: {
                        collapsed: sidebar.__x.$data.isCollapsed,
                        mobileOpen: value
                    }
                }));
            });
        }

        // Tutup sidebar mobile saat klik di overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                if (sidebar && sidebar.__x) {
                    sidebar.__x.$data.isMobileOpen = false;
                    window.dispatchEvent(new CustomEvent('sidebarToggled', {
                        detail: {
                            collapsed: sidebar.__x.$data.isCollapsed,
                            mobileOpen: false
                        }
                    }));
                }
            });
        }

        // Keyboard shortcut: Ctrl+B untuk toggle sidebar (fallback)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'b' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('toggleSidebar'));
            }
        });

        // Update saat resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && sidebar && sidebar.__x) {
                // Pastikan mobile tertutup saat resize ke desktop
                if (sidebar.__x.$data.isMobileOpen) {
                    sidebar.__x.$data.isMobileOpen = false;
                    if (overlay && overlay.__x) {
                        overlay.__x.$data.show = false;
                    }
                }
            }
        });
    });
</script>
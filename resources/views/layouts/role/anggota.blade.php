{{-- resources/views/layouts/role/anggota.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('partials.role.anggota.head')
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-midnight text-gray-800 dark:text-gray-200 antialiased">

    <div class="flex h-screen overflow-hidden" x-data="layoutState()" x-init="initLayout()">

        {{-- Sidebar --}}
        @include('partials.role.anggota.sidebar')

        {{-- Main Wrapper dengan margin dinamis --}}
        <div id="mainContent"
             class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300 min-w-0"
             :style="'margin-left: ' + mainMargin + 'px;'">

            {{-- Header --}}
            @include('partials.role.anggota.header')

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 dark:bg-midnight">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('partials.role.anggota.footer')
        </div>
    </div>

    {{-- Modal Logout --}}
    @include('partials.role.anggota.logout-modal')

    {{-- Scripts --}}
    <script src="{{ asset('js/theme.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ============================================
            // LAYOUT STATE — Alpine.js component
            // ============================================
            window.layoutState = function() {
                return {
                    isCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                    isMobileOpen: false,
                    isDesktop: window.innerWidth >= 768,
                    mainMargin: 0,

                    initLayout() {
                        this.updateMainMargin();
                        this.setupResizeListener();
                        this.setupToggleListener();
                        this.setupKeyboardShortcuts();
                        // Dispatch initial state
                        setTimeout(() => this.updateMainMargin(), 50);
                    },

                    updateMainMargin() {
                        if (this.isDesktop) {
                            this.mainMargin = this.isCollapsed ? 80 : 256;
                        } else {
                            this.mainMargin = 0;
                        }
                        // Trigger event untuk komponen lain
                        window.dispatchEvent(new CustomEvent('sidebarStateChanged', {
                            detail: {
                                collapsed: this.isCollapsed,
                                mobileOpen: this.isMobileOpen,
                                isDesktop: this.isDesktop,
                                margin: this.mainMargin
                            }
                        }));
                    },

                    setupResizeListener() {
                        let resizeTimer;
                        window.addEventListener('resize', () => {
                            clearTimeout(resizeTimer);
                            resizeTimer = setTimeout(() => {
                                const wasDesktop = this.isDesktop;
                                this.isDesktop = window.innerWidth >= 768;
                                // Jika pindah dari mobile ke desktop, tutup mobile overlay
                                if (!wasDesktop && this.isDesktop) {
                                    this.isMobileOpen = false;
                                }
                                // Jika pindah dari desktop ke mobile, pastikan margin 0
                                if (wasDesktop && !this.isDesktop) {
                                    this.mainMargin = 0;
                                }
                                this.updateMainMargin();
                            }, 100);
                        });
                    },

                    setupToggleListener() {
                        // Dengarkan event toggle dari header
                        window.addEventListener('toggleSidebar', () => {
                            if (this.isDesktop) {
                                // Desktop: toggle collapse
                                this.isCollapsed = !this.isCollapsed;
                                localStorage.setItem('sidebarCollapsed', this.isCollapsed);
                            } else {
                                // Mobile: toggle open/close
                                this.isMobileOpen = !this.isMobileOpen;
                            }
                            this.updateMainMargin();
                        });

                        // Dengarkan event dari sidebar untuk sinkronisasi
                        window.addEventListener('sidebarToggled', (e) => {
                            if (e.detail && e.detail.collapsed !== undefined) {
                                this.isCollapsed = e.detail.collapsed;
                                localStorage.setItem('sidebarCollapsed', this.isCollapsed);
                                this.updateMainMargin();
                            }
                            if (e.detail && e.detail.mobileOpen !== undefined) {
                                this.isMobileOpen = e.detail.mobileOpen;
                                this.updateMainMargin();
                            }
                        });
                    },

                    setupKeyboardShortcuts() {
                        document.addEventListener('keydown', (e) => {
                            if (e.ctrlKey && e.key === 'b' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                                e.preventDefault();
                                window.dispatchEvent(new CustomEvent('toggleSidebar'));
                            }
                            if (e.ctrlKey && e.key === ' ' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                                e.preventDefault();
                                const container = document.getElementById('profileDropdownContainer');
                                if (container && container.__x) {
                                    container.__x.$data.open = !container.__x.$data.open;
                                }
                            }
                        });
                    }
                };
            };
        });

        // Fungsi logout global
        window.showLogoutModal = function(event) {
            if (event) event.preventDefault();
            const modal = document.getElementById('logout-confirm-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                const content = modal.querySelector('.transform');
                if (content) {
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }
            }, 10);
        };

        window.closeLogoutModal = function() {
            const modal = document.getElementById('logout-confirm-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            const content = modal.querySelector('.transform');
            if (content) {
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        };

        // Close modal on backdrop click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('logout-confirm-modal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                window.closeLogoutModal();
            }
        });

        // Offline/Online redirect
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('previousPage', window.location.href);
        });

        window.addEventListener('offline', function() {
            if (window.location.pathname !== '/offline') {
                window.location.href = '/offline';
            }
        });

        window.addEventListener('online', function() {
            if (window.location.pathname === '/offline') {
                const prev = sessionStorage.getItem('previousPage') || '/';
                window.location.href = prev;
            }
        });
    </script>
    {{-- Deteksi Offline/Online Global --}}
<script>
    (function() {
        // Simpan URL sebelum offline
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('previousPage', window.location.href);
        });

        // Saat offline, redirect ke halaman offline (kecuali sudah di offline)
        window.addEventListener('offline', function() {
            if (window.location.pathname !== '/offline') {
                sessionStorage.setItem('previousPage', window.location.href);
                window.location.href = '/offline';
            }
        });

        // Saat online, jika di halaman offline, redirect ke halaman sebelumnya
        window.addEventListener('online', function() {
            if (window.location.pathname === '/offline') {
                const prev = sessionStorage.getItem('previousPage') || '/';
                window.location.href = prev;
            }
        });

        // Cek status saat halaman dimuat (jika offline, redirect)
        if (!navigator.onLine && window.location.pathname !== '/offline') {
            sessionStorage.setItem('previousPage', window.location.href);
            window.location.href = '/offline';
        }

        // Jika sudah di halaman offline dan ternyata online, redirect balik
        if (navigator.onLine && window.location.pathname === '/offline') {
            const prev = sessionStorage.getItem('previousPage') || '/';
            window.location.href = prev;
        }
    })();
</script>
    @stack('scripts')
</body>
</html>
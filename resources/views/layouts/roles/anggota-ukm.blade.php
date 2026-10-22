{{-- resources/views/layouts/roles/anggota.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('partials.roles.anggota-ukm.head')
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-midnight text-gray-800 dark:text-gray-200 antialiased">

    {{-- Root Alpine: 1 store, 1 root --}}
    <div x-data
         x-effect="document.body.style.overflow = (!$store.sidebar.isDesktop && $store.sidebar.mobileOpen) ? 'hidden' : ''">

        {{-- ================= SIDEBAR ================= --}}
        @include('partials.roles.anggota-ukm.sidebar')

        {{-- ================= MAIN WRAPPER ================= --}}
        <div id="mainContent"
             class="min-h-screen flex flex-col
                    transition-[margin] duration-300 ease-in-out will-change-[margin]"
             :class="{
                 'md:ml-80':  $store.sidebar.desktopOpen,
                 'md:ml-0':  !$store.sidebar.desktopOpen
             }">

            @include('partials.roles.anggota-ukm.header')

            <main class="flex-1 p-4 md:p-6">
                @yield('content')
            </main>

            @include('partials.roles.anggota-ukm.footer')
        </div>
    </div>

    @include('partials.roles.anggota-ukm.logout-modal')

    {{-- =========================================================
         SIDEBAR STORE — HARUS sebelum Alpine CDN
         Berisi: state, helper, shortcut, resize handler
    ========================================================== --}}
    <script>
    document.addEventListener('alpine:init', () => {

        /* -------- STORE UTAMA -------- */
        Alpine.store('sidebar', {
            // Desktop: baca dari localStorage, default terbuka
            desktopOpen: (localStorage.getItem('sidebarDesktopOpen') ?? 'true') === 'true',
            // Mobile: selalu mulai tertutup
            mobileOpen: false,
            // Deteksi viewport
            isDesktop: window.matchMedia('(min-width: 768px)').matches,

            /* --- Aksi utama --- */
            toggle() {
                this.isDesktop ? this.setDesktop(!this.desktopOpen)
                               : this.setMobile(!this.mobileOpen);
            },
            open() {
                this.isDesktop ? this.setDesktop(true) : this.setMobile(true);
            },
            close() {
                this.isDesktop ? this.setDesktop(false) : this.setMobile(false);
            },

            /* --- Setters dengan side effect --- */
            setDesktop(val) {
                this.desktopOpen = !!val;
                localStorage.setItem('sidebarDesktopOpen', String(this.desktopOpen));
            },
            setMobile(val) {
                this.mobileOpen = !!val;
            },
        });

        const store = Alpine.store('sidebar');

        /* -------- RESIZE (debounced) -------- */
        let rt;
        window.addEventListener('resize', () => {
            clearTimeout(rt);
            rt = setTimeout(() => {
                const wasDesktop = store.isDesktop;
                store.isDesktop = window.matchMedia('(min-width: 768px)').matches;

                // Pindah dari mobile → desktop: tutup drawer mobile
                if (!wasDesktop && store.isDesktop) {
                    store.mobileOpen = false;
                }
            }, 100);
        });

        /* -------- KEYBOARD SHORTCUTS GLOBAL -------- */
        window.addEventListener('keydown', (e) => {
            // Lewati jika user sedang mengetik di form
            const tag = (document.activeElement?.tagName || '').toUpperCase();
            const typing = tag === 'INPUT'
                || tag === 'TEXTAREA'
                || tag === 'SELECT'
                || document.activeElement?.isContentEditable;
            if (typing) return;

            // Ctrl + B / Cmd + B → toggle sidebar
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                store.toggle();
                return;
            }

            // Esc → tutup drawer mobile
            if (e.key === 'Escape' && !store.isDesktop && store.mobileOpen) {
                store.setMobile(false);
                return;
            }

            // Ctrl + Shift + D → buka drawer mobile (bonus, opsional)
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 'd') {
                e.preventDefault();
                if (!store.isDesktop) store.setMobile(true);
            }
        });

        /* -------- TUTUP DRAWER MOBILE OTOMATIS SAAT LINK DIKLIK -------- */
        document.addEventListener('click', (e) => {
            if (store.isDesktop || !store.mobileOpen) return;
            const link = e.target.closest('aside a[href]');
            if (link) store.setMobile(false);
        });
    });
    </script>

    {{-- Alpine.js (defer = tidak blocking) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Theme (dark mode, dll) --}}
    <script src="{{ asset('js/theme.js') }}"></script>

    {{-- Logout modal helper --}}
    <script>
        window.showLogoutModal = function (event) {
            if (event) event.preventDefault();
            const modal = document.getElementById('logout-confirm-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform')?.classList.replace('scale-95', 'scale-100');
            });
        };

        window.closeLogoutModal = function () {
            const modal = document.getElementById('logout-confirm-modal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            modal.querySelector('.transform')?.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        };

        document.addEventListener('click', (e) => {
            const modal = document.getElementById('logout-confirm-modal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                window.closeLogoutModal();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
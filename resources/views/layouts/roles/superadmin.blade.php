{{-- resources/views/layouts/roles/superadmin.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('partials.roles.superadmin.head')
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-slate-950 text-gray-800 dark:text-gray-200 antialiased">

    <div x-data
         x-effect="document.body.style.overflow = (!$store.sidebar.isDesktop && $store.sidebar.mobileOpen) ? 'hidden' : ''">

        {{-- ================= SIDEBAR ================= --}}
        @include('partials.roles.superadmin.sidebar')

        {{-- ================= MAIN WRAPPER ================= --}}
        <div id="mainContent"
             class="min-h-screen flex flex-col
                    transition-[margin] duration-300 ease-in-out will-change-[margin]"
             :class="{
                 'md:ml-80':  $store.sidebar.desktopOpen,
                 'md:ml-0':  !$store.sidebar.desktopOpen
             }">

            @include('partials.roles.superadmin.header')

            {{-- Banner Impersonasi — hanya tampil jika Owner sedang meninjau --}}
            @if(!empty($isImpersonating))
                <div class="bg-amber-50 dark:bg-amber-900/20
                            border-b border-amber-200 dark:border-amber-800
                            px-4 md:px-6 py-2.5
                            flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-2 text-sm
                                text-amber-800 dark:text-amber-200">
                        <i class="fas fa-user-shield"></i>
                        <span>
                            Anda melihat sebagai
                            <strong class="font-semibold">{{ $exploreTarget->name ?? 'Superadmin' }}</strong>
                            ({{ ucfirst($exploreRole ?? 'superadmin') }})
                        </span>
                    </div>
                    <a href="{{ route('exit.impersonation') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                              text-xs font-semibold
                              bg-amber-500 hover:bg-amber-600 text-white
                              transition-all duration-200 active:scale-95">
                        <i class="fas fa-arrow-left"></i>
                        Keluar dari Impersonasi
                    </a>
                </div>
            @endif

            <main class="flex-1 p-4 md:p-6">
                @yield('content')
            </main>

            @include('partials.roles.superadmin.footer')
        </div>
    </div>

    @include('partials.roles.superadmin.logout-modal')

    {{-- ================= SIDEBAR STORE — HARUS sebelum Alpine CDN ================= --}}
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('sidebar', {
            desktopOpen: (localStorage.getItem('sidebarDesktopOpen') ?? 'true') === 'true',
            mobileOpen: false,
            isDesktop: window.matchMedia('(min-width: 768px)').matches,

            toggle() {
                this.isDesktop ? this.setDesktop(!this.desktopOpen)
                               : this.setMobile(!this.mobileOpen);
            },
            open()  { this.isDesktop ? this.setDesktop(true)  : this.setMobile(true); },
            close() { this.isDesktop ? this.setDesktop(false) : this.setMobile(false); },

            setDesktop(val) {
                this.desktopOpen = !!val;
                localStorage.setItem('sidebarDesktopOpen', String(this.desktopOpen));
            },
            setMobile(val) { this.mobileOpen = !!val; },
        });

        const store = Alpine.store('sidebar');

        // Resize (debounced) — reset drawer mobile saat pindah ke desktop
        let rt;
        window.addEventListener('resize', () => {
            clearTimeout(rt);
            rt = setTimeout(() => {
                const wasDesktop = store.isDesktop;
                store.isDesktop = window.matchMedia('(min-width: 768px)').matches;
                if (!wasDesktop && store.isDesktop) store.mobileOpen = false;
            }, 100);
        });

        // Keyboard shortcuts global
        window.addEventListener('keydown', (e) => {
            const tag = (document.activeElement?.tagName || '').toUpperCase();
            const typing = ['INPUT','TEXTAREA','SELECT'].includes(tag)
                || document.activeElement?.isContentEditable;
            if (typing) return;

            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                store.toggle();
                return;
            }
            if (e.key === 'Escape' && !store.isDesktop && store.mobileOpen) {
                store.setMobile(false);
            }
        });

        // Auto-tutup drawer mobile saat link diklik
        document.addEventListener('click', (e) => {
            if (store.isDesktop || !store.mobileOpen) return;
            if (e.target.closest('aside a[href]')) store.setMobile(false);
        });
    });
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/theme.js') }}"></script>

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
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard') – UKM UNICODE</title>
        
        <!-- Tailwind & Font Awesome -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            midnight: '#0f172a',
                            deepblue: '#1e1b4b',
                            electric: '#06b6d4',
                            cyan: '#00d4ff',
                            violet: '#8b5cf6',
                            pink: '#ec4899',
                        }
                    }
                }
            }
        </script>
        <style>
            /* Custom Scrollbar untuk Dashboard */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            .dark ::-webkit-scrollbar-thumb { background: #475569; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }
            
            /* Glassmorphism Effect */
            .glass-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
            .dark .glass-card {
                background: rgba(15, 23, 42, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
        @stack('styles')
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 antialiased">
        
        <!-- Wrapper Flex -->
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar Komponen -->
            @include('partials.dashboard.sidebar')
            
            <!-- Main Content Area (Akan menyesuaikan lebar ketika Sidebar di-toggle) -->
            <div id="dashboard-main-content" class="flex-1 flex flex-col h-screen overflow-hidden relative transition-all duration-300 min-w-0">
                
                <!-- Header Khusus Dashboard -->
                @include('partials.dashboard.header')
                
                <!-- Dashboard Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
                    @yield('content')
                </main>
                
                <!-- Footer Khusus Dashboard -->
                @include('partials.dashboard.footer')
            </div>
        </div>

        <!-- Script untuk Toggle Sidebar Mobile & Dark Mode & SHORTCUT Ctrl+B -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // --- Sidebar Elements ---
                const sidebar = document.getElementById('dashboard-sidebar');
                const toggleBtn = document.getElementById('sidebar-toggle'); // Tombol collapse sidebar
                const toggleMobileBtn = document.getElementById('sidebar-toggle-mobile'); // Tombol mobile di header
                let isCollapsed = false; // Mode Desktop (w-64 vs w-16)

                // 1. Fungsi Collapse Sidebar Desktop (Ctrl+B & Tombol)
                function toggleSidebarCollapse() {
                    isCollapsed = !isCollapsed;
                    if (isCollapsed) {
                        sidebar.classList.remove('w-64');
                        sidebar.classList.add('w-16');
                        sidebar.dataset.collapsed = 'true';
                        if(toggleBtn) toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                    } else {
                        sidebar.classList.remove('w-16');
                        sidebar.classList.add('w-64');
                        sidebar.dataset.collapsed = 'false';
                        if(toggleBtn) toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                    }
                }

                // 2. Fungsi Buka/Tutup Mobile
                function toggleMobileSidebar() {
                    const isCurrentlyHidden = sidebar.classList.contains('-translate-x-full');
                    if (isCurrentlyHidden) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                    } else {
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');
                    }
                }

                // 3. Event Listener Tombol
                if(toggleBtn) {
                    toggleBtn.addEventListener('click', toggleSidebarCollapse);
                }
                if(toggleMobileBtn) {
                    toggleMobileBtn.addEventListener('click', toggleMobileSidebar);
                }

                // 4. SHORTCUT KEYBOARD: CTRL + B
                document.addEventListener('keydown', function(e) {
                    // Hanya aktif jika tidak sedang mengetik di input/textarea
                    if (e.ctrlKey && e.key === 'b' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                        e.preventDefault();
                        // Jika layar mobile (<1024px), buka/tutup mobile. Jika desktop, collapse sidebar.
                        if (window.innerWidth < 1024) {
                            toggleMobileSidebar();
                        } else {
                            toggleSidebarCollapse();
                        }
                    }
                });

                // --- Dark Mode Toggle (Sync) ---
                const themeToggle = document.getElementById('dashboard-theme-toggle');
                const themeIcon = document.getElementById('dashboard-theme-icon');
                const html = document.documentElement;
                
                if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    html.classList.add('dark');
                    if(themeIcon) { themeIcon.classList.remove('fa-moon'); themeIcon.classList.add('fa-sun'); }
                }
                
                if(themeToggle) {
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
                }
            });
        </script>

        {{-- ========================================== --}}
        {{-- TAMBAHAN: MODAL KONFIRMASI LOGOUT --}}
        {{-- ========================================== --}}
        <div id="logout-confirm-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 text-red-600 dark:text-red-400 text-3xl">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Konfirmasi Keluar</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin keluar dari akun ini?</p>
                    
                    <div class="flex w-full gap-4">
                        <button onclick="closeLogoutModal()" class="flex-1 py-2.5 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium">
                            Tidak
                        </button>
                        
                        {{-- Form Logout --}}
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-md shadow-red-500/20 transition">
                                Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- JavaScript Modal --}}
        <script>
            function showLogoutModal(event) {
                event.preventDefault();
                const modal = document.getElementById('logout-confirm-modal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                    modal.querySelector('.transform').classList.add('scale-100');
                }, 10);
            }

            function closeLogoutModal() {
                const modal = document.getElementById('logout-confirm-modal');
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-100');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            document.getElementById('logout-confirm-modal').addEventListener('click', function(e) {
                if (e.target === this) { closeLogoutModal(); }
            });
        </script>
        
        @stack('scripts')
    </body>
    </html>
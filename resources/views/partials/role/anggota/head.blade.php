{{-- resources/views/partials/role/anggota/head.blade.php --}}
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>@yield('title', 'Dashboard Anggota – UNICODE')</title>

{{-- Meta --}}
<meta name="description" content="Dashboard anggota UKM Pemrograman UNICODE" />
<link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon" />

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

{{-- Alpine.js untuk dropdown --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

{{-- Tailwind --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    midnight: '#050A30',
                    deepblue: '#0B1F6B',
                    electric: '#2563EB',
                    cyan: '#00D9FF',
                    purple: '#7C3AED',
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    poppins: ['Poppins', 'sans-serif'],
                },
                animation: {
                    'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                    'slide-down': 'slideDown 0.2s ease-out forwards',
                    'slide-up': 'slideUp 0.15s ease-out forwards',
                    'pulse-soft': 'pulse 2s ease-in-out infinite',
                },
                keyframes: {
                    fadeInUp: {
                        '0%': { opacity: '0', transform: 'translateY(20px)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' },
                    },
                    slideDown: {
                        '0%': { opacity: '0', transform: 'translateY(-10px)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' },
                    },
                    slideUp: {
                        '0%': { opacity: '1', transform: 'translateY(0)' },
                        '100%': { opacity: '0', transform: 'translateY(-10px)' },
                    },
                },
            }
        }
    }
</script>

{{-- Custom CSS --}}
<style>
    /* Glassmorphism card */
    .glass-card {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dark .glass-card {
        background: rgba(31, 41, 55, 0.45);
        border-color: rgba(255, 255, 255, 0.05);
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    .dark ::-webkit-scrollbar-thumb { background: #475569; }

    /* Sidebar link active */
    .sidebar-link {
        transition: all 0.25s ease;
        position: relative;
    }
    .sidebar-link.active {
        background: rgba(6, 182, 212, 0.15);
        color: #06b6d4;
        border-right: 3px solid #06b6d4;
    }
    .sidebar-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #06b6d4;
        border-radius: 0 3px 3px 0;
    }
    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateX(2px);
    }
    .dark .sidebar-link.active {
        background: rgba(6, 182, 212, 0.2);
        color: #22d3ee;
        border-right-color: #22d3ee;
    }
    .dark .sidebar-link.active::before {
        background: #22d3ee;
    }

    /* Dark mode text colors - semua teks otomatis menyesuaikan */
    .dark .text-gray-800 { color: #f1f5f9; }
    .dark .text-gray-700 { color: #e2e8f0; }
    .dark .text-gray-600 { color: #cbd5e1; }
    .dark .text-gray-500 { color: #94a3b8; }
    .dark .text-gray-400 { color: #64748b; }
    .dark .border-gray-200 { border-color: #334155; }
    .dark .border-gray-300 { border-color: #475569; }

    /* Transisi untuk margin-left */
    #mainContent {
        transition: margin-left 0.3s ease-in-out;
    }

    /* Sidebar transisi */
    #sidebar {
        transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    /* Saat sidebar collapsed, icon menu di header berputar */
    #sidebarToggleBtn i {
        transition: transform 0.3s ease;
    }

    /* Memastikan konten tidak overflow */
    .max-w-7xl {
        max-width: 80rem;
    }
</style>
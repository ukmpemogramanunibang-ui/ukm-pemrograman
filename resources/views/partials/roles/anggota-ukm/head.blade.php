{{-- resources/views/partials/roles/anggota-ukm/head.blade.php --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', 'Dashboard Anggota – UNICODE')</title>

{{-- Favicon --}}
<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

{{-- Tailwind CSS (CDN) --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    midnight: '#0f172a',
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
            },
        },
    };
</script>

{{-- Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Dark Mode Init (harus sebelum render biar tidak flash) --}}
<script>
    (function() {
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

{{-- Base Styles --}}
<style>
    html, body {
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
    }
    [x-cloak] { display: none !important; }
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.4);
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.7); }
    .dark ::-webkit-scrollbar-thumb { background: rgba(75, 85, 99, 0.6); }
    .dark ::-webkit-scrollbar-thumb:hover { background: rgba(107, 114, 128, 0.8); }
</style>
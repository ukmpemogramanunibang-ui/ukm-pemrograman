{{-- resources/views/partials/roles/superadmin/head.blade.php --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">

<title>@yield('title', 'Superadmin – UNICODE')</title>

<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

{{-- FontAwesome --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous" referrerpolicy="no-referrer">

{{-- Tailwind (CDN — ganti ke build Vite bila perlu) --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    midnight: '#0b1220',
                }
            }
        }
    };
</script>

{{-- Alpine x-cloak + scrollbar halus --}}
<style>
    [x-cloak] { display: none !important; }
    .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: rgba(148,163,184,.35); border-radius: 999px;
    }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(71,85,105,.55); }
</style>

{{-- Dark mode tanpa flash --}}
<script>
    (function () {
        try {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    })();
</script>
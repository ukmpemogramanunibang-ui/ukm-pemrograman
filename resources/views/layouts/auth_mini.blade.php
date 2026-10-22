<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UNICODE – Login')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { midnight: '#050A30', deepblue: '#0B1F6B', electric: '#2563EB', cyan: '#00D9FF' },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-midnight text-white antialiased">

    @include('partials.auth.header')

    <main>
        @yield('content')
    </main>

    @include('partials.auth.footer')

    @stack('scripts')
</body>
</html>

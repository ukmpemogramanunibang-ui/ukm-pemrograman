<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak – UNICODE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full bg-gradient-to-br from-purple-50 via-white to-pink-50 dark:from-gray-900 dark:via-gray-900 dark:to-purple-950
             flex items-center justify-center p-6">
    <div class="max-w-lg w-full text-center">
        <div class="w-24 h-24 mx-auto mb-6 rounded-3xl
                    bg-gradient-to-br from-rose-500 to-red-600
                    flex items-center justify-center shadow-2xl">
            <i class="fas fa-shield-alt text-4xl text-white"></i>
        </div>

        <h1 class="text-6xl font-black text-gray-900 dark:text-white mb-2">403</h1>
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-3">
            Akses Ditolak
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
            {{ $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>

        @if (!empty($reason))
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20
                        border border-amber-200 dark:border-amber-800
                        text-xs text-amber-700 dark:text-amber-300 mb-6">
                <i class="fas fa-info-circle mr-1"></i>
                {{ $reason }}
            </div>
        @endif

        <div class="flex flex-wrap justify-center gap-2">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                      bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                      text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            @auth
                <a href="{{ auth()->user()->getDashboardRoute() }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-purple-600 hover:bg-purple-700 text-white
                          text-sm font-bold shadow-lg shadow-purple-500/20 transition">
                    <i class="fas fa-home"></i> Ke Dashboard Saya
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-purple-600 hover:bg-purple-700 text-white
                          text-sm font-bold shadow-lg shadow-purple-500/20 transition">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Credential – UNICODE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-4 text-white text-center">
            <h1 class="text-2xl font-bold">UNICODE</h1>
            <p class="text-sm opacity-80">UKM Pemrograman</p>
        </div>

        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                @if($user->avatar_url)
                    <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-cyan-500">
                @else
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->nim ?? 'NIM tidak tersedia' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <span class="text-gray-500 dark:text-gray-400">Member Number</span>
                    <p class="font-semibold text-gray-800 dark:text-white">{{ $user->member_number ?? '-' }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <span class="text-gray-500 dark:text-gray-400">Role</span>
                    <p class="font-semibold text-gray-800 dark:text-white">{{ $profile['role'] }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <span class="text-gray-500 dark:text-gray-400">Poin</span>
                    <p class="font-semibold text-gray-800 dark:text-white">{{ $profile['total_points'] }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <span class="text-gray-500 dark:text-gray-400">Rank</span>
                    <p class="font-semibold text-gray-800 dark:text-white">{{ $profile['rank'] }}</p>
                </div>
            </div>

            @if(!empty($profile['jabatans']))
                <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Jabatan Aktif</span>
                    <p class="font-medium text-gray-800 dark:text-white">{{ implode(', ', $profile['jabatans']) }}</p>
                </div>
            @endif

            <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-3">
                <span>Status QR: <span class="text-green-600 dark:text-green-400 font-semibold">✓ AKTIF</span></span>
                <span>Credential: {{ substr($credential->qr_uuid, 0, 8) }}...</span>
            </div>

            <div class="mt-4 flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition text-sm">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
                <a href="{{ route('landing') }}" class="flex-1 text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
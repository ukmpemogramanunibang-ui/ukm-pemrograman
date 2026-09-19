<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi QR – UNICODE UKM Programming</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full p-6 md:p-8 border border-gray-200 dark:border-gray-700">

        {{-- Badge status --}}
        <div class="flex justify-between items-start mb-4">
            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full text-xs font-semibold flex items-center gap-1">
                <i class="fas fa-check-circle"></i> TERVALIDASI
            </span>
            <span class="text-xs text-gray-400">{{ $credential->created_at->format('d M Y') }}</span>
        </div>

        {{-- Profil --}}
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    {{ $user->level->name ?? 'Member' }}
                    @if($user->jabatans->where('pivot.is_primary', true)->first())
                        · {{ $user->jabatans->where('pivot.is_primary', true)->first()->name }}
                    @endif
                </p>
                <p class="text-xs text-gray-400">Member sejak {{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        {{-- Detail informasi --}}
        <div class="grid grid-cols-2 gap-4 mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">NIM</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $user->nim ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Member Number</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $user->member_number ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
                <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                    {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Poin</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ number_format($user->total_points ?? 0) }}</p>
            </div>
        </div>

        {{-- QR Credential info --}}
        <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
            <p><i class="fas fa-qrcode mr-2 text-cyan-500"></i> Credential ID: <span class="font-mono">{{ $credential->qr_uuid }}</span></p>
            <p><i class="fas fa-clock mr-2 text-gray-400"></i> Terakhir discan: {{ $credential->last_used_at ? $credential->last_used_at->diffForHumans() : 'Belum pernah' }}</p>
        </div>

        {{-- Footer --}}
        <div class="mt-6 text-center text-xs text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-4 flex items-center justify-center gap-2">
            <i class="fas fa-shield-alt text-green-500"></i>
            Verifikasi resmi oleh <span class="font-semibold text-gray-600 dark:text-gray-300">UNICODE UKM Programming</span>
        </div>
    </div>

</body>
</html>
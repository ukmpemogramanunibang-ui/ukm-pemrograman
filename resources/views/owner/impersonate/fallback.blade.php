<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mode Jelajah — Halaman Belum Tersedia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.74-3l-7-12a2 2 0 00-3.48 0l-7 12A2 2 0 005 19z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Halaman Belum Tersedia</h1>
                <p class="text-sm text-slate-500">Anda sedang menjelajahi akun <strong>{{ $user->name ?? '-' }}</strong> ({{ $roleSlug }}).</p>
            </div>
        </div>

        <div class="text-sm text-slate-600 mb-6">
            View yang diminta (<code class="px-1.5 py-0.5 bg-slate-100 rounded text-xs">{{ $requestedView ?? '-' }}</code>)
            belum disiapkan untuk mode jelajah. Halaman ini akan menampilkan versi aslinya jika sudah tersedia.
        </div>

        <a href="{{ route('owner.impersonate.dashboard', ['roleSlug' => $roleSlug, 'impersonationToken' => session('impersonation_current.token')]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">
            ← Kembali ke Dashboard Target
        </a>
    </div>
</body>
</html>
@extends('layouts.dashboard')
@section('title', 'Dashboard Pengunjung – UNICODE')
@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex items-center justify-between bg-gradient-to-r from-gray-700 to-gray-900 dark:from-gray-600 dark:to-black p-6 rounded-3xl shadow-xl text-white">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold drop-shadow-md">🌐 Hallo, {{ Auth::user()->name }}!</h1>
            <p class="text-sm md:text-base text-white/80 mt-1">Selamat datang. Anda saat ini login sebagai <strong>Pengunjung (Guest)</strong>.</p>
            <p class="text-xs text-yellow-300 mt-1"><i class="fas fa-info-circle"></i> Beberapa fitur mungkin tidak dapat diakses.</p>
        </div>
        <div class="hidden md:block text-5xl opacity-30"><i class="fas fa-globe"></i></div>
    </div>

    <!-- Guest Widget (Sama seperti Anggota tapi mungkin dikunci) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 backdrop-blur-lg bg-white/30 dark:bg-gray-800/30">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Informasi UKM</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800 dark:text-white">UNICODE</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-gray-400 text-white flex items-center justify-center"><i class="fas fa-info"></i></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Komunitas pemrograman terbaik di kampus.</p>
        </div>

        <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 backdrop-blur-lg bg-white/30 dark:bg-gray-800/30">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kontak Admin</p>
                    <h3 class="text-lg font-bold mt-1 text-gray-800 dark:text-white">Hubungi Kami</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-cyan-500/20 text-cyan-500 flex items-center justify-center"><i class="fas fa-envelope"></i></div>
            </div>
            <div class="mt-2 flex flex-col gap-1 text-[10px] text-gray-500">
                <span><i class="fas fa-phone mr-1"></i> +62 812-3456-7890</span>
                <span><i class="fas fa-envelope mr-1"></i> ukm@example.com</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 backdrop-blur-lg bg-white/30 dark:bg-gray-800/30">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Jelajahi</p>
                    <h3 class="text-lg font-bold mt-1 text-gray-800 dark:text-white">Lihat Prestasi</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-500/20 text-yellow-500 flex items-center justify-center"><i class="fas fa-trophy"></i></div>
            </div>
            <button class="mt-4 w-full py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold rounded-lg hover:opacity-80 transition">Lihat Galeri Prestasi</button>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 backdrop-blur-lg bg-white/30 dark:bg-gray-800/30">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-200/50 dark:border-gray-700/50 pb-2">Profil Pengunjung</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500">Nama:</span> <span class="font-semibold">{{ Auth::user()->name }}</span></div>
            <div><span class="text-gray-500">Email:</span> <span class="font-semibold">{{ Auth::user()->email }}</span></div>
            <div><span class="text-gray-500">Tipe:</span> <span class="font-semibold capitalize">{{ Auth::user()->account_type ?? 'Visitor' }}</span></div>
            <div><span class="text-gray-500">Role:</span> <span class="font-semibold text-gray-500 bg-gray-500/20 px-2 py-0.5 rounded-full inline-block">{{ Auth::user()->roles->pluck('name')->implode(', ') }}</span></div>
        </div>
    </div>
</div>
@endsection
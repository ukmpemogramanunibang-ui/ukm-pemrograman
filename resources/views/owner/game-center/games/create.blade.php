@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-50 to-slate-200 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 py-8">
    <div class="container mx-auto px-4 md:px-6">

        {{-- Header --}}
        <div class="mb-10 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-slate-800 dark:text-white tracking-tight">
                🎮 Pilih Tipe Game
            </h1>
            <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-xl mx-auto">
                Tentukan jenis game yang ingin Anda buat. Pilih salah satu dari dua kategori di bawah ini.
            </p>
        </div>

        {{-- Dua Kartu Utama --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">

            {{-- KARTU KIRI: GAME EXTERNAL --}}
            <div class="group relative bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-lg hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-300 overflow-hidden flex flex-col items-center justify-center p-8 md:p-10 text-center">
                {{-- Dekorasi blur --}}
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl group-hover:bg-cyan-500/20 transition"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition"></div>

                <div class="relative z-10">
                    <div class="text-7xl mb-6 animate-bounce-slow">🔗</div>
                    <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white mb-3">
                        Game External
                    </h2>
                    <p class="text-slate-600 dark:text-slate-400 max-w-xs mx-auto mb-8">
                        Integrasikan game dari luar melalui link, embed, atau API eksternal.
                    </p>
                    <a href="{{ route('owner.game.games.create-form', ['type' => 'external']) }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white rounded-2xl font-bold text-lg shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 transform hover:-translate-y-1 transition-all duration-200">
                        <span>Buat Game External</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- KARTU KANAN: GAME BUATAN SENDIRI (NATIVE) --}}
            <div class="relative bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-lg hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 p-8 md:p-10">
                {{-- Dekorasi blur --}}
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition"></div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl group-hover:bg-purple-500/20 transition"></div>

                <div class="relative z-10">
                    <div class="text-center mb-8">
                        <div class="text-7xl mb-4 animate-bounce-slow">🧩</div>
                        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white">
                            Game Buatan Sendiri
                        </h2>
                        <p class="text-slate-600 dark:text-slate-400 mt-2">
                            Pilih jenis game native yang tersedia di platform.
                        </p>
                    </div>

                    {{-- Dua pilihan game --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Typing --}}
                        <a href="{{ route('owner.game.games.create-form', ['type' => 'native', 'game_type' => 'typing']) }}"
                           class="group relative block bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-slate-200 dark:border-slate-700 p-6 text-center hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">⌨️</div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Typing</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Game mengetik cepat</p>
                            <div class="absolute inset-0 rounded-2xl ring-0 group-hover:ring-2 ring-indigo-500/50 pointer-events-none transition"></div>
                        </a>

                        {{-- Quiz --}}
                        <a href="{{ route('owner.game.games.create-form', ['type' => 'native', 'game_type' => 'quiz']) }}"
                           class="group relative block bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-slate-200 dark:border-slate-700 p-6 text-center hover:border-purple-500 dark:hover:border-purple-500 hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">❓</div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Quiz</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Game kuis interaktif</p>
                            <div class="absolute inset-0 rounded-2xl ring-0 group-hover:ring-2 ring-purple-500/50 pointer-events-none transition"></div>
                        </a>
                    </div>

                    {{-- Catatan kecil --}}
                    <div class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
                        Klik salah satu kartu untuk langsung membuat game.
                    </div>
                </div>
            </div>

        </div>

        {{-- Tombol kembali --}}
        <div class="mt-10 text-center">
            <a href="{{ route('owner.game.games.index') }}"
               class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Kembali ke Daftar Game
            </a>
        </div>

    </div>
</div>
@endsection
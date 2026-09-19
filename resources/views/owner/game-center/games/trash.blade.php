@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- ============================================================ --}}
        {{-- HEADER --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <span>🗑️</span> Tempat Sampah Game
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Game yang telah dihapus akan muncul di sini. Anda dapat memulihkan atau menghapus permanen.
                    Total <strong>{{ $totalTrashed ?? $games->total() }}</strong> game di tempat sampah.
                </p>
            </div>

            <a href="{{ route('owner.game.games.index') }}"
               class="inline-flex items-center gap-2 px-5 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Daftar Game
            </a>
        </div>

        {{-- ============================================================ --}}
        {{-- NOTIFIKASI --}}
        {{-- ============================================================ --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl">
                <p class="text-emerald-700 dark:text-emerald-300 font-bold">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <p class="text-red-700 dark:text-red-300 font-bold">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </p>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- GRID TRASH --}}
        {{-- ============================================================ --}}
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($games as $game)
                @php
                    // Ambil gambar
                    $gameImages = is_array($game->game_images) ? $game->game_images : (json_decode($game->game_images, true) ?? []);
                    $firstImage = !empty($gameImages) ? $gameImages[0] : null;
                    
                    // Warna status
                    $statusColor = 'bg-slate-500/80 text-white';
                    if ($game->status === 'published') $statusColor = 'bg-emerald-500/80 text-white';
                    elseif ($game->status === 'testing') $statusColor = 'bg-amber-500/80 text-white';
                    elseif ($game->status === 'maintenance') $statusColor = 'bg-red-500/80 text-white';
                    elseif ($game->status === 'archived') $statusColor = 'bg-slate-500/80 text-white';
                @endphp

                <div class="group bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl transition-all duration-300 relative">
                    {{-- BANNER --}}
                    <div class="relative h-40 bg-gradient-to-br from-red-600/20 via-amber-600/20 to-yellow-500/20 flex items-center justify-center overflow-hidden rounded-t-3xl">
                        @if($firstImage && Storage::disk('public')->exists($firstImage))
                            <img src="{{ asset('storage/' . $firstImage) }}"
                                alt="{{ $game->name }}"
                                class="w-full h-full object-cover"
                                loading="lazy">
                        @else
                            <span class="text-6xl opacity-50">{{ $game->icon ?? '🎮' }}</span>
                        @endif

                        {{-- BADGE DIHAPUS --}}
                        <span class="absolute top-3 right-3 text-[10px] px-2.5 py-1 rounded-full font-bold shadow-sm bg-red-500/80 text-white">
                            <i class="fas fa-trash mr-1"></i>
                            Dihapus
                        </span>

                        {{-- WAKTU DIHAPUS --}}
                        <span class="absolute bottom-3 right-3 text-[10px] px-2.5 py-1 rounded-full font-bold shadow-sm bg-slate-800/70 text-white">
                            {{ $game->deleted_at->diffForHumans() }}
                        </span>

                        {{-- STATUS GAME --}}
                        <span class="absolute bottom-3 left-3 text-[10px] px-2.5 py-1 rounded-full font-bold shadow-sm {{ $statusColor }}">
                            {{ ucfirst($game->status) }}
                        </span>
                    </div>

                    {{-- KONTEN --}}
                    <div class="p-5">
                        <h2 class="font-black text-lg text-slate-800 dark:text-white truncate">{{ $game->name }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ $game->description ?? 'Tidak ada deskripsi' }}
                        </p>

                        {{-- TYPE & SOURCE --}}
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if($game->source_type === 'native')
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700/50">
                                    <i class="fas fa-code mr-1"></i> UNICODE
                                </span>
                                @if($game->game_type)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700/50">
                                        {{ ucfirst($game->game_type) }}
                                    </span>
                                @endif
                            @else
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700/50">
                                    <i class="fas fa-globe mr-1"></i> External
                                </span>
                            @endif
                        </div>

                        {{-- STATISTIK --}}
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2 text-center">
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Pemain</p>
                                <p class="font-bold text-sm text-slate-800 dark:text-white">{{ number_format($game->player_count ?? 0) }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2 text-center">
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Match</p>
                                <p class="font-bold text-sm text-slate-800 dark:text-white">{{ number_format($game->total_matches ?? 0) }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2 text-center">
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Versi</p>
                                <p class="font-bold text-sm text-slate-800 dark:text-white">{{ $game->version ?? '1.0.0' }}</p>
                            </div>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="flex flex-wrap items-center gap-2 mt-4">
                            {{-- Pulihkan --}}
                            <form action="{{ route('owner.game.games.restore', $game->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-undo"></i> Pulihkan
                                </button>
                            </form>

                            {{-- Hapus Permanen --}}
                            <form action="{{ route('owner.game.games.force', $game->id) }}" method="POST"
                                  onsubmit="return confirm('⚠️ Yakin ingin menghapus permanen game ini? Semua data dan gambar akan hilang dan tidak dapat dikembalikan!')"
                                  class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt"></i> Hapus Permanen
                                </button>
                            </form>
                        </div>

                        {{-- Detail game --}}
                        <a href="{{ route('owner.game.games.show', $game) }}"
                           class="mt-2 block text-center text-xs text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-slate-500 dark:text-slate-400">
                    <div class="text-7xl mb-4 opacity-50">🗑️</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-700 dark:text-slate-300">Tempat Sampah Kosong</h3>
                    <p class="text-sm">Tidak ada game yang dihapus. Semua game aman.</p>
                    <a href="{{ route('owner.game.games.index') }}"
                       class="mt-4 inline-block px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Game
                    </a>
                </div>
            @endforelse
        </div>

        {{-- ============================================================ --}}
        {{-- PAGINASI --}}
        {{-- ============================================================ --}}
        <div class="mt-6">
            {{ $games->withQueryString()->links() }}
        </div>

        {{-- ============================================================ --}}
        {{-- STATISTIK --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Game</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ \App\Models\Game::count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Di Tempat Sampah</p>
                <p class="text-2xl font-black text-red-500">{{ $totalTrashed ?? $games->total() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Game UNICODE</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ \App\Models\Game::where('source_type', 'native')->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Game External</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ \App\Models\Game::where('source_type', 'external')->count() }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
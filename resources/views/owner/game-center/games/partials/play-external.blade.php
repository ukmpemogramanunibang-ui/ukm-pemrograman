{{-- resources/views/owner/game-center/games/partials/play-external.blade.php --}}
<div class="p-8 md:p-12 text-center">
    <div class="text-6xl mb-4">🌐</div>
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Game Eksternal</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-2">
        Anda akan diarahkan ke: <br>
        <span class="font-mono text-indigo-600 dark:text-indigo-400 break-all">{{ $game->url }}</span>
    </p>
    <div class="mt-6 flex flex-wrap justify-center gap-4">
        <a href="{{ $game->url }}" target="_blank" rel="noopener noreferrer"
           class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
            <i class="fas fa-external-link-alt"></i> Buka Game
        </a>
        <a href="{{ route('owner.game.games.show', $game) }}"
           class="px-8 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition hover:bg-slate-300 dark:hover:bg-slate-600">
            Kembali
        </a>
    </div>
    <p class="mt-4 text-xs text-slate-400 dark:text-slate-500">
        Jika tidak terbuka, salin URL dan buka di tab baru.
    </p>
</div>
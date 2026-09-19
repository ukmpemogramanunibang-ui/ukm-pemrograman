{{-- resources/views/owner/game-center/games/partials/play-custom.blade.php --}}
<div class="p-4 md:p-8 text-center py-16">
    <div class="text-6xl mb-4">✨</div>
    <h2 class="text-2xl font-bold text-slate-700 dark:text-slate-300">Game Custom</h2>
    <p class="text-slate-500 mt-2">Pengaturan khusus untuk game ini akan muncul di sini.</p>
    <pre class="mt-4 text-left text-xs bg-slate-100 dark:bg-slate-800 p-4 rounded-xl overflow-auto max-h-60">{{ json_encode($gameData, JSON_PRETTY_PRINT) }}</pre>
</div>
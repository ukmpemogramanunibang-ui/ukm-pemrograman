<div class="text-center py-12">
    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4 block"></i>
    <h3 class="text-xl font-bold text-red-700 dark:text-red-300">Gagal Memuat Game</h3>
    <p class="text-slate-600 dark:text-slate-400 mt-2">{{ $message ?? 'Terjadi kesalahan yang tidak diketahui.' }}</p>
    @if(isset($details))
        <pre class="mt-4 text-xs text-left bg-red-50 dark:bg-red-900/20 p-4 rounded-lg overflow-auto max-h-60 text-red-700 dark:text-red-300">{{ $details }}</pre>
    @endif
    <a href="{{ route('owner.game.games.index') }}" class="mt-6 inline-block px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Game
    </a>
</div>
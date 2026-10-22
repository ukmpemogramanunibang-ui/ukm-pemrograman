@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $game->name }}</h1>
                <p class="text-sm text-slate-500">{{ ucfirst($gameConfig['game_type'] ?? 'Game') }}</p>
            </div>
            <a href="{{ route('owner.game.games.show', $game) }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-xl">← Keluar</a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-6 text-center">
            <p class="text-slate-500">Game ini belum memiliki tampilan khusus. Lihat konfigurasi di bawah.</p>
            <pre class="text-left bg-slate-100 dark:bg-slate-800 p-4 rounded-xl mt-4 overflow-auto">{{ json_encode($gameData, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
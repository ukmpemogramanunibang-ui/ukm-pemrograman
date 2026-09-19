@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-5xl">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('owner.game.games.show', $game) }}" class="text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">🧪 Testing Mode</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Game: <strong>{{ $game->name }}</strong> 
                        <span class="mx-2">•</span> 
                        Tipe: <span class="capitalize">{{ $game->game_type }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-3 py-1 rounded-full font-mono">
                    Token: {{ substr($token, 0, 12) }}...
                </span>
                @if($game->status !== 'testing')
                    <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-3 py-1 rounded-full">
                        ⚠️ Status: {{ ucfirst($game->status) }}
                    </span>
                @endif
            </div>
        </div>

        {{-- KONTEN TESTING --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            @if($game->source_type === 'native')
                @if($game->game_type === 'typing')
                    @include('owner.game-center.partials.testing-typing', ['game' => $game, 'gameData' => $gameData])
                @elseif(in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding']))
                    @include('owner.game-center.partials.testing-quiz', ['game' => $game, 'gameData' => $gameData])
                @else
                    <div class="text-center py-16">
                        <div class="text-6xl mb-4">🔬</div>
                        <h3 class="text-xl font-bold text-slate-700 dark:text-slate-300">Mode Testing untuk "{{ ucfirst($game->game_type) }}"</h3>
                        <p class="text-slate-500 mt-2">Halaman testing untuk tipe game ini sedang dalam pengembangan.</p>
                        <p class="text-sm text-slate-400 mt-1">Game ID: {{ $game->id }} | Tipe: {{ $game->game_type }}</p>
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">🌐</div>
                    <h3 class="text-xl font-bold text-slate-700 dark:text-slate-300">Game External</h3>
                    <p class="text-slate-500 mt-2">Game ini adalah game eksternal. Testing hanya untuk game native UNICODE.</p>
                    <a href="{{ $game->url }}" target="_blank" class="mt-4 inline-block px-6 py-2 bg-cyan-600 text-white rounded-xl font-bold hover:bg-cyan-700 transition">
                        <i class="fas fa-external-link-alt mr-2"></i> Buka Game
                    </a>
                </div>
            @endif
        </div>

        {{-- INFORMASI TAMBAHAN --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
                <p class="text-slate-500 dark:text-slate-400">Game ID</p>
                <p class="font-bold text-slate-800 dark:text-white">{{ $game->id }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
                <p class="text-slate-500 dark:text-slate-400">Versi</p>
                <p class="font-bold text-slate-800 dark:text-white">{{ $game->version ?? '1.0.0' }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
                <p class="text-slate-500 dark:text-slate-400">Status</p>
                <p class="font-bold text-slate-800 dark:text-white">
                    <span class="px-2 py-0.5 rounded-full text-xs
                        {{ $game->status === 'published' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 
                           ($game->status === 'testing' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300' : 
                           'bg-slate-500/20 text-slate-700 dark:text-slate-300') }}">
                        {{ ucfirst($game->status) }}
                    </span>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
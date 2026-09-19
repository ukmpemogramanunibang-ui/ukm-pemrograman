@extends('layouts.owner')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <span class="text-4xl">{{ $game->icon ?? '🎮' }}</span>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white">{{ $game->name }}</h1>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">{{ ucfirst($game->game_type) }}</span>
                        @if($game->difficulty)
                        <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full capitalize">{{ $game->difficulty }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('owner.game.games.show', $game) }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div><span class="text-xs text-slate-500 uppercase tracking-wider">Pemain</span><p class="font-bold">{{ auth()->user()->name }}</p></div>
                <div><span class="text-xs text-slate-500 uppercase tracking-wider">Role</span><p class="font-bold">{{ auth()->user()->level->name ?? 'Anggota' }}</p></div>
                <div><span class="text-xs text-slate-500 uppercase tracking-wider">Token</span><p class="font-mono text-sm break-all">{{ $token }}</p></div>
                <div><span class="text-xs text-slate-500 uppercase tracking-wider">Status</span><p><span class="px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">{{ ucfirst($game->status) }}</span></p></div>
                @if($game->description)<div><span class="text-xs text-slate-500 uppercase tracking-wider">Deskripsi</span><p class="text-sm">{{ $game->description }}</p></div>@endif
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4">
                <h3 class="font-bold mb-2">📊 Data Game</h3>
                <pre class="text-xs bg-white dark:bg-slate-900 p-3 rounded-lg overflow-auto max-h-60">{{ json_encode($gameData, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('owner.game.games.gameplay', ['game' => $game->id, 'token' => $token]) }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/20">
                <i class="fas fa-play mr-2"></i> Mulai Game
            </a>
            <a href="{{ route('owner.game.games.diagnostic', $game) }}" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition">
                <i class="fas fa-flask mr-2"></i> Diagnostic
            </a>
        </div>
    </div>
</div>
@endsection
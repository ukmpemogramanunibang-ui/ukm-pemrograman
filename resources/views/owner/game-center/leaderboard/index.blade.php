@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        <div class="rounded-3xl bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 text-white p-6 md:p-8">
            <span class="text-xs uppercase tracking-widest text-white/70">Ranking System</span>
            <h1 class="text-3xl md:text-4xl font-black mt-2">Leaderboard UNICODE</h1>
            <p class="mt-2 text-white/75">
                Ranking pemain berdasarkan performa, rating, XP, kemenangan, challenge, dan kompetisi.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('owner.game.leaderboard.index', ['period' => 'global']) }}" class="px-5 py-3 rounded-xl {{ $period == 'global' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }} font-bold">
                Global
            </a>
            <a href="{{ route('owner.game.leaderboard.index', ['period' => 'weekly']) }}" class="px-5 py-3 rounded-xl {{ $period == 'weekly' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }} font-bold">
                Mingguan
            </a>
            <a href="{{ route('owner.game.leaderboard.index', ['period' => 'monthly']) }}" class="px-5 py-3 rounded-xl {{ $period == 'monthly' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }} font-bold">
                Bulanan
            </a>
            <a href="{{ route('owner.game.leaderboard.index', ['period' => 'yearly']) }}" class="px-5 py-3 rounded-xl {{ $period == 'yearly' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800' }} font-bold">
                Tahunan
            </a>
        </div>

        @if($leaderboard && $leaderboard->entries->count())
        {{-- TOP 3 --}}
        <div class="grid md:grid-cols-3 gap-5">
            @php
                $top3 = $leaderboard->entries->sortBy('rank')->take(3);
                $medals = ['🥇', '🥈', '🥉'];
                $rankIndex = 0;
            @endphp

            @foreach($top3 as $entry)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 text-center">
                <div class="text-5xl">{{ $medals[$rankIndex++] }}</div>
                <div class="w-20 h-20 mx-auto mt-4 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-white text-2xl font-black">
                    {{ substr($entry->player->name ?? '?', 0, 1) }}
                </div>
                <h2 class="font-black text-lg mt-4">{{ $entry->player->name ?? 'Unknown' }}</h2>
                <p class="text-sm text-slate-500">Rank {{ $entry->rank }}</p>
                <div class="text-3xl font-black mt-3">{{ number_format($entry->score ?? 0) }}</div>
                <p class="text-xs text-slate-500">Skor Terbaik</p>
                @php
                    $stats = $entry->player->gameStats()->where('game_id', $leaderboard->game_id)->first();
                @endphp
                <p class="text-sm text-slate-500 mt-1">Main: {{ $stats->total_plays ?? 0 }} kali</p>
            </div>
            @endforeach
        </div>

        {{-- TABLE --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                <h2 class="font-black text-lg">Top Players</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="text-left px-5 py-4">#</th>
                            <th class="text-left px-5 py-4">Pemain</th>
                            <th class="text-left px-5 py-4">Skor Terbaik</th>
                            <th class="text-left px-5 py-4">Total Main</th>
                            <th class="text-left px-5 py-4">Rata-rata</th>
                            <th class="text-left px-5 py-4">Terakhir Main</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard->entries->sortBy('rank') as $entry)
                        @php
                            $stats = $entry->player->gameStats()->where('game_id', $leaderboard->game_id)->first();
                        @endphp
                        <tr class="border-t border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-4 font-black">#{{ $entry->rank }}</td>
                            <td class="px-5 py-4 font-bold">{{ $entry->player->name ?? 'Unknown' }}</td>
                            <td class="px-5 py-4 font-bold text-emerald-600">{{ number_format($entry->score ?? 0) }}</td>
                            <td class="px-5 py-4">{{ $stats->total_plays ?? 0 }} kali</td>
                            <td class="px-5 py-4">{{ number_format($stats->avg_score ?? 0, 1) }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $stats->last_played_at ? $stats->last_played_at->diffForHumans() : 'Belum pernah' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="text-6xl mb-4">📊</div>
            <p class="text-slate-500">Belum ada data leaderboard. Mainkan game terlebih dahulu!</p>
            <form action="{{ route('owner.game.leaderboard.rebuild') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                    Rebuild Leaderboard
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
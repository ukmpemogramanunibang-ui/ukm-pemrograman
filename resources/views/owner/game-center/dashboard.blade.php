@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-white">

    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- ============================================================ --}}
        {{-- HERO --}}
        {{-- ============================================================ --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-cyan-500 text-white shadow-2xl">
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-20 w-80 h-80 bg-cyan-300/10 rounded-full blur-3xl"></div>

            <div class="relative p-6 md:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 border border-white/20 text-xs font-semibold mb-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                            GAME CENTER ONLINE
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tight">UNICODE Game Center</h1>
                        <p class="mt-3 text-white/80 max-w-2xl">
                            Pusat permainan, challenge, kompetisi, ranking, reward, matchmaking, dan statistik pemain dalam satu ekosistem.
                        </p>
                    </div>

                    {{-- STATS CEPAT --}}
                    <div class="grid grid-cols-2 gap-3 min-w-[260px]">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4 backdrop-blur-xl text-center">
                            <div class="text-2xl font-black">{{ $totalGames ?? 0 }}</div>
                            <div class="text-xs text-white/70">Total Game</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4 backdrop-blur-xl text-center">
                            <div class="text-2xl font-black">{{ number_format($activePlayers ?? 0) }}</div>
                            <div class="text-xs text-white/70">Pemain Aktif</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4 backdrop-blur-xl text-center">
                            <div class="text-2xl font-black">{{ $activeChallenges ?? 0 }}</div>
                            <div class="text-xs text-white/70">Challenge Aktif</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4 backdrop-blur-xl text-center">
                            <div class="text-2xl font-black">{{ $activeCompetitions ?? 0 }}</div>
                            <div class="text-xs text-white/70">Kompetisi Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SYSTEM STATUS – REAL DATA --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Matchmaking Status --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-slate-500">Matchmaking</p>
                        <h3 class="text-2xl font-black mt-1">{{ $matchmakingStatus ?? 'Tidak Aktif' }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                {{ ($matchmakingColor ?? 'amber') === 'emerald' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                        {{ ($matchmakingColor ?? 'amber') === 'emerald' ? '✓' : '⏳' }}
                    </div>
                </div>
            </div>

            {{-- Game Server Status --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-slate-500">Game Server</p>
                        <h3 class="text-2xl font-black mt-1">{{ $serverStatus ?? 'Online' }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                {{ ($serverColor ?? 'cyan') === 'cyan' ? 'bg-cyan-500/10 text-cyan-500' : 'bg-red-500/10 text-red-500' }}">
                        {{ ($serverColor ?? 'cyan') === 'cyan' ? '⚡' : '🔧' }}
                    </div>
                </div>
            </div>

            {{-- Matches Hari Ini --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-slate-500">Matches Hari Ini</p>
                        <h3 class="text-2xl font-black mt-1">{{ $matchesToday ?? 0 }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">🎮</div>
                </div>
            </div>

            {{-- Reward Dibagikan --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-slate-500">Total Reward</p>
                        <h3 class="text-2xl font-black mt-1">{{ $totalRewardFormatted ?? 'Rp 0' }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">🏆</div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STATISTIK LANJUTAN – Lebih Lengkap --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Total Pemain --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-xl">👥</div>
                    <div>
                        <p class="text-xs text-slate-500">Total Pemain</p>
                        <h4 class="text-xl font-black">{{ number_format($totalPlayers ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            {{-- Total Challenge --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-xl">⚔️</div>
                    <div>
                        <p class="text-xs text-slate-500">Total Challenge</p>
                        <h4 class="text-xl font-black">{{ number_format($totalChallenges ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            {{-- Total Kompetisi --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center text-xl">🏆</div>
                    <div>
                        <p class="text-xs text-slate-500">Total Kompetisi</p>
                        <h4 class="text-xl font-black">{{ number_format($totalCompetitions ?? 0) }}</h4>
                    </div>
                </div>
            </div>

            {{-- Total Pertandingan --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-xl">🎮</div>
                    <div>
                        <p class="text-xs text-slate-500">Total Pertandingan</p>
                        <h4 class="text-xl font-black">{{ number_format($totalMatches ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- STATISTIK PEMAIN & RANK --}}
        {{-- ============================================================ --}}
        <div class="grid md:grid-cols-2 gap-4">
            {{-- Rata-rata Win Rate --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Rata-rata Win Rate</p>
                        <h4 class="text-2xl font-black">{{ $averageWinRateFormatted ?? '0%' }}</h4>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl">📈</div>
                </div>
                <div class="mt-3 w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600" style="width: {{ min($averageWinRate ?? 0, 100) }}%"></div>
                </div>
            </div>

            {{-- Pemain Terbaik --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500">🏅 Pemain Terbaik</p>
                        <h4 class="text-lg font-black">{{ $topPlayerName ?? 'Belum ada' }}</h4>
                        <div class="flex gap-3 mt-1 text-sm">
                            <span class="text-slate-500">Rating: <strong>{{ $topPlayerRating ?? '-' }}</strong></span>
                            <span class="text-slate-500">Rank: <strong>{{ $topPlayerRank ?? '-' }}</strong></span>
                        </div>
                    </div>
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-3xl shadow-lg">
                        👑
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- QUICK ACTION --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-black">Quick Control</h2>
                    <p class="text-sm text-slate-500">Aksi utama Game Center</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('owner.game.games.create') }}" class="group p-5 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 hover:scale-[1.02] transition">
                    <div class="text-3xl mb-3">🎮</div>
                    <h3 class="font-bold">Tambah Game</h3>
                    <p class="text-xs text-slate-500 mt-1">Buat game baru</p>
                </a>
                <a href="{{ route('owner.game.challenges.create') }}" class="group p-5 rounded-2xl bg-purple-50 dark:bg-purple-500/10 border border-purple-100 dark:border-purple-500/20 hover:scale-[1.02] transition">
                    <div class="text-3xl mb-3">⚔️</div>
                    <h3 class="font-bold">Buat Challenge</h3>
                    <p class="text-xs text-slate-500 mt-1">Challenge pemain</p>
                </a>
                <a href="{{ route('owner.game.competitions.create') }}" class="group p-5 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-100 dark:border-cyan-500/20 hover:scale-[1.02] transition">
                    <div class="text-3xl mb-3">🏆</div>
                    <h3 class="font-bold">Kompetisi</h3>
                    <p class="text-xs text-slate-500 mt-1">Buat turnamen</p>
                </a>
                <a href="#" class="group p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 hover:scale-[1.02] transition">
                    <div class="text-3xl mb-3">📊</div>
                    <h3 class="font-bold">Analytics</h3>
                    <p class="text-xs text-slate-500 mt-1">Analisis permainan</p>
                </a>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- MAIN GRID – GAME POPULER & LIVE ACTIVITY --}}
        {{-- ============================================================ --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- GAME POPULER --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-black">Game Populer</h2>
                        <p class="text-sm text-slate-500">Game yang paling banyak dimainkan</p>
                    </div>
                    <a href="{{ route('owner.game.games.index') }}" class="text-sm font-semibold text-indigo-500 hover:underline">Lihat semua</a>
                </div>
                @if(isset($popularGames) && $popularGames->count())
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($popularGames as $game)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-4 hover:border-indigo-500/40 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-cyan-500/20 flex items-center justify-center text-3xl">
                                {{ $game->icon ?? '🎮' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold truncate">{{ $game->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $game->sessions_count ?? 0 }} pemain</p>
                            </div>
                            <span class="text-[10px] px-2 py-1 rounded-full {{ ($game->status ?? 'draft') == 'published' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                {{ ucfirst($game->status ?? 'draft') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-slate-500">
                    <p>Belum ada data game populer.</p>
                </div>
                @endif
            </div>

            {{-- LIVE ACTIVITY --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-xl font-black">Live Activity</h2>
                <p class="text-sm text-slate-500 mb-5">Aktivitas pemain saat ini</p>
                @if(isset($recentActivities) && $recentActivities->count())
                <div class="space-y-5">
                    @foreach($recentActivities as $activity)
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-white text-xs font-bold">
                           {{ substr($activity->player ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm"><span class="font-bold">{{ $activity->player ?? 'User' }}</span> {{ $activity->action ?? 'melakukan aktivitas' }}</p>
<p class="text-xs text-slate-500 mt-1">{{ $activity->time ?? 'baru saja' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-slate-500">
                    <p>Belum ada aktivitas terbaru.</p>
                </div>
                @endif
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- FOOTER / STATISTIK TAMBAHAN --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-xs text-slate-500">Total Game</p>
                    <p class="text-2xl font-black">{{ $totalGames ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Total Pemain</p>
                    <p class="text-2xl font-black">{{ number_format($totalPlayers ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Total Match</p>
                    <p class="text-2xl font-black">{{ number_format($totalMatches ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Match Selesai</p>
                    <p class="text-2xl font-black">{{ number_format($finishedMatches ?? 0) }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
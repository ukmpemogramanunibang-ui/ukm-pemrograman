@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-5xl">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('owner.game.games.show', $game) }}" 
                       class="text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                        <span>🧪</span> Game Diagnostic Center
                    </h1>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Automated health check & runtime verification untuk <strong>{{ $game->name }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1.5 rounded-full font-mono">
                    Token: {{ $token }}
                </span>
                <a href="{{ route('owner.game.games.diagnostic.rescan', $game) }}" 
                   class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i> Scan Ulang
                </a>
            </div>
        </div>

        {{-- OVERALL STATUS CARD --}}
        @php
            $overallColor = match($result->overall) {
                'passed' => 'emerald',
                'warning' => 'amber',
                'failed' => 'red',
                'critical' => 'rose',
                default => 'gray',
            };
            $overallText = match($result->overall) {
                'passed' => 'GAME READY ✅',
                'warning' => 'GAME WITH WARNINGS ⚠️',
                'failed' => 'GAME NOT READY ❌',
                'critical' => 'CRITICAL ERROR 🔴',
                default => 'PENDING ⏳',
            };
            $overallIcon = match($result->overall) {
                'passed' => '✅',
                'warning' => '⚠️',
                'failed' => '❌',
                'critical' => '🔴',
                default => '⏳',
            };
            $isReady = $result->isReady();
        @endphp

        <div class="rounded-3xl border-2 {{ $isReady ? 'border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/20' : 'border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950/20' }} p-8 shadow-sm mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="text-5xl">{{ $overallIcon }}</div>
                    <div>
                        <h2 class="text-2xl font-black {{ $isReady ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300' }}">
                            {{ $overallText }}
                        </h2>
                        <div class="flex items-center gap-4 mt-1 text-sm text-slate-600 dark:text-slate-400">
                            <span><strong>{{ $result->summary['passed'] }}</strong> Passed</span>
                            <span class="text-amber-500"><strong>{{ $result->summary['warning'] }}</strong> Warning</span>
                            <span class="text-red-500"><strong>{{ $result->summary['failed'] }}</strong> Failed</span>
                            @if($result->summary['critical'] > 0)
                                <span class="text-rose-500"><strong>{{ $result->summary['critical'] }}</strong> Critical</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($isReady)
                        <a href="{{ route('owner.game.games.gameplay', ['game' => $game->id, 'token' => $token]) }}"
                           class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold transition shadow-lg shadow-emerald-500/30 flex items-center gap-2">
                            <i class="fas fa-play"></i> Mainkan Test
                        </a>
                    @else
                        <button disabled class="px-6 py-3 bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-xl font-bold cursor-not-allowed flex items-center gap-2">
                            <i class="fas fa-play"></i> Mainkan Test (Disabled)
                        </button>
                    @endif
                    <a href="{{ route('owner.game.games.edit', $game) }}"
                       class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                        <i class="fas fa-cog"></i> Edit Game
                    </a>
                    <a href="{{ route('owner.game.games.diagnostic.report', $game) }}"
                       target="_blank"
                       class="px-5 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition flex items-center gap-2">
                        <i class="fas fa-download"></i> Report
                    </a>
                </div>
            </div>
        </div>

        {{-- PROGRESS BAR (opsional) --}}
        @php
            $total = $result->summary['passed'] + $result->summary['warning'] + $result->summary['failed'] + $result->summary['critical'];
            $percent = $total > 0 ? round(($result->summary['passed'] / $total) * 100) : 0;
        @endphp
        <div class="mb-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-500">Health Score</span>
                <span class="font-bold {{ $percent >= 80 ? 'text-emerald-500' : ($percent >= 50 ? 'text-amber-500' : 'text-red-500') }}">
                    {{ $percent }}%
                </span>
            </div>
            <div class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700 
                    {{ $percent >= 80 ? 'bg-emerald-500' : ($percent >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                    style="width: {{ $percent }}%">
                </div>
            </div>
        </div>

        {{-- GRID CARD CHECKS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($result->checks as $check)
                @php
                    $statusClass = match($check['status']) {
                        'passed' => 'border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/20',
                        'warning' => 'border-amber-300 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/20',
                        'failed' => 'border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950/20',
                        'critical' => 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/20',
                        default => 'border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800',
                    };
                    $icon = match($check['status']) {
                        'passed' => '✅',
                        'warning' => '⚠️',
                        'failed' => '❌',
                        'critical' => '🔴',
                        default => '⏳',
                    };
                    $titleColor = match($check['status']) {
                        'passed' => 'text-emerald-700 dark:text-emerald-300',
                        'warning' => 'text-amber-700 dark:text-amber-300',
                        'failed' => 'text-red-700 dark:text-red-300',
                        'critical' => 'text-rose-700 dark:text-rose-300',
                        default => 'text-slate-700 dark:text-slate-300',
                    };
                @endphp
                <div class="rounded-2xl border-2 {{ $statusClass }} p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl">{{ $icon }}</span>
                                <h3 class="font-bold {{ $titleColor }}">{{ $check['name'] }}</h3>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $check['message'] ?? '-' }}</p>
                            @if(isset($check['details']) && !empty($check['details']))
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 bg-white/50 dark:bg-black/20 rounded-lg p-2">
                                    <pre class="whitespace-pre-wrap">{{ json_encode($check['details'], JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                        <span class="text-xs font-bold uppercase {{ $titleColor }}">
                            {{ ucfirst($check['status']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- DETAIL GAME – INFORMASI LENGKAP --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm mb-6">
            <h2 class="text-xl font-black text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span>📋</span> Detail Game
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Nama Game</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['name'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Slug</p>
                    <p class="font-mono text-slate-800 dark:text-white">{{ $result->gameDetails['slug'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Icon</p>
                    <p class="text-2xl">{{ $result->gameDetails['icon'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Versi</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['version'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Tipe Sumber</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold 
                        {{ $result->gameDetails['source_type'] === 'native' ? 'bg-purple-500/20 text-purple-700 dark:text-purple-300' : 'bg-cyan-500/20 text-cyan-700 dark:text-cyan-300' }}">
                        {{ $result->gameDetails['source_type'] === 'native' ? 'UNICODE (Native)' : 'External' }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Jenis Game</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">
                        {{ ucfirst($result->gameDetails['game_type']) }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                        {{ $result->gameDetails['status'] === 'published' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 
                           ($result->gameDetails['status'] === 'testing' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300' : 
                           'bg-slate-500/20 text-slate-700 dark:text-slate-300') }}">
                        {{ ucfirst($result->gameDetails['status']) }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Difficulty</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-700 dark:text-yellow-300">
                        {{ ucfirst($result->gameDetails['difficulty']) }}
                    </span>
                </div>
                <div class="md:col-span-2">
                    <p class="text-slate-500 dark:text-slate-400">Deskripsi</p>
                    <p class="text-slate-800 dark:text-white">{{ $result->gameDetails['description'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Developer</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['developer'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Jumlah Pemain</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ number_format($result->gameDetails['player_count']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Total Match</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ number_format($result->gameDetails['total_matches']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Total Kunjungan</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ number_format($result->gameDetails['visits']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Multiplayer</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                        {{ $result->gameDetails['support_matchmaking'] ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-500/20 text-slate-700 dark:text-slate-300' }}">
                        {{ $result->gameDetails['support_matchmaking'] ? '✅ Aktif' : '❌ Tidak Aktif' }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Rating System</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                        {{ $result->gameDetails['support_rating'] ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-500/20 text-slate-700 dark:text-slate-300' }}">
                        {{ $result->gameDetails['support_rating'] ? '✅ Aktif' : '❌ Tidak Aktif' }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Challenge System</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                        {{ $result->gameDetails['support_challenge'] ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-500/20 text-slate-700 dark:text-slate-300' }}">
                        {{ $result->gameDetails['support_challenge'] ? '✅ Aktif' : '❌ Tidak Aktif' }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Leaderboard</p>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                        {{ $result->gameDetails['show_in_leaderboard'] ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-slate-500/20 text-slate-700 dark:text-slate-300' }}">
                        {{ $result->gameDetails['show_in_leaderboard'] ? '✅ Aktif' : '❌ Tidak Aktif' }}
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Total Challenge</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ number_format($result->gameDetails['total_challenges']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Total Kompetisi</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ number_format($result->gameDetails['total_competitions']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Total Sesi</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ number_format($result->gameDetails['total_sessions']) }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Terakhir Dimainkan</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['last_played_at'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Dibuat</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['created_at'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Diperbarui</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['updated_at'] }}</p>
                </div>
                @if($result->gameDetails['source_type'] === 'external')
                <div class="md:col-span-2">
                    <p class="text-slate-500 dark:text-slate-400">URL External</p>
                    <a href="{{ $result->gameDetails['url'] }}" target="_blank" 
                       class="text-indigo-600 dark:text-indigo-400 hover:underline break-all">
                        {{ $result->gameDetails['url'] }}
                    </a>
                    <span class="ml-2 text-xs bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 px-2 py-0.5 rounded-full">
                        Integrasi: {{ ucfirst($result->gameDetails['integration_level']) }}
                    </span>
                </div>
                @endif
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Jumlah Gambar</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['image_count'] }}</p>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Jumlah Soal</p>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $result->gameDetails['question_count'] }}</p>
                </div>
            </div>
        </div>

        {{-- RUNTIME TEST & SIMULATION --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Runtime Test --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <span>⚡</span> Runtime Test
                </h3>
                <div class="space-y-2">
                    @foreach($result->runtime as $step)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-lg">
                                {{ $step['status'] === 'passed' ? '✅' : ($step['status'] === 'failed' ? '❌' : '⏳') }}
                            </span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $step['step'] }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 ml-auto">{{ $step['message'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Player Simulation --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <span>🤖</span> Player Simulation
                </h3>
                <div class="space-y-2">
                    @foreach($result->simulation as $step)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-lg">
                                {{ $step['status'] === 'passed' ? '✅' : ($step['status'] === 'failed' ? '❌' : '⏳') }}
                            </span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $step['step'] }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 ml-auto">{{ $step['message'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
            Diagnostic run at {{ now()->format('d M Y H:i:s') }} • Session: {{ $result->sessionId }}
        </div>

    </div>
</div>
@endsection
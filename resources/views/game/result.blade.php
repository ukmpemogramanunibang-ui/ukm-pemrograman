@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 py-6">
    <div class="container mx-auto px-4 md:px-6 max-w-7xl">

        {{-- ============================================================ --}}
        {{-- HEADER --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl p-4 md:p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-3xl shadow-lg shadow-indigo-500/20">
                        🏆
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 dark:text-white">Hasil Permainan</h1>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                                {{ $game->name }}
                            </span>
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full">
                                Peringkat #{{ $rank }} dari {{ $totalPlayers }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Pemain</p>
                        <p class="font-bold text-sm text-slate-800 dark:text-white">{{ $session->player->name ?? 'Guest' }}</p>
                        <p class="text-xs text-slate-500">{{ $session->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ substr($session->player->name ?? 'G', 0, 1) }}
                    </div>
                    <a href="{{ route('owner.game.games.show', $game) }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition text-sm">
                        ✕
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- DASHBOARD STATISTIK UTAMA --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">WPM Kasar</p>
                <p class="text-2xl font-black text-cyan-600 dark:text-cyan-400">{{ $metrics['gross_wpm'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">WPM Bersih</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $metrics['wpm'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Akurasi</p>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $metrics['accuracy'] ?? 0 }}%</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">⌫ Backspace</p>
                <p class="text-2xl font-black text-orange-600 dark:text-orange-400">{{ $metrics['backspace'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Kata</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $metrics['total_words'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Durasi</p>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $metrics['duration_used'] ?? 0 }}s</p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RITME & KONSISTENSI --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                    <i class="fas fa-chart-line text-indigo-500"></i>
                    Ritme & Konsistensi
                    <span class="text-sm font-normal text-slate-500 dark:text-slate-400">
                        {{ $rhythmData['consistency_label'] }}
                    </span>
                </h2>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-6">
                    {{-- Konsistensi Progress Bar --}}
                    <div class="flex-1 min-w-[200px]">
                        <div class="flex justify-between text-sm text-slate-500 mb-1">
                            <span>Konsistensi</span>
                            <span>{{ $rhythmData['consistency'] }}%</span>
                        </div>
                        <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-red-500 via-amber-500 to-emerald-500 rounded-full transition-all duration-1000"
                                 style="width: {{ $rhythmData['consistency'] }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $rhythmData['consistency'] >= 80 ? '✅ Ritme sangat rata — pertahankan!' : ($rhythmData['consistency'] >= 60 ? '⚡ Ritme cukup rata, tingkatkan lagi.' : '📉 Ritme kurang rata — buat batang seragam.') }}
                        </p>
                    </div>

                    {{-- Statistik tombol --}}
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl px-4 py-2">
                            <span class="text-slate-500">Total Tombol</span>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $rhythmData['total_keys'] }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl px-4 py-2">
                            <span class="text-slate-500">Tombol Terlambat</span>
                            <p class="font-bold text-red-600 dark:text-red-400">{{ $rhythmData['total_slow'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TOMBOL TERLAMBAT & PASANGAN TERLAMBAT --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Tombol Terlambat --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-clock text-red-500"></i>
                        Tombol Terlambat
                        <span class="text-xs font-normal text-slate-500">(delay terbesar)</span>
                    </h3>
                </div>
                <div class="p-5">
                    @if(!empty($rhythmData['slow_keys']))
                        <div class="space-y-2">
                            @foreach($rhythmData['slow_keys'] as $item)
                                <div class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-lg transition">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-mono font-bold text-sm flex items-center justify-center">
                                            {{ $item['key'] }}
                                        </span>
                                        <span class="text-sm text-slate-600 dark:text-slate-400">delay</span>
                                    </div>
                                    <span class="font-mono font-bold text-red-600 dark:text-red-400">{{ $item['delay'] }}ms</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 text-center py-4">✨ Tidak ada tombol terlambat — sempurna!</p>
                    @endif
                </div>
            </div>

            {{-- Pasangan Terlambat --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-link text-purple-500"></i>
                        Pasangan Terlambat
                        <span class="text-xs font-normal text-slate-500">(kombinasi tombol)</span>
                    </h3>
                </div>
                <div class="p-5">
                    @if(!empty($rhythmData['late_pairs']))
                        <div class="space-y-2">
                            @foreach($rhythmData['late_pairs'] as $item)
                                <div class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-lg transition">
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-mono font-bold text-sm">
                                            {{ $item['pair'] }}
                                        </span>
                                    </div>
                                    <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $item['delay'] }}ms</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 text-center py-4">✨ Tidak ada pasangan terlambat — luar biasa!</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- KATA YANG TERLEWAT --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-keyboard text-amber-500"></i>
                    Kata yang Terlewat
                    <span class="text-xs font-normal text-slate-500">({{ count($metrics['missed_words'] ?? []) }} kata)</span>
                </h3>
            </div>
            <div class="p-5">
                @if(!empty($metrics['missed_words']))
                    <div class="flex flex-wrap gap-2">
                        @foreach($metrics['missed_words'] as $word)
                            <span class="px-3 py-1.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 font-mono text-sm">
                                {{ $word }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-3">💡 Kata-kata ini tidak berhasil diketik dengan benar.</p>
                @else
                    <p class="text-sm text-slate-500 text-center py-4">✅ Semua kata berhasil diketik dengan benar!</p>
                @endif
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- LEADERBOARD --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-trophy text-yellow-500"></i>
                    Leaderboard
                    <span class="text-xs font-normal text-slate-500">({{ $leaderboard->count() }} pemain teratas)</span>
                </h3>
                <span class="text-xs text-slate-500">Peringkat #{{ $rank }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr class="text-left text-slate-500 dark:text-slate-400">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Pemain</th>
                            <th class="px-5 py-3">WPM</th>
                            <th class="px-5 py-3">Akurasi</th>
                            <th class="px-5 py-3">Kata</th>
                            <th class="px-5 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($leaderboard as $idx => $row)
                            @php
                                $isCurrent = $row->id === $session->id;
                                $medal = $idx === 0 ? '🥇' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : '#'.($idx+1)));
                                $rowClass = $isCurrent ? 'bg-indigo-50 dark:bg-indigo-950/20 font-bold' : '';
                            @endphp
                            <tr class="{{ $rowClass }} hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="px-5 py-3">{{ $medal }}</td>
                                <td class="px-5 py-3 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ substr($row->player->name ?? 'G', 0, 1) }}
                                    </span>
                                    {{ $row->player->name ?? 'Guest' }}
                                    @if($isCurrent)
                                        <span class="text-[10px] px-2 py-0.5 bg-indigo-500 text-white rounded-full">Anda</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $row->score }}</td>
                                <td class="px-5 py-3">{{ $row->metrics['accuracy'] ?? 0 }}%</td>
                                <td class="px-5 py-3">{{ $row->metrics['total_words'] ?? 0 }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $row->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TOMBOL AKSI --}}
        {{-- ============================================================ --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
            <a href="{{ route('owner.game.games.show', $game) }}" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('owner.game.games.gameplay', ['game' => $game->id, 'token' => md5($game->id . auth()->id() . 'secret')]) }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/20 flex items-center gap-2">
                    <i class="fas fa-redo"></i> Main Lagi
                </a>
                @if($session->player_id === auth()->id())
                    <button onclick="window.print()" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-md shadow-emerald-500/20 flex items-center gap-2">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .bg-slate-50, .dark\:bg-slate-950 { background: white !important; }
        .shadow-xl, .shadow-sm { box-shadow: none !important; }
        .border { border-color: #e2e8f0 !important; }
    }
    .transition-all { transition: all 0.3s ease; }
</style>
@endpush
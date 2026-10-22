@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        <div>
            <h1 class="text-3xl font-black">Pengaturan Game Center</h1>
            <p class="text-sm text-slate-500 mt-1">
                Pusat konfigurasi sistem, automation, keamanan, ekonomi, reward, matchmaking, dan integrasi game.
            </p>
        </div>

        {{-- SYSTEM HEALTH --}}
        <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-purple-950 text-white p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-sm font-bold text-emerald-300">ALL SYSTEM OPERATIONAL</span>
                    </div>
                    <h2 class="text-2xl font-black mt-3">Game Center Control Center</h2>
                    <p class="text-sm text-white/60 mt-2">Semua subsystem berjalan normal.</p>
                </div>
                <button class="px-5 py-3 rounded-xl bg-white/10 border border-white/10 font-bold hover:bg-white/20">
                    Run System Check
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('owner.game.settings.update') }}">
            @csrf
            <div class="grid lg:grid-cols-2 gap-6">

                {{-- GENERAL --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
                    <h2 class="font-black text-lg">General Configuration</h2>
                    <div class="space-y-5 mt-5">
                        <div>
                            <label class="text-sm font-semibold">Nama Game Center</label>
                            <input type="text" name="game_center_name" value="{{ $settings['game_center_name'] ?? 'UNICODE Game Center' }}" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Maximum Matchmaking</label>
                            <input type="number" name="max_matchmaking" value="{{ $settings['max_matchmaking'] ?? 100 }}" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Default Match Duration</label>
                            <select name="default_match_duration" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                                <option value="5" {{ ($settings['default_match_duration'] ?? 10) == 5 ? 'selected' : '' }}>5 Minutes</option>
                                <option value="10" {{ ($settings['default_match_duration'] ?? 10) == 10 ? 'selected' : '' }}>10 Minutes</option>
                                <option value="15" {{ ($settings['default_match_duration'] ?? 10) == 15 ? 'selected' : '' }}>15 Minutes</option>
                                <option value="20" {{ ($settings['default_match_duration'] ?? 10) == 20 ? 'selected' : '' }}>20 Minutes</option>
                                <option value="30" {{ ($settings['default_match_duration'] ?? 10) == 30 ? 'selected' : '' }}>30 Minutes</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- AUTOMATION --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
                    <h2 class="font-black text-lg">Smart Automation</h2>
                    <p class="text-xs text-slate-500 mt-1">Sistem yang berjalan otomatis tanpa perlu intervensi admin.</p>
                    <div class="space-y-5 mt-5">
                        @php
                            $autoSettings = [
                                ['auto_matchmaking', 'Auto Matchmaking', 'Mencari lawan berdasarkan rating pemain'],
                                ['auto_daily_challenge', 'Auto Daily Challenge', 'Membuat challenge harian otomatis'],
                                ['auto_reward', 'Auto Reward', 'Memberikan XP dan reward secara otomatis'],
                                ['auto_rank_update', 'Auto Rank Update', 'Menghitung rank pemain otomatis'],
                                ['auto_tournament', 'Auto Tournament', 'Menjalankan bracket kompetisi otomatis'],
                            ];
                        @endphp
                        @foreach($autoSettings as $setting)
                        <label class="flex items-center justify-between gap-4">
                            <div>
                                <div class="font-semibold text-sm">{{ $setting[1] }}</div>
                                <div class="text-xs text-slate-500">{{ $setting[2] }}</div>
                            </div>
                            <input type="checkbox" name="{{ $setting[0] }}" value="1" {{ isset($settings[$setting[0]]) && $settings[$setting[0]] ? 'checked' : 'checked' }} class="w-5 h-5">
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- ECONOMY --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
                    <h2 class="font-black text-lg">Smart Economy</h2>
                    <p class="text-xs text-slate-500 mt-1">Sistem XP, coin, reward dan achievement.</p>
                    <div class="grid grid-cols-2 gap-4 mt-5">
                        @php
                            $economySettings = [
                                ['xp_per_win', 'XP per Win', '100'],
                                ['xp_per_challenge', 'XP per Challenge', '50'],
                                ['coin_per_win', 'Win Bonus (Coin)', '25'],
                                ['streak_bonus', 'Streak Bonus', '10'],
                            ];
                        @endphp
                        @foreach($economySettings as $setting)
                        <div>
                            <label class="text-xs text-slate-500">{{ $setting[1] }}</label>
                            <input type="number" name="{{ $setting[0] }}" value="{{ $settings[$setting[0]] ?? $setting[2] }}" class="mt-2 w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- SECURITY --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
                    <h2 class="font-black text-lg">Security & Anti Cheat</h2>
                    <div class="space-y-5 mt-5">
                        @php
                            $securitySettings = [
                                'anti_cheat_engine',
                                'suspicious_activity_detection',
                                'multiple_account_detection',
                                'score_manipulation_detection',
                                'abnormal_speed_detection',
                                'automatic_penalty_system',
                            ];
                        @endphp
                        @foreach($securitySettings as $setting)
                        <label class="flex items-center justify-between">
                            <span class="text-sm font-semibold">{{ ucwords(str_replace('_', ' ', $setting)) }}</span>
                            <input type="checkbox" name="{{ $setting }}" value="1" {{ isset($settings[$setting]) && $settings[$setting] ? 'checked' : 'checked' }} class="w-5 h-5">
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- DANGER ZONE --}}
            <div class="rounded-3xl border border-red-500/20 bg-red-500/5 p-6 mt-6">
                <h2 class="font-black text-red-500">Danger Zone</h2>
                <p class="text-sm text-slate-500 mt-2">
                    Gunakan dengan hati-hati. Operasi berikut dapat memengaruhi seluruh Game Center.
                </p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <button type="button" class="px-4 py-3 rounded-xl border border-red-500/30 text-red-500 font-bold hover:bg-red-500/10">
                        Clear Match Cache
                    </button>
                    <button type="button" class="px-4 py-3 rounded-xl border border-red-500/30 text-red-500 font-bold hover:bg-red-500/10">
                        Rebuild Leaderboard
                    </button>
                    <button type="button" class="px-4 py-3 rounded-xl border border-red-500/30 text-red-500 font-bold hover:bg-red-500/10">
                        Maintenance Mode
                    </button>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-7 py-3 rounded-xl bg-indigo-600 text-white font-black shadow-lg hover:bg-indigo-700">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
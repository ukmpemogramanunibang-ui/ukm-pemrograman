@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        <div class="rounded-3xl bg-gradient-to-r from-purple-700 via-indigo-700 to-cyan-600 text-white p-6 md:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-white/60">Game Center</div>
                    <h1 class="text-3xl md:text-4xl font-black mt-2">Challenge Arena</h1>
                    <p class="text-white/75 mt-2 max-w-2xl">
                        Buat dan kelola tantangan pemain dengan sistem skor, XP, timer, reward, dan matchmaking otomatis.
                    </p>
                </div>

                <a href="{{ route('owner.game.challenges.create') }}" class="px-5 py-3 rounded-2xl bg-white text-indigo-700 font-bold shadow-lg hover:scale-105 transition">
                    + Buat Challenge
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <div class="text-2xl">⚔️</div>
                <div class="text-2xl font-black mt-2">{{ $challenges->total() }}</div>
                <div class="text-xs text-slate-500">Total Challenge</div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <div class="text-2xl">👥</div>
                <div class="text-2xl font-black mt-2">{{ $challenges->sum('max_participants') ?: '0' }}</div>
                <div class="text-xs text-slate-500">Peserta</div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <div class="text-2xl">🎮</div>
                <div class="text-2xl font-black mt-2">{{ $challenges->where('is_active', true)->count() }}</div>
                <div class="text-xs text-slate-500">Aktif</div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <div class="text-2xl">⭐</div>
                <div class="text-2xl font-black mt-2">{{ $challenges->sum('xp_reward') ?: '0' }}</div>
                <div class="text-xs text-slate-500">Total XP</div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                @forelse($challenges as $challenge)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5">
                    <div class="flex gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-cyan-500/20 flex items-center justify-center text-3xl">
                            {{ $challenge->icon ?? '⚡' }}
                        </div>

                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-black">{{ $challenge->title }}</h3>
                                <span class="px-2 py-1 rounded-full bg-red-500/10 text-red-500 text-[10px] font-bold">
                                    {{ ucfirst($challenge->difficulty) }}
                                </span>
                                <span class="px-2 py-1 rounded-full {{ $challenge->is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }} text-[10px] font-bold">
                                    {{ $challenge->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <p class="text-sm text-slate-500 mt-1">{{ $challenge->game->name ?? 'Game' }}</p>

                            <div class="flex flex-wrap gap-4 mt-4 text-xs text-slate-500">
                                <span>⏱ {{ $challenge->duration_minutes }} menit</span>
                                <span>⭐ {{ $challenge->xp_reward }} XP</span>
                                <span>👥 {{ $challenge->max_participants ?? '∞' }} peserta</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <a href="{{ route('owner.game.challenges.edit', $challenge) }}" class="px-4 py-2 rounded-xl bg-indigo-500 text-white text-sm font-bold text-center hover:bg-indigo-600">
                                Kelola
                            </a>
                            <form action="{{ route('owner.game.challenges.toggle', $challenge) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl w-full text-sm font-bold {{ $challenge->is_active ? 'bg-amber-500/10 text-amber-500 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500/20' }}">
                                    {{ $challenge->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-slate-500">
                    <div class="text-6xl mb-4">⚔️</div>
                    <p>Belum ada challenge. Buat challenge pertama!</p>
                </div>
                @endforelse
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6">
                    <h2 class="font-black text-lg">Challenge Automation</h2>
                    <div class="space-y-4 mt-5">
                        <label class="flex items-center justify-between">
                            <span class="text-sm">Daily Challenge</span>
                            <input type="checkbox" checked class="w-5 h-5">
                        </label>
                        <label class="flex items-center justify-between">
                            <span class="text-sm">Auto Reward</span>
                            <input type="checkbox" checked class="w-5 h-5">
                        </label>
                        <label class="flex items-center justify-between">
                            <span class="text-sm">Auto Matchmaking</span>
                            <input type="checkbox" checked class="w-5 h-5">
                        </label>
                        <label class="flex items-center justify-between">
                            <span class="text-sm">Anti Cheat</span>
                            <input type="checkbox" checked class="w-5 h-5">
                        </label>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6">
                    <div class="text-3xl">🤖</div>
                    <h2 class="font-black text-lg mt-3">Smart Challenge Engine</h2>
                    <p class="text-sm text-white/60 mt-2">
                        Sistem dapat memilih tingkat kesulitan challenge berdasarkan performa pemain secara otomatis.
                    </p>
                    <button class="mt-5 w-full py-3 rounded-xl bg-white/10 border border-white/10 font-bold hover:bg-white/20">
                        Buka Smart Engine
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $challenges->links() }}
        </div>
    </div>
</div>
@endsection
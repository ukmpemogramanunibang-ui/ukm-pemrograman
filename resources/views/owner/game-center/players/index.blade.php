@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        <div>
            <h1 class="text-3xl font-black">Manajemen Pemain</h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola akun gaming, rank, XP, statistik, penalti, achievement, dan riwayat pemain.
            </p>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="text-2xl">🟢</div>
                <div class="text-2xl font-black mt-2">{{ $players->where('status', 'active')->count() }}</div>
                <div class="text-xs text-slate-500">Pemain Aktif</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="text-2xl">👥</div>
                <div class="text-2xl font-black mt-2">{{ $players->total() }}</div>
                <div class="text-xs text-slate-500">Total Pemain</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="text-2xl">🚫</div>
                <div class="text-2xl font-black mt-2">{{ $players->where('status', 'suspended')->count() + $players->where('status', 'banned')->count() }}</div>
                <div class="text-xs text-slate-500">Suspended / Banned</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5">
                <div class="text-2xl">❤️</div>
                <div class="text-2xl font-black mt-2">{{ $players->total() > 0 ? round(($players->where('status', 'active')->count() / $players->total()) * 100, 1) : 0 }}%</div>
                <div class="text-xs text-slate-500">Account Health</div>
            </div>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5">
            <div class="grid md:grid-cols-4 gap-3">
                <input type="text" name="search" placeholder="Cari nama / NIM..." value="{{ request('search') }}" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                <select name="rank" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                    <option value="">Semua Rank</option>
                    <option value="Bronze" {{ request('rank') == 'Bronze' ? 'selected' : '' }}>Bronze</option>
                    <option value="Silver" {{ request('rank') == 'Silver' ? 'selected' : '' }}>Silver</option>
                    <option value="Gold" {{ request('rank') == 'Gold' ? 'selected' : '' }}>Gold</option>
                    <option value="Platinum" {{ request('rank') == 'Platinum' ? 'selected' : '' }}>Platinum</option>
                    <option value="Diamond" {{ request('rank') == 'Diamond' ? 'selected' : '' }}>Diamond</option>
                    <option value="Master" {{ request('rank') == 'Master' ? 'selected' : '' }}>Master</option>
                    <option value="Grandmaster" {{ request('rank') == 'Grandmaster' ? 'selected' : '' }}>Grandmaster</option>
                </select>
                <select name="status" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-4 py-3">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
                <button type="submit" class="rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700">Terapkan Filter</button>
            </div>
        </form>

        {{-- PLAYER TABLE --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="text-left px-5 py-4">Pemain</th>
                            <th class="text-left px-5 py-4">Rank</th>
                            <th class="text-left px-5 py-4">Rating</th>
                            <th class="text-left px-5 py-4">XP</th>
                            <th class="text-left px-5 py-4">Win Rate</th>
                            <th class="text-left px-5 py-4">Status</th>
                            <th class="text-right px-5 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
                        <tr class="border-t border-slate-200 dark:border-slate-800">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-white font-bold">
                                        {{ substr($player->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ $player->name }}</div>
                                        <div class="text-xs text-slate-500">Player ID #{{ $player->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full bg-indigo-500/10 text-indigo-500 text-xs font-bold">
                                    {{ $player->rank }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-bold">{{ number_format($player->rating) }}</td>
                            <td class="px-5 py-4">{{ number_format($player->xp) }}</td>
                            <td class="px-5 py-4">{{ $player->win_rate }}%</td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-bold
                                    {{ $player->status === 'active' ? 'text-emerald-500' : 
                                       ($player->status === 'suspended' ? 'text-amber-500' : 'text-red-500') }}">
                                    ● {{ ucfirst($player->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('owner.game.players.show', $player) }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                                <div class="text-6xl mb-4">👤</div>
                                <p>Belum ada pemain yang terdaftar.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $players->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
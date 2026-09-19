@extends('layouts.roles.owner')

@section('title', 'Leaderboard Poin - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.points.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">🏆 Leaderboard Poin</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-yellow-500 to-amber-500 text-white">
            <p class="text-sm">Peringkat berdasarkan total poin anggota</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peringkat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Poin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Badge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($leaderboard ?? [] as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition {{ $index < 3 ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                        <td class="px-4 py-3 whitespace-nowrap text-lg">
                            @if($index == 0) 🥇
                            @elseif($index == 1) 🥈
                            @elseif($index == 2) 🥉
                            @else <span class="text-gray-500 dark:text-gray-400">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800 dark:text-white">
                            {{ $item->user->name ?? '-' }}
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $item->user->nim ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap font-bold text-yellow-600 dark:text-yellow-400 text-lg">{{ number_format($item->balance ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if(($item->balance ?? 0) >= 200)
                                <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">👑 Legendary</span>
                            @elseif(($item->balance ?? 0) >= 100)
                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">⭐ Gold</span>
                            @elseif(($item->balance ?? 0) >= 50)
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">💎 Silver</span>
                            @elseif(($item->balance ?? 0) >= 20)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">🌟 Bronze</span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">🌱 Newbie</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-trophy text-2xl block mb-2 text-gray-300 dark:text-gray-600"></i>
                            Belum ada data leaderboard.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
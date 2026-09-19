@extends('layouts.roles.owner')

@section('title', 'Manajemen Poin - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Poin Anggota</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola reward points anggota</p>
        </div>
        <a href="{{ route('owner.financial.points.leaderboard') }}" class="mt-3 md:mt-0 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition">
            <i class="fas fa-trophy"></i> Leaderboard
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Poin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($points ?? [] as $index => $point)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            @if($index == 0) 🥇
                            @elseif($index == 1) 🥈
                            @elseif($index == 2) 🥉
                            @else {{ $index + 1 }}
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-white">{{ $point->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($point->balance ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ ($point->balance ?? 0) >= 100 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                   (($point->balance ?? 0) >= 50 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400') }}">
                                {{ ($point->balance ?? 0) >= 100 ? 'High' : (($point->balance ?? 0) >= 50 ? 'Medium' : 'Low') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-star text-2xl block mb-2 text-gray-300 dark:text-gray-600"></i>
                            Belum ada data poin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
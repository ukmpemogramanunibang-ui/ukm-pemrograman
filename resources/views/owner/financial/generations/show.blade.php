@extends('layouts.roles.owner')

@section('title', 'Detail Generasi - UNICODE')

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.generations.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-layer-group text-amber-500"></i> {{ $generation->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $generation->code }} · {{ $generation->period_label }}</p>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('owner.financial.generations.target', $generation) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-bullseye"></i> Pilih Target
            </a>
            <a href="{{ route('owner.financial.generations.edit', $generation) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Anggota</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $total }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $total }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
            <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $completed }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Keluar / Pindah</p>
            <p class="text-2xl font-bold text-red-500 dark:text-red-400">{{ $removed + $transferred }}</p>
        </div>
    </div>

    {{-- ROLE DISTRIBUTION --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Distribusi Role</h3>
        <div class="flex flex-wrap gap-4">
            @foreach($membersByRole as $roleId => $count)
                @php
                    $roleName = \App\Models\Level::find($roleId)?->name ?? 'Role ' . $roleId;
                @endphp
                <div class="bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-full">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $roleName }}</span>
                    <span class="ml-2 text-sm font-bold text-blue-600 dark:text-blue-400">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- HISTORY --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Riwayat Keanggotaan</h3>
            <span class="text-xs text-gray-500">{{ $history->count() }} catatan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Anggota</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Mulai</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Selesai</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Alasan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($history as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-2 whitespace-nowrap text-gray-800 dark:text-white">{{ $item->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $item->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                                   ($item->status == 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' :
                                   ($item->status == 'removed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' :
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400')) }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->started_at->format('d M Y') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->ended_at ? $item->ended_at->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ Str::limit($item->reason, 30) }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $item->assigner->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Belum ada riwayat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
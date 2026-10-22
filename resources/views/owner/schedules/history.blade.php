@extends('layouts.roles.owner')

@section('title', 'Riwayat Jadwal – Owner')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-history text-purple-500"></i>
                Riwayat Jadwal
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Jadwal yang sudah selesai atau telah berakhir.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('owner.schedules.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition flex items-center gap-2">
                <i class="fas fa-list"></i> Semua Jadwal
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">🔍 Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, jenis..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('owner.schedules.history') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Ruangan</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Mulai</th>
                        <th class="px-4 py-3">Selesai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $schedule->title }}</p>
                            @if($schedule->description)
                                <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $schedule->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm">{{ $schedule->room->name ?? '-' }}</span>
                            <p class="text-[10px] text-gray-400">{{ $schedule->room->room_code ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-[10px] font-medium">{{ $schedule->activity_type ?: 'Umum' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $schedule->start_datetime ? \Carbon\Carbon::parse($schedule->start_datetime)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $schedule->end_datetime ? \Carbon\Carbon::parse($schedule->end_datetime)->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusClass = $schedule->status == 'Selesai' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">{{ $schedule->status ?: 'Selesai' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-check text-4xl mb-3 block opacity-30"></i>
                            <p>Belum ada riwayat jadwal.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $schedules->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
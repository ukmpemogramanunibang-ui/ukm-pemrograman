@extends('layouts.roles.owner')

@section('title', 'Riwayat Publikasi – Owner')

@push('styles')
<style>
    .fade-in {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 3px #e5e7eb;
        flex-shrink: 0;
    }
    .dark .timeline-dot {
        box-shadow: 0 0 0 3px #374151;
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 fade-in">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.rooms.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Ruangan</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.rooms.public.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Publik QR</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Riwayat Publikasi</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 mt-2">
                <i class="fas fa-history text-purple-500 text-2xl"></i>
                Riwayat Publikasi
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Catatan semua perubahan publikasi ruangan.
            </p>
        </div>
        <a href="{{ route('owner.rooms.public.index') }}" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 fade-in">
        <form method="GET" action="{{ route('owner.rooms.public.history') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Aksi</label>
                <select name="action" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-purple-500">
                    <option value="">Semua</option>
                    <option value="publish" {{ request('action') == 'publish' ? 'selected' : '' }}>Publikasi</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update Akses</option>
                    <option value="unpublish" {{ request('action') == 'unpublish' ? 'selected' : '' }}>Nonaktifkan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari</label>
                <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai</label>
                <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('owner.rooms.public.history') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in">
        <div class="p-6">
            @if(($logs ?? collect())->count())
                <div class="space-y-6 relative pl-8 before:absolute before:left-[18px] before:top-0 before:bottom-0 before:w-0.5 before:bg-gray-200 dark:before:bg-gray-700">
                    @foreach($logs as $log)
                    <div class="relative">
                        <div class="timeline-dot absolute -left-8 top-1
                            @if($log->action == 'publish') bg-emerald-500
                            @elseif($log->action == 'update') bg-blue-500
                            @elseif($log->action == 'unpublish') bg-red-500
                            @else bg-gray-400 @endif
                        "></div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 hover:shadow-md transition">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-800 dark:text-white">{{ $log->user_name ?? 'Sistem' }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full
                                            @if($log->action == 'publish') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                            @elseif($log->action == 'update') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                            @elseif($log->action == 'unpublish') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif
                                        ">
                                            @if($log->action == 'publish') Publikasi
                                            @elseif($log->action == 'update') Update Akses
                                            @elseif($log->action == 'unpublish') Nonaktifkan
                                            @else {{ $log->action }} @endif
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $log->description ?? 'Mempublikasikan ruangan' }}
                                        @if($log->room_name)
                                            <span class="font-medium text-gray-800 dark:text-white">{{ $log->room_name }}</span>
                                        @endif
                                    </p>
                                    @if($log->details)
                                        <p class="text-xs text-gray-400 mt-1">{{ $log->details }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    {{ ($logs ?? collect())->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-history text-5xl mb-4 block opacity-30"></i>
                    <p class="text-base">Belum ada riwayat publikasi.</p>
                    <p class="text-sm mt-1">Publikasikan ruangan untuk mulai mencatat riwayat.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
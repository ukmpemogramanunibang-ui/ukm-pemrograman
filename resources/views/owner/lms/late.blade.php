@extends('layouts.roles.owner')

@section('title', 'Pengumpulan Terlambat')

@section('content')
<div class="p-4 sm:p-6 max-w-7xl mx-auto">

    <div class="mb-5">
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mb-1">
            <a href="{{ route('owner.lms.monitoring') }}" class="hover:text-violet-600">
                <i class="fas fa-chart-line"></i> Monitoring
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Terlambat</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            <i class="fas fa-clock text-rose-500 mr-2"></i> Pengumpulan Terlambat
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Submission yang dikumpulkan melewati deadline. Total: <b>{{ $submissions->total() }}</b>.
        </p>
    </div>

    @if($submissions->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <i class="fas fa-thumbs-up text-5xl text-emerald-300"></i>
            <p class="mt-3 font-bold text-gray-700 dark:text-gray-300">Tidak ada submission terlambat.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($submissions as $s)
                <a href="{{ route('owner.lms.submissions.show', $s->id) }}"
                   class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-2xl
                          border-2 border-rose-200 dark:border-rose-700/40 hover:shadow-lg transition">
                    <div class="w-11 h-11 rounded-xl bg-rose-500 text-white flex items-center justify-center font-black text-sm shrink-0">
                        {{ strtoupper(mb_substr($s->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $s->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                            <i class="fas fa-clipboard-list text-violet-500"></i> {{ $s->item->title ?? '-' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-bold text-rose-600 dark:text-rose-400">
                            <i class="fas fa-clock"></i>
                            {{ optional($s->submitted_at)->isoFormat('D MMM Y · HH:mm') }}
                        </p>
                        @if($s->item?->deadline_at)
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                Deadline: {{ $s->item->deadline_at->isoFormat('D MMM Y · HH:mm') }}
                            </p>
                        @endif
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white">LATE</span>
                </a>
            @endforeach
        </div>
        <div class="mt-5">{{ $submissions->links() }}</div>
    @endif
</div>
@endsection
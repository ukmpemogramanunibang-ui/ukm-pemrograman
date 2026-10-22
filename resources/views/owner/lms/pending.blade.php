@extends('layouts.roles.owner')

@section('title', 'Menunggu Review')

@section('content')
<div class="p-4 sm:p-6 max-w-7xl mx-auto">

    <div class="mb-5">
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mb-1">
            <a href="{{ route('owner.lms.monitoring') }}" class="hover:text-violet-600">
                <i class="fas fa-chart-line"></i> Monitoring
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Menunggu Review</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            <i class="fas fa-hourglass-half text-amber-500 mr-2"></i> Menunggu Review
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Submission yang belum diperiksa. Total: <b>{{ $submissions->total() }}</b>.
        </p>
    </div>

    @if($submissions->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <i class="fas fa-check-circle text-5xl text-emerald-300"></i>
            <p class="mt-3 font-bold text-gray-700 dark:text-gray-300">Semua submission sudah direview! 🎉</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($submissions as $s)
                <a href="{{ route('owner.lms.submissions.show', $s->id) }}"
                   class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-2xl
                          border-2 border-amber-200 dark:border-amber-700/40 hover:shadow-lg transition">
                    <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-sm shrink-0">
                        {{ strtoupper(mb_substr($s->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $s->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                            <i class="fas fa-clipboard-list text-violet-500"></i> {{ $s->item->title ?? '-' }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[10px] text-gray-400">
                            {{ optional($s->submitted_at)->diffForHumans() }}
                        </p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                            <i class="fas fa-eye"></i> Review
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-5">{{ $submissions->links() }}</div>
    @endif
</div>
@endsection
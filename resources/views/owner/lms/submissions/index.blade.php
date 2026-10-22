@extends('layouts.roles.owner')

@section('title', 'Pengumpulan Tugas')

@section('content')
@php
    $statusMeta = [
        'pending_review' => ['label' => 'Menunggu Review', 'cls' => 'warn', 'icon' => 'fa-hourglass-half'],
        'completed'      => ['label' => 'Selesai',         'cls' => 'done', 'icon' => 'fa-circle-check'],
        'revision'       => ['label' => 'Revisi',          'cls' => 'late', 'icon' => 'fa-rotate-left'],
        'late'           => ['label' => 'Terlambat',       'cls' => 'late', 'icon' => 'fa-clock'],
        'draft'          => ['label' => 'Draft',           'cls' => 'muted','icon' => 'fa-file-pen'],
    ];
@endphp

<div class="p-4 sm:p-6 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-start justify-between flex-wrap gap-3 mb-5">
        <div>
            <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mb-1">
                <a href="{{ route('owner.lms.monitoring') }}" class="hover:text-violet-600">
                    <i class="fas fa-chart-line"></i> Monitoring
                </a>
                <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
                <span class="font-bold text-gray-700 dark:text-gray-300">Pengumpulan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">Pengumpulan Tugas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Semua submission dari semua tugas, dengan filter status.
            </p>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="mb-5 flex flex-wrap gap-2 items-center">
        <select name="status"
                class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
            <option value="">Semua Status</option>
            @foreach($statusMeta as $key => $meta)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $meta['label'] }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700 transition">
            <i class="fas fa-filter"></i> Terapkan
        </button>
        @if(request('status'))
            <a href="{{ route('owner.lms.submissions.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-sm font-bold text-gray-600 dark:text-gray-300">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>

    {{-- LIST --}}
    @if($submissions->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
            <i class="fas fa-inbox text-5xl text-gray-300 dark:text-gray-600"></i>
            <p class="mt-3 font-bold text-gray-700 dark:text-gray-300">Belum ada pengumpulan.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($submissions as $s)
                @php $meta = $statusMeta[$s->status] ?? $statusMeta['draft']; @endphp
                <a href="{{ route('owner.lms.submissions.show', $s->id) }}"
                   class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-2xl
                          border border-gray-200 dark:border-gray-700
                          hover:border-violet-400 dark:hover:border-violet-500 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 text-white
                                flex items-center justify-center font-black text-sm shrink-0">
                        {{ strtoupper(mb_substr($s->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-gray-900 dark:text-white truncate">
                            {{ $s->user->name ?? 'User dihapus' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                            <i class="fas fa-clipboard-list text-violet-500"></i>
                            {{ $s->item->title ?? '-' }}
                            @if($s->item?->group)
                                · <i class="fas fa-folder"></i> {{ $s->item->group->name }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black
                            {{ $meta['cls'] === 'done' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' :
                               ($meta['cls'] === 'warn' ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300' :
                               ($meta['cls'] === 'late' ? 'bg-rose-500/10 text-rose-700 dark:text-rose-300' :
                               'bg-slate-500/10 text-slate-700 dark:text-slate-300')) }}">
                            <i class="fas {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                        </span>
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ optional($s->submitted_at)->isoFormat('D MMM Y · HH:mm') ?? '—' }}
                        </p>
                    </div>
                    @if($s->is_late)
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white">LATE</span>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-5">{{ $submissions->links() }}</div>
    @endif
</div>
@endsection
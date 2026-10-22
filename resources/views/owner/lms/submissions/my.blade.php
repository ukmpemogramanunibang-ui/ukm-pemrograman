@extends('layouts.roles.owner')

@section('title', 'Pengumpulan Saya')

@section('content')
@php
    $user = auth()->user();

    $statusMeta = [
        'pending_review' => [
            'label' => 'Menunggu Review',
            'icon'  => 'fa-hourglass-half',
            'cls'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-700/40',
        ],
        'completed' => [
            'label' => 'Selesai',
            'icon'  => 'fa-circle-check',
            'cls'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/40',
        ],
        'done' => [
            'label' => 'Selesai',
            'icon'  => 'fa-circle-check',
            'cls'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/40',
        ],
        'revision' => [
            'label' => 'Perlu Revisi',
            'icon'  => 'fa-rotate-left',
            'cls'   => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 border-rose-200 dark:border-rose-700/40',
        ],
        'late' => [
            'label' => 'Terlambat',
            'icon'  => 'fa-clock',
            'cls'   => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 border-rose-200 dark:border-rose-700/40',
        ],
        'draft' => [
            'label' => 'Draft',
            'icon'  => 'fa-file-pen',
            'cls'   => 'bg-slate-100 text-slate-700 dark:bg-slate-700/40 dark:text-slate-300 border-slate-200 dark:border-slate-600',
        ],
    ];
@endphp

<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-5">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div>
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap mb-2">
            <a href="{{ route('owner.lms.dashboard') }}" class="hover:text-violet-600 transition">
                <i class="fas fa-home"></i> LMS
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Pengumpulan Saya</span>
        </nav>

        <div class="flex items-start justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-check text-violet-500"></i>
                    Pengumpulan Saya
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Riwayat pengumpulan tugas Anda.
                </p>
            </div>

            @if($submissions->total() > 0)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                            bg-violet-50 dark:bg-violet-900/30
                            border border-violet-200 dark:border-violet-700/50">
                    <i class="fas fa-list text-violet-500 text-xs"></i>
                    <span class="text-xs font-bold text-violet-700 dark:text-violet-300">
                        {{ $submissions->total() }} total
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════ FLASH ═══════════════ --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20
                    border border-emerald-200 dark:border-emerald-700/40
                    text-emerald-700 dark:text-emerald-300 text-sm flex items-start gap-2">
            <i class="fas fa-circle-check mt-0.5"></i>
            <div class="min-w-0">
                <p class="font-black">Berhasil</p>
                <p class="mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20
                    border border-rose-200 dark:border-rose-700/40
                    text-rose-700 dark:text-rose-300 text-sm flex items-start gap-2">
            <i class="fas fa-times-circle mt-0.5"></i>
            <div class="min-w-0">
                <p class="font-black">Gagal</p>
                <p class="mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ═══════════════ EMPTY STATE ═══════════════ --}}
    @if($submissions->isEmpty())
        <div class="text-center py-16 rounded-3xl
                    bg-white dark:bg-gray-800
                    border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="w-20 h-20 mx-auto rounded-3xl
                        bg-gray-100 dark:bg-gray-900
                        flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-3xl text-gray-400"></i>
            </div>
            <p class="font-black text-gray-800 dark:text-gray-200">Belum ada pengumpulan</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Anda belum mengumpulkan tugas apapun.
            </p>
            <a href="{{ route('owner.lms.all') }}"
               class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl
                      text-white font-bold text-sm
                      bg-gradient-to-r from-violet-500 to-fuchsia-500
                      hover:shadow-lg hover:shadow-violet-500/30 transition-all">
                <i class="fas fa-list"></i> Lihat Semua Item
            </a>
        </div>
    @else
        {{-- ═══════════════ LIST ═══════════════ --}}
        <div class="space-y-3">
            @foreach($submissions as $s)
                @php
                    $meta = $statusMeta[$s->status] ?? $statusMeta['draft'];
                    $item = $s->item;
                @endphp

                <a href="{{ route('owner.lms.submissions.show', $s->id) }}"
                   class="group block overflow-hidden
                          bg-white dark:bg-gray-800
                          rounded-2xl
                          border border-gray-200 dark:border-gray-700
                          hover:border-violet-400 dark:hover:border-violet-500
                          hover:shadow-xl hover:shadow-violet-500/10
                          transition-all">

                    <div class="p-4 sm:p-5">

                        {{-- Row 1: status + date --}}
                        <div class="flex items-start justify-between gap-3 flex-wrap mb-3">
                            <span class="inline-flex items-center gap-1.5
                                         px-2.5 py-1 rounded-full
                                         text-[10px] font-black uppercase tracking-wider
                                         border {{ $meta['cls'] }}">
                                <i class="fas {{ $meta['icon'] }} text-[9px]"></i>
                                {{ $meta['label'] }}
                            </span>

                            <div class="flex items-center gap-3 text-[11px] text-gray-500 dark:text-gray-400">
                                @if($s->is_late)
                                    <span class="inline-flex items-center gap-1 font-bold text-rose-600 dark:text-rose-400">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                        Terlambat
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1">
                                    <i class="far fa-clock text-[10px]"></i>
                                    {{ optional($s->submitted_at)->isoFormat('D MMM Y · HH:mm') ?? '—' }}
                                </span>
                            </div>
                        </div>

                        {{-- Row 2: item info --}}
                        <div class="flex items-start gap-3">
                            {{-- Icon --}}
                            <div class="shrink-0 w-11 h-11 rounded-xl
                                        bg-gradient-to-br from-violet-500 to-fuchsia-500
                                        text-white flex items-center justify-center
                                        shadow-sm">
                                <i class="fas {{ ($item->type ?? '') === 'task' ? 'fa-clipboard-list' : 'fa-book-open' }}"></i>
                            </div>

                            {{-- Text --}}
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm sm:text-base font-black
                                           text-gray-900 dark:text-white
                                           group-hover:text-violet-600 dark:group-hover:text-violet-400
                                           transition-colors line-clamp-2">
                                    {{ $item->title ?? 'Item dihapus' }}
                                </h3>

                                @if($item)
                                    <div class="flex items-center gap-2 mt-1
                                                text-[11px] text-gray-500 dark:text-gray-400 flex-wrap">
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-folder-open text-[10px]"></i>
                                            {{ $item->group->name ?? 'Tanpa Grup' }}
                                        </span>
                                        @if($item->deadline_at)
                                            <span class="opacity-40">·</span>
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fas fa-hourglass-half text-[10px]"></i>
                                                Deadline {{ $item->deadline_at->isoFormat('D MMM · HH:mm') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Score --}}
                                @if($s->score !== null)
                                    <div class="inline-flex items-center gap-1.5 mt-2
                                                px-2.5 py-1 rounded-lg
                                                bg-emerald-50 dark:bg-emerald-900/20
                                                text-emerald-700 dark:text-emerald-300
                                                text-[11px] font-black">
                                        <i class="fas fa-star text-[10px]"></i>
                                        Nilai: {{ $s->score }}/100
                                    </div>
                                @endif

                                {{-- Feedback preview --}}
                                @if($s->feedback)
                                    <p class="mt-2 text-[11px] text-gray-600 dark:text-gray-400
                                              italic line-clamp-2">
                                        <i class="fas fa-comment text-[9px] mr-1"></i>
                                        {{ $s->feedback }}
                                    </p>
                                @endif
                            </div>

                            {{-- Arrow --}}
                            <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600
                                      group-hover:text-violet-500
                                      group-hover:translate-x-0.5
                                      transition-all shrink-0 mt-3"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($submissions->hasPages())
            <div class="mt-6">{{ $submissions->links() }}</div>
        @endif
    @endif
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection
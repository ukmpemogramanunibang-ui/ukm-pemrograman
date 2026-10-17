@extends('layouts.roles.owner')

@section('title', $group->name . ' — Pengelompokan')

@section('content')
@php
    $authId = auth()->id();
    $isMine = ($group->creator_id ?? null) === $authId
           || ($group->created_by ?? null) === $authId
           || ($group->creator->id ?? null) === $authId;

    $resolveAvatar = function ($user) {
        if (!$user) return 'https://ui-avatars.com/api/?name=?&background=6366f1&color=fff';
        $raw = $user->avatar_url ?? null;
        if (!$raw) {
            return 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'U').'&background=6366f1&color=fff';
        }
        return \Illuminate\Support\Str::startsWith($raw, ['http', '/'])
            ? $raw
            : asset('storage/'.ltrim($raw, '/'));
    };
@endphp

@push('styles')
<style>
    .lc-1, .lc-2, .lc-3 {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .lc-1 { -webkit-line-clamp: 1; line-clamp: 1; }
    .lc-2 { -webkit-line-clamp: 2; line-clamp: 2; }
    .lc-3 { -webkit-line-clamp: 3; line-clamp: 3; }

    /* Card hover effect */
    .item-card {
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .item-card:hover {
        transform: translateY(-3px);
    }

    /* Progress bar mini */
    .mini-progress {
        height: 3px;
        border-radius: 999px;
        background: rgba(15,23,42,.08);
        overflow: hidden;
    }
    .dark .mini-progress { background: rgba(148,163,184,.2); }
    .mini-progress-bar {
        height: 100%;
        border-radius: 999px;
        transition: width .4s;
    }
</style>
@endpush

<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-5">

    {{-- ═══════════════ BREADCRUMB ═══════════════ --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('owner.lms.dashboard') }}" class="hover:text-violet-600 transition">
            <i class="fas fa-home"></i> LMS
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('owner.lms.groups.index') }}" class="hover:text-violet-600 transition">Pengelompokan</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $group->name }}</span>
    </nav>

    {{-- ═══════════════ HERO — GROUP INFO ═══════════════ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700 shadow-sm">

        {{-- Cover strip --}}
        <div class="h-32 sm:h-40 relative"
             style="background: {{ $group->cover_url
                ? 'url('.$group->cover_url.') center/cover'
                : 'linear-gradient(135deg,#8b5cf6 0%,#6366f1 50%,#d946ef 100%)' }};">
            @if(!$group->cover_url)
                <div class="absolute inset-0 flex items-center justify-center
                            text-white/30 text-7xl">
                    <i class="fas fa-folder-open"></i>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>

            {{-- Ownership badge --}}
            @if($isMine)
                <span class="absolute top-4 left-4 inline-flex items-center gap-1.5
                             px-3 py-1.5 rounded-full
                             bg-emerald-500 text-white
                             text-[11px] font-black shadow-lg">
                    <i class="fas fa-user-shield"></i> Grup Saya
                </span>
            @else
                <span class="absolute top-4 left-4 inline-flex items-center gap-1.5
                             px-3 py-1.5 rounded-full
                             bg-slate-900/70 backdrop-blur-sm text-white
                             text-[11px] font-black">
                    <i class="fas fa-users"></i> Grup Bersama
                </span>
            @endif
        </div>

        {{-- Body --}}
        <div class="p-5 sm:p-6 -mt-8 sm:-mt-10 relative">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">

                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white break-words">
                        {{ $group->name }}
                    </h1>

                    @if($group->code)
                        <p class="text-xs font-mono text-violet-600 dark:text-violet-400 mt-1">
                            <i class="fas fa-tag"></i> {{ $group->code }}
                        </p>
                    @endif

                    @if($group->description)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 lc-3">
                            {{ $group->description }}
                        </p>
                    @endif

                    {{-- Stats row --}}
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold
                                     text-gray-700 dark:text-gray-300">
                            <span class="w-6 h-6 rounded-lg bg-violet-100 dark:bg-violet-900/30
                                         text-violet-600 dark:text-violet-300
                                         flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-[10px]"></i>
                            </span>
                            {{ $group->tasks_count ?? 0 }} Tugas
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold
                                     text-gray-700 dark:text-gray-300">
                            <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/30
                                         text-emerald-600 dark:text-emerald-300
                                         flex items-center justify-center">
                                <i class="fas fa-book-open text-[10px]"></i>
                            </span>
                            {{ $group->materials_count ?? 0 }} Materi
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold
                                     text-gray-700 dark:text-gray-300">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-900/30
                                         text-amber-600 dark:text-amber-300
                                         flex items-center justify-center">
                                <i class="fas fa-layer-group text-[10px]"></i>
                            </span>
                            {{ $group->items_count ?? 0 }} Total
                        </span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    <a href="{{ route('owner.lms.groups.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              bg-gray-100 dark:bg-gray-700
                              text-gray-700 dark:text-gray-200
                              text-xs font-bold
                              hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    @if($isMine || auth()->user()->level_id <= 2)
                        <a href="{{ route('owner.lms.groups.items.create', $group->id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                                  text-white text-xs font-black
                                  bg-gradient-to-r from-violet-500 to-fuchsia-500
                                  hover:shadow-lg hover:shadow-violet-500/30
                                  transition-all">
                            <i class="fas fa-plus"></i> Buat Tugas / Materi
                        </a>
                    @endif
                </div>
            </div>

            {{-- Creator --}}
            <div class="flex items-center gap-2 mt-4 pt-4
                        border-t border-dashed border-gray-200 dark:border-gray-700">
                <img src="{{ $resolveAvatar($group->creator) }}"
                     alt="{{ $group->creator->name ?? '?' }}"
                     class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 lc-1">
                        {{ $group->creator->name ?? 'Tidak diketahui' }}
                    </p>
                    <p class="text-[10px] font-bold uppercase
                              text-violet-600 dark:text-violet-400 lc-1">
                        {{ $group->creator->level->name ?? 'Member' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ ITEM LIST — GRID 4×2 ═══════════════ --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-1 h-5 rounded-full bg-violet-500"></span>
                <h2 class="text-sm font-black text-gray-900 dark:text-white">
                    <i class="fas fa-list text-violet-500 mr-1"></i>
                    Item di Pengelompokan Ini
                </h2>
            </div>
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                {{ $items->total() ?? 0 }} item
            </span>
        </div>

        @if(!$items || $items->isEmpty())
            {{-- Empty state --}}
            <div class="text-center py-16 rounded-3xl
                        bg-white dark:bg-gray-800
                        border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-20 h-20 mx-auto rounded-3xl
                            bg-gray-100 dark:bg-gray-900
                            flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                </div>
                <p class="font-black text-gray-800 dark:text-gray-200">Belum ada item.</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Buat tugas atau materi pertama di grup ini.
                </p>
                @if($isMine || auth()->user()->level_id <= 2)
                    <a href="{{ route('owner.lms.groups.items.create', $group->id) }}"
                       class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl
                              text-white font-bold text-sm
                              bg-gradient-to-r from-violet-500 to-fuchsia-500">
                        <i class="fas fa-plus"></i> Buat Sekarang
                    </a>
                @endif
            </div>
        @else
            @php
                // ─── GRID: 2 kolom mobile · 4 kolom desktop ───
                $gridClass = 'grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4';
            @endphp

            <div class="{{ $gridClass }}">
                @foreach($items as $item)
                    @php
                        $isTask      = ($item->type ?? '') === 'task';
                        $deadline    = $item->deadline_at;
                        $isOverdue   = $deadline && now()->gt($deadline);
                        $hasDeadline = (bool) $deadline;
                        $deadlineMode = $item->deadline_mode ?? ($hasDeadline ? 'limited' : 'unlimited');
                        $isUnlimited = $deadlineMode === 'unlimited' || !$hasDeadline;

                        // Cover
                        $cover = $item->cover_url;

                        // Progress (kalau ada metric)
                        $metric = $item->metric ?? [];
                        $total = (int) ($metric['total'] ?? 0);
                        $done  = (int) ($metric['completed'] ?? 0);
                        $pct   = $total > 0 ? (int) round($done / $total * 100) : 0;

                        // Type theme
                        $typeTheme = $isTask
                            ? ['badge' => 'bg-violet-500 text-white', 'gradient' => 'from-violet-500 to-fuchsia-500', 'icon' => 'fa-clipboard-list', 'label' => 'TUGAS']
                            : ['badge' => 'bg-emerald-500 text-white', 'gradient' => 'from-emerald-500 to-teal-500', 'icon' => 'fa-book-open', 'label' => 'MATERI'];
                    @endphp

                    <a href="{{ route('owner.lms.items.show', $item->id) }}"
                       class="item-card group flex flex-col overflow-hidden
                              bg-white dark:bg-gray-800
                              rounded-2xl
                              border border-gray-200 dark:border-gray-700
                              hover:border-violet-400 dark:hover:border-violet-500
                              hover:shadow-xl hover:shadow-violet-500/10">

                        {{-- ═════ COVER THUMBNAIL ═════ --}}
                        <div class="relative aspect-video overflow-hidden
                                    bg-gradient-to-br {{ $typeTheme['gradient'] }}">

                            @if($cover)
                                <img src="{{ $cover }}"
                                     alt="{{ $item->title }}"
                                     class="w-full h-full object-cover
                                            group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div class="absolute inset-0 hidden items-center justify-center text-white/80 text-3xl">
                                    <i class="fas {{ $typeTheme['icon'] }}"></i>
                                </div>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center
                                            text-white/85 text-3xl sm:text-4xl">
                                    <i class="fas {{ $typeTheme['icon'] }}"></i>
                                </div>
                            @endif

                            {{-- Gradient overlay --}}
                            <div class="absolute inset-x-0 bottom-0 h-1/2
                                        bg-gradient-to-t from-black/60 to-transparent
                                        pointer-events-none"></div>

                            {{-- Type badge --}}
                            <span class="absolute top-2 left-2
                                         inline-flex items-center gap-1
                                         px-2 py-0.5 rounded-full
                                         text-[9px] font-black tracking-wider
                                         {{ $typeTheme['badge'] }} shadow-md">
                                <i class="fas {{ $typeTheme['icon'] }} text-[8px]"></i>
                                {{ $typeTheme['label'] }}
                            </span>

                            {{-- Overdue badge --}}
                            @if($isOverdue)
                                <span class="absolute top-2 right-2
                                             inline-flex items-center gap-1
                                             px-2 py-0.5 rounded-full
                                             text-[9px] font-black tracking-wider
                                             bg-rose-500 text-white shadow-md">
                                    <i class="fas fa-exclamation-triangle text-[8px]"></i>
                                    LEWAT
                                </span>
                            @endif

                            {{-- Title overlay (kalau tanpa cover, tetap jelas) --}}
                            @if(!$cover)
                                <div class="absolute bottom-2 left-2 right-2
                                            text-[10px] font-bold text-white/90 lc-1">
                                    {{ $item->title }}
                                </div>
                            @endif
                        </div>

                        {{-- ═════ BODY ═════ --}}
                        <div class="flex-1 flex flex-col p-3">

                            {{-- Title --}}
                            <h3 class="text-xs sm:text-[13px] font-black
                                       text-gray-900 dark:text-white
                                       lc-2 min-h-[2.4em]
                                       group-hover:text-violet-600 dark:group-hover:text-violet-400
                                       transition-colors">
                                {{ $item->title }}
                            </h3>

                            {{-- Description --}}
                            @if($item->description)
                                <p class="text-[10px] text-gray-500 dark:text-gray-400
                                          mt-1 lc-2 min-h-[2.6em]">
                                    {{ $item->description }}
                                </p>
                            @endif

                            {{-- Deadline chip --}}
                            <div class="mt-auto pt-2.5">
                                @if($isUnlimited)
                                    <span class="inline-flex items-center gap-1
                                                 px-2 py-1 rounded-lg
                                                 bg-slate-100 dark:bg-slate-700/50
                                                 text-slate-600 dark:text-slate-300
                                                 text-[10px] font-bold w-full
                                                 justify-center">
                                        <i class="fas fa-infinity text-[9px]"></i>
                                        Tanpa Batas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1
                                                 px-2 py-1 rounded-lg w-full justify-center
                                                 text-[10px] font-bold
                                                 {{ $isOverdue
                                                     ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300'
                                                     : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                        <i class="fas {{ $isOverdue ? 'fa-lock' : 'fa-clock' }} text-[9px]"></i>
                                        {{ $deadline->isoFormat('D MMM · HH:mm') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Mini progress (kalau ada penerima) --}}
                            @if($total > 0)
                                <div class="mt-2.5 flex items-center gap-2">
                                    <div class="flex-1 mini-progress">
                                        <div class="mini-progress-bar
                                                    bg-gradient-to-r from-emerald-500 to-teal-500"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[9px] font-black
                                                 text-emerald-600 dark:text-emerald-400
                                                 shrink-0">
                                        {{ $pct }}%
                                    </span>
                                </div>
                            @endif

                            {{-- Footer: creator mini --}}
                            <div class="flex items-center gap-1.5 mt-2.5
                                        pt-2.5 border-t border-dashed
                                        border-gray-200 dark:border-gray-700">
                                <img src="{{ $resolveAvatar($item->creator ?? null) }}"
                                     alt="{{ $item->creator->name ?? '?' }}"
                                     class="w-5 h-5 rounded-full object-cover shrink-0"
                                     loading="lazy">
                                <span class="text-[9px] font-bold
                                             text-gray-600 dark:text-gray-400 lc-1 flex-1">
                                    {{ $item->creator->name ?? 'Sistem' }}
                                </span>
                                @if(($item->recipients_count ?? 0) > 0)
                                    <span class="text-[9px] font-bold
                                                 text-violet-600 dark:text-violet-400
                                                 inline-flex items-center gap-0.5 shrink-0">
                                        <i class="fas fa-users text-[8px]"></i>
                                        {{ $item->recipients_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if(method_exists($items, 'hasPages') && $items->hasPages())
                <div class="mt-6">{{ $items->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
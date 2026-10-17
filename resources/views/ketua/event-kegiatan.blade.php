@extends('layouts.roles.ketua')

@section('title', 'Kegiatan, Event & Tugas – UNICODE')
@section('page-title', 'Kegiatan & Event')

@include('partials.roles.ketua.sidebar-aware')

@push('styles')
<style>
    /* ═══ TAB NAVIGATION ═══ */
    .ketua-tabs {
        display: flex; flex-wrap: wrap; gap: 0.25rem;
        background: #f3f4f6;
        padding: 0.25rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
    }
    .dark .ketua-tabs { background: #1f2937; }

    .ketua-tab {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: #6b7280;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .dark .ketua-tab { color: #9ca3af; }
    .ketua-tab:hover:not(.active) {
        background: rgba(255,255,255,0.5);
        color: #374151;
    }
    .dark .ketua-tab:hover:not(.active) {
        background: rgba(55,65,81,0.5);
        color: #e5e7eb;
    }
    .ketua-tab.active {
        background: white;
        color: #7c3aed;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .dark .ketua-tab.active {
        background: #374151;
        color: #c4b5fd;
    }
    .ketua-tab .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.25rem;
        height: 1.25rem;
        padding: 0 0.35rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 800;
        background: #e5e7eb;
        color: #4b5563;
    }
    .dark .ketua-tab .tab-badge { background: #4b5563; color: #d1d5db; }
    .ketua-tab.active .tab-badge { background: #7c3aed; color: white; }
    .dark .ketua-tab.active .tab-badge { background: #a78bfa; color: #111827; }

    /* ═══ LMS CARD ═══ */
    .lms-card {
        position: relative;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.25s ease;
    }
    .dark .lms-card {
        background: #1f2937;
        border-color: #374151;
    }
    .lms-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -8px rgba(124,58,237,0.25);
        border-color: rgba(124,58,237,0.4);
    }

    .lms-card-cover {
        position: relative;
        aspect-ratio: 16 / 9;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        overflow: hidden;
    }
    .lms-card-cover img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .lms-card:hover .lms-card-cover img { transform: scale(1.08); }

    .lms-card-cover .cover-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
    }

    .lms-card-badge-type {
        position: absolute; top: 0.5rem; left: 0.5rem;
        padding: 0.15rem 0.55rem;
        border-radius: 9999px;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(255,255,255,0.95);
        color: #7c3aed;
        backdrop-filter: blur(6px);
    }
    .dark .lms-card-badge-type {
        background: rgba(17,24,39,0.9);
        color: #c4b5fd;
    }
    .lms-card-badge-type.material {
        color: #0891b2;
    }
    .dark .lms-card-badge-type.material { color: #67e8f9; }

    .lms-card-deadline {
        position: absolute;
        bottom: 0.5rem; left: 0.5rem; right: 0.5rem;
        padding: 0.35rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.65rem;
        font-weight: 700;
        background: rgba(0,0,0,0.7);
        color: white;
        backdrop-filter: blur(6px);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lms-card-deadline.urgent { background: rgba(220,38,38,0.85); }
    .lms-card-deadline.warning { background: rgba(245,158,11,0.85); }

    .lms-card-body {
        padding: 0.75rem 0.85rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .lms-card-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .dark .lms-card-title { color: #f9fafb; }

    .lms-card-desc {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 0.25rem;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2em;
    }
    .dark .lms-card-desc { color: #9ca3af; }

    .lms-card-group {
        font-size: 0.65rem;
        color: #9ca3af;
        margin-top: 0.5rem;
        display: flex; align-items: center; gap: 0.3rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lms-card-footer {
        display: flex; align-items: center; gap: 0.5rem;
        margin-top: 0.65rem;
        padding-top: 0.65rem;
        border-top: 1px solid #f3f4f6;
    }
    .dark .lms-card-footer { border-color: #374151; }

    .lms-card-footer img {
        width: 1.75rem; height: 1.75rem;
        border-radius: 9999px;
        object-fit: cover;
        flex-shrink: 0;
        box-shadow: 0 0 0 2px white;
    }
    .dark .lms-card-footer img { box-shadow: 0 0 0 2px #1f2937; }

    .lms-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .lms-status-pill.new      { background: #dbeafe; color: #1e40af; }
    .lms-status-pill.progress { background: #fef3c7; color: #92400e; }
    .lms-status-pill.done     { background: #d1fae5; color: #065f46; }
    .lms-status-pill.late     { background: #fee2e2; color: #991b1b; }
    .dark .lms-status-pill.new      { background: rgba(59,130,246,0.2);  color: #93c5fd; }
    .dark .lms-status-pill.progress { background: rgba(245,158,11,0.2);  color: #fbbf24; }
    .dark .lms-status-pill.done     { background: rgba(34,197,94,0.2);   color: #86efac; }
    .dark .lms-status-pill.late     { background: rgba(239,68,68,0.2);   color: #fca5a5; }

    /* ═══ LMS STATS ═══ */
    .lms-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .lms-stats-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .lms-stat-card {
        background: white;
        border-radius: 0.85rem;
        border: 1px solid #e5e7eb;
        padding: 0.85rem 1rem;
        transition: all 0.2s;
    }
    .dark .lms-stat-card { background: #1f2937; border-color: #374151; }
    .lms-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -6px rgba(0,0,0,0.1);
    }

    .lms-stat-card .lms-stat-value {
        font-size: 1.5rem;
        font-weight: 900;
        line-height: 1;
        margin-top: 0.25rem;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }
    .dark .lms-stat-card .lms-stat-value { color: #f9fafb; }

    /* ═══ EMPTY STATE ═══ */
    .lms-empty {
        grid-column: 1 / -1;
        padding: 2.5rem 1rem;
        text-align: center;
        background: white;
        border-radius: 1rem;
        border: 2px dashed #e5e7eb;
    }
    .dark .lms-empty { background: #1f2937; border-color: #374151; }

    /* ═══ SECTION DIVIDER ═══ */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        font-weight: 800;
        color: #111827;
    }
    .dark .section-title { color: #f9fafb; }
</style>
@endpush

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;

    /* ═══ Helper: resolve cover ═══ */
    $resolveCover = function ($model, array $candidates = ['cover_path','cover','cover_url','thumbnail','image','banner','attachments']) {
        if (!$model) return null;
        foreach ($candidates as $c) {
            $val = $model->{$c} ?? null;
            if (empty($val)) continue;

            // Kalau array (attachments)
            if (is_array($val)) {
                $first = collect($val)->first();
                if (is_string($first) && !empty($first)) {
                    return \Illuminate\Support\Str::startsWith($first, ['http://','https://','/'])
                        ? $first
                        : asset('storage/'.ltrim($first, '/'));
                }
                continue;
            }

            if (\Illuminate\Support\Str::startsWith($val, ['http://','https://','/'])) {
                return $val;
            }
            return asset('storage/'.ltrim($val, '/'));
        }
        return null;
    };

    /* ═══ Helper: resolve role label ═══ */
    $resolveRole = function ($user) {
        if (!$user) return 'Sistem';
        if (!empty($user->level->name)) {
            return ucfirst(str_replace('_', ' ', $user->level->name));
        }
        if (method_exists($user, 'getRoleNames')) {
            $names = $user->getRoleNames();
            if ($names && $names->count()) return $names->first();
        }
        return 'Member';
    };

    /* ═══ Helper: resolve avatar ═══ */
    $resolveAvatar = function ($user) {
        if (!$user) return 'https://ui-avatars.com/api/?name=?&background=6366f1&color=fff';
        if (!empty($user->avatar_url)) {
            return \Illuminate\Support\Str::startsWith($user->avatar_url, ['http','/'])
                ? $user->avatar_url
                : asset('storage/'.$user->avatar_url);
        }
        return 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'U').'&background=6366f1&color=fff';
    };

    $gridCls = 'grid grid-cols-2 landscape:grid-cols-3 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4';

    /* ═══ Current tab ═══ */
    $currentTab = $tab ?? request('tab', 'events');

    /* ═══ LMS stats availability ═══ */
    $lmsAvailable = ($lmsStats['total'] ?? 0) > 0
        || $lmsTasks->count() > 0
        || $lmsMaterials->count() > 0;

    /* ═══ Events stats ═══ */
    $totalEvents    = $events->total() ?? 0;
    $upcomingEvents = collect($events->items() ?? [])
        ->filter(fn ($e) => isset($e->start_date)
            && \Carbon\Carbon::parse($e->start_date)->isFuture())
        ->count();
    $ongoingEvents  = collect($events->items() ?? [])
        ->filter(fn ($e) => isset($e->start_date, $e->end_date)
            && \Carbon\Carbon::now()->between($e->start_date, $e->end_date))
        ->count();
@endphp

<div class="page-shell">
<div class="page-content-narrow space-y-6">

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- HERO HEADER --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
                Event & Learning Center
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
                <i class="fas fa-calendar-check text-purple-500 mr-2"></i>Kegiatan, Event & Tugas
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Semua kegiatan, event, tugas, dan materi dari Owner untuk Anda.
            </p>
        </div>

    {{-- Quick Action Buttons --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Buat Tugas --}}
        <a href="{{ $safeRoute('ketua.lms.items.create', [], $safeRoute('owner.lms.items.create')) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                  bg-gradient-to-r from-blue-500 to-cyan-600
                  text-white text-sm font-bold
                  hover:shadow-lg hover:shadow-blue-500/30
                  hover:-translate-y-0.5 transition-all active:scale-95">
            <i class="fas fa-plus-circle"></i>
            <span class="hidden sm:inline">Buat Tugas</span>
            <span class="sm:hidden">Tugas</span>
        </a>

        {{-- Buat Event --}}
        <a href="{{ $safeRoute('ketua.events.create', [], $safeRoute('owner.events.create', [], '#')) }}"
           onclick="if(this.getAttribute('href') === '#'){ event.preventDefault(); alert('Fitur buat event sedang dikembangkan. Hubungi Owner untuk membuat event baru.'); }"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                  bg-gradient-to-r from-purple-500 to-pink-600
                  text-white text-sm font-bold
                  hover:shadow-lg hover:shadow-purple-500/30
                  hover:-translate-y-0.5 transition-all active:scale-95">
            <i class="fas fa-calendar-plus"></i>
            <span class="hidden sm:inline">Buat Event</span>
            <span class="sm:hidden">Event</span>
        </a>

        {{-- Buat Pengelompokan (optional) --}}
        <a href="{{ $safeRoute('ketua.lms.groups.create', [], $safeRoute('owner.lms.groups.create')) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                  bg-white dark:bg-gray-800
                  border border-gray-200 dark:border-gray-700
                  text-sm font-bold text-gray-700 dark:text-gray-200
                  hover:shadow-md hover:border-amber-400 transition-all">
            <i class="fas fa-folder-plus text-amber-500"></i>
            <span class="hidden sm:inline">Grup</span>
        </a>

        {{-- Dashboard --}}
        <a href="{{ $safeRoute('ketua.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                  bg-white dark:bg-gray-800
                  border border-gray-200 dark:border-gray-700
                  text-sm font-bold text-gray-700 dark:text-gray-200
                  hover:shadow-md transition-all">
            <i class="fas fa-home text-purple-500"></i>
        </a>
    </div>
</div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- STATS OVERVIEW --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="lms-stats-grid">
        {{-- Total Event --}}
        <div class="lms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Total Event</p>
                    <p class="lms-stat-value">{{ number_format($totalEvents) }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">
                        {{ $upcomingEvents }} akan datang
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-500/15 text-purple-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>

        {{-- Total Tugas --}}
        <div class="lms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Tugas</p>
                    <p class="lms-stat-value">{{ number_format($lmsStats['tasks'] ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">
                        {{ $myStats['new'] ?? 0 }} belum dibuka
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>

        {{-- Total Materi --}}
        <div class="lms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Materi</p>
                    <p class="lms-stat-value">{{ number_format($lmsStats['materials'] ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">
                        {{ $myStats['done'] ?? 0 }} selesai dipelajari
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-500/15 text-cyan-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>

        {{-- Deadline Terlambat --}}
        <div class="lms-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Terlambat</p>
                    <p class="lms-stat-value {{ ($myStats['late'] ?? 0) > 0 ? 'text-rose-500' : '' }}">
                        {{ number_format($myStats['late'] ?? 0) }}
                    </p>
                    <p class="text-[10px] text-gray-400 mt-1">
                        {{ $myStats['progress'] ?? 0 }} sedang dikerjakan
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 text-rose-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TABS NAVIGATION --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="ketua-tabs" role="tablist">
        <button type="button" class="ketua-tab {{ $currentTab === 'events' ? 'active' : '' }}"
                onclick="switchTab('events')" role="tab">
            <i class="fas fa-calendar-check"></i>
            Event
            <span class="tab-badge">{{ $totalEvents }}</span>
        </button>

        <button type="button" class="ketua-tab {{ $currentTab === 'tasks' ? 'active' : '' }}"
                onclick="switchTab('tasks')" role="tab">
            <i class="fas fa-tasks"></i>
            Tugas
            <span class="tab-badge">{{ $lmsStats['tasks'] ?? 0 }}</span>
        </button>

        <button type="button" class="ketua-tab {{ $currentTab === 'materials' ? 'active' : '' }}"
                onclick="switchTab('materials')" role="tab">
            <i class="fas fa-book-open"></i>
            Materi
            <span class="tab-badge">{{ $lmsStats['materials'] ?? 0 }}</span>
        </button>

        @if(($lmsGroups ?? collect())->count() > 0)
        <button type="button" class="ketua-tab {{ $currentTab === 'groups' ? 'active' : '' }}"
                onclick="switchTab('groups')" role="tab">
            <i class="fas fa-folder-tree"></i>
            Grup
            <span class="tab-badge">{{ $lmsGroups->count() }}</span>
        </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB CONTENT: EVENTS --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div id="tab-events" class="tab-panel {{ $currentTab === 'events' ? '' : 'hidden' }} space-y-4">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-calendar-check text-purple-500"></i>
                Semua Event
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300">
                    {{ $totalEvents }} event
                </span>
                <a href="{{ $safeRoute('ketua.events.create', [], '#') }}"
                   onclick="if(this.getAttribute('href') === '#'){ event.preventDefault(); alert('Fitur buat event sedang dikembangkan.'); }"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                          bg-gradient-to-r from-purple-500 to-pink-600
                          text-white text-xs font-bold
                          hover:shadow-md transition active:scale-95">
                    <i class="fas fa-plus"></i> Buat Event
                </a>
            </div>
        </div>

        @if ($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($events as $event)
                    @php
                        $date = isset($event->start_date) ? \Carbon\Carbon::parse($event->start_date) : null;
                        $isFuture = $date && $date->isFuture();
                        $isOngoing = isset($event->start_date, $event->end_date)
                            && \Carbon\Carbon::now()->between($event->start_date, $event->end_date);
                        $cover = $resolveCover($event, ['banner','image','cover','cover_path','thumbnail']);
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700
                                overflow-hidden shadow-sm hover:shadow-lg hover:border-purple-400/40
                                hover:-translate-y-0.5 transition-all group">
                        <div class="relative h-32 bg-gradient-to-br from-purple-400 to-pink-500 overflow-hidden">
                            @if ($cover)
                                <img src="{{ $cover }}" alt="{{ $event->name ?? $event->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <i class="fas fa-calendar-star text-5xl text-white/40"></i>
                                </div>
                            @endif

                            @if ($date)
                                <div class="absolute top-3 left-3 bg-white/95 dark:bg-gray-900/95 rounded-xl px-3 py-1.5 shadow-lg">
                                    <p class="text-[10px] font-bold uppercase text-purple-600 tracking-wider">
                                        {{ $date->translatedFormat('M') }}
                                    </p>
                                    <p class="text-lg font-extrabold text-gray-900 dark:text-white leading-none">
                                        {{ $date->format('d') }}
                                    </p>
                                </div>
                            @endif

                            @if ($isOngoing)
                                <span class="absolute top-3 right-3 px-2 py-0.5 rounded-full bg-emerald-500 text-white
                                             text-[10px] font-bold animate-pulse">
                                    <i class="fas fa-circle text-[6px]"></i> Live
                                </span>
                            @endif
                        </div>

                        <div class="p-4">
                            <h3 class="font-bold text-sm text-gray-900 dark:text-white line-clamp-2 mb-1">
                                {{ $event->name ?? $event->title ?? 'Untitled Event' }}
                            </h3>
                            @if (!empty($event->description))
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit($event->description, 100) }}
                                </p>
                            @endif

                            <div class="flex items-center gap-2 mt-3 text-[10px] text-gray-400">
                                @if (!empty($event->location))
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ \Illuminate\Support\Str::limit($event->location, 20) }}
                                    </span>
                                @endif
                                @if ($isFuture)
                                    <span class="ml-auto px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30
                                                 text-amber-700 dark:text-amber-300 font-bold">
                                        Akan Datang
                                    </span>
                                @elseif ($isOngoing)
                                    <span class="ml-auto px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30
                                                 text-emerald-700 dark:text-emerald-300 font-bold">
                                        Berlangsung
                                    </span>
                                @elseif ($date)
                                    <span class="ml-auto px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700
                                                 text-gray-500 dark:text-gray-400 font-bold">
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (method_exists($events, 'hasPages') && $events->hasPages())
                <div class="flex justify-center pt-2">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @endif
               @else
            <div class="lms-empty">
                <i class="fas fa-calendar-xmark text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada kegiatan</p>
                <p class="text-xs text-gray-500 mt-1">Event baru akan muncul di sini</p>
                <a href="{{ $safeRoute('ketua.events.create', [], '#') }}"
                   onclick="if(this.getAttribute('href') === '#'){ event.preventDefault(); alert('Fitur buat event sedang dikembangkan.'); }"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl
                          bg-gradient-to-r from-purple-500 to-pink-600 text-white
                          font-bold text-sm hover:shadow-lg transition">
                    <i class="fas fa-calendar-plus"></i> Buat Event Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB CONTENT: TASKS --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div id="tab-tasks" class="tab-panel {{ $currentTab === 'tasks' ? '' : 'hidden' }} space-y-4">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-tasks text-blue-500"></i>
                Tugas dari Owner
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ $safeRoute('owner.lms.tasks') }}"
                   class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline
                          inline-flex items-center gap-1">
                    Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
                <a href="{{ $safeRoute('ketua.lms.items.create', [], $safeRoute('owner.lms.items.create')) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                          bg-gradient-to-r from-blue-500 to-cyan-600
                          text-white text-xs font-bold
                          hover:shadow-md transition active:scale-95">
                    <i class="fas fa-plus"></i> Buat Tugas
                </a>
            </div>
        </div>

        @if ($lmsTasks->count() > 0)
            <div class="{{ $gridCls }}">
                @foreach ($lmsTasks as $task)
                    @php
                        $cover    = $resolveCover($task);
                        $creator  = $task->creator ?? null;
                        $deadline = $task->deadline_at ?? null;
                        $deadlineCarbon = $deadline ? \Carbon\Carbon::parse($deadline) : null;

                        $deadlineClass = '';
                        if ($deadlineCarbon) {
                            $diffHours = now()->diffInHours($deadlineCarbon, false);
                            if ($deadlineCarbon->isPast()) $deadlineClass = 'urgent';
                            elseif ($diffHours < 24) $deadlineClass = 'warning';
                        }
                    @endphp
                    <a href="{{ $safeRoute('owner.lms.items.show', $task->id) }}"
                       class="lms-card group">
                        <div class="lms-card-cover">
                            @if ($cover)
                                <img src="{{ $cover }}" alt="{{ $task->title }}" loading="lazy">
                            @else
                                <div class="cover-placeholder">
                                    <i class="fas fa-clipboard-list text-4xl text-white/80"></i>
                                </div>
                            @endif

                            <span class="lms-card-badge-type">
                                <i class="fas fa-tasks"></i> Tugas
                            </span>

                            @if ($deadlineCarbon)
                                <span class="lms-card-deadline {{ $deadlineClass }}">
                                    <i class="fas fa-clock"></i>
                                    {{ $deadlineCarbon->isoFormat('D MMM · HH:mm') }} WIB
                                </span>
                            @endif
                        </div>

                        <div class="lms-card-body">
                            <h3 class="lms-card-title" title="{{ $task->title }}">
                                {{ $task->title }}
                            </h3>
                            <p class="lms-card-desc">
                                {{ $task->description ? \Illuminate\Support\Str::limit($task->description, 80) : 'Tanpa deskripsi.' }}
                            </p>

                            @if ($task->group)
                                <p class="lms-card-group">
                                    <i class="fas fa-folder text-violet-500"></i>
                                    {{ $task->group->name }}
                                </p>
                            @endif

                            @if ($creator)
                                <div class="lms-card-footer">
                                    <img src="{{ $resolveAvatar($creator) }}" alt="{{ $creator->name }}"
                                         loading="lazy">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 truncate">
                                            {{ $creator->name }}
                                        </p>
                                        <p class="text-[9px] font-semibold text-violet-600 dark:text-violet-400
                                                  truncate uppercase tracking-wide">
                                            <i class="fas fa-shield-alt"></i>
                                            {{ $resolveRole($creator) }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if (method_exists($lmsTasks, 'hasPages') && $lmsTasks->hasPages())
                <div class="flex justify-center pt-2">
                    {{ $lmsTasks->appends(['tab' => 'tasks'])->links() }}
                </div>
            @endif
               @else
            <div class="lms-empty">
                <i class="fas fa-clipboard-check text-4xl text-blue-500 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada tugas</p>
                <p class="text-xs text-gray-500 mt-1">Buat tugas pertama untuk anggota/pengurus.</p>
                <a href="{{ $safeRoute('ketua.lms.items.create', [], $safeRoute('owner.lms.items.create')) }}"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl
                          bg-gradient-to-r from-blue-500 to-cyan-600 text-white
                          font-bold text-sm hover:shadow-lg transition">
                    <i class="fas fa-plus"></i> Buat Tugas Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB CONTENT: MATERIALS --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div id="tab-materials" class="tab-panel {{ $currentTab === 'materials' ? '' : 'hidden' }} space-y-4">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-book-open text-cyan-500"></i>
                Materi Pembelajaran
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ $safeRoute('owner.lms.materials') }}"
                   class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline
                          inline-flex items-center gap-1">
                    Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
                <a href="{{ $safeRoute('ketua.lms.items.create', ['type' => 'material'], $safeRoute('owner.lms.items.create', ['type' => 'material'])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                          bg-gradient-to-r from-cyan-500 to-blue-600
                          text-white text-xs font-bold
                          hover:shadow-md transition active:scale-95">
                    <i class="fas fa-plus"></i> Buat Materi
                </a>
            </div>
        </div>

        @if ($lmsMaterials->count() > 0)
            <div class="{{ $gridCls }}">
                @foreach ($lmsMaterials as $material)
                    @php
                        $cover   = $resolveCover($material);
                        $creator = $material->creator ?? null;
                    @endphp
                    <a href="{{ $safeRoute('owner.lms.items.show', $material->id) }}"
                       class="lms-card group">
                        <div class="lms-card-cover" style="background: linear-gradient(135deg, #06b6d4, #3b82f6);">
                            @if ($cover)
                                <img src="{{ $cover }}" alt="{{ $material->title }}" loading="lazy">
                            @else
                                <div class="cover-placeholder">
                                    <i class="fas fa-book text-4xl text-white/80"></i>
                                </div>
                            @endif

                            <span class="lms-card-badge-type material">
                                <i class="fas fa-book-open"></i> Materi
                            </span>
                        </div>

                        <div class="lms-card-body">
                            <h3 class="lms-card-title" title="{{ $material->title }}">
                                {{ $material->title }}
                            </h3>
                            <p class="lms-card-desc">
                                {{ $material->description ? \Illuminate\Support\Str::limit($material->description, 80) : 'Tanpa deskripsi.' }}
                            </p>

                            @if ($material->group)
                                <p class="lms-card-group">
                                    <i class="fas fa-folder text-cyan-500"></i>
                                    {{ $material->group->name }}
                                </p>
                            @endif

                            @if ($creator)
                                <div class="lms-card-footer">
                                    <img src="{{ $resolveAvatar($creator) }}" alt="{{ $creator->name }}"
                                         loading="lazy">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 truncate">
                                            {{ $creator->name }}
                                        </p>
                                        <p class="text-[9px] font-semibold text-cyan-600 dark:text-cyan-400
                                                  truncate uppercase tracking-wide">
                                            <i class="fas fa-shield-alt"></i>
                                            {{ $resolveRole($creator) }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if (method_exists($lmsMaterials, 'hasPages') && $lmsMaterials->hasPages())
                <div class="flex justify-center pt-2">
                    {{ $lmsMaterials->appends(['tab' => 'materials'])->links() }}
                </div>
            @endif
                @else
            <div class="lms-empty">
                <i class="fas fa-book text-4xl text-cyan-500 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada materi</p>
                <p class="text-xs text-gray-500 mt-1">Buat materi pertama untuk anggota/pengurus.</p>
                <a href="{{ $safeRoute('ketua.lms.items.create', ['type' => 'material'], $safeRoute('owner.lms.items.create', ['type' => 'material'])) }}"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl
                          bg-gradient-to-r from-cyan-500 to-blue-600 text-white
                          font-bold text-sm hover:shadow-lg transition">
                    <i class="fas fa-plus"></i> Buat Materi Pertama
                </a>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- TAB CONTENT: GROUPS --}}
    {{-- ══════════════════════════════════════════════════ --}}
    @if(($lmsGroups ?? collect())->count() > 0)
    <div id="tab-groups" class="tab-panel {{ $currentTab === 'groups' ? '' : 'hidden' }} space-y-4">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-folder-tree text-amber-500"></i>
                Grup Pembelajaran
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300">
                    {{ $lmsGroups->count() }} grup aktif
                </span>
                <a href="{{ $safeRoute('ketua.lms.groups.create', [], $safeRoute('owner.lms.groups.create')) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                          bg-gradient-to-r from-amber-500 to-orange-600
                          text-white text-xs font-bold
                          hover:shadow-md transition active:scale-95">
                    <i class="fas fa-plus"></i> Buat Grup
                </a>
            </div>
        </div>

        <div class="{{ $gridCls }}">
            @foreach ($lmsGroups as $group)
                @php
                    $cover   = $resolveCover($group);
                    $creator = $group->creator ?? null;
                @endphp
                <a href="{{ $safeRoute('owner.lms.groups.show', $group->id) }}"
                   class="lms-card group">
                    <div class="lms-card-cover">
                        @if ($cover)
                            <img src="{{ $cover }}" alt="{{ $group->name }}" loading="lazy">
                        @else
                            <div class="cover-placeholder">
                                <i class="fas fa-folder-open text-4xl text-white/80"></i>
                            </div>
                        @endif

                        <span class="lms-card-badge-type">
                            <i class="fas fa-layer-group"></i> {{ $group->items_count ?? 0 }} item
                        </span>
                    </div>

                    <div class="lms-card-body">
                        <h3 class="lms-card-title" title="{{ $group->name }}">
                            {{ $group->name }}
                        </h3>
                        <p class="lms-card-desc">
                            {{ $group->description ?: 'Tanpa deskripsi.' }}
                        </p>

                        @if ($creator)
                            <div class="lms-card-footer">
                                <img src="{{ $resolveAvatar($creator) }}" alt="{{ $creator->name }}"
                                     loading="lazy">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 truncate">
                                        {{ $creator->name }}
                                    </p>
                                    <p class="text-[9px] font-semibold text-amber-600 dark:text-amber-400
                                              truncate uppercase tracking-wide">
                                        <i class="fas fa-shield-alt"></i>
                                        {{ $resolveRole($creator) }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>

@push('scripts')
<script>
    /* ═══════════════════════════════════════════════════════
       TAB SWITCHING
       ═══════════════════════════════════════════════════════ */
    function switchTab(tabName) {
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.ketua-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected panel
        const targetPanel = document.getElementById('tab-' + tabName);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
            // Animate fade in
            targetPanel.style.opacity = '0';
            requestAnimationFrame(() => {
                targetPanel.style.transition = 'opacity 0.2s';
                targetPanel.style.opacity = '1';
            });
        }

        // Add active class to clicked tab
        const clickedTab = Array.from(document.querySelectorAll('.ketua-tab'))
            .find(tab => tab.getAttribute('onclick')?.includes(`'${tabName}'`));
        if (clickedTab) clickedTab.classList.add('active');

        // Update URL without reload
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url.toString());

        // Scroll to top of tabs on mobile
        if (window.innerWidth < 768) {
            document.querySelector('.ketua-tabs')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Sync tab dari URL saat halaman load
    document.addEventListener('DOMContentLoaded', function () {
        const urlTab = new URL(window.location.href).searchParams.get('tab');
        if (urlTab && document.getElementById('tab-' + urlTab)) {
            switchTab(urlTab);
        }
    });
</script>
@endpush
@endsection
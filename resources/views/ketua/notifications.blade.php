@extends('layouts.roles.ketua')

@section('title', 'Notification Center – UNICODE')
@section('page-title', 'Notifikasi')

@include('partials.roles.ketua.sidebar-aware')

@push('styles')
<style>
    .notif-row {
        transition: background-color .15s ease;
        position: relative;
    }
    .notif-row:hover { background-color: rgba(124, 58, 237, .03); }
    .dark .notif-row:hover { background-color: rgba(124, 58, 237, .05); }
    .notif-row.is-unread { background-color: rgba(139, 92, 246, .04); }
    .dark .notif-row.is-unread { background-color: rgba(139, 92, 246, .08); }
    .notif-row.is-unread::before {
        content: '';
        position: absolute;
        left: 0; top: 12%; bottom: 12%;
        width: 3px;
        background: #7c3aed;
        border-radius: 0 4px 4px 0;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .toast-stack {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        pointer-events: none;
    }
    .toast {
        pointer-events: auto;
        min-width: 280px;
        max-width: 400px;
        padding: .75rem 1rem;
        border-radius: .75rem;
        box-shadow: 0 12px 30px -8px rgba(0,0,0,.25);
        font-size: .8rem;
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        color: #fff;
        animation: toastIn .25s ease;
    }
    .toast.success { background: #10b981; }
    .toast.error   { background: #ef4444; }
    .toast.warn    { background: #f59e0b; }
    .toast.info    { background: #7c3aed; }
    @keyframes toastIn {
        from { opacity: 0; transform: translateX(24px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .toast.hide {
        opacity: 0; transform: translateX(24px);
        transition: all .25s ease;
    }

    /* Stat cards — responsive dinamis */
    .stats-grid-4 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: .75rem;
        transition: grid-template-columns .35s cubic-bezier(.4, 0, .2, 1);
    }

    /* Main grid — content + aside */
    .responsive-aside-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        transition: grid-template-columns .35s cubic-bezier(.4, 0, .2, 1);
    }
    @media (min-width: 1024px) {
        .responsive-aside-grid {
            grid-template-columns: minmax(0, 1fr) 340px;
        }
    }
</style>
@endpush

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], string $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
@endphp

{{-- page-shell: auto-expand when sidebar collapses --}}
<div class="page-shell">
<div class="page-content-narrow space-y-5">

    {{-- ═══════════════════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-purple-500 font-bold mb-1">
                Notification Center
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white
                       flex items-center gap-2">
                <i class="fas fa-bell text-purple-500"></i>
                Pusat Notifikasi
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola seluruh informasi, aktivitas, dan pemberitahuan akun Ketua.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" id="btn-mark-all"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                           bg-purple-600 hover:bg-purple-700 text-white
                           text-sm font-bold transition disabled:opacity-50">
                <i class="fas fa-check-double"></i>
                Tandai Semua Dibaca
            </button>
            <button type="button" id="btn-clear-read"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                           bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40
                           text-red-600 dark:text-red-400 text-sm font-bold transition">
                <i class="fas fa-broom"></i>
                Bersihkan Dibaca
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MAIN GRID: Content + Sidebar (auto-adjust)
         ═══════════════════════════════════════════════════════════ --}}
    <div class="responsive-aside-grid">

        {{-- ═══════════════════════════════════════════════════════
             KIRI: Tabs + Filter + List
             ═══════════════════════════════════════════════════════ --}}
        <div class="space-y-4 min-w-0">

            {{-- TABS --}}
            <div class="flex gap-1 overflow-x-auto pb-1
                        border-b border-gray-200 dark:border-gray-700 scrollbar-thin">
                @php
                    $tabs = [
                        'all'      => ['label' => 'Semua',        'icon' => 'fa-inbox'],
                        'unread'   => ['label' => 'Belum Dibaca', 'icon' => 'fa-circle-dot'],
                        'starred'  => ['label' => 'Favorit',      'icon' => 'fa-star'],
                        'archived' => ['label' => 'Arsip',        'icon' => 'fa-box-archive'],
                    ];
                @endphp

                @foreach ($tabs as $key => $meta)
                    <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'page' => null]) }}"
                       class="px-4 py-2.5 text-sm font-semibold rounded-t-lg
                              whitespace-nowrap transition border-b-2 -mb-px
                              flex items-center gap-1.5
                              {{ $tab === $key
                                  ? 'text-purple-700 dark:text-purple-300 border-purple-600 bg-purple-50 dark:bg-purple-900/20'
                                  : 'text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <i class="fas {{ $meta['icon'] }} text-xs"></i>
                        <span>{{ $meta['label'] }}</span>
                        @if (($counts[$key] ?? 0) > 0)
                            <span class="ml-0.5 text-[10px] px-1.5 py-0.5 rounded-full font-bold min-w-[18px] text-center
                                         {{ $tab === $key
                                             ? 'bg-purple-600 text-white'
                                             : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                {{ $counts[$key] > 99 ? '99+' : $counts[$key] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- TOOLBAR --}}
            <form method="GET" action="{{ $safeRoute('ketua.notifications.index') }}"
                  class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                         border border-gray-200 dark:border-gray-700 p-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2
                                  text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Cari judul atau isi notifikasi..."
                               autocomplete="off"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm
                                      border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white
                                      focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <select name="category"
                            class="rounded-xl text-sm px-3 py-2.5
                                   border-gray-300 dark:border-gray-600
                                   bg-gray-50 dark:bg-gray-700
                                   text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-purple-500">
                        @foreach ($categories as $key => $meta)
                            <option value="{{ $key }}"
                                {{ request('category', 'all') === $key ? 'selected' : '' }}>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>

                    <select name="range"
                            class="rounded-xl text-sm px-3 py-2.5
                                   border-gray-300 dark:border-gray-600
                                   bg-gray-50 dark:bg-gray-700
                                   text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-purple-500">
                        <option value="">Semua Waktu</option>
                        <option value="today" {{ request('range') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="7d"    {{ request('range') === '7d'    ? 'selected' : '' }}>7 Hari</option>
                        <option value="30d"   {{ request('range') === '30d'   ? 'selected' : '' }}>30 Hari</option>
                    </select>

                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-purple-600 hover:bg-purple-700 text-white transition
                                   flex items-center justify-center gap-1.5 shrink-0">
                        <i class="fas fa-filter text-xs"></i>
                        <span class="hidden md:inline">Filter</span>
                    </button>

                    @if (request()->filled('q') || request()->filled('category') || request()->filled('range'))
                        <a href="{{ $safeRoute('ketua.notifications.index', ['tab' => $tab]) }}"
                           class="px-4 py-2.5 rounded-xl text-sm font-semibold
                                  bg-gray-100 dark:bg-gray-700
                                  text-gray-600 dark:text-gray-300
                                  hover:bg-gray-200 dark:hover:bg-gray-600 transition
                                  flex items-center justify-center gap-1.5 shrink-0">
                            <i class="fas fa-times text-xs"></i>
                            <span class="hidden md:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            {{-- LIST --}}
            @if ($notifications->count() > 0)
                @foreach ($grouped as $label => $items)
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 px-1">
                            <div class="w-1 h-4 rounded-full bg-purple-500"></div>
                            <h3 class="text-xs font-bold uppercase tracking-wider
                                       text-gray-500 dark:text-gray-400">
                                {{ $label }}
                            </h3>
                            <span class="text-[10px] text-gray-400">
                                ({{ $items->count() }} notifikasi)
                            </span>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl
                                    border border-gray-200 dark:border-gray-700
                                    overflow-hidden shadow-sm divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($items as $notif)
                                @php
                                    $isUnread   = $notif->isUnread();
                                    $isStarred  = $notif->isStarred();
                                    $isArchived = $notif->isArchived();
                                    $priority   = $notif->priority ?? 'normal';

                                    $catColor = match($notif->category) {
                                        'member'    => 'blue',
                                        'event'     => 'amber',
                                        'financial' => 'emerald',
                                        'approval'  => 'violet',
                                        'system'    => 'slate',
                                        default     => 'purple',
                                    };
                                    $catLabel = $categories[$notif->category]['label']
                                        ?? ucfirst($notif->category);
                                @endphp

                                <div class="notif-row group flex items-start gap-3 p-4
                                            {{ $isUnread ? 'is-unread' : '' }}"
                                     data-notif-id="{{ $notif->id }}">

                                    <div class="shrink-0 w-10 h-10 rounded-xl
                                                bg-{{ $catColor }}-500/15
                                                text-{{ $catColor }}-500
                                                flex items-center justify-center">
                                        <i class="fas {{ $notif->iconOrDefault() }}"></i>
                                    </div>

                                    <a href="{{ $safeRoute('ketua.notifications.show', $notif->id) }}"
                                       data-action="open"
                                       data-id="{{ $notif->id }}"
                                       data-read="{{ $isUnread ? 0 : 1 }}"
                                       class="flex-1 min-w-0 block">

                                        <div class="flex items-start gap-2 mb-0.5">
                                            <p class="text-sm font-semibold
                                                      text-gray-900 dark:text-white
                                                      flex-1 min-w-0 truncate">
                                                {{ $notif->title }}
                                            </p>
                                            @if ($isUnread)
                                                <span class="shrink-0 w-2 h-2 bg-purple-500 rounded-full mt-1.5
                                                             ring-2 ring-purple-100 dark:ring-purple-900/40"></span>
                                            @endif
                                            @if ($isStarred)
                                                <i class="fas fa-star text-amber-400 text-xs mt-1 shrink-0"></i>
                                            @endif
                                        </div>

                                        @if ($notif->message)
                                            <p class="text-xs text-gray-500 dark:text-gray-400
                                                      line-clamp-2 leading-relaxed">
                                                {{ $notif->message }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-2 mt-2
                                                    text-[10px] text-gray-400">
                                            <span class="inline-flex items-center gap-1">
                                                <i class="far fa-clock"></i>
                                                {{ $notif->created_at->diffForHumans() }}
                                            </span>

                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full
                                                         bg-{{ $catColor }}-500/10
                                                         text-{{ $catColor }}-600 dark:text-{{ $catColor }}-400
                                                         font-semibold">
                                                {{ $catLabel }}
                                            </span>

                                            @if ($priority !== 'normal')
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full font-bold
                                                             {{ $priority === 'urgent'
                                                                 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                                                 : ($priority === 'high'
                                                                     ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                                                     : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300') }}">
                                                    <span class="w-1 h-1 rounded-full bg-current"></span>
                                                    {{ ucfirst($priority) }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>

                                    <div class="shrink-0 flex items-center gap-0.5
                                                md:opacity-0 md:group-hover:opacity-100 transition">
                                        <button type="button" data-action="toggle-read"
                                                data-id="{{ $notif->id }}"
                                                data-read="{{ $isUnread ? 0 : 1 }}"
                                                class="w-8 h-8 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20
                                                       flex items-center justify-center transition"
                                                title="{{ $isUnread ? 'Tandai dibaca' : 'Tandai belum dibaca' }}">
                                            <i class="fas {{ $isUnread ? 'fa-check' : 'fa-circle-dot' }}
                                                      text-xs text-gray-400 hover:text-purple-500"></i>
                                        </button>

                                        <button type="button" data-action="star"
                                                data-id="{{ $notif->id }}"
                                                data-starred="{{ $isStarred ? 1 : 0 }}"
                                                class="w-8 h-8 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/20
                                                       flex items-center justify-center transition"
                                                title="{{ $isStarred ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}">
                                            <i class="fas fa-star text-xs
                                                      {{ $isStarred ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        </button>

                                        <button type="button"
                                                data-action="{{ $isArchived ? 'unarchive' : 'archive' }}"
                                                data-id="{{ $notif->id }}"
                                                class="w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                                                       flex items-center justify-center transition"
                                                title="{{ $isArchived ? 'Kembalikan dari arsip' : 'Arsipkan' }}">
                                            <i class="fas {{ $isArchived ? 'fa-box-open' : 'fa-box-archive' }}
                                                      text-xs text-gray-400"></i>
                                        </button>

                                        @if (! $isUnread)
                                            <button type="button" data-action="delete"
                                                    data-id="{{ $notif->id }}"
                                                    class="w-8 h-8 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20
                                                           flex items-center justify-center transition"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs text-gray-400 hover:text-red-500"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center
                                                           opacity-30 cursor-not-allowed"
                                                    title="Buka dulu untuk menghapus">
                                                <i class="fas fa-trash text-xs text-gray-300 dark:text-gray-600"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if ($notifications->hasPages())
                    <div class="flex justify-center pt-2">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl
                            border border-gray-200 dark:border-gray-700 p-12 text-center">
                    @php
                        $isEmptyFilter = request()->filled('q') || request()->filled('category') || request()->filled('range');
                        $empty = match($tab) {
                            'unread'   => ['🎉', 'Semua notifikasi sudah dibaca', 'Kerja bagus! Anda tidak punya notifikasi yang belum dibaca.'],
                            'starred'  => ['⭐', 'Belum ada favorit',             'Tandai notifikasi penting sebagai favorit agar mudah dicari.'],
                            'archived' => ['📦', 'Arsip masih kosong',           'Notifikasi yang diarsipkan akan muncul di sini.'],
                            default    => $isEmptyFilter
                                ? ['🔍', 'Tidak ada hasil',  'Coba ubah kata kunci atau reset filter pencarian.']
                                : ['🔔', 'Belum ada notifikasi', 'Notifikasi baru akan muncul di sini saat ada aktivitas.'],
                        };
                    @endphp
                    <div class="text-5xl mb-4">{{ $empty[0] }}</div>
                    <h3 class="text-base font-bold text-gray-700 dark:text-gray-200 mb-1">{{ $empty[1] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">{{ $empty[2] }}</p>
                    @if ($isEmptyFilter)
                        <a href="{{ $safeRoute('ketua.notifications.index', ['tab' => $tab]) }}"
                           class="mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                  bg-gray-100 dark:bg-gray-700
                                  hover:bg-gray-200 dark:hover:bg-gray-600
                                  text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                            <i class="fas fa-times text-xs"></i> Reset Filter
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════
             KANAN: Sidebar Stats (sticky)
             ═══════════════════════════════════════════════════════ --}}
        <aside class="space-y-4 lg:sticky lg:top-20 self-start">

            {{-- Ringkasan --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white
                           flex items-center gap-2 mb-4">
                    <i class="fas fa-chart-simple text-purple-500"></i>
                    Ringkasan Notifikasi
                </h3>

                <div class="stats-grid-4">
                    @php
                        $statCards = [
                            ['label' => 'Total',       'value' => $stats['total'],     'icon' => 'fa-bell',         'color' => 'purple',  'sub' => 'semua'],
                            ['label' => 'Belum Dibaca','value' => $stats['unread'],    'icon' => 'fa-envelope',     'color' => 'red',     'sub' => 'perlu perhatian'],
                            ['label' => 'Penting',     'value' => $stats['important'], 'icon' => 'fa-exclamation',  'color' => 'amber',   'sub' => 'prioritas tinggi'],
                            ['label' => 'Hari Ini',    'value' => $stats['today'],     'icon' => 'fa-calendar-day', 'color' => 'emerald', 'sub' => 'baru'],
                        ];
                    @endphp

                    @foreach ($statCards as $s)
                        <div class="rounded-xl bg-{{ $s['color'] }}-500/8
                                    border border-{{ $s['color'] }}-500/20 p-3">
                            <div class="flex items-center gap-1.5 mb-1.5">
                                <i class="fas {{ $s['icon'] }}
                                          text-{{ $s['color'] }}-500 text-[10px]"></i>
                                <span class="text-[9px] font-bold uppercase tracking-wider
                                             text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400 truncate">
                                    {{ $s['label'] }}
                                </span>
                            </div>
                            <p class="text-xl font-extrabold
                                      text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400">
                                {{ number_format($s['value']) }}
                            </p>
                            <p class="text-[9px] text-gray-400 mt-0.5 truncate">{{ $s['sub'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl
                        border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white
                           flex items-center gap-2 mb-3">
                    <i class="fas fa-bolt text-amber-500"></i> Aksi Cepat
                </h3>

                <div class="space-y-2">
                    @php
                        $quickActions = [
                            ['tab' => 'unread',   'label' => 'Belum Dibaca', 'icon' => 'fa-circle-dot', 'color' => 'purple', 'count' => $counts['unread']],
                            ['tab' => 'starred',  'label' => 'Favorit',      'icon' => 'fa-star',       'color' => 'amber',  'count' => $counts['starred']],
                            ['tab' => 'archived', 'label' => 'Arsip',        'icon' => 'fa-box-archive','color' => 'gray',   'count' => $counts['archived']],
                        ];
                    @endphp

                    @foreach ($quickActions as $qa)
                        <a href="{{ $safeRoute('ketua.notifications.index', ['tab' => $qa['tab']]) }}"
                           class="flex items-center gap-3 p-3 rounded-xl
                                  bg-gray-50 dark:bg-gray-900/40
                                  hover:bg-{{ $qa['color'] }}-50 dark:hover:bg-{{ $qa['color'] }}-900/20
                                  transition group">
                            <div class="w-9 h-9 rounded-lg bg-{{ $qa['color'] }}-500/15
                                        text-{{ $qa['color'] }}-500
                                        flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $qa['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-100">
                                    {{ $qa['label'] }}
                                </p>
                                <p class="text-[10px] text-gray-500">{{ $qa['count'] }} notifikasi</p>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-gray-400
                                      group-hover:text-{{ $qa['color'] }}-500 transition"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Info panel --}}
            <div class="rounded-2xl border border-purple-200 dark:border-purple-900
                        bg-purple-50 dark:bg-purple-900/10 p-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-500/15 text-purple-500
                                flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-sm"></i>
                    </div>
                    <div class="text-xs">
                        <p class="font-bold text-purple-800 dark:text-purple-200 mb-1">Info</p>
                        <p class="text-purple-700/80 dark:text-purple-300/80 leading-relaxed">
                            Halaman ini auto-expand saat sidebar ditutup, dan
                            sinkron setiap 30 detik.
                        </p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
</div>

<div id="notif-toast" class="toast-stack"></div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
              || '{{ csrf_token() }}';

    const URLS = {
        poll:      '{{ route('ketua.notifications.poll') }}',
        unread:    '{{ route('ketua.notifications.unread-count') }}',
        markAll:   '{{ route('ketua.notifications.mark-all-read') }}',
        clearRead: '{{ route('ketua.notifications.destroy-all') }}',
        readTpl:   '{{ route('ketua.notifications.read', ['notification' => '__ID__']) }}',
        unreadTpl: '{{ route('ketua.notifications.unread', ['notification' => '__ID__']) }}',
        starTpl:   '{{ route('ketua.notifications.toggle-star', ['notification' => '__ID__']) }}',
        archiveTpl:'{{ route('ketua.notifications.archive', ['notification' => '__ID__']) }}',
        unarchiveTpl:'{{ route('ketua.notifications.unarchive', ['notification' => '__ID__']) }}',
        deleteTpl: '{{ route('ketua.notifications.destroy', ['notification' => '__ID__']) }}',
    };

    let lastNotifId = 0;
    let pollTimer   = null;

    async function api(url, method = 'GET', body = null) {
        const opts = {
            method,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            credentials: 'same-origin',
        };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        try {
            const res = await fetch(url, opts);
            let data = null;
            try { data = await res.json(); } catch (e) {}
            return { ok: res.ok, data, status: res.status };
        } catch (err) {
            return { ok: false, data: null, status: 0 };
        }
    }

    function toast(msg, type = 'info', duration = 3200) {
        const box = document.getElementById('notif-toast');
        if (! box) return;
        const t = document.createElement('div');
        t.className = `toast ${type}`;
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warn: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        t.innerHTML = `<i class="fas ${icons[type] || icons.info} mt-0.5"></i><div class="flex-1">${msg}</div>`;
        box.appendChild(t);
        setTimeout(() => {
            t.classList.add('hide');
            setTimeout(() => t.remove(), 300);
        }, duration);
    }

    function syncGlobalBadge() {
        api(URLS.unread).then(({ ok, data }) => {
            if (! ok || ! data) return;
            const count = data.count || 0;
            const b = document.getElementById('sidebar-notif-badge');
            if (b) {
                if (count > 0) {
                    b.textContent = count > 99 ? '99+' : count;
                    b.classList.remove('hidden');
                    b.classList.add('inline-flex');
                } else {
                    b.classList.add('hidden');
                    b.classList.remove('inline-flex');
                }
            }
        });
    }

    document.addEventListener('click', async (e) => {
        const openLink = e.target.closest('[data-action="open"]');
        if (openLink) {
            if (openLink.dataset.read === '0') {
                const id = openLink.dataset.id;
                const row = document.querySelector(`[data-notif-id="${id}"]`);
                row?.classList.remove('is-unread');
                row?.querySelector('.bg-purple-500.rounded-full')?.remove();
                openLink.dataset.read = '1';
                fetch(URLS.readTpl.replace('__ID__', id), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                }).then(() => syncGlobalBadge());
            }
            return;
        }

        const toggleRead = e.target.closest('[data-action="toggle-read"]');
        if (toggleRead) {
            e.preventDefault();
            const id = toggleRead.dataset.id;
            const isRead = toggleRead.dataset.read === '1';
            const url = isRead ? URLS.unreadTpl : URLS.readTpl;
            const { ok } = await api(url.replace('__ID__', id), 'POST');
            if (ok) {
                const row = document.querySelector(`[data-notif-id="${id}"]`);
                const openA = row?.querySelector('[data-action="open"]');
                const icon = toggleRead.querySelector('i');
                if (isRead) {
                    row?.classList.add('is-unread');
                    toggleRead.dataset.read = '0';
                    if (openA) openA.dataset.read = '0';
                    icon.className = 'fas fa-check text-xs text-gray-400 hover:text-purple-500';
                    toast('Ditandai belum dibaca', 'info');
                } else {
                    row?.classList.remove('is-unread');
                    toggleRead.dataset.read = '1';
                    if (openA) openA.dataset.read = '1';
                    icon.className = 'fas fa-circle-dot text-xs text-gray-400 hover:text-purple-500';
                    toast('Ditandai sudah dibaca', 'success');
                }
                syncGlobalBadge();
            }
        }

        const starBtn = e.target.closest('[data-action="star"]');
        if (starBtn) {
            e.preventDefault();
            const id = starBtn.dataset.id;
            const wasStarred = starBtn.dataset.starred === '1';
            const { ok, data } = await api(URLS.starTpl.replace('__ID__', id), 'POST');
            if (ok) {
                const icon = starBtn.querySelector('i');
                starBtn.dataset.starred = wasStarred ? '0' : '1';
                icon.classList.toggle('text-amber-400', ! wasStarred);
                icon.classList.toggle('text-gray-300', wasStarred);
                icon.classList.toggle('dark:text-gray-600', wasStarred);
                toast(data?.message || 'Diperbarui', 'success');
            }
        }

        const archiveBtn = e.target.closest('[data-action="archive"]');
        if (archiveBtn) {
            e.preventDefault();
            const id = archiveBtn.dataset.id;
            const { ok } = await api(URLS.archiveTpl.replace('__ID__', id), 'POST');
            if (ok) {
                document.querySelector(`[data-notif-id="${id}"]`)?.remove();
                toast('Notifikasi diarsipkan', 'success');
            }
        }

        const unarchiveBtn = e.target.closest('[data-action="unarchive"]');
        if (unarchiveBtn) {
            e.preventDefault();
            const id = unarchiveBtn.dataset.id;
            const { ok } = await api(URLS.unarchiveTpl.replace('__ID__', id), 'POST');
            if (ok) {
                document.querySelector(`[data-notif-id="${id}"]`)?.remove();
                toast('Notifikasi dikembalikan', 'success');
            }
        }

        const delBtn = e.target.closest('[data-action="delete"]');
        if (delBtn) {
            e.preventDefault();
            if (! confirm('Hapus notifikasi ini?')) return;
            const id = delBtn.dataset.id;
            const { ok, data } = await api(URLS.deleteTpl.replace('__ID__', id), 'DELETE');
            if (ok && data?.success) {
                document.querySelector(`[data-notif-id="${id}"]`)?.remove();
                toast('Notifikasi dihapus', 'success');
            }
        }
    });

    document.getElementById('btn-mark-all')?.addEventListener('click', async function () {
        if (! confirm('Tandai semua notifikasi sebagai dibaca?')) return;
        this.disabled = true;
        const original = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        const { ok } = await api(URLS.markAll, 'POST');
        if (ok) {
            document.querySelectorAll('.notif-row.is-unread').forEach(el => {
                el.classList.remove('is-unread');
                el.querySelector('.bg-purple-500.rounded-full')?.remove();
            });
            document.querySelectorAll('[data-action="open"]').forEach(a => a.dataset.read = '1');
            document.querySelectorAll('[data-action="toggle-read"]').forEach(b => b.dataset.read = '1');
            toast('Semua ditandai dibaca', 'success');
            syncGlobalBadge();
        } else {
            toast('Gagal', 'error');
        }
        this.disabled = false;
        this.innerHTML = original;
    });

    document.getElementById('btn-clear-read')?.addEventListener('click', async function () {
        if (! confirm('Hapus semua notifikasi yang sudah dibaca?')) return;
        this.disabled = true;
        const original = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        const { ok, data } = await api(URLS.clearRead, 'DELETE');
        if (ok && data?.success) {
            toast(data.message || 'Dibersihkan', 'success');
            setTimeout(() => window.location.reload(), 700);
        } else {
            this.disabled = false;
            this.innerHTML = original;
            toast('Gagal', 'error');
        }
    });

    async function poll() {
        if (document.hidden) return;
        const { ok, data } = await api(`${URLS.poll}?last_id=${lastNotifId}`);
        if (! ok || ! data) return;
        if (data.latest_id > lastNotifId && lastNotifId > 0 && data.has_new) {
            const newest = data.items[0];
            toast(`🔔 Notifikasi baru: <strong>${newest.title}</strong>`, 'info', 5000);
            syncGlobalBadge();
        }
        lastNotifId = data.latest_id || lastNotifId;
    }

    const firstRow = document.querySelector('[data-notif-id]');
    if (firstRow) lastNotifId = parseInt(firstRow.dataset.notifId, 10) || 0;

    pollTimer = setInterval(poll, 30000);
    document.addEventListener('visibilitychange', () => { if (! document.hidden) poll(); });
    window.addEventListener('beforeunload', () => { if (pollTimer) clearInterval(pollTimer); });
})();
</script>
@endpush
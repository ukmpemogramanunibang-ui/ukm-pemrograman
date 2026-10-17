@extends('layouts.roles.owner')

@section('title', 'Pengaturan Umum – Owner UNICODE')
@section('page-title', 'Pengaturan Umum')

@section('content')
@php
    $cards               = $cards               ?? collect();
    $standaloneShortcuts = $standaloneShortcuts ?? collect();
    $orphanShortcuts     = $orphanShortcuts     ?? collect();

    $totalShortcuts = $cards->sum(fn ($c) => $c->shortcuts->count())
        + $standaloneShortcuts->count();
@endphp

<div class="space-y-6" x-data="dashboardBuilder()">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.15em] text-cyan-600 dark:text-cyan-400 font-bold mb-1">
                System Control Center
            </p>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 dark:text-white">
                Dashboard Builder
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 max-w-2xl">
                Buat folder & shortcut sekali di sini — otomatis muncul di dashboard semua role sesuai target.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.settings.dashboard-cards.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold
                      shadow-lg shadow-cyan-500/20 transition">
                <i class="fas fa-folder-plus"></i> Create Folder
            </a>
            <a href="{{ route('owner.settings.dashboard-shortcuts.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100
                      text-white dark:text-gray-900 text-sm font-semibold shadow-lg transition">
                <i class="fas fa-link"></i> Create Shortcut
            </a>
        </div>
    </div>

    {{-- ============================================================
         TABS
         ============================================================ --}}
    @include('owner.settings._sidebar')

    {{-- ============================================================
         SMART ENGINE INFO
         ============================================================ --}}
    <div class="rounded-2xl border-2 border-cyan-500/30 dark:border-cyan-500/40
                bg-gradient-to-r from-cyan-50 via-blue-50 to-cyan-50
                dark:from-cyan-950/40 dark:via-blue-950/40 dark:to-cyan-950/40
                p-5 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl
                        bg-gradient-to-br from-cyan-500 to-blue-500
                        text-white flex items-center justify-center flex-shrink-0
                        shadow-lg shadow-cyan-500/30">
                <i class="fas fa-brain text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-base text-cyan-900 dark:text-cyan-100 flex items-center gap-2">
                    Folder-Based Dashboard
                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-700 dark:text-cyan-200 text-[10px] font-bold uppercase">
                        Auto
                    </span>
                </h3>
                <p class="text-sm text-cyan-800 dark:text-cyan-200 mt-1 leading-relaxed">
                    Folder adalah container, Shortcut adalah isi di dalamnya. Akses & jadwal folder otomatis
                    berlaku untuk semua shortcut di dalamnya.
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS
         ============================================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
            $stats_list = [
                ['key' => 'total_cards',      'label' => 'Total Folder',      'icon' => 'fa-folder',       'from' => 'cyan',    'to' => 'blue'],
                ['key' => 'active_cards',     'label' => 'Folder Aktif',      'icon' => 'fa-folder-open',  'from' => 'emerald', 'to' => 'green'],
                ['key' => 'total_shortcuts',  'label' => 'Total Shortcut',    'icon' => 'fa-link',         'from' => 'purple',  'to' => 'fuchsia'],
                ['key' => 'active_shortcuts', 'label' => 'Shortcut Aktif',    'icon' => 'fa-bolt',         'from' => 'amber',   'to' => 'orange'],
            ];
        @endphp

        @foreach ($stats_list as $s)
            <div class="group relative overflow-hidden rounded-2xl
                        bg-white dark:bg-gray-800
                        border border-gray-200 dark:border-gray-700
                        p-4 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full
                            bg-{{ $s['from'] }}-500/10 group-hover:bg-{{ $s['from'] }}-500/20
                            blur-2xl transition-all duration-500"></div>

                <div class="relative flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] uppercase tracking-wider
                                  text-gray-500 dark:text-gray-400 font-bold truncate">
                            {{ $s['label'] }}
                        </p>
                        <p class="text-3xl font-extrabold
                                  text-gray-900 dark:text-white mt-1 leading-none">
                            {{ $stats[$s['key']] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex-shrink-0
                                bg-gradient-to-br from-{{ $s['from'] }}-500 to-{{ $s['to'] }}-600
                                text-white flex items-center justify-center shadow-lg">
                        <i class="fas {{ $s['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============================================================
         WARNING: ORPHAN SHORTCUTS
         ============================================================ --}}
    @if ($orphanShortcuts->count() > 0)
        <div class="rounded-2xl border-2 border-amber-300 dark:border-amber-800
                    bg-amber-50 dark:bg-amber-900/20 p-5">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400
                            flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                        ⚠ {{ $orphanShortcuts->count() }} Shortcut Kehilangan Folder
                    </h3>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-1">
                        Shortcut berikut tidak lagi terhubung ke folder induk. Silakan edit & pilih folder baru.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($orphanShortcuts->take(8) as $sc)
                            <a href="{{ route('owner.settings.dashboard-shortcuts.edit', $sc) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                      bg-white dark:bg-gray-800 border border-amber-300 dark:border-amber-700
                                      text-xs font-semibold text-amber-700 dark:text-amber-300
                                      hover:bg-amber-100 dark:hover:bg-amber-900/30 transition">
                                <i class="fas {{ $sc->icon ?: 'fa-link' }} text-[10px]"></i>
                                {{ $sc->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         WARNING: STANDALONE SHORTCUTS
         ============================================================ --}}
    @if ($standaloneShortcuts->count() > 0)
        <div class="rounded-2xl border-2 border-blue-300 dark:border-blue-800
                    bg-blue-50 dark:bg-blue-900/20 p-5">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-500/20 text-blue-600 dark:text-blue-400
                            flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-blue-900 dark:text-blue-200 text-sm">
                        {{ $standaloneShortcuts->count() }} Shortcut Belum di Folder
                    </h3>
                    <p class="text-xs text-blue-800 dark:text-blue-300 mt-1">
                        Di konsep folder-based, semua shortcut sebaiknya berada di dalam folder.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach ($standaloneShortcuts->take(8) as $sc)
                            <a href="{{ route('owner.settings.dashboard-shortcuts.edit', $sc) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg
                                      bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-700
                                      text-xs font-semibold text-blue-700 dark:text-blue-300
                                      hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                                <i class="fas {{ $sc->icon ?: 'fa-link' }} text-[10px]"></i>
                                {{ $sc->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         DASHBOARD FOLDERS
         ============================================================ --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-folder-open text-cyan-500"></i>
                    Dashboard Folders
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $cards->count() }} folder ·
                    {{ $totalShortcuts }} shortcut total
                    @if ($cards->where('is_active', false)->count() > 0)
                        · <span class="text-amber-500 font-semibold">
                            {{ $cards->where('is_active', false)->count() }} nonaktif
                          </span>
                    @endif
                </p>
            </div>

            @if ($cards->count() > 0)
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" x-model="searchCard"
                               placeholder="🔎 Cari folder..."
                               class="pl-9 pr-3 py-2 text-sm rounded-xl
                                      bg-white dark:bg-gray-800
                                      border border-gray-200 dark:border-gray-700
                                      text-gray-900 dark:text-white
                                      placeholder-gray-400 dark:placeholder-gray-500
                                      focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500
                                      outline-none transition w-48">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>

                    <select x-model="filterCategory"
                            class="px-3 py-2 text-sm rounded-xl bg-white dark:bg-gray-800
                                   border border-gray-200 dark:border-gray-700
                                   text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-cyan-500/30 outline-none">
                        <option value="">Semua</option>
                        <option value="formal">Formal</option>
                        <option value="non_formal">Non Formal</option>
                    </select>
                </div>
            @endif
        </div>

        @if ($cards->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach ($cards as $card)
                    @php
                        $shortcutsList = $card->shortcuts;
                        $targetLabel = match ($card->audience_mode) {
                            'roles' => 'Role: ' . ($card->targets->pluck('role.name')->filter()->take(2)->implode(', ') ?: '-'),
                            'users' => 'Pribadi: ' . ($card->targets->pluck('user.name')->filter()->take(2)->implode(', ') ?: '-'),
                            default => 'Semua Akun',
                        };
                        $searchable = strtolower(($card->name ?? '') . ' ' . $card->category);
                        $isExpired  = $card->isExpired();
                    @endphp

                    <div x-show="matchCard('{{ $searchable }}', '{{ $card->category }}')"
                         x-transition
                         class="group relative rounded-2xl overflow-hidden
                                bg-white dark:bg-gray-800
                                border border-gray-200 dark:border-gray-700
                                shadow-sm hover:shadow-xl hover:border-cyan-500/30
                                transition-all duration-300 flex flex-col">

                        {{-- FOLDER HEADER --}}
                        <div class="relative p-5 {{ $card->text_color }}"
                             style="{{ $card->background_style }}">
                            <div class="flex justify-between items-start gap-2">
                                <div class="w-12 h-12 rounded-xl {{ $card->icon_bg }} backdrop-blur
                                            flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $card->icon ?: 'fa-folder' }} text-xl"></i>
                                </div>
                                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                                                 {{ $card->badge_bg }} backdrop-blur whitespace-nowrap">
                                        {{ $card->category === 'formal' ? 'Formal' : 'Non Formal' }}
                                    </span>
                                    @if (!$card->is_active)
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                                                     bg-red-500 text-white shadow whitespace-nowrap">
                                            <i class="fas fa-pause"></i> Nonaktif
                                        </span>
                                    @elseif ($isExpired)
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                                                     bg-orange-500 text-white shadow whitespace-nowrap">
                                            <i class="fas fa-hourglass-end"></i> Expired
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <h3 class="mt-4 text-xl font-extrabold drop-shadow-sm truncate">
                                {{ $card->name }}
                            </h3>
                            <p class="text-xs {{ $card->subtext_color }} mt-0.5">
                                <i class="fas fa-link mr-1"></i>
                                {{ $shortcutsList->count() }} shortcut
                                · {{ $card->schedule_label }}
                            </p>
                        </div>

                        {{-- SHORTCUT ICON GRID — FULL SIZE --}}
<div class="flex-1 px-3 py-4 bg-gradient-to-b from-gray-50 to-gray-100/60
            dark:from-gray-900/40 dark:to-gray-900/20
            border-y border-gray-100 dark:border-gray-700">
    @if ($shortcutsList->count())
        <div class="grid grid-cols-3 gap-3">
            @foreach ($shortcutsList->take(9) as $sc)
                <a href="{{ $sc->url }}"
                   @if ($sc->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                   title="{{ $sc->name }} — {{ $sc->url }}"
                   class="relative flex flex-col items-center gap-2 p-1.5 rounded-2xl
                          hover:bg-white dark:hover:bg-gray-800
                          hover:shadow-md hover:-translate-y-0.5
                          transition-all duration-200 group/sc">

                    {{-- ICON FULL SIZE --}}
                    <div class="w-full aspect-square rounded-2xl overflow-hidden
                                bg-white dark:bg-gray-800
                                border border-gray-200 dark:border-gray-700
                                group-hover/sc:border-cyan-400 dark:group-hover/sc:border-cyan-500
                                group-hover/sc:shadow-lg
                                flex items-center justify-center
                                transition-all duration-200">

                        @if ($sc->background_image)
                            <img src="{{ asset('storage/' . $sc->background_image) }}"
                                 alt="{{ $sc->name }}"
                                 class="w-full h-full object-cover
                                        group-hover/sc:scale-105 transition-transform">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-cyan-500/10 to-blue-500/10
                                        flex items-center justify-center
                                        group-hover/sc:from-cyan-500/20 group-hover/sc:to-blue-500/20
                                        transition-all">
                                <i class="fas {{ $sc->icon ?: 'fa-link' }} text-3xl
                                          text-cyan-500 dark:text-cyan-400
                                          group-hover/sc:scale-110 transition-transform"></i>
                            </div>
                        @endif

                        {{-- Badge nonaktif --}}
                        @if (!$sc->is_active)
                            <span class="absolute top-2 right-2 w-3.5 h-3.5 rounded-full
                                         bg-red-500 border-2 border-white dark:border-gray-900
                                         shadow" title="Nonaktif"></span>
                        @endif
                    </div>

                    {{-- NAME DI BAWAH ICON --}}
                    <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300
                                 text-center w-full truncate leading-tight px-0.5
                                 group-hover/sc:text-cyan-600 dark:group-hover/sc:text-cyan-400
                                 transition-colors">
                        {{ $sc->name }}
                    </span>
                </a>
            @endforeach
        </div>

        @if ($shortcutsList->count() > 9)
            <div class="mt-3 text-center">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full
                             bg-cyan-100 dark:bg-cyan-900/30
                             text-[11px] font-bold
                             text-cyan-700 dark:text-cyan-300">
                    <i class="fas fa-plus-circle text-[10px]"></i>
                    {{ $shortcutsList->count() - 9 }} shortcut lainnya
                </span>
            </div>
        @endif
    @else
        <div class="py-4 text-center">
            <div class="inline-flex items-center gap-1.5 text-[11px]
                        text-gray-400 dark:text-gray-500 italic">
                <i class="fas fa-inbox text-[10px]"></i>
                Belum ada shortcut di folder ini
            </div>
        </div>
    @endif
</div>

                        {{-- META & ACTIONS --}}
                        <div class="p-4">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             bg-cyan-50 dark:bg-cyan-900/20
                                             text-cyan-700 dark:text-cyan-300 text-[10px] font-semibold">
                                    <i class="fas fa-users"></i> {{ $targetLabel }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             bg-gray-100 dark:bg-gray-700
                                             text-gray-600 dark:text-gray-300 text-[10px] font-semibold">
                                    <i class="fas fa-fire"></i> P{{ $card->priority }}
                                </span>
                            </div>

                            <div class="grid grid-cols-4 gap-1.5">
                                {{-- Tambah Shortcut --}}
                                <a href="{{ route('owner.settings.dashboard-shortcuts.create', ['parent_id' => $card->id]) }}"
                                   title="Tambah Shortcut"
                                   class="flex items-center justify-center px-2 py-2 rounded-lg
                                          bg-emerald-50 dark:bg-emerald-900/20
                                          text-emerald-600 dark:text-emerald-400
                                          hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition">
                                    <i class="fas fa-plus text-xs"></i>
                                </a>

                                {{-- Edit Folder --}}
                                <a href="{{ route('owner.settings.dashboard-cards.edit', $card) }}"
                                   title="Edit Folder"
                                   class="flex items-center justify-center px-2 py-2 rounded-lg
                                          bg-blue-50 dark:bg-blue-900/20
                                          text-blue-600 dark:text-blue-400
                                          hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>

                                {{-- Toggle Active --}}
                                <form method="POST" action="{{ route('owner.settings.dashboard-cards.toggle', $card) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            title="{{ $card->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            class="w-full flex items-center justify-center px-2 py-2 rounded-lg
                                                   bg-amber-50 dark:bg-amber-900/20
                                                   text-amber-600 dark:text-amber-400
                                                   hover:bg-amber-100 dark:hover:bg-amber-900/30 transition">
                                        <i class="fas fa-{{ $card->is_active ? 'pause' : 'play' }} text-xs"></i>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('owner.settings.dashboard-cards.destroy', $card) }}"
                                      onsubmit="return confirm('Pindahkan folder dan semua shortcut ke Trash?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            title="Hapus"
                                            class="w-full flex items-center justify-center px-2 py-2 rounded-lg
                                                   bg-red-50 dark:bg-red-900/20
                                                   text-red-600 dark:text-red-400
                                                   hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- EMPTY STATE --}}
            <div class="rounded-2xl border-2 border-dashed
                        border-gray-300 dark:border-gray-700
                        bg-gray-50/50 dark:bg-gray-900/30
                        p-10 text-center">
                <div class="w-16 h-16 rounded-2xl
                            bg-gradient-to-br from-cyan-500/10 to-blue-500/10
                            mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-2xl text-cyan-500 dark:text-cyan-400"></i>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-white">Belum ada Folder</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Buat folder pertama untuk mengelompokkan shortcut di dashboard.
                </p>
                <a href="{{ route('owner.settings.dashboard-cards.create') }}"
                   class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 rounded-xl
                          bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold
                          shadow-lg shadow-cyan-500/20 transition">
                    <i class="fas fa-folder-plus"></i> Create Folder
                </a>
            </div>
        @endif
    </section>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function dashboardBuilder() {
    return {
        searchCard: '',
        filterCategory: '',

        matchCard(searchable, category) {
            const q   = (this.searchCard || '').toLowerCase().trim();
            const cat = this.filterCategory;
            if (cat && category !== cat) return false;
            if (!q) return true;
            return searchable.includes(q);
        },
    };
}
</script>
@endpush
@endsection
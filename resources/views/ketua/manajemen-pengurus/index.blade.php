@extends('layouts.ketua')

@section('title', 'Manajemen Pengurus')

@section('content')
@php
    $safeRoute = fn (string $name, $params = [], string $fallback = '#') =>
        \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;

    $totalOfficial    = $officials->total();
    $totalKetua       = $officials->getCollection()->where('level_id', 3)->count();
    $totalPengurus    = $officials->getCollection()->where('level_id', 4)->count();
    $withJabatan      = $officials->getCollection()
        ->filter(fn ($o) => $o->jabatans && $o->jabatans->count() > 0)->count();
    $tanpaJabatan     = $totalOfficial - $withJabatan;

    $availableRoles = $availableRoles ?? collect([
        (object) ['id' => 3, 'name' => 'Ketua'],
        (object) ['id' => 4, 'name' => 'Pengurus'],
        (object) ['id' => 5, 'name' => 'Anggota'],
    ]);
    $jabatans = $jabatans ?? collect();

    /* Kandidat transfer ketua */
    $eligibleCandidates = $eligibleCandidates ?? [];
@endphp

<div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto" id="pengurus-app">

    {{-- ═══ HERO ═══ --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-500
                p-6 sm:p-8 shadow-2xl shadow-indigo-500/20">
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-white/15 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-cyan-300/25 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/20 backdrop-blur-xl
                                border border-white/30 flex items-center justify-center shadow-2xl">
                        <i class="fas fa-user-tie text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full
                                 bg-emerald-400 border-2 border-white dark:border-gray-900 animate-pulse"></span>
                </div>
                <div>
                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur text-white
                                 text-[10px] font-bold uppercase tracking-widest border border-white/20">
                        <i class="fas fa-star text-[8px] mr-1"></i> Manajemen
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mt-2">
                        Ketua & Pengurus UKM
                    </h1>
                    <p class="text-sm text-white/80 mt-1">Kelola data ketua dan pengurus</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="px-4 py-3 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/20">
                    <p class="text-white/70 text-[9px] font-bold uppercase tracking-widest mb-1">Total</p>
                    <span class="text-white text-xl font-black tabular-nums">{{ number_format($totalOfficial) }}</span>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/20">
                    <p class="text-white/70 text-[9px] font-bold uppercase tracking-widest mb-1">Ketua</p>
                    <span class="text-white text-xl font-black tabular-nums">{{ $totalKetua }}</span>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/20">
                    <p class="text-white/70 text-[9px] font-bold uppercase tracking-widest mb-1">Pengurus</p>
                    <span class="text-white text-xl font-black tabular-nums">{{ $totalPengurus }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ACTION BAR ═══ --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-3 sm:p-4">
        <div class="flex flex-wrap items-center gap-2">

            <a href="{{ $safeRoute('ketua.manajemen-pengurus.create', [], '#') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-user-plus"></i>
                <span class="hidden sm:inline">Tambah Pengurus</span>
                <span class="sm:hidden">Tambah</span>
            </a>

            <button type="button" id="btn-toggle-select"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-check-square"></i>
                <span id="btn-toggle-label">Pilih</span>
            </button>

            <button type="button" id="btn-export" disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-sm font-bold cursor-not-allowed transition-all"
                    data-enabled-class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5">
                <i class="fas fa-file-export"></i>
                <span class="hidden sm:inline">Export Terpilih</span>
                <span class="sm:hidden">Export</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-black/10 text-[10px] font-black tabular-nums" data-count>0</span>
            </button>

            <button type="button" id="btn-manage-role" disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-sm font-bold cursor-not-allowed transition-all"
                    data-enabled-class="bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:shadow-lg hover:shadow-amber-500/30 hover:-translate-y-0.5">
                <i class="fas fa-user-shield"></i>
                <span class="hidden sm:inline">Manage Role</span>
                <span class="sm:hidden">Role</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-black/10 text-[10px] font-black tabular-nums" data-count>0</span>
            </button>

            <button type="button" id="btn-delete" disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-sm font-bold cursor-not-allowed transition-all"
                    data-enabled-class="bg-gradient-to-r from-red-500 to-rose-600 text-white hover:shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5">
                <i class="fas fa-trash-alt"></i>
                <span class="hidden sm:inline">Hapus Terpilih</span>
                <span class="sm:hidden">Hapus</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-black/10 text-[10px] font-black tabular-nums" data-count>0</span>
            </button>

            <div id="selection-info"
                 class="hidden ml-auto flex items-center gap-2 px-3 py-2 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800">
                <i class="fas fa-info-circle text-purple-600 dark:text-purple-400 text-xs"></i>
                <span class="text-xs font-bold text-purple-700 dark:text-purple-300">
                    <span id="selection-count">0</span> dipilih
                </span>
                <button type="button" id="btn-clear-selection"
                        class="ml-1 w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center hover:bg-purple-700 transition">
                    <i class="fas fa-times text-[8px]"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ STATS ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $stats = [
                ['icon' => 'fa-user-tie',   'label' => 'Total',         'value' => $totalOfficial, 'from' => 'from-indigo-500',  'to' => 'to-blue-600'],
                ['icon' => 'fa-user-check', 'label' => 'Aktif',         'value' => $officials->where('is_active', true)->count(), 'from' => 'from-emerald-500', 'to' => 'to-teal-600'],
                ['icon' => 'fa-briefcase',  'label' => 'Punya Jabatan', 'value' => $withJabatan,   'from' => 'from-purple-500',  'to' => 'to-fuchsia-600'],
                ['icon' => 'fa-user-minus', 'label' => 'Tanpa Jabatan', 'value' => $tanpaJabatan,  'from' => 'from-amber-500',   'to' => 'to-orange-600'],
            ];
        @endphp

        @foreach($stats as $s)
        <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800
                    p-4 sm:p-5 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $s['label'] }}</p>
                    <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1 tabular-nums">{{ $s['value'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br {{ $s['from'] }} {{ $s['to'] }}
                            flex items-center justify-center shadow-lg
                            group-hover:scale-110 group-hover:rotate-3 transition-transform">
                    <i class="fas {{ $s['icon'] }} text-white text-base"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══ TOOLBAR ═══ --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-3 sm:p-4">
        <form method="GET" action="{{ $safeRoute('ketua.manajemen-pengurus.index') }}"
              class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm
                          group-focus-within:text-indigo-500 transition"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, NIM, atau email..."
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-indigo-500
                              focus:border-indigo-500 outline-none transition">
            </div>

            <select name="level"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-800/50 text-sm font-medium
                           focus:ring-2 focus:ring-indigo-500 outline-none transition
                           text-gray-700 dark:text-gray-300">
                <option value="">Semua Role</option>
                <option value="3" {{ request('level') === '3' ? 'selected' : '' }}>Ketua</option>
                <option value="4" {{ request('level') === '4' ? 'selected' : '' }}>Pengurus</option>
            </select>

            <select name="status"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-800/50 text-sm font-medium
                           focus:ring-2 focus:ring-indigo-500 outline-none transition
                           text-gray-700 dark:text-gray-300">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>✓ Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>✗ Nonaktif</option>
            </select>

            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600
                           text-white text-sm font-bold hover:shadow-lg hover:shadow-indigo-500/30
                           transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['q', 'level', 'status']))
                <a href="{{ $safeRoute('ketua.manajemen-pengurus.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800
                          text-gray-700 dark:text-gray-300 text-sm font-bold
                          hover:bg-gray-200 dark:hover:bg-gray-700 transition
                          flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- ═══ GRID ═══ --}}
    @if($officials->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="official-grid">
            @foreach($officials as $official)
                @php
                    $roleMeta = match((int) $official->level_id) {
                        3 => ['label' => 'Ketua',    'icon' => 'fa-crown',    'from' => 'from-purple-500', 'to' => 'to-fuchsia-600',
                              'badge_bg' => 'bg-purple-50 dark:bg-purple-900/20', 'badge_text' => 'text-purple-700 dark:text-purple-400',
                              'ring' => 'ring-purple-200 dark:ring-purple-800'],
                        4 => ['label' => 'Pengurus', 'icon' => 'fa-user-tie', 'from' => 'from-indigo-500', 'to' => 'to-blue-600',
                              'badge_bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'badge_text' => 'text-indigo-700 dark:text-indigo-400',
                              'ring' => 'ring-indigo-200 dark:ring-indigo-800'],
                        default => ['label' => 'User', 'icon' => 'fa-user', 'from' => 'from-gray-500', 'to' => 'to-gray-600',
                              'badge_bg' => 'bg-gray-50', 'badge_text' => 'text-gray-700', 'ring' => 'ring-gray-200'],
                    };
                @endphp

                <div class="official-card group relative bg-white dark:bg-gray-900 rounded-2xl
                            border border-gray-200 dark:border-gray-800 overflow-hidden
                            hover:border-indigo-300 dark:hover:border-indigo-700
                            hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-1.5
                            transition-all duration-300"
                     data-user-id="{{ $official->id }}">

                    <div class="absolute top-0 left-0 right-0 h-1
                                bg-gradient-to-r {{ $roleMeta['from'] }} {{ $roleMeta['to'] }}
                                scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 z-10"></div>

                    <div class="checkbox-overlay hidden absolute top-3 left-3 z-20">
                        <label class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded-lg
                                      bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600
                                      hover:border-indigo-500 transition-all
                                      has-[:checked]:bg-indigo-600 has-[:checked]:border-indigo-600 shadow-lg">
                            <input type="checkbox" class="official-checkbox w-4 h-4 rounded appearance-none cursor-pointer"
                                   data-id="{{ $official->id }}">
                            <i class="fas fa-check text-white text-xs absolute pointer-events-none opacity-0 check-icon"></i>
                        </label>
                    </div>

                    <a href="{{ $safeRoute('ketua.manajemen-pengurus.show', $official->id) }}"
                       class="block card-link">
                        <div class="p-5">
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 relative">
                                    @if($official->avatar_url)
                                        <img src="{{ asset('storage/'.$official->avatar_url) }}"
                                             class="w-16 h-16 rounded-2xl object-cover ring-2 ring-gray-100 dark:ring-gray-800 group-hover:ring-indigo-500/40 transition-all">
                                    @else
                                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $roleMeta['from'] }} {{ $roleMeta['to'] }}
                                                    flex items-center justify-center text-white font-black text-xl shadow-lg
                                                    group-hover:scale-105 group-hover:rotate-3 transition-transform">
                                            {{ strtoupper(substr($official->name ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full
                                                 border-2 border-white dark:border-gray-900
                                                 {{ $official->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 dark:text-white truncate
                                               group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $official->name ?? 'Tanpa Nama' }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                                        {{ $official->nim ?? '-' }}
                                    </p>
                                    @if($official->jabatans && $official->jabatans->count())
                                        <p class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 truncate mt-1.5">
                                            <i class="fas fa-briefcase text-[9px] mr-1"></i>
                                            {{ $official->jabatans->pluck('name')->join(', ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5 mt-4">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                                             text-[10px] font-bold uppercase tracking-wide
                                             {{ $official->is_active
                                                ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800'
                                                : 'bg-gray-100 dark:bg-gray-800 text-gray-500 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                                {{ $official->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                    {{ $official->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>

                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide
                                             {{ $roleMeta['badge_bg'] }} {{ $roleMeta['badge_text'] }} ring-1 {{ $roleMeta['ring'] }}">
                                    <i class="fas {{ $roleMeta['icon'] }} text-[8px]"></i>
                                    {{ $roleMeta['label'] }}
                                </span>
                            </div>

                            @if($official->university || $official->major)
                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                    <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                        {{ optional($official->university)->name ?? '-' }}
                                        @if($official->major) · {{ $official->major->name }} @endif
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-800/30
                                    border-t border-gray-100 dark:border-gray-800
                                    flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lihat Profil</span>
                            <i class="fas fa-arrow-right text-gray-400
                                      group-hover:text-indigo-500 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if(method_exists($officials, 'hasPages') && $officials->hasPages())
            <div class="flex justify-center pt-4">
                @if(view()->exists('vendor.pagination.ketua'))
                    {{ $officials->withQueryString()->links('vendor.pagination.ketua') }}
                @else
                    {{ $officials->withQueryString()->links() }}
                @endif
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-900 rounded-3xl
                    border-2 border-dashed border-gray-200 dark:border-gray-800 p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-3xl
                        bg-gradient-to-br from-indigo-100 to-blue-100
                        dark:from-indigo-900/30 dark:to-blue-900/20
                        flex items-center justify-center mb-5">
                <i class="fas fa-user-tie text-indigo-500 text-3xl"></i>
            </div>
            <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-2">Belum ada ketua atau pengurus</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                @if(request()->hasAny(['q', 'level', 'status']))
                    Coba ubah filter atau hapus pencarian.
                @else
                    Data ketua dan pengurus akan muncul di sini setelah ditambahkan.
                @endif
            </p>
        </div>
    @endif
</div>

{{-- ══════════════ MODAL MANAGE ROLE ══════════════ --}}
<div id="modal-manage-role"
     class="hidden fixed inset-0 z-[1200] bg-black/60 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto">

        <div id="mr-header" class="sticky top-0 z-10 bg-gradient-to-r from-amber-500 to-orange-600 p-5 rounded-t-3xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <i id="mr-header-icon" class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 id="mr-title" class="text-xl font-black text-white">Manage Role</h3>
                        <p id="mr-subtitle" class="text-xs text-white/80">Kelola level & jabatan pengurus terpilih</p>
                    </div>
                </div>
                <button type="button" onclick="closeManageRole()"
                        class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 text-white transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-4">

            {{-- SECTION A: NORMAL MODE --}}
            <div id="mr-normal-section" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Pengurus Terpilih (<span id="mr-selected-count">0</span>)
                    </label>
                    <div id="mr-selected-list"
                         class="flex flex-wrap gap-2 max-h-24 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Aksi</label>
                    <select id="mr-action"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-amber-500
                                   outline-none transition font-medium">
                        <option value="set_level">🎯 Ubah Level Role</option>
                        <option value="promote">⬆️ Naikkan Pangkat</option>
                        <option value="demote">⬇️ Turunkan Pangkat</option>
                        <option value="add_position">➕ Tambah Jabatan</option>
                        <option value="remove_position">➖ Kurangi Jabatan</option>
                        <option value="transfer_ketua">👑 Transfer Jabatan Ketua (dari Anda)</option>
                    </select>
                </div>

                <div id="mr-target-level-group">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Level Target</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($availableRoles as $role)
                            @php $rid = is_object($role) ? $role->id : $role['id']; $rname = is_object($role) ? $role->name : $role['name']; @endphp
                            <label class="role-option cursor-pointer relative flex items-center gap-2 px-3 py-2.5 rounded-xl
                                          border-2 border-gray-200 dark:border-gray-700 hover:border-amber-400 transition-all
                                          has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50
                                          dark:has-[:checked]:bg-amber-900/20 has-[:checked]:shadow-md">
                                <input type="radio" name="mr-target-level" value="{{ $rid }}" class="sr-only" @if($rid==4) checked @endif>
                                <i class="fas fa-{{ $rid==3 ? 'crown' : ($rid==4 ? 'user-tie' : ($rid==5 ? 'users' : 'user')) }} text-amber-500"></i>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $rname }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div id="mr-jabatan-group" class="hidden">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih Jabatan</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                        @forelse($jabatans as $j)
                            @php $jid = is_object($j) ? $j->id : $j['id']; $jname = is_object($j) ? $j->name : $j['name']; @endphp
                            <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition">
                                <input type="checkbox" name="mr-jabatan" value="{{ $jid }}" class="w-4 h-4 rounded accent-amber-500">
                                <span class="text-gray-700 dark:text-gray-300">{{ $jname }}</span>
                            </label>
                        @empty
                            <p class="col-span-2 text-xs text-gray-400 italic">Belum ada data jabatan.</p>
                        @endforelse
                    </div>
                </div>

                <div id="mr-ketua-warning"
                     class="hidden rounded-2xl bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 p-4">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-amber-900 dark:text-amber-300 text-sm">Peringatan: Role Ketua Hanya 1</h4>
                            <p class="text-xs text-amber-800 dark:text-amber-400 mt-1">
                                Gunakan <strong>Transfer Jabatan Ketua</strong> untuk memindahkan role Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="mr-normal-comment-group">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Komentar / Pesan <span class="text-gray-400 normal-case font-normal">(opsional)</span>
                    </label>
                    <textarea id="mr-comment" rows="2" maxlength="1000"
                              placeholder="Tuliskan alasan atau pesan..."
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                     bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-amber-500
                                     outline-none transition resize-none"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1"><span id="mr-comment-count">0</span>/1000</p>
                </div>
            </div>

            {{-- SECTION B: TRANSFER MODE --}}
            <div id="mr-transfer-section" class="hidden space-y-4">
                <div class="rounded-2xl bg-gradient-to-r from-red-500 to-rose-600 p-4 shadow-lg">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exchange-alt text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-white text-sm uppercase tracking-wide">Mode Transfer Ketua</h4>
                            <p class="text-xs text-white/90 mt-1">
                                Setelah disimpan, Anda akan <strong>kehilangan seluruh akses Ketua</strong>
                                dan <strong>otomatis logout</strong>. Pilih 1 orang saja.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 p-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ketua Saat Ini (Anda)</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600
                                    flex items-center justify-center text-white font-black text-sm shadow-md">
                            {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-3 py-1">
                    <div class="h-px bg-gradient-to-r from-transparent via-amber-400 to-amber-500 flex-1"></div>
                    <i class="fas fa-arrow-down text-amber-500 animate-bounce"></i>
                    <div class="h-px bg-gradient-to-l from-transparent via-amber-400 to-amber-500 flex-1"></div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2 flex-wrap gap-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                            Pilih 1 Calon Ketua Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-1.5">
                            <span id="mr-candidate-count"
                                  class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30
                                         text-blue-700 dark:text-blue-400 text-[10px] font-bold">
                                {{ count($eligibleCandidates) }} tersedia
                            </span>
                            <span id="mr-candidate-badge"
                                  class="hidden px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30
                                         text-emerald-700 dark:text-emerald-400 text-[10px] font-black">
                                ✓ Terpilih
                            </span>
                        </div>
                    </div>

                    <div class="relative mb-2">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="mr-candidate-search"
                               placeholder="Cari nama / NIM / email kandidat..."
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-800/50 text-xs
                                      focus:ring-2 focus:ring-amber-500 outline-none transition">
                    </div>

                    <div id="mr-candidate-list"
                         class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto
                                p-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl
                                border border-gray-200 dark:border-gray-700"></div>

                    <div id="mr-candidate-empty" class="hidden text-center py-8 px-4"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        <i class="fas fa-lock text-amber-500 mr-1"></i>
                        PIN Keamanan Ketua <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="mr-pin" maxlength="20" inputmode="numeric"
                               autocomplete="new-password"
                               placeholder="Masukkan PIN (default: 12345678)"
                               class="w-full pl-4 pr-11 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                      bg-gray-50 dark:bg-gray-800/50 text-sm font-mono tracking-widest
                                      focus:ring-2 focus:ring-amber-500 outline-none transition">
                        <button type="button" id="mr-pin-toggle"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-500 transition">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Pertama kali pakai PIN default
                        <span class="font-mono font-bold text-amber-600 dark:text-amber-400">12345678</span>.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Alasan Transfer <span class="text-red-500">*</span>
                    </label>
                    <textarea id="mr-transfer-comment" rows="3" maxlength="1000"
                              placeholder="Contoh: Serah terima jabatan periode 2025/2026..."
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                                     bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-amber-500
                                     outline-none transition resize-none"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">
                        <span id="mr-transfer-comment-count">0</span>/1000
                    </p>
                </div>

                <label class="flex items-start gap-3 p-3 rounded-xl bg-red-50 dark:bg-red-900/20
                              border-2 border-red-200 dark:border-red-800 cursor-pointer
                              hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    <input type="checkbox" id="mr-transfer-confirm"
                           class="mt-0.5 w-4 h-4 accent-red-600 cursor-pointer">
                    <span class="text-xs text-red-800 dark:text-red-300 font-semibold leading-relaxed">
                        Saya memahami bahwa setelah transfer, saya akan <strong>kehilangan akses Ketua</strong>
                        dan <strong>otomatis logout</strong>. Tindakan ini tidak dapat dibatalkan.
                    </span>
                </label>
            </div>

            <div id="mr-error"
                 class="hidden rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                        p-3 text-sm text-red-700 dark:text-red-400 flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <span id="mr-error-text">Terjadi kesalahan.</span>
            </div>
        </div>

        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-800
                    p-4 flex gap-3 rounded-b-3xl">
            <button type="button" onclick="closeManageRole()"
                    class="flex-1 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                           text-gray-700 dark:text-gray-300 font-bold text-sm
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                Batal
            </button>
            <button type="button" id="mr-submit"
                    class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white
                           font-bold text-sm hover:shadow-lg hover:shadow-amber-500/30
                           transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> <span id="mr-submit-label">Simpan Perubahan</span>
            </button>
        </div>
    </div>
</div>

{{-- ══════════════ MODAL KETUA CONFLICT ══════════════ --}}
<div id="modal-ketua-conflict" class="hidden fixed inset-0 z-[1300] bg-black/70 backdrop-blur-md items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-md">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg shadow-red-500/30 mb-4">
                <i class="fas fa-crown text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Konflik Role Ketua</h3>
            <p id="ketua-conflict-msg" class="text-sm text-gray-500 dark:text-gray-400 mb-5"></p>
            <div id="ketua-conflict-info"
                 class="hidden mb-5 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-left">
                <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">Ketua Saat Ini</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white" id="existing-ketua-name">-</p>
                <p class="text-xs text-gray-500 dark:text-gray-400" id="existing-ketua-email">-</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeKetuaConflict()"
                        class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                               font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Batal
                </button>
                <button type="button" onclick="switchToTransferKetua()"
                        class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white
                               font-bold text-sm hover:shadow-lg transition active:scale-95">
                    <i class="fas fa-exchange-alt mr-1"></i> Transfer Ketua
                </button>
            </div>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[1400] flex flex-col gap-2 pointer-events-none"></div>

@push('styles')
<style>
    .animate-fade-in-up { animation: fadeInUp .4s ease-out forwards; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px) scale(.95); }
        to   { opacity: 1; transform: translateY(0)    scale(1);   }
    }
    .animate-pulse-slow { animation: pulseSlow 2s ease-in-out infinite; }
    @keyframes pulseSlow {
        0%, 100% { transform: scale(1);    }
        50%      { transform: scale(1.06); }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════
       STATE GLOBAL
       ═══════════════════════════════════════════════════════ */
    const state = {
        selectionMode: false,
        selected: new Set(),
        csrfToken: '{{ csrf_token() }}',
        routes: {
            bulkAction: '{{ $safeRoute("ketua.manajemen-pengurus.bulk-action") }}',
            manageRole: '{{ $safeRoute("ketua.manajemen-pengurus.manage-role") }}',
            logout:     '{{ $safeRoute("logout") }}',
        },
    };

    const ELIGIBLE_CANDIDATES = @json($eligibleCandidates);

    const mrState = {
        selectedCandidateId: null,
        searchDebounceTimer: null,
    };

    /* ═══════════════════════════════════════════════════════
       HELPER — escapeHtml
       ═══════════════════════════════════════════════════════ */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    }

    /* ═══════════════════════════════════════════════════════
       TOAST
       ═══════════════════════════════════════════════════════ */
    function toast(message, type = 'info') {
        const colors = {
            success: 'from-emerald-500 to-teal-600',
            error:   'from-red-500 to-rose-600',
            info:    'from-blue-500 to-cyan-600',
            warning: 'from-amber-500 to-orange-600',
        };
        const icons = {
            success: 'fa-check-circle',
            error:   'fa-exclamation-circle',
            info:    'fa-info-circle',
            warning: 'fa-exclamation-triangle',
        };

        const el = document.createElement('div');
        el.className = `pointer-events-auto bg-gradient-to-r ${colors[type] || colors.info} text-white
                        px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3
                        min-w-[280px] max-w-md translate-x-full transition-transform duration-300`;
        el.innerHTML = `
            <i class="fas ${icons[type] || icons.info} text-lg"></i>
            <span class="text-sm font-semibold flex-1">${escapeHtml(message)}</span>
            <button class="w-6 h-6 rounded-full hover:bg-white/20 flex items-center justify-center">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

        document.getElementById('toast-container').appendChild(el);
        requestAnimationFrame(() => el.classList.remove('translate-x-full'));

        const dismiss = () => {
            el.classList.add('translate-x-full');
            setTimeout(() => el.remove(), 300);
        };

        el.querySelector('button').addEventListener('click', dismiss);
        setTimeout(dismiss, 4000);
    }

    /* ═══════════════════════════════════════════════════════
       DOM REFS — GLOBAL
       ═══════════════════════════════════════════════════════ */
    const btnToggle = document.getElementById('btn-toggle-select');
    const btnLabel  = document.getElementById('btn-toggle-label');
    const checkboxes = document.querySelectorAll('.official-checkbox');
    const overlays   = document.querySelectorAll('.checkbox-overlay');
    const cardLinks  = document.querySelectorAll('.card-link');

    const btnExport    = document.getElementById('btn-export');
    const btnManage    = document.getElementById('btn-manage-role');
    const btnDelete    = document.getElementById('btn-delete');
    const selectionInfo  = document.getElementById('selection-info');
    const selectionCount = document.getElementById('selection-count');

    /* ═══════════════════════════════════════════════════════
       SELECTION MODE
       ═══════════════════════════════════════════════════════ */
    function setSelectionMode(on) {
        state.selectionMode = on;
        overlays.forEach(o => o.classList.toggle('hidden', !on));
        cardLinks.forEach(a => { a.style.pointerEvents = on ? 'none' : ''; });

        if (on) {
            btnLabel.textContent = 'Batal Pilih';
            btnToggle.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-gray-600 to-gray-800 text-white text-sm font-bold hover:shadow-lg hover:-translate-y-0.5 transition-all active:scale-95';
        } else {
            btnLabel.textContent = 'Pilih';
            btnToggle.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all active:scale-95';

            state.selected.clear();
            checkboxes.forEach(cb => { cb.checked = false; updateCheckboxUI(cb); });
            updateActionButtons();
        }
    }

    btnToggle.addEventListener('click', () => setSelectionMode(!state.selectionMode));

    /* ═══════════════════════════════════════════════════════
       CHECKBOX
       ═══════════════════════════════════════════════════════ */
    function updateCheckboxUI(cb) {
        const card = cb.closest('.official-card');
        const icon = card.querySelector('.check-icon');
        if (cb.checked) {
            card.classList.add('ring-2', 'ring-indigo-500', 'shadow-lg', 'shadow-indigo-500/20');
            if (icon) icon.style.opacity = '1';
        } else {
            card.classList.remove('ring-2', 'ring-indigo-500', 'shadow-lg', 'shadow-indigo-500/20');
            if (icon) icon.style.opacity = '0';
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const id = parseInt(this.dataset.id);
            if (this.checked) state.selected.add(id);
            else state.selected.delete(id);
            updateCheckboxUI(this);
            updateActionButtons();
        });
    });

    /* ═══════════════════════════════════════════════════════
       ACTION BUTTONS
       ═══════════════════════════════════════════════════════ */
    function updateActionButtons() {
        const count = state.selected.size;
        const enabled = count > 0;

        [btnExport, btnManage, btnDelete].forEach(btn => {
            const enabledClass = btn.dataset.enabledClass || '';
            const disabledClass = 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 cursor-not-allowed';

            if (enabled) {
                btn.disabled = false;
                btn.className = btn.className.replace(disabledClass, '').replace(/\s+/g, ' ').trim();
                if (enabledClass && !btn.className.includes(enabledClass.split(' ')[0])) {
                    btn.className += ' ' + enabledClass;
                }
            } else {
                btn.disabled = true;
                btn.className = btn.className.replace(enabledClass, '').replace(/\s+/g, ' ').trim();
                if (!btn.className.includes('bg-gray-100')) {
                    btn.className += ' ' + disabledClass;
                }
            }

            const counter = btn.querySelector('[data-count]');
            if (counter) counter.textContent = count;
        });

        if (enabled) {
            selectionInfo.classList.remove('hidden');
            selectionCount.textContent = count;
        } else {
            selectionInfo.classList.add('hidden');
        }
    }

    document.getElementById('btn-clear-selection').addEventListener('click', () => {
        state.selected.clear();
        checkboxes.forEach(cb => { cb.checked = false; updateCheckboxUI(cb); });
        updateActionButtons();
    });

    /* ═══════════════════════════════════════════════════════
       EXPORT
       ═══════════════════════════════════════════════════════ */
    btnExport.addEventListener('click', () => {
        if (state.selected.size === 0) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = state.routes.bulkAction;
        form.style.display = 'none';

        const addField = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };

        addField('_token', state.csrfToken);
        addField('action', 'export');
        Array.from(state.selected).forEach((id, i) => addField(`user_ids[${i}]`, id));

        document.body.appendChild(form);
        form.submit();
        setTimeout(() => form.remove(), 1000);
    });

    /* ═══════════════════════════════════════════════════════
       DELETE
       ═══════════════════════════════════════════════════════ */
    btnDelete.addEventListener('click', async () => {
        if (state.selected.size === 0) return;
        if (!confirm(`Hapus ${state.selected.size} pengurus yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) return;

        try {
            const res = await fetch(state.routes.bulkAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    user_ids: Array.from(state.selected),
                    action: 'delete',
                }),
            });

            const data = await res.json();
            if (data.success) {
                toast(data.message, 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                toast(data.message || 'Gagal menghapus.', 'error');
            }
        } catch (e) {
            toast('Gagal menghubungi server.', 'error');
        }
    });

    /* ═══════════════════════════════════════════════════════
       MANAGE ROLE MODAL — DOM REFS
       ═══════════════════════════════════════════════════════ */
    const modal              = document.getElementById('modal-manage-role');
    const mrHeader           = document.getElementById('mr-header');
    const mrHeaderIcon       = document.getElementById('mr-header-icon');
    const mrTitle            = document.getElementById('mr-title');
    const mrSubtitle         = document.getElementById('mr-subtitle');
    const mrNormalSection    = document.getElementById('mr-normal-section');
    const mrTransferSection  = document.getElementById('mr-transfer-section');
    const mrAction           = document.getElementById('mr-action');
    const mrTargetLevelGroup = document.getElementById('mr-target-level-group');
    const mrJabatanGroup     = document.getElementById('mr-jabatan-group');
    const mrKetuaWarning     = document.getElementById('mr-ketua-warning');
    const mrComment          = document.getElementById('mr-comment');
    const mrCommentCount     = document.getElementById('mr-comment-count');
    const mrSubmit           = document.getElementById('mr-submit');
    const mrSubmitLabel      = document.getElementById('mr-submit-label');
    const mrError            = document.getElementById('mr-error');
    const mrErrorText        = document.getElementById('mr-error-text');

    const mrCandidateList      = document.getElementById('mr-candidate-list');
    const mrCandidateSearch    = document.getElementById('mr-candidate-search');
    const mrCandidateEmpty     = document.getElementById('mr-candidate-empty');
    const mrCandidateBadge     = document.getElementById('mr-candidate-badge');
    const mrCandidateCount     = document.getElementById('mr-candidate-count');
    const mrPin                = document.getElementById('mr-pin');
    const mrPinToggle          = document.getElementById('mr-pin-toggle');
    const mrTransferComment    = document.getElementById('mr-transfer-comment');
    const mrTransferCommentCnt = document.getElementById('mr-transfer-comment-count');
    const mrTransferConfirm    = document.getElementById('mr-transfer-confirm');

    /* ─── Open / Close ─── */
    window.closeManageRole = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.openManageRole = function () {
        renderSelectedUsers();
        mrError.classList.add('hidden');
        mrComment.value = '';
        mrCommentCount.textContent = '0';
        mrAction.value = 'set_level';
        document.querySelectorAll('input[name="mr-jabatan"]').forEach(cb => cb.checked = false);
        updateManageRoleFields();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    btnManage.addEventListener('click', () => {
        if (state.selected.size === 0) return;
        openManageRole();
    });

    /* ─── Render selected chips ─── */
    function renderSelectedUsers() {
        const list = document.getElementById('mr-selected-list');
        const cards = document.querySelectorAll('.official-card');
        let html = '';

        cards.forEach(card => {
            const id = parseInt(card.dataset.userId);
            if (state.selected.has(id)) {
                const name = card.querySelector('h3')?.textContent.trim() || '';
                html += `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full
                                 bg-amber-100 dark:bg-amber-900/30
                                 text-amber-700 dark:text-amber-400 text-xs font-bold">
                    <i class="fas fa-user text-[9px]"></i> ${escapeHtml(name)}
                </span>`;
            }
        });

        list.innerHTML = html || '<p class="text-xs text-gray-400">Tidak ada yang dipilih</p>';
        document.getElementById('mr-selected-count').textContent = state.selected.size;
    }

    /* ─── Show/hide per action ─── */
    function updateManageRoleFields() {
        const action = mrAction.value;
        const isTransfer = action === 'transfer_ketua';
        const targetLevel = document.querySelector('input[name="mr-target-level"]:checked')?.value;
        const isKetuaTarget = targetLevel === '3';

        if (isTransfer) {
            mrHeader.className = 'sticky top-0 z-10 bg-gradient-to-r from-red-500 to-rose-600 p-5 rounded-t-3xl';
            mrHeaderIcon.className = 'fas fa-exchange-alt text-white text-xl';
            mrTitle.textContent = 'Transfer Jabatan Ketua';
            mrSubtitle.textContent = 'Pindahkan jabatan Ketua ke pengurus/anggota lain';
            mrSubmitLabel.textContent = 'Transfer & Logout';
            mrSubmit.className = 'flex-1 py-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white font-bold text-sm hover:shadow-lg hover:shadow-red-500/30 transition active:scale-95 flex items-center justify-center gap-2';
        } else {
            mrHeader.className = 'sticky top-0 z-10 bg-gradient-to-r from-amber-500 to-orange-600 p-5 rounded-t-3xl';
            mrHeaderIcon.className = 'fas fa-user-shield text-white text-xl';
            mrTitle.textContent = 'Manage Role';
            mrSubtitle.textContent = 'Kelola level & jabatan pengurus terpilih';
            mrSubmitLabel.textContent = 'Simpan Perubahan';
            mrSubmit.className = 'flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm hover:shadow-lg hover:shadow-amber-500/30 transition active:scale-95 flex items-center justify-center gap-2';
        }

        mrNormalSection.classList.toggle('hidden', isTransfer);
        mrTransferSection.classList.toggle('hidden', !isTransfer);

        if (!isTransfer) {
            mrTargetLevelGroup.classList.toggle('hidden', !['set_level', 'promote', 'demote'].includes(action));
            mrJabatanGroup.classList.toggle('hidden', !['add_position', 'remove_position'].includes(action));
            const shouldWarn = isKetuaTarget && ['set_level', 'promote'].includes(action);
            mrKetuaWarning.classList.toggle('hidden', !shouldWarn);
        } else {
            mrState.selectedCandidateId = null;
            mrCandidateBadge.classList.add('hidden');
            mrPin.value = '';
            mrPin.type = 'password';
            mrPinToggle.querySelector('i').className = 'fas fa-eye text-xs';
            mrTransferComment.value = '';
            mrTransferCommentCnt.textContent = '0';
            mrTransferConfirm.checked = false;
            mrCandidateSearch.value = '';
            renderCandidateList();
            setTimeout(() => mrCandidateSearch.focus(), 200);
        }
    }

    /* ═══════════════════════════════════════════════════════
       RENDER CANDIDATE LIST
       ═══════════════════════════════════════════════════════ */
    function renderCandidateList() {
        const query = (mrCandidateSearch.value || '').toLowerCase().trim();
        const totalCandidates = ELIGIBLE_CANDIDATES.length;

        if (mrCandidateCount) {
            mrCandidateCount.textContent = totalCandidates + ' tersedia';
        }

        /* Tidak ada kandidat sama sekali */
        if (totalCandidates === 0) {
            mrCandidateList.innerHTML = '';
            mrCandidateEmpty.innerHTML = `
                <div class="flex flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800
                                flex items-center justify-center">
                        <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                    </div>
                    <p class="font-bold text-gray-600 dark:text-gray-300 text-sm">Tidak Ada Kandidat</p>
                    <p class="text-[11px] text-gray-400 max-w-xs leading-relaxed">
                        Belum ada <b>Pengurus</b> atau <b>Anggota aktif</b> yang bisa menerima jabatan Ketua.
                        Tambahkan pengurus/anggota dulu.
                    </p>
                </div>
            `;
            mrCandidateEmpty.classList.remove('hidden');
            return;
        }

        /* Filter */
        const filtered = ELIGIBLE_CANDIDATES.filter(c => {
            if (!query) return true;
            return (c.name  || '').toLowerCase().includes(query)
                || (c.nim   || '').toLowerCase().includes(query)
                || (c.email || '').toLowerCase().includes(query);
        });

        /* Hasil filter kosong */
        if (filtered.length === 0) {
            mrCandidateList.innerHTML = '';
            mrCandidateEmpty.innerHTML = `
                <div class="flex flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20
                                flex items-center justify-center">
                        <i class="fas fa-search text-2xl text-amber-500"></i>
                    </div>
                    <p class="font-bold text-gray-600 dark:text-gray-300 text-sm">Tidak Ada Hasil</p>
                    <p class="text-[11px] text-gray-400">
                        Tidak ada kandidat cocok dengan
                        "<span class="font-bold text-amber-600">${escapeHtml(query)}</span>"
                    </p>
                    <button type="button" id="mr-candidate-reset"
                            class="mt-2 px-3 py-1.5 rounded-lg bg-amber-500 text-white
                                   text-[11px] font-bold hover:bg-amber-600 transition active:scale-95">
                        <i class="fas fa-times mr-1"></i> Reset Pencarian
                    </button>
                </div>
            `;
            mrCandidateEmpty.classList.remove('hidden');

            document.getElementById('mr-candidate-reset')?.addEventListener('click', () => {
                mrCandidateSearch.value = '';
                renderCandidateList();
                mrCandidateSearch.focus();
            });
            return;
        }

        mrCandidateEmpty.classList.add('hidden');

        mrCandidateList.innerHTML = filtered.map(c => {
            const isSelected = mrState.selectedCandidateId === c.id;
            const levelBadge = c.level_id === 4
                ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400'
                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400';

            const avatar = c.avatar_url
                ? `<img src="${c.avatar_url}"
                        class="w-11 h-11 rounded-xl object-cover ring-2
                               ${isSelected ? 'ring-amber-500' : 'ring-gray-200 dark:ring-gray-700'}">`
                : `<div class="w-11 h-11 rounded-xl flex items-center justify-center text-white font-black text-sm
                              bg-gradient-to-br from-amber-500 to-orange-600
                              ring-2 ${isSelected ? 'ring-amber-500' : 'ring-transparent'}">
                       ${escapeHtml(c.initial)}
                   </div>`;

            return `
                <button type="button"
                        data-candidate-id="${c.id}"
                        class="candidate-item group text-left flex items-center gap-3 p-3 rounded-xl
                               border-2 transition-all duration-200
                               ${isSelected
                                    ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 shadow-md'
                                    : 'border-transparent bg-white dark:bg-gray-800/40 hover:border-amber-300 hover:bg-amber-50/50 dark:hover:bg-amber-900/10'}">
                    ${avatar}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                ${escapeHtml(c.name)}
                            </p>
                            ${isSelected ? '<i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>' : ''}
                        </div>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-mono truncate mt-0.5">
                            ${escapeHtml(c.nim)}
                        </p>
                        <span class="inline-block mt-1 px-1.5 py-0.5 rounded-md text-[9px]
                                     font-bold uppercase tracking-wide ${levelBadge}">
                            ${escapeHtml(c.level_name)}
                        </span>
                    </div>
                </button>
            `;
        }).join('');

        mrCandidateList.querySelectorAll('.candidate-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const newId = parseInt(btn.dataset.candidateId);
                if (mrState.selectedCandidateId === newId) {
                    mrState.selectedCandidateId = null;
                    mrCandidateBadge.classList.add('hidden');
                } else {
                    mrState.selectedCandidateId = newId;
                    mrCandidateBadge.classList.remove('hidden');
                }
                renderCandidateList();
            });
        });
    }

    /* ─── Event listeners ─── */
    mrAction.addEventListener('change', updateManageRoleFields);

    document.querySelectorAll('input[name="mr-target-level"]').forEach(r => {
        r.addEventListener('change', updateManageRoleFields);
    });

    mrComment.addEventListener('input', () => {
        mrCommentCount.textContent = mrComment.value.length;
    });

    mrTransferComment.addEventListener('input', () => {
        mrTransferCommentCnt.textContent = mrTransferComment.value.length;
    });

    mrCandidateSearch.addEventListener('input', () => {
        clearTimeout(mrState.searchDebounceTimer);
        mrState.searchDebounceTimer = setTimeout(renderCandidateList, 150);
    });

    mrCandidateSearch.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            mrCandidateSearch.value = '';
            renderCandidateList();
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            const first = mrCandidateList.querySelector('.candidate-item');
            if (first) first.click();
        }
    });

    mrPinToggle.addEventListener('click', () => {
        const type = mrPin.type === 'password' ? 'text' : 'password';
        mrPin.type = type;
        mrPinToggle.querySelector('i').className = type === 'password'
            ? 'fas fa-eye text-xs'
            : 'fas fa-eye-slash text-xs';
    });

    /* ═══════════════════════════════════════════════════════
       SUBMIT
       ═══════════════════════════════════════════════════════ */
    mrSubmit.addEventListener('click', async () => {
        const action        = mrAction.value;
        const isTransfer    = action === 'transfer_ketua';
        const targetLevelId = document.querySelector('input[name="mr-target-level"]:checked')?.value;
        const jabatanIds    = Array.from(document.querySelectorAll('input[name="mr-jabatan"]:checked'))
                                   .map(cb => parseInt(cb.value));
        const comment       = mrComment.value.trim();

        mrError.classList.add('hidden');

        /* VALIDASI */
        if (isTransfer) {
            if (!mrState.selectedCandidateId) {
                return showMrError('Silakan pilih 1 calon Ketua baru terlebih dahulu.');
            }
            if (!mrPin.value || mrPin.value.length < 6) {
                return showMrError('Masukkan PIN keamanan (minimal 6 digit).');
            }
            if (!mrTransferComment.value.trim()) {
                return showMrError('Alasan transfer wajib diisi.');
            }
            if (!mrTransferConfirm.checked) {
                return showMrError('Centang konfirmasi pemahaman terlebih dahulu.');
            }
        } else {
            if (['add_position', 'remove_position'].includes(action) && jabatanIds.length === 0) {
                return showMrError('Pilih minimal satu jabatan.');
            }
        }

        const originalHtml = mrSubmit.innerHTML;
        mrSubmit.disabled = true;
        mrSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        try {
            const payload = isTransfer
                ? {
                    action:       'transfer_ketua',
                    user_ids:     [mrState.selectedCandidateId],
                    candidate_id: mrState.selectedCandidateId,
                    pin:          mrPin.value,
                    comment:      mrTransferComment.value.trim(),
                  }
                : {
                    user_ids:        Array.from(state.selected),
                    action:          action,
                    target_level_id: targetLevelId,
                    jabatan_ids:     jabatanIds,
                    comment:         comment,
                  };

            const res = await fetch(state.routes.manageRole, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                    'Accept':       'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            /* SUKSES TRANSFER → LOGOUT */
            if (isTransfer && data.success && data.redirect_to_login) {
                showLogoutOverlay(data.transfer_to?.name || 'pengurus baru');
                return;
            }

            if (data.success) {
                toast(data.message, 'success');
                closeManageRole();
                setTimeout(() => location.reload(), 800);
                return;
            }

            if (data.conflict_type === 'multiple_ketua' || data.conflict_type === 'already_ketua') {
                closeManageRole();
                showKetuaConflict(data);
                return;
            }

            if (data.error_type === 'invalid_pin') {
                showMrError('PIN keamanan salah. Coba lagi.');
                mrPin.value = '';
                mrPin.focus();
            } else {
                showMrError(data.message || 'Terjadi kesalahan.');
            }

        } catch (e) {
            showMrError('Gagal menghubungi server. Cek koneksi Anda.');
        } finally {
            mrSubmit.disabled = false;
            mrSubmit.innerHTML = originalHtml;
        }
    });

    function showMrError(msg) {
        mrErrorText.textContent = String(msg).replace(/^[⚠️❌]\s*/u, '');
        mrError.classList.remove('hidden');
        mrError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ═══════════════════════════════════════════════════════
       LOGOUT OVERLAY
       ═══════════════════════════════════════════════════════ */
    function showLogoutOverlay(newKetuaName) {
        document.getElementById('transfer-logout-overlay')?.remove();

        const overlay = document.createElement('div');
        overlay.id = 'transfer-logout-overlay';
        overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md';
        overlay.innerHTML = `
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-md p-8 text-center animate-fade-in-up">
                <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-emerald-500 to-teal-600
                            flex items-center justify-center shadow-2xl shadow-emerald-500/30 mb-5 animate-pulse-slow">
                    <i class="fas fa-check text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Transfer Berhasil!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">
                    Jabatan Ketua telah berpindah ke
                    <strong class="text-emerald-600 dark:text-emerald-400">${escapeHtml(newKetuaName)}</strong>.
                    <br>Anda akan logout otomatis dalam
                    <span id="logout-countdown" class="font-black text-amber-500">3</span> detik...
                </p>
                <div class="w-full h-1.5 rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden mb-5">
                    <div id="logout-progress"
                         class="h-full bg-gradient-to-r from-emerald-500 to-teal-600 transition-all duration-1000"
                         style="width:0%"></div>
                </div>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                          bg-gradient-to-r from-amber-500 to-orange-600 text-white
                          font-bold text-sm hover:shadow-lg transition">
                    <i class="fas fa-sign-in-alt"></i> Login Sekarang
                </a>
            </div>
        `;
        document.body.appendChild(overlay);

        let count = 3;
        const progress = overlay.querySelector('#logout-progress');
        const counter = overlay.querySelector('#logout-countdown');

        const interval = setInterval(() => {
            count--;
            if (count > 0) counter.textContent = count;
        }, 1000);

        requestAnimationFrame(() => { progress.style.width = '100%'; });

        setTimeout(() => {
            clearInterval(interval);
            forceLogout();
        }, 3200);
    }

    function forceLogout() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = state.routes.logout;
        form.style.display = 'none';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_token';
        input.value = state.csrfToken;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    }

    /* ═══════════════════════════════════════════════════════
       KETUA CONFLICT
       ═══════════════════════════════════════════════════════ */
    const conflictModal = document.getElementById('modal-ketua-conflict');

    window.closeKetuaConflict = function () {
        conflictModal.classList.add('hidden');
        conflictModal.classList.remove('flex');
    };

    function showKetuaConflict(data) {
        document.getElementById('ketua-conflict-msg').textContent = data.message;

        const infoBox = document.getElementById('ketua-conflict-info');
        if (data.existing_ketua) {
            infoBox.classList.remove('hidden');
            document.getElementById('existing-ketua-name').textContent = data.existing_ketua.name;
            document.getElementById('existing-ketua-email').textContent = data.existing_ketua.email;
        } else {
            infoBox.classList.add('hidden');
        }

        conflictModal.classList.remove('hidden');
        conflictModal.classList.add('flex');
    }

    window.switchToTransferKetua = function () {
        closeKetuaConflict();
        openManageRole();
        mrAction.value = 'transfer_ketua';
        updateManageRoleFields();
    };

    /* ═══════════════════════════════════════════════════════
       ESC KEY
       ═══════════════════════════════════════════════════════ */
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!conflictModal.classList.contains('hidden')) closeKetuaConflict();
            else if (!modal.classList.contains('hidden')) closeManageRole();
        }
    });

    /* ═══════════════════════════════════════════════════════
       INIT
       ═══════════════════════════════════════════════════════ */
    updateActionButtons();

})();
</script>
@endpush
@endsection
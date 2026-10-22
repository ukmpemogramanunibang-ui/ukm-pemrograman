@extends('layouts.roles.owner')

@section('title', 'Semua Kelompok')

@push('styles')
<style>
/* ============================================================
 | GRID RESPONSIVE CERDAS
 | - Mobile Portrait  : 1 card full width (lebar)
 | - Mobile Landscape : 3 card sejajar
 | - Tablet ke atas   : 2 / 3 / 4 kolom otomatis
 * ============================================================ */
.kelompok-grid {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 1rem;
}

/* Tablet kecil */
@media (min-width: 640px) {
    .kelompok-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

/* Tablet / Desktop kecil */
@media (min-width: 1024px) {
    .kelompok-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

/* Desktop besar */
@media (min-width: 1536px) {
    .kelompok-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}

/* 📱 HP PORTRAIT (tegak) — 1 card lebar penuh */
@media (max-width: 767px) and (orientation: portrait) {
    .kelompok-grid { grid-template-columns: repeat(1, minmax(0, 1fr)); }
}

/* 📱 HP LANDSCAPE (mendatar) — 3 card sejajar */
@media (max-width: 900px) and (orientation: landscape) {
    .kelompok-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

/* ============================================================
 | ANIMASI HALUS
 * ============================================================ */
.kelompok-card {
    transition: transform .18s ease, box-shadow .18s ease;
    will-change: transform;
}
.kelompok-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px -10px rgba(0, 0, 0, .15);
}
.kelompok-card:active {
    transform: translateY(0) scale(.995);
}

/* Loading skeleton */
@keyframes skeletonPulse {
    0%, 100% { opacity: 1; }
    50%      { opacity: .45; }
}
.skeleton { animation: skeletonPulse 1.3s ease-in-out infinite; }

/* Progress bar smooth */
.capacity-bar-fill {
    transition: width .6s cubic-bezier(.4, 0, .2, 1);
}

/* Line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@section('content')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $groups */
@endphp

<div class="p-4 md:p-6 max-w-[1600px] mx-auto">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <span class="w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-900/30
                             flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-600"></i>
                </span>
                Group Command Center
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                Kelola kelompok, kapasitas, dan struktur keanggotaan UKM UNICODE.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('owner.kelompok.custom') }}"
               class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700
                      text-white text-sm font-semibold shadow-sm
                      transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat Kelompok
            </a>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- SESSION ALERTS --}}
    {{-- ========================================================= --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500
                    text-green-700 dark:text-green-300 p-4 rounded-xl
                    flex items-start gap-3">
            <i class="fas fa-check-circle mt-0.5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500
                    text-red-700 dark:text-red-300 p-4 rounded-xl
                    flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- STATS — COMMAND CENTER
    {{-- ========================================================= --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">

        {{-- Total Kelompok --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30
                            flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-600"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider
                             text-purple-600 dark:text-purple-400">Groups</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white">
                    {{ $stats['total_groups'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $stats['active_groups'] }} aktif
                </div>
            </div>
        </div>

        {{-- Total Anggota --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30
                            flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider
                             text-blue-600 dark:text-blue-400">Members</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white">
                    {{ $stats['total_members'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    dari {{ $stats['total_capacity'] > 0 ? $stats['total_capacity'] : '∞' }} kapasitas
                </div>
            </div>
        </div>

        {{-- Kapasitas --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                            flex items-center justify-center">
                    <i class="fas fa-chart-pie text-emerald-600"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider
                             text-emerald-600 dark:text-emerald-400">Capacity</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white">
                    {{ $stats['health_pct'] !== null ? $stats['health_pct'] . '%' : '—' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    utilisasi keseluruhan
                </div>
            </div>
        </div>

        {{-- Perlu Perhatian --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border {{ $stats['near_full'] > 0
                        ? 'border-orange-300 dark:border-orange-700'
                        : 'border-gray-200 dark:border-gray-700' }}
                    p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl
                            {{ $stats['near_full'] > 0
                                ? 'bg-orange-100 dark:bg-orange-900/30'
                                : 'bg-gray-100 dark:bg-gray-700' }}
                            flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle
                              {{ $stats['near_full'] > 0 ? 'text-orange-600' : 'text-gray-400' }}"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider
                             {{ $stats['near_full'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400' }}">
                    Alerts
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl md:text-3xl font-extrabold
                            {{ $stats['near_full'] > 0 ? 'text-orange-600' : 'text-gray-800 dark:text-white' }}">
                    {{ $stats['near_full'] }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    kelompok hampir penuh
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- TOOLBAR — SEARCH / FILTER / SORT
    {{-- ========================================================= --}}
    <form method="GET" action="{{ route('owner.kelompok.index') }}"
          id="filterForm"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                 border border-gray-200 dark:border-gray-700 p-4 md:p-5 mb-6">

        {{-- Baris 1: Search + Tombol --}}
        <div class="flex flex-col md:flex-row gap-3">

            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2
                          text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       placeholder="Cari nama, kode, atau deskripsi kelompok..."
                       autocomplete="off"
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl
                              border-gray-300 dark:border-gray-600
                              bg-gray-50 dark:bg-gray-700
                              text-gray-900 dark:text-white
                              text-sm focus:ring-2 focus:ring-purple-500
                              focus:border-purple-500">
            </div>

            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700
                           text-white text-sm font-semibold
                           transition flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Terapkan
            </button>

            @if($filters['q'] || $filters['status'] || $filters['health'] || $filters['sort'] !== 'latest')
                <a href="{{ route('owner.kelompok.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700
                          hover:bg-gray-200 dark:hover:bg-gray-600
                          text-gray-700 dark:text-gray-200 text-sm font-medium
                          transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </div>

        {{-- Baris 2: Filter chips --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">
                Status:
            </span>

            @php
                $statusOptions = [
                    ''         => 'Semua',
                    'aktif'    => 'Aktif',
                    'nonaktif' => 'Nonaktif',
                    'arsip'    => 'Arsip',
                ];
            @endphp

            @foreach($statusOptions as $val => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => null]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
                          {{ $filters['status'] === $val
                              ? 'bg-purple-600 text-white shadow-sm'
                              : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach

            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 ml-3 mr-1">
                Kesehatan:
            </span>

            @php
                $healthOptions = [
                    ''          => 'Semua',
                    'near_full' => 'Hampir Penuh',
                    'full'      => 'Penuh',
                    'empty'     => 'Kosong',
                ];
            @endphp

            @foreach($healthOptions as $val => $label)
                <a href="{{ request()->fullUrlWithQuery(['health' => $val, 'page' => null]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
                          {{ $filters['health'] === $val
                              ? 'bg-purple-600 text-white shadow-sm'
                              : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Baris 3: Sort + per-page --}}
        <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
            <label class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                Urutkan:
                <select name="sort" onchange="document.getElementById('filterForm').submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white
                               text-xs px-2 py-1.5 focus:ring-2 focus:ring-purple-500">
                    <option value="latest"  {{ $filters['sort'] === 'latest'  ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest"  {{ $filters['sort'] === 'oldest'  ? 'selected' : '' }}>Terlama</option>
                    <option value="name"    {{ $filters['sort'] === 'name'    ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="members" {{ $filters['sort'] === 'members' ? 'selected' : '' }}>Anggota Terbanyak</option>
                </select>
            </label>

            <label class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                Per halaman:
                <select name="per_page" onchange="document.getElementById('filterForm').submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white
                               text-xs px-2 py-1.5 focus:ring-2 focus:ring-purple-500">
                    @foreach([12, 24, 48] as $pp)
                        <option value="{{ $pp }}" {{ $filters['per_page'] === $pp ? 'selected' : '' }}>
                            {{ $pp }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="ml-auto text-gray-500 dark:text-gray-400">
                Menampilkan
                <strong class="text-gray-800 dark:text-white">{{ $groups->count() }}</strong>
                dari
                <strong class="text-gray-800 dark:text-white">{{ $groups->total() }}</strong>
                kelompok
            </div>
        </div>
    </form>

    {{-- ========================================================= --}}
    {{-- GRID KELOMPOK
    {{-- ========================================================= --}}
    @if($groups->count() > 0)
        <div class="kelompok-grid">

            @foreach($groups as $group)
                @php
                    $health   = $group->health_status;
                    $badge    = $group->status_badge;
                    $pct      = $group->capacity_percentage;
                    $hasLimit = $group->max_members && $group->max_members > 0;
                    $ketua    = $group->ketua_name;
                @endphp

                <div class="kelompok-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700
                            overflow-hidden flex flex-col">

                    {{-- ===================== HEADER ===================== --}}
                    <div class="p-5 pb-3">
                        <div class="flex items-start justify-between gap-3">

                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                <div class="w-11 h-11 rounded-xl shrink-0
                                            bg-gradient-to-br from-purple-500 to-indigo-600
                                            flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                    {{ strtoupper(substr($group->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h2 class="font-bold text-base text-gray-800 dark:text-white
                                               truncate" title="{{ $group->name }}">
                                        {{ $group->name }}
                                    </h2>
                                    <p class="text-[11px] text-gray-400 font-mono truncate">
                                        {{ $group->code }}
                                    </p>
                                </div>
                            </div>

                            <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold
                                         uppercase tracking-wide {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </div>

                        {{-- Description --}}
                        @if($group->description)
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                {{ $group->description }}
                            </p>
                        @endif
                    </div>

                    {{-- ===================== CAPACITY ===================== --}}
                    <div class="px-5 pb-3">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-semibold text-gray-600 dark:text-gray-300">
                                <i class="fas fa-users text-[10px] mr-1"></i>
                                Anggota
                            </span>
                            <span class="font-bold text-gray-800 dark:text-white">
                                {{ $group->member_count }}
                                @if($hasLimit)
                                    <span class="text-gray-400 font-medium">/ {{ $group->max_members }}</span>
                                @else
                                    <span class="text-gray-400 font-medium">• ∞</span>
                                @endif
                            </span>
                        </div>

                        @if($hasLimit)
                            <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="capacity-bar-fill h-full rounded-full
                                            {{ $pct >= 100
                                                ? 'bg-red-500'
                                                : ($pct >= 80
                                                    ? 'bg-orange-500'
                                                    : 'bg-emerald-500') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-[10px] text-gray-400">
                                <span>{{ $pct }}% terpakai</span>
                                <span>
                                    @if($pct >= 100)
                                        Penuh
                                    @elseif($pct >= 80)
                                        Hampir penuh
                                    @else
                                        Tersedia {{ $group->max_members - $group->member_count }} slot
                                    @endif
                                </span>
                            </div>
                        @else
                            <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700"></div>
                            <div class="mt-1 text-[10px] text-gray-400">
                                Tidak dibatasi kapasitas
                            </div>
                        @endif
                    </div>

                    {{-- ===================== META ===================== --}}
                    <div class="px-5 pb-3 flex flex-wrap items-center gap-x-4 gap-y-1.5
                                text-[11px] text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $health['dot'] }}"></span>
                            <span class="font-semibold text-gray-600 dark:text-gray-300">
                                {{ $health['label'] }}
                            </span>
                        </span>

                        @if($ketua)
                            <span class="inline-flex items-center gap-1 truncate">
                                <i class="fas fa-crown text-amber-500"></i>
                                <span class="truncate">{{ $ketua }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-gray-400">
                                <i class="fas fa-user-slash"></i>
                                <span>Belum ada ketua</span>
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-1 truncate">
                            <i class="fas fa-user-cog"></i>
                            <span class="truncate">{{ optional($group->creator)->name ?? 'Sistem' }}</span>
                        </span>
                    </div>

                    {{-- ===================== ACTIONS ===================== --}}
                    <div class="mt-auto px-5 py-3 border-t border-gray-100 dark:border-gray-700
                                bg-gray-50/60 dark:bg-gray-900/30
                                flex items-center gap-2">

                        <a href="{{ route('owner.kelompok.anggota', ['groupId' => $group->id]) }}"
                           class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center
                                  bg-blue-50 dark:bg-blue-900/30
                                  text-blue-700 dark:text-blue-300
                                  hover:bg-blue-100 dark:hover:bg-blue-900/50
                                  transition">
                            <i class="fas fa-info-circle mr-1"></i> Detail
                        </a>

                        <a href="{{ route('owner.kelompok.akses', ['group_id' => $group->id]) }}"
                           class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center
                                  bg-red-50 dark:bg-red-900/30
                                  text-red-700 dark:text-red-300
                                  hover:bg-red-100 dark:hover:bg-red-900/50
                                  transition">
                            <i class="fas fa-key mr-1"></i> Akses
                        </a>

                        <a href="{{ route('owner.kelompok.invite', ['group_id' => $group->id]) }}"
                           class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center
                                  bg-purple-50 dark:bg-purple-900/30
                                  text-purple-700 dark:text-purple-300
                                  hover:bg-purple-100 dark:hover:bg-purple-900/50
                                  transition">
                            <i class="fas fa-user-plus mr-1"></i> Undang
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ========================================================= --}}
        {{-- PAGINATION
        {{-- ========================================================= --}}
        @if($groups->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $groups->links() }}
            </div>
        @endif

    @else
        {{-- ========================================================= --}}
        {{-- EMPTY STATE
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700
                    p-10 md:p-16 text-center">

            <div class="w-20 h-20 mx-auto rounded-2xl
                        bg-purple-50 dark:bg-purple-900/20
                        flex items-center justify-center mb-5">
                <i class="fas fa-layer-group text-4xl text-purple-400"></i>
            </div>

            @if($filters['q'] || $filters['status'] || $filters['health'])
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    Tidak ada kelompok yang cocok
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
                    Coba ubah kata kunci pencarian, atau reset filter untuk melihat semua kelompok.
                </p>
                <a href="{{ route('owner.kelompok.index') }}"
                   class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                          text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
            @else
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    Belum ada kelompok
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
                    Mulai dengan membuat kelompok pertama untuk mengorganisir anggota UKM UNICODE.
                </p>
                <a href="{{ route('owner.kelompok.custom') }}"
                   class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-purple-600 hover:bg-purple-700
                          text-white text-sm font-semibold shadow-sm transition">
                    <i class="fas fa-plus"></i> Buat Kelompok Pertama
                </a>
            @endif
        </div>
    @endif

</div>

{{-- ============================================================ --}}
{{-- LIVE INDICATOR + AUTO REFRESH COUNT (SINGLE REQUEST)
{{-- ============================================================ --}}
<div id="liveIndicator"
     class="fixed bottom-4 right-4 z-40 hidden md:flex items-center gap-2
            bg-white dark:bg-gray-800 rounded-full shadow-lg
            border border-gray-200 dark:border-gray-700 px-3.5 py-1.5
            text-xs font-semibold text-gray-600 dark:text-gray-300">
    <span class="relative flex h-2 w-2">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
    </span>
    <span>Live</span>
    <span class="text-gray-400" id="lastSyncText">• baru saja</span>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* =========================================================
     | AUTO-REFRESH JUMLAH ANGGOTA — SATU REQUEST UNTUK SEMUA
     | (menggantikan pola N-request yang lama)
     * ========================================================= */
    const endpoint = @json(route('owner.kelompok.members-counts'));
    const groupIds = @json($groups->pluck('id'));
    const indicator = document.getElementById('liveIndicator');
    const lastSyncText = document.getElementById('lastSyncText');

    if (!groupIds.length) return;

    let lastSyncAt = Date.now();

    function updateSyncText() {
        if (!lastSyncText) return;
        const diff = Math.floor((Date.now() - lastSyncAt) / 1000);
        if (diff < 5)         lastSyncText.textContent = '• baru saja';
        else if (diff < 60)   lastSyncText.textContent = `• ${diff}s lalu`;
        else                  lastSyncText.textContent = `• ${Math.floor(diff / 60)}m lalu`;
    }

    async function refreshCounts() {
        try {
            const res = await fetch(endpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) return;

            const data = await res.json(); // { "1": 12, "2": 8, ... }

            Object.entries(data).forEach(([id, count]) => {
                const el = document.getElementById(`member-count-${id}`);
                if (el && el.textContent !== String(count)) {
                    el.textContent = count;

                    // animasi kecil saat berubah
                    el.classList.add('text-emerald-600', 'dark:text-emerald-400');
                    setTimeout(() => {
                        el.classList.remove('text-emerald-600', 'dark:text-emerald-400');
                    }, 1200);
                }
            });

            lastSyncAt = Date.now();
            updateSyncText();

            // Show indicator
            if (indicator) indicator.style.opacity = '1';

        } catch (err) {
            console.warn('Refresh count gagal:', err);
        }
    }

    // Setiap 20 detik saja (bukan 10 detik) — cukup untuk kebutuhan owner
    setInterval(refreshCounts, 20000);
    setInterval(updateSyncText, 5000);

    // Refresh saat tab kembali aktif
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refreshCounts();
    });

})();
</script>
@endpush
@extends('layouts.roles.owner')

@section('title', 'Hak Akses Kelompok')

@push('styles')
<style>
/* ============================================================
 | RESPONSIVE
 * ============================================================ */
.member-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: .75rem;
}
@media (max-width: 900px) and (orientation: landscape) {
    .member-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (min-width: 1024px) {
    .member-list { display: block; }
}

.member-row { transition: background-color .15s ease; }
.member-row:hover { background-color: rgba(0,0,0,.015); }
.dark .member-row:hover { background-color: rgba(255,255,255,.02); }
.member-row.is-dirty {
    background-color: rgba(59, 130, 246, .04);
}
.dark .member-row.is-dirty {
    background-color: rgba(59, 130, 246, .08);
}

.capacity-bar-fill { transition: width .6s cubic-bezier(.4,0,.2,1); }

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

[x-cloak] { display: none !important; }

/* ============================================================
 | TOGGLE SWITCH
 * ============================================================ */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
    flex-shrink: 0;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    border-radius: 22px;
    transition: background-color .2s ease;
}
.dark .toggle-slider { background-color: #4b5563; }
.toggle-slider::before {
    content: '';
    position: absolute;
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: transform .2s cubic-bezier(.4,0,.2,1);
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}
.toggle-switch input:checked + .toggle-slider {
    background-color: #10b981;
}
.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(18px);
}
.toggle-switch input:focus-visible + .toggle-slider {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, .3);
}
.toggle-switch input:disabled + .toggle-slider {
    opacity: .35;
    cursor: not-allowed;
}

/* Toast */
.toast-stack {
    position: fixed;
    top: 1rem; right: 1rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    pointer-events: none;
}
.toast {
    pointer-events: auto;
    min-width: 260px;
    max-width: 380px;
    padding: .75rem 1rem;
    border-radius: .75rem;
    box-shadow: 0 10px 25px -10px rgba(0,0,0,.2);
    font-size: .8rem;
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    background: #fff;
    border-left: 4px solid #10b981;
    animation: toastIn .25s ease;
}
.toast.error { border-left-color: #ef4444; }
.dark .toast { background: #1f2937; color: #f3f4f6; }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Sticky save bar */
.save-bar {
    transform: translateY(120%);
    transition: transform .25s cubic-bezier(.4,0,.2,1);
}
.save-bar.active { transform: translateY(0); }
</style>
@endpush

@section('content')

@php
    // Hitung stats di Blade dari koleksi yang sudah ada — tidak perlu query tambahan
    $totalUsers     = $users->count();
    $totalMembers   = $users->where('is_member', true)->count();
    $totalNonMember = $users->where('is_member', false)->count();
    $totalAdmin     = $users->whereIn('level_id', [1, 2])->count();
@endphp

<div class="p-4 md:p-6 max-w-[1600px] mx-auto"
     x-data="accessPage()"
     x-cloak>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}"
           class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700
                  hover:bg-gray-200 dark:hover:bg-gray-600
                  flex items-center justify-center text-gray-600 dark:text-gray-300
                  transition shrink-0"
           aria-label="Kembali">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <div class="min-w-0 flex-1">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white
                       flex items-center gap-2">
                <i class="fas fa-key text-red-500"></i>
                Hak Akses Kelompok
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Atur siapa yang boleh melihat, mengumumkan, mengirim pesan, dan mengelola anggota.
            </p>
        </div>

        @if($selectedGroup)
            <a href="{{ route('owner.kelompok.invite', ['group_id' => $selectedGroup->id]) }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700
                      text-white text-sm font-semibold shadow-sm
                      transition flex items-center gap-2 shrink-0">
                <i class="fas fa-user-plus"></i>
                <span class="hidden sm:inline">Tambah Anggota</span>
            </a>
        @endif
    </div>

    {{-- SESSION ALERTS --}}
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

    @if($groups->isEmpty())
        {{-- EMPTY: belum ada kelompok --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700
                    p-10 md:p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl
                        bg-red-50 dark:bg-red-900/20
                        flex items-center justify-center mb-5">
                <i class="fas fa-key text-4xl text-red-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Belum ada kelompok</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
                Buat kelompok terlebih dahulu sebelum mengatur hak akses.
            </p>
            <a href="{{ route('owner.kelompok.custom') }}"
               class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                      bg-purple-600 hover:bg-purple-700 text-white text-sm
                      font-semibold shadow-sm transition">
                <i class="fas fa-plus"></i> Buat Kelompok
            </a>
        </div>
    @else

        {{-- ========================================================= --}}
        {{-- GROUP SELECTOR --}}
        {{-- ========================================================= --}}
        <form method="GET" action="{{ route('owner.kelompok.akses') }}"
              id="groupPicker"
              @submit="confirmNavigation($event)"
              class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                     border border-gray-200 dark:border-gray-700 p-4 md:p-5 mb-5">
            <label for="group_id"
                   class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Pilih Kelompok
            </label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select id="group_id" name="group_id" required
                        class="flex-1 rounded-xl border-gray-300 dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                               text-sm px-4 py-2.5 focus:ring-2 focus:ring-red-500
                               focus:border-red-500">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}"
                                {{ (int) $selectedGroupId === $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700
                               text-white text-sm font-semibold
                               transition flex items-center justify-center gap-2 shrink-0 shadow-sm">
                    <i class="fas fa-arrow-right"></i> Tampilkan
                </button>
            </div>
        </form>

        @if($selectedGroup)
            @php
                $health    = $selectedGroup->health_status ?? ['label' => 'Normal', 'dot' => 'bg-emerald-500'];
                $pct       = $selectedGroup->capacity_percentage ?? null;
                $hasLimit  = $selectedGroup->max_members && $selectedGroup->max_members > 0;
                $memberCount = $selectedGroup->members->count();
                $slotsLeft = $hasLimit ? max(0, $selectedGroup->max_members - $memberCount) : null;
            @endphp

            {{-- ========================================================= --}}
            {{-- GROUP HEADER --}}
            {{-- ========================================================= --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700 p-5 mb-5">
                <div class="flex flex-col md:flex-row md:items-center gap-4">

                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-xl shrink-0
                                    bg-gradient-to-br from-red-500 to-rose-600
                                    flex items-center justify-center
                                    text-white font-bold text-xl shadow-sm">
                            {{ strtoupper(substr($selectedGroup->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="font-bold text-lg text-gray-800 dark:text-white truncate">
                                {{ $selectedGroup->name }}
                            </h2>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1
                                        text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                @if($selectedGroup->code)
                                    <span class="font-mono">{{ $selectedGroup->code }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $health['dot'] }}"></span>
                                    {{ $health['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-64 shrink-0">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-500 dark:text-gray-400">Kapasitas</span>
                            <span class="font-bold text-gray-800 dark:text-white">
                                {{ $memberCount }}
                                @if($hasLimit) / {{ $selectedGroup->max_members }} @endif
                            </span>
                        </div>
                        @if($hasLimit)
                            <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="capacity-bar-fill h-full rounded-full
                                            {{ $pct >= 100 ? 'bg-red-500'
                                                : ($pct >= 80 ? 'bg-orange-500' : 'bg-emerald-500') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="text-[10px] text-gray-400 mt-1">
                                {{ $pct }}% terpakai
                                @if($slotsLeft !== null && $slotsLeft > 0)
                                    • sisa {{ $slotsLeft }} slot
                                @endif
                            </div>
                        @else
                            <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700"></div>
                            <div class="text-[10px] text-gray-400 mt-1">Tidak dibatasi</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- STATS ROW --}}
            {{-- ========================================================= --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-5">

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total User</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $totalUsers }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-user-check text-emerald-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Anggota</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $totalMembers }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-user-plus text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Belum Anggota</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $totalNonMember }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-100 dark:bg-red-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-crown text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Owner/Superadmin</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $totalAdmin }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- TOOLBAR — SEARCH + FILTER --}}
            {{-- ========================================================= --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        p-4 md:p-5 mb-5">

                <div class="flex flex-col md:flex-row gap-3 mb-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2
                                  text-gray-400 text-sm"></i>
                        <input type="text"
                               x-model="search"
                               placeholder="Cari nama atau NIM..."
                               autocomplete="off"
                               aria-label="Cari user"
                               class="w-full pl-11 pr-10 py-2.5 rounded-xl
                                      border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white text-sm
                                      focus:ring-2 focus:ring-red-500
                                      focus:border-red-500">
                        <button type="button"
                                x-show="search.length > 0"
                                @click="search = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       text-gray-400 hover:text-gray-600
                                       w-6 h-6 rounded-full flex items-center justify-center"
                                aria-label="Hapus pencarian">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">
                        Filter:
                    </span>
                    <button type="button"
                            @click="setFilter('all')"
                            :class="filter === 'all'
                                ? 'bg-red-600 text-white shadow-sm'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Semua ({{ $totalUsers }})
                    </button>
                    <button type="button"
                            @click="setFilter('member')"
                            :class="filter === 'member'
                                ? 'bg-red-600 text-white shadow-sm'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Anggota ({{ $totalMembers }})
                    </button>
                    <button type="button"
                            @click="setFilter('non-member')"
                            :class="filter === 'non-member'
                                ? 'bg-red-600 text-white shadow-sm'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Belum Anggota ({{ $totalNonMember }})
                    </button>
                    <button type="button"
                            @click="setFilter('special')"
                            :class="filter === 'special'
                                ? 'bg-red-600 text-white shadow-sm'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Akses Khusus
                    </button>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- PERMISSION FORM --}}
            {{-- ========================================================= --}}
            <form action="{{ route('owner.kelompok.update-permissions', $selectedGroup->id) }}"
                  method="POST"
                  id="permissionForm"
                  @submit="onSubmit($event)">

                @csrf

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700">

                    {{-- Desktop header --}}
                    <div class="hidden lg:grid grid-cols-12 gap-3 px-5 py-3
                                bg-gray-50 dark:bg-gray-900/40
                                rounded-t-2xl
                                border-b border-gray-200 dark:border-gray-700
                                text-[11px] font-bold uppercase tracking-wider
                                text-gray-500 dark:text-gray-400">
                        <div class="col-span-3">Nama / NIM</div>
                        <div class="col-span-2">Level</div>
                        <div class="col-span-2">Posisi</div>
                        <div class="col-span-4 grid grid-cols-4 gap-1 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span>Lihat</span>
                                <button type="button"
                                        @click="toggleColumn('can_view')"
                                        class="text-[9px] font-medium text-red-500 hover:text-red-700
                                               normal-case tracking-normal">
                                    toggle all
                                </button>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span>Umumkan</span>
                                <button type="button"
                                        @click="toggleColumn('can_announcement')"
                                        class="text-[9px] font-medium text-red-500 hover:text-red-700
                                               normal-case tracking-normal">
                                    toggle all
                                </button>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span>Pesan</span>
                                <button type="button"
                                        @click="toggleColumn('can_message')"
                                        class="text-[9px] font-medium text-red-500 hover:text-red-700
                                               normal-case tracking-normal">
                                    toggle all
                                </button>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span>Kelola</span>
                                <button type="button"
                                        @click="toggleColumn('can_manage_members')"
                                        class="text-[9px] font-medium text-red-500 hover:text-red-700
                                               normal-case tracking-normal">
                                    toggle all
                                </button>
                            </div>
                        </div>
                        <div class="col-span-1 text-right">Aksi</div>
                    </div>

                    <div class="member-list p-2 md:p-3 lg:p-0">

                        @forelse($users as $index => $u)
                            @php
                                $isMember  = (bool) $u->is_member;
                                $isAdmin   = in_array($u->level_id, [1, 2], true);
                                $isKetua   = ($u->position ?? null) === 'ketua';
                                $isDirty   = false; // dihitung via Alpine
                                $searchTxt = strtolower($u->name . ' ' . ($u->nim ?? ''));
                                $filterTag = $isMember ? 'member' : 'non-member';
                                $hasSpecial = $isMember && (
                                    $u->can_announcement ||
                                    $u->can_manage_members ||
                                    ! $u->can_message
                                );
                            @endphp

                            <div class="member-row user-row
                                        lg:grid lg:grid-cols-12 lg:gap-3 lg:items-center
                                        px-4 md:px-5 py-4
                                        border-b border-gray-100 dark:border-gray-700
                                        last:border-b-0
                                        relative
                                        rounded-xl lg:rounded-none
                                        bg-white dark:bg-gray-800 lg:bg-transparent dark:lg:bg-transparent
                                        border border-gray-200 dark:border-gray-700 lg:border-0 lg:border-b"
                                 data-name="{{ $searchTxt }}"
                                 data-filter="{{ $filterTag }}"
                                 data-special="{{ $hasSpecial ? '1' : '0' }}"
                                 data-user-id="{{ $u->id }}"
                                 :class="{ 'is-dirty': isDirty({{ $u->id }}) }">

                                {{-- ==================== IDENTITY ==================== --}}
                                <div class="lg:col-span-3 flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full shrink-0
                                                bg-gradient-to-br from-red-400 to-rose-500
                                                flex items-center justify-center
                                                text-white font-bold text-sm">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-semibold text-sm text-gray-800 dark:text-white
                                                    line-clamp-1">
                                            {{ $u->name }}
                                        </div>
                                        @if($u->nim)
                                            <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5
                                                        font-mono">
                                                NIM {{ $u->nim }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ==================== LEVEL ==================== --}}
                                <div class="lg:col-span-2 mt-3 lg:mt-0">
                                    <span class="lg:hidden text-[11px] text-gray-400 block mb-1">Level:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                                 text-[11px] font-semibold
                                                 {{ $isAdmin
                                                     ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                                                     : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' }}">
                                        {{ $u->level->name ?? 'Level ' . $u->level_id }}
                                    </span>
                                </div>

                                {{-- ==================== POSISI ==================== --}}
                                <div class="lg:col-span-2 mt-2 lg:mt-0">
                                    <span class="lg:hidden text-[11px] text-gray-400 block mb-1">Posisi:</span>
                                    @if($isMember)
                                        @php
                                            $posColor = match ($u->position) {
                                                'ketua'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                'pengurus' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                default    => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                     text-[11px] font-semibold {{ $posColor }}">
                                            <i class="fas {{ $isKetua ? 'fa-crown' : 'fa-user' }} text-[9px]"></i>
                                            {{ ucfirst($u->position ?? 'Anggota') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                     text-[11px] font-semibold
                                                     bg-gray-100 dark:bg-gray-700
                                                     text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-user-slash text-[9px]"></i>
                                            Belum Anggota
                                        </span>
                                    @endif
                                </div>

                                {{-- ==================== PERMISSIONS ==================== --}}
                                <div class="lg:col-span-4 mt-3 lg:mt-0">
                                    <div class="grid grid-cols-4 gap-1 lg:gap-2">
                                        @foreach([
                                            'can_view'           => ['Lihat',    'fa-eye',       'Boleh melihat kelompok'],
                                            'can_announcement'   => ['Umumkan',  'fa-bullhorn',  'Boleh membuat pengumuman'],
                                            'can_message'        => ['Pesan',    'fa-comment',   'Boleh mengirim pesan'],
                                            'can_manage_members' => ['Kelola',   'fa-users-cog', 'Boleh kelola anggota'],
                                        ] as $field => [$label, $icon, $title])
                                            <div class="flex flex-col items-center gap-1">
                                                <label class="lg:hidden text-[10px] text-gray-400">{{ $label }}</label>
                                                <label class="toggle-switch" title="{{ $title }}">
                                                    <input type="checkbox"
                                                           name="permissions[{{ $index }}][{{ $field }}]"
                                                           value="1"
                                                           :disabled="{{ $isMember ? 'false' : 'true' }}"
                                                           x-model="current[{{ $u->id }}].{{ $field }}"
                                                           @change="markDirty({{ $u->id }})"
                                                           @if($isMember && $u->{$field}) checked @endif>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- ==================== AKSI ==================== --}}
                                <div class="lg:col-span-1 mt-3 lg:mt-0 flex justify-end">
                                    @if($isMember)
                                        <input type="hidden"
                                               name="permissions[{{ $index }}][user_id]"
                                               value="{{ $u->id }}">

                                        @if($isAdmin)
                                            <span class="text-[10px] text-gray-400 text-right">
                                                <i class="fas fa-lock mr-1"></i> Terkunci
                                            </span>
                                        @else
                                            <div class="relative" x-data="{ open: false }">
                                                <button type="button"
                                                        @click="open = !open"
                                                        class="w-8 h-8 rounded-lg
                                                               hover:bg-gray-100 dark:hover:bg-gray-700
                                                               flex items-center justify-center
                                                               text-gray-500 dark:text-gray-400
                                                               transition"
                                                        aria-label="Aksi">
                                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                                </button>

                                                <div x-show="open"
                                                     x-cloak
                                                     x-transition
                                                     @click.away="open = false"
                                                     class="absolute right-0 mt-1 w-52 z-50
                                                            bg-white dark:bg-gray-800
                                                            rounded-xl shadow-lg
                                                            border border-gray-200 dark:border-gray-700
                                                            overflow-hidden text-sm">

                                                    <button type="button"
                                                            @click="open = false; presetRole({{ $u->id }}, 'ketua')"
                                                            class="w-full text-left px-4 py-2.5
                                                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                                                   flex items-center gap-2
                                                                   text-gray-700 dark:text-gray-200">
                                                        <i class="fas fa-crown text-amber-500 w-4 shrink-0"></i>
                                                        <span>Preset: Ketua</span>
                                                    </button>
                                                    <button type="button"
                                                            @click="open = false; presetRole({{ $u->id }}, 'pengurus')"
                                                            class="w-full text-left px-4 py-2.5
                                                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                                                   flex items-center gap-2
                                                                   text-gray-700 dark:text-gray-200">
                                                        <i class="fas fa-user-tie text-blue-500 w-4 shrink-0"></i>
                                                        <span>Preset: Pengurus</span>
                                                    </button>
                                                    <button type="button"
                                                            @click="open = false; presetRole({{ $u->id }}, 'anggota')"
                                                            class="w-full text-left px-4 py-2.5
                                                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                                                   flex items-center gap-2
                                                                   text-gray-700 dark:text-gray-200">
                                                        <i class="fas fa-user text-gray-500 w-4 shrink-0"></i>
                                                        <span>Preset: Anggota</span>
                                                    </button>

                                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                                                    <button type="button"
                                                            @click="open = false; resetRow({{ $u->id }})"
                                                            class="w-full text-left px-4 py-2.5
                                                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                                                   flex items-center gap-2
                                                                   text-gray-600 dark:text-gray-300">
                                                        <i class="fas fa-undo text-gray-400 w-4 shrink-0"></i>
                                                        <span>Reset ke Awal</span>
                                                    </button>

                                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                                                    <button type="button"
                                                            @click="open = false; openRemove({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                                            class="w-full text-left px-4 py-2.5
                                                                   hover:bg-red-50 dark:hover:bg-red-900/20
                                                                   flex items-center gap-2
                                                                   text-red-600 dark:text-red-400">
                                                        <i class="fas fa-user-minus w-4 shrink-0"></i>
                                                        <span>Keluarkan dari Kelompok</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <a href="{{ route('owner.kelompok.invite', ['group_id' => $selectedGroup->id]) }}"
                                           class="px-3 py-2 rounded-lg text-xs font-semibold
                                                  bg-emerald-50 dark:bg-emerald-900/20
                                                  text-emerald-600 dark:text-emerald-400
                                                  hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                                  transition flex items-center gap-1.5">
                                            <i class="fas fa-plus"></i>
                                            <span class="lg:hidden">Tambah</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-10 md:p-16 text-center text-gray-500 dark:text-gray-400">
                                <div class="w-16 h-16 mx-auto rounded-2xl
                                            bg-red-50 dark:bg-red-900/20
                                            flex items-center justify-center mb-4">
                                    <i class="fas fa-user-slash text-3xl text-red-400"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white mb-1">
                                    Tidak ada user
                                </h3>
                                <p class="text-sm max-w-md mx-auto">
                                    Tidak ada user eligible untuk diatur hak aksesnya.
                                </p>
                            </div>
                        @endforelse

                        {{-- No search result --}}
                        <div id="noResult"
                             class="hidden p-10 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-search text-3xl mb-3 block opacity-30"></i>
                            <p class="text-sm">Tidak ada user yang cocok dengan pencarian/filter.</p>
                        </div>
                    </div>
                </div>

                {{-- ========================================================= --}}
                {{-- STICKY SAVE BAR --}}
                {{-- ========================================================= --}}
                <div x-show="hasChanges()"
                     x-transition
                     x-cloak
                     class="save-bar active fixed bottom-0 left-0 right-0 z-40
                            bg-white dark:bg-gray-800
                            border-t-2 border-blue-500
                            shadow-2xl">
                    <div class="max-w-[1600px] mx-auto px-4 md:px-6 py-3
                                flex items-center gap-3">
                        <div class="flex items-center gap-2 text-sm flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30
                                        flex items-center justify-center shrink-0">
                                <i class="fas fa-pencil-alt text-blue-600 text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-gray-800 dark:text-white text-sm">
                                    <span x-text="dirtyCount()"></span> perubahan belum disimpan
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                    Klik Simpan untuk menerapkan, atau Batal untuk membuang.
                                </div>
                            </div>
                        </div>

                        <button type="button"
                                @click="resetAll()"
                                class="px-4 py-2 rounded-lg text-xs font-semibold
                                       bg-gray-100 dark:bg-gray-700
                                       text-gray-600 dark:text-gray-300
                                       hover:bg-gray-200 dark:hover:bg-gray-600
                                       transition">
                            Batal
                        </button>

                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-xs font-bold text-white
                                       bg-blue-600 hover:bg-blue-700
                                       shadow-sm transition flex items-center gap-1.5">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

                <div class="h-24"></div> {{-- spacer for sticky bar --}}
            </form>

            {{-- ========================================================= --}}
            {{-- MODAL — KELUARKAN ANGGOTA --}}
            {{-- ========================================================= --}}
            <div x-show="modal.remove"
                 x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4
                        bg-black/50 backdrop-blur-sm">
                <div @click.away="modal.remove = null"
                     x-show="modal.remove"
                     x-transition
                     class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                            max-w-md w-full p-6">

                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-red-100 dark:bg-red-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-user-minus text-red-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 dark:text-white">Keluarkan Anggota</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Tindakan ini akan menghapus anggota dari kelompok.
                            </p>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20
                                border border-red-200 dark:border-red-700 mb-5">
                        <p class="text-sm text-red-800 dark:text-red-200">
                            Anda akan mengeluarkan
                            <strong x-text="modal.remove?.name || ''"></strong>
                            dari kelompok <strong>{{ $selectedGroup->name }}</strong>.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button type="button"
                                @click="modal.remove = null"
                                class="flex-1 px-4 py-2.5 rounded-xl
                                       bg-gray-100 dark:bg-gray-700
                                       hover:bg-gray-200 dark:hover:bg-gray-600
                                       text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="button"
                                @click="confirmRemove()"
                                class="flex-1 px-4 py-2.5 rounded-xl
                                       bg-red-600 hover:bg-red-700
                                       text-white text-sm font-bold shadow-sm transition">
                            <i class="fas fa-user-minus mr-1"></i> Keluarkan
                        </button>
                    </div>
                </div>
            </div>

        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        p-10 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-hand-point-up text-3xl mb-3 block opacity-30"></i>
                <p class="text-sm">Pilih kelompok dari dropdown di atas.</p>
            </div>
        @endif
    @endif

</div>

{{-- TOAST STACK --}}
<div id="toastStack" class="toast-stack"></div>

{{-- Hidden remove form --}}
<form id="removeForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function accessPage() {
    return {
        search: '',
        filter: 'all',
        modal: { remove: null },
        current: {},   // { userId: { can_view, can_announcement, can_message, can_manage_members } }
        original: {},  // snapshot awal

        init() {
            // Inisialisasi dari DOM
            document.querySelectorAll('.user-row').forEach(row => {
                const uid = parseInt(row.dataset.userId, 10);
                const isMember = row.dataset.filter === 'member';

                this.current[uid]  = this.readPermissions(uid, isMember);
                this.original[uid] = { ...this.current[uid] };

                // Bind visibility ke search + filter
                this.$watch('search', () => this.applyRowVisibility(row, uid));
                this.$watch('filter', () => this.applyRowVisibility(row, uid));

                // Initial call
                this.applyRowVisibility(row, uid);
            });
        },

        readPermissions(uid, isMember) {
            const defaults = {
                can_view: false,
                can_announcement: false,
                can_message: false,
                can_manage_members: false,
            };
            if (!isMember) return defaults;

            // Ambil nilai aktual dari checkbox yang sudah di-render Blade
            const row = document.querySelector(`.user-row[data-user-id="${uid}"]`);
            ['can_view','can_announcement','can_message','can_manage_members'].forEach(f => {
                const input = row.querySelector(`input[name*="[${f}]"]`);
                defaults[f] = input ? input.checked : false;
            });
            return defaults;
        },

        applyRowVisibility(row, uid) {
            const q = (this.search || '').toLowerCase().trim();
            const nameMatch = !q || (row.dataset.name || '').includes(q);

            let filterMatch = true;
            switch (this.filter) {
                case 'member':     filterMatch = row.dataset.filter === 'member'; break;
                case 'non-member': filterMatch = row.dataset.filter === 'non-member'; break;
                case 'special':    filterMatch = row.dataset.special === '1'; break;
            }

            row.style.display = (nameMatch && filterMatch) ? '' : 'none';
            this.updateNoResult();
        },

        updateNoResult() {
            const rows = document.querySelectorAll('.user-row');
            const visible = Array.from(rows).filter(r => r.style.display !== 'none').length;
            const empty = document.getElementById('noResult');
            if (empty) empty.classList.toggle('hidden', visible > 0);
        },

        setFilter(f) {
            this.filter = f;
        },

        isDirty(uid) {
            const a = this.original[uid];
            const b = this.current[uid];
            if (!a || !b) return false;
            return ['can_view','can_announcement','can_message','can_manage_members']
                .some(k => !!a[k] !== !!b[k]);
        },

        dirtyCount() {
            return Object.keys(this.current)
                .filter(uid => this.isDirty(parseInt(uid, 10)))
                .length;
        },

        hasChanges() {
            return this.dirtyCount() > 0;
        },

        markDirty() {
            // Memicu reaktivitas Alpine (dipanggil via @change)
        },

        // ---- Column toggle ----
        toggleColumn(field) {
            const visible = this.visibleMemberRows();
            if (visible.length === 0) return;

            // Tentukan target: kalau semua sudah ON → matikan; kalau ada yang OFF → nyalakan
            const allOn = visible.every(uid => this.current[uid][field]);
            const target = !allOn;

            visible.forEach(uid => {
                this.current[uid][field] = target;
                // Update checkbox DOM
                const input = document.querySelector(
                    `.user-row[data-user-id="${uid}"] input[name*="[${field}]"]`
                );
                if (input && ! input.disabled) input.checked = target;
            });
        },

        // ---- Preset posisi ----
        presetRole(uid, role) {
            const presets = {
                ketua: {
                    can_view: true,
                    can_announcement: true,
                    can_message: true,
                    can_manage_members: true,
                },
                pengurus: {
                    can_view: true,
                    can_announcement: false,
                    can_message: true,
                    can_manage_members: false,
                },
                anggota: {
                    can_view: true,
                    can_announcement: false,
                    can_message: false,
                    can_manage_members: false,
                },
            };

            const preset = presets[role];
            if (!preset) return;

            Object.keys(preset).forEach(field => {
                this.current[uid][field] = preset[field];
                const input = document.querySelector(
                    `.user-row[data-user-id="${uid}"] input[name*="[${field}]"]`
                );
                if (input && ! input.disabled) input.checked = preset[field];
            });

            this.showToast(`Preset "${role}" diterapkan. Jangan lupa Simpan.`);
        },

        // ---- Reset row ----
        resetRow(uid) {
            const snapshot = this.original[uid];
            Object.keys(snapshot).forEach(field => {
                this.current[uid][field] = snapshot[field];
                const input = document.querySelector(
                    `.user-row[data-user-id="${uid}"] input[name*="[${field}]"]`
                );
                if (input && ! input.disabled) input.checked = snapshot[field];
            });
            this.showToast('Perubahan baris dibatalkan.');
        },

        // ---- Reset all ----
        resetAll() {
            Object.keys(this.original).forEach(uid => {
                this.resetRow(parseInt(uid, 10));
            });
            this.showToast('Semua perubahan dibatalkan.');
        },

        // ---- Visible member rows ----
        visibleMemberRows() {
            return Array.from(document.querySelectorAll('.user-row'))
                .filter(r => r.style.display !== 'none' && r.dataset.filter === 'member')
                .map(r => parseInt(r.dataset.userId, 10));
        },

        // ---- Remove member ----
        openRemove(uid, name) {
            this.modal.remove = { id: uid, name };
        },

        confirmRemove() {
            if (! this.modal.remove) return;

            const form = document.getElementById('removeForm');
            form.action = `{{ url('owner/kelompok') }}/{{ $selectedGroup?->id ?? 0 }}/remove-member/${this.modal.remove.id}`;
            form.submit();
        },

        // ---- Navigasi — warning jika ada perubahan belum disimpan ----
        confirmNavigation(event) {
            if (this.hasChanges()) {
                const ok = confirm('Ada perubahan yang belum disimpan. Yakin ingin pindah kelompok?');
                if (! ok) event.preventDefault();
            }
        },

        // ---- Submit ----
        onSubmit(event) {
            if (! this.hasChanges()) {
                event.preventDefault();
                this.showToast('Tidak ada perubahan untuk disimpan.', 'error');
                return;
            }

            const btn = event.submitter;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            }
        },

        // ---- Toast ----
        showToast(msg, type = 'success') {
            const stack = document.getElementById('toastStack');
            if (! stack) return;
            const el = document.createElement('div');
            el.className = 'toast' + (type === 'error' ? ' error' : '');
            el.innerHTML = `
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle text-red-500' : 'fa-check-circle text-emerald-500'} mt-0.5"></i>
                <div class="flex-1">${msg}</div>
            `;
            stack.appendChild(el);
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transform = 'translateX(20px)';
                el.style.transition = 'all .25s ease';
                setTimeout(() => el.remove(), 300);
            }, 3500);
        },
    };
}
</script>
@endpush
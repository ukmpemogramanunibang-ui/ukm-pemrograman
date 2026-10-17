@extends('layouts.roles.owner')

@section('title', 'Anggota Kelompok')

@push('styles')
<style>
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

.capacity-bar-fill { transition: width .6s cubic-bezier(.4,0,.2,1); }

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

[x-cloak] { display: none !important; }

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
</style>
@endpush

@section('content')

<div class="p-4 md:p-6 max-w-[1400px] mx-auto"
     x-data="memberPage()"
     x-cloak>

    {{-- HEADER --}}
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
                <i class="fas fa-users text-blue-500"></i>
                Anggota Kelompok
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Kelola anggota, posisi, dan struktur kelompok.
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-700
                    p-10 md:p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-2xl
                        bg-purple-50 dark:bg-purple-900/20
                        flex items-center justify-center mb-5">
                <i class="fas fa-layer-group text-4xl text-purple-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Belum ada kelompok</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
                Buat kelompok terlebih dahulu sebelum melihat anggotanya.
            </p>
            <a href="{{ route('owner.kelompok.custom') }}"
               class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                      bg-purple-600 hover:bg-purple-700 text-white text-sm
                      font-semibold shadow-sm transition">
                <i class="fas fa-plus"></i> Buat Kelompok
            </a>
        </div>
    @else

        {{-- GROUP SELECTOR --}}
        <form method="GET" action="{{ route('owner.kelompok.anggota') }}"
              class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                     border border-gray-200 dark:border-gray-700 p-4 md:p-5 mb-5">
            <label for="group_id"
                   class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Pilih Kelompok
            </label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select id="group_id" name="group_id" required
                        onchange="this.form.submit()"
                        class="flex-1 rounded-xl border-gray-300 dark:border-gray-600
                               bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                               text-sm px-4 py-2.5 focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}"
                                {{ (int) $selectedGroupId === $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700
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
                $activeCount = $stats['total'] ?? $members->where('status', 'aktif')->count();
                $slotsLeft = $hasLimit ? max(0, $selectedGroup->max_members - $activeCount) : null;
                $isFull    = $hasLimit && $activeCount >= $selectedGroup->max_members;
            @endphp

            {{-- GROUP HEADER --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700 p-5 mb-5">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-xl shrink-0
                                    bg-gradient-to-br from-blue-500 to-indigo-600
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
                                {{ $activeCount }}
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

            {{-- STATS ROW --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-5">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $stats['total'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-crown text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Ketua</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $stats['ketua'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-user-tie text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Pengurus</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $stats['pengurus'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-emerald-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Anggota</div>
                            <div class="text-xl font-extrabold text-gray-800 dark:text-white">
                                {{ $stats['anggota'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOOLBAR --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        p-4 md:p-5 mb-5">
                <div class="flex flex-col md:flex-row gap-3 mb-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2
                                  text-gray-400 text-sm"></i>
                        <input type="text" id="searchInput"
                               placeholder="Cari nama atau NIM anggota..."
                               autocomplete="off"
                               aria-label="Cari anggota"
                               class="w-full pl-11 pr-10 py-2.5 rounded-xl
                                      border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white text-sm
                                      focus:ring-2 focus:ring-blue-500
                                      focus:border-blue-500">
                        <button type="button" id="clearSearch"
                                class="hidden absolute right-3 top-1/2 -translate-y-1/2
                                       text-gray-400 hover:text-gray-600
                                       w-6 h-6 rounded-full flex items-center justify-center"
                                aria-label="Hapus pencarian">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">
                        Filter posisi:
                    </span>
                    <button type="button" data-filter="all"
                            class="filter-chip active px-3 py-1.5 rounded-lg text-xs font-semibold
                                   transition bg-blue-600 text-white">
                        Semua ({{ $stats['total'] }})
                    </button>
                    <button type="button" data-filter="ketua"
                            class="filter-chip px-3 py-1.5 rounded-lg text-xs font-semibold
                                   transition bg-gray-100 dark:bg-gray-700
                                   text-gray-600 dark:text-gray-300
                                   hover:bg-gray-200 dark:hover:bg-gray-600">
                        Ketua ({{ $stats['ketua'] }})
                    </button>
                    <button type="button" data-filter="pengurus"
                            class="filter-chip px-3 py-1.5 rounded-lg text-xs font-semibold
                                   transition bg-gray-100 dark:bg-gray-700
                                   text-gray-600 dark:text-gray-300
                                   hover:bg-gray-200 dark:hover:bg-gray-600">
                        Pengurus ({{ $stats['pengurus'] }})
                    </button>
                    <button type="button" data-filter="anggota"
                            class="filter-chip px-3 py-1.5 rounded-lg text-xs font-semibold
                                   transition bg-gray-100 dark:bg-gray-700
                                   text-gray-600 dark:text-gray-300
                                   hover:bg-gray-200 dark:hover:bg-gray-600">
                        Anggota ({{ $stats['anggota'] }})
                    </button>
                </div>
            </div>

            {{-- MEMBER LIST — 🔥 FIX: tanpa overflow-hidden --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700">

                {{-- Desktop header — rounded-t-2xl dipertahankan --}}
                <div class="hidden lg:grid grid-cols-12 gap-3 px-5 py-3
                            bg-gray-50 dark:bg-gray-900/40
                            rounded-t-2xl
                            border-b border-gray-200 dark:border-gray-700
                            text-[11px] font-bold uppercase tracking-wider
                            text-gray-500 dark:text-gray-400">
                    <div class="col-span-4">Nama / NIM</div>
                    <div class="col-span-2">Posisi</div>
                    <div class="col-span-2">Status</div>
                    <div class="col-span-2">Bergabung</div>
                    <div class="col-span-2 text-right">Aksi</div>
                </div>

                <div id="memberList" class="member-list">

                    @forelse($members as $member)
                        @php
                            $u = $member->user;
                            if (! $u) continue;

                            $searchText = strtolower($u->name . ' ' . ($u->nim ?? ''));
                            $position   = $member->position;

                            $posColor = match ($position) {
                                'ketua'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                'pengurus' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                default    => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            };
                            $posIcon = match ($position) {
                                'ketua'    => 'fa-crown',
                                'pengurus' => 'fa-user-tie',
                                default    => 'fa-user',
                            };
                        @endphp

                        <div class="member-row user-row
                                    lg:grid lg:grid-cols-12 lg:gap-3 lg:items-center
                                    px-4 md:px-5 py-4
                                    border-b border-gray-100 dark:border-gray-700
                                    last:border-b-0
                                    relative"
                             data-name="{{ $searchText }}"
                             data-position="{{ $position }}"
                             data-status="{{ $member->status }}">

                            <div class="lg:col-span-4 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full shrink-0
                                            bg-gradient-to-br from-blue-400 to-indigo-500
                                            flex items-center justify-center
                                            text-white font-bold text-sm">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white
                                                line-clamp-1">
                                        {{ $u->name }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5
                                                space-y-0.5">
                                        @if($u->nim)
                                            <div class="font-mono">NIM {{ $u->nim }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-2 mt-3 lg:mt-0">
                                <span class="lg:hidden text-[11px] text-gray-400 block mb-1">Posisi:</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             text-[11px] font-semibold {{ $posColor }}">
                                    <i class="fas {{ $posIcon }} text-[9px]"></i>
                                    {{ ucfirst($position) }}
                                </span>
                            </div>

                            <div class="lg:col-span-2 mt-2 lg:mt-0">
                                <span class="lg:hidden text-[11px] text-gray-400 block mb-1">Status:</span>
                                @if($member->status === 'aktif')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-[11px] font-semibold
                                                 bg-emerald-100 dark:bg-emerald-900/30
                                                 text-emerald-700 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-[11px] font-semibold
                                                 bg-gray-100 dark:bg-gray-700
                                                 text-gray-600 dark:text-gray-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        {{ ucfirst($member->status) }}
                                    </span>
                                @endif
                            </div>

                            <div class="lg:col-span-2 mt-2 lg:mt-0">
                                <span class="lg:hidden text-[11px] text-gray-400 block mb-1">Bergabung:</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $member->joined_at
                                        ? \Carbon\Carbon::parse($member->joined_at)->translatedFormat('d M Y')
                                        : '-' }}
                                </span>
                            </div>

                            {{-- Actions — 🔥 FIX: z-index tinggi agar dropdown tidak ketimpa --}}
                            <div class="lg:col-span-2 mt-3 lg:mt-0 flex justify-end">
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

                                    {{-- 🔥 z-50 --}}
                                    <div x-show="open"
                                         x-transition
                                         x-cloak
                                         @click.away="open = false"
                                         class="absolute right-0 mt-1 w-52 z-50
                                                bg-white dark:bg-gray-800
                                                rounded-xl shadow-lg
                                                border border-gray-200 dark:border-gray-700
                                                overflow-hidden text-sm">

                                        <button type="button"
                                                @click="open = false; openRole(@js([
                                                    'id'       => $member->id,
                                                    'name'     => $u->name,
                                                    'position' => $member->position,
                                                ]))"
                                                class="w-full text-left px-4 py-2.5
                                                       hover:bg-gray-50 dark:hover:bg-gray-700
                                                       flex items-center gap-2
                                                       text-gray-700 dark:text-gray-200">
                                            <i class="fas fa-user-tag text-blue-500 w-4 shrink-0"></i>
                                            <span>Ubah Posisi</span>
                                        </button>

                                        @if($allGroups->where('id', '!=', $selectedGroup->id)->count() > 0)
                                            <button type="button"
                                                    @click="open = false; openMove(@js([
                                                        'id'   => $member->id,
                                                        'name' => $u->name,
                                                    ]))"
                                                    class="w-full text-left px-4 py-2.5
                                                           hover:bg-gray-50 dark:hover:bg-gray-700
                                                           flex items-center gap-2
                                                           text-gray-700 dark:text-gray-200">
                                                <i class="fas fa-exchange-alt text-purple-500 w-4 shrink-0"></i>
                                                <span>Pindahkan</span>
                                            </button>
                                        @endif

                                        <div class="border-t border-gray-100 dark:border-gray-700"></div>

                                        @php
                                            $lastKetua = $position === 'ketua'
                                                && $members->where('position', 'ketua')->where('status', 'aktif')->count() <= 1;
                                        @endphp

                                        @if($lastKetua)
                                            <div class="px-4 py-2.5 text-xs text-gray-400
                                                        flex items-center gap-2">
                                                <i class="fas fa-lock w-4 shrink-0"></i>
                                                <span>Ketua terakhir tidak bisa dikeluarkan</span>
                                            </div>
                                        @else
                                            <button type="button"
                                                    @click="open = false; openRemove(@js([
                                                        'id'      => $member->id,
                                                        'user_id' => $member->user_id,
                                                        'name'    => $u->name,
                                                    ]))"
                                                    class="w-full text-left px-4 py-2.5
                                                           hover:bg-red-50 dark:hover:bg-red-900/20
                                                           flex items-center gap-2
                                                           text-red-600 dark:text-red-400">
                                                <i class="fas fa-user-minus w-4 shrink-0"></i>
                                                <span>Keluarkan dari Kelompok</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 md:p-16 text-center text-gray-500 dark:text-gray-400
                                    rounded-b-2xl">
                            <div class="w-16 h-16 mx-auto rounded-2xl
                                        bg-blue-50 dark:bg-blue-900/20
                                        flex items-center justify-center mb-4">
                                <i class="fas fa-user-plus text-3xl text-blue-400"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white mb-1">
                                Belum ada anggota
                            </h3>
                            <p class="text-sm max-w-md mx-auto">
                                Tambahkan anggota pertama untuk mulai mengelola kelompok ini.
                            </p>
                            <a href="{{ route('owner.kelompok.invite', ['group_id' => $selectedGroup->id]) }}"
                               class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl
                                      bg-emerald-600 hover:bg-emerald-700
                                      text-white text-sm font-semibold transition">
                                <i class="fas fa-user-plus"></i> Tambah Anggota Pertama
                            </a>
                        </div>
                    @endforelse

                    <div id="noSearchResult"
                         class="hidden p-10 text-center text-gray-500 dark:text-gray-400 rounded-b-2xl">
                        <i class="fas fa-search text-3xl mb-3 block opacity-30"></i>
                        <p class="text-sm">Tidak ada anggota yang cocok dengan pencarian.</p>
                    </div>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- MODAL — UBAH POSISI
            {{-- ========================================================= --}}
            <div x-show="modal.role"
                 x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4
                        bg-black/50 backdrop-blur-sm">
                <div @click.away="modal.role = null"
                     x-show="modal.role"
                     x-transition
                     class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                            max-w-md w-full p-6">

                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-user-tag text-blue-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 dark:text-white">Ubah Posisi</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate"
                               x-text="modal.role ? modal.role.name : ''"></p>
                        </div>
                    </div>

                    <form :action="'{{ url('owner/kelompok') }}/' + '{{ $selectedGroup->id }}' + '/update-member/' + (modal.role?.id || '')"
                          method="POST">
                        @csrf @method('PUT')

                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Pilih posisi baru
                        </label>
                        <div class="space-y-2">
                            <template x-for="opt in [
                                {v:'anggota',  l:'Anggota',  i:'fa-user',     c:'text-gray-500'},
                                {v:'pengurus', l:'Pengurus', i:'fa-user-tie', c:'text-blue-500'},
                                {v:'ketua',    l:'Ketua',    i:'fa-crown',    c:'text-amber-500'},
                            ]" :key="opt.v">
                                <label class="flex items-center gap-3 p-3 rounded-xl
                                              border border-gray-200 dark:border-gray-700
                                              hover:bg-gray-50 dark:hover:bg-gray-700/50
                                              cursor-pointer transition">
                                    <input type="radio" name="position" :value="opt.v"
                                           :checked="modal.role && modal.role.position === opt.v"
                                           class="text-blue-600 focus:ring-blue-500">
                                    <i class="fas shrink-0" :class="opt.i + ' ' + opt.c"></i>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white"
                                          x-text="opt.l"></span>
                                </label>
                            </template>
                        </div>

                        <div class="flex gap-2 mt-6">
                            <button type="button" @click="modal.role = null"
                                    class="flex-1 px-4 py-2.5 rounded-xl
                                           bg-gray-100 dark:bg-gray-700
                                           hover:bg-gray-200 dark:hover:bg-gray-600
                                           text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 rounded-xl
                                           bg-blue-600 hover:bg-blue-700
                                           text-white text-sm font-bold shadow-sm transition">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- MODAL — PINDAHKAN
            {{-- ========================================================= --}}
            <div x-show="modal.move"
                 x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4
                        bg-black/50 backdrop-blur-sm">
                <div @click.away="modal.move = null"
                     x-show="modal.move"
                     x-transition
                     class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                            max-w-md w-full p-6">

                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="fas fa-exchange-alt text-purple-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 dark:text-white">Pindahkan Anggota</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate"
                               x-text="modal.move ? modal.move.name : ''"></p>
                        </div>
                    </div>

                    <form :action="'{{ url('owner/kelompok') }}/' + '{{ $selectedGroup->id }}' + '/move-member/' + (modal.move?.id || '')"
                          method="POST">
                        @csrf

                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Pilih kelompok tujuan
                        </label>
                        <select name="target_group_id" required
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white text-sm
                                       px-4 py-3 focus:ring-2 focus:ring-purple-500
                                       focus:border-purple-500">
                            @foreach($allGroups->where('id', '!=', $selectedGroup->id) as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>

                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Anggota akan dipindahkan dari kelompok ini ke kelompok tujuan.
                        </p>

                        <div class="flex gap-2 mt-6">
                            <button type="button" @click="modal.move = null"
                                    class="flex-1 px-4 py-2.5 rounded-xl
                                           bg-gray-100 dark:bg-gray-700
                                           hover:bg-gray-200 dark:hover:bg-gray-600
                                           text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 rounded-xl
                                           bg-purple-600 hover:bg-purple-700
                                           text-white text-sm font-bold shadow-sm transition">
                                <i class="fas fa-exchange-alt mr-1"></i> Pindahkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- MODAL — KELUARKAN
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
                            <strong x-text="modal.remove ? modal.remove.name : ''"></strong>
                            dari kelompok <strong>{{ $selectedGroup->name }}</strong>.
                        </p>
                    </div>

                    <form :action="'{{ url('owner/kelompok') }}/' + '{{ $selectedGroup->id }}' + '/remove-member/' + (modal.remove?.user_id || modal.remove?.id || '')"
                          method="POST">
                        @csrf @method('DELETE')

                        <div class="flex gap-2">
                            <button type="button" @click="modal.remove = null"
                                    class="flex-1 px-4 py-2.5 rounded-xl
                                           bg-gray-100 dark:bg-gray-700
                                           hover:bg-gray-200 dark:hover:bg-gray-600
                                           text-gray-700 dark:text-gray-200 text-sm font-semibold transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 rounded-xl
                                           bg-red-600 hover:bg-red-700
                                           text-white text-sm font-bold shadow-sm transition">
                                <i class="fas fa-user-minus mr-1"></i> Keluarkan
                            </button>
                        </div>
                    </form>
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

<div id="toastStack" class="toast-stack"></div>

@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function memberPage() {
    return {
        modal: { role: null, move: null, remove: null },
        openRole(data)   { this.modal.role   = data; },
        openMove(data)   { this.modal.move   = data; },
        openRemove(data) { this.modal.remove = data; },
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const filterChips = document.querySelectorAll('.filter-chip');
    const rows        = document.querySelectorAll('.user-row');
    const noResult    = document.getElementById('noSearchResult');

    if (!searchInput) return;

    let activeFilter = 'all';

    function applyFilters() {
        const q = (searchInput.value || '').toLowerCase().trim();
        let visible = 0;

        rows.forEach(row => {
            const name     = row.dataset.name     || '';
            const position = row.dataset.position || '';
            const matchQ   = !q || name.includes(q);
            const matchF   = activeFilter === 'all' || position === activeFilter;
            const show     = matchQ && matchF;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (noResult) noResult.classList.toggle('hidden', visible > 0);
    }

    let timer;
    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(applyFilters, 200);
        clearSearch.classList.toggle('hidden', !this.value);
    });

    clearSearch?.addEventListener('click', function () {
        searchInput.value = '';
        this.classList.add('hidden');
        applyFilters();
        searchInput.focus();
    });

    filterChips.forEach(chip => {
        chip.addEventListener('click', function () {
            filterChips.forEach(c => {
                c.classList.remove('active', 'bg-blue-600', 'text-white');
                c.classList.add('bg-gray-100', 'dark:bg-gray-700',
                                'text-gray-600', 'dark:text-gray-300');
            });
            this.classList.add('active', 'bg-blue-600', 'text-white');
            this.classList.remove('bg-gray-100', 'dark:bg-gray-700',
                                  'text-gray-600', 'dark:text-gray-300');

            activeFilter = this.dataset.filter || 'all';
            applyFilters();
        });
    });
});
</script>
@endpush
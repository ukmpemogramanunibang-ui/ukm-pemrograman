@extends('layouts.roles.owner')

@section('title', 'Tambah Anggota Kelompok')

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
@media (min-width: 1024px) { .member-list { display: block; } }

.member-row { transition: background-color .15s ease; }
.member-row:hover { background-color: rgba(0,0,0,.015); }
.dark .member-row:hover { background-color: rgba(255,255,255,.02); }

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.capacity-bar-fill { transition: width .6s cubic-bezier(.4,0,.2,1); }

.btn-loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}
.btn-loading::after {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    width: 14px; height: 14px;
    margin: -7px 0 0 -7px;
    border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: btnSpin .6s linear infinite;
}
@keyframes btnSpin { to { transform: rotate(360deg); } }

.bulk-toolbar {
    transform: translateY(120%);
    transition: transform .25s cubic-bezier(.4,0,.2,1);
}
.bulk-toolbar.active { transform: translateY(0); }

/* Toast */
.toast-stack {
    position: fixed;
    top: 1rem;
    right: 1rem;
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
    animation: toastIn .25s ease;
    background: #fff;
    border-left: 4px solid #10b981;
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

<div class="p-4 md:p-6 max-w-[1400px] mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}"
           class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700
                  hover:bg-gray-200 dark:hover:bg-gray-600
                  flex items-center justify-center text-gray-600 dark:text-gray-300
                  transition shrink-0" aria-label="Kembali">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div class="min-w-0">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white
                       flex items-center gap-2">
                <i class="fas fa-user-plus text-emerald-500"></i>
                Tambah Anggota Kelompok
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Pilih pengguna yang akan <strong>langsung menjadi anggota</strong> kelompok ini.
            </p>
        </div>
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
    @if($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500
                    text-red-700 dark:text-red-300 p-4 rounded-xl">
            <div class="font-bold mb-1 text-sm">Periksa kembali:</div>
            <ul class="list-disc ml-5 text-xs space-y-0.5">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @if($groups->isEmpty())
        {{-- empty state kelompok --}}
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
                Buat kelompok terlebih dahulu sebelum menambahkan anggota.
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
        <form method="GET" action="{{ route('owner.kelompok.invite') }}"
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
                               text-sm px-4 py-2.5 focus:ring-2 focus:ring-emerald-500
                               focus:border-emerald-500">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}"
                                {{ (int) $selectedGroupId === $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                            @if(! empty($g->members_count))
                                ({{ $g->members_count }}{{ $g->max_members ? '/'.$g->max_members : '' }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700
                               text-white text-sm font-semibold
                               transition flex items-center justify-center gap-2 shrink-0 shadow-sm">
                    <i class="fas fa-arrow-right"></i> Tampilkan
                </button>
            </div>
        </form>

        @if($selectedGroup)
            @php
                $health   = $selectedGroup->health_status ?? ['label' => 'Normal', 'dot' => 'bg-emerald-500'];
                $pct      = $selectedGroup->capacity_percentage ?? null;
                $hasLimit = $maxMembers && $maxMembers > 0;
            @endphp

            {{-- GROUP HEADER --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700 p-5 mb-5">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-xl shrink-0
                                    bg-gradient-to-br from-emerald-500 to-teal-600
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
                                @if($hasLimit) / {{ $maxMembers }} @endif
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

            {{-- SMART HINTS --}}
            @if(! $hasKetua)
                <div class="mb-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20
                            border border-amber-200 dark:border-amber-700
                            text-amber-800 dark:text-amber-200 text-sm
                            flex items-start gap-3">
                    <i class="fas fa-crown mt-0.5 text-amber-500"></i>
                    <div>
                        <strong>Kelompok ini belum memiliki Ketua.</strong>
                        Anggota pertama yang ditambahkan akan otomatis menjadi Ketua.
                    </div>
                </div>
            @endif

            @if($isFull)
                <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20
                            border border-red-200 dark:border-red-700
                            text-red-800 dark:text-red-200 text-sm
                            flex items-start gap-3">
                    <i class="fas fa-ban mt-0.5 text-red-500"></i>
                    <div>
                        <strong>Kapasitas kelompok sudah penuh.</strong>
                        Tingkatkan kapasitas maksimal terlebih dahulu, atau keluarkan anggota yang tidak aktif.
                    </div>
                </div>
            @endif

            {{-- TOOLBAR --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        p-4 md:p-5 mb-5">
                <div class="flex flex-col md:flex-row gap-3 mb-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2
                                  text-gray-400 text-sm"></i>
                        <input type="text" id="searchInput"
                               placeholder="Cari nama, NIM, atau email..."
                               autocomplete="off" aria-label="Cari user"
                               class="w-full pl-11 pr-10 py-2.5 rounded-xl
                                      border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white text-sm
                                      focus:ring-2 focus:ring-emerald-500
                                      focus:border-emerald-500">
                        <button type="button" id="clearSearch"
                                class="hidden absolute right-3 top-1/2 -translate-y-1/2
                                       text-gray-400 hover:text-gray-600
                                       w-6 h-6 rounded-full flex items-center justify-center"
                                aria-label="Hapus pencarian">
                            <i class="fas fa-times-circle text-sm"></i>
                        </button>
                    </div>

                    <div class="shrink-0">
                        <label for="positionSelect" class="sr-only">Posisi anggota</label>
                        <select id="positionSelect"
                                class="w-full md:w-auto rounded-xl border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                                       text-sm px-3 py-2.5 focus:ring-2 focus:ring-emerald-500
                                       focus:border-emerald-500">
                            <option value="anggota">Posisi: Anggota</option>
                            <option value="pengurus">Posisi: Pengurus</option>
                            <option value="ketua">Posisi: Ketua</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">Tampilkan:</span>
                    <button type="button" data-filter="all"
                            class="filter-chip active px-3 py-1.5 rounded-lg text-xs font-semibold
                                   transition bg-emerald-600 text-white">Semua</button>
                    <button type="button" data-filter="available"
                            class="filter-chip px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                   bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                                   hover:bg-gray-200 dark:hover:bg-gray-600">Belum punya kelompok</button>
                    <button type="button" data-filter="member"
                            class="filter-chip px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                   bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                                   hover:bg-gray-200 dark:hover:bg-gray-600">Sudah di kelompok ini</button>
                    <button type="button" data-filter="elsewhere"
                            class="filter-chip px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                   bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                                   hover:bg-gray-200 dark:hover:bg-gray-600">Di kelompok lain</button>
                </div>
            </div>

            {{-- USER LIST --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700
                        overflow-hidden mb-28">
                {{-- Desktop header --}}
                <div class="hidden lg:grid grid-cols-12 gap-3 px-5 py-3
                            bg-gray-50 dark:bg-gray-900/40
                            border-b border-gray-200 dark:border-gray-700
                            text-[11px] font-bold uppercase tracking-wider
                            text-gray-500 dark:text-gray-400">
                    <div class="col-span-1 flex items-center">
                        <input type="checkbox" id="selectAll"
                               class="rounded text-emerald-600 focus:ring-emerald-500"
                               aria-label="Pilih semua">
                    </div>
                    <div class="col-span-4">Nama / NIM</div>
                    <div class="col-span-2">Level</div>
                    <div class="col-span-1 text-right">Poin</div>
                    <div class="col-span-2">Status</div>
                    <div class="col-span-2 text-center">Aksi</div>
                </div>

                <div id="memberList" class="member-list">
                    @forelse($users as $user)
                        @php
                            $isMember      = $user->is_member ?? false;
                            $otherGroups   = $user->other_groups ?? collect();
                            $hasOtherGroup = $otherGroups->isNotEmpty();
                            $searchText    = strtolower($user->name.' '.($user->nim ?? '').' '.($user->email ?? ''));
                            $filterState   = $isMember ? 'member' : ($hasOtherGroup ? 'elsewhere' : 'available');
                            $disabled      = $isMember || $isFull;
                        @endphp

                        <div class="member-row user-row
                                    lg:grid lg:grid-cols-12 lg:gap-3
                                    px-4 md:px-5 py-4
                                    border-b border-gray-100 dark:border-gray-700
                                    last:border-b-0"
                             data-name="{{ $searchText }}"
                             data-state="{{ $filterState }}"
                             data-user-id="{{ $user->id }}"
                             data-user-name="{{ $user->name }}">

                            {{-- Checkbox desktop --}}
                            <div class="hidden lg:flex lg:col-span-1 items-center">
                                @unless($disabled)
                                    <input type="checkbox"
                                           class="user-checkbox rounded text-emerald-600
                                                  focus:ring-emerald-500"
                                           value="{{ $user->id }}"
                                           aria-label="Pilih {{ $user->name }}">
                                @endunless
                            </div>

                            {{-- Identity --}}
                            <div class="lg:col-span-4 flex items-start gap-3">
                                @unless($disabled)
                                    <input type="checkbox"
                                           class="user-checkbox lg:hidden mt-1.5
                                                  rounded text-emerald-600 focus:ring-emerald-500"
                                           value="{{ $user->id }}"
                                           aria-label="Pilih {{ $user->name }}">
                                @endunless

                                <div class="w-10 h-10 rounded-full shrink-0
                                            bg-gradient-to-br from-emerald-400 to-teal-500
                                            flex items-center justify-center
                                            text-white font-bold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white line-clamp-1">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 space-y-0.5">
                                        @if($user->nim) <div class="font-mono">NIM {{ $user->nim }}</div> @endif
                                        @if($user->email) <div class="truncate">{{ $user->email }}</div> @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Level --}}
                            <div class="lg:col-span-2 mt-3 lg:mt-0 lg:flex lg:items-center">
                                <div class="flex items-center gap-2 lg:block">
                                    <span class="lg:hidden text-[11px] text-gray-400 w-16">Level:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                                 text-[11px] font-semibold
                                                 bg-indigo-50 dark:bg-indigo-900/30
                                                 text-indigo-700 dark:text-indigo-300">
                                        {{ $user->level->name ?? 'Level '.$user->level_id }}
                                    </span>
                                </div>
                            </div>

                            {{-- Poin --}}
                            <div class="lg:col-span-1 lg:text-right mt-2 lg:mt-0
                                        lg:flex lg:items-center lg:justify-end">
                                <div class="flex items-center gap-2 lg:block">
                                    <span class="lg:hidden text-[11px] text-gray-400 w-16">Poin:</span>
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">
                                        {{ number_format($user->total_points ?? 0) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="lg:col-span-2 mt-2 lg:mt-0 lg:flex lg:items-center">
                                <div class="flex items-center gap-2 lg:block">
                                    <span class="lg:hidden text-[11px] text-gray-400 w-16">Status:</span>
                                    @if($isMember)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                     text-[11px] font-semibold
                                                     bg-emerald-100 dark:bg-emerald-900/30
                                                     text-emerald-700 dark:text-emerald-300">
                                            <i class="fas fa-check-circle text-[9px]"></i>
                                            {{ ucfirst($user->member_position ?? 'Anggota') }}
                                        </span>
                                    @elseif($hasOtherGroup)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                     text-[11px] font-semibold
                                                     bg-amber-100 dark:bg-amber-900/30
                                                     text-amber-700 dark:text-amber-300"
                                              title="{{ $otherGroups->pluck('name')->implode(', ') }}">
                                            <i class="fas fa-users text-[9px]"></i>
                                            {{ $otherGroups->first()->name }}
                                            @if($otherGroups->count() > 1) +{{ $otherGroups->count() - 1 }} @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                     text-[11px] font-semibold
                                                     bg-gray-100 dark:bg-gray-700
                                                     text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-user text-[9px]"></i> Belum ada kelompok
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="lg:col-span-2 mt-3 lg:mt-0 lg:flex lg:items-center lg:justify-center">
                                @if($isMember)
                                    <form action="{{ route('owner.kelompok.remove-member', [$selectedGroup->id, $user->id]) }}"
                                          method="POST" class="remove-form w-full lg:w-auto">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                data-confirm="Keluarkan {{ $user->name }} dari {{ $selectedGroup->name }}?"
                                                class="w-full lg:w-auto px-3 py-2 rounded-lg text-xs font-semibold
                                                       bg-red-50 dark:bg-red-900/20
                                                       text-red-600 dark:text-red-300
                                                       hover:bg-red-100 dark:hover:bg-red-900/40
                                                       transition flex items-center justify-center gap-1.5">
                                            <i class="fas fa-user-minus"></i> Keluarkan
                                        </button>
                                    </form>
                                @elseif($isFull)
                                    <button type="button" disabled
                                            class="w-full lg:w-auto px-3 py-2 rounded-lg text-xs font-semibold
                                                   bg-gray-100 dark:bg-gray-700
                                                   text-gray-400 cursor-not-allowed
                                                   flex items-center justify-center gap-1.5"
                                            title="Kapasitas penuh">
                                        <i class="fas fa-ban"></i> Penuh
                                    </button>
                                @else
                                    <form action="{{ route('owner.kelompok.add-member', $selectedGroup->id) }}"
                                          method="POST" class="add-form w-full lg:w-auto">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <input type="hidden" name="position"
                                               class="position-hidden" value="anggota">
                                        <button type="submit"
                                                class="w-full lg:w-auto px-3 py-2 rounded-lg text-xs font-semibold
                                                       text-white bg-emerald-600 hover:bg-emerald-700
                                                       transition flex items-center justify-center gap-1.5
                                                       shadow-sm">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user-slash text-4xl mb-3 block opacity-30"></i>
                            <p class="text-sm">Tidak ada pengguna yang dapat ditambahkan.</p>
                        </div>
                    @endforelse

                    <div id="noSearchResult"
                         class="hidden p-10 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                        <p class="text-sm">Tidak ada pengguna yang cocok dengan pencarian.</p>
                    </div>
                </div>
            </div>

            {{-- BULK TOOLBAR --}}
            @unless($isFull)
                <div id="bulkToolbar"
                     class="bulk-toolbar fixed bottom-0 left-0 right-0 z-50
                            bg-white dark:bg-gray-800
                            border-t-2 border-emerald-500 shadow-2xl">
                    <div class="max-w-[1400px] mx-auto px-4 md:px-6 py-3 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Dipilih</div>
                            <div class="font-bold text-gray-800 dark:text-white text-sm">
                                <span id="selectedCount">0</span> pengguna
                            </div>
                        </div>
                        <button type="button" id="clearSelection"
                                class="px-4 py-2 rounded-lg text-xs font-semibold
                                       bg-gray-100 dark:bg-gray-700
                                       text-gray-600 dark:text-gray-300
                                       hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Batal
                        </button>
                        <form id="bulkForm"
                              action="{{ route('owner.kelompok.add-members-bulk', $selectedGroup->id) }}"
                              method="POST" class="shrink-0">
                            @csrf
                            <input type="hidden" name="position" id="bulkPosition" value="anggota">
                            <div id="bulkUserIds"></div>
                            <button type="submit"
                                    class="px-5 py-2 rounded-lg text-xs font-bold text-white
                                           bg-emerald-600 hover:bg-emerald-700
                                           shadow-sm transition flex items-center gap-1.5">
                                <i class="fas fa-user-plus"></i>
                                Tambah <span id="bulkCount">0</span> Anggota
                            </button>
                        </form>
                    </div>
                </div>
            @endunless

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

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* =========================================================
     | TOAST
     * ========================================================= */
    const toastStack = document.getElementById('toastStack');
    function showToast(message, type = 'success') {
        const el = document.createElement('div');
        el.className = 'toast' + (type === 'error' ? ' error' : '');
        el.innerHTML = `
            <i class="fas ${type === 'error' ? 'fa-exclamation-circle text-red-500' : 'fa-check-circle text-emerald-500'} mt-0.5"></i>
            <div class="flex-1">${message}</div>
        `;
        toastStack.appendChild(el);
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            el.style.transition = 'all .25s ease';
            setTimeout(() => el.remove(), 300);
        }, 4000);
    }

    /* =========================================================
     | SEARCH + FILTER
     * ========================================================= */
    const searchInput    = document.getElementById('searchInput');
    const clearSearch    = document.getElementById('clearSearch');
    const filterChips    = document.querySelectorAll('.filter-chip');
    const userRows       = document.querySelectorAll('.user-row');
    const noSearchResult = document.getElementById('noSearchResult');

    if (!searchInput) return;

    let activeFilter = 'all';

    function applyFilters() {
        const q = (searchInput.value || '').toLowerCase().trim();
        let visible = 0;
        userRows.forEach(row => {
            const name  = row.dataset.name  || '';
            const state = row.dataset.state || '';
            const matchQ = !q || name.includes(q);
            const matchF = activeFilter === 'all' || state === activeFilter;
            const show   = matchQ && matchF;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (noSearchResult) noSearchResult.classList.toggle('hidden', visible > 0);
        clearSelection();
    }

    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilters, 200);
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
                c.classList.remove('active', 'bg-emerald-600', 'text-white');
                c.classList.add('bg-gray-100', 'dark:bg-gray-700',
                                'text-gray-600', 'dark:text-gray-300');
            });
            this.classList.add('active', 'bg-emerald-600', 'text-white');
            this.classList.remove('bg-gray-100', 'dark:bg-gray-700',
                                  'text-gray-600', 'dark:text-gray-300');
            activeFilter = this.dataset.filter || 'all';
            applyFilters();
        });
    });

    /* =========================================================
     | POSITION SYNC
     * ========================================================= */
    const positionSelect = document.getElementById('positionSelect');
    positionSelect?.addEventListener('change', function () {
        const val = this.value;
        document.querySelectorAll('.position-hidden').forEach(el => el.value = val);
        const bulkPos = document.getElementById('bulkPosition');
        if (bulkPos) bulkPos.value = val;
    });

    /* =========================================================
     | BULK SELECTION
     * ========================================================= */
    const selectAll     = document.getElementById('selectAll');
    const bulkToolbar   = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkCount     = document.getElementById('bulkCount');
    const bulkUserIds   = document.getElementById('bulkUserIds');
    const clearBtn      = document.getElementById('clearSelection');

    function getVisibleCheckboxes() {
        return Array.from(document.querySelectorAll('.user-checkbox'))
            .filter(cb => cb.closest('.user-row')?.style.display !== 'none');
    }

    function updateBulkState() {
        const checked = getVisibleCheckboxes().filter(cb => cb.checked);
        if (selectedCount) selectedCount.textContent = checked.length;
        if (bulkCount)     bulkCount.textContent     = checked.length;
        if (bulkToolbar)   bulkToolbar.classList.toggle('active', checked.length > 0);
        if (bulkUserIds) {
            bulkUserIds.innerHTML = checked
                .map(cb => `<input type="hidden" name="user_ids[]" value="${cb.value}">`)
                .join('');
        }
        if (selectAll) {
            const all = getVisibleCheckboxes();
            selectAll.checked       = all.length > 0 && checked.length === all.length;
            selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
        }
    }

    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    selectAll?.addEventListener('change', function () {
        const checked = this.checked;
        getVisibleCheckboxes().forEach(cb => cb.checked = checked);
        updateBulkState();
    });

    function clearSelection() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
        if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
        updateBulkState();
    }
    clearBtn?.addEventListener('click', clearSelection);

    /* =========================================================
     | REMOVE — confirm + loading
     * ========================================================= */
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const btn = form.querySelector('button[type="submit"]');
            const msg = btn?.dataset.confirm || 'Yakin ingin mengeluarkan pengguna ini?';
            if (! confirm(msg)) { e.preventDefault(); return; }
            btn?.classList.add('btn-loading');
        });
    });

    /* =========================================================
     | ADD SINGLE — loading state
     * ========================================================= */
    document.querySelectorAll('.add-form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            btn?.classList.add('btn-loading');
        });
    });

    /* =========================================================
     | BULK SUBMIT — konfirmasi + loading
     * ========================================================= */
    document.getElementById('bulkForm')?.addEventListener('submit', function (e) {
        const btn = this.querySelector('button[type="submit"]');
        const selected = getVisibleCheckboxes().filter(cb => cb.checked).length;
        if (selected === 0) { e.preventDefault(); return; }

        const names = getVisibleCheckboxes()
            .filter(cb => cb.checked)
            .map(cb => cb.closest('.user-row')?.dataset.userName || '')
            .filter(Boolean)
            .slice(0, 5);

        const preview = names.join(', ') + (selected > 5 ? `, +${selected - 5} lainnya` : '');
        if (! confirm(`Tambahkan ${selected} pengguna ke kelompok?\n\n${preview}`)) {
            e.preventDefault();
            return;
        }
        btn?.classList.add('btn-loading');
    });

})();
</script>
@endpush
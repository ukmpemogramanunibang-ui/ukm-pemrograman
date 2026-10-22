@extends('layouts.ketua')

@section('title', 'Manajemen Anggota')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto" id="anggota-app">

    {{-- ══════════════ HERO ══════════════ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 p-6 sm:p-8 shadow-2xl shadow-purple-500/20">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-white/15 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-pink-400/25 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/20 backdrop-blur-xl border border-white/30 flex items-center justify-center shadow-2xl">
                        <i class="fas fa-users text-white text-2xl sm:text-3xl"></i>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-400 border-2 border-white dark:border-gray-900 animate-pulse"></span>
                </div>
                <div>
                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur text-white text-[10px] font-bold uppercase tracking-widest border border-white/20 inline-flex items-center">
                        <i class="fas fa-crown text-[8px] mr-1"></i> Manajemen
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mt-2">Anggota UKM</h1>
                    <p class="text-sm text-white/80 mt-1">Kelola seluruh data anggota terdaftar</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="px-5 py-4 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/20 hover:bg-white/25 transition">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-database text-white/70 text-xs"></i>
                        <span class="text-white/70 text-[10px] font-bold uppercase tracking-widest">Total</span>
                    </div>
                    <span class="text-white text-2xl font-black tabular-nums">{{ number_format($stats['total']) }}</span>
                </div>
                <div class="px-5 py-4 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/20 hover:bg-white/25 transition">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-signal text-white/70 text-xs"></i>
                        <span class="text-white/70 text-[10px] font-bold uppercase tracking-widest">Aktif</span>
                    </div>
                    <span class="text-white text-2xl font-black tabular-nums">{{ number_format($stats['active']) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ ACTION BAR ══════════════ --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-3 sm:p-4">
        <div class="flex flex-wrap items-center gap-2">

            {{-- Tambah Anggota --}}
            <a href="{{ route('owner.management.members.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-user-plus"></i>
                <span class="hidden sm:inline">Tambah Anggota</span>
                <span class="sm:hidden">Tambah</span>
            </a>

            {{-- Pilih Mode Toggle --}}
            <button type="button" id="btn-toggle-select"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-check-square"></i>
                <span id="btn-toggle-label">Pilih</span>
            </button>

            {{-- Export Terpilih --}}
            <button type="button" id="btn-export" disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-sm font-bold cursor-not-allowed transition-all"
                    data-enabled-class="bg-gradient-to-r from-blue-500 to-cyan-600 text-white hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5">
                <i class="fas fa-file-export"></i>
                <span class="hidden sm:inline">Export Terpilih</span>
                <span class="sm:hidden">Export</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-black/10 text-[10px] font-black tabular-nums" data-count>0</span>
            </button>

            {{-- Manage Role --}}
            <button type="button" id="btn-manage-role" disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-sm font-bold cursor-not-allowed transition-all"
                    data-enabled-class="bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:shadow-lg hover:shadow-amber-500/30 hover:-translate-y-0.5">
                <i class="fas fa-user-shield"></i>
                <span class="hidden sm:inline">Manage Role</span>
                <span class="sm:hidden">Role</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-black/10 text-[10px] font-black tabular-nums" data-count>0</span>
            </button>

            {{-- Delete Terpilih --}}
            <button type="button" id="btn-delete" disabled
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-sm font-bold cursor-not-allowed transition-all"
                    data-enabled-class="bg-gradient-to-r from-red-500 to-rose-600 text-white hover:shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5">
                <i class="fas fa-trash-alt"></i>
                <span class="hidden sm:inline">Hapus Terpilih</span>
                <span class="sm:hidden">Hapus</span>
                <span class="ml-1 px-1.5 py-0.5 rounded-md bg-black/10 text-[10px] font-black tabular-nums" data-count>0</span>
            </button>

            {{-- Selection Info --}}
            <div id="selection-info" class="hidden ml-auto flex items-center gap-2 px-3 py-2 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800">
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

    {{-- ══════════════ STATS ══════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $statsCards = [
                ['icon' => 'fa-users',      'label' => 'Total Anggota', 'value' => $stats['total'],    'from' => 'from-blue-500',    'to' => 'to-indigo-600'],
                ['icon' => 'fa-user-check', 'label' => 'Aktif',         'value' => $stats['active'],   'from' => 'from-emerald-500', 'to' => 'to-teal-600'],
                ['icon' => 'fa-mars',       'label' => 'Laki-laki',     'value' => $stats['male'],     'from' => 'from-sky-500',     'to' => 'to-blue-600'],
                ['icon' => 'fa-venus',      'label' => 'Perempuan',     'value' => $stats['female'],   'from' => 'from-pink-500',    'to' => 'to-rose-600'],
            ];
        @endphp
        @foreach($statsCards as $s)
        <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 sm:p-5 hover:shadow-2xl hover:shadow-purple-500/10 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br {{ $s['from'] }} {{ $s['to'] }} opacity-0 group-hover:opacity-5 rounded-full blur-2xl transition-opacity"></div>
            <div class="relative flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $s['label'] }}</p>
                    <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1 tabular-nums">{{ number_format($s['value']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br {{ $s['from'] }} {{ $s['to'] }} flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform">
                    <i class="fas {{ $s['icon'] }} text-white text-base"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════ TOOLBAR ══════════════ --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-3 sm:p-4">
        <form method="GET" action="{{ route('ketua.manajemen-anggota.index') }}" class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm group-focus-within:text-purple-500 transition"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama, NIM, atau email..."
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition placeholder-gray-400">
            </div>

            <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-purple-500 outline-none transition font-medium text-gray-700 dark:text-gray-300">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status')==='active'   ? 'selected' : '' }}>✓ Aktif</option>
                <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>✗ Nonaktif</option>
            </select>

            <select name="gender" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-purple-500 outline-none transition font-medium text-gray-700 dark:text-gray-300">
                <option value="">Semua Gender</option>
                <option value="L" {{ request('gender')==='L' ? 'selected' : '' }}>♂ Laki-laki</option>
                <option value="P" {{ request('gender')==='P' ? 'selected' : '' }}>♀ Perempuan</option>
            </select>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['q','status','gender']))
                <a href="{{ route('ketua.manajemen-anggota.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- ══════════════ GRID ══════════════ --}}
    @if($members->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="member-grid">
            @foreach($members as $member)
            <div class="member-card group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-2xl hover:shadow-purple-500/10 hover:-translate-y-1.5 transition-all duration-300"
                 data-user-id="{{ $member->id }}">

                {{-- Accent bar --}}
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 via-pink-500 to-orange-400 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>

                {{-- CHECKBOX overlay (hidden by default) --}}
                <div class="checkbox-overlay hidden absolute top-3 left-3 z-20">
                    <label class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-purple-500 transition-all has-[:checked]:bg-purple-600 has-[:checked]:border-purple-600 shadow-lg">
                        <input type="checkbox" class="member-checkbox w-4 h-4 rounded appearance-none cursor-pointer" data-id="{{ $member->id }}">
                        <i class="fas fa-check text-white text-xs absolute pointer-events-none opacity-0 check-icon"></i>
                    </label>
                </div>

                <a href="{{ route('ketua.manajemen-anggota.show', $member->id) }}" class="block card-link">
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 relative">
                                @if($member->avatar_url)
                                    <img src="{{ asset('storage/'.$member->avatar_url) }}"
                                         class="w-16 h-16 rounded-2xl object-cover ring-2 ring-gray-100 dark:ring-gray-800 group-hover:ring-purple-500/40 transition-all">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 flex items-center justify-center text-white font-black text-xl shadow-lg group-hover:scale-105 group-hover:rotate-3 transition-transform">
                                        {{ strtoupper(substr($member->name ?? '?', 0, 1)) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full border-2 border-white dark:border-gray-900 {{ $member->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 dark:text-white truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    {{ $member->name ?? 'Tanpa Nama' }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">{{ $member->nim ?? '-' }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ $member->email ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 mt-4">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide
                                {{ $member->is_active
                                    ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-500 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $member->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>

                            @if($member->gender)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 ring-1 ring-purple-200 dark:ring-purple-800">
                                    <i class="fas fa-{{ $member->gender === 'L' ? 'mars' : 'venus' }} text-[8px]"></i>
                                    {{ $member->gender }}
                                </span>
                            @endif

                            @if($member->level)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 ring-1 ring-indigo-200 dark:ring-indigo-800">
                                    <i class="fas fa-shield-alt text-[8px]"></i>
                                    {{ $member->level->name }}
                                </span>
                            @endif
                        </div>

                        @if($member->university || $member->major)
                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <i class="fas fa-graduation-cap text-gray-400 text-xs"></i>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                    {{ $member->university->name ?? '-' }}
                                    @if($member->major) · {{ $member->major->name }} @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lihat Detail</span>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        {{ $members->withQueryString()->links('vendor.pagination.ketua') }}
    @else
        <div class="bg-white dark:bg-gray-900 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-800 p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/20 flex items-center justify-center mb-5">
                <i class="fas fa-users text-purple-500 text-3xl"></i>
            </div>
            <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-2">Belum ada anggota</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Data anggota akan muncul di sini.</p>
        </div>
    @endif
</div>

{{-- ══════════════ MODAL MANAGE ROLE ══════════════ --}}
<div id="modal-manage-role" class="hidden fixed inset-0 z-[1200] bg-black/60 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 z-10 bg-gradient-to-r from-amber-500 to-orange-600 p-5 rounded-t-3xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">Manage Role</h3>
                        <p class="text-xs text-white/80">Kelola pangkat & jabatan user terpilih</p>
                    </div>
                </div>
                <button type="button" onclick="closeManageRole()"
                        class="w-9 h-9 rounded-xl bg-white/20 hover:bg-white/30 text-white transition flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-4">

            {{-- Selected users --}}
            <div>
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                    User Terpilih (<span id="mr-selected-count">0</span>)
                </label>
                <div id="mr-selected-list" class="flex flex-wrap gap-2 max-h-24 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl"></div>
            </div>

            {{-- Action --}}
            <div>
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Aksi</label>
                <select id="mr-action" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-amber-500 outline-none transition font-medium">
                    <option value="set_level">🎯 Ubah Level Role</option>
                    <option value="promote">⬆️ Naikkan Pangkat</option>
                    <option value="demote">⬇️ Turunkan Pangkat</option>
                    <option value="add_position">➕ Tambah Jabatan</option>
                    <option value="remove_position">➖ Kurangi Jabatan</option>
                    <option value="transfer_ketua">👑 Transfer Jabatan Ketua (dari Anda)</option>
                </select>
            </div>

            {{-- Target Level --}}
            <div id="mr-target-level-group">
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Level Target</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($availableRoles as $role)
                        <label class="role-option cursor-pointer relative flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-amber-400 transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 dark:has-[:checked]:bg-amber-900/20 has-[:checked]:shadow-md">
                            <input type="radio" name="mr-target-level" value="{{ $role['id'] }}" class="sr-only" @if($role['id']==5) checked @endif>
                            <i class="fas fa-{{ $role['id']==3 ? 'crown' : ($role['id']==4 ? 'user-tie' : ($role['id']==5 ? 'users' : 'user')) }} text-amber-500"></i>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $role['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Jabatan --}}
            <div id="mr-jabatan-group" class="hidden">
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Pilih Jabatan</label>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    @foreach($jabatans as $j)
                        <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition">
                            <input type="checkbox" name="mr-jabatan" value="{{ $j->id }}" class="w-4 h-4 rounded accent-amber-500">
                            <span class="text-gray-700 dark:text-gray-300">{{ $j->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Ketua Transfer Warning (auto-show when target = ketua) --}}
            <div id="mr-ketua-warning" class="hidden rounded-2xl bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 p-4">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-black text-amber-900 dark:text-amber-300 text-sm">Peringatan: Role Ketua Hanya 1</h4>
                        <p class="text-xs text-amber-800 dark:text-amber-400 mt-1">
                            Sistem hanya mengizinkan 1 Ketua aktif. Jika sudah ada Ketua lain, gunakan
                            <strong>Transfer Jabatan Ketua</strong> untuk memindahkan role Anda, atau turunkan Ketua yang ada terlebih dahulu.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                    Komentar / Pesan <span class="text-gray-400 normal-case font-normal">(wajib untuk transfer ketua)</span>
                </label>
                <textarea id="mr-comment" rows="3" maxlength="1000"
                          placeholder="Tuliskan alasan atau pesan untuk user..."
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-amber-500 outline-none transition resize-none"></textarea>
                <p class="text-[10px] text-gray-400 mt-1"><span id="mr-comment-count">0</span>/1000</p>
            </div>

            {{-- Error --}}
            <div id="mr-error" class="hidden rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm text-red-700 dark:text-red-400"></div>
        </div>

        {{-- Footer --}}
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-800 p-4 flex gap-3 rounded-b-3xl">
            <button type="button" onclick="closeManageRole()"
                    class="flex-1 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                Batal
            </button>
            <button type="button" id="mr-submit"
                    class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm hover:shadow-lg hover:shadow-amber-500/30 transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Perubahan
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
            <div id="ketua-conflict-info" class="hidden mb-5 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-left">
                <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">Ketua Saat Ini</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white" id="existing-ketua-name">-</p>
                <p class="text-xs text-gray-500 dark:text-gray-400" id="existing-ketua-email">-</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeKetuaConflict()"
                        class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Batal
                </button>
                <button type="button" onclick="switchToTransferKetua()"
                        class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm hover:shadow-lg transition active:scale-95">
                    <i class="fas fa-exchange-alt mr-1"></i> Transfer Ketua
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ TOAST CONTAINER ══════════════ --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[1400] flex flex-col gap-2 pointer-events-none"></div>

@push('scripts')
<script>
(function() {
    'use strict';

    /* ═══════════════════════════════════════════════════════
       STATE
       ═══════════════════════════════════════════════════════ */
    const state = {
        selectionMode: false,
        selected: new Set(),
        csrfToken: '{{ csrf_token() }}',
        routes: {
            bulkAction: '{{ route("ketua.manajemen-anggota.bulk-action") }}',
            manageRole: '{{ route("ketua.manajemen-anggota.manage-role") }}',
            logout:     '{{ route("logout") }}',
        },
    };

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
        el.className = `pointer-events-auto bg-gradient-to-r ${colors[type]} text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] max-w-md translate-x-full transition-transform duration-300`;
        el.innerHTML = `
            <i class="fas ${icons[type]} text-lg"></i>
            <span class="text-sm font-semibold flex-1">${message}</span>
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
       SELECTION MODE
       ═══════════════════════════════════════════════════════ */
    const btnToggle = document.getElementById('btn-toggle-select');
    const btnLabel = document.getElementById('btn-toggle-label');
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const overlays = document.querySelectorAll('.checkbox-overlay');
    const cardLinks = document.querySelectorAll('.card-link');

    function setSelectionMode(on) {
        state.selectionMode = on;
        overlays.forEach(o => o.classList.toggle('hidden', !on));

        // Disable link click saat selection mode
        cardLinks.forEach(a => {
            if (on) a.style.pointerEvents = 'none';
            else a.style.pointerEvents = '';
        });

        if (on) {
            btnLabel.textContent = 'Batal Pilih';
            btnToggle.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-gray-600 to-gray-800 text-white text-sm font-bold hover:shadow-lg hover:-translate-y-0.5 transition-all active:scale-95';
        } else {
            btnLabel.textContent = 'Pilih';
            btnToggle.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-bold hover:shadow-lg hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all active:scale-95';

            // Clear all
            state.selected.clear();
            checkboxes.forEach(cb => { cb.checked = false; updateCheckboxUI(cb); });
            updateActionButtons();
        }
    }

    btnToggle.addEventListener('click', () => setSelectionMode(!state.selectionMode));

    /* ═══════════════════════════════════════════════════════
       CHECKBOX HANDLING
       ═══════════════════════════════════════════════════════ */
    function updateCheckboxUI(cb) {
        const card = cb.closest('.member-card');
        const icon = card.querySelector('.check-icon');
        if (cb.checked) {
            card.classList.add('ring-2', 'ring-purple-500', 'shadow-lg', 'shadow-purple-500/20');
            icon.style.opacity = '1';
        } else {
            card.classList.remove('ring-2', 'ring-purple-500', 'shadow-lg', 'shadow-purple-500/20');
            icon.style.opacity = '0';
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
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
    const btnExport = document.getElementById('btn-export');
    const btnManage = document.getElementById('btn-manage-role');
    const btnDelete = document.getElementById('btn-delete');
    const selectionInfo = document.getElementById('selection-info');
    const selectionCount = document.getElementById('selection-count');

    function updateActionButtons() {
        const count = state.selected.size;
        const enabled = count > 0;

        [btnExport, btnManage, btnDelete].forEach(btn => {
            const enabledClass = btn.dataset.enabledClass;
            const disabledClass = 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 cursor-not-allowed';

            if (enabled) {
                btn.disabled = false;
                btn.className = btn.className.replace(disabledClass, '');
                if (!btn.className.includes(enabledClass.split(' ')[0])) {
                    btn.className += ' ' + enabledClass;
                }
            } else {
                btn.disabled = true;
                btn.className = btn.className.replace(enabledClass, '');
                if (!btn.className.includes('bg-gray-100')) {
                    btn.className += ' ' + disabledClass;
                }
            }

            // Update count badge
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
    btnExport.addEventListener('click', async () => {
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
        if (!confirm(`Hapus ${state.selected.size} akun yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) return;

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
       MANAGE ROLE MODAL
       ═══════════════════════════════════════════════════════ */
    const modal = document.getElementById('modal-manage-role');
    const mrAction = document.getElementById('mr-action');
    const mrTargetLevelGroup = document.getElementById('mr-target-level-group');
    const mrJabatanGroup = document.getElementById('mr-jabatan-group');
    const mrKetuaWarning = document.getElementById('mr-ketua-warning');
    const mrComment = document.getElementById('mr-comment');
    const mrCommentCount = document.getElementById('mr-comment-count');
    const mrSubmit = document.getElementById('mr-submit');
    const mrError = document.getElementById('mr-error');

    window.closeManageRole = function() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.openManageRole = function() {
        const list = document.getElementById('mr-selected-list');
        const cards = document.querySelectorAll('.member-card');
        let html = '';

        cards.forEach(card => {
            const id = parseInt(card.dataset.userId);
            if (state.selected.has(id)) {
                const name = card.querySelector('h3').textContent.trim();
                html += `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold">
                    <i class="fas fa-user text-[9px]"></i> ${name}
                </span>`;
            }
        });

        list.innerHTML = html || '<p class="text-xs text-gray-400">Tidak ada yang dipilih</p>';
        document.getElementById('mr-selected-count').textContent = state.selected.size;

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

    function updateManageRoleFields() {
        const action = mrAction.value;
        const targetLevel = document.querySelector('input[name="mr-target-level"]:checked')?.value;
        const isKetuaTarget = targetLevel === '3';

        // Show/hide target level group
        mrTargetLevelGroup.classList.toggle('hidden', !['set_level', 'promote', 'demote'].includes(action));

        // Show/hide jabatan
        mrJabatanGroup.classList.toggle('hidden', !['add_position', 'remove_position'].includes(action));

        // Ketua warning
        const shouldWarn = isKetuaTarget && ['set_level', 'promote'].includes(action);
        mrKetuaWarning.classList.toggle('hidden', !shouldWarn);

        // Transfer ketua needs comment
        const needsComment = action === 'transfer_ketua';
        if (needsComment) {
            mrComment.required = true;
            mrComment.placeholder = 'Wajib diisi: alasan transfer jabatan Ketua...';
        } else {
            mrComment.required = false;
            mrComment.placeholder = 'Tuliskan alasan atau pesan untuk user...';
        }
    }

    mrAction.addEventListener('change', updateManageRoleFields);
    document.querySelectorAll('input[name="mr-target-level"]').forEach(r => {
        r.addEventListener('change', updateManageRoleFields);
    });

    mrComment.addEventListener('input', () => {
        mrCommentCount.textContent = mrComment.value.length;
    });

    /* ═══════════════════════════════════════════════════════
       SUBMIT MANAGE ROLE
       ═══════════════════════════════════════════════════════ */
    mrSubmit.addEventListener('click', async () => {
        const action = mrAction.value;
        const targetLevelId = document.querySelector('input[name="mr-target-level"]:checked')?.value;
        const jabatanIds = Array.from(document.querySelectorAll('input[name="mr-jabatan"]:checked')).map(cb => parseInt(cb.value));
        const comment = mrComment.value.trim();

        // Validation
        if (action === 'transfer_ketua' && !comment) {
            mrError.textContent = '⚠️ Komentar wajib diisi untuk transfer jabatan Ketua.';
            mrError.classList.remove('hidden');
            return;
        }

        if (['add_position', 'remove_position'].includes(action) && jabatanIds.length === 0) {
            mrError.textContent = '⚠️ Pilih minimal satu jabatan.';
            mrError.classList.remove('hidden');
            return;
        }

        mrSubmit.disabled = true;
        mrSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        mrError.classList.add('hidden');

        try {
            const res = await fetch(state.routes.manageRole, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    user_ids: Array.from(state.selected),
                    action: action,
                    target_level_id: targetLevelId,
                    jabatan_ids: jabatanIds,
                    comment: comment,
                }),
            });

            const data = await res.json();

            if (data.success) {
                toast(data.message, 'success');

                if (data.redirect_to_login) {
                    // Ketua transfer → logout otomatis
                    setTimeout(() => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = state.routes.logout;
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = '_token';
                        input.value = state.csrfToken;
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }, 1500);
                    return;
                }

                closeManageRole();
                setTimeout(() => location.reload(), 800);

            } else if (data.conflict_type === 'multiple_ketua' || data.conflict_type === 'already_ketua') {
                // Show conflict modal
                closeManageRole();
                showKetuaConflict(data);
            } else {
                mrError.textContent = '❌ ' + (data.message || 'Terjadi kesalahan.');
                mrError.classList.remove('hidden');
            }
        } catch (e) {
            mrError.textContent = '❌ Gagal menghubungi server.';
            mrError.classList.remove('hidden');
        } finally {
            mrSubmit.disabled = false;
            mrSubmit.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
        }
    });

    /* ═══════════════════════════════════════════════════════
       KETUA CONFLICT MODAL
       ═══════════════════════════════════════════════════════ */
    const conflictModal = document.getElementById('modal-ketua-conflict');

    window.closeKetuaConflict = function() {
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

    window.switchToTransferKetua = function() {
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
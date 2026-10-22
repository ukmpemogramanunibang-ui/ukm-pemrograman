@extends('layouts.roles.owner')

@section('title', 'Manajemen NFC')
@section('page-title', 'NFC Credential')

@php
    // =========================================================
    // Kumpulkan ID user yang BELUM punya NFC aktif (eligible)
    // =========================================================
    $eligibleIds = $users->getCollection()
        ->filter(function ($u) {
            $nfc      = $u->nfcCredential;
            $isActive = $nfc && $nfc->is_active && !$nfc->is_revoked
                        && ($nfc->status ?? 'active') === 'active'
                        && (!$nfc->expired_at || $nfc->expired_at->isFuture());
            return !$isActive;
        })
        ->pluck('id')
        ->values()
        ->all();
@endphp

@section('content')
<div class="space-y-6" x-data="nfcIndex(@js($eligibleIds))" x-cloak>

    {{-- ============================================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================================ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-600 via-blue-600 to-indigo-700 text-white shadow-xl">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>

        <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="relative w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-lg flex-shrink-0">
                    <i class="fas fa-id-card text-2xl"></i>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-400 border-2 border-white dark:border-gray-900 animate-pulse"></span>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">NFC Credential Center</h1>
                    <p class="text-white/75 text-sm mt-1 max-w-lg">
                        Kelola kartu NFC anggota — satu kartu hanya boleh terhubung ke satu akun pada satu waktu.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 border border-white/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Sistem Normal
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 border border-white/20">
                            <i class="fas fa-users text-[10px]"></i>
                            {{ number_format($stats['total_users']) }} user
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/25 border border-emerald-400/30">
                            <i class="fas fa-check-circle text-[10px]"></i>
                            {{ number_format($stats['with_nfc']) }} aktif
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 sm:flex-col sm:items-end">
                <a href="{{ route('owner.management.nfc.generate') }}"
                   class="group inline-flex items-center gap-2 px-5 py-3 bg-white text-cyan-700 rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-95 transition-all">
                    <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform duration-300"></i>
                    <span>Generate NFC</span>
                </a>
                <a href="{{ route('owner.management.nfc.history') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl text-sm hover:bg-white/25 transition">
                    <i class="fas fa-history"></i>
                    <span>Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ============================================================ --}}
   @if(session('success'))
    <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 overflow-hidden animate-fadeIn">
        <div class="flex items-start gap-3 p-4">
            <i class="fas fa-check-circle text-lg mt-0.5 text-emerald-600 dark:text-emerald-400"></i>
            <div class="flex-1">
                <div class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                    {{ session('success') }}
                </div>

                {{-- Tampilkan list user yang baru saja di-generate --}}
                @if(session('generated_users'))
                    <div class="mt-3 pt-3 border-t border-emerald-200 dark:border-emerald-700/50">
                        <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">
                            <i class="fas fa-user-check mr-1"></i>
                            Akun yang baru terdaftar
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(session('generated_users') as $name)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-700/50">
                                    <i class="fas fa-check text-[10px]"></i>
                                    {{ $name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                    class="text-emerald-400 hover:text-emerald-600 transition flex-shrink-0">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50 text-red-800 dark:text-red-300 animate-fadeIn">
            <i class="fas fa-exclamation-triangle text-lg mt-0.5"></i>
            <div class="flex-1 text-sm">{{ session('error') }}</div>
            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- STAT CARDS --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total User --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total User</span>
                    <span class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                    </span>
                </div>
                <div class="text-3xl font-black text-gray-800 dark:text-white tabular-nums">{{ number_format($stats['total_users']) }}</div>
                <div class="text-xs text-gray-500 mt-1">akun terdaftar</div>
            </div>
        </div>

        {{-- NFC Aktif --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">NFC Aktif</span>
                    <span class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                    </span>
                </div>
                <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($stats['with_nfc']) }}</div>
                <div class="text-xs text-gray-500 mt-1">
                    @if($stats['total_users'] > 0)
                        {{ round(($stats['with_nfc'] / max($stats['total_users'], 1)) * 100) }}% dari total
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        {{-- Belum NFC --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Belum NFC</span>
                    <span class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-amber-600 dark:text-amber-400"></i>
                    </span>
                </div>
                <div class="text-3xl font-black text-amber-600 dark:text-amber-400 tabular-nums">{{ number_format($stats['without_nfc']) }}</div>
                <div class="text-xs text-gray-500 mt-1">menunggu didaftarkan</div>
            </div>
        </div>

        {{-- Revoked --}}
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-rose-500/10 to-transparent rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dicabut</span>
                    <span class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                        <i class="fas fa-ban text-rose-600 dark:text-rose-400"></i>
                    </span>
                </div>
                <div class="text-3xl font-black text-rose-600 dark:text-rose-400 tabular-nums">{{ number_format($stats['revoked']) }}</div>
                <div class="text-xs text-gray-500 mt-1">kartu tidak aktif</div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TOOLBAR --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-4">
        <div class="flex flex-col lg:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                <input type="text"
                       id="nfc-search"
                       x-model="search"
                       @keydown.enter.prevent="applySearch()"
                       placeholder="Cari nama / NIM... (tekan Enter)"
                       aria-label="Cari anggota"
                       class="w-full pl-11 pr-24 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition">

                <button type="button" @click="applySearch()"
                        class="absolute right-10 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-cyan-600 text-white text-xs font-semibold hover:bg-cyan-700 transition active:scale-95">
                    <i class="fas fa-search"></i> Cari
                </button>

                <button x-show="search" @click="search = ''; applySearch()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                        aria-label="Reset pencarian">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            {{-- Status filter pills --}}
            <div class="flex flex-wrap items-center gap-1.5 p-1 bg-gray-100 dark:bg-gray-900/40 rounded-xl">
                <button @click="setStatus('')"
                        :class="status === '' ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white'"
                        class="px-3.5 py-1.5 rounded-lg text-sm transition-all">
                    Semua
                </button>
                <button @click="setStatus('active')"
                        :class="status === 'active' ? 'bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white'"
                        class="px-3.5 py-1.5 rounded-lg text-sm transition-all">
                    <i class="fas fa-circle text-[8px] mr-1 text-emerald-500"></i> Aktif
                </button>
                <button @click="setStatus('none')"
                        :class="status === 'none' ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white'"
                        class="px-3.5 py-1.5 rounded-lg text-sm transition-all">
                    <i class="fas fa-circle text-[8px] mr-1 text-amber-500"></i> Belum
                </button>
                <button @click="setStatus('revoked')"
                        :class="status === 'revoked' ? 'bg-white dark:bg-gray-700 text-rose-600 dark:text-rose-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white'"
                        class="px-3.5 py-1.5 rounded-lg text-sm transition-all">
                    <i class="fas fa-circle text-[8px] mr-1 text-rose-500"></i> Dicabut
                </button>
            </div>

            {{-- Reload --}}
            <button onclick="window.location.href='{{ route('owner.management.nfc.index') }}'"
                    class="px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                    title="Reset filter" aria-label="Reset filter">
                <i class="fas fa-rotate-right text-gray-500"></i>
            </button>
        </div>

        {{-- Bulk actions bar --}}
        <div x-show="selected.length > 0" x-transition.duration.300ms
             class="mt-3 flex flex-wrap items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border border-cyan-200 dark:border-cyan-700/40">

            {{-- Counter selected --}}
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-cyan-600 text-white text-xs font-bold flex items-center justify-center tabular-nums"
                      x-text="selected.length"></span>
                <span class="font-semibold text-cyan-700 dark:text-cyan-300 text-sm">dipilih</span>

                {{-- Info: berapa yang eligible --}}
                <template x-if="selectedEligibleCount > 0">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-700/50">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span x-text="selectedEligibleCount"></span> siap daftar
                    </span>
                </template>

                {{-- Info: berapa yang sudah NFC --}}
                <template x-if="selectedBlockedCount > 0">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 font-semibold border border-amber-200 dark:border-amber-700/50">
                        <i class="fas fa-lock mr-1"></i>
                        <span x-text="selectedBlockedCount"></span> sudah NFC
                    </span>
                </template>
            </div>

            <div class="flex-1"></div>

            {{-- Daftarkan yang eligible --}}
            <button type="button" @click="openBulkGenerate()"
                    :disabled="selectedEligibleCount === 0"
                    :class="selectedEligibleCount === 0
                        ? 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-500 cursor-not-allowed'
                        : 'bg-cyan-600 text-white hover:bg-cyan-700 active:scale-95'"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                <i class="fas fa-plus-circle"></i>
                <span x-text="`Daftarkan (${selectedEligibleCount})`"></span>
            </button>

            {{-- Cabut massal --}}
            <button type="button" @click="openBulkRevoke()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-orange-500 text-white text-sm font-semibold hover:bg-orange-600 active:scale-95 transition shadow-sm">
                <i class="fas fa-ban"></i> Cabut Massal
            </button>

            {{-- Batal --}}
            <button type="button" @click="selected = []"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700/60">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox"
                                   @change="toggleAll($event)"
                                   :checked="allSelected"
                                   aria-label="Pilih semua"
                                   class="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-500 focus:ring-offset-0 cursor-pointer">
                        </th>
                        <th class="text-left px-4 py-3 font-semibold text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Anggota</th>
                        <th class="text-left px-4 py-3 font-semibold text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">UID Kartu</th>
                        <th class="text-left px-4 py-3 font-semibold text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">UUID</th>
                        <th class="text-left px-4 py-3 font-semibold text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Terdaftar</th>
                        <th class="text-right px-4 py-3 font-semibold text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($users as $user)
                        @php
                            $nfc      = $user->nfcCredential;
                            $hasNfc   = (bool) $nfc;
                            $isActive = $hasNfc && $nfc->is_active && !$nfc->is_revoked;
                            $label    = $hasNfc ? $nfc->status_label : 'Belum ada';
                            $color    = $hasNfc ? $nfc->status_color : 'gray';
                        @endphp
                        <tr class="group hover:bg-cyan-50/30 dark:hover:bg-cyan-900/10 transition-colors"
                            :class="selected.includes({{ $user->id }}) ? 'bg-cyan-50/50 dark:bg-cyan-900/20' : ''">

                            {{-- Checkbox --}}
                            <td class="px-4 py-3">
                                <input type="checkbox"
                                       value="{{ $user->id }}"
                                       x-model.number="selected"
                                       aria-label="Pilih {{ $user->name }}"
                                       class="row-checkbox w-4 h-4 rounded text-cyan-600 focus:ring-cyan-500 focus:ring-offset-0 cursor-pointer">
                            </td>

                            {{-- User info --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br
                                        @if($color === 'green') from-emerald-400 to-emerald-600
                                        @elseif($color === 'red') from-rose-400 to-rose-600
                                        @elseif($color === 'yellow') from-amber-400 to-amber-600
                                        @else from-gray-400 to-gray-500
                                        @endif
                                        text-white text-xs font-bold flex items-center justify-center shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-800 dark:text-white truncate">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2 flex-wrap">
                                            <span class="font-mono">{{ $user->nim ?? '-' }}</span>
                                            @if($user->level)
                                                <span class="px-1.5 py-0.5 rounded bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 text-[10px] font-medium">
                                                    {{ $user->level->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- UID --}}
                            <td class="px-4 py-3">
                                @if($nfc && $nfc->nfc_identifier)
                                    <button type="button"
                                            onclick="copyToClipboard('{{ $nfc->nfc_identifier }}', this)"
                                            class="group/uid inline-flex items-center gap-1.5 font-mono text-xs px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-cyan-100 dark:hover:bg-cyan-900/40 hover:text-cyan-700 dark:hover:text-cyan-300 transition"
                                            title="Klik untuk copy">
                                        <i class="fas fa-wifi text-[10px] opacity-50"></i>
                                        <span>{{ $nfc->nfc_identifier }}</span>
                                        <i class="fas fa-copy text-[10px] opacity-0 group-hover/uid:opacity-100 transition"></i>
                                    </button>
                                @elseif($nfc)
    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-md bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-700/50 font-semibold"
          title="NFC aktif tapi belum punya UID Fisik — tap kartu tidak akan terbaca">
        <i class="fas fa-exclamation-triangle"></i>
        Belum di-set
    </span>
@else
    <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
@endif
                            </td>

                            {{-- UUID --}}
                            <td class="px-4 py-3">
                                @if($nfc)
                                    <button type="button"
                                            onclick="copyToClipboard('{{ $nfc->nfc_uuid }}', this)"
                                            class="group/uuid inline-flex items-center gap-1.5 font-mono text-xs text-gray-500 hover:text-cyan-600 dark:hover:text-cyan-400 transition"
                                            title="{{ $nfc->nfc_uuid }}">
                                        <span>{{ \Illuminate\Support\Str::limit($nfc->nfc_uuid, 14) }}</span>
                                        <i class="fas fa-copy text-[10px] opacity-0 group-hover/uuid:opacity-100 transition"></i>
                                    </button>
                                @else
                                    <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = match ($color) {
                                        'green'  => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-700/50',
                                        'red'    => 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-700/50',
                                        'yellow' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-700/50',
                                        default  => 'bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-600',
                                    };
                                    $dotColor = match ($color) {
                                        'green'  => 'bg-emerald-500',
                                        'red'    => 'bg-rose-500',
                                        'yellow' => 'bg-amber-500',
                                        default  => 'bg-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badgeClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} {{ $isActive ? 'animate-pulse' : '' }}"></span>
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- Registered --}}
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if($nfc && $nfc->registered_at)
                                    <div class="font-medium text-gray-700 dark:text-gray-300">{{ $nfc->registered_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $nfc->registered_at->diffForHumans() }}</div>
                                @elseif($nfc && $nfc->generated_at)
                                    <div class="font-medium text-gray-700 dark:text-gray-300">{{ $nfc->generated_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $nfc->generated_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3">
                                @if($nfc)
                                    <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                                        @if($isActive)
                                            {{-- Download — buka modal pilih format --}}
<button type="button"
        onclick="openDownloadModal(
            {{ $user->id }},
            @js($user->name),
            @js($user->nim ?? '-'),
            @js($nfc->nfc_identifier ?? ''),
            @js($nfc->nfc_uuid ?? ''),
            @js($user->member_number ?? '')
        )"
        class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition"
        title="Download QR" aria-label="Download QR">
    <i class="fas fa-download text-sm"></i>
</button>
                                            {{-- Move --}}
                                            <button type="button"
                                                    onclick="openMoveModal('{{ $nfc->nfc_uuid }}', @js($user->name))"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/30 transition"
                                                    title="Pindahkan NFC" aria-label="Pindahkan NFC">
                                                <i class="fas fa-exchange-alt text-sm"></i>
                                            </button>

                                            {{-- Revoke --}}
                                            <button type="button"
                                                    onclick="openRevokeModal('{{ $nfc->nfc_uuid }}', @js($user->name))"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/30 transition"
                                                    title="Cabut NFC" aria-label="Cabut NFC">
                                                <i class="fas fa-ban text-sm"></i>
                                            </button>
                                        @endif

                                        {{-- Delete --}}
                                        <button type="button"
                                                onclick="openDeleteModal('{{ $nfc->id }}', @js($user->name))"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition"
                                                title="Hapus data" aria-label="Hapus data">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                @else
                                    {{-- Tombol Daftarkan — kirim preselect user ini --}}
                                    <div class="flex justify-end">
                                        <a href="{{ route('owner.management.nfc.generate', ['preselect' => $user->id]) }}"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 transition">
                                            <i class="fas fa-plus-circle"></i> Daftarkan
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                        <i class="fas fa-id-card text-3xl text-gray-400"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        @if(request()->hasAny(['q', 'status']))
                                            Tidak ada hasil
                                        @else
                                            Belum ada data
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request()->hasAny(['q', 'status']))
                                            Coba ubah kata kunci atau filter status.
                                        @else
                                            Mulai dengan mendaftarkan NFC untuk anggota.
                                        @endif
                                    </p>
                                    <a href="{{ route('owner.management.nfc.generate') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition text-sm font-semibold">
                                        <i class="fas fa-plus"></i> Daftarkan Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $users->firstItem() }}</span>
                        – <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $users->lastItem() }}</span>
                        dari <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $users->total() }}</span> user
                    </p>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODALS --}}
{{-- ============================================================ --}}
{{-- ============================================================ --}}
{{-- MODAL: DOWNLOAD QR --}}
{{-- ============================================================ --}}
<div id="downloadModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden animate-slideUp">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-download text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Download QR Code</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pilih format file yang diinginkan.</p>
            </div>
            <button type="button" onclick="closeDownloadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 -mt-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">

            {{-- User Info Card --}}
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-200 dark:border-blue-700/40">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xl font-black shadow-lg flex-shrink-0"
                     id="dlAvatar">?</div>
                <div class="flex-1 min-w-0">
                    <div class="font-black text-gray-800 dark:text-white truncate" id="dlName">—</div>
                    <div class="text-xs text-gray-500 font-mono mt-0.5" id="dlNim">—</div>
                    <div class="text-[10px] text-gray-400 mt-0.5" id="dlMemberNumber">—</div>
                </div>
            </div>

            {{-- Info Kredensial --}}
            <div class="grid grid-cols-2 gap-3">
                {{-- UID Fisik --}}
                <div class="p-3 rounded-xl bg-gray-900 dark:bg-black border border-gray-700">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-microchip text-emerald-400 text-[10px]"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">UID Fisik Kartu</span>
                    </div>
                    <div class="font-mono text-emerald-400 text-xs break-all" id="dlUid">—</div>
                </div>

                {{-- System UUID --}}
                <div class="p-3 rounded-xl bg-gray-900 dark:bg-black border border-gray-700">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-fingerprint text-cyan-400 text-[10px]"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">System UUID</span>
                    </div>
                    <div class="font-mono text-cyan-400 text-[10px] break-all" id="dlUuid">—</div>
                </div>
            </div>

            {{-- Pilih Format --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-file-export mr-1 text-blue-500"></i>
                    Pilih format download
                </label>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">

                    {{-- PNG --}}
                    <button type="button" onclick="downloadAs('png')"
                            class="group flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:shadow-md active:scale-[0.97] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition">
                            <i class="fas fa-image text-blue-600 dark:text-blue-400 group-hover:text-white"></i>
                        </div>
                        <span class="font-bold text-sm text-gray-800 dark:text-white">PNG</span>
                        <span class="text-[9px] text-gray-500 dark:text-gray-400">Lossless</span>
                    </button>

                    {{-- JPG --}}
                    <button type="button" onclick="downloadAs('jpg')"
                            class="group flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:shadow-md active:scale-[0.97] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition">
                            <i class="fas fa-file-image text-amber-600 dark:text-amber-400 group-hover:text-white"></i>
                        </div>
                        <span class="font-bold text-sm text-gray-800 dark:text-white">JPG</span>
                        <span class="text-[9px] text-gray-500 dark:text-gray-400">Compact</span>
                    </button>

                    {{-- JPEG --}}
                    <button type="button" onclick="downloadAs('jpeg')"
                            class="group flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:shadow-md active:scale-[0.97] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition">
                            <i class="fas fa-file-image text-orange-600 dark:text-orange-400 group-hover:text-white"></i>
                        </div>
                        <span class="font-bold text-sm text-gray-800 dark:text-white">JPEG</span>
                        <span class="text-[9px] text-gray-500 dark:text-gray-400">Standard</span>
                    </button>

                    {{-- PDF --}}
                    <button type="button" onclick="downloadAs('pdf')"
                            class="group flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:shadow-md active:scale-[0.97] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition">
                            <i class="fas fa-file-pdf text-rose-600 dark:text-rose-400 group-hover:text-white"></i>
                        </div>
                        <span class="font-bold text-sm text-gray-800 dark:text-white">PDF</span>
                        <span class="text-[9px] text-gray-500 dark:text-gray-400">Print-ready</span>
                    </button>
                </div>
            </div>

            {{-- Info hint --}}
            <div class="flex items-start gap-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40 text-xs text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                <span>File akan otomatis ter-download. Cek folder <strong>Downloads</strong> di browser Anda.</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex">
            <button type="button" onclick="closeDownloadModal()"
                    class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-[0.98] transition">
                Tutup
            </button>
        </div>
    </div>
</div>
{{-- MODAL: PINDAHKAN --}}
<div id="moveModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden animate-slideUp">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exchange-alt text-purple-600 dark:text-purple-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Pindahkan NFC</h3>
                <p class="text-xs text-gray-500 mt-0.5">Credential kartu tetap sama, hanya berpindah pemilik.</p>
            </div>
            <button onclick="closeMoveModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 -mt-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="moveForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="p-4 rounded-xl bg-gradient-to-br from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 border border-purple-100 dark:border-purple-700/40">
                <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold mb-1">DIPINDAHKAN DARI</p>
                <p class="font-bold text-gray-800 dark:text-white" id="moveFromName"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Ke user <span class="text-red-500">*</span>
                </label>
                <select name="new_user_id" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500">
                    <option value="">— Pilih user tujuan —</option>
                    @foreach($moveCandidates as $candidate)
                        <option value="{{ $candidate->id }}">
                            {{ $candidate->name }} ({{ $candidate->nim ?? 'tanpa NIM' }})
                        </option>
                    @endforeach
                </select>
                @if($moveCandidates->isEmpty())
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i> Tidak ada user yang belum punya NFC aktif.
                    </p>
                @endif
            </div>

            <div class="flex items-start gap-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-xs text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle mt-0.5"></i>
                <span>Setelah dipindahkan, pemilik lama otomatis kehilangan akses NFC ini.</span>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeMoveModal()"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 transition shadow-sm disabled:opacity-50"
                        {{ $moveCandidates->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-exchange-alt mr-1"></i> Pindahkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: CABUT --}}
<div id="revokeModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden animate-slideUp">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-ban text-orange-600 dark:text-orange-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Cabut NFC</h3>
                <p class="text-xs text-gray-500 mt-0.5">Kartu tidak akan bisa digunakan untuk login/absensi.</p>
            </div>
            <button onclick="closeRevokeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 -mt-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="revokeForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="p-4 rounded-xl bg-gradient-to-br from-orange-50 to-rose-50 dark:from-orange-900/20 dark:to-rose-900/20 border border-orange-100 dark:border-orange-700/40">
                <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mb-1">AKAN DICABUT</p>
                <p class="font-bold text-gray-800 dark:text-white" id="revokeTargetName"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Alasan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="reason" rows="3" maxlength="255"
                          placeholder="Contoh: kartu hilang, sudah diganti dengan yang baru..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500 resize-none"></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeRevokeModal()"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600 transition shadow-sm">
                    <i class="fas fa-ban mr-1"></i> Cabut
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: HAPUS --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full sm:max-w-sm bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden animate-slideUp">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                <i class="fas fa-trash text-2xl text-rose-600 dark:text-rose-400"></i>
            </div>
            <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-1">Hapus NFC?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Data NFC milik</p>
            <p class="font-bold text-gray-800 dark:text-white mb-3" id="deleteTargetName"></p>
            <p class="text-xs text-gray-500">Riwayat tetap tersimpan (soft delete).</p>
        </div>
        <form id="deleteForm" method="POST" class="px-6 pb-6 flex gap-2">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                Batal
            </button>
            <button type="submit"
                    class="flex-1 px-4 py-3 rounded-xl bg-rose-600 text-white font-semibold hover:bg-rose-700 transition shadow-sm">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- MODAL: BULK REVOKE --}}
<div id="bulkRevokeModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden animate-slideUp">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-layer-group text-rose-600 dark:text-rose-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Cabut Massal</h3>
                <p class="text-xs text-gray-500 mt-0.5">Semua kartu yang dipilih akan dicabut sekaligus.</p>
            </div>
            <button onclick="closeBulkRevokeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 -mt-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('owner.management.nfc.bulk-revoke') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="nfc_ids[]" :value="id">
            </template>

            <div class="p-4 rounded-xl bg-gradient-to-br from-rose-50 to-orange-50 dark:from-rose-900/20 dark:to-orange-900/20 border border-rose-100 dark:border-rose-700/40 text-center">
                <div class="text-3xl font-black text-rose-600 dark:text-rose-400" x-text="selected.length"></div>
                <div class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-0.5">NFC akan dicabut</div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Alasan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="reason" rows="2" maxlength="255"
                          placeholder="Contoh: audit keamanan rutin..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/40 focus:border-rose-500 resize-none"></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeBulkRevokeModal()"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-rose-600 text-white font-semibold hover:bg-rose-700 transition shadow-sm">
                    <i class="fas fa-ban mr-1"></i> Cabut Semua
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] hidden px-4 py-3 rounded-xl bg-gray-900 text-white text-sm shadow-2xl animate-slideUp flex items-center">
    <i class="fas fa-check-circle text-emerald-400 mr-2"></i>
    <span id="toastMessage">Tersalin ke clipboard</span>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn .25s ease-out; }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-slideUp { animation: slideUp .25s cubic-bezier(.16, 1, .3, 1); }

    .row-checkbox:checked {
        background-color: rgb(8 145 178);
        border-color: rgb(8 145 178);
    }

    @media (hover: none) {
        button:active, a:active { transform: scale(.97); }
    }
</style>
@endpush

@push('scripts')
<script>
    /**
     * Alpine component — NFC Index page.
     * Terima array eligibleIds (user yang belum punya NFC aktif) dari controller.
     */
    function nfcIndex(eligibleIds = []) {
        return {
            // ---------- STATE ----------
            search: @json(request('q', '')),
            status: @json(request('status', '')),
            selected: [],

            /** Set of user IDs yang eligible untuk didaftarkan NFC baru. */
            eligibleSet: new Set(eligibleIds),

            // ---------- COMPUTED ----------
            get allSelected() {
                const boxes = document.querySelectorAll('.row-checkbox');
                return boxes.length > 0 && this.selected.length === boxes.length;
            },

            /** Jumlah user terpilih yang BELUM punya NFC aktif. */
            get selectedEligibleCount() {
                return this.selected.filter(id => this.eligibleSet.has(id)).length;
            },

            /** Jumlah user terpilih yang SUDAH punya NFC aktif. */
            get selectedBlockedCount() {
                return this.selected.filter(id => !this.eligibleSet.has(id)).length;
            },

            // ---------- METHODS ----------
            setStatus(val) {
                this.status = val;
                this.applySearch();
            },

            applySearch() {
                const params = new URLSearchParams();
                if (this.search) params.set('q', this.search);
                if (this.status) params.set('status', this.status);
                const qs = params.toString();
                window.location.href = '{{ route('owner.management.nfc.index') }}' + (qs ? '?' + qs : '');
            },

            toggleAll(e) {
                const boxes = document.querySelectorAll('.row-checkbox');
                if (e.target.checked) {
                    this.selected = Array.from(boxes).map(b => parseInt(b.value, 10));
                } else {
                    this.selected = [];
                }
            },

            /**
             * Arahkan ke halaman generate dengan hanya user eligible.
             * Kalau tidak ada yang eligible → toast error.
             */
            openBulkGenerate() {
                if (this.selected.length === 0) return;

                const eligible = this.selected.filter(id => this.eligibleSet.has(id));

                if (eligible.length === 0) {
                    showToast('Semua akun terpilih sudah punya NFC aktif.', 'error');
                    return;
                }

                window.location.href = '{{ route('owner.management.nfc.generate') }}'
                    + '?preselect=' + encodeURIComponent(eligible.join(','));
            },

            openBulkRevoke() {
                if (this.selected.length === 0) return;
                openModal('bulkRevokeModal');
            },
        };
    }

    // ---------- Modal helpers ----------
    function openModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.classList.add('hidden');
        m.classList.remove('flex');
        document.body.style.overflow = '';
    }
/* ============================================================
   DOWNLOAD MODAL
   ============================================================ */
let currentDownloadUserId = null;

/**
 * Buka modal download + isi data user.
 */
function openDownloadModal(userId, userName, userNim, uidPhysical, systemUuid, memberNumber) {
    // Simpan user_id untuk tombol download
    currentDownloadUserId = userId;

    // Isi avatar (huruf pertama nama)
    document.getElementById('dlAvatar').textContent = (userName || '?').charAt(0).toUpperCase();

    // Isi nama + NIM + member number
    document.getElementById('dlName').textContent = userName || '—';
    document.getElementById('dlNim').textContent  = 'NIM: ' + (userNim || '-');

    const memberEl = document.getElementById('dlMemberNumber');
    if (memberNumber) {
        memberEl.textContent = 'Member #' + memberNumber;
        memberEl.style.display = '';
    } else {
        memberEl.style.display = 'none';
    }

    // Isi UID Fisik (kalau kosong, tampilkan warning)
    const uidEl = document.getElementById('dlUid');
    if (uidPhysical && uidPhysical.trim()) {
        uidEl.textContent = uidPhysical;
        uidEl.className = 'font-mono text-emerald-400 text-xs break-all';
    } else {
        uidEl.textContent = '⚠ Belum di-set — tap kartu tidak akan terbaca';
        uidEl.className = 'font-mono text-amber-400 text-[10px] italic';
    }

    // Isi System UUID
    document.getElementById('dlUuid').textContent = systemUuid || '—';

    // Buka modal
    openModal('downloadModal');
}

/**
 * Tutup modal download.
 */
function closeDownloadModal() {
    closeModal('downloadModal');
    currentDownloadUserId = null;
}

/**
 * Download QR dengan format tertentu.
 * Menggunakan iframe tersembunyi → download tanpa navigate/reload halaman.
 */
function downloadAs(format) {
    if (!currentDownloadUserId) {
        showToast('Data user tidak ditemukan. Coba buka ulang.', 'error');
        return;
    }

    // Validasi format
    const allowed = ['png', 'jpg', 'jpeg', 'pdf'];
    if (!allowed.includes(format)) {
        showToast('Format tidak didukung: ' + format, 'error');
        return;
    }

    // Build URL download
    const url = '/owner/management/nfc/download/' + currentDownloadUserId + '/' + format;

    // Feedback visual
    showToast('Menyiapkan file ' + format.toUpperCase() + '...', 'success');

    // Trigger download via iframe tersembunyi (tidak navigate halaman)
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    iframe.src = url;

    document.body.appendChild(iframe);

    // Hapus iframe setelah 10 detik
    setTimeout(() => {
        iframe.remove();
    }, 10000);

    // Tutup modal setelah 500ms
    setTimeout(() => {
        closeDownloadModal();
    }, 500);
}
    function openMoveModal(nfcUuid, userName) {
        document.getElementById('moveForm').action = '/owner/management/nfc/' + nfcUuid + '/move';
        document.getElementById('moveFromName').textContent = userName;
        openModal('moveModal');
    }
    function closeMoveModal() { closeModal('moveModal'); }

    function openRevokeModal(nfcUuid, userName) {
        document.getElementById('revokeForm').action = '/owner/management/nfc/' + nfcUuid + '/revoke';
        document.getElementById('revokeTargetName').textContent = userName;
        openModal('revokeModal');
    }
    function closeRevokeModal() { closeModal('revokeModal'); }

    function openDeleteModal(nfcId, userName) {
        document.getElementById('deleteForm').action = '/owner/management/nfc/' + nfcId;
        document.getElementById('deleteTargetName').textContent = userName;
        openModal('deleteModal');
    }
    function closeDeleteModal() { closeModal('deleteModal'); }

    function closeBulkRevokeModal() { closeModal('bulkRevokeModal'); }

    // ---------- ESC + backdrop ----------
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            ['moveModal', 'revokeModal', 'deleteModal', 'bulkRevokeModal'].forEach(closeModal);
        }
        if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            document.getElementById('nfc-search')?.focus();
        }
    });

    ['moveModal', 'revokeModal', 'deleteModal', 'bulkRevokeModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', (e) => {
            if (e.target === el) closeModal(id);
        });
    });

    // ---------- Copy to clipboard + toast ----------
    function copyToClipboard(text, el) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Tersalin: ' + (text.length > 20 ? text.substring(0, 20) + '…' : text));
            if (el) {
                const original = el.innerHTML;
                el.innerHTML = '<i class="fas fa-check text-emerald-500"></i>';
                setTimeout(() => { el.innerHTML = original; }, 900);
            }
        }).catch(() => {
            showToast('Gagal copy — copy manual: ' + text, 'error');
        });
    }

    let toastTimer = null;
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        const m = document.getElementById('toastMessage');
        if (!t || !m) return;
        m.textContent = msg;
        t.classList.remove('hidden');
        t.classList.add('flex');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            t.classList.add('hidden');
            t.classList.remove('flex');
        }, 2500);
    }
</script>
@endpush
@endsection
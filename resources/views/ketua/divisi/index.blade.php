@extends('layouts.ketua')
@section('title', 'Divisi – UNICODE')

@section('content')
@php
    $stats = array_merge([
        'total'         => $divisions->total() ?? 0,
        'aktif'         => 0,
        'nonaktif'      => 0,
        'total_anggota' => 0,
    ], $stats ?? []);
@endphp

<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    {{-- HERO HEADER --}}
    <div class="relative overflow-hidden rounded-3xl
                bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600
                p-6 sm:p-8 shadow-2xl shadow-purple-500/20">
        <div class="absolute inset-0 opacity-20"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-white/15 blur-3xl"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-xl border border-white/30
                            flex items-center justify-center shadow-lg">
                    <i class="fas fa-sitemap text-white text-2xl"></i>
                </div>
                <div>
                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur text-white text-[10px]
                                 font-bold uppercase tracking-widest border border-white/20 inline-flex items-center">
                        <i class="fas fa-users-cog text-[8px] mr-1"></i> Manajemen Ketua
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-2">Divisi</h1>
                    <p class="text-sm text-white/80 mt-0.5">Kelola semua divisi dalam organisasi</p>
                </div>
            </div>

            <div class="px-5 py-4 rounded-2xl bg-white/15 backdrop-blur-xl border border-white/20">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-database text-white/70 text-xs"></i>
                    <span class="text-white/70 text-[10px] font-bold uppercase tracking-widest">Total</span>
                </div>
                <span class="text-white text-2xl font-black tabular-nums">
                    {{ number_format($stats['total']) }}
                </span>
            </div>
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $statCards = [
                ['icon' => 'fa-sitemap',     'label' => 'Total Divisi',  'value' => $stats['total'],         'from' => 'from-violet-500',  'to' => 'to-purple-600'],
                ['icon' => 'fa-check-circle','label' => 'Divisi Aktif',  'value' => $stats['aktif'],         'from' => 'from-emerald-500', 'to' => 'to-teal-600'],
                ['icon' => 'fa-pause-circle','label' => 'Nonaktif',      'value' => $stats['nonaktif'],      'from' => 'from-amber-500',   'to' => 'to-orange-600'],
                ['icon' => 'fa-users',       'label' => 'Total Anggota', 'value' => $stats['total_anggota'], 'from' => 'from-fuchsia-500', 'to' => 'to-pink-600'],
            ];
        @endphp

        @foreach($statCards as $s)
        <div class="group bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800
                    p-4 sm:p-5 hover:shadow-2xl hover:shadow-purple-500/10 hover:-translate-y-1
                    transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        {{ $s['label'] }}
                    </p>
                    <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1 tabular-nums">
                        {{ number_format($s['value']) }}
                    </p>
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

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ACTION TOOLBAR — 6 TOMBOL AKSI --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-3 sm:p-4">
        <div class="flex flex-wrap items-center gap-2">

            {{-- Buat Divisi --}}
            <a href="{{ route('ketua.divisi.custom') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-violet-500 to-fuchsia-600
                      text-white text-sm font-bold
                      hover:shadow-lg hover:shadow-violet-500/30
                      hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Buat Divisi</span>
                <span class="sm:hidden">Buat</span>
            </a>

            {{-- Lihat Anggota --}}
            <a href="{{ route('ketua.divisi.anggota') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-blue-500 to-cyan-600
                      text-white text-sm font-bold
                      hover:shadow-lg hover:shadow-blue-500/30
                      hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-users"></i>
                <span class="hidden sm:inline">Lihat Anggota</span>
                <span class="sm:hidden">Anggota</span>
            </a>

            {{-- Hak Akses --}}
            <a href="{{ route('ketua.divisi.akses') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-rose-500 to-red-600
                      text-white text-sm font-bold
                      hover:shadow-lg hover:shadow-rose-500/30
                      hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-key"></i>
                <span class="hidden sm:inline">Hak Akses</span>
                <span class="sm:hidden">Akses</span>
            </a>

            {{-- Invite Anggota --}}
            <a href="{{ route('ketua.divisi.invite') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-emerald-500 to-teal-600
                      text-white text-sm font-bold
                      hover:shadow-lg hover:shadow-emerald-500/30
                      hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-user-plus"></i>
                <span class="hidden sm:inline">Invite Anggota</span>
                <span class="sm:hidden">Invite</span>
            </a>

            {{-- Pengumuman --}}
            <a href="{{ route('ketua.divisi.pengumuman') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-amber-500 to-orange-600
                      text-white text-sm font-bold
                      hover:shadow-lg hover:shadow-amber-500/30
                      hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-bullhorn"></i>
                <span class="hidden sm:inline">Pengumuman</span>
                <span class="sm:hidden">Umum</span>
            </a>

            {{-- Beri Pesan --}}
            <a href="{{ route('ketua.divisi.pesan') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                      bg-gradient-to-r from-indigo-500 to-blue-600
                      text-white text-sm font-bold
                      hover:shadow-lg hover:shadow-indigo-500/30
                      hover:-translate-y-0.5 transition-all active:scale-95">
                <i class="fas fa-comment-dots"></i>
                <span class="hidden sm:inline">Beri Pesan</span>
                <span class="sm:hidden">Pesan</span>
            </a>
        </div>
    </div>

    {{-- TOOLBAR SEARCH --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-3 sm:p-4">
        <form method="GET" action="{{ route('ketua.divisi.index') }}"
              class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm
                          group-focus-within:text-violet-500 transition"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama atau kode divisi..."
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-violet-500
                              focus:border-violet-500 outline-none transition">
            </div>

            <select name="status"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                           bg-gray-50 dark:bg-gray-800/50 text-sm focus:ring-2 focus:ring-violet-500
                           outline-none transition font-medium text-gray-700 dark:text-gray-300">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status')==='aktif' ? 'selected' : '' }}>✓ Aktif</option>
                <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected' : '' }}>✗ Nonaktif</option>
            </select>

            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600
                           text-white text-sm font-bold hover:shadow-lg hover:shadow-violet-500/30
                           transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['q', 'status']))
                <a href="{{ route('ketua.divisi.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                          text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition
                          flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- GRID --}}
    @if($divisions->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($divisions as $div)
            <div class="group relative bg-white dark:bg-gray-900 rounded-2xl
                        border border-gray-200 dark:border-gray-800 overflow-hidden
                        hover:border-violet-300 dark:hover:border-violet-700
                        hover:shadow-2xl hover:shadow-violet-500/10 hover:-translate-y-1.5
                        transition-all duration-300">

                <div class="absolute top-0 left-0 right-0 h-1
                            bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500
                            scale-x-0 group-hover:scale-x-100 origin-left
                            transition-transform duration-300"></div>

                <a href="{{ route('ketua.divisi.show', $div->id) }}" class="block p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl
                                    bg-gradient-to-br from-violet-500 to-fuchsia-600
                                    flex items-center justify-center shrink-0 shadow-lg
                                    group-hover:scale-105 group-hover:rotate-3 transition-transform">
                            <i class="fas fa-sitemap text-white text-xl"></i>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 dark:text-white truncate
                                       group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">
                                {{ $div->name }}
                            </h3>
                            @if($div->code)
                                <p class="text-[11px] font-mono text-gray-400 mt-0.5">{{ $div->code }}</p>
                            @endif
                            @if($div->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $div->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4 flex-wrap">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                                     text-[10px] font-bold uppercase tracking-wide
                                     {{ $div->status === 'aktif'
                                        ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800'
                                        : 'bg-gray-100 dark:bg-gray-800 text-gray-500 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full
                                        {{ $div->status === 'aktif' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                            {{ $div->status ?? 'unknown' }}
                        </span>

                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                                     text-[10px] font-bold uppercase tracking-wide
                                     bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400
                                     ring-1 ring-violet-200 dark:ring-violet-800">
                            <i class="fas fa-users text-[8px]"></i>
                            {{ $div->members_count ?? 0 }} anggota
                        </span>
                    </div>
                </a>

                <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-800/30
                            border-t border-gray-100 dark:border-gray-800
                            flex items-center justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Lihat Detail
                    </span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('ketua.divisi.anggota', ['divisionId' => $div->id]) }}"
                           onclick="event.stopPropagation()"
                           class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                  flex items-center justify-center hover:bg-blue-200 transition"
                           title="Lihat Anggota">
                            <i class="fas fa-users text-[10px]"></i>
                        </a>
                        <a href="{{ route('ketua.divisi.akses', ['division_id' => $div->id]) }}"
                           onclick="event.stopPropagation()"
                           class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400
                                  flex items-center justify-center hover:bg-rose-200 transition"
                           title="Hak Akses">
                            <i class="fas fa-key text-[10px]"></i>
                        </a>
                        <a href="{{ route('ketua.divisi.show', $div->id) }}"
                           class="w-7 h-7 rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400
                                  flex items-center justify-center hover:bg-violet-200 transition"
                           title="Detail">
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($divisions->hasPages())
            <div class="flex justify-center pt-4">
                {{ $divisions->withQueryString()->links() }}
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-900 rounded-3xl
                    border-2 border-dashed border-gray-200 dark:border-gray-800
                    p-16 text-center">
            <div class="w-20 h-20 mx-auto rounded-3xl
                        bg-gradient-to-br from-violet-100 to-fuchsia-100
                        dark:from-violet-900/30 dark:to-fuchsia-900/20
                        flex items-center justify-center mb-5">
                <i class="fas fa-sitemap text-violet-500 text-3xl"></i>
            </div>
            <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-2">Belum ada divisi</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-5">
                Buat divisi pertama untuk menampung anggota dan struktur organisasi.
            </p>
            <a href="{{ route('ketua.divisi.custom') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                      bg-gradient-to-r from-violet-500 to-fuchsia-600 text-white
                      font-bold text-sm hover:shadow-lg transition">
                <i class="fas fa-plus"></i> Buat Divisi Pertama
            </a>
        </div>
    @endif
</div>
@endsection
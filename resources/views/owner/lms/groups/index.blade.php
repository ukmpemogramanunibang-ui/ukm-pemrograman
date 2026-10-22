@extends('layouts.roles.owner')

@section('title', 'Pengelompokan — Tugas & Materi')

@section('content')
@php
    $authId = auth()->id();

    $resolveAvatar = function ($user) {
        if (!$user) return 'https://ui-avatars.com/api/?name=?&background=6366f1&color=fff';
        $raw = $user->avatar_url ?? null;
        if (!$raw) {
            return 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'U').'&background=6366f1&color=fff';
        }
        return \Illuminate\Support\Str::startsWith($raw, ['http', '/'])
            ? $raw
            : asset('storage/'.ltrim($raw, '/'));
    };

    $resolveRole = function ($user) {
        if (!$user) return 'Sistem';
        if (!empty($user->level->name)) return $user->level->name;
        foreach (['role_label','jabatan','position','role'] as $col) {
            if (!empty($user->{$col})) return ucfirst(str_replace('_',' ', $user->{$col}));
        }
        return 'Member';
    };
@endphp

<style>
    /* Fallback line-clamp — bekerja tanpa plugin */
    .lc-1, .lc-2 {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .lc-1 { -webkit-line-clamp: 1; line-clamp: 1; }
    .lc-2 { -webkit-line-clamp: 2; line-clamp: 2; }
</style>

<div class="p-4 sm:p-6 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-start justify-between flex-wrap gap-3 mb-5">
        <div class="min-w-0">
            <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap mb-1">
                <a href="{{ route('owner.lms.dashboard') }}" class="hover:text-violet-600">
                    <i class="fas fa-home"></i> LMS
                </a>
                <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
                <span class="font-bold text-gray-700 dark:text-gray-300">Pengelompokan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">Pengelompokan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Folder untuk menampung tugas &amp; materi UNICODE.
            </p>
        </div>

        <a href="{{ route('owner.lms.groups.create') }}"
           class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl text-white font-bold text-sm
                  bg-gradient-to-r from-violet-500 to-fuchsia-500
                  hover:shadow-lg hover:shadow-violet-500/30 transition-all">
            <i class="fas fa-plus"></i> Buat Pengelompokan
        </a>
    </div>

    {{-- SEARCH --}}
    <form method="GET" class="mb-5">
        <div class="relative max-w-md">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari pengelompokan..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl
                          border border-gray-200 dark:border-gray-700
                          bg-white dark:bg-gray-800 text-sm
                          focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">
        </div>
    </form>

    {{-- GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @forelse($groups as $g)
            @php
                $isMine = ($g->created_by ?? null) === $authId
                       || ($g->creator_id ?? null) === $authId
                       || (isset($g->creator) && $g->creator && $g->creator->id === $authId);
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden
                        border border-gray-100 dark:border-gray-700 shadow-sm
                        hover:shadow-xl hover:-translate-y-0.5 transition-all">

                {{-- COVER --}}
                <div class="h-28 sm:h-32 relative"
                     style="background: {{ $g->cover_url
                        ? 'url('.$g->cover_url.') center/cover'
                        : 'linear-gradient(135deg,#8b5cf6,#d946ef)' }};">
                    @if(!$g->cover_url)
                        <div class="absolute inset-0 flex items-center justify-center text-white/90 text-5xl">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    @endif

                    {{-- badge item count --}}
                    <span class="absolute top-3 right-3 px-2 py-1 rounded-full text-[10px] font-bold
                                 bg-white/20 backdrop-blur-sm text-white border border-white/30">
                        <i class="fas fa-layer-group text-[9px] mr-1"></i>
                        {{ $g->items_count }} item
                    </span>

                    {{-- badge ownership --}}
                    @if($isMine)
                        <span class="absolute top-3 left-3 px-2 py-1 rounded-full text-[10px] font-bold
                                     bg-emerald-500/90 text-white shadow-sm">
                            <i class="fas fa-user-shield text-[9px] mr-1"></i> Grup Saya
                        </span>
                    @else
                        <span class="absolute top-3 left-3 px-2 py-1 rounded-full text-[10px] font-bold
                                     bg-slate-700/80 backdrop-blur-sm text-white">
                            <i class="fas fa-users text-[9px] mr-1"></i> Grup Bersama
                        </span>
                    @endif
                </div>

                {{-- BODY --}}
                <div class="p-4">
                    <h3 class="font-black text-base text-gray-900 dark:text-white lc-1">
                        {{ $g->name }}
                    </h3>

                    @if($g->code)
                        <p class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $g->code }}</p>
                    @endif

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 lc-2 min-h-[2.4em]">
                        {{ $g->description ?: 'Tanpa deskripsi.' }}
                    </p>

                    {{-- CREATOR --}}
                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-dashed border-gray-200 dark:border-gray-700">
                        <img src="{{ $resolveAvatar($g->creator) }}"
                             alt="{{ $g->creator->name ?? '?' }}"
                             class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 lc-1">
                                {{ $g->creator->name ?? 'Tidak diketahui' }}
                            </p>
                            <p class="text-[9px] font-bold uppercase text-violet-600 dark:text-violet-400 lc-1">
                                {{ $resolveRole($g->creator) }}
                            </p>
                        </div>

                        <a href="{{ route('owner.lms.groups.show', $g->id) }}"
                           class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold
                                  bg-violet-50 dark:bg-violet-900/30
                                  text-violet-600 dark:text-violet-300
                                  hover:bg-violet-100 dark:hover:bg-violet-900/50 transition">
                            Buka <i class="fas fa-arrow-right text-[9px] ml-0.5"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-gray-100 dark:bg-gray-800
                            flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                </div>
                <p class="font-bold text-gray-700 dark:text-gray-300">Belum ada pengelompokan.</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Buat folder pertama untuk menampung tugas &amp; materi.
                </p>
                <a href="{{ route('owner.lms.groups.create') }}"
                   class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl
                          text-white font-bold text-sm
                          bg-gradient-to-r from-violet-500 to-fuchsia-500">
                    <i class="fas fa-plus"></i> Buat Sekarang
                </a>
            </div>
        @endforelse
    </div>

    @if($groups->hasPages())
        <div class="mt-6">{{ $groups->links() }}</div>
    @endif
</div>
@endsection
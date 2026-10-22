@extends('layouts.roles.owner')
@section('title', 'Tugas & Materi — Command Center')

@section('content')
@php
    /**
     * Resolve URL cover (gambar) dari model.
     * Mendukung: full URL, path relatif storage, atau null.
     */
    $resolveCover = function ($model, array $candidates = ['cover_path','cover','cover_url','thumbnail','image','banner']) {
        if (!$model) return null;
        foreach ($candidates as $c) {
            $val = $model->{$c} ?? null;
            if (!empty($val)) {
                if (\Illuminate\Support\Str::startsWith($val, ['http://','https://','/'])) {
                    return $val;
                }
                return asset('storage/'.ltrim($val, '/'));
            }
        }
        return null;
    };

    /**
     * Ambil label role tertinggi dari user.
     * Cek: kolom langsung -> Spatie getRoleNames -> relasi roles (urut level/priority).
     */
$resolveRole = function ($user) {
    if (!$user) return 'Sistem';

    // Prioritas: level relationship
    if (!empty($user->level->name)) {
        return $user->level->name;
    }

    foreach (['role_label','jabatan','position','role'] as $col) {
        if (!empty($user->{$col})) {
            return ucfirst(str_replace('_',' ', $user->{$col}));
        }
    }

    if (method_exists($user, 'getRoleNames')) {
        $names = $user->getRoleNames();
        if ($names && $names->count()) {
            return $names->first();
        }
    }

    return 'Member';
};

    /**
     * Avatar user dengan fallback UI-Avatars.
     */
    $resolveAvatar = function ($user) {
        if (!$user) return 'https://ui-avatars.com/api/?name=?&background=6366f1&color=fff';
        if (!empty($user->avatar_url)) return $user->avatar_url;
        if (!empty($user->avatar)) {
            return \Illuminate\Support\Str::startsWith($user->avatar, ['http','/'])
                ? $user->avatar
                : asset('storage/'.$user->avatar);
        }
        return 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'U').'&background=6366f1&color=fff';
    };

    // Grid class reusable: 2 (HP vertikal) · 3 (HP horizontal/tablet) · 4 (desktop)
    $gridCls = 'grid grid-cols-2 landscape:grid-cols-3 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4';
@endphp

<div class="p-4 sm:p-5 max-w-7xl mx-auto space-y-5">

    {{-- HERO --}}
    <div class="rounded-2xl p-5 sm:p-6 text-white bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-fuchsia-400/20 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-graduation-cap"></i> UNICODE LMS
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Tugas &amp; Materi</h1>
            <p class="text-sm opacity-90 mt-1">Kelola pembelajaran, tugas, dan pengumpulan dari satu command center.</p>
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] uppercase font-bold text-gray-400 tracking-wide">Total Item</p>
            <p class="text-2xl font-black text-violet-500 mt-1">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">{{ $stats['tasks'] ?? 0 }} tugas · {{ $stats['materials'] ?? 0 }} materi</p>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] uppercase font-bold text-gray-400 tracking-wide">Baru</p>
            <p class="text-2xl font-black text-blue-500 mt-1">{{ $myStats['new'] ?? 0 }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Belum dibuka</p>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] uppercase font-bold text-gray-400 tracking-wide">Selesai</p>
            <p class="text-2xl font-black text-emerald-500 mt-1">{{ $myStats['done'] ?? 0 }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Sudah dikumpulkan</p>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
            <p class="text-[11px] uppercase font-bold text-gray-400 tracking-wide">Terlambat</p>
            <p class="text-2xl font-black text-rose-500 mt-1">{{ $myStats['late'] ?? 0 }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Melewati deadline</p>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- PENGELOMPOKAN --}}
    {{-- ========================================================= --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-black text-sm text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-folder-tree text-amber-500"></i> Pengelompokan
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300">
                    {{ ($myGroups ?? collect())->count() }}
                </span>
            </h2>
            <a href="{{ route('owner.lms.groups.create') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-violet-500 to-fuchsia-500 hover:opacity-90 transition shadow-sm">
                <i class="fas fa-plus"></i> Buat
            </a>
        </div>

        @if(($myGroups ?? collect())->count())
            <div class="{{ $gridCls }}">
                @foreach($myGroups as $g)
                    @php
                        $cover   = $resolveCover($g);
                        $creator = $g->relationLoaded('creator') ? $g->creator : null;

                    @endphp
                    <a href="{{ route('owner.lms.groups.show', $g->id) }}"
                       class="group block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        <div class="relative aspect-video bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 overflow-hidden">
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $g->name }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-folder-open text-4xl text-white/70"></i>
                                </div>
                            @endif
                            <span class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/50 text-white backdrop-blur-sm">
                                <i class="fas fa-layer-group"></i> {{ $g->items_count ?? 0 }}
                            </span>
                        </div>
                        <div class="p-3">
                            <p class="font-bold text-sm text-gray-900 dark:text-white truncate" title="{{ $g->name }}">{{ $g->name }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 mt-0.5 min-h-[2.4em]">
                                {{ $g->description ?: 'Tanpa deskripsi.' }}
                            </p>
                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <img src="{{ $resolveAvatar($creator) }}" alt="{{ $creator->name ?? 'U' }}" loading="lazy"
                                     class="w-7 h-7 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 truncate">{{ $creator->name ?? 'Tidak diketahui' }}</p>
                                    <p class="text-[9px] font-semibold text-violet-600 dark:text-violet-400 truncate uppercase tracking-wide">
                                        <i class="fas fa-shield-alt"></i> {{ $resolveRole($creator) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
                <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                <p class="mt-2 text-sm font-bold text-gray-700 dark:text-gray-300">Belum ada pengelompokan</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Mulai dengan membuat grup pertama kamu.</p>
                <a href="{{ route('owner.lms.groups.create') }}"
                   class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-violet-500 to-fuchsia-500">
                    <i class="fas fa-plus"></i> Buat Sekarang
                </a>
            </div>
        @endif
    </section>

    {{-- ========================================================= --}}
    {{-- DEADLINE TERDEKAT --}}
    {{-- ========================================================= --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-black text-sm text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-fire text-amber-500"></i> Deadline Terdekat
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300">
                    {{ ($nextDeadlines ?? collect())->count() }}
                </span>
            </h2>
            <a href="{{ route('owner.lms.tasks') }}" class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline">
                Lihat Semua →
            </a>
        </div>

        @php $hasDeadline = false; @endphp
        @foreach(($nextDeadlines ?? []) as $a) @if($a->item ?? null) @php $hasDeadline = true; @endphp @break @endif @endforeach

        @if($hasDeadline)
            <div class="{{ $gridCls }}">
                @foreach($nextDeadlines as $a)
                    @php $it = $a->item ?? null; @endphp
                    @if(!$it) @continue @endif
                    @php
                        $cover    = $resolveCover($it);
                        $creator  = $it->creator ?? $it->user ?? null;
                        $deadline = $it->deadline_at ?? null;
                    @endphp
                    <a href="{{ route('owner.lms.items.show', $it->id) }}"
                       class="group block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden">
                        <div class="relative aspect-video bg-gradient-to-br from-amber-400 via-orange-500 to-rose-500 overflow-hidden">
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $it->title }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-hourglass-half text-4xl text-white/80"></i>
                                </div>
                            @endif
                            <span class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/90 dark:bg-gray-900/90 text-violet-600 dark:text-violet-300 backdrop-blur-sm uppercase">
                                {{ $it->type ?? 'TUGAS' }}
                            </span>
                            @if($deadline)
                                <span class="absolute bottom-2 left-2 right-2 text-[10px] font-bold px-2 py-1 rounded-lg bg-black/60 text-white backdrop-blur-sm truncate">
                                    <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($deadline)->isoFormat('D MMM · HH:mm') }} WIB
                                </span>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="font-bold text-sm text-gray-900 dark:text-white truncate" title="{{ $it->title }}">{{ $it->title }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 mt-0.5 min-h-[2.4em]">
                                {{ $it->description ?: 'Tanpa deskripsi.' }}
                            </p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 truncate">
                                <i class="fas fa-folder"></i> {{ $it->group->name ?? '-' }}
                            </p>
                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <img src="{{ $resolveAvatar($creator) }}" alt="{{ $creator->name ?? 'U' }}" loading="lazy"
                                     class="w-7 h-7 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 truncate">{{ $creator->name ?? 'Tidak diketahui' }}</p>
                                    <p class="text-[9px] font-semibold text-violet-600 dark:text-violet-400 truncate uppercase tracking-wide">
                                        <i class="fas fa-shield-alt"></i> {{ $resolveRole($creator) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
                <i class="fas fa-check-circle text-3xl text-emerald-500"></i>
                <p class="mt-2 text-sm font-bold text-gray-700 dark:text-gray-300">Tidak ada deadline aktif</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Semua tugas beres!</p>
            </div>
        @endif
    </section>

    {{-- ========================================================= --}}
    {{-- PERLU REVIEW --}}
    {{-- ========================================================= --}}
    @if(isset($pendingReview) && $pendingReview->count() > 0)
    <section>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-black text-sm text-gray-900 dark:text-white">
                    <i class="fas fa-inbox text-cyan-500"></i> Perlu Review
                </h2>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500 text-white">{{ $pendingReview->count() }}</span>
            </div>
            <div class="p-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($pendingReview as $s)
                    <a href="{{ route('owner.lms.submissions.show', $s->id) }}"
                       class="block p-3 rounded-xl bg-gray-50 dark:bg-gray-900 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition-colors border border-transparent hover:border-cyan-200 dark:hover:border-cyan-800">
                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $s->item->title ?? '-' }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            <i class="fas fa-user"></i> {{ $s->user->name ?? '-' }} · {{ optional($s->submitted_at)->diffForHumans() }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>
@endsection
@extends('layouts.roles.owner')

@section('title', 'Manajemen Poin - UNICODE')

@push('styles')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-in { animation: fadeSlideUp .28s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay: .04s; }
    .anim-in-2 { animation-delay: .08s; }
    .anim-in-3 { animation-delay: .12s; }

    .row-hoverable { transition: background-color .15s ease; }
    .row-hoverable:hover { background-color: rgba(234, 179, 8, .05); }
    .dark .row-hoverable:hover { background-color: rgba(234, 179, 8, .08); }

    .focus-ring:focus-visible {
        outline: 2px solid rgb(234, 179, 8);
        outline-offset: 2px;
        border-radius: .5rem;
    }

    .tier-pill {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .25rem .625rem; border-radius: 9999px;
        font-size: .6875rem; font-weight: 600; letter-spacing: .01em;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
@php
    /* ── Klasifikasi tier — INLINE, tidak bergantung model ── */
    $tiers = [
        ['min' => 200, 'label' => 'Legendary', 'icon' => 'fa-crown',        'class' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300'],
        ['min' => 100, 'label' => 'Gold',      'icon' => 'fa-star',         'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        ['min' =>  50, 'label' => 'Silver',    'icon' => 'fa-gem',          'class' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300'],
        ['min' =>  20, 'label' => 'Bronze',    'icon' => 'fa-medal',        'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
    ];

    $tierOf = function (int $balance) use ($tiers) {
        foreach ($tiers as $t) {
            if ($balance >= $t['min']) return $t;
        }
        return ['label' => 'Newbie', 'icon' => 'fa-seedling', 'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'];
    };

    $totalMembers = ($points instanceof \Illuminate\Pagination\LengthAwarePaginator)
        ? $points->total()
        : $points->count();

    $totalPoints = collect($points->items() ?? $points)->sum('balance');
    $topTierCount = collect($points->items() ?? $points)->filter(fn ($p) => ($p->balance ?? 0) >= 100)->count();
@endphp

<div class="p-4 md:p-6 max-w-[1600px] mx-auto">

    {{-- ═══════════ BREADCRUMB ═══════════ --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}"
           class="hover:text-amber-600 dark:hover:text-amber-400 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-home text-[11px]" aria-hidden="true"></i>
            <span>Keuangan</span>
        </a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium">Poin Anggota</span>
    </nav>

    @include('owner.financial._partials.submenu', ['active' => 'points'])

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-6 mt-5 gap-4 anim-in">
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-star text-white text-sm" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Poin Anggota</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-11.5">
                Reward points seluruh anggota aktif — dihitung dari kehadiran, tugas, dan kontribusi.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('owner.financial.points.leaderboard') }}"
               class="focus-ring inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-sm font-semibold shadow-sm hover:shadow transition-all">
                <i class="fas fa-trophy text-xs" aria-hidden="true"></i>
                Leaderboard
            </a>
        </div>
    </div>

    {{-- ═══════════ RINGKASAN KARTU ═══════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Anggota</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($totalMembers, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">dalam peringkat</p>
        </div>

        <div class="anim-in anim-in-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Poin</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <i class="fas fa-coins text-amber-600 dark:text-amber-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 tracking-tight tabular-nums">
                {{ number_format($totalPoints, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">akumulasi poin</p>
        </div>

        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">High Achiever</span>
                <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <i class="fas fa-crown text-violet-600 dark:text-violet-400 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-violet-600 dark:text-violet-400 tracking-tight">{{ number_format($topTierCount, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">poin ≥ 100</p>
        </div>

        <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sumber Data</span>
                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-700/50 flex items-center justify-center">
                    <i class="fas fa-database text-slate-600 dark:text-slate-300 text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 tracking-tight truncate" title="{{ $source ?? 'unknown' }}">
                {{ $source ?? 'unknown' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">terdeteksi otomatis</p>
        </div>
    </div>

    {{-- ═══════════ TABEL ═══════════ --}}
    <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <caption class="sr-only">Peringkat poin anggota</caption>
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">#</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Poin</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($points as $index => $point)
                        @php
                            $balance = (int) ($point->balance ?? 0);
                            $tier    = $tierOf($balance);
                            $user    = $point->user ?? $point;
                            $rank    = method_exists($points, 'firstItem') && $points->firstItem()
                                ? $points->firstItem() + $index
                                : $index + 1;
                        @endphp
                        <tr class="row-hoverable">
                            {{-- Rank --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 text-white text-xs font-bold shadow-sm">🥇</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 text-white text-xs font-bold shadow-sm">🥈</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-br from-orange-400 to-amber-600 text-white text-xs font-bold shadow-sm">🥉</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold tabular-nums">{{ $rank }}</span>
                                @endif
                            </td>

                            {{-- Anggota --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-200 to-orange-300 dark:from-amber-700 dark:to-orange-800 flex items-center justify-center text-[11px] font-bold text-amber-900 dark:text-amber-100 shrink-0">
                                        {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $user->name ?? 'Tanpa nama' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $user->nim ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Poin --}}
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="text-lg font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                    {{ number_format($balance, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Tier --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="tier-pill {{ $tier['class'] }}">
                                    <i class="fas {{ $tier['icon'] }}" aria-hidden="true"></i>
                                    {{ $tier['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-16 text-center">
                                <div class="inline-flex flex-col items-center max-w-md">
                                    <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4">
                                        <i class="fas fa-star text-2xl text-amber-400 dark:text-amber-500" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">Belum ada data poin</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Poin akan muncul di sini setelah anggota menyelesaikan aktivitas yang direkam sistem
                                        (kehadiran, tugas, atau kontribusi lain).
                                    </p>
                                    @if(($source ?? '') === 'kosong')
                                        <div class="text-xs text-left bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 text-amber-800 dark:text-amber-300 w-full">
                                            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                                            <strong>Info teknis:</strong> sistem tidak menemukan tabel/model sumber poin.
                                            Periksa apakah tabel <code class="font-mono bg-white/60 dark:bg-black/20 px-1 rounded">point_accounts</code>,
                                            <code class="font-mono bg-white/60 dark:bg-black/20 px-1 rounded">points</code>, atau
                                            <code class="font-mono bg-white/60 dark:bg-black/20 px-1 rounded">point_histories</code> sudah ada.
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($points instanceof \Illuminate\Pagination\LengthAwarePaginator && $points->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                {{ $points->withQueryString()->links() }}
            </div>
        @endif

        @if($points->count() > 0)
            <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/10 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>
                    Menampilkan
                    <strong class="text-gray-700 dark:text-gray-300">
                        {{ $points instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($points->firstItem() ?? 0) . '–' . ($points->lastItem() ?? 0) : '1' }}
                    </strong>
                    dari
                    <strong class="text-gray-700 dark:text-gray-300">{{ number_format($totalMembers, 0, ',', '.') }}</strong> anggota
                </span>
                <span class="hidden sm:inline">
                    <i class="fas fa-shield-check text-[10px] mr-1" aria-hidden="true"></i>
                    Data live dari server
                </span>
            </div>
        @endif
    </div>
</div>
@endsection
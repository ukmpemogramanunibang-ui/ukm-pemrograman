@extends('layouts.roles.owner')

@section('title', 'Leaderboard Poin - UNICODE')

@push('styles')
<style>
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shine {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }
    .anim-in { animation: fadeSlideUp .35s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay: .05s; }
    .anim-in-2 { animation-delay: .10s; }
    .anim-in-3 { animation-delay: .15s; }

    .podium-card {
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .podium-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -8px rgba(0,0,0,.15);
    }
    .podium-card::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(110deg, transparent 40%, rgba(255,255,255,.15) 50%, transparent 60%);
        background-size: 200% 100%;
        opacity: 0;
        pointer-events: none;
    }
    .podium-card:hover::before {
        opacity: 1;
        animation: shine 1.2s ease;
    }

    .row-hoverable { transition: background-color .15s ease; }
    .row-hoverable:hover { background-color: rgba(234, 179, 8, .05); }
    .dark .row-hoverable:hover { background-color: rgba(234, 179, 8, .08); }

    .focus-ring:focus-visible {
        outline: 2px solid rgb(234, 179, 8);
        outline-offset: 2px;
        border-radius: .5rem;
    }
</style>
@endpush

@section('content')
@php
    $tiers = [
        ['min' => 200, 'label' => 'Legendary', 'icon' => 'fa-crown',  'class' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-300'],
        ['min' => 100, 'label' => 'Gold',      'icon' => 'fa-star',   'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'],
        ['min' =>  50, 'label' => 'Silver',    'icon' => 'fa-gem',    'class' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300'],
        ['min' =>  20, 'label' => 'Bronze',    'icon' => 'fa-medal',  'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
    ];
    $tierOf = function (int $b) use ($tiers) {
        foreach ($tiers as $t) if ($b >= $t['min']) return $t;
        return ['label' => 'Newbie', 'icon' => 'fa-seedling', 'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'];
    };

    $items  = collect($leaderboard ?? $points ?? []);
    $top3   = $items->take(3);
    $rest   = $items->slice(3);
    $total  = $items->sum(fn ($i) => (int) ($i->balance ?? 0));
@endphp

<div class="p-4 md:p-6 max-w-[1600px] mx-auto">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="flex items-center gap-3 mb-6 anim-in">
        <a href="{{ route('owner.financial.points.index') }}"
           class="focus-ring inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:border-gray-300 transition-all"
           aria-label="Kembali ke Manajemen Poin">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-trophy text-white text-sm" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Leaderboard Poin</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-11.5 mt-0.5">
                Peringkat anggota berdasarkan total poin yang terkumpul.
            </p>
        </div>
    </div>

    @if($items->isEmpty())
        {{-- ═══════════ EMPTY STATE ═══════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-16 text-center shadow-sm">
            <div class="inline-flex flex-col items-center max-w-md">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-trophy text-3xl text-amber-500 dark:text-amber-400" aria-hidden="true"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">Leaderboard masih kosong</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    Belum ada anggota dengan poin. Peringkat akan muncul setelah aktivitas anggota terekam.
                </p>
                <a href="{{ route('owner.financial.points.index') }}"
                   class="focus-ring inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition-colors">
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                    Kembali
                </a>
            </div>
        </div>
    @else
        {{-- ═══════════ PODIUM TOP 3 ═══════════ --}}
        @if($top3->isNotEmpty())
            @php
                $podiumOrder = [1, 0, 2];  // Perak, Emas, Perunggu
                $podiumMeta = [
                    0 => ['rank' => 1, 'medal' => '🥇', 'gradient' => 'from-amber-400 via-yellow-400 to-amber-500',   'ring' => 'ring-amber-300 dark:ring-amber-600', 'height' => 'h-32', 'delay' => 'anim-in-2'],
                    1 => ['rank' => 2, 'medal' => '🥈', 'gradient' => 'from-slate-300 via-slate-400 to-slate-500',   'ring' => 'ring-slate-300 dark:ring-slate-600', 'height' => 'h-24', 'delay' => 'anim-in-1'],
                    2 => ['rank' => 3, 'medal' => '🥉', 'gradient' => 'from-orange-400 via-amber-500 to-orange-600', 'ring' => 'ring-orange-300 dark:ring-orange-600', 'height' => 'h-20', 'delay' => 'anim-in-3'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 items-end">
                @foreach($podiumOrder as $srcIdx)
                    @if($top3->has($srcIdx))
                        @php
                            $item = $top3->get($srcIdx);
                            $user = $item->user ?? $item;
                            $bal  = (int) ($item->balance ?? 0);
                            $tier = $tierOf($bal);
                            $meta = $podiumMeta[$srcIdx];
                        @endphp
                        <div class="podium-card anim-in {{ $meta['delay'] }} bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm">
                            {{-- Badge Rank --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $srcIdx === 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : ($srcIdx === 1 ? 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300') }}">
                                    <span class="text-base leading-none">{{ $meta['medal'] }}</span>
                                    Peringkat {{ $meta['rank'] }}
                                </div>
                                <span class="text-2xl" aria-hidden="true">{{ $meta['medal'] }}</span>
                            </div>

                            {{-- Avatar --}}
                            <div class="flex flex-col items-center text-center mb-4">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br {{ $meta['gradient'] }} flex items-center justify-center text-white text-2xl font-bold shadow-lg ring-4 {{ $meta['ring'] }} mb-3">
                                    {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base truncate max-w-full" title="{{ $user->name ?? '-' }}">
                                    {{ $user->name ?? 'Tanpa nama' }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                                    {{ $user->nim ?? '—' }}
                                </p>
                            </div>

                            {{-- Poin --}}
                            <div class="text-center mb-3">
                                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 tabular-nums leading-none">
                                    {{ number_format($bal, 0, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-1.5">Total Poin</p>
                            </div>

                            {{-- Tier pill --}}
                            <div class="flex justify-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $tier['class'] }} text-[11px] font-semibold">
                                    <i class="fas {{ $tier['icon'] }} text-[10px]" aria-hidden="true"></i>
                                    {{ $tier['label'] }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- ═══════════ TABEL PERINGKAT 4+ ═══════════ --}}
        @if($rest->isNotEmpty())
            <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-list-ol text-amber-500 mr-2" aria-hidden="true"></i>
                        Peringkat 4 ke Atas
                    </h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format($rest->count(), 0, ',', '.') }} anggota
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-900/40">
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                                <th scope="col" class="px-4 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Poin</th>
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tier</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach($rest as $index => $item)
                                @php
                                    $bal  = (int) ($item->balance ?? 0);
                                    $tier = $tierOf($bal);
                                    $user = $item->user ?? $item;
                                    $rank = $index + 4;
                                @endphp
                                <tr class="row-hoverable">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold tabular-nums">
                                            {{ $rank }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-200 to-orange-300 dark:from-amber-700 dark:to-orange-800 flex items-center justify-center text-[11px] font-bold text-amber-900 dark:text-amber-100 shrink-0">
                                                {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $user->name ?? 'Tanpa nama' }}</p>
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">{{ $user->nim ?? '—' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <span class="font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                            {{ number_format($bal, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $tier['class'] }} text-[11px] font-semibold">
                                            <i class="fas {{ $tier['icon'] }} text-[10px]" aria-hidden="true"></i>
                                            {{ $tier['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ═══════════ FOOTER INFO ═══════════ --}}
        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span>
                Total poin terakumulasi:
                <strong class="text-amber-600 dark:text-amber-400 tabular-nums">{{ number_format($total, 0, ',', '.') }}</strong>
            </span>
            @if(!empty($source))
                <span>
                    <i class="fas fa-database text-[10px] mr-1" aria-hidden="true"></i>
                    Sumber: <span class="font-mono">{{ $source }}</span>
                </span>
            @endif
        </div>
    @endif
</div>
@endsection
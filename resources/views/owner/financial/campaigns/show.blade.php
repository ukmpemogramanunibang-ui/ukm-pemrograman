@extends('layouts.roles.owner')

@section('title', 'Detail Kampanye - ' . ($campaign->title ?? 'UNICODE'))

@push('styles')
<style>
    @keyframes fadeSlideUp { from{opacity:0;transform:translateY(6px);} to{opacity:1;transform:translateY(0);} }
    @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
    @keyframes pulseRing { 0%{box-shadow:0 0 0 0 rgba(239,68,68,.5);} 70%{box-shadow:0 0 0 6px rgba(239,68,68,0);} 100%{box-shadow:0 0 0 0 rgba(239,68,68,0);} }
    .anim-in { animation: fadeSlideUp .3s cubic-bezier(.22,.61,.36,1) both; }
    .anim-in-1 { animation-delay:.04s; } .anim-in-2 { animation-delay:.08s; } .anim-in-3 { animation-delay:.12s; } .anim-in-4 { animation-delay:.16s; }

    .focus-ring:focus-visible { outline: 2px solid rgb(147,51,234); outline-offset: 2px; border-radius: .5rem; }

    .progress-track { position:relative; height:10px; border-radius:9999px; overflow:hidden; background: rgb(229,231,235); }
    .dark .progress-track { background: rgb(55,65,81); }
    .progress-fill { height:100%; border-radius:9999px; transition: width .8s cubic-bezier(.22,.61,.36,1); position:relative; overflow:hidden; }
    .progress-fill::after { content:''; position:absolute; inset:0; background: linear-gradient(90deg, transparent 40%, rgba(255,255,255,.35) 50%, transparent 60%); background-size: 200% 100%; animation: shimmer 2.2s ease-in-out infinite; }

    .status-pill, .category-pill { display:inline-flex; align-items:center; gap:.375rem; padding:.3125rem .75rem; border-radius:9999px; font-size:.75rem; font-weight:600; letter-spacing:.01em; white-space:nowrap; line-height:1; }
    .category-pill { border-radius:.5rem; font-weight:500; }
    .status-pill i, .category-pill i { font-size:.625rem; }

    .info-tile { background: rgb(249,250,251); border-radius: .75rem; padding: .875rem 1rem; transition: background .15s ease; }
    .dark .info-tile { background: rgb(17,24,39); }
    .info-tile:hover { background: rgb(243,244,246); }
    .dark .info-tile:hover { background: rgb(31,41,55); }

    .btn-primary { background: linear-gradient(145deg, rgb(147,51,234), rgb(126,34,206)); color:#fff; padding:.6875rem 1.25rem; border-radius:.625rem; font-weight:600; font-size:.8125rem; border:none; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.5rem; box-shadow: 0 4px 12px rgba(147,51,234,.25); text-decoration:none; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(147,51,234,.35); }

    .btn-secondary { background: rgb(243,244,246); color: rgb(31,41,55); padding:.6875rem 1.125rem; border-radius:.625rem; font-weight:500; font-size:.8125rem; border:none; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.5rem; text-decoration:none; }
    .dark .btn-secondary { background: rgb(55,65,81); color: rgb(243,244,246); }
    .btn-secondary:hover { background: rgb(229,231,235); }
    .dark .btn-secondary:hover { background: rgb(75,85,99); }

    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:2.25rem; height:2.25rem; border-radius:.5rem; transition:all .15s ease; }
    .btn-icon:hover { transform: scale(1.05); }

    .urgent-dot { width:8px; height:8px; border-radius:50%; background: rgb(239,68,68); animation: pulseRing 1.6s ease-out infinite; display:inline-block; }
</style>
@endpush

@section('content')
@php
    $categoryMap = [
        'id_card'    => ['label' => 'ID Card',    'icon' => 'fa-id-card',            'class' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300'],
        'event'      => ['label' => 'Event',      'icon' => 'fa-calendar-check',     'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'reuni'      => ['label' => 'Reuni',      'icon' => 'fa-users',              'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
        'seragam'    => ['label' => 'Seragam',    'icon' => 'fa-shirt',              'class' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300'],
        'study_tour' => ['label' => 'Study Tour', 'icon' => 'fa-plane',              'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'],
        'donasi'     => ['label' => 'Donasi',     'icon' => 'fa-hand-holding-heart', 'class' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300'],
        'lainnya'    => ['label' => 'Lainnya',    'icon' => 'fa-circle-dot',         'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
    ];
    $statusMap = [
        'draft'     => ['label' => 'Draft',      'icon' => 'fa-pen',          'class' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'],
        'active'    => ['label' => 'Berjalan',   'icon' => 'fa-circle-play',  'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'],
        'closed'    => ['label' => 'Selesai',    'icon' => 'fa-circle-check', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'fa-circle-xmark', 'class' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300'],
    ];

    $catKey = strtolower((string) ($campaign->category ?? ''));
    $catMeta = $categoryMap[$catKey] ?? $categoryMap['lainnya'];
    $stKey = strtolower((string) ($campaign->status ?? 'draft'));
    $stMeta = $statusMap[$stKey] ?? $statusMap['draft'];

    // Statistik
    $amount    = (float) ($campaign->amount ?? 0);
    $target    = (float) ($campaign->target_amount    ?? $amount);
    $collected = (float) ($campaign->collected_amount ?? 0);
    $progress  = $target > 0 ? min(100, round(($collected / $target) * 100, 1)) : 0;

    $participants = (int) ($campaign->participants_count ?? 0);
    $expected     = (int) ($campaign->expected_participants ?? 0);
    $partPct      = $expected > 0 ? min(100, round(($participants / $expected) * 100)) : 0;

    $remaining = max(0, $target - $collected);

    // Tanggal
    $fmtDate = fn ($v) => $v ? (is_string($v) ? \Carbon\Carbon::parse($v) : $v) : null;
    $startDate = $fmtDate($campaign->start_date);
    $endDate   = $fmtDate($campaign->end_date);
    $dueDate   = $fmtDate($campaign->due_date);
    $isPast    = $dueDate && $dueDate->isPast();
    $daysLeft  = $dueDate ? now()->diffInDays($dueDate, false) : null;
    $isUrgent  = $dueDate && !$isPast && $daysLeft !== null && $daysLeft <= 7 && $stKey === 'active';

    $isRequired = (bool) ($campaign->is_required ?? false);

    // Target breakdown
    $targetData = is_array($campaign->target_data ?? null) ? $campaign->target_data : [];
    $targetType = $campaign->target_type ?? 'all';
    $targetLabel = match ($targetType) {
        'all'        => 'Semua anggota aktif',
        'generation' => 'Generasi tertentu · ' . count($targetData['generation_ids'] ?? []) . ' generasi',
        'specific'   => 'Individu tertentu · ' . count($targetData['user_ids'] ?? []) . ' anggota',
        default      => 'Tidak ditentukan',
    };
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-6xl mx-auto">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('owner.financial.dashboard') }}" class="hover:text-purple-600 dark:hover:text-purple-400 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-home text-[11px]" aria-hidden="true"></i><span>Keuangan</span>
        </a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <a href="{{ route('owner.financial.campaigns.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Kampanye</a>
        <i class="fas fa-chevron-right mx-2 text-[9px] text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-700 dark:text-gray-200 font-medium truncate max-w-xs">{{ $campaign->title ?? 'Detail' }}</span>
    </nav>

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6 anim-in">
        <div class="flex items-start gap-3 flex-1 min-w-0">
            <a href="{{ route('owner.financial.campaigns.index') }}"
               class="focus-ring mt-1 inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-all shrink-0"
               aria-label="Kembali">
                <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="category-pill {{ $catMeta['class'] }}">
                        <i class="fas {{ $catMeta['icon'] }}" aria-hidden="true"></i>{{ $catMeta['label'] }}
                    </span>
                    <span class="status-pill {{ $stMeta['class'] }}">
                        <i class="fas {{ $stMeta['icon'] }}" aria-hidden="true"></i>{{ $stMeta['label'] }}
                    </span>
                    @if($isRequired)
                        <span class="status-pill bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300">
                            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>WAJIB
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight break-words">
                    {{ $campaign->title ?? 'Tanpa judul' }}
                </h1>
                @if($campaign->description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 max-w-2xl">{{ $campaign->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-2 shrink-0">
            @can('update', $campaign)
                <a href="{{ route('owner.financial.campaigns.edit', $campaign) }}" class="btn-primary">
                    <i class="fas fa-pen text-xs" aria-hidden="true"></i>Edit
                </a>
            @endcan

            @if(($campaign->status ?? '') === 'draft')
                @can('activate', $campaign)
                    <form action="{{ route('owner.financial.campaigns.activate', $campaign) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-secondary text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30" title="Aktifkan">
                            <i class="fas fa-circle-play text-xs" aria-hidden="true"></i>Aktifkan
                        </button>
                    </form>
                @endcan
            @endif

            @if(($campaign->status ?? '') === 'active')
                @can('close', $campaign)
                    <form action="{{ route('owner.financial.campaigns.close', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Tutup kampanye ini? Setelah ditutup, tidak bisa menerima pembayaran baru.')">
                        @csrf
                        <button type="submit" class="btn-secondary text-blue-700 dark:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30" title="Tutup">
                            <i class="fas fa-circle-check text-xs" aria-hidden="true"></i>Tutup
                        </button>
                    </form>
                @endcan
            @endif

            @if(!in_array($campaign->status ?? '', ['cancelled', 'closed'], true))
                @can('cancel', $campaign)
                    <form action="{{ route('owner.financial.campaigns.cancel', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan kampanye ini? Tindakan akan dicatat di audit log.')">
                        @csrf
                        <button type="submit" class="btn-icon text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30" title="Batalkan" aria-label="Batalkan kampanye">
                            <i class="fas fa-circle-xmark text-sm" aria-hidden="true"></i>
                        </button>
                    </form>
                @endcan
            @endif
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div role="status" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 mb-4">
            <i class="fas fa-circle-check mt-0.5 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-sm">{{ session('success') }}</p>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup" class="text-emerald-600 dark:text-emerald-400">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div role="alert" class="anim-in flex items-start gap-3 px-4 py-3 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 mb-4">
            <i class="fas fa-circle-exclamation mt-0.5 shrink-0" aria-hidden="true"></i>
            <p class="flex-1 text-sm">{{ session('error') }}</p>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup" class="text-rose-600 dark:text-rose-400">&times;</button>
        </div>
    @endif

    {{-- ═══════════ KARTU PROGRES UTAMA ═══════════ --}}
    <div class="anim-in anim-in-1 bg-gradient-to-br {{ $stKey === 'active' ? 'from-purple-500 via-fuchsia-500 to-purple-600' : ($stKey === 'closed' ? 'from-slate-500 via-slate-600 to-slate-700' : ($stKey === 'cancelled' ? 'from-rose-500 via-rose-600 to-rose-700' : 'from-purple-400 to-purple-500')) }} rounded-2xl shadow-lg p-6 mb-5 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.4) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,.3) 0, transparent 40%);"></div>

        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-5 items-center">
            <div class="md:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-wider opacity-90 mb-1">Dana Terkumpul</p>
                <p class="text-3xl md:text-4xl font-bold tabular-nums leading-none mb-3">
                    Rp {{ number_format($collected, 0, ',', '.') }}
                </p>
                <div class="bg-white/20 rounded-full h-2.5 mb-2 overflow-hidden relative">
                    <div class="bg-white rounded-full h-full transition-all duration-700 shadow-sm" style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                    <span><strong>{{ $progress }}%</strong> tercapai</span>
                    <span class="opacity-80">dari target Rp {{ number_format($target, 0, ',', '.') }}</span>
                    @if($remaining > 0)
                        <span class="opacity-80">· sisa Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>

            <div class="md:text-right space-y-2">
                <div class="bg-white/15 backdrop-blur-sm rounded-lg px-3 py-2">
                    <p class="text-[11px] uppercase tracking-wider opacity-80">Nominal / Anggota</p>
                    <p class="text-base font-bold tabular-nums">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                </div>
                @if($dueDate)
                    <div class="bg-white/15 backdrop-blur-sm rounded-lg px-3 py-2">
                        <p class="text-[11px] uppercase tracking-wider opacity-80">
                            @if($isPast && $stKey === 'active') Terlambat
                            @elseif($isUrgent) Sisa Waktu
                            @else Jatuh Tempo
                            @endif
                        </p>
                        <p class="text-sm font-bold">
                            @if($isPast && $stKey === 'active')
                                <span class="urgent-dot mr-1" aria-hidden="true"></span>{{ $dueDate->translatedFormat('d M Y') }}
                            @elseif($isUrgent)
                                {{ $daysLeft }} hari lagi
                            @else
                                {{ $dueDate->translatedFormat('d M Y') }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════ GRID INFORMASI ═══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ─────── KOLOM KIRI: Informasi ─────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Jadwal --}}
            <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="fas fa-calendar-days text-purple-600 dark:text-purple-400 text-xs" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Jadwal</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="info-tile">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Mulai</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                            {{ $startDate ? $startDate->translatedFormat('d M Y') : '—' }}
                        </p>
                    </div>
                    <div class="info-tile">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Berakhir</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                            {{ $endDate ? $endDate->translatedFormat('d M Y') : 'Tanpa batas' }}
                        </p>
                    </div>
                    <div class="info-tile">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Jatuh Tempo</p>
                        <p class="text-sm font-bold tabular-nums {{ $isPast && $stKey === 'active' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $dueDate ? $dueDate->translatedFormat('d M Y') : '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Target & Peserta --}}
            <div class="anim-in anim-in-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center">
                        <i class="fas fa-users text-sky-600 dark:text-sky-400 text-xs" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Target & Peserta</h3>
                </div>

                <div class="space-y-4">
                    <div class="info-tile">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Sasaran</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $targetLabel }}</p>
                        @if($targetType === 'generation' && !empty($targetData['generation_ids']))
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($targetData['generation_ids'] as $gid)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-[10px] font-mono">
                                        Gen #{{ $gid }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if($expected > 0)
                        <div>
                            <div class="flex items-baseline justify-between mb-2">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Peserta berpartisipasi</p>
                                <p class="text-xs font-bold text-sky-600 dark:text-sky-400 tabular-nums">{{ $partPct }}%</p>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill bg-gradient-to-r from-sky-400 to-sky-500" style="width: {{ $partPct }}%"></div>
                            </div>
                            <div class="flex items-baseline justify-between mt-1.5 text-xs">
                                <span class="font-bold text-sky-600 dark:text-sky-400 tabular-nums">{{ number_format($participants, 0, ',', '.') }}</span>
                                <span class="text-gray-500 dark:text-gray-400 tabular-nums">/ {{ number_format($expected, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="info-tile">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Peserta</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                {{ number_format($participants, 0, ',', '.') }} anggota
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Deskripsi --}}
            @if($campaign->description)
                <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                            <i class="fas fa-align-left text-amber-600 dark:text-amber-400 text-xs" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Deskripsi</h3>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $campaign->description }}</p>
                </div>
            @endif

            {{-- Audit Trail --}}
            <div class="anim-in anim-in-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-clock-rotate-left text-gray-500" aria-hidden="true"></i>
                        Jejak Waktu
                    </h3>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <li class="px-5 py-3 flex items-start gap-3 text-sm">
                        <i class="fas fa-circle text-[6px] text-emerald-500 mt-1.5" aria-hidden="true"></i>
                        <div class="flex-1">
                            <p class="text-gray-700 dark:text-gray-300">Dibuat</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $campaign->created_at?->translatedFormat('d M Y H:i') ?? '—' }}
                                @if($campaign->creator ?? null)
                                    · oleh {{ $campaign->creator->name ?? 'Sistem' }}
                                @endif
                            </p>
                        </div>
                    </li>
                    @if($campaign->updated_at && $campaign->updated_at != $campaign->created_at)
                        <li class="px-5 py-3 flex items-start gap-3 text-sm">
                            <i class="fas fa-circle text-[6px] text-blue-500 mt-1.5" aria-hidden="true"></i>
                            <div class="flex-1">
                                <p class="text-gray-700 dark:text-gray-300">Terakhir diubah</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $campaign->updated_at->translatedFormat('d M Y H:i') }}
                                    · {{ $campaign->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- ─────── KOLOM KANAN: Ringkasan & Aksi ─────── --}}
        <div class="space-y-5">

            {{-- Ringkasan Cepat --}}
            <div class="anim-in anim-in-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Ringkasan</h3>
                <dl class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Status</dt>
                        <dd><span class="status-pill {{ $stMeta['class'] }}"><i class="fas {{ $stMeta['icon'] }}" aria-hidden="true"></i>{{ $stMeta['label'] }}</span></dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Kategori</dt>
                        <dd><span class="category-pill {{ $catMeta['class'] }}"><i class="fas {{ $catMeta['icon'] }}" aria-hidden="true"></i>{{ $catMeta['label'] }}</span></dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Wajib</dt>
                        <dd class="text-sm font-semibold {{ $isRequired ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $isRequired ? 'Ya' : 'Tidak' }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Nominal</dt>
                        <dd class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">Rp {{ number_format($amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Terkumpul</dt>
                        <dd class="text-sm font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">Rp {{ number_format($collected, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Kekurangan</dt>
                        <dd class="text-sm font-bold tabular-nums {{ $remaining > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400' }}">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Panduan --}}
            <div class="anim-in anim-in-4 bg-gradient-to-br from-purple-50 to-fuchsia-50 dark:from-purple-900/20 dark:to-fuchsia-900/20 border border-purple-100 dark:border-purple-900/40 rounded-xl p-5">
                <div class="flex items-start gap-2 mb-3">
                    <i class="fas fa-lightbulb text-purple-600 dark:text-purple-400 mt-0.5" aria-hidden="true"></i>
                    <h3 class="text-xs font-bold text-purple-900 dark:text-purple-200 uppercase tracking-wider">Panduan Cepat</h3>
                </div>
                <ul class="space-y-2 text-xs text-purple-800 dark:text-purple-300">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[5px] mt-1.5 shrink-0" aria-hidden="true"></i>
                        <span><strong>Draft</strong> — belum diumumkan, bebas diedit.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[5px] mt-1.5 shrink-0" aria-hidden="true"></i>
                        <span><strong>Aktifkan</strong> setelah pengumuman ke anggota target.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[5px] mt-1.5 shrink-0" aria-hidden="true"></i>
                        <span><strong>Tutup</strong> setelah semua pembayaran diverifikasi.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[5px] mt-1.5 shrink-0" aria-hidden="true"></i>
                        <span><strong>Batalkan</strong> hanya bila benar-benar tidak dilanjutkan.</span>
                    </li>
                </ul>
            </div>

            {{-- Danger Zone --}}
            @can('delete', $campaign)
                <div class="anim-in anim-in-4 bg-white dark:bg-gray-800 rounded-xl border border-rose-100 dark:border-rose-900/40 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                        Zona Berbahaya
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        Menghapus kampanye akan menghilangkan seluruh data terkait. Tindakan tidak dapat dibatalkan.
                    </p>
                    <form action="{{ route('owner.financial.campaigns.destroy', $campaign) }}" method="POST"
                          onsubmit="return confirm('Hapus kampanye \"{{ addslashes($campaign->title) }}\"? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors">
                            <i class="fas fa-trash text-[10px]" aria-hidden="true"></i>Hapus Kampanye Permanen
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection
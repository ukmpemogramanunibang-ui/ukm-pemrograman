@extends('layouts.roles.owner')

@section('title', 'Monitoring Tugas & Materi')

@push('styles')
<style>
:root {
    --lms-violet: #8b5cf6;
    --lms-green:  #10b981;
    --lms-red:    #ef4444;
    --lms-blue:   #3b82f6;
    --lms-amber:  #f59e0b;
    --lms-cyan:   #06b6d4;
}

.mn-page { padding: 1rem 1.25rem 3rem; max-width: 1400px; margin: 0 auto; }

/* ============== HERO ============== */
.mn-hero {
    background:
        radial-gradient(circle at 88% 12%, rgba(255,255,255,.18), transparent 30%),
        linear-gradient(135deg, #0ea5e9 0%, #6366f1 50%, #8b5cf6 100%);
    color: #fff; border-radius: 20px;
    padding: 1.5rem 1.75rem;
    display: grid; grid-template-columns: minmax(0,1fr) auto;
    gap: 1rem; align-items: center;
    box-shadow: 0 18px 45px rgba(99,102,241,.25);
}
@media (max-width: 820px) { .mn-hero { grid-template-columns: 1fr; } }
.mn-hero-title { font-size: clamp(1.3rem, 2.4vw, 1.75rem); font-weight: 950; margin: .35rem 0 .25rem; }
.mn-hero p { font-size: .82rem; color: rgba(255,255,255,.85); }
.mn-hero-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .7rem; border-radius: 999px;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.22);
    font-size: .66rem; font-weight: 850;
}
.mn-hero-chip.a { text-decoration: none; color: #fff; transition: background .15s; }
.mn-hero-chip.a:hover { background: rgba(255,255,255,.25); }
.mn-hero-actions { display: flex; gap: .5rem; flex-wrap: wrap; }

/* ============== KPI ============== */
.mn-kpi-grid {
    margin-top: 1rem;
    display: grid; grid-template-columns: repeat(6, minmax(0,1fr)); gap: .65rem;
}
@media (max-width: 1080px) { .mn-kpi-grid { grid-template-columns: repeat(3, minmax(0,1fr)); } }
@media (max-width: 640px)  { .mn-kpi-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }

.mn-kpi {
    padding: .9rem 1rem; border-radius: 12px;
    background: #fff; border: 1px solid rgba(15,23,42,.06);
    box-shadow: 0 1px 3px rgba(15,23,42,.06);
    position: relative; overflow: hidden;
}
.dark .mn-kpi { background: #0f172a; border-color: rgba(148,163,184,.15); }
.mn-kpi::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    background: var(--lms-violet);
}
.mn-kpi[data-tone="danger"]::before  { background: var(--lms-red); }
.mn-kpi[data-tone="warning"]::before { background: var(--lms-amber); }
.mn-kpi[data-tone="info"]::before    { background: var(--lms-blue); }
.mn-kpi[data-tone="success"]::before { background: var(--lms-green); }
.mn-kpi[data-tone="cyan"]::before    { background: var(--lms-cyan); }
.mn-kpi .lbl {
    font-size: .62rem; font-weight: 850; text-transform: uppercase;
    color: #64748b; letter-spacing: .07em;
}
.mn-kpi .val {
    margin-top: .35rem; font-size: 1.4rem; font-weight: 950;
    color: #0f172a; line-height: 1;
}
.dark .mn-kpi .val { color: #f8fafc; }

/* ============== FILTER ============== */
.mn-filter {
    margin-top: 1rem; background: #fff;
    border: 1px solid rgba(15,23,42,.06); border-radius: 12px;
    padding: .85rem 1rem;
}
.dark .mn-filter { background: #0f172a; border-color: rgba(148,163,184,.15); }
.mn-filter-form {
    display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: .5rem;
}
@media (max-width: 980px) { .mn-filter-form { grid-template-columns: 1fr 1fr; } }
@media (max-width: 640px) { .mn-filter-form { grid-template-columns: 1fr; } }
.mn-filter input, .mn-filter select {
    padding: .6rem .85rem; border-radius: 10px;
    border: 1px solid rgba(15,23,42,.1); font-size: .8rem;
    background: #f8fafc; width: 100%;
}
.dark .mn-filter input, .dark .mn-filter select {
    background: #111827; color: #f8fafc; border-color: rgba(148,163,184,.15);
}
.mn-filter button {
    padding: .6rem 1.1rem; border-radius: 10px;
    background: linear-gradient(135deg, #0ea5e9, #6366f1);
    color: #fff; border: 0; cursor: pointer;
    font-size: .8rem; font-weight: 850;
    display: inline-flex; align-items: center; gap: .4rem;
    justify-content: center;
}
.mn-filter .btn-reset {
    background: #f1f5f9; color: #475569;
}
.dark .mn-filter .btn-reset {
    background: #1e293b; color: #94a3b8;
}

/* ============== ITEM CARD ============== */
.mn-list { margin-top: 1rem; display: grid; gap: .85rem; }
.mn-item {
    background: #fff; border: 1px solid rgba(15,23,42,.06);
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,.06);
    transition: transform .2s, box-shadow .2s;
}
.mn-item:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(15,23,42,.08); }
.dark .mn-item { background: #0f172a; border-color: rgba(148,163,184,.15); }

.mn-item-head {
    padding: 1rem 1.15rem .75rem;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: .75rem; flex-wrap: wrap;
}
.mn-item-title {
    font-size: 1rem; font-weight: 950; color: #0f172a;
    margin: 0; display: flex; align-items: center; gap: .4rem; flex-wrap: wrap;
}
.dark .mn-item-title { color: #f8fafc; }
.mn-item-meta {
    display: flex; flex-wrap: wrap; gap: .35rem .8rem;
    font-size: .7rem; color: #64748b; margin-top: .35rem;
}
.mn-item-meta i { margin-right: .2rem; }

/* badge */
.mn-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem .55rem; border-radius: 999px;
    font-size: .6rem; font-weight: 900; text-transform: uppercase;
    letter-spacing: .04em;
}
.mn-badge.task     { background: rgba(139,92,246,.12); color: #7c3aed; }
.mn-badge.material { background: rgba(16,185,129,.12); color: #059669; }
.mn-badge.pub      { background: rgba(16,185,129,.12); color: #059669; }
.mn-badge.draft    { background: rgba(100,116,139,.12); color: #475569; }
.mn-badge.overdue  { background: rgba(239,68,68,.12); color: #dc2626; }
.mn-badge.warn     { background: rgba(245,158,11,.12); color: #b45309; }
.dark .mn-badge.task     { background: rgba(139,92,246,.2); color: #c4b5fd; }
.dark .mn-badge.material { background: rgba(16,185,129,.2); color: #6ee7b7; }
.dark .mn-badge.pub      { background: rgba(16,185,129,.2); color: #6ee7b7; }
.dark .mn-badge.draft    { background: rgba(100,116,139,.2); color: #cbd5e1; }
.dark .mn-badge.overdue  { background: rgba(239,68,68,.2); color: #fca5a5; }
.dark .mn-badge.warn     { background: rgba(245,158,11,.2); color: #fcd34d; }

/* progress */
.mn-item-progress { padding: 0 1.15rem 1rem; }
.mn-progress-header {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .72rem; color: #64748b; margin-bottom: .35rem;
}
.mn-progress-header .pct {
    font-weight: 950; color: var(--lms-violet); font-size: .9rem;
}
.mn-progress {
    height: 10px; border-radius: 999px;
    background: rgba(15,23,42,.06); overflow: hidden;
    display: flex;
}
.dark .mn-progress { background: rgba(148,163,184,.15); }
.mn-progress-seg { height: 100%; transition: width .4s; }
.mn-progress-seg.done { background: linear-gradient(90deg, #10b981, #059669); }

.mn-legend {
    display: flex; gap: .85rem; flex-wrap: wrap;
    margin-top: .55rem; font-size: .62rem; color: #64748b;
}
.mn-legend span { display: inline-flex; align-items: center; gap: .3rem; }
.mn-legend i { font-size: .55rem; }

/* stats */
.mn-item-stats {
    padding: .85rem 1.15rem 1rem;
    display: grid; grid-template-columns: repeat(5, minmax(0,1fr));
    gap: .5rem;
    border-top: 1px solid rgba(15,23,42,.06);
    background: #f8fafc;
}
.dark .mn-item-stats { background: #111827; border-color: rgba(148,163,184,.15); }
@media (max-width: 640px) { .mn-item-stats { grid-template-columns: repeat(3,1fr); } }

.mn-stat { text-align: center; }
.mn-stat .n { font-size: 1.1rem; font-weight: 950; line-height: 1; }
.mn-stat .n.violet { color: #7c3aed; }
.mn-stat .n.blue   { color: #2563eb; }
.mn-stat .n.amber  { color: #d97706; }
.mn-stat .n.green  { color: #059669; }
.mn-stat .n.red    { color: #dc2626; }
.mn-stat .l {
    font-size: .6rem; color: #64748b; text-transform: uppercase;
    font-weight: 850; margin-top: .25rem; letter-spacing: .05em;
}

/* actions */
.mn-actions {
    padding: .75rem 1.15rem;
    display: flex; gap: .5rem; flex-wrap: wrap;
    border-top: 1px solid rgba(15,23,42,.06);
}
.dark .mn-actions { border-color: rgba(148,163,184,.15); }
.mn-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .5rem .9rem; border-radius: 9px;
    font-size: .72rem; font-weight: 850; text-decoration: none;
    border: 0; cursor: pointer; transition: all .15s;
}
.mn-btn-ghost { background: rgba(139,92,246,.1); color: #7c3aed; }
.mn-btn-ghost:hover { background: rgba(139,92,246,.2); }
.mn-btn-warn { background: rgba(245,158,11,.12); color: #b45309; }
.mn-btn-warn:hover { background: rgba(245,158,11,.22); }
.mn-btn-danger { background: rgba(239,68,68,.1); color: #dc2626; }
.mn-btn-danger:hover { background: rgba(239,68,68,.2); }
.dark .mn-btn-ghost  { background: rgba(139,92,246,.2); color: #c4b5fd; }
.dark .mn-btn-warn   { background: rgba(245,158,11,.2); color: #fcd34d; }
.dark .mn-btn-danger { background: rgba(239,68,68,.2); color: #fca5a5; }

/* empty */
.mn-empty {
    text-align: center; padding: 3rem 1rem;
    background: #fff; border-radius: 14px;
    border: 1px solid rgba(15,23,42,.06);
    color: #64748b;
}
.dark .mn-empty { background: #0f172a; border-color: rgba(148,163,184,.15); }
.mn-empty i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: .75rem; }

/* ============== MODAL PENERIMA ============== */
.mn-modal-bg {
    position: fixed; inset: 0; z-index: 3000;
    background: rgba(8,12,26,.72);
    backdrop-filter: blur(4px);
    display: none; align-items: center; justify-content: center;
    padding: 1rem;
}
.mn-modal-bg.open { display: flex; }
.mn-modal {
    background: #fff; border-radius: 16px;
    max-width: 780px; width: 100%;
    max-height: 90vh; overflow: hidden;
    display: flex; flex-direction: column;
    box-shadow: 0 24px 60px rgba(0,0,0,.4);
}
.dark .mn-modal { background: #0f172a; }
.mn-modal-head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(15,23,42,.08);
    display: flex; justify-content: space-between; align-items: center; gap: 1rem;
}
.dark .mn-modal-head { border-color: rgba(148,163,184,.15); }
.mn-modal-head h3 { margin: 0; font-size: 1rem; font-weight: 900; }
.mn-modal-head p  { margin: .15rem 0 0; font-size: .72rem; color: #64748b; }
.mn-modal-close {
    width: 34px; height: 34px; border-radius: 8px;
    background: #f1f5f9; color: #475569;
    display: flex; align-items: center; justify-content: center;
    border: 0; cursor: pointer;
}
.dark .mn-modal-close { background: #1e293b; color: #cbd5e1; }
.mn-modal-tabs {
    display: flex; gap: .35rem; padding: .75rem 1.25rem .35rem;
    border-bottom: 1px solid rgba(15,23,42,.08); overflow-x: auto;
}
.dark .mn-modal-tabs { border-color: rgba(148,163,184,.15); }
.mn-tab {
    padding: .4rem .85rem; border-radius: 999px;
    font-size: .7rem; font-weight: 850;
    background: #f1f5f9; color: #64748b; border: 0; cursor: pointer;
    white-space: nowrap;
}
.dark .mn-tab { background: #1e293b; color: #94a3b8; }
.mn-tab.active { background: #8b5cf6; color: #fff; }
.mn-modal-body {
    flex: 1; overflow-y: auto; padding: .5rem 0;
}
.mn-user-row {
    display: flex; align-items: center; gap: .75rem;
    padding: .7rem 1.25rem; border-bottom: 1px solid rgba(15,23,42,.05);
}
.dark .mn-user-row { border-color: rgba(148,163,184,.08); }
.mn-user-row:hover { background: #f8fafc; }
.dark .mn-user-row:hover { background: #111827; }
.mn-user-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #8b5cf6, #d946ef);
    color: #fff; font-weight: 900; font-size: .75rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.mn-user-info { flex: 1; min-width: 0; }
.mn-user-info .nm { font-size: .82rem; font-weight: 850; color: #0f172a; }
.dark .mn-user-info .nm { color: #f8fafc; }
.mn-user-info .sub { font-size: .68rem; color: #64748b; margin-top: .1rem; }
.mn-user-right { text-align: right; font-size: .68rem; color: #64748b; }
.mn-user-right .badge {
    display: inline-block; padding: .18rem .5rem; border-radius: 999px;
    font-size: .6rem; font-weight: 900; text-transform: uppercase;
}
.badge-done    { background: rgba(16,185,129,.12); color: #059669; }
.badge-review  { background: rgba(6,182,212,.12);  color: #0891b2; }
.badge-late    { background: rgba(239,68,68,.12);  color: #dc2626; }
.badge-progress{ background: rgba(245,158,11,.12); color: #b45309; }
.badge-missing { background: rgba(100,116,139,.12);color: #475569; }
.dark .badge-done    { background: rgba(16,185,129,.2); color: #6ee7b7; }
.dark .badge-review  { background: rgba(6,182,212,.2);  color: #67e8f9; }
.dark .badge-late    { background: rgba(239,68,68,.2);  color: #fca5a5; }
.dark .badge-progress{ background: rgba(245,158,11,.2); color: #fcd34d; }
.dark .badge-missing { background: rgba(100,116,139,.2);color: #cbd5e1; }

/* anti-tamper indicator */
.mn-tamper {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .62rem; color: #059669; font-weight: 850;
}
.mn-tamper.warn { color: #d97706; }
.mn-tamper.danger { color: #dc2626; }
</style>
@endpush

@section('content')

<div class="mn-page">

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    <section class="mn-hero">
        <div>
            <span class="mn-hero-chip"><i class="fas fa-satellite-dish"></i> LMS COMMAND CENTER</span>
            <h1 class="mn-hero-title">Monitoring Tugas &amp; Materi</h1>
            <p>Data dihitung server-side · waktu realtime dari clock server · timestamp tidak dapat dimanipulasi client.</p>
        </div>
        <div class="mn-hero-actions">
            <a href="{{ route('owner.lms.all') }}" class="mn-hero-chip a">
                <i class="fas fa-list"></i> Semua Item
            </a>
            <a href="{{ route('owner.lms.tasks') }}" class="mn-hero-chip a">
                <i class="fas fa-tasks"></i> Tugas
            </a>
            <a href="{{ route('owner.lms.materials') ?? '#' }}" class="mn-hero-chip a">
                <i class="fas fa-book-open"></i> Materi
            </a>
            <button type="button" onclick="window.location.reload()" class="mn-hero-chip a" style="cursor:pointer;font-family:inherit;">
                <i class="fas fa-rotate"></i> Refresh
            </button>
        </div>
    </section>

    {{-- ═══════════════════ KPI ═══════════════════ --}}
    <section class="mn-kpi-grid">
        <div class="mn-kpi" data-tone="info">
            <div class="lbl">Total Item</div>
            <div class="val">{{ number_format($summary['total_items']) }}</div>
        </div>
        <div class="mn-kpi" data-tone="info">
            <div class="lbl">Total Penerima</div>
            <div class="val">{{ number_format($summary['total_recipients']) }}</div>
        </div>
        <div class="mn-kpi" data-tone="success">
            <div class="lbl">Selesai</div>
            <div class="val">{{ number_format($summary['total_done']) }}</div>
        </div>
        <div class="mn-kpi" data-tone="danger">
            <div class="lbl">Terlambat</div>
            <div class="val">{{ number_format($summary['total_late']) }}</div>
        </div>
        <div class="mn-kpi" data-tone="warning">
            <div class="lbl">Belum Selesai</div>
            <div class="val">{{ number_format($summary['total_missing']) }}</div>
        </div>
        <div class="mn-kpi" data-tone="cyan">
            <div class="lbl">Completion</div>
            <div class="val">{{ $summary['avg_completion'] }}%</div>
        </div>
    </section>

    {{-- ═══════════════════ FILTER ═══════════════════ --}}
    <section class="mn-filter">
        <form method="GET" class="mn-filter-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul / deskripsi...">

            <select name="group_id">
                <option value="">Semua Pengelompokan</option>
                @foreach($groups ?? [] as $g)
                    <option value="{{ $g->id }}" @selected(request('group_id') == $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>

            <select name="type">
                <option value="">Semua Tipe</option>
                <option value="task"     @selected(request('type') === 'task')>Tugas</option>
                <option value="material" @selected(request('type') === 'material')>Materi</option>
            </select>

            <select name="status">
                <option value="">Semua Status</option>
                <option value="draft"       @selected(request('status') === 'draft')>Draft</option>
                <option value="published"   @selected(request('status') === 'published')>Dipublikasikan</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>Sedang Berjalan</option>
                <option value="overdue"     @selected(request('status') === 'overdue')>Melewati Deadline</option>
            </select>

            <button type="submit"><i class="fas fa-filter"></i> Terapkan</button>

            @if(request()->hasAny(['q','group_id','type','status','only_mine']))
                <a href="{{ route('owner.lms.monitoring') }}" class="btn-reset" style="text-decoration:none;padding:.6rem 1rem;border-radius:10px;font-size:.8rem;font-weight:850;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>
    </section>

    {{-- ═══════════════════ LIST ═══════════════════ --}}
    @if($items->isEmpty())
        <div class="mn-empty" style="margin-top:1rem;">
            <i class="fas fa-chart-bar"></i>
            <p style="font-weight:850;">Tidak ada item yang cocok dengan filter.</p>
            <p style="font-size:.78rem;margin-top:.25rem;">Coba ubah filter atau buat item baru.</p>
        </div>
    @else
        <div class="mn-list">
            @foreach($items as $item)
                @php
                    // ═════════════════════════════════════════════════
                    // Semua angka di bawah ini datang dari accessor
                    // LmsItem::getMetricAttribute() — server-side.
                    // Tidak dapat dimanipulasi oleh client.
                    // ═════════════════════════════════════════════════
                    $m       = $item->metric;
                    $total   = max((int) ($m['total'] ?? 0), 1);
                    $done    = (int) ($m['completed'] ?? 0);
                    $submit  = (int) ($m['submitted'] ?? 0);
                    $started = (int) ($m['started'] ?? 0);
                    $viewed  = (int) ($m['viewed'] ?? 0);
                    $late    = (int) ($m['late'] ?? 0);
                    $missing = (int) ($m['missing'] ?? max(0, $total - $done));

                    $donePct = (int) round($done / $total * 100);

                    $overdue     = $item->deadline_at && now()->gt($item->deadline_at);
                    $published   = (bool) $item->published_at;
                    $isTask      = $item->type === 'task';

                    $deadlineIso = optional($item->deadline_at)->toIso8601String();
                    $remaining   = $item->deadline_at ? now()->diffInSeconds($item->deadline_at, false) : null;
                @endphp

                <div class="mn-item" data-item-id="{{ $item->id }}">

                    {{-- HEAD --}}
                    <div class="mn-item-head">
                        <div style="flex:1;min-width:0;">
                            <h3 class="mn-item-title">
                                <span class="mn-badge {{ $isTask ? 'task' : 'material' }}">
                                    <i class="fas {{ $isTask ? 'fa-clipboard-list' : 'fa-book-open' }}"></i>
                                    {{ $isTask ? 'TUGAS' : 'MATERI' }}
                                </span>

                                @if($overdue)
                                    <span class="mn-badge overdue"><i class="fas fa-exclamation-triangle"></i> OVERDUE</span>
                                @elseif(! $published)
                                    <span class="mn-badge draft"><i class="fas fa-file-pen"></i> DRAFT</span>
                                @else
                                    <span class="mn-badge pub"><i class="fas fa-circle-check"></i> AKTIF</span>
                                @endif

                                {{ $item->title }}
                            </h3>

                            <div class="mn-item-meta">
                                <span><i class="fas fa-folder"></i> {{ $item->group->name ?? '-' }}</span>
                                <span><i class="fas fa-user"></i> {{ $item->creator->name ?? '-' }}</span>
                                @if($item->deadline_at)
                                    <span style="color: {{ $overdue ? '#dc2626' : '#64748b' }};">
                                        <i class="fas fa-clock"></i>
                                        {{ $item->deadline_at->isoFormat('D MMM Y · HH:mm') }} WIB
                                    </span>
                                @else
                                    <span><i class="fas fa-infinity"></i> Tanpa batas waktu</span>
                                @endif
                                @if($item->published_at)
                                    <span><i class="fas fa-rocket"></i> Dipublikasikan {{ $item->published_at->isoFormat('D MMM Y') }}</span>
                                @endif
                            </div>
                        </div>

                        @if($deadlineIso && ! $overdue)
                            <div class="mn-badge warn" data-countdown="{{ $deadlineIso }}">
                                <i class="fas fa-hourglass-half"></i>
                                <span class="cd-text">—</span>
                            </div>
                        @endif
                    </div>

                    {{-- PROGRESS --}}
                    <div class="mn-item-progress">
                        <div class="mn-progress-header">
                            <span><i class="fas fa-chart-simple"></i> Progress Penyelesaian</span>
                            <span class="pct">{{ $donePct }}%</span>
                        </div>
                        <div class="mn-progress">
                            <div class="mn-progress-seg done" style="width: {{ $donePct }}%;"></div>
                        </div>

                        <div class="mn-legend">
                            <span><i class="fas fa-circle" style="color:#10b981;"></i> Selesai ({{ $done }})</span>
                            <span><i class="fas fa-circle" style="color:#06b6d4;"></i> Review ({{ max(0, $submit - $done) }})</span>
                            <span><i class="fas fa-circle" style="color:#f59e0b;"></i> Proses ({{ max(0, $started - $submit) }})</span>
                            <span><i class="fas fa-circle" style="color:#ef4444;"></i> Terlambat ({{ $late }})</span>
                            <span><i class="fas fa-circle" style="color:#94a3b8;"></i> Belum ({{ $missing }})</span>
                        </div>
                    </div>

                    {{-- STATS --}}
                    <div class="mn-item-stats">
                        <div class="mn-stat">
                            <div class="n violet">{{ $m['total'] }}</div>
                            <div class="l">Total</div>
                        </div>
                        <div class="mn-stat">
                            <div class="n blue">{{ $viewed }}</div>
                            <div class="l">Dilihat</div>
                        </div>
                        <div class="mn-stat">
                            <div class="n amber">{{ $started }}</div>
                            <div class="l">Dikerjakan</div>
                        </div>
                        <div class="mn-stat">
                            <div class="n green">{{ $submit }}</div>
                            <div class="l">Dikumpulkan</div>
                        </div>
                        <div class="mn-stat">
                            <div class="n red">{{ $late }}</div>
                            <div class="l">Terlambat</div>
                        </div>
                    </div>

                    {{-- ANTI-TAMPER FOOTER --}}
                    <div class="mn-actions" style="justify-content:space-between;">
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                            <button type="button"
                                    class="mn-btn mn-btn-ghost"
                                    data-view-recipients="{{ $item->id }}"
                                    data-item-title="{{ e($item->title) }}">
                                <i class="fas fa-users"></i> Penerima ({{ $m['total'] }})
                            </button>

                            <a href="{{ route('owner.lms.items.show', $item->id) }}" class="mn-btn mn-btn-ghost">
                                <i class="fas fa-eye"></i> Detail
                            </a>

                            @if($isTask)
                                <a href="{{ route('owner.lms.submissions.index', ['item_id' => $item->id]) }}" class="mn-btn mn-btn-ghost">
                                    <i class="fas fa-inbox"></i> Pengumpulan ({{ $submit }})
                                </a>
                            @endif

                            @if($missing > 0 && Route::has('owner.lms.items.remind'))
                                <button type="button"
                                        class="mn-btn mn-btn-warn"
                                        data-remind="{{ $item->id }}"
                                        data-missing="{{ $missing }}">
                                    <i class="fas fa-bell"></i> Ingatkan {{ $missing }} orang
                                </button>
                            @endif
                        </div>

                        <div class="mn-tamper" title="Semua timestamp dihitung dari server. Client tidak dapat mengubah.">
                            <i class="fas fa-shield-halved"></i> Server-verified
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div style="margin-top:1.5rem;">
            {{ $items->links() }}
        </div>
    @endif
</div>

{{-- ═══════════════════ MODAL PENERIMA ═══════════════════ --}}
<div id="recipientsModal" class="mn-modal-bg">
    <div class="mn-modal">
        <div class="mn-modal-head">
            <div style="min-width:0;">
                <h3 id="recipientsTitle">Penerima</h3>
                <p id="recipientsSub">—</p>
            </div>
            <button type="button" class="mn-modal-close" onclick="closeRecipientsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mn-modal-tabs">
            <button type="button" class="mn-tab active" data-filter="all">
                Semua <span class="cnt"></span>
            </button>
            <button type="button" class="mn-tab" data-filter="done">
                <i class="fas fa-check"></i> Selesai
            </button>
            <button type="button" class="mn-tab" data-filter="review">
                <i class="fas fa-eye"></i> Review
            </button>
            <button type="button" class="mn-tab" data-filter="late">
                <i class="fas fa-clock"></i> Terlambat
            </button>
            <button type="button" class="mn-tab" data-filter="missing">
                <i class="fas fa-circle-xmark"></i> Belum
            </button>
        </div>

        <div class="mn-modal-body" id="recipientsBody">
            <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:.8rem;">
                <i class="fas fa-circle-notch fa-spin"></i> Memuat data...
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       1. COUNTDOWN per card (dari server ISO, bukan client)
    ═══════════════════════════════════════════════════════════ */
    function updateCountdowns() {
        document.querySelectorAll('[data-countdown]').forEach((el) => {
            const iso = el.dataset.countdown;
            const target = new Date(iso).getTime();
            const diff = target - Date.now();
            const txt = el.querySelector('.cd-text');
            if (!txt) return;

            if (diff <= 0) {
                el.classList.remove('warn');
                el.classList.add('overdue');
                el.innerHTML = '<i class="fas fa-lock"></i> Deadline lewat';
                return;
            }

            const t = Math.floor(diff / 1000);
            const d = Math.floor(t / 86400);
            const h = Math.floor((t % 86400) / 3600);
            const m = Math.floor((t % 3600) / 60);
            const s = t % 60;

            const parts = [];
            if (d > 0) parts.push(d + 'h');
            parts.push(String(h).padStart(2, '0') + 'j');
            parts.push(String(m).padStart(2, '0') + 'm');
            if (d === 0) parts.push(String(s).padStart(2, '0') + 'd');
            txt.textContent = parts.join(' ');
        });
    }
    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    /* ═══════════════════════════════════════════════════════════
       2. REMINDER — endpoint sungguhan
    ═══════════════════════════════════════════════════════════ */
    document.querySelectorAll('[data-remind]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const itemId = btn.dataset.remind;
            const missing = btn.dataset.missing;

            if (!confirm(`Kirim reminder ke ${missing} penerima yang belum selesai?`)) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengirim...';

            try {
                const res = await fetch(`/owner/lms/items/${itemId}/remind`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal');

                btn.innerHTML = `<i class="fas fa-check"></i> Terkirim (${data.sent || missing})`;
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fas fa-bell"></i> Ingatkan ${missing} orang`;
                }, 3500);
            } catch (e) {
                alert('Gagal mengirim reminder: ' + e.message);
                btn.disabled = false;
                btn.innerHTML = `<i class="fas fa-bell"></i> Ingatkan ${missing} orang`;
            }
        });
    });

    /* ═══════════════════════════════════════════════════════════
       3. PER-USER MODAL — drill down penerima
    ═══════════════════════════════════════════════════════════ */
    const modal       = document.getElementById('recipientsModal');
    const modalTitle  = document.getElementById('recipientsTitle');
    const modalSub    = document.getElementById('recipientsSub');
    const modalBody   = document.getElementById('recipientsBody');
    const tabs        = document.querySelectorAll('.mn-tab[data-filter]');

    let currentItemId = null;
    let currentFilter = 'all';

    function humanDate(iso) {
        if (!iso) return '—';
        const d = new Date(iso);
        return d.toLocaleString('id-ID', {
            timeZone: 'Asia/Jakarta',
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
        }) + ' WIB';
    }

    function statusBadge(r) {
        if (r.is_late) return '<span class="badge badge-late">Terlambat</span>';
        if (r.submitted_at && r.graded_at) return '<span class="badge badge-done">Selesai</span>';
        if (r.submitted_at) return '<span class="badge badge-review">Review</span>';
        if (r.started_at)   return '<span class="badge badge-progress">Dikerjakan</span>';
        if (r.viewed_at)    return '<span class="badge badge-progress">Dilihat</span>';
        return '<span class="badge badge-missing">Belum</span>';
    }

    function renderRows(recipients) {
        if (!recipients.length) {
            modalBody.innerHTML = `
                <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:.8rem;">
                    <i class="fas fa-inbox" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i>
                    Tidak ada penerima pada filter ini.
                </div>`;
            return;
        }

        modalBody.innerHTML = recipients.map(r => {
            const initial = (r.name || 'U').split(/\s+/).slice(0,2).map(p => p[0]?.toUpperCase()).join('');
            const dur = r.work_duration_sec
                ? Math.round(r.work_duration_sec / 60) + ' mnt kerja'
                : '';

            return `
                <div class="mn-user-row">
                    <div class="mn-user-avatar">${initial || 'U'}</div>
                    <div class="mn-user-info">
                        <div class="nm">${escapeHtml(r.name)}</div>
                        <div class="sub">
                            ${r.nim ? 'NIM ' + escapeHtml(r.nim) : ''}
                            ${r.email ? ' · ' + escapeHtml(r.email) : ''}
                            ${dur ? ' · ' + dur : ''}
                        </div>
                    </div>
                    <div class="mn-user-right">
                        ${statusBadge(r)}
                        <div style="margin-top:.2rem;">${humanDate(r.submitted_at || r.started_at || r.viewed_at)}</div>
                    </div>
                </div>`;
        }).join('');
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    async function loadRecipients(itemId, filter) {
        currentItemId = itemId;
        currentFilter = filter || 'all';

        modalBody.innerHTML = `
            <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:.8rem;">
                <i class="fas fa-circle-notch fa-spin"></i> Memuat data...
            </div>`;

        try {
            const res = await fetch(`/owner/lms/items/${itemId}/recipients?filter=${currentFilter}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Gagal memuat data');
            const data = await res.json();

            modalTitle.textContent = data.item.title || 'Penerima';
            const m = data.item.metric || {};
            modalSub.textContent = `${data.count} dari ${m.total || 0} penerima · filter: ${currentFilter}`;

            renderRows(data.recipients || []);
        } catch (e) {
            modalBody.innerHTML = `
                <div style="text-align:center;padding:2rem;color:#ef4444;font-size:.8rem;">
                    <i class="fas fa-triangle-exclamation" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>
                    ${escapeHtml(e.message)}
                </div>`;
        }
    }

    document.querySelectorAll('[data-view-recipients]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.viewRecipients;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            tabs.forEach(t => t.classList.toggle('active', t.dataset.filter === 'all'));
            loadRecipients(id, 'all');
        });
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            if (currentItemId) loadRecipients(currentItemId, tab.dataset.filter);
        });
    });

    modal.addEventListener('click', e => {
        if (e.target === modal) closeRecipientsModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeRecipientsModal();
    });

    window.closeRecipientsModal = function () {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    };
})();
</script>
@endpush
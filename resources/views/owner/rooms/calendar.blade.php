@extends('layouts.roles.owner')

@section('title', 'Kalender Ruangan Pintar – Owner')

@push('styles')
<style>
/* ============================================================
   ROOM COMMAND CALENDAR v2 — FINAL
   ============================================================ */
.rc-page {
    --rc-bg:        #f1f5f9;
    --rc-card:      #ffffff;
    --rc-soft:      #f8fafc;
    --rc-border:    rgba(15,23,42,.08);
    --rc-text:      #0f172a;
    --rc-muted:     #64748b;
    --rc-blue:      #2563eb;
    --rc-cyan:      #06b6d4;
    --rc-green:     #10b981;
    --rc-red:       #dc2626;
    --rc-yellow:    #eab308;
    --rc-orange:    #f97316;
    --rc-slate:     #64748b;
    --rc-indigo:    #4f46e5;
    --rc-radius:    14px;
    --rc-radius-lg: 20px;
    --rc-shadow-sm: 0 1px 3px rgba(15,23,42,.06);
    --rc-shadow-md: 0 10px 30px rgba(15,23,42,.08);
    --rc-shadow-lg: 0 20px 60px rgba(15,23,42,.12);
    --rc-ease:      cubic-bezier(.4, 0, .2, 1);
    --rc-ease-out:  cubic-bezier(.16, 1, .3, 1);
}
.dark .rc-page {
    --rc-bg:     #0b1220;
    --rc-card:   #0f172a;
    --rc-soft:   #111827;
    --rc-border: rgba(148,163,184,.16);
    --rc-text:   #f8fafc;
    --rc-muted:  #94a3b8;
    --rc-shadow-sm: 0 1px 3px rgba(0,0,0,.35);
    --rc-shadow-md: 0 10px 30px rgba(0,0,0,.35);
    --rc-shadow-lg: 0 20px 60px rgba(0,0,0,.5);
}
.rc-page * { box-sizing: border-box; }
.rc-page {
    min-height: 100vh;
    padding: 1rem 1.15rem 2rem;
    background: var(--rc-bg);
    color: var(--rc-text);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
}

/* HERO */
.rc-hero {
    position: relative; overflow: hidden;
    padding: 1.25rem 1.4rem;
    border-radius: var(--rc-radius-lg);
    color: #fff;
    background:
        radial-gradient(circle at 88% 12%, rgba(255,255,255,.22), transparent 30%),
        radial-gradient(circle at 10% 100%, rgba(255,255,255,.12), transparent 30%),
        linear-gradient(135deg, #0891b2 0%, #2563eb 48%, #4f46e5 100%);
    box-shadow: 0 18px 45px rgba(37,99,235,.22);
    display: grid;
    grid-template-columns: minmax(0,1fr) auto;
    gap: 1rem; align-items: center;
}
.rc-hero::after {
    content: ""; position: absolute; right: -90px; bottom: -120px;
    width: 280px; height: 280px; border-radius: 50%;
    background: rgba(255,255,255,.06);
    box-shadow: 0 0 0 40px rgba(255,255,255,.04);
    pointer-events: none;
}
.rc-hero-inner { position: relative; z-index: 1; min-width: 0; }
.rc-eyebrow {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .35rem .7rem; border-radius: 999px;
    background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.2);
    font-size: .65rem; font-weight: 900;
    letter-spacing: .12em; text-transform: uppercase;
}
.rc-hero-title {
    margin: .55rem 0 .25rem;
    font-size: clamp(1.25rem, 2.4vw, 1.75rem);
    line-height: 1.1; font-weight: 950; letter-spacing: -.02em;
}
.rc-hero-desc {
    margin: 0; max-width: 640px;
    color: rgba(255,255,255,.82); font-size: .8rem; line-height: 1.6;
}
.rc-hero-meta { display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .75rem; }
.rc-hero-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .28rem .6rem; border-radius: 999px;
    background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.18);
    font-size: .66rem; font-weight: 800;
}
.rc-hero-chip i { font-size: .7rem; }

.rc-hero-actions {
    position: relative; z-index: 1;
    display: flex; flex-wrap: wrap; gap: .45rem;
    justify-content: flex-end; min-width: max-content;
}
.rc-action {
    display: inline-flex; align-items: center; gap: .45rem;
    min-height: 40px; padding: .55rem .85rem;
    border: 1px solid rgba(255,255,255,.24); border-radius: 11px;
    background: rgba(255,255,255,.12); color: #fff;
    text-decoration: none; font-size: .74rem; font-weight: 850;
    backdrop-filter: blur(8px);
    transition: transform .15s var(--rc-ease), background .15s var(--rc-ease);
    cursor: pointer;
}
.rc-action:hover { transform: translateY(-1px); background: rgba(255,255,255,.20); }
.rc-action:active { transform: scale(.97); }
.rc-action.primary { background: #fff; color: #0f172a; border-color: #fff; }
.rc-action.primary:hover { background: #f1f5f9; }

/* KPI */
.rc-kpi-grid {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: repeat(6, minmax(0,1fr));
    gap: .65rem;
}
.rc-kpi {
    padding: .85rem .9rem;
    border: 1px solid var(--rc-border);
    border-radius: var(--rc-radius);
    background: var(--rc-card);
    box-shadow: var(--rc-shadow-sm);
    transition: transform .2s var(--rc-ease), box-shadow .2s var(--rc-ease);
    position: relative; overflow: hidden;
}
.rc-kpi:hover { transform: translateY(-2px); box-shadow: var(--rc-shadow-md); }
.rc-kpi::before {
    content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    background: var(--rc-blue); opacity: .85;
}
.rc-kpi[data-tone="danger"]::before   { background: var(--rc-red); }
.rc-kpi[data-tone="warning"]::before  { background: var(--rc-yellow); }
.rc-kpi[data-tone="info"]::before     { background: var(--rc-blue); }
.rc-kpi[data-tone="success"]::before  { background: var(--rc-green); }
.rc-kpi[data-tone="conflict"]::before { background: var(--rc-orange); }
.rc-kpi .label {
    color: var(--rc-muted); font-size: .62rem; font-weight: 850;
    text-transform: uppercase; letter-spacing: .07em;
}
.rc-kpi .value {
    margin-top: .35rem; color: var(--rc-text);
    font-size: 1.45rem; line-height: 1; font-weight: 950;
    display: flex; align-items: center; justify-content: space-between; gap: .5rem;
}
.rc-kpi .value i { color: var(--rc-muted); font-size: .9rem; }
.rc-kpi .note { margin-top: .4rem; color: var(--rc-muted); font-size: .62rem; line-height: 1.4; }

/* FILTER */
.rc-filter {
    margin-top: .85rem;
    border: 1px solid var(--rc-border);
    border-radius: var(--rc-radius);
    background: var(--rc-card);
    box-shadow: var(--rc-shadow-sm);
    overflow: hidden;
    transition: box-shadow .2s var(--rc-ease);
}
.rc-filter:hover { box-shadow: var(--rc-shadow-md); }
.rc-filter-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: .6rem; padding: .7rem .95rem; cursor: pointer; user-select: none;
    transition: background .15s var(--rc-ease);
}
.rc-filter-head:hover { background: var(--rc-soft); }
.rc-filter-title { display: flex; align-items: center; gap: .5rem; font-size: .8rem; font-weight: 900; }
.rc-filter-title i { color: var(--rc-cyan); }
.rc-filter-count {
    display: none; padding: .08rem .5rem; border-radius: 999px;
    background: rgba(37,99,235,.12); color: var(--rc-blue);
    font-size: .6rem; font-weight: 900;
}
.rc-filter-count.show { display: inline-flex; }
.rc-filter-chev {
    width: 26px; height: 26px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    background: var(--rc-soft); color: var(--rc-muted);
    font-size: .7rem; transition: transform .3s var(--rc-ease);
}
.rc-filter.open .rc-filter-chev { transform: rotate(180deg); }
.rc-filter-body {
    max-height: 0; overflow: hidden; opacity: 0; padding: 0 .95rem;
    transition: max-height .4s var(--rc-ease), padding .3s var(--rc-ease), opacity .25s var(--rc-ease);
}
.rc-filter.open .rc-filter-body { max-height: 500px; padding: 0 .95rem .95rem; opacity: 1; }
.rc-filter-form {
    display: grid; grid-template-columns: repeat(5, minmax(0,1fr));
    gap: .5rem;
}
.rc-filter-form input, .rc-filter-form select {
    min-height: 38px; width: 100%; padding: .5rem .7rem;
    border: 1px solid var(--rc-border); border-radius: 10px;
    background: var(--rc-soft); color: var(--rc-text);
    font-size: .78rem; outline: none;
    transition: border-color .15s var(--rc-ease), box-shadow .15s var(--rc-ease);
}
.rc-filter-form input:focus, .rc-filter-form select:focus {
    border-color: var(--rc-cyan);
    box-shadow: 0 0 0 3px rgba(6,182,212,.15);
}
.rc-filter-form button[type="submit"] {
    min-height: 38px; border: 0; border-radius: 10px;
    background: linear-gradient(135deg, #06b6d4, #2563eb);
    color: #fff; font-weight: 850; font-size: .78rem; cursor: pointer;
    transition: transform .15s var(--rc-ease), box-shadow .15s var(--rc-ease);
}
.rc-filter-form button[type="submit"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(37,99,235,.25);
}
.rc-filter-reset {
    display: inline-flex; align-items: center; gap: .35rem;
    color: var(--rc-muted); text-decoration: none;
    font-size: .72rem; font-weight: 800;
    padding: .35rem .6rem; border-radius: 8px;
    transition: background .15s var(--rc-ease);
}
.rc-filter-reset:hover { background: var(--rc-soft); color: var(--rc-blue); }
.rc-chips { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .65rem; }
.rc-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .25rem .55rem; border-radius: 999px;
    background: rgba(37,99,235,.1); color: var(--rc-blue);
    font-size: .66rem; font-weight: 850;
}
.rc-chip a { color: inherit; text-decoration: none; opacity: .7; }
.rc-chip a:hover { opacity: 1; }

/* VIEW TABS */
.rc-view-tabs {
    margin-top: 1rem;
    display: flex; flex-wrap: wrap; gap: .4rem;
    padding: .35rem; border-radius: 12px;
    background: var(--rc-card);
    border: 1px solid var(--rc-border);
    box-shadow: var(--rc-shadow-sm);
    width: fit-content;
}
.rc-tab {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem .85rem; border-radius: 9px;
    color: var(--rc-muted); font-size: .75rem; font-weight: 850;
    text-decoration: none;
    transition: background .15s var(--rc-ease), color .15s var(--rc-ease);
}
.rc-tab:hover { background: var(--rc-soft); color: var(--rc-text); }
.rc-tab.active {
    background: linear-gradient(135deg, var(--rc-blue), var(--rc-indigo));
    color: #fff;
    box-shadow: 0 8px 20px rgba(37,99,235,.25);
}

/* MAIN GRID */
.rc-grid {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 1rem; align-items: start;
}

/* CALENDAR */
.rc-cal {
    border: 1px solid var(--rc-border);
    border-radius: var(--rc-radius-lg);
    background: var(--rc-card);
    box-shadow: var(--rc-shadow-md);
    overflow: hidden;
}
.rc-cal-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: .8rem; padding: 1rem 1.15rem;
    color: #fff;
    background: linear-gradient(135deg, #0f766e 0%, #2563eb 48%, #4338ca 100%);
}
.rc-cal-head h2 {
    margin: 0; font-size: 1.15rem; font-weight: 950; line-height: 1.2;
    display: flex; align-items: center; gap: .5rem;
}
.rc-cal-head p { margin: .2rem 0 0; color: rgba(255,255,255,.82); font-size: .7rem; }
.rc-cal-head-actions { display: flex; align-items: center; gap: .35rem; flex-wrap: wrap; }
.rc-nav-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 .7rem;
    border-radius: 10px;
    color: #fff; text-decoration: none;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.22);
    font-size: .72rem; font-weight: 850;
    transition: background .15s var(--rc-ease), transform .15s var(--rc-ease);
    cursor: pointer;
}
.rc-nav-btn:hover { background: rgba(255,255,255,.24); transform: translateY(-1px); }
.rc-nav-btn:active { transform: scale(.95); }
.rc-nav-btn.wide { padding: 0 .9rem; gap: .4rem; }

/* Legend */
.rc-legend {
    display: flex; flex-wrap: wrap; gap: .5rem 1rem;
    padding: .7rem 1rem;
    border-bottom: 1px solid var(--rc-border);
    background: var(--rc-soft);
    font-size: .66rem;
}
.rc-legend-title {
    color: var(--rc-text); font-weight: 950;
    letter-spacing: .06em; text-transform: uppercase;
}
.rc-legend-item {
    display: inline-flex; align-items: center; gap: .35rem;
    color: var(--rc-muted); font-weight: 800;
}
.rc-legend-dot { width: 10px; height: 10px; border-radius: 50%; flex: 0 0 auto; }
.rc-dot-red    { background: var(--rc-red); }
.rc-dot-yellow { background: var(--rc-yellow); }
.rc-dot-blue   { background: var(--rc-blue); }
.rc-dot-green  { background: var(--rc-green); }
.rc-dot-slate  { background: var(--rc-slate); }

/* Day header */
.rc-day-head {
    display: grid; grid-template-columns: repeat(7, minmax(0,1fr));
    padding: .5rem .35rem;
    border-bottom: 1px solid var(--rc-border);
}
.rc-day-head > div {
    text-align: center; padding: .45rem 0;
    color: var(--rc-muted); font-size: .66rem;
    font-weight: 950; letter-spacing: .08em; text-transform: uppercase;
}
.rc-day-head > div.weekend { color: #d97706; }

/* Grid */
.rc-cal-grid {
    display: grid; grid-template-columns: repeat(7, minmax(0,1fr));
    gap: 1px; padding: 0 .35rem .35rem;
    background: var(--rc-border);
}
.rc-cell {
    position: relative; min-height: 132px;
    padding: .5rem .55rem;
    background: var(--rc-card);
    display: flex; flex-direction: column; gap: .3rem;
    transition: background .15s var(--rc-ease);
}
.rc-cell:hover { background: var(--rc-soft); }
.rc-cell.out-month { opacity: .5; background: var(--rc-soft); }
.rc-cell.today {
    outline: 2px solid var(--rc-green);
    outline-offset: -2px;
    background: rgba(16,185,129,.05);
}
.rc-date-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: .35rem; min-height: 26px;
}
.rc-date-num {
    width: 26px; height: 26px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 950;
    color: var(--rc-text);
}
.rc-cell.today .rc-date-num { color: #fff; background: var(--rc-green); }
.rc-day-state { color: var(--rc-muted); font-size: .55rem; font-weight: 850; }

.rc-bk-list { display: grid; gap: .28rem; }
.rc-bk {
    position: relative; display: block;
    padding: .38rem .5rem .4rem .55rem;
    border-radius: 8px; border: 1px solid transparent;
    text-decoration: none; color: inherit; overflow: hidden;
    transition: transform .15s var(--rc-ease), box-shadow .15s var(--rc-ease), filter .15s var(--rc-ease);
    cursor: pointer;
}
.rc-bk::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
.rc-bk:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(15,23,42,.08); filter: brightness(1.02); }

.rc-bk.red    { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.rc-bk.red::before    { background: var(--rc-red); }
.rc-bk.yellow { background: #fefce8; color: #854d0e; border-color: #fde68a; }
.rc-bk.yellow::before { background: var(--rc-yellow); }
.rc-bk.blue   { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.rc-bk.blue::before   { background: var(--rc-blue); }
.rc-bk.green  { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
.rc-bk.green::before  { background: var(--rc-green); }

.dark .rc-bk.red    { background: rgba(127,29,29,.28); color: #fecaca; border-color: rgba(248,113,113,.24); }
.dark .rc-bk.yellow { background: rgba(113,63,18,.28); color: #fde68a; border-color: rgba(250,204,21,.23); }
.dark .rc-bk.blue   { background: rgba(30,64,175,.25); color: #bfdbfe; border-color: rgba(96,165,250,.23); }
.dark .rc-bk.green  { background: rgba(6,78,59,.28);  color: #a7f3d0; border-color: rgba(52,211,153,.23); }

.rc-bk-top { display: flex; align-items: center; justify-content: space-between; gap: .35rem; }
.rc-bk-time { font-size: .6rem; font-weight: 950; }
.rc-bk-badges { display: inline-flex; gap: .2rem; align-items: center; }
.rc-bk-badge {
    font-size: .5rem; font-weight: 900;
    padding: .1rem .32rem; border-radius: 4px;
    background: rgba(255,255,255,.7);
    display: inline-flex; align-items: center; gap: .2rem;
}
.dark .rc-bk-badge { background: rgba(15,23,42,.5); }
.rc-bk-badge.conflict { background: #fee2e2; color: #b91c1c; }
.dark .rc-bk-badge.conflict { background: rgba(220,38,38,.25); color: #fca5a5; }
.rc-bk-title {
    margin-top: .22rem; font-size: .64rem; font-weight: 950; line-height: 1.25;
    overflow: hidden; text-overflow: ellipsis;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.rc-bk-meta { display: flex; flex-wrap: wrap; gap: .2rem; margin-top: .25rem; }
.rc-bk-mini {
    display: inline-flex; align-items: center; gap: .2rem;
    padding: .14rem .35rem; border-radius: 5px;
    background: rgba(255,255,255,.7);
    font-size: .5rem; font-weight: 900;
    max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.dark .rc-bk-mini { background: rgba(15,23,42,.45); }
.rc-bk-mini .dot { width: 6px; height: 6px; border-radius: 50%; }
.rc-bk-mini .dot.good         { background: var(--rc-green); }
.rc-bk-mini .dot.damaged      { background: var(--rc-slate); }
.rc-bk-mini .dot.renovation   { background: var(--rc-yellow); }
.rc-bk-mini .dot.maintenance  { background: var(--rc-orange); }
.rc-bk-mini .dot.unavailable  { background: var(--rc-red); }
.rc-bk-more { padding-right: .2rem; text-align: right; color: var(--rc-muted); font-size: .56rem; font-weight: 850; }
.rc-bk-empty { padding: .3rem .1rem; color: var(--rc-muted); font-size: .55rem; font-style: italic; }

/* INTELLIGENCE PANEL */
.rc-intel { display: grid; gap: .85rem; position: sticky; top: 1rem; }
.rc-panel {
    border: 1px solid var(--rc-border);
    border-radius: var(--rc-radius);
    background: var(--rc-card);
    box-shadow: var(--rc-shadow-sm);
    overflow: hidden;
}
.rc-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem; padding: .75rem .95rem;
    border-bottom: 1px solid var(--rc-border);
    background: var(--rc-soft);
}
.rc-panel-title { display: flex; align-items: center; gap: .5rem; font-size: .78rem; font-weight: 900; }
.rc-panel-title i { color: var(--rc-blue); }
.rc-panel-badge {
    padding: .18rem .55rem; border-radius: 999px;
    background: rgba(37,99,235,.12); color: var(--rc-blue);
    font-size: .6rem; font-weight: 900;
}
.rc-panel-body { padding: .75rem; display: grid; gap: .5rem; }

.rc-signal {
    display: flex; gap: .6rem; padding: .6rem .65rem;
    border: 1px solid var(--rc-border); border-radius: 10px;
    background: var(--rc-soft);
    animation: rcFade .3s var(--rc-ease-out) both;
}
.rc-signal-dot {
    width: 9px; height: 9px; border-radius: 50%; flex: 0 0 auto; margin-top: .3rem;
    background: var(--rc-green);
    box-shadow: 0 0 0 4px rgba(16,185,129,.12);
}
.rc-signal-dot.warning { background: var(--rc-yellow); box-shadow: 0 0 0 4px rgba(234,179,8,.12); }
.rc-signal-dot.danger  { background: var(--rc-red);    box-shadow: 0 0 0 4px rgba(220,38,38,.12); }
.rc-signal-dot.info    { background: var(--rc-blue);   box-shadow: 0 0 0 4px rgba(37,99,235,.12); }
.rc-signal-content strong { display: block; font-size: .72rem; font-weight: 900; color: var(--rc-text); }
.rc-signal-content span { display: block; margin-top: .15rem; font-size: .66rem; color: var(--rc-muted); line-height: 1.45; }

.rc-rooms-list { max-height: 280px; overflow-y: auto; padding: .25rem; }
.rc-room-item {
    display: grid; grid-template-columns: minmax(0,1fr) auto;
    gap: .5rem; align-items: center;
    padding: .55rem .5rem;
    border-bottom: 1px dashed var(--rc-border);
}
.rc-room-item:last-child { border-bottom: 0; }
.rc-room-name {
    font-size: .72rem; font-weight: 900; color: var(--rc-text);
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.rc-room-meta { margin-top: .15rem; color: var(--rc-muted); font-size: .6rem; }
.rc-room-pill {
    padding: .22rem .55rem; border-radius: 999px;
    font-size: .58rem; font-weight: 900; border: 1px solid transparent;
    display: inline-flex; align-items: center; gap: .25rem; white-space: nowrap;
}
.rc-room-pill.good         { background: rgba(16,185,129,.12); color: #059669; border-color: rgba(16,185,129,.22); }
.rc-room-pill.damaged      { background: rgba(100,116,139,.13); color: #475569; border-color: rgba(100,116,139,.22); }
.rc-room-pill.renovation   { background: rgba(234,179,8,.14);  color: #a16207; border-color: rgba(234,179,8,.26); }
.rc-room-pill.maintenance  { background: rgba(249,115,22,.14); color: #c2410c; border-color: rgba(249,115,22,.24); }
.rc-room-pill.unavailable  { background: rgba(220,38,38,.12);  color: #b91c1c; border-color: rgba(220,38,38,.24); }
.dark .rc-room-pill.good        { color: #6ee7b7; }
.dark .rc-room-pill.damaged     { color: #cbd5e1; }
.dark .rc-room-pill.renovation  { color: #fde68a; }
.dark .rc-room-pill.maintenance { color: #fdba74; }
.dark .rc-room-pill.unavailable { color: #fca5a5; }

/* MODAL */
.rc-modal {
    position: fixed; inset: 0; z-index: 9000;
    display: none; align-items: center; justify-content: center;
    padding: 1rem;
    background: rgba(2,6,23,.58);
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
}
.rc-modal.open { display: flex; animation: rcFade .2s var(--rc-ease-out); }
.rc-modal-card {
    width: min(640px, 100%); max-height: 90vh; overflow-y: auto;
    border-radius: 20px;
    background: var(--rc-card); color: var(--rc-text);
    border: 1px solid var(--rc-border);
    box-shadow: 0 40px 90px rgba(0,0,0,.4);
    animation: rcZoom .3s var(--rc-ease-out);
}
.rc-modal-head {
    position: sticky; top: 0; z-index: 2;
    display: flex; align-items: center; justify-content: space-between;
    gap: .75rem; padding: 1rem 1.15rem;
    background: var(--rc-card);
    border-bottom: 1px solid var(--rc-border);
}
.rc-modal-head h3 { margin: 0; font-size: 1rem; font-weight: 950; }
.rc-modal-close {
    width: 34px; height: 34px; border-radius: 50%;
    border: 1px solid var(--rc-border); background: var(--rc-soft);
    color: var(--rc-text); cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    transition: background .15s var(--rc-ease), transform .15s var(--rc-ease);
}
.rc-modal-close:hover { background: var(--rc-border); }
.rc-modal-close:active { transform: scale(.92); }
.rc-modal-body { padding: 1.15rem; }
.rc-modal-row {
    display: grid; grid-template-columns: 140px minmax(0,1fr);
    gap: .5rem; padding: .55rem 0;
    border-bottom: 1px solid var(--rc-border);
    font-size: .8rem;
}
.rc-modal-row:last-child { border-bottom: 0; }
.rc-modal-row .k { color: var(--rc-muted); font-weight: 800; }
.rc-modal-row .v { color: var(--rc-text); font-weight: 700; word-break: break-word; }

/* TOAST */
.rc-toast {
    position: fixed; bottom: 1.25rem; left: 50%;
    transform: translateX(-50%) translateY(20px);
    padding: .65rem 1rem; border-radius: 12px;
    background: #0f172a; color: #fff;
    font-size: .78rem; font-weight: 800;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
    opacity: 0; pointer-events: none;
    transition: opacity .25s var(--rc-ease), transform .25s var(--rc-ease);
    z-index: 9999;
}
.rc-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

/* LIVE */
.rc-live {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .28rem .65rem; border-radius: 999px;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.2);
    font-size: .66rem; font-weight: 900;
}
.rc-live-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,.25);
    animation: rcPulse 1.8s ease-in-out infinite;
}

/* ANIMATIONS */
@keyframes rcFade { from { opacity: 0; } to { opacity: 1; } }
@keyframes rcZoom {
    from { opacity: 0; transform: scale(.94) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes rcPulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50%     { opacity: .55; transform: scale(.85); }
}

/* RESPONSIVE */
@media (max-width: 1280px) {
    .rc-kpi-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
    .rc-grid { grid-template-columns: minmax(0,1fr) 300px; }
}
@media (max-width: 1080px) {
    .rc-grid { grid-template-columns: 1fr; }
    .rc-intel { position: static; }
    .rc-filter-form { grid-template-columns: repeat(3, minmax(0,1fr)); }
}
@media (max-width: 820px) {
    .rc-page { padding: .85rem .75rem 1.5rem; }
    .rc-hero { grid-template-columns: 1fr; padding: 1rem 1.1rem; }
    .rc-hero-actions { justify-content: flex-start; min-width: 0; }
    .rc-kpi-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .rc-filter-form { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .rc-cal-head { flex-direction: column; align-items: flex-start; }
    .rc-cal-head-actions { width: 100%; justify-content: space-between; }
    .rc-cell { min-height: 108px; }
}
@media (max-width: 640px) {
    .rc-hero-title { font-size: 1.15rem; }
    .rc-kpi { padding: .7rem; }
    .rc-kpi .value { font-size: 1.2rem; }
    .rc-cal-head h2 { font-size: 1rem; }
    .rc-day-head > div { font-size: .55rem; padding: .35rem 0; }
    .rc-cell { min-height: 84px; padding: .3rem .35rem; }
    .rc-date-num { width: 22px; height: 22px; font-size: .62rem; }
    .rc-day-state { display: none; }
    .rc-bk { padding: .3rem .35rem .32rem .45rem; }
    .rc-bk-title { font-size: .56rem; -webkit-line-clamp: 2; }
    .rc-bk-time { font-size: .55rem; }
    .rc-bk-mini { font-size: .46rem; padding: .1rem .28rem; }
    .rc-bk-meta { display: none; }
    .rc-filter-form { grid-template-columns: 1fr; }
    .rc-view-tabs { width: 100%; justify-content: space-between; }
    .rc-tab { flex: 1; justify-content: center; }
}
@media (max-width: 400px) {
    .rc-kpi-grid { grid-template-columns: 1fr; }
    .rc-cell { min-height: 72px; }
}
@media (prefers-reduced-motion: reduce) {
    .rc-page *, .rc-page *::before, .rc-page *::after {
        animation-duration: .01ms !important;
        transition-duration: .01ms !important;
    }
}
.rc-page ::-webkit-scrollbar { width: 8px; height: 8px; }
.rc-page ::-webkit-scrollbar-thumb { background: rgba(148,163,184,.5); border-radius: 999px; }
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;
    use Carbon\WeekDay;

    /** @var \App\Services\RoomBookingClassifier $classifier */

    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $totalBookings = $bookings->count();
    $todayStr      = now()->toDateString();
    $now           = now();

    $classifiedSafe = is_array($classified) ? $classified : [];

    /* ---------- AGREGASI ---------- */
    $byPriority = ['red' => 0, 'yellow' => 0, 'blue' => 0, 'green' => 0];
    $byStatus   = ['approved' => 0, 'pending' => 0, 'other' => 0];
    $ongoing    = 0;
    $futurePending = 0;

    foreach ($classifiedSafe as $m) {
        if (isset($byPriority[$m['priority']])) $byPriority[$m['priority']]++;

        $s = $m['status_raw'];
        if (in_array($s, ['approved','approve','approved_by_owner','disetujui','setuju'], true)) $byStatus['approved']++;
        elseif (in_array($s, ['pending','pending_approval','menunggu','menunggu persetujuan'], true)) $byStatus['pending']++;
        else $byStatus['other']++;

        if ($m['start'] && $m['end']) {
            if ($m['start']->lte($now) && $m['end']->gte($now)
                && !in_array($s, ['rejected','ditolak','cancelled','dibatalkan'], true)) {
                $ongoing++;
            } elseif ($m['start']->gt($now)
                && in_array($s, ['pending','pending_approval','menunggu','menunggu persetujuan'], true)) {
                $futurePending++;
            }
        }
    }

    $conflictCount = is_array($conflicts) ? count($conflicts) : 0;
    $conflictSet   = array_flip(is_array($conflicts) ? $conflicts : []);

    /* ---------- FILTER AKTIF ---------- */
    $activeFilters = collect([
        'building' => request('building'),
        'room_id'  => request('room_id'),
        'status'   => request('status'),
        'q'        => request('q'),
    ])->filter()->all();

    /* ---------- GRID RANGE ---------- */
    $firstDay = $cursor->copy()->startOfMonth()->startOfWeek(WeekDay::Monday);
    $lastDay  = $cursor->copy()->endOfMonth()->endOfWeek(WeekDay::Sunday);
    $prev     = $cursor->copy()->subMonthNoOverflow();
    $next     = $cursor->copy()->addMonthNoOverflow();

    /* ---------- URL HELPER ---------- */
    $qs = fn(array $overrides = []) => array_merge(
        array_filter(request()->only(['view','month','year','building','room_id','status','q'])),
        $overrides
    );

    /* ============================================================
       PERSIAPAN DATA UNTUK JS — WAJIB DI @php
       Jangan taruh collect()->map() langsung di dalam @json()
       karena parser Blade tidak bisa menangani multi-line bracket.
       ============================================================ */
    $jsBookings = [];
    foreach ($classifiedSafe as $m) {
        $jsBookings[] = [
            'id'            => $m['id'] ?? null,
            'title'         => $m['title'] ?? '',
            'priority'      => $m['priority'] ?? 'green',
            'priorityLabel' => $m['priority_label'] ?? '',
            'statusLabel'   => $m['status_label'] ?? '',
            'activityLabel' => $m['activity_label'] ?? '',
            'roomName'      => $m['room_name'] ?? '',
            'roomClass'     => $m['room_class'] ?? 'good',
            'roomLabel'     => $m['room_label'] ?? '',
            'start'         => $m['start'] ? $m['start']->format('Y-m-d H:i:s') : null,
            'end'           => $m['end']   ? $m['end']->format('Y-m-d H:i:s')   : null,
        ];
    }

    // URL untuk shortcut keyboard (precompute, tidak perlu route() di JS)
    $urlPrev   = route('owner.rooms.calendar', $qs(['month' => $prev->month, 'year' => $prev->year]));
    $urlNext   = route('owner.rooms.calendar', $qs(['month' => $next->month, 'year' => $next->year]));
    $urlToday  = route('owner.rooms.calendar', $qs(['month' => now()->month, 'year' => now()->year]));
    $urlMonth  = route('owner.rooms.calendar', $qs(['view' => 'month']));
    $urlWeek   = route('owner.rooms.calendar', $qs(['view' => 'week']));
    $urlDay    = route('owner.rooms.calendar', $qs(['view' => 'day']));
    $urlAgenda = route('owner.rooms.calendar', $qs(['view' => 'agenda']));
@endphp

<div class="rc-page">

    {{-- ============================================================
         HERO
    ============================================================ --}}
    <section class="rc-hero">
        <div class="rc-hero-inner">
            <span class="rc-eyebrow">
                <i class="fas fa-shield-halved"></i> Room Command Center
            </span>
            <h1 class="rc-hero-title">Kalender Ruangan Pintar</h1>
            <p class="rc-hero-desc">
                Pusat kendali jadwal ruang kampus dengan klasifikasi prioritas, jenis kegiatan,
                status persetujuan, kondisi fisik ruangan, dan deteksi konflik otomatis.
            </p>

            <div class="rc-hero-meta">
                <span class="rc-live" id="rcLive">
                    <span class="rc-live-dot"></span>
                    <span id="rcClock">{{ $now->format('H:i:s') }}</span>
                    <span style="opacity:.65;">WIB</span>
                </span>
                <span class="rc-hero-chip">
                    <i class="fas fa-calendar-days"></i> {{ $monthNames[$month] }} {{ $year }}
                </span>
                <span class="rc-hero-chip">
                    <i class="fas fa-database"></i> {{ $totalBookings }} booking
                </span>
                <span class="rc-hero-chip">
                    <i class="fas fa-clock"></i> {{ $ongoing }} berlangsung
                </span>
            </div>
        </div>

        <div class="rc-hero-actions">
            <a href="{{ route('owner.rooms.index') }}" class="rc-action">
                <i class="fas fa-door-open"></i> Daftar Ruangan
            </a>
            <a href="{{ $urlToday }}" class="rc-action primary">
                <i class="fas fa-calendar-day"></i> Hari Ini
                <kbd style="font-size:.6rem;opacity:.65;">T</kbd>
            </a>
        </div>
    </section>

    {{-- ============================================================
         KPI
    ============================================================ --}}
    <section class="rc-kpi-grid">
        <div class="rc-kpi" data-tone="info">
            <div class="label">Total Booking</div>
            <div class="value">{{ $totalBookings }}<i class="fas fa-calendar-days"></i></div>
            <div class="note">Seluruh booking pada rentang waktu aktif.</div>
        </div>
        <div class="rc-kpi" data-tone="danger">
            <div class="label">Prioritas Tinggi</div>
            <div class="value">{{ $byPriority['red'] }}<i class="fas fa-triangle-exclamation"></i></div>
            <div class="note">Sidang, ujian, akreditasi, agenda penting.</div>
        </div>
        <div class="rc-kpi" data-tone="warning">
            <div class="label">Akademik / Organisasi</div>
            <div class="value">{{ $byPriority['yellow'] }}<i class="fas fa-people-group"></i></div>
            <div class="note">Rapat, UKM, skripsi, makalah, seminar.</div>
        </div>
        <div class="rc-kpi" data-tone="success">
            <div class="label">Kelas / Umum</div>
            <div class="value">{{ $byPriority['blue'] + $byPriority['green'] }}<i class="fas fa-book-open"></i></div>
            <div class="note">Perkuliahan & kegiatan umum lainnya.</div>
        </div>
        <div class="rc-kpi" data-tone="{{ $byStatus['pending'] > 0 ? 'warning' : 'success' }}">
            <div class="label">Menunggu Persetujuan</div>
            <div class="value">{{ $byStatus['pending'] }}<i class="fas fa-clock"></i></div>
            <div class="note">Perlu ditinjau sebelum waktu kegiatan.</div>
        </div>
        <div class="rc-kpi" data-tone="conflict">
            <div class="label">Indikasi Konflik</div>
            <div class="value">{{ $conflictCount }}<i class="fas fa-code-compare"></i></div>
            <div class="note">Ruangan & waktu tumpang tindih.</div>
        </div>
    </section>

    {{-- ============================================================
         FILTER
    ============================================================ --}}
    <section class="rc-filter {{ $activeFilters ? 'open' : '' }}" id="rcFilter">
        <div class="rc-filter-head" id="rcFilterHead" role="button" tabindex="0"
             aria-expanded="{{ $activeFilters ? 'true' : 'false' }}">
            <div class="rc-filter-title">
                <i class="fas fa-filter"></i>
                <span>Filter & Pencarian</span>
                <span class="rc-filter-count {{ $activeFilters ? 'show' : '' }}">
                    {{ count($activeFilters) }} aktif
                </span>
            </div>
            <div style="display:flex;align-items:center;gap:.5rem;">
                @if($activeFilters)
                    <a href="{{ route('owner.rooms.calendar', ['view' => $view, 'month' => $month, 'year' => $year]) }}"
                       class="rc-filter-reset"
                       onclick="event.stopPropagation();">
                        <i class="fas fa-rotate-left"></i> Reset
                    </a>
                @endif
                <div class="rc-filter-chev"><i class="fas fa-chevron-down"></i></div>
            </div>
        </div>
        <div class="rc-filter-body">
            <form method="GET" class="rc-filter-form" id="rcFilterForm">
                <input type="hidden" name="view"  value="{{ $view }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year"  value="{{ $year }}">

                <input type="text" name="q" placeholder="Cari kegiatan / pemesan..."
                       value="{{ request('q') }}" aria-label="Cari kegiatan">

                <select name="building" aria-label="Gedung">
                    <option value="">Semua Gedung</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b }}" @selected(request('building') === $b)>{{ $b }}</option>
                    @endforeach
                </select>

                <select name="room_id" aria-label="Ruangan">
                    <option value="">Semua Ruangan</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" @selected((int) request('room_id') === $r->id)>
                            {{ $r->name }} — {{ $r->room_code }}
                        </option>
                    @endforeach
                </select>

                <select name="status" aria-label="Status">
                    <option value="">Semua Status</option>
                    @foreach(['Disetujui','Menunggu Persetujuan','Ditolak','Dibatalkan','Selesai'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>

                <button type="submit"><i class="fas fa-filter"></i> Terapkan</button>
            </form>

            @if($activeFilters)
                <div class="rc-chips">
                    @foreach($activeFilters as $k => $v)
                        <span class="rc-chip">
                            <i class="fas fa-tag"></i> {{ $k }}: {{ $v }}
                            <a href="{{ route('owner.rooms.calendar', $qs([$k => null])) }}" title="Hapus filter">×</a>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================================
         VIEW TABS
    ============================================================ --}}
    <nav class="rc-view-tabs" role="tablist">
        <a href="{{ $urlMonth }}"  class="rc-tab {{ $view === 'month'  ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Bulan</a>
        <a href="{{ $urlWeek }}"   class="rc-tab {{ $view === 'week'   ? 'active' : '' }}"><i class="fas fa-calendar-week"></i> Minggu</a>
        <a href="{{ $urlDay }}"    class="rc-tab {{ $view === 'day'    ? 'active' : '' }}"><i class="fas fa-calendar-day"></i> Hari</a>
        <a href="{{ $urlAgenda }}" class="rc-tab {{ $view === 'agenda' ? 'active' : '' }}"><i class="fas fa-list"></i> Agenda</a>
    </nav>

    {{-- ============================================================
         MAIN GRID
    ============================================================ --}}
    <div class="rc-grid">

        {{-- CALENDAR --}}
        <section class="rc-cal">
            <header class="rc-cal-head">
                <div>
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        @if($view === 'month')
                            {{ $monthNames[$month] }} {{ $year }}
                        @elseif($view === 'week')
                            Pekan {{ $cursor->weekOfYear }} — {{ $cursor->isoFormat('MMMM YYYY') }}
                        @elseif($view === 'day')
                            {{ $cursor->isoFormat('dddd, D MMMM YYYY') }}
                        @else
                            Agenda {{ $monthNames[$month] }} – {{ $monthNames[(int)$rangeEnd->format('n')] }}
                        @endif
                    </h2>
                    <p>
                        {{ $totalBookings }} booking ·
                        {{ $byStatus['approved'] }} disetujui ·
                        {{ $byStatus['pending'] }} menunggu ·
                        {{ $conflictCount }} indikasi konflik
                    </p>
                </div>

                <div class="rc-cal-head-actions">
                    <a href="{{ $urlPrev }}" class="rc-nav-btn" title="Sebelumnya" aria-label="Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="{{ $urlToday }}" class="rc-nav-btn wide" title="Hari Ini">
                        <i class="fas fa-calendar-day"></i> Hari Ini
                    </a>
                    <a href="{{ $urlNext }}" class="rc-nav-btn" title="Berikutnya" aria-label="Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </header>

            {{-- Legend --}}
            <div class="rc-legend">
                <span class="rc-legend-title">Prioritas:</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-red"></span> Penting</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-yellow"></span> Organisasi / Akademik</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-blue"></span> Kelas</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-green"></span> Umum</span>
                <span class="rc-legend-title" style="margin-left:.5rem;">Kondisi Ruang:</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-green"></span> Baik</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-slate"></span> Rusak</span>
                <span class="rc-legend-item"><span class="rc-legend-dot rc-dot-yellow"></span> Renovasi</span>
            </div>

            @if($view === 'agenda')
                {{-- ========== AGENDA VIEW ========== --}}
                <div style="padding:1rem;">
                    @php
                        $sortedAgenda = $bookings
                            ->sortBy(fn($b) => optional($b->start_datetime)->timestamp ?? PHP_INT_MAX)
                            ->groupBy(fn($b) => optional($b->start_datetime)->format('Y-m-d'));
                    @endphp

                    @forelse($sortedAgenda as $date => $dayBookings)
                        @php $dayObj = Carbon::parse($date); @endphp
                        <div style="margin-bottom:1rem;">
                            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                                <span style="
                                    display:inline-flex;align-items:center;justify-content:center;
                                    width:36px;height:36px;border-radius:10px;
                                    background:linear-gradient(135deg,#06b6d4,#2563eb);color:#fff;
                                    font-weight:900;font-size:.9rem;">
                                    {{ $dayObj->format('d') }}
                                </span>
                                <div>
                                    <div style="font-weight:950;font-size:.85rem;">{{ $dayObj->isoFormat('dddd') }}</div>
                                    <div style="color:var(--rc-muted);font-size:.68rem;">{{ $dayObj->isoFormat('D MMMM YYYY') }}</div>
                                </div>
                            </div>

                            @foreach($dayBookings as $bk)
                                @php
                                    $meta = $classifiedSafe[$bk->id] ?? null;
                                    if (!$meta) continue;
                                    $isConflict = isset($conflictSet[(string)$bk->id]);
                                @endphp
                                <a href="#" class="rc-bk {{ $meta['priority'] }}"
                                   style="display:block;margin-bottom:.35rem;"
                                   data-booking-modal="{{ $bk->id }}">
                                    <div class="rc-bk-top">
                                        <span class="rc-bk-time">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $meta['start'] ? $meta['start']->format('H:i') : '--:--' }}
                                            –
                                            {{ $meta['end'] ? $meta['end']->format('H:i') : '--:--' }}
                                        </span>
                                        <span class="rc-bk-badges">
                                            @if($isConflict)
                                                <span class="rc-bk-badge conflict"><i class="fas fa-triangle-exclamation"></i> Konflik</span>
                                            @endif
                                            <span class="rc-bk-badge">{{ $meta['status_label'] }}</span>
                                        </span>
                                    </div>
                                    <div class="rc-bk-title" style="-webkit-line-clamp:1;">{{ $meta['title'] }}</div>
                                    <div class="rc-bk-meta">
                                        <span class="rc-bk-mini"><i class="fas {{ $meta['priority_icon'] }}"></i> {{ $meta['activity_label'] }}</span>
                                        <span class="rc-bk-mini"><span class="dot {{ $meta['room_class'] }}"></span> {{ $meta['room_name'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @empty
                        <div style="padding:2rem;text-align:center;color:var(--rc-muted);">
                            <i class="fas fa-calendar-xmark" style="font-size:2rem;opacity:.5;display:block;margin-bottom:.5rem;"></i>
                            Tidak ada agenda pada rentang waktu ini.
                        </div>
                    @endforelse
                </div>
            @else
                {{-- ========== MONTH / WEEK / DAY VIEW ========== --}}
                <div class="rc-day-head">
                    <div>Sen</div><div>Sel</div><div>Rab</div>
                    <div>Kam</div><div>Jum</div>
                    <div class="weekend">Sab</div><div class="weekend">Min</div>
                </div>

                <div class="rc-cal-grid">
                    @php $day = $firstDay->copy(); @endphp
                    @while($day <= $lastDay)
                        @php
                            $dateKey = $day->format('Y-m-d');
                            $isCurrentMonth = (int) $day->month === (int) $month;
                            $isToday = $dateKey === $todayStr;
                            $isWeekend = in_array($day->dayOfWeekIso, [6,7], true);

                            if ($view === 'day' && $dateKey !== $cursor->format('Y-m-d')) {
                                $day->addDay(); continue;
                            }

                            $dayBks = $bookingsByDay->get($dateKey, collect());
                            if (!$dayBks instanceof \Illuminate\Support\Collection) $dayBks = collect($dayBks ?? []);
                        @endphp

                        <div class="rc-cell {{ !$isCurrentMonth && $view === 'month' ? 'out-month' : '' }} {{ $isToday ? 'today' : '' }}"
                             data-date="{{ $dateKey }}">
                            <div class="rc-date-row">
                                <span class="rc-date-num">{{ $day->format('d') }}</span>
                                @if($isToday)
                                    <span class="rc-day-state">HARI INI</span>
                                @elseif($isWeekend)
                                    <span class="rc-day-state">PEKAN</span>
                                @endif
                            </div>

                            <div class="rc-bk-list">
                                @forelse($dayBks->take(4) as $bk)
                                    @php
                                        $meta = $classifiedSafe[$bk->id] ?? null;
                                        if (!$meta) continue;
                                        $isConflict = isset($conflictSet[(string)$bk->id]);
                                    @endphp
                                    <a href="#" class="rc-bk {{ $meta['priority'] }}"
                                       title="{{ $meta['title'] }} — {{ $meta['room_name'] }}"
                                       data-booking-modal="{{ $bk->id }}">
                                        <div class="rc-bk-top">
                                            <span class="rc-bk-time">
                                                {{ $meta['start'] ? $meta['start']->format('H:i') : '--:--' }}
                                            </span>
                                            <span class="rc-bk-badges">
                                                @if($isConflict)
                                                    <span class="rc-bk-badge conflict"><i class="fas fa-triangle-exclamation"></i></span>
                                                @else
                                                    <span class="rc-bk-badge">{{ $meta['status_label'] }}</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="rc-bk-title">{{ $meta['title'] }}</div>
                                        <div class="rc-bk-meta">
                                            <span class="rc-bk-mini"><span class="dot {{ $meta['room_class'] }}"></span> {{ $meta['room_name'] }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="rc-bk-empty">Tidak ada booking</div>
                                @endforelse

                                @if($dayBks->count() > 4)
                                    <div class="rc-bk-more">+{{ $dayBks->count() - 4 }} lagi</div>
                                @endif
                            </div>
                        </div>

                        @php $day->addDay(); @endphp
                    @endwhile
                </div>
            @endif
        </section>

        {{-- INTELLIGENCE PANEL --}}
        <aside class="rc-intel">
            <div class="rc-panel">
                <div class="rc-panel-head">
                    <div class="rc-panel-title"><i class="fas fa-brain"></i> Smart Signals</div>
                    <span class="rc-panel-badge">Live</span>
                </div>
                <div class="rc-panel-body">
                    <div class="rc-signal">
                        <span class="rc-signal-dot"></span>
                        <div class="rc-signal-content">
                            <strong>{{ $ongoing }} kegiatan berlangsung</strong>
                            <span>Booking yang sedang aktif saat ini.</span>
                        </div>
                    </div>
                    <div class="rc-signal">
                        <span class="rc-signal-dot {{ $futurePending > 0 ? 'warning' : '' }}"></span>
                        <div class="rc-signal-content">
                            <strong>{{ $futurePending }} menunggu persetujuan</strong>
                            <span>Perlu ditinjau sebelum pelaksanaan.</span>
                        </div>
                    </div>
                    <div class="rc-signal">
                        <span class="rc-signal-dot {{ $conflictCount > 0 ? 'danger' : '' }}"></span>
                        <div class="rc-signal-content">
                            <strong>{{ $conflictCount }} indikasi konflik</strong>
                            <span>{{ $conflictCount > 0
                                ? 'Ruangan & waktu saling tumpang tindih. Cek sebelum menyetujui.'
                                : 'Tidak ada konflik waktu pada booking aktif.' }}</span>
                        </div>
                    </div>
                    <div class="rc-signal">
                        <span class="rc-signal-dot info"></span>
                        <div class="rc-signal-content">
                            <strong>{{ $byPriority['red'] }} prioritas tinggi</strong>
                            <span>Pastikan ruang & waktu tidak bentrok dengan agenda lain.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rc-panel">
                <div class="rc-panel-head">
                    <div class="rc-panel-title"><i class="fas fa-door-open"></i> Kondisi Ruangan</div>
                    <span class="rc-panel-badge">{{ $rooms->count() }} ruang</span>
                </div>
                <div class="rc-rooms-list">
                    @forelse($rooms->take(15) as $r)
                        @php
                            $rawCond = strtolower((string) ($r->condition ?? $r->status ?? 'baik'));
                            if (preg_match('/\b(renovasi|renovation)\b/i', $rawCond))       { $rc = 'renovation'; $rl = 'Renovasi'; $ri = 'fa-hammer'; }
                            elseif (preg_match('/\b(rusak|damage|broken)\b/i', $rawCond))    { $rc = 'damaged'; $rl = 'Rusak'; $ri = 'fa-screwdriver-wrench'; }
                            elseif (preg_match('/\b(perawatan|maintenance)\b/i', $rawCond))  { $rc = 'maintenance'; $rl = 'Perawatan'; $ri = 'fa-wrench'; }
                            elseif (preg_match('/\b(tidak tersedia|unavailable|ditutup)\b/i', $rawCond)) { $rc = 'unavailable'; $rl = 'Tidak Tersedia'; $ri = 'fa-ban'; }
                            else                                                              { $rc = 'good'; $rl = 'Baik'; $ri = 'fa-circle-check'; }
                        @endphp
                        <div class="rc-room-item">
                            <div>
                                <div class="rc-room-name">{{ $r->name }}</div>
                                <div class="rc-room-meta">{{ $r->room_code ?? '-' }} · {{ $r->floor ?? '-' }}</div>
                            </div>
                            <span class="rc-room-pill {{ $rc }}">
                                <i class="fas {{ $ri }}"></i> {{ $rl }}
                            </span>
                        </div>
                    @empty
                        <div style="padding:1rem;text-align:center;color:var(--rc-muted);font-size:.72rem;">
                            Belum ada data ruangan.
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- ============================================================
     MODAL DETAIL BOOKING
============================================================ --}}
<div class="rc-modal" id="rcModal" role="dialog" aria-modal="true">
    <div class="rc-modal-card">
        <div class="rc-modal-head">
            <h3 id="rcModalTitle">Detail Booking</h3>
            <button class="rc-modal-close" id="rcModalClose" aria-label="Tutup">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="rc-modal-body" id="rcModalBody"></div>
    </div>
</div>

{{-- TOAST --}}
<div class="rc-toast" id="rcToast"></div>

{{-- ============================================================
     DATA UNTUK JS — VARIABEL SEDERHANA
     CATATAN: Jangan taruh collect()->map() langsung di dalam
     @json(). Parser Blade tidak bisa menangani nested brackets
     di directive @json() multi-baris.
============================================================ --}}
<script>
window.__RC__ = {
    bookings:  @json($jsBookings),
    conflicts: @json($conflicts),
    today:     @json($todayStr),
    view:      @json($view),
    month:     @json($month),
    year:      @json($year),
    urls: {
        prev:   @json($urlPrev),
        next:   @json($urlNext),
        today:  @json($urlToday),
        month:  @json($urlMonth),
        week:   @json($urlWeek),
        day:    @json($urlDay),
        agenda: @json($urlAgenda)
    }
};
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ============================================================
       LIVE CLOCK
    ============================================================ */
    const clock = document.getElementById('rcClock');
    if (clock) {
        const tick = () => {
            const d = new Date();
            clock.textContent = [d.getHours(), d.getMinutes(), d.getSeconds()]
                .map(n => String(n).padStart(2, '0')).join(':');
        };
        tick();
        setInterval(tick, 1000);
    }

    /* ============================================================
       FILTER COLLAPSIBLE
    ============================================================ */
    const filter     = document.getElementById('rcFilter');
    const filterHead = document.getElementById('rcFilterHead');
    filterHead?.addEventListener('click', () => {
        const open = filter.classList.toggle('open');
        filterHead.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    filterHead?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); filterHead.click(); }
    });

    /* ============================================================
       MODAL DETAIL BOOKING
    ============================================================ */
    const modal      = document.getElementById('rcModal');
    const modalBody  = document.getElementById('rcModalBody');
    const modalTitle = document.getElementById('rcModalTitle');
    const modalClose = document.getElementById('rcModalClose');

    const map         = Object.fromEntries((window.__RC__.bookings || []).map(b => [String(b.id), b]));
    const conflictSet = new Set((window.__RC__.conflicts || []).map(String));

    function fmtDT(s) {
        if (!s) return '-';
        try {
            const d = new Date(s.replace(' ', 'T'));
            return d.toLocaleString('id-ID', {
                weekday:'long', day:'2-digit', month:'long', year:'numeric',
                hour:'2-digit', minute:'2-digit'
            });
        } catch { return s; }
    }

    function openModal(id) {
        const b = map[String(id)];
        if (!b) return;
        modalTitle.textContent = b.title || 'Detail Booking';
        const isConflict = conflictSet.has(String(id));
        modalBody.innerHTML = `
            <div class="rc-modal-row"><div class="k">Ruangan</div><div class="v">${b.roomName}</div></div>
            <div class="rc-modal-row"><div class="k">Jenis Kegiatan</div><div class="v">${b.activityLabel}</div></div>
            <div class="rc-modal-row"><div class="k">Prioritas</div><div class="v">${b.priorityLabel}</div></div>
            <div class="rc-modal-row"><div class="k">Status</div><div class="v">${b.statusLabel}${isConflict ? ' · <span style="color:#dc2626;font-weight:900;">Konflik terdeteksi</span>' : ''}</div></div>
            <div class="rc-modal-row"><div class="k">Mulai</div><div class="v">${fmtDT(b.start)}</div></div>
            <div class="rc-modal-row"><div class="k">Selesai</div><div class="v">${fmtDT(b.end)}</div></div>
            <div class="rc-modal-row"><div class="k">Kondisi Ruang</div><div class="v">${b.roomLabel}</div></div>
        `;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-booking-modal]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(el.dataset.bookingModal);
        });
    });

    modalClose?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    /* ============================================================
       KEYBOARD SHORTCUTS
    ============================================================ */
    document.addEventListener('keydown', (e) => {
        const isInput = ['INPUT','SELECT','TEXTAREA'].includes(document.activeElement?.tagName);
        if (isInput) return;

        const key = (e.key || '').toLowerCase();
        const U   = window.__RC__.urls || {};

        if (key === 'escape') { closeModal(); return; }
        if (key === 'arrowleft' && U.prev)   { window.location.href = U.prev; }
        if (key === 'arrowright' && U.next)  { window.location.href = U.next; }
        if (key === 't' && U.today)          { window.location.href = U.today; }
        if (key === '1' && U.month)          { window.location.href = U.month; }
        if (key === '2' && U.week)           { window.location.href = U.week; }
        if (key === '3' && U.day)            { window.location.href = U.day; }
        if (key === '4' && U.agenda)         { window.location.href = U.agenda; }

        if ((e.ctrlKey || e.metaKey) && key === 'k') {
            e.preventDefault();
            filter.classList.add('open');
            document.querySelector('.rc-filter-form input[name="q"]')?.focus();
        }
    });

    /* ============================================================
       REALTIME REFRESH (silent polling tiap 60 detik)
    ============================================================ */
    const REALTIME_INTERVAL = 60000;
    let rt = null;

    function startRealtime() {
        if (rt) return;
        rt = setInterval(() => {
            if (document.hidden) return;
            if (!modal.classList.contains('open')) {
                window.location.reload();
            }
        }, REALTIME_INTERVAL);
    }
    function stopRealtime() { clearInterval(rt); rt = null; }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stopRealtime();
        else startRealtime();
    });
    startRealtime();

    /* ============================================================
       SMOOTH SCROLL KE HARI INI
    ============================================================ */
    const todayCell = document.querySelector(`.rc-cell[data-date="${window.__RC__.today}"]`);
    if (todayCell && window.innerWidth > 640) {
        setTimeout(() => {
            todayCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }
});
</script>
@endsection
@extends('layouts.roles.owner')

@section('title', 'Kehadiran – Command Center')

@section('content')

<div class="attendance-cc min-h-screen bg-slate-50 dark:bg-slate-950"
     x-data="attendanceCenter({ refreshInterval: 60000 })"
     x-init="init()">

    {{-- ═══════════════════════════════════════════════════════════
         HEADER (NON-STICKY — akan tertutup saat scroll)
    ════════════════════════════════════════════════════════════ --}}
    <header class="cc-header">
        <div class="cc-container">
            <div class="cc-header-inner">

                {{-- Left: Brand + Clock --}}
                <div class="cc-header-left">
                    <div class="cc-icon-badge">
                        <i class="fas fa-satellite-dish"></i>
                    </div>

                    <div class="cc-header-text">
                        <div class="cc-header-title-row">
                            <h1 class="cc-title">Command Center Absensi</h1>
                            <span class="cc-live-badge">
                                <span class="cc-pulse-dot">
                                    <span class="cc-pulse-ring"></span>
                                    <span class="cc-pulse-core"></span>
                                </span>
                                LIVE
                            </span>
                            <span class="cc-device-badge" :title="'Terdeteksi: ' + deviceLabel">
                                <i class="fas" :class="deviceIcon"></i>
                                <span x-text="deviceLabel"></span>
                            </span>
                        </div>

                        <div class="cc-breadcrumb">
                            <span class="cc-bc-item">Absensi</span>
                            <span class="cc-bc-sep">/</span>
                            <span class="cc-bc-current">Kehadiran</span>
                            <span class="cc-bc-divider"></span>
                            <span class="cc-clock">
                                <i class="fas fa-clock"></i>
                                <span class="cc-clock-time" x-text="serverClock"></span>
                            </span>
                            <span class="cc-sync">
                                <span class="cc-sync-dot" :class="'cc-sync-' + syncStatus"></span>
                                <span class="cc-sync-label" x-text="syncStatus"></span>
                                <span class="cc-sync-drift" x-show="driftMs > 0"
                                      x-text="'±' + Math.round(driftMs) + 'ms'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="cc-header-actions">
                    <button type="button" @click="refreshPage()" class="cc-btn cc-btn-ghost">
                        <i class="fas fa-sync-alt"></i>
                        <span class="cc-btn-label">Refresh</span>
                    </button>

                    <a href="{{ route('owner.attendance.calendar') }}" class="cc-btn cc-btn-ghost">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="cc-btn-label">Kalender</span>
                    </a>

                    <a href="{{ route('owner.attendance.create') }}" class="cc-btn cc-btn-primary">
                        <i class="fas fa-plus"></i>
                        <span class="cc-btn-label">Buat Absensi</span>
                    </a>
                </div>
            </div>
        </div>
    </header>


    {{-- ═══════════════════════════════════════════════════════════
         MAIN CONTAINER
    ════════════════════════════════════════════════════════════ --}}
    <main class="cc-main">

        {{-- ─────────────────────────────────────────────────────────
             SECTION: PERLU PERHATIAN
        ────────────────────────────────────────────────────────── --}}
        <section class="cc-section">
            @if(count($alerts) > 0)
                <div class="cc-alert-panel">
                    <div class="cc-alert-head">
                        <div class="cc-alert-head-left">
                            <div class="cc-alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h2 class="cc-alert-title">Perlu Perhatian</h2>
                                <p class="cc-alert-sub">
                                    Sistem mendeteksi <strong>{{ count($alerts) }}</strong> kondisi yang membutuhkan tindakan
                                </p>
                            </div>
                        </div>
                        <span class="cc-alert-tag">AUTO-DETECTED</span>
                    </div>

                    <div class="cc-alert-list">
                        @foreach(array_slice($alerts, 0, 6) as $alert)
                            @php
                                $lvl = match($alert['level']) {
                                    'urgent'  => ['cls' => 'urgent',  'icon' => 'fa-circle-exclamation',    'label' => 'URGENT'],
                                    'warning' => ['cls' => 'warning', 'icon' => 'fa-triangle-exclamation',  'label' => 'PERHATIAN'],
                                    default   => ['cls' => 'info',    'icon' => 'fa-circle-info',           'label' => 'INFO'],
                                };
                            @endphp
                            <div class="cc-alert-item cc-alert-{{ $lvl['cls'] }}">
                                <div class="cc-alert-item-icon">
                                    <i class="fas {{ $lvl['icon'] }}"></i>
                                </div>
                                <div class="cc-alert-item-body">
                                    <div class="cc-alert-item-head">
                                        <span class="cc-alert-item-tag cc-alert-item-tag-{{ $lvl['cls'] }}">
                                            {{ $lvl['label'] }}
                                        </span>
                                        <span class="cc-alert-item-title">
                                            {{ $alert['session_title'] }}
                                        </span>
                                    </div>
                                    <p class="cc-alert-item-text">{{ $alert['text'] }}</p>
                                </div>
                                <a href="{{ route('owner.attendance.show', $alert['session_id']) }}"
                                   class="cc-alert-item-action">
                                    <span>Buka</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach

                        @if(count($alerts) > 6)
                            <div class="cc-alert-more">
                                + {{ count($alerts) - 6 }} kondisi lain terdeteksi
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="cc-all-good">
                    <div class="cc-all-good-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="cc-all-good-title">Semua Sistem Normal</h3>
                        <p class="cc-all-good-sub">
                            Tidak ada kondisi yang membutuhkan perhatian khusus saat ini.
                        </p>
                    </div>
                </div>
            @endif
        </section>


        {{-- ─────────────────────────────────────────────────────────
             SECTION: STATS
        ────────────────────────────────────────────────────────── --}}
        <section class="cc-section">
            @php
                $stats_items = [
                    ['k' => 'total',     'label' => 'Sesi Hari Ini', 'icon' => 'fa-calendar-day', 'tone' => 'cyan'],
                    ['k' => 'aktif',     'label' => 'Absensi Aktif', 'icon' => 'fa-play',         'tone' => 'emerald'],
                    ['k' => 'scheduled', 'label' => 'Terjadwal',     'icon' => 'fa-clock',        'tone' => 'blue'],
                    ['k' => 'selesai',   'label' => 'Selesai',       'icon' => 'fa-check-double', 'tone' => 'indigo'],
                    ['k' => 'hadir',     'label' => 'Total Hadir',   'icon' => 'fa-user-check',   'tone' => 'green'],
                    ['k' => 'target',    'label' => 'Total Target',  'icon' => 'fa-users',        'tone' => 'purple'],
                ];
            @endphp

            <div class="cc-stats-grid">
                @foreach($stats_items as $item)
                    <div class="cc-stat-card cc-tone-{{ $item['tone'] }}">
                        <div class="cc-stat-body">
                            <p class="cc-stat-label">{{ $item['label'] }}</p>
                            <p class="cc-stat-value">{{ $stats[$item['k']] }}</p>
                        </div>
                        <div class="cc-stat-icon">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        {{-- ─────────────────────────────────────────────────────────
             SECTION: SESSION CARDS (SIMPLIFIED + AUTO-RESPONSIVE)
        ────────────────────────────────────────────────────────── --}}
        <section class="cc-section">
            <div class="cc-session-head">
                <div>
                    <h2 class="cc-session-title">Sesi Absensi</h2>
                    <p class="cc-session-sub">
                        {{ $sessions->total() }} sesi ditemukan
                        <span class="cc-device-hint">
                            · Tampilan: <strong x-text="layoutMode"></strong>
                        </span>
                    </p>
                </div>

                <div class="cc-legend">
                    <span class="cc-legend-item cc-legend-emerald">
                        <span class="cc-legend-dot"></span> Aktif
                    </span>
                    <span class="cc-legend-item cc-legend-blue">
                        <span class="cc-legend-dot"></span> Terjadwal
                    </span>
                    <span class="cc-legend-item cc-legend-amber">
                        <span class="cc-legend-dot"></span> Segera Tutup
                    </span>
                </div>
            </div>

            {{-- Grid: 1 kolom di HP/Tablet, 4 kolom di laptop+ / TV --}}
            <div class="cc-session-grid" :class="'cc-grid-' + layoutMode">
                @forelse($sessions as $session)
                    @php
                        $s  = $session;
                        $ts = $s->time_status;

                        $ts_theme = match($ts) {
                            'open'                 => ['tone' => 'emerald', 'label' => 'ABSENSI AKTIF',  'live' => true],
                            'opening_soon'         => ['tone' => 'cyan',    'label' => 'SEGERA DIBUKA',  'live' => false],
                            'closing_soon'         => ['tone' => 'amber',   'label' => 'SEGERA DITUTUP', 'live' => true],
                            'upcoming'             => ['tone' => 'blue',    'label' => 'AKAN DATANG',    'live' => false],
                            'closed_event_running' => ['tone' => 'violet',  'label' => 'ACARA BERJALAN', 'live' => false],
                            'event_ended'          => ['tone' => 'slate',   'label' => 'SELESAI',        'live' => false],
                            'cancelled'            => ['tone' => 'rose',    'label' => 'DIBATALKAN',     'live' => false],
                            'unscheduled'          => ['tone' => 'slate',   'label' => 'BELUM DIJADWAL','live' => false],
                            default                => ['tone' => 'slate',   'label' => strtoupper($ts),  'live' => false],
                        };

                        // Tanggal awal & akhir (dari scheduled_start_at dan attendance_close_at)
                        $startDate = $s->scheduled_start_at?->translatedFormat('d M Y') 
                                     ?? $s->attendance_open_at?->translatedFormat('d M Y') 
                                     ?? '-';
                        $endDate   = $s->scheduled_end_at?->translatedFormat('d M Y') 
                                     ?? $s->attendance_close_at?->translatedFormat('d M Y') 
                                     ?? $s->scheduled_start_at?->translatedFormat('d M Y')
                                     ?? '-';
                    @endphp

                    <article class="cc-session-card cc-tone-{{ $ts_theme['tone'] }}">

                        {{-- Top accent bar --}}
                        <div class="cc-session-bar"></div>

                        {{-- HEAD: Status + Type --}}
                        <div class="cc-session-head-row">
                            <span class="cc-session-status cc-status-{{ $ts_theme['tone'] }}">
                                @if($ts_theme['live'])
                                    <span class="cc-status-pulse"></span>
                                @endif
                                {{ $ts_theme['label'] }}
                            </span>
                            <span class="cc-session-type">
                                {{ strtoupper($s->attendance_type) }}
                            </span>
                        </div>

                        {{-- NAMA ABSENSI --}}
                        <h3 class="cc-session-name" title="{{ $s->title }}">
                            {{ $s->title }}
                        </h3>

                        {{-- INFO LIST: Ruangan, Jadwal, Tanggal --}}
                        <ul class="cc-info-list">
                            {{-- Nama Ruangan --}}
                            <li class="cc-info-item">
                                <span class="cc-info-icon cc-info-icon-room">
                                    <i class="fas fa-door-open"></i>
                                </span>
                                <span class="cc-info-body">
                                    <span class="cc-info-label">Ruangan</span>
                                    <span class="cc-info-value">
                                        {{ $s->room?->name ?? 'Online / Tidak ditentukan' }}
                                    </span>
                                </span>
                            </li>

                            {{-- Nama Jadwal --}}
                            <li class="cc-info-item">
                                <span class="cc-info-icon cc-info-icon-schedule">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                                <span class="cc-info-body">
                                    <span class="cc-info-label">Jadwal</span>
                                    <span class="cc-info-value">
                                        {{ $s->schedule?->title ?? $s->title ?? '-' }}
                                    </span>
                                </span>
                            </li>

                            {{-- Tanggal Awal - Tanggal Akhir --}}
                            <li class="cc-info-item">
                                <span class="cc-info-icon cc-info-icon-date">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <span class="cc-info-body">
                                    <span class="cc-info-label">Tanggal</span>
                                    <span class="cc-info-value cc-info-date">
                                        <span>{{ $startDate }}</span>
                                        <span class="cc-info-sep">→</span>
                                        <span>{{ $endDate }}</span>
                                    </span>
                                </span>
                            </li>
                        </ul>

                        {{-- ACTIONS --}}
                        <div class="cc-session-actions">
                            <a href="{{ route('owner.attendance.show', $s) }}"
                               class="cc-action cc-action-ghost">
                                <i class="fas fa-eye"></i>
                                <span>Detail</span>
                            </a>

                            @if($ts === 'open' || $ts === 'closing_soon')
                                <a href="{{ route('owner.attendance.monitor', $s) }}"
                                   class="cc-action cc-action-violet">
                                    <i class="fas fa-broadcast-tower"></i>
                                    <span>Live</span>
                                </a>
                            @elseif($s->session_status === 'scheduled')
                                <button type="button"
                                        @click="openNow({{ $s->id }}, @js($s->title), {{ $s->state_version ?? 1 }})"
                                        class="cc-action cc-action-emerald">
                                    <i class="fas fa-play"></i>
                                    <span>Buka</span>
                                </button>
                            @endif
                        </div>
                    </article>

                @empty
                    <div class="cc-empty">
                        <div class="cc-empty-icon">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <h3 class="cc-empty-title">Belum ada sesi absensi</h3>
                        <p class="cc-empty-sub">
                            Buat sesi absensi baru untuk mulai melacak kehadiran anggota.
                        </p>
                        <a href="{{ route('owner.attendance.create') }}"
                           class="cc-btn cc-btn-primary cc-btn-lg">
                            <i class="fas fa-plus"></i>
                            <span>Buat Absensi</span>
                        </a>
                    </div>
                @endforelse
            </div>

            @if(method_exists($sessions, 'links') && $sessions->hasPages())
                <div class="cc-pagination">{{ $sessions->links() }}</div>
            @endif
        </section>
    </main>
</div>


{{-- ════════════════════════════════════════════════════════════════
     STYLES
════════════════════════════════════════════════════════════════ --}}
<style>
/* ───────────────────────────────
   LAYOUT CONTAINER (fluid, max-width)
─────────────────────────────── */
.attendance-cc {
    --cc-max: 100%;
    --cc-gap: 1rem;
    --cc-radius: 1rem;
    --cc-radius-sm: 0.75rem;
}

@media (min-width: 768px) {
    .attendance-cc { --cc-gap: 1.25rem; }
}

/* Lebar maksimum adaptif: di TV/layar super lebar tetap nyaman */
@media (min-width: 1920px) { .attendance-cc { --cc-max: 1800px; } }
@media (min-width: 2560px) { .attendance-cc { --cc-max: 2200px; } }

.cc-container,
.cc-main {
    width: 100%;
    max-width: var(--cc-max);
    margin-inline: auto;
    padding-inline: clamp(0.875rem, 3vw, 2.5rem);
}

.cc-main {
    padding-block: 1.25rem 3rem;
    display: flex;
    flex-direction: column;
    gap: var(--cc-gap);
}

.cc-section {
    display: flex;
    flex-direction: column;
    gap: var(--cc-gap);
}

/* ───────────────────────────────
   HEADER — NON-STICKY
   Header akan tertutup saat user scroll ke bawah.
─────────────────────────────── */
.cc-header {
    position: relative;          /* TIDAK sticky */
    z-index: 1;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: saturate(180%) blur(16px);
    -webkit-backdrop-filter: saturate(180%) blur(16px);
    border-bottom: 1px solid rgb(226 232 240);
}
.dark .cc-header {
    background: rgba(15, 23, 42, 0.92);
    border-bottom-color: rgb(30 41 59);
}

.cc-header-inner {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-block: 0.875rem;
}

@media (min-width: 1024px) {
    .cc-header-inner {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding-block: 1rem;
        gap: 1.5rem;
    }
}

.cc-header-left {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    min-width: 0;
    flex: 1;
}

.cc-icon-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.875rem;
    background: linear-gradient(135deg, #06b6d4 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 10px 15px -3px rgb(6 182 212 / 0.25),
                0 4px 6px -4px rgb(6 182 212 / 0.2);
    font-size: 1.125rem;
}

.cc-header-text { min-width: 0; flex: 1; }

.cc-header-title-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.cc-title {
    font-size: 1.125rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: rgb(15 23 42);
    line-height: 1.3;
}
.dark .cc-title { color: white; }

@media (min-width: 640px) { .cc-title { font-size: 1.25rem; } }

.cc-live-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.1875rem 0.625rem;
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    border-radius: 9999px;
    background: rgb(209 250 229);
    color: rgb(4 120 87);
}
.dark .cc-live-badge {
    background: rgb(16 185 129 / 0.1);
    color: rgb(52 211 153);
}

.cc-device-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3125rem;
    padding: 0.1875rem 0.625rem;
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    border-radius: 9999px;
    background: rgb(237 233 254);
    color: rgb(91 33 182);
    text-transform: uppercase;
}
.dark .cc-device-badge {
    background: rgb(139 92 246 / 0.15);
    color: rgb(196 181 253);
}
.cc-device-badge i { font-size: 0.625rem; }

.cc-pulse-dot {
    position: relative;
    display: inline-flex;
    width: 0.375rem;
    height: 0.375rem;
}
.cc-pulse-ring {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: rgb(16 185 129);
    animation: cc-ping 1.6s cubic-bezier(0, 0, 0.2, 1) infinite;
    opacity: 0.75;
}
.cc-pulse-core {
    position: relative;
    display: inline-flex;
    width: 100%;
    height: 100%;
    border-radius: 9999px;
    background: rgb(16 185 129);
}
@keyframes cc-ping {
    75%, 100% { transform: scale(2.2); opacity: 0; }
}

.cc-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 0.75rem;
    margin-top: 0.375rem;
    font-size: 0.75rem;
    color: rgb(100 116 139);
}
.dark .cc-breadcrumb { color: rgb(148 163 184); }

.cc-bc-item { color: rgb(100 116 139); }
.cc-bc-sep { color: rgb(203 213 225); }
.cc-bc-current { font-weight: 600; color: rgb(51 65 85); }
.dark .cc-bc-current { color: rgb(226 232 240); }

.cc-bc-divider {
    display: none;
    width: 1px;
    height: 0.75rem;
    background: rgb(203 213 225);
}
@media (min-width: 640px) { .cc-bc-divider { display: inline-block; } }
.dark .cc-bc-divider { background: rgb(51 65 85); }

.cc-clock,
.cc-sync {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.cc-clock-time {
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-weight: 600;
    color: rgb(51 65 85);
    font-variant-numeric: tabular-nums;
}
.dark .cc-clock-time { color: rgb(226 232 240); }

.cc-clock i { color: rgb(6 182 212); font-size: 0.6875rem; }

.cc-sync-dot {
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 9999px;
    background: rgb(148 163 184);
}
.cc-sync-synced   { background: rgb(16 185 129); }
.cc-sync-drifting { background: rgb(245 158 11); animation: cc-pulse-slow 1.5s infinite; }
.cc-sync-offline  { background: rgb(244 63 94); animation: cc-pulse-slow 1.5s infinite; }
.cc-sync-pending  { background: rgb(148 163 184); }
@keyframes cc-pulse-slow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.cc-sync-label {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgb(100 116 139);
}
.cc-sync-drift {
    font-size: 0.625rem;
    color: rgb(148 163 184);
}

.cc-header-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}

/* ───────────────────────────────
   BUTTONS
─────────────────────────────── */
.cc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    font-size: 0.8125rem;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.18s ease;
    cursor: pointer;
    border: 1px solid transparent;
    white-space: nowrap;
}
.cc-btn i { font-size: 0.75rem; }

.cc-btn-ghost {
    background: white;
    color: rgb(51 65 85);
    border-color: rgb(226 232 240);
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.03);
}
.cc-btn-ghost:hover {
    background: rgb(248 250 252);
    border-color: rgb(203 213 225);
}
.dark .cc-btn-ghost {
    background: rgb(30 41 59);
    color: rgb(226 232 240);
    border-color: rgb(51 65 85);
}
.dark .cc-btn-ghost:hover { background: rgb(51 65 85); }

.cc-btn-primary {
    background: linear-gradient(135deg, #06b6d4 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 10px 15px -3px rgb(6 182 212 / 0.25),
                0 4px 6px -4px rgb(6 182 212 / 0.2);
}
.cc-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 20px -3px rgb(6 182 212 / 0.35);
}

.cc-btn-lg {
    padding: 0.6875rem 1.25rem;
    font-size: 0.875rem;
}

.cc-btn-label { display: inline; }
@media (max-width: 480px) {
    .cc-btn-label { display: none; }
    .cc-btn { padding: 0.5rem 0.75rem; }
}

/* ───────────────────────────────
   ALERT PANEL
─────────────────────────────── */
.cc-alert-panel {
    border-radius: var(--cc-radius);
    border: 1px solid rgb(226 232 240);
    background: white;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04);
}
.dark .cc-alert-panel {
    background: rgb(15 23 42);
    border-color: rgb(30 41 59);
}

.cc-alert-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.875rem 1.125rem;
    border-bottom: 1px solid rgb(226 232 240);
    background: linear-gradient(90deg, #fffbeb 0%, #fff1f2 100%);
    flex-wrap: wrap;
}
.dark .cc-alert-head {
    border-bottom-color: rgb(30 41 59);
    background: linear-gradient(90deg, rgb(245 158 11 / 0.05) 0%, rgb(244 63 94 / 0.05) 100%);
}

.cc-alert-head-left {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    min-width: 0;
}

.cc-alert-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 0.625rem;
    background: linear-gradient(135deg, #f59e0b 0%, #f43f5e 100%);
    color: white;
    box-shadow: 0 4px 10px -2px rgb(245 158 11 / 0.35);
    font-size: 0.75rem;
}

.cc-alert-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: rgb(15 23 42);
    line-height: 1.3;
}
.dark .cc-alert-title { color: white; }

.cc-alert-sub {
    font-size: 0.6875rem;
    color: rgb(100 116 139);
    margin-top: 0.125rem;
}
.cc-alert-sub strong { color: rgb(51 65 85); }
.dark .cc-alert-sub strong { color: rgb(226 232 240); }

.cc-alert-tag {
    padding: 0.1875rem 0.625rem;
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    border-radius: 9999px;
    background: rgb(255 255 255 / 0.8);
    color: rgb(71 85 105);
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.03);
}
.dark .cc-alert-tag {
    background: rgb(30 41 59);
    color: rgb(203 213 225);
}

.cc-alert-list { display: flex; flex-direction: column; }

.cc-alert-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 1.125rem;
    border-left: 4px solid;
    transition: background 0.15s ease;
}
.cc-alert-item + .cc-alert-item { border-top: 1px solid rgb(241 245 249); }
.dark .cc-alert-item + .cc-alert-item { border-top-color: rgb(30 41 59); }

.cc-alert-item:hover { background: rgb(248 250 252); }
.dark .cc-alert-item:hover { background: rgb(30 41 59 / 0.5); }

.cc-alert-urgent  { border-left-color: rgb(244 63 94); background: rgb(255 241 242 / 0.6); }
.cc-alert-warning { border-left-color: rgb(245 158 11); background: rgb(255 251 235 / 0.6); }
.cc-alert-info    { border-left-color: rgb(59 130 246); background: rgb(239 246 255 / 0.6); }

.dark .cc-alert-urgent  { background: rgb(244 63 94 / 0.05); }
.dark .cc-alert-warning { background: rgb(245 158 11 / 0.05); }
.dark .cc-alert-info    { background: rgb(59 130 246 / 0.05); }

.cc-alert-item-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
}
.cc-alert-urgent  .cc-alert-item-icon { background: rgb(254 226 226); color: rgb(220 38 38); }
.cc-alert-warning .cc-alert-item-icon { background: rgb(254 243 199); color: rgb(217 119 6); }
.cc-alert-info    .cc-alert-item-icon { background: rgb(219 234 254); color: rgb(37 99 235); }

.dark .cc-alert-urgent  .cc-alert-item-icon { background: rgb(244 63 94 / 0.15); color: rgb(251 113 133); }
.dark .cc-alert-warning .cc-alert-item-icon { background: rgb(245 158 11 / 0.15); color: rgb(251 191 36); }
.dark .cc-alert-info    .cc-alert-item-icon { background: rgb(59 130 246 / 0.15); color: rgb(96 165 250); }

.cc-alert-item-body { min-width: 0; flex: 1; }

.cc-alert-item-head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.375rem 0.5rem;
    margin-bottom: 0.25rem;
}

.cc-alert-item-tag {
    padding: 0.125rem 0.4375rem;
    font-size: 0.5625rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    border-radius: 0.25rem;
}
.cc-alert-item-tag-urgent  { background: rgb(254 226 226); color: rgb(185 28 28); }
.cc-alert-item-tag-warning { background: rgb(254 243 199); color: rgb(180 83 9); }
.cc-alert-item-tag-info    { background: rgb(219 234 254); color: rgb(29 78 216); }

.dark .cc-alert-item-tag-urgent  { background: rgb(244 63 94 / 0.15); color: rgb(252 165 165); }
.dark .cc-alert-item-tag-warning { background: rgb(245 158 11 / 0.15); color: rgb(252 211 77); }
.dark .cc-alert-item-tag-info    { background: rgb(59 130 246 / 0.15); color: rgb(147 197 253); }

.cc-alert-item-title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: rgb(30 41 59);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
}
.dark .cc-alert-item-title { color: rgb(226 232 240); }

.cc-alert-item-text {
    font-size: 0.75rem;
    color: rgb(71 85 105);
    line-height: 1.5;
}
.dark .cc-alert-item-text { color: rgb(148 163 184); }

.cc-alert-item-action {
    display: inline-flex;
    align-items: center;
    gap: 0.3125rem;
    flex-shrink: 0;
    align-self: center;
    padding: 0.375rem 0.625rem;
    font-size: 0.6875rem;
    font-weight: 700;
    color: rgb(51 65 85);
    background: white;
    border: 1px solid rgb(226 232 240);
    border-radius: 0.5rem;
    transition: all 0.15s ease;
}
.cc-alert-item-action:hover { background: rgb(248 250 252); }
.dark .cc-alert-item-action {
    background: rgb(30 41 59);
    color: rgb(226 232 240);
    border-color: rgb(51 65 85);
}
.dark .cc-alert-item-action:hover { background: rgb(51 65 85); }
.cc-alert-item-action i { font-size: 0.5625rem; }

.cc-alert-more {
    padding: 0.75rem 1.125rem;
    text-align: center;
    font-size: 0.75rem;
    font-weight: 500;
    color: rgb(100 116 139);
    background: rgb(248 250 252);
}
.dark .cc-alert-more {
    background: rgb(30 41 59 / 0.5);
    color: rgb(148 163 184);
}

/* ───────────────────────────────
   ALL GOOD PANEL
─────────────────────────────── */
.cc-all-good {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 1rem 1.25rem;
    border-radius: var(--cc-radius);
    border: 1px solid rgb(167 243 208);
    background: linear-gradient(90deg, #ecfdf5 0%, #f0fdfa 100%);
}
.dark .cc-all-good {
    border-color: rgb(16 185 129 / 0.2);
    background: linear-gradient(90deg, rgb(16 185 129 / 0.06) 0%, rgb(20 184 166 / 0.06) 100%);
}

.cc-all-good-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    background: rgb(16 185 129);
    color: white;
    box-shadow: 0 8px 15px -3px rgb(16 185 129 / 0.3);
}

.cc-all-good-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: rgb(6 78 59);
}
.dark .cc-all-good-title { color: rgb(110 231 183); }

.cc-all-good-sub {
    font-size: 0.75rem;
    color: rgb(6 95 70 / 0.75);
    margin-top: 0.125rem;
}
.dark .cc-all-good-sub { color: rgb(167 243 208 / 0.7); }

/* ───────────────────────────────
   STATS GRID
─────────────────────────────── */
.cc-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
}

@media (min-width: 640px) {
    .cc-stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (min-width: 1280px) {
    .cc-stats-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); }
}

.cc-stat-card {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: white;
    border: 1px solid rgb(226 232 240);
    border-radius: var(--cc-radius);
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    min-width: 0;
}
.cc-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgb(0 0 0 / 0.08);
}
.dark .cc-stat-card {
    background: rgb(15 23 42);
    border-color: rgb(30 41 59);
}

.cc-stat-body { min-width: 0; flex: 1; }

.cc-stat-label {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: rgb(100 116 139);
    margin-bottom: 0.25rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cc-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: rgb(15 23 42);
    font-variant-numeric: tabular-nums;
}
.dark .cc-stat-value { color: white; }

.cc-stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    transition: transform 0.2s ease;
}
.cc-stat-card:hover .cc-stat-icon { transform: scale(1.08); }

.cc-tone-cyan    .cc-stat-icon { background: rgb(207 250 254); color: rgb(8 145 178); }
.cc-tone-emerald .cc-stat-icon { background: rgb(209 250 229); color: rgb(5 150 105); }
.cc-tone-blue    .cc-stat-icon { background: rgb(219 234 254); color: rgb(37 99 235); }
.cc-tone-indigo  .cc-stat-icon { background: rgb(224 231 255); color: rgb(79 70 229); }
.cc-tone-green   .cc-stat-icon { background: rgb(220 252 231); color: rgb(22 163 74); }
.cc-tone-purple  .cc-stat-icon { background: rgb(243 232 255); color: rgb(147 51 234); }
.cc-tone-violet  .cc-stat-icon { background: rgb(237 233 254); color: rgb(124 58 237); }
.cc-tone-amber   .cc-stat-icon { background: rgb(254 243 199); color: rgb(217 119 6); }
.cc-tone-rose    .cc-stat-icon { background: rgb(254 226 226); color: rgb(225 29 72); }
.cc-tone-slate   .cc-stat-icon { background: rgb(226 232 240); color: rgb(71 85 105); }

.dark .cc-tone-cyan    .cc-stat-icon { background: rgb(6 182 212 / 0.12); color: rgb(34 211 238); }
.dark .cc-tone-emerald .cc-stat-icon { background: rgb(16 185 129 / 0.12); color: rgb(52 211 153); }
.dark .cc-tone-blue    .cc-stat-icon { background: rgb(59 130 246 / 0.12); color: rgb(96 165 250); }
.dark .cc-tone-indigo  .cc-stat-icon { background: rgb(99 102 241 / 0.12); color: rgb(129 140 248); }
.dark .cc-tone-green   .cc-stat-icon { background: rgb(34 197 94 / 0.12);  color: rgb(74 222 128); }
.dark .cc-tone-purple  .cc-stat-icon { background: rgb(168 85 247 / 0.12); color: rgb(192 132 252); }
.dark .cc-tone-violet  .cc-stat-icon { background: rgb(139 92 246 / 0.12); color: rgb(167 139 250); }
.dark .cc-tone-amber   .cc-stat-icon { background: rgb(245 158 11 / 0.12); color: rgb(251 191 36); }
.dark .cc-tone-rose    .cc-stat-icon { background: rgb(244 63 94 / 0.12);  color: rgb(251 113 133); }
.dark .cc-tone-slate   .cc-stat-icon { background: rgb(71 85 105 / 0.3);   color: rgb(148 163 184); }

/* ───────────────────────────────
   SESSION SECTION HEAD
─────────────────────────────── */
.cc-session-head {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

@media (min-width: 640px) {
    .cc-session-head {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.cc-session-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: rgb(15 23 42);
}
.dark .cc-session-title { color: white; }

.cc-session-sub {
    font-size: 0.75rem;
    color: rgb(100 116 139);
    margin-top: 0.125rem;
}

.cc-device-hint {
    color: rgb(100 116 139);
}
.cc-device-hint strong {
    color: rgb(6 182 212);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.cc-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    font-size: 0.75rem;
}

.cc-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.625rem;
    font-weight: 600;
    border-radius: 9999px;
}

.cc-legend-emerald { background: rgb(236 253 245); color: rgb(5 122 85); }
.cc-legend-blue    { background: rgb(239 246 255); color: rgb(29 78 216); }
.cc-legend-amber   { background: rgb(255 251 235); color: rgb(180 83 9); }

.dark .cc-legend-emerald { background: rgb(16 185 129 / 0.1); color: rgb(52 211 153); }
.dark .cc-legend-blue    { background: rgb(59 130 246 / 0.1); color: rgb(96 165 250); }
.dark .cc-legend-amber   { background: rgb(245 158 11 / 0.1); color: rgb(251 191 36); }

.cc-legend-dot {
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 9999px;
}
.cc-legend-emerald .cc-legend-dot { background: rgb(16 185 129); }
.cc-legend-blue    .cc-legend-dot { background: rgb(59 130 246); }
.cc-legend-amber   .cc-legend-dot { background: rgb(245 158 11); }

/* ───────────────────────────────
   ★ SESSION GRID — AUTO RESPONSIVE
   • HP / Tablet ( < 1024px )  → 1 kolom
   • Laptop ( ≥ 1024px )       → 4 kolom
   • Monitor besar ( ≥ 1920 )  → 4 kolom (lebih lega)
   • TV ( ≥ 2560px )           → 4 kolom (lebar maks 2200px)
─────────────────────────────── */
.cc-session-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 1024px) {
    .cc-session-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.125rem;
    }
}

@media (min-width: 1920px) {
    .cc-session-grid { gap: 1.5rem; }
}

/* Layout mode classes (dikontrol Alpine, hanya sebagai fallback) */
.cc-grid-mobile,
.cc-grid-tablet { grid-template-columns: 1fr; }

.cc-grid-laptop,
.cc-grid-monitor,
.cc-grid-tv { grid-template-columns: repeat(4, minmax(0, 1fr)); }

@media (max-width: 1023px) {
    .cc-session-grid.cc-grid-laptop,
    .cc-session-grid.cc-grid-monitor,
    .cc-session-grid.cc-grid-tv {
        grid-template-columns: 1fr;
    }
}

/* ───────────────────────────────
   ★ SESSION CARD — SIMPLIFIED
─────────────────────────────── */
.cc-session-card {
    position: relative;
    display: flex;
    flex-direction: column;
    background: white;
    border: 1px solid rgb(226 232 240);
    border-radius: var(--cc-radius);
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.cc-session-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px -8px rgb(0 0 0 / 0.1);
}
.dark .cc-session-card {
    background: rgb(15 23 42);
    border-color: rgb(30 41 59);
}

.cc-session-bar {
    height: 0.3125rem;
    width: 100%;
    flex-shrink: 0;
}
.cc-tone-emerald .cc-session-bar { background: linear-gradient(90deg, #34d399, #10b981); }
.cc-tone-cyan    .cc-session-bar { background: linear-gradient(90deg, #22d3ee, #06b6d4); }
.cc-tone-amber   .cc-session-bar { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.cc-tone-blue    .cc-session-bar { background: linear-gradient(90deg, #60a5fa, #3b82f6); }
.cc-tone-violet  .cc-session-bar { background: linear-gradient(90deg, #a78bfa, #8b5cf6); }
.cc-tone-slate   .cc-session-bar { background: linear-gradient(90deg, #cbd5e1, #94a3b8); }
.cc-tone-rose    .cc-session-bar { background: linear-gradient(90deg, #fb7185, #f43f5e); }

/* Head: Status + Type */
.cc-session-head-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 1rem 1rem 0.5rem;
    flex-wrap: wrap;
}

.cc-session-status {
    display: inline-flex;
    align-items: center;
    gap: 0.3125rem;
    padding: 0.1875rem 0.5rem;
    font-size: 0.5625rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    border-radius: 9999px;
    color: white;
}
.cc-status-emerald { background: rgb(16 185 129); }
.cc-status-cyan    { background: rgb(6 182 212); }
.cc-status-amber   { background: rgb(245 158 11); }
.cc-status-blue    { background: rgb(59 130 246); }
.cc-status-violet  { background: rgb(139 92 246); }
.cc-status-slate   { background: rgb(100 116 139); }
.cc-status-rose    { background: rgb(244 63 94); }

.cc-status-pulse {
    width: 0.25rem;
    height: 0.25rem;
    border-radius: 9999px;
    background: white;
    animation: cc-pulse-slow 1.2s infinite;
}

.cc-session-type {
    font-size: 0.5625rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: rgb(148 163 184);
}

/* Nama Absensi */
.cc-session-name {
    padding: 0 1rem 0.75rem;
    font-size: 1rem;
    font-weight: 700;
    color: rgb(15 23 42);
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    min-height: 2.6em;
}
.dark .cc-session-name { color: white; }

/* Info List */
.cc-info-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0 1rem 1rem;
    flex: 1;
}

.cc-info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.625rem;
    padding: 0.5rem 0.625rem;
    border-radius: 0.625rem;
    background: rgb(248 250 252);
    min-width: 0;
}
.dark .cc-info-item { background: rgb(30 41 59 / 0.6); }

.cc-info-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.5rem;
    font-size: 0.6875rem;
    color: white;
}
.cc-info-icon-room     { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.cc-info-icon-schedule { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.cc-info-icon-date     { background: linear-gradient(135deg, #f59e0b, #d97706); }

.cc-info-body {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
    line-height: 1.3;
}

.cc-info-label {
    font-size: 0.5625rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: rgb(148 163 184);
    margin-bottom: 0.0625rem;
}

.cc-info-value {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgb(30 41 59);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.dark .cc-info-value { color: rgb(226 232 240); }

.cc-info-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
    white-space: normal;
    font-variant-numeric: tabular-nums;
    font-size: 0.6875rem;
}

.cc-info-sep {
    color: rgb(6 182 212);
    font-weight: 700;
}

/* Actions */
.cc-session-actions {
    display: flex;
    gap: 0.5rem;
    padding: 0.75rem 1rem 1rem;
    border-top: 1px solid rgb(241 245 249);
    margin-top: auto;
}
.dark .cc-session-actions { border-top-color: rgb(30 41 59); }

.cc-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    flex: 1;
    padding: 0.5rem 0.75rem;
    font-size: 0.6875rem;
    font-weight: 700;
    border-radius: 0.625rem;
    transition: all 0.15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    white-space: nowrap;
}
.cc-action i { font-size: 0.6875rem; }

.cc-action-ghost {
    background: white;
    color: rgb(51 65 85);
    border-color: rgb(226 232 240);
}
.cc-action-ghost:hover { background: rgb(248 250 252); }
.dark .cc-action-ghost {
    background: transparent;
    color: rgb(226 232 240);
    border-color: rgb(51 65 85);
}
.dark .cc-action-ghost:hover { background: rgb(30 41 59); }

.cc-action-violet {
    background: linear-gradient(135deg, #a855f7 0%, #8b5cf6 100%);
    color: white;
}
.cc-action-violet:hover { opacity: 0.92; }

.cc-action-emerald {
    background: rgb(16 185 129);
    color: white;
}
.cc-action-emerald:hover { background: rgb(5 150 105); }

/* Empty state */
.cc-empty {
    grid-column: 1 / -1;
    padding: 4rem 1.5rem;
    text-align: center;
    background: white;
    border: 2px dashed rgb(226 232 240);
    border-radius: var(--cc-radius);
}
.dark .cc-empty {
    background: rgb(15 23 42);
    border-color: rgb(30 41 59);
}

.cc-empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
    border-radius: 1rem;
    background: rgb(236 254 255);
    color: rgb(6 182 212);
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
.dark .cc-empty-icon {
    background: rgb(6 182 212 / 0.1);
    color: rgb(34 211 238);
}

.cc-empty-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: rgb(15 23 42);
    margin-bottom: 0.5rem;
}
.dark .cc-empty-title { color: white; }

.cc-empty-sub {
    font-size: 0.875rem;
    color: rgb(100 116 139);
    max-width: 28rem;
    margin: 0 auto 1.25rem;
    line-height: 1.6;
}

/* Pagination */
.cc-pagination {
    margin-top: 1.5rem;
}
</style>


{{-- ════════════════════════════════════════════════════════════════
     ALPINE
════════════════════════════════════════════════════════════════ --}}
<script>
function attendanceCenter(config = {}) {
    return {
        serverOffsetMs: 0,
        syncStatus: 'pending',
        serverClock: '',
        driftMs: 0,
        refreshInterval: config.refreshInterval ?? 60000,

        // ─── Device auto-detection ───────────────────────────────
        layoutMode: 'desktop',
        deviceLabel: 'Desktop',
        deviceIcon: 'fa-desktop',
        viewportWidth: 0,

        async init() {
            this.detectDevice();
            window.addEventListener('resize', () => this.detectDevice());

            await this.syncClock();
            setInterval(() => this.tick(), 1000);
            setInterval(() => this.syncClock(), 20000);
            setInterval(() => this.softRefresh(), this.refreshInterval);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.syncClock();
            });
        },

        /**
         * Deteksi tipe device berdasarkan lebar viewport.
         * - mobile  : < 640px   → 1 kartu
         * - tablet  : 640-1023  → 1 kartu (sesuai permintaan)
         * - laptop  : 1024-1919 → 4 kartu
         * - monitor : 1920-2559 → 4 kartu
         * - tv      : ≥ 2560    → 4 kartu
         */
        detectDevice() {
            const w = window.innerWidth;
            this.viewportWidth = w;

            if (w < 640) {
                this.layoutMode  = 'mobile';
                this.deviceLabel = 'Mobile';
                this.deviceIcon  = 'fa-mobile-alt';
            } else if (w < 1024) {
                this.layoutMode  = 'tablet';
                this.deviceLabel = 'Tablet';
                this.deviceIcon  = 'fa-tablet-alt';
            } else if (w < 1920) {
                this.layoutMode  = 'laptop';
                this.deviceLabel = 'Laptop';
                this.deviceIcon  = 'fa-laptop';
            } else if (w < 2560) {
                this.layoutMode  = 'monitor';
                this.deviceLabel = 'Monitor';
                this.deviceIcon  = 'fa-desktop';
            } else {
                this.layoutMode  = 'tv';
                this.deviceLabel = 'TV / Ultra Wide';
                this.deviceIcon  = 'fa-tv';
            }
        },

        async syncClock() {
            const t0 = performance.now();
            try {
                const res = await fetch('{{ route('owner.system.time') }}', {
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json' },
                });
                const t1 = performance.now();
                if (!res.ok) throw new Error('sync failed');
                const data = await res.json();

                const latency = (t1 - t0) / 2;
                const serverNowMs = data.server_unix_ms + Math.round(t1 - latency);
                const clientNowMs = Date.now();

                this.serverOffsetMs = serverNowMs - clientNowMs;
                this.driftMs        = Math.abs(this.serverOffsetMs);
                this.syncStatus     = this.driftMs < 500  ? 'synced'
                                    : this.driftMs < 2000 ? 'drifting'
                                    : 'offline';
            } catch (e) {
                this.syncStatus = 'offline';
            }
            this.tick();
        },

        tick() {
            const d = new Date(Date.now() + this.serverOffsetMs);
            this.serverClock = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit',
                timeZone: 'Asia/Jakarta',
            }).format(d);
        },

        softRefresh() {
            if (document.hidden) return;
            window.location.reload();
        },

        refreshPage() { window.location.reload(); },

        async openNow(id, title, version = null) {
            if (!confirm(`Buka sesi "${title}" sekarang?`)) return;
            try {
                const res = await fetch(`/owner/attendance/${id}/open-now`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        client_unix_ms: Date.now() + this.serverOffsetMs,
                        state_version:  version,
                    }),
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Gagal.');
                window.location.reload();
            } catch (e) {
                alert(e.message);
            }
        },
    };
}
</script>

@endsection
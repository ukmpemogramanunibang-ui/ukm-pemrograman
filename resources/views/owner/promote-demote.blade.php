@extends('layouts.roles.owner')

@section('title', 'Naik/Turun Pangkat – Owner UNICODE')
@section('page-title', 'Naik / Turun Pangkat')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════
   GLOBAL
   ═══════════════════════════════════════════════════════ */
.promote-container {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.dark .promote-container { background: #1f2937; }

/* ═══════════════════════════════════════════════════════
   HEADER
   ═══════════════════════════════════════════════════════ */
.promote-header {
    display: flex; flex-wrap: wrap; justify-content: space-between;
    align-items: center; margin-bottom: 1.25rem; gap: 1rem;
}
.promote-header h3 {
    font-size: 1.25rem; font-weight: 600; color: #111827;
    display: flex; align-items: center; gap: 0.5rem;
}
.dark .promote-header h3 { color: #f3f4f6; }
.promote-header p { font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem; }
.dark .promote-header p { color: #9ca3af; }

/* ═══════════════════════════════════════════════════════
   LEGEND PANEL (collapsible)
   ═══════════════════════════════════════════════════════ */
.legend-panel {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    border: 1px solid #e2e8f0;
    animation: fadeInUp 0.3s ease;
}
.dark .legend-panel {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-color: #334155;
}
.legend-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 1rem; padding-bottom: 0.75rem;
    border-bottom: 2px solid rgba(99,102,241,0.15);
}
.legend-header h4 {
    font-size: 1rem; font-weight: 700;
    display: flex; align-items: center; gap: 0.5rem;
}
.dark .legend-header h4 { color: #f1f5f9; }
.legend-close-btn {
    background: transparent; border: none; cursor: pointer;
    color: #94a3b8; font-size: 1rem;
    padding: 0.25rem 0.5rem; border-radius: 0.375rem;
    transition: 0.15s;
}
.legend-close-btn:hover { background: rgba(239,68,68,0.1); color: #ef4444; }

.legend-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 0.75rem;
}
.legend-tier {
    padding: 0.85rem 1rem;
    border-radius: 0.625rem;
    background: white;
    border-left: 4px solid transparent;
    border-top: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    transition: 0.2s;
}
.legend-tier:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.dark .legend-tier { background: #0f172a; border-color: #334155; }
.tier-owner         { border-left-color: #f59e0b; }
.tier-superadmin    { border-left-color: #dc2626; }
.tier-ketua         { border-left-color: #d97706; }
.tier-pengurus-inti { border-left-color: #2563eb; }
.tier-pengurus      { border-left-color: #0891b2; }
.tier-anggota       { border-left-color: #16a34a; }
.tier-umum          { border-left-color: #64748b; }
.tier-pengunjung    { border-left-color: #94a3b8; }

.tier-badge { margin-bottom: 0.5rem; }
.tier-desc p {
    font-size: 0.82rem; line-height: 1.5;
    margin: 0.15rem 0; color: #334155;
}
.dark .tier-desc p { color: #cbd5e1; }

.tier-jabatan-list {
    display: flex; flex-wrap: wrap; gap: 0.25rem;
    margin: 0.4rem 0;
}
.jabatan-tag {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.15rem 0.5rem; border-radius: 9999px;
    font-size: 0.68rem; font-weight: 600;
    border: 1px solid;
}
.tag-inti     { background: rgba(37,99,235,0.1);  color: #2563eb; border-color: rgba(37,99,235,0.3); }
.tag-pj       { background: rgba(8,145,178,0.1);  color: #0891b2; border-color: rgba(8,145,178,0.3); }
.tag-anggota  { background: rgba(22,163,74,0.1);  color: #16a34a; border-color: rgba(22,163,74,0.3); }
.dark .tag-inti    { background: rgba(96,165,250,0.15); color: #93c5fd; border-color: rgba(96,165,250,0.4); }
.dark .tag-pj      { background: rgba(34,211,238,0.15); color: #67e8f9; border-color: rgba(34,211,238,0.4); }
.dark .tag-anggota { background: rgba(74,222,128,0.15); color: #86efac; border-color: rgba(74,222,128,0.4); }

.legend-footer {
    margin-top: 1rem; padding: 0.75rem 1rem;
    background: rgba(59,130,246,0.08);
    border-radius: 0.5rem; font-size: 0.8rem;
    display: flex; align-items: flex-start; gap: 0.5rem;
    color: #1e40af;
}
.dark .legend-footer { background: rgba(96,165,250,0.1); color: #93c5fd; }

/* ═══════════════════════════════════════════════════════
   STATS BAR
   ═══════════════════════════════════════════════════════ */
.stats-bar {
    display: flex; flex-wrap: wrap; gap: 1.5rem;
    padding: 0.5rem 1rem; background: #f9fafb;
    border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem;
}
.dark .stats-bar { background: #111827; }
.stats-bar .stat-item { display: flex; align-items: center; gap: 0.4rem; }
.stats-bar .stat-number { font-weight: 700; color: #6366f1; font-size: 1.1rem; }
.dark .stats-bar .stat-number { color: #818cf8; }

/* ═══════════════════════════════════════════════════════
   TABLE
   ═══════════════════════════════════════════════════════ */
.table-container { overflow-x: auto; margin-top: 0.5rem; }
.table-container table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-container th {
    text-align: left; padding: 0.6rem 0.75rem; font-weight: 600;
    text-transform: uppercase; font-size: 0.7rem; color: #6b7280;
    border-bottom: 1px solid #e5e7eb; background: #f9fafb;
    white-space: nowrap;
}
.dark .table-container th { color: #9ca3af; border-color: #374151; background: #1f2937; }
.table-container td {
    padding: 0.65rem 0.75rem; border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}
.dark .table-container td { border-color: #374151; color: #e5e7eb; }
.table-container tr:hover td { background: #f9fafb; }
.dark .table-container tr:hover td { background: #374151; }

/* ═══════════════════════════════════════════════════════
   LEVEL BADGES
   ═══════════════════════════════════════════════════════ */
.badge-level {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.2rem 0.65rem; border-radius: 9999px;
    font-size: 0.7rem; font-weight: 700;
    white-space: nowrap; letter-spacing: 0.02em;
    border: 1px solid transparent;
}
.badge-level-owner         { background: linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; border-color:#f59e0b; }
.badge-level-superadmin    { background: rgba(239,68,68,0.12);  color:#dc2626; border-color: rgba(239,68,68,0.3); }
.badge-level-ketua         { background: rgba(245,158,11,0.12); color:#d97706; border-color: rgba(245,158,11,0.3); }
.badge-level-pengurus      { background: rgba(59,130,246,0.12); color:#2563eb; border-color: rgba(59,130,246,0.3); }
.badge-level-pj            { background: rgba(6,182,212,0.12);  color:#0891b2; border-color: rgba(6,182,212,0.3); }
.badge-level-anggota       { background: rgba(34,197,94,0.12);  color:#16a34a; border-color: rgba(34,197,94,0.3); }
.badge-level-anggota-umum  { background: rgba(107,114,128,0.12);color:#4b5563; border-color: rgba(107,114,128,0.3); }
.badge-level-pengunjung    { background: rgba(100,116,139,0.12);color:#475569; border-color: rgba(100,116,139,0.3); }
.badge-level-default       { background: rgba(107,114,128,0.12);color:#4b5563; }

.dark .badge-level-owner        { background: rgba(245,158,11,0.15); color:#fbbf24; border-color:#f59e0b; }
.dark .badge-level-superadmin   { background: rgba(239,68,68,0.2);  color:#f87171; border-color: rgba(239,68,68,0.4); }
.dark .badge-level-ketua        { background: rgba(245,158,11,0.2); color:#fbbf24; border-color: rgba(245,158,11,0.4); }
.dark .badge-level-pengurus     { background: rgba(59,130,246,0.2); color:#60a5fa; border-color: rgba(59,130,246,0.4); }
.dark .badge-level-pj           { background: rgba(6,182,212,0.2);  color:#22d3ee; border-color: rgba(6,182,212,0.4); }
.dark .badge-level-anggota      { background: rgba(34,197,94,0.2);  color:#4ade80; border-color: rgba(34,197,94,0.4); }
.dark .badge-level-anggota-umum { background: rgba(107,114,128,0.2);color:#9ca3af; border-color: rgba(107,114,128,0.4); }
.dark .badge-level-pengunjung   { background: rgba(100,116,139,0.2);color:#94a3b8; border-color: rgba(100,116,139,0.4); }

/* ═══════════════════════════════════════════════════════
   NAME / JABATAN CELL
   ═══════════════════════════════════════════════════════ */
.name-cell { display: flex; align-items: center; gap: 0.4rem; max-width: 220px; }
.name-cell .name-text {
    font-weight: 500; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.row-info-btn {
    background: transparent; border: none;
    color: #9ca3af; cursor: pointer;
    padding: 0.15rem 0.35rem; border-radius: 0.35rem;
    font-size: 0.75rem; transition: 0.15s; flex-shrink: 0;
}
.row-info-btn:hover { color: #6366f1; background: rgba(99,102,241,0.1); }
.dark .row-info-btn:hover { color: #a5b4fc; background: rgba(129,140,248,0.15); }

.jabatan-cell { display: flex; flex-direction: column; gap: 0.25rem; align-items: flex-start; }
.jabatan-main {
    font-size: 0.85rem; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis; max-width: 150px;
    font-weight: 500;
}
.jabatan-more-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 0.2rem;
    background: rgba(99,102,241,0.12);
    color: #6366f1;
    border: 1px solid rgba(99,102,241,0.25);
    border-radius: 9999px;
    padding: 0.1rem 0.5rem;
    font-size: 0.68rem; font-weight: 700;
    cursor: pointer; transition: all 0.15s; white-space: nowrap;
}
.jabatan-more-btn:hover {
    background: #6366f1; color: white;
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(99,102,241,0.4);
}
.dark .jabatan-more-btn { background: rgba(129,140,248,0.2); color: #a5b4fc; border-color: rgba(129,140,248,0.4); }
.dark .jabatan-more-btn:hover { background: #818cf8; color: #111827; }
.jabatan-empty { color: #9ca3af; font-size: 0.85rem; font-style: italic; }

/* ═══════════════════════════════════════════════════════
   JABATAN CLASSIFICATION BADGES
   ═══════════════════════════════════════════════════════ */
.jabatan-primary-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.15rem 0.55rem; border-radius: 9999px;
    font-size: 0.65rem; font-weight: 700;
    letter-spacing: 0.02em;
}
.jabatan-primary-badge.inti {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af; border: 1px solid #60a5fa;
}
.jabatan-primary-badge.pj {
    background: linear-gradient(135deg, #cffafe, #a5f3fc);
    color: #155e75; border: 1px solid #22d3ee;
}
.jabatan-primary-badge.anggota {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534; border: 1px solid #4ade80;
}
.dark .jabatan-primary-badge.inti    { background: rgba(96,165,250,0.2); color: #93c5fd; border-color: #3b82f6; }
.dark .jabatan-primary-badge.pj      { background: rgba(34,211,238,0.2); color: #67e8f9; border-color: #06b6d4; }
.dark .jabatan-primary-badge.anggota { background: rgba(74,222,128,0.2); color: #86efac; border-color: #22c55e; }

/* ═══════════════════════════════════════════════════════
   MASA JABATAN CELL
   ═══════════════════════════════════════════════════════ */
.periode-cell { font-size: 0.75rem; line-height: 1.35; min-width: 120px; }
.periode-range {
    display: inline-flex; align-items: center; gap: 0.25rem;
    font-weight: 600; color: #1e293b;
}
.dark .periode-range { color: #e2e8f0; }
.periode-duration {
    display: block; font-size: 0.65rem;
    color: #64748b; margin-top: 0.1rem;
}
.periode-expired { color: #dc2626; font-weight: 700; font-size: 0.65rem; }
.periode-active  { color: #16a34a; font-weight: 700; font-size: 0.65rem; }
.periode-empty   { color: #94a3b8; font-style: italic; font-size: 0.75rem; }

/* ═══════════════════════════════════════════════════════
   PIN BADGES
   ═══════════════════════════════════════════════════════ */
.badge-pin {
    display: inline-flex; align-items: center; gap: 0.2rem;
    padding: 0.1rem 0.5rem; border-radius: 9999px;
    font-size: 0.62rem; font-weight: 700;
    background: rgba(34,197,94,0.12); color: #22c55e;
}
.dark .badge-pin { background: rgba(34,197,94,0.2); color: #4ade80; }
.badge-no-pin {
    background: rgba(239,68,68,0.12) !important;
    color: #ef4444 !important;
}
.dark .badge-no-pin { background: rgba(239,68,68,0.2) !important; color: #f87171 !important; }

/* ═══════════════════════════════════════════════════════
   CHECKBOX
   ═══════════════════════════════════════════════════════ */
.checkbox-column { width: 36px; text-align: center; }
.checkbox-column input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }

/* ═══════════════════════════════════════════════════════
   BUTTONS
   ═══════════════════════════════════════════════════════ */
.btn-pangkat {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white; padding: 0.4rem 1.2rem;
    border-radius: 0.5rem; font-weight: 600;
    border: none; cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 0.4rem;
    font-size: 0.85rem;
}
.btn-pangkat:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245,158,11,0.4);
}
.btn-pangkat:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.btn-outline {
    background: transparent; border: 1px solid #d1d5db;
    color: #4b5563; padding: 0.4rem 1.2rem;
    border-radius: 0.5rem; font-weight: 500;
    font-size: 0.85rem; cursor: pointer;
    transition: 0.2s; display: inline-flex;
    align-items: center; gap: 0.4rem;
}
.btn-outline:hover { background: #f3f4f6; border-color: #9ca3af; }
.dark .btn-outline { color: #e5e7eb; border-color: #4b5563; }
.dark .btn-outline:hover { background: #374151; }

.btn-simpan {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white; padding: 0.5rem 1.8rem;
    border-radius: 0.5rem; font-weight: 600;
    border: none; cursor: pointer; transition: 0.2s;
}
.btn-simpan:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(34,197,94,0.4);
}
.btn-simpan:disabled { opacity: 0.5; cursor: not-allowed; }

/* ═══════════════════════════════════════════════════════
   MODAL
   ═══════════════════════════════════════════════════════ */
.modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 9999; display: none;
    align-items: center; justify-content: center; padding: 1rem;
}
.modal-box {
    background: white; border-radius: 1rem;
    max-width: 680px; width: 100%;
    max-height: 90vh; overflow-y: auto;
    padding: 1.5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: fadeInUp 0.25s ease;
}
.dark .modal-box { background: #1f2937; color: #e5e7eb; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem; }
.form-control {
    width: 100%; padding: 0.5rem 1rem;
    border: 1px solid #d1d5db; border-radius: 0.5rem;
    background: white; font-size: 0.9rem; transition: 0.15s;
}
.dark .form-control { background: #374151; border-color: #4b5563; color: #e5e7eb; }
.form-control:focus {
    outline: none; border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

.selected-users-list {
    max-height: 140px; overflow-y: auto;
    background: #f3f4f6; padding: 0.4rem;
    border-radius: 0.5rem; display: flex;
    flex-wrap: wrap; gap: 0.3rem;
}
.dark .selected-users-list { background: #374151; }
.selected-users-list .user-badge {
    background: white; padding: 0.2rem 0.6rem;
    border-radius: 9999px; font-size: 0.8rem;
    border: 1px solid #e5e7eb;
}
.dark .selected-users-list .user-badge { background: #1f2937; border-color: #4b5563; }

.jabatan-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0.3rem; max-height: 160px; overflow-y: auto;
    padding: 0.4rem; background: #f9fafb; border-radius: 0.5rem;
}
.dark .jabatan-grid { background: #374151; }
.jabatan-grid label {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.85rem; cursor: pointer;
    padding: 0.2rem 0.4rem; border-radius: 0.25rem;
}
.jabatan-grid label:hover { background: #f3f4f6; }
.dark .jabatan-grid label:hover { background: #4b5563; }

/* ═══════════════════════════════════════════════════════
   PAGINATION
   ═══════════════════════════════════════════════════════ */
.pagination-controls { display: flex; flex-wrap: wrap; gap: 0.25rem; align-items: center; }
.pagination-controls button {
    padding: 0.3rem 0.7rem; border-radius: 0.375rem;
    border: 1px solid #e5e7eb; background: white;
    cursor: pointer; font-size: 0.8rem; transition: 0.15s;
}
.dark .pagination-controls button { background: #1f2937; border-color: #4b5563; color: #e5e7eb; }
.pagination-controls button:hover:not(:disabled) { background: #f3f4f6; }
.dark .pagination-controls button:hover:not(:disabled) { background: #374151; }
.pagination-controls .active { background: #6366f1; color: white; border-color: #6366f1; }
.dark .pagination-controls .active { background: #818cf8; border-color: #818cf8; color: #111827; }
.pagination-controls button:disabled { opacity: 0.4; cursor: not-allowed; }

/* ═══════════════════════════════════════════════════════
   TOAST
   ═══════════════════════════════════════════════════════ */
.toast-container {
    position: fixed; bottom: 1.5rem; right: 1.5rem;
    z-index: 99999; display: flex; flex-direction: column; gap: 0.5rem;
}
.toast {
    padding: 0.6rem 1.2rem; border-radius: 0.5rem;
    color: white; font-weight: 500; font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    animation: slideInRight 0.3s ease;
    display: flex; align-items: center; gap: 0.6rem; min-width: 200px;
}
.toast-success { background: #22c55e; }
.toast-error   { background: #ef4444; }
.toast-info    { background: #3b82f6; }
.toast .toast-close {
    background: none; border: none;
    color: rgba(255,255,255,0.7); cursor: pointer;
    font-size: 1.1rem; margin-left: auto; padding: 0 0.2rem;
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}

/* ═══════════════════════════════════════════════════════
   DETAIL CARD MODAL
   ═══════════════════════════════════════════════════════ */
.detail-card-modal .modal-box { max-width: 580px; }
.detail-header {
    display: flex; align-items: flex-start; gap: 1rem;
    padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1rem;
}
.dark .detail-header { border-color: #374151; }
.detail-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 700; font-size: 1.5rem;
    flex-shrink: 0; overflow: hidden;
    box-shadow: 0 4px 12px rgba(99,102,241,0.3);
}
.detail-avatar img { width: 100%; height: 100%; object-fit: cover; }
.detail-name {
    font-size: 1.05rem; font-weight: 700;
    color: #111827; margin: 0; line-height: 1.3;
}
.dark .detail-name { color: #f3f4f6; }
.detail-sub { font-size: 0.8rem; color: #6b7280; margin-top: 0.2rem; word-break: break-all; }
.dark .detail-sub { color: #9ca3af; }
.detail-badges { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
@media (max-width: 480px) { .detail-grid { grid-template-columns: 1fr; } }
.detail-row {
    display: flex; flex-direction: column;
    padding: 0.6rem 0.8rem;
    background: #f9fafb; border-radius: 0.5rem;
    border: 1px solid #f3f4f6;
}
.dark .detail-row { background: #111827; border-color: #374151; }
.detail-row.full { grid-column: 1 / -1; }
.detail-label {
    font-size: 0.62rem; text-transform: uppercase;
    letter-spacing: 0.06em; color: #6b7280;
    font-weight: 700; margin-bottom: 0.2rem;
}
.dark .detail-label { color: #9ca3af; }
.detail-value {
    font-size: 0.875rem; color: #111827;
    font-weight: 500; word-break: break-word;
}
.dark .detail-value { color: #e5e7eb; }

/* Detail jabatan item */
.jabatan-detail-item {
    display: flex; flex-direction: column; gap: 0.35rem;
    padding: 0.6rem 0.75rem;
    background: #f8fafc; border-radius: 0.5rem;
    border-left: 3px solid #cbd5e1;
    margin-bottom: 0.4rem;
}
.dark .jabatan-detail-item { background: #1e293b; border-left-color: #475569; }
.jabatan-detail-item.is-primary {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-left-color: #f59e0b;
}
.dark .jabatan-detail-item.is-primary {
    background: linear-gradient(135deg, rgba(180,83,9,0.3), rgba(217,119,6,0.25));
    border-left-color: #f59e0b;
}
.jabatan-detail-name {
    font-weight: 700; font-size: 0.85rem;
    display: flex; align-items: center; gap: 0.4rem;
    flex-wrap: wrap;
}
.jabatan-detail-meta {
    display: flex; flex-wrap: wrap; gap: 0.5rem;
    font-size: 0.7rem; color: #64748b;
}
.dark .jabatan-detail-meta { color: #94a3b8; }
.jabatan-meta-item {
    display: inline-flex; align-items: center; gap: 0.2rem;
}

/* ═══════════════════════════════════════════════════════
   STATUS PILLS
   ═══════════════════════════════════════════════════════ */
.status-pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.22rem 0.6rem; border-radius: 9999px;
    font-size: 0.72rem; font-weight: 700;
}
.status-pill.ok   { background: rgba(34,197,94,0.12); color: #16a34a; }
.status-pill.warn { background: rgba(245,158,11,0.12); color: #d97706; }
.status-pill.bad  { background: rgba(239,68,68,0.12); color: #dc2626; }
.dark .status-pill.ok   { background: rgba(34,197,94,0.2); color: #4ade80; }
.dark .status-pill.warn { background: rgba(245,158,11,0.2); color: #fbbf24; }
.dark .status-pill.bad  { background: rgba(239,68,68,0.2); color: #f87171; }

/* ═══════════════════════════════════════════════════════
   SECURITY FIELDS
   ═══════════════════════════════════════════════════════ */
.security-check-wrap {
    display: flex; align-items: center; gap: 0.5rem;
    margin-top: 0.3rem; padding: 0.6rem 1rem;
    background: #eff6ff; border-radius: 0.5rem;
    border: 1px solid #bfdbfe;
}
.dark .security-check-wrap { background: #1e293b; border-color: #334155; }
.security-check-wrap input[type="checkbox"] {
    width: 18px; height: 18px; cursor: pointer; accent-color: #3b82f6;
}
.security-check-wrap label {
    font-weight: 500; font-size: 0.9rem;
    cursor: pointer; color: #1e40af;
}
.dark .security-check-wrap label { color: #93c5fd; }

.security-fields {
    margin-top: 0.75rem; padding: 0.8rem 1rem;
    background: #f3f4f6; border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}
.dark .security-fields { background: #374151; border-color: #4b5563; }
.security-fields .form-group:last-child { margin-bottom: 0; }

.pin-warning-box {
    background: #fef3c7; border: 1px solid #f59e0b;
    border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.5rem;
}
.dark .pin-warning-box { background: #1f2937; border-color: #f59e0b; }
.pin-warning-box .pin-warning-title { font-weight: 600; color: #92400e; }
.dark .pin-warning-box .pin-warning-title { color: #fbbf24; }
.pin-warning-box .pin-warning-text { font-size: 0.85rem; color: #78350f; }
.dark .pin-warning-box .pin-warning-text { color: #fcd34d; }

/* ═══════════════════════════════════════════════════════
   HELPERS
   ═══════════════════════════════════════════════════════ */
.hidden { display: none !important; }
.text-center { text-align: center; }
.text-gray-500 { color: #6b7280; }
.dark .text-gray-500 { color: #9ca3af; }
.text-red-500 { color: #ef4444; }
.text-sm { font-size: 0.875rem; }
.text-xs { font-size: 0.75rem; }
.font-bold { font-weight: 700; }
.font-semibold { font-weight: 600; }
.mt-1 { margin-top: 0.25rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-3 { margin-top: 0.75rem; }
.mt-4 { margin-top: 1rem; }
.mt-6 { margin-top: 1.5rem; }
.mb-3 { margin-bottom: 0.75rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.flex { display: flex; }
.flex-wrap { flex-wrap: wrap; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.justify-end { justify-content: flex-end; }
.w-full { width: 100%; }
.mr-1 { margin-right: 0.25rem; }
.mr-2 { margin-right: 0.5rem; }

.spinner-border {
    display: inline-block; width: 1.2rem; height: 1.2rem;
    border: 2px solid rgba(255,255,255,0.3); border-top: 2px solid #fff;
    border-radius: 50%; animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════ */
@media (max-width: 640px) {
    .promote-container { padding: 1rem; }
    .stats-bar { gap: 0.75rem; font-size: 0.8rem; }
    .modal-box { padding: 1rem; }
    .jabatan-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="promote-container">

    {{-- ═══════════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════════ --}}
    <div class="promote-header">
        <div>
            <h3>
                <i class="fas fa-arrows-alt-v text-amber-500"></i>
                Manajemen Pangkat & Jabatan
            </h3>
            <p>Kelola level dan jabatan seluruh user.
                <span class="text-red-500">* Owner tidak dapat diubah.</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button id="toggle-legend" class="btn-outline text-sm" type="button">
                <i class="fas fa-sitemap"></i> Hierarki
            </button>
            <button id="toggle-checkbox" class="btn-outline text-sm" type="button">
                <i class="fas fa-check-square"></i> Pilih
            </button>
            <button id="btn-pangkat" class="btn-pangkat text-sm" disabled>
                <i class="fas fa-crown"></i> Pangkat
                <span id="selected-badge"
                      class="bg-white/20 px-2 py-0.5 rounded-full text-xs ml-1">0</span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         LEGEND HIERARKI (collapsible)
         ═══════════════════════════════════════════════════ --}}
    <div id="legend-panel" class="legend-panel hidden">
        <div class="legend-header">
            <h4><i class="fas fa-sitemap text-indigo-500"></i> Hierarki Role & Jabatan</h4>
            <button type="button" id="legend-close" class="legend-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="legend-grid">
            {{-- TIER 1: OWNER --}}
            <div class="legend-tier tier-owner">
                <div class="tier-badge">
                    <span class="badge-level badge-level-owner">
                        <i class="fas fa-crown"></i> Owner
                    </span>
                </div>
                <div class="tier-desc">
                    <p><b>Akses penuh sistem.</b> Pemilik UKM. Tidak dapat diubah.</p>
                    <p class="text-xs text-gray-500 mt-1">Hanya 1 akun.</p>
                </div>
            </div>

            {{-- TIER 2: SUPERADMIN --}}
            <div class="legend-tier tier-superadmin">
                <div class="tier-badge">
                    <span class="badge-level badge-level-superadmin">
                        <i class="fas fa-user-shield"></i> Superadmin
                    </span>
                </div>
                <div class="tier-desc">
                    <p>Wakil owner. Akses hampir penuh kecuali setting kritikal.</p>
                    <p class="text-xs text-gray-500 mt-1">Wajib punya <b>PIN</b> unik. Maks 2-3 akun.</p>
                </div>
            </div>

            {{-- TIER 3: KETUA --}}
            <div class="legend-tier tier-ketua">
                <div class="tier-badge">
                    <span class="badge-level badge-level-ketua">
                        <i class="fas fa-star"></i> Ketua
                    </span>
                </div>
                <div class="tier-desc">
                    <p><b>Pimpinan UKM.</b> Hanya 1 akun aktif.</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Ganti ketua via <b>Transfer Ketua</b> di Manajemen Pengurus.
                    </p>
                </div>
            </div>

            {{-- TIER 4: PENGURUS INTI --}}
            <div class="legend-tier tier-pengurus-inti">
                <div class="tier-badge">
                    <span class="badge-level badge-level-pengurus">
                        <i class="fas fa-users-cog"></i> Pengurus Inti
                    </span>
                </div>
                <div class="tier-desc">
                    <p>Pimpinan harian UKM:</p>
                    <div class="tier-jabatan-list">
                        <span class="jabatan-tag tag-inti"><i class="fas fa-user-tie"></i> Wakil Ketua</span>
                        <span class="jabatan-tag tag-inti"><i class="fas fa-book"></i> Sekretaris</span>
                        <span class="jabatan-tag tag-inti"><i class="fas fa-coins"></i> Bendahara</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <b>Masa jabatan: 1 periode (1 tahun).</b>
                    </p>
                </div>
            </div>

            {{-- TIER 5: PENGURUS / PJ --}}
            <div class="legend-tier tier-pengurus">
                <div class="tier-badge">
                    <span class="badge-level badge-level-pj">
                        <i class="fas fa-user-tie"></i> Pengurus / PJ
                    </span>
                </div>
                <div class="tier-desc">
                    <p>Koordinator & asisten:</p>
                    <div class="tier-jabatan-list">
                        <span class="jabatan-tag tag-pj"><i class="fas fa-bullhorn"></i> PJ PDD</span>
                        <span class="jabatan-tag tag-pj"><i class="fas fa-code"></i> PJ Pengembang</span>
                        <span class="jabatan-tag tag-pj"><i class="fas fa-user-friends"></i> Asisten Bendahara</span>
                        <span class="jabatan-tag tag-pj"><i class="fas fa-user-friends"></i> Asisten PJ Divisi</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <b>PJ & Asisten = level Pengurus (4).</b>
                    </p>
                </div>
            </div>

            {{-- TIER 6: ANGGOTA UKM --}}
            <div class="legend-tier tier-anggota">
                <div class="tier-badge">
                    <span class="badge-level badge-level-anggota">
                        <i class="fas fa-user-friends"></i> Anggota UKM
                    </span>
                </div>
                <div class="tier-desc">
                    <p>Anggota resmi UKM. Syarat utama masuk divisi.</p>
                    <div class="tier-jabatan-list">
                        <span class="jabatan-tag tag-anggota"><i class="fas fa-user"></i> Anggota UKM</span>
                        <span class="jabatan-tag tag-anggota"><i class="fas fa-bullhorn"></i> Anggota PDD</span>
                        <span class="jabatan-tag tag-anggota"><i class="fas fa-code"></i> Anggota Pengembang</span>
                    </div>
                    <p class="text-xs text-red-500 mt-1">
                        <i class="fas fa-exclamation-triangle"></i>
                        <b>Wajib punya jabatan "Anggota UKM"</b> sebelum masuk divisi.
                    </p>
                </div>
            </div>

            {{-- TIER 7: ANGGOTA UMUM --}}
            <div class="legend-tier tier-umum">
                <div class="tier-badge">
                    <span class="badge-level badge-level-anggota-umum">
                        <i class="fas fa-user"></i> Anggota Umum
                    </span>
                </div>
                <div class="tier-desc">
                    <p>Anggota non-UKM. Akses event umum saja.</p>
                    <p class="text-xs text-gray-500 mt-1">Bisa upgrade ke Anggota UKM.</p>
                </div>
            </div>

            {{-- TIER 8: PENGUNJUNG --}}
            <div class="legend-tier tier-pengunjung">
                <div class="tier-badge">
                    <span class="badge-level badge-level-pengunjung">
                        <i class="fas fa-eye"></i> Pengunjung
                    </span>
                </div>
                <div class="tier-desc">
                    <p>Guest / tamu. Akses read-only & event publik.</p>
                    <p class="text-xs text-gray-500 mt-1">Level terendah.</p>
                </div>
            </div>
        </div>

        <div class="legend-footer">
            <i class="fas fa-info-circle text-blue-500"></i>
            <span>
                <b>Promote</b> = naik 1 tingkat. <b>Demote</b> = turun 1 tingkat.
                Level tertinggi (Ketua) memerlukan fitur <b>Transfer Ketua</b>
                dari halaman Manajemen Pengurus.
            </span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         STATS BAR
         ═══════════════════════════════════════════════════ --}}
    <div class="stats-bar">
        <div class="stat-item">
            <i class="fas fa-users text-gray-400"></i>
            Total User: <span class="stat-number" id="total-users">0</span>
        </div>
        <div class="stat-item">
            <i class="fas fa-check-circle text-green-500"></i>
            Dipilih: <span class="stat-number" id="selected-stats">0</span>
        </div>
        <div class="stat-item">
            <i class="fas fa-shield-alt text-amber-500"></i>
            Superadmin: <span class="stat-number" id="superadmin-count">0</span>
        </div>
        <div class="stat-item">
            <i class="fas fa-key text-blue-500"></i>
            Ada PIN: <span class="stat-number" id="pin-count">0</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         TABLE
         ═══════════════════════════════════════════════════ --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="checkbox-column hidden" id="th-checkbox">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th class="text-center" style="width:40px;">#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Jabatan</th>
                    <th style="min-width:130px;">Masa Jabatan</th>
                    <th>PIN</th>
                    <th>Poin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <tr>
                    <td colspan="11" class="text-center py-6 text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════
         PAGINATION
         ═══════════════════════════════════════════════════ --}}
    <div class="flex flex-wrap justify-between items-center gap-2 mt-4 text-sm text-gray-500">
        <span id="info-text">Menampilkan 0 dari 0 user</span>
        <div id="pagination-controls" class="pagination-controls"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MODAL PANGKAT
     ═══════════════════════════════════════════════════════ --}}
<div id="pangkat-modal" class="modal-backdrop">
    <div class="modal-box">
        <h3 class="font-bold text-lg flex items-center gap-2">
            <i class="fas fa-crown text-amber-500"></i> Atur Pangkat / Jabatan
        </h3>

        <div class="form-group mt-4">
            <label>User Terpilih (<span id="selected-count">0</span>)</label>
            <div id="selected-users-list" class="selected-users-list"></div>
        </div>

        <div class="form-group">
            <label>Aksi</label>
            <select id="action-select" class="form-control">
                <option value="promote">⬆️ Naikkan Level</option>
                <option value="demote">⬇️ Turunkan Level</option>
                <option value="add_position">➕ Tambah Jabatan</option>
                <option value="remove_position">➖ Kurangi Jabatan</option>
            </select>
        </div>

        <div id="target-role-group" class="form-group">
            <label>Target Level</label>
            <select id="target-role" class="form-control">
                <option value="superadmin">Superadmin</option>
                <option value="ketua">Ketua</option>
                <option value="pengurus">Pengurus Inti</option>
                <option value="pj">PJ / Asisten</option>
                <option value="anggota">Anggota UKM (Kelompok)</option>
                <option value="anggota_umum">Anggota Umum</option>
                <option value="pengunjung">Pengunjung</option>
            </select>
        </div>

        <div id="pin-superadmin-group" class="form-group hidden">
            <label class="text-amber-600">
                <i class="fas fa-shield-alt"></i> Password Owner <span class="text-red-500">*</span>
            </label>
            <input type="password" id="pin-superadmin" class="form-control"
                   placeholder="Masukkan password Owner Anda" maxlength="255">
            <p class="text-xs text-gray-400 mt-1">
                Verifikasi keamanan untuk perubahan ke Superadmin.
            </p>
        </div>

        <div id="pin-warning-group" class="pin-warning-box hidden">
            <div class="pin-warning-title">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                Perhatian: Superadmin Membutuhkan PIN!
            </div>
            <div class="pin-warning-text mt-1">
                Setiap Superadmin wajib memiliki PIN baru. Silakan isi di bawah.
            </div>
        </div>

        <div id="jabatan-group" class="form-group hidden">
            <label>Pilih Jabatan</label>
            <div id="jabatan-list" class="jabatan-grid"></div>
        </div>

        <div class="form-group mt-3">
            <div class="security-check-wrap">
                <input type="checkbox" id="update-security-check">
                <label for="update-security-check">
                    <i class="fas fa-shield-alt text-blue-500"></i> Perbarui Keamanan Akun
                    <span class="text-xs text-gray-400">(Password & PIN)</span>
                </label>
            </div>
            <div id="security-fields" class="security-fields hidden">
                <div class="form-group">
                    <label>Password Baru
                        <span class="text-xs text-gray-400">(opsional, min 4 karakter)</span>
                    </label>
                    <input type="password" id="new-password" class="form-control"
                           placeholder="Masukkan password baru" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>
                        PIN Baru
                        <span id="pin-required-label" class="hidden text-red-500">*</span>
                        <span class="text-xs text-gray-400">(min 4, max 10 karakter)</span>
                    </label>
                    <input type="password" id="new-pin" class="form-control"
                           placeholder="Masukkan PIN baru" autocomplete="new-password">
                    <p class="text-xs text-gray-400 mt-1" id="pin-helper-text">
                        * Kosongkan jika tidak ingin mengubah PIN.
                    </p>
                </div>
            </div>
        </div>

        <div id="modal-error"
             class="hidden text-red-600 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg mb-3"></div>

        <div class="flex justify-end gap-3 mt-6">
            <button id="btn-modal-batal" class="btn-outline">Batal</button>
            <button id="btn-modal-simpan" class="btn-simpan">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MODAL DETAIL USER
     ═══════════════════════════════════════════════════════ --}}
<div id="detail-modal" class="modal-backdrop detail-card-modal">
    <div class="modal-box">
        <div class="detail-header">
            <div class="detail-avatar" id="detail-avatar">?</div>
            <div style="flex:1; min-width:0;">
                <h3 class="detail-name" id="detail-name">-</h3>
                <div class="detail-sub" id="detail-sub">-</div>
                <div class="detail-badges" id="detail-badges"></div>
            </div>
        </div>
        <div class="detail-grid" id="detail-grid"></div>
        <div class="flex justify-end mt-4">
            <button id="detail-close-btn" class="btn-outline">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════
       DOM REFS
       ═══════════════════════════════════════════════════════ */
    const tableBody          = document.getElementById('user-table-body');
    const toggleCheckbox     = document.getElementById('toggle-checkbox');
    const btnPangkat         = document.getElementById('btn-pangkat');
    const selectAll          = document.getElementById('select-all');
    const thCheckbox         = document.getElementById('th-checkbox');
    const infoText           = document.getElementById('info-text');
    const paginationControls = document.getElementById('pagination-controls');
    const selectedBadge      = document.getElementById('selected-badge');
    const selectedStats      = document.getElementById('selected-stats');
    const totalUsersEl       = document.getElementById('total-users');
    const superadminCountEl  = document.getElementById('superadmin-count');
    const pinCountEl         = document.getElementById('pin-count');

    const legendPanel        = document.getElementById('legend-panel');
    const toggleLegend       = document.getElementById('toggle-legend');
    const legendClose        = document.getElementById('legend-close');

    const modal              = document.getElementById('pangkat-modal');
    const selectedUsersList  = document.getElementById('selected-users-list');
    const selectedCount      = document.getElementById('selected-count');
    const actionSelect       = document.getElementById('action-select');
    const targetRoleGroup    = document.getElementById('target-role-group');
    const targetRole         = document.getElementById('target-role');
    const pinSuperadminGroup = document.getElementById('pin-superadmin-group');
    const pinSuperadminInput = document.getElementById('pin-superadmin');
    const pinWarningGroup    = document.getElementById('pin-warning-group');
    const pinRequiredLabel   = document.getElementById('pin-required-label');
    const pinHelperText      = document.getElementById('pin-helper-text');
    const jabatanGroup       = document.getElementById('jabatan-group');
    const jabatanList        = document.getElementById('jabatan-list');
    const updateSecurityCheck= document.getElementById('update-security-check');
    const securityFields     = document.getElementById('security-fields');
    const newPassword        = document.getElementById('new-password');
    const newPin             = document.getElementById('new-pin');
    const modalError         = document.getElementById('modal-error');
    const btnSimpan          = document.getElementById('btn-modal-simpan');
    const btnBatal           = document.getElementById('btn-modal-batal');

    const detailModal        = document.getElementById('detail-modal');
    const detailAvatar       = document.getElementById('detail-avatar');
    const detailName         = document.getElementById('detail-name');
    const detailSub          = document.getElementById('detail-sub');
    const detailBadges       = document.getElementById('detail-badges');
    const detailGrid         = document.getElementById('detail-grid');
    const detailCloseBtn     = document.getElementById('detail-close-btn');

    const toastContainer     = document.getElementById('toast-container');

    /* ═══════════════════════════════════════════════════════
       STATE
       ═══════════════════════════════════════════════════════ */
    let allUsers       = [];
    let selectedIds    = new Set();
    let isCheckboxMode = false;
    let currentPage    = 1;
    const perPage      = 15;
    let isLoading      = false;
    let isSaving       = false;

    /* ═══════════════════════════════════════════════════════
       UTILS
       ═══════════════════════════════════════════════════════ */
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icons = { success: '✅', error: '❌', info: 'ℹ️' };
        toast.innerHTML = `
            <span>${icons[type] || 'ℹ️'} ${escapeHtml(message)}</span>
            <button class="toast-close" type="button">&times;</button>`;
        toastContainer.appendChild(toast);
        toast.querySelector('.toast-close')
            .addEventListener('click', () => toast.remove());
        setTimeout(() => toast.remove(), 4000);
    }

    /* ═══════════════════════════════════════════════════════
       LEVEL MAPPING
       ═══════════════════════════════════════════════════════ */
    function getLevelInfo(levelName) {
        const key = (levelName || '').toString().toLowerCase().trim();
        const map = {
            'owner':         { label: 'Owner',         cls: 'badge-level-owner',        icon: 'fa-crown' },
            'superadmin':    { label: 'Superadmin',    cls: 'badge-level-superadmin',   icon: 'fa-user-shield' },
            'ketua':         { label: 'Ketua',         cls: 'badge-level-ketua',        icon: 'fa-star' },
            'pengurus':      { label: 'Pengurus Inti', cls: 'badge-level-pengurus',     icon: 'fa-users-cog' },
            'pengurus_inti': { label: 'Pengurus Inti', cls: 'badge-level-pengurus',     icon: 'fa-users-cog' },
            'pj':            { label: 'PJ / Asisten',  cls: 'badge-level-pj',           icon: 'fa-user-tie' },
            'asisten':       { label: 'PJ / Asisten',  cls: 'badge-level-pj',           icon: 'fa-user-tie' },
            'anggota':       { label: 'Anggota UKM',   cls: 'badge-level-anggota',      icon: 'fa-user-friends' },
            'anggota_ukm':   { label: 'Anggota UKM',   cls: 'badge-level-anggota',      icon: 'fa-user-friends' },
            'anggota_umum':  { label: 'Anggota Umum',  cls: 'badge-level-anggota-umum', icon: 'fa-user' },
            'pengunjung':    { label: 'Pengunjung',    cls: 'badge-level-pengunjung',   icon: 'fa-eye' },
            'guest':         { label: 'Pengunjung',    cls: 'badge-level-pengunjung',   icon: 'fa-eye' },
        };
        return map[key] || { label: levelName || '-', cls: 'badge-level-default', icon: 'fa-user' };
    }

    /* ═══════════════════════════════════════════════════════
       JABATAN — PARSE & PRIORITY
       ═══════════════════════════════════════════════════════ */
    function parseJabatans(user) {
        if (Array.isArray(user.jabatans) && user.jabatans.length > 0) {
            return user.jabatans
                .map(j => typeof j === 'string' ? j : (j && j.name ? j.name : ''))
                .filter(Boolean);
        }
        if (Array.isArray(user.jabatan_names) && user.jabatan_names.length > 0) {
            return user.jabatan_names.filter(Boolean);
        }
        return [];
    }

    const JABATAN_PRIORITY_PATTERNS = [
        { match: /^ketua$/i,       score: 100 },
        { match: /wakil\s*ketua/i, score: 90 },
        { match: /sekretaris/i,    score: 80 },
        { match: /bendahara/i,     score: 70 },
        { match: /pengembang/i,    score: 60 },
        { match: /\bpdd\b/i,       score: 55 },
        { match: /\bpj\b/i,        score: 50 },
        { match: /koordinator/i,   score: 45 },
        { match: /asisten/i,       score: 40 },
        { match: /anggota/i,       score: 30 },
    ];

    function getJabatanScore(name) {
        if (!name) return 10;
        for (const p of JABATAN_PRIORITY_PATTERNS) {
            if (p.match.test(name)) return p.score;
        }
        return 20;
    }

    function sortJabatans(list) {
        return [...list].sort((a, b) => getJabatanScore(b) - getJabatanScore(a));
    }

    /* ═══════════════════════════════════════════════════════
       JABATAN CLASSIFICATION
       ═══════════════════════════════════════════════════════ */
    function classifyJabatan(name) {
        if (!name) return null;
        const n = name.toLowerCase();

        // Pimpinan Inti
        if (/^(ketua|wakil\s*ketua|sekretaris|bendahara)(\s|$)/i.test(n)
            || /^ketua\s*ukm/i.test(n)) {
            return 'inti';
        }

        // PJ / Asisten / Koordinator
        if (/\bpj\b/i.test(n)
            || /\basisten\b/i.test(n)
            || /\bkoordinator\b/i.test(n)
            || /pengembang/i.test(n)
            || /\bpdd\b/i.test(n)) {
            return 'pj';
        }

        // Anggota
        if (/anggota/i.test(n)) return 'anggota';

        return null;
    }

    function getJabatanClassMeta(cls) {
        return {
            inti:    { label: 'Pimpinan Inti', icon: 'fa-crown',     style: 'inti' },
            pj:      { label: 'PJ / Asisten',  icon: 'fa-user-tie',  style: 'pj' },
            anggota: { label: 'Anggota',       icon: 'fa-user',      style: 'anggota' },
        }[cls] || { label: '', icon: '', style: '' };
    }

    /* ═══════════════════════════════════════════════════════
       PERIODE / MASA JABATAN
       ═══════════════════════════════════════════════════════ */
    function formatPeriode(startDate, endDate) {
        if (!startDate && !endDate) return null;

        const fmtMonthYear = (d) => {
            if (!d) return '—';
            const date = new Date(d);
            if (isNaN(date.getTime())) return '—';
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        };

        const start = fmtMonthYear(startDate);
        const end   = fmtMonthYear(endDate);

        let status = 'active';
        let durationText = '';

        if (startDate && endDate) {
            const endD = new Date(endDate);
            const now  = new Date();
            const diffDays = Math.ceil((endD - now) / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                status = 'expired';
                durationText = `Berakhir ${Math.abs(diffDays)} hari lalu`;
            } else if (diffDays < 30) {
                status = 'expiring';
                durationText = `Berakhir dalam ${diffDays} hari`;
            } else {
                const months = Math.ceil(diffDays / 30);
                durationText = `Aktif ~${months} bulan lagi`;
            }
        } else if (startDate && !endDate) {
            const startD = new Date(startDate);
            const now = new Date();
            const months = Math.max(0, Math.ceil((now - startD) / (1000 * 60 * 60 * 24 * 30)));
            durationText = `Aktif ${months} bulan`;
        }

        return { start, end, durationText, status };
    }

    function renderPeriodeCell(user) {
        // Prioritas: primary_jabatan
        let target = user.primary_jabatan;

        // Fallback: cari di array jabatans
        if (!target && Array.isArray(user.jabatans)) {
            const found = user.jabatans.find(j =>
                j && typeof j === 'object' && (j.start_date || j.end_date)
            );
            if (found) {
                target = {
                    name: found.name,
                    start_date: found.start_date,
                    end_date: found.end_date,
                };
            }
        }

        if (!target || (!target.start_date && !target.end_date)) {
            return '<span class="periode-empty">—</span>';
        }

        const periode = formatPeriode(target.start_date, target.end_date);
        if (!periode) return '<span class="periode-empty">—</span>';

        const statusClass = (periode.status === 'expired' || periode.status === 'expiring')
            ? 'periode-expired'
            : 'periode-active';

        return `
            <div class="periode-cell">
                <div class="periode-range">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                    ${escapeHtml(periode.start)} – ${escapeHtml(periode.end)}
                </div>
                <span class="${statusClass}">
                    ${escapeHtml(periode.durationText)}
                </span>
            </div>`;
    }

    /* ═══════════════════════════════════════════════════════
       FETCH USERS
       ═══════════════════════════════════════════════════════ */
    function fetchUsers() {
        if (isLoading) return;
        isLoading = true;

        tableBody.innerHTML = `
            <tr><td colspan="11" class="text-center py-6">
                <div class="spinner-border"></div>
                <p class="mt-2 text-gray-500">Memuat data...</p>
            </td></tr>`;

        fetch('{{ route("owner.promote-demote.data") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (!Array.isArray(data)) throw new Error('Data bukan array');
            allUsers = data;
            renderTable();
            updateStats();
        })
        .catch(err => {
            console.error('Error:', err);
            tableBody.innerHTML = `
                <tr><td colspan="11" class="text-center py-6 text-red-500">
                    ❌ Gagal memuat data: ${escapeHtml(err.message)}
                </td></tr>`;
            showToast('Gagal memuat data', 'error');
        })
        .finally(() => { isLoading = false; });
    }

    /* ═══════════════════════════════════════════════════════
       RENDER TABLE
       ═══════════════════════════════════════════════════════ */
    function renderTable() {
        const total = allUsers.length;
        if (total === 0) {
            tableBody.innerHTML = `
                <tr><td colspan="11" class="text-center py-6 text-gray-500">
                    Tidak ada user
                </td></tr>`;
            infoText.textContent = 'Menampilkan 0 dari 0 user';
            paginationControls.innerHTML = '';
            return;
        }

        const totalPages = Math.ceil(total / perPage);
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const end   = Math.min(start + perPage, total);
        const pageData = allUsers.slice(start, end);

        infoText.textContent = `Menampilkan ${start + 1} - ${end} dari ${total} user`;

        let html = '';
        pageData.forEach((user, idx) => {
            const num      = start + idx + 1;
            const checked  = selectedIds.has(user.id) ? 'checked' : '';
            const isOwner  = user.level_name === 'owner';
            const hasPin   = user.has_pin === true;
            const levelInfo = getLevelInfo(user.level_name);
            const jabatans = sortJabatans(parseJabatans(user));

            /* Level cell */
            const levelCell = `
                <span class="badge-level ${levelInfo.cls}">
                    <i class="fas ${levelInfo.icon}"></i> ${escapeHtml(levelInfo.label)}
                </span>`;

            /* Jabatan cell */
            let jabatanCell = '<span class="jabatan-empty">—</span>';
            if (jabatans.length > 0) {
                const primaryJabatan = jabatans[0];
                const cls  = classifyJabatan(primaryJabatan);
                const meta = getJabatanClassMeta(cls);

                const classBadge = meta.style
                    ? `<span class="jabatan-primary-badge ${meta.style}">
                           <i class="fas ${meta.icon}"></i> ${escapeHtml(meta.label)}
                       </span>`
                    : '';

                const extras  = jabatans.length - 1;
                const moreBtn = extras > 0
                    ? `<button type="button" class="jabatan-more-btn"
                               data-detail-id="${user.id}"
                               title="Lihat semua jabatan (${jabatans.length})">
                           +${extras} <i class="fas fa-info-circle"></i>
                       </button>`
                    : '';

                jabatanCell = `
                    <div class="jabatan-cell">
                        <div style="display:flex; align-items:center; gap:0.4rem; flex-wrap:wrap;">
                            <span class="jabatan-main" title="${escapeHtml(primaryJabatan)}">${escapeHtml(primaryJabatan)}</span>
                            ${moreBtn}
                        </div>
                        ${classBadge}
                    </div>`;
            }

            /* Masa Jabatan cell */
            const periodeCell = renderPeriodeCell(user);

            /* Checkbox */
            const checkboxHtml = isCheckboxMode
                ? `<td class="checkbox-column">
                       <input type="checkbox" class="user-checkbox"
                              data-id="${user.id}" ${checked} ${isOwner ? 'disabled' : ''}>
                   </td>`
                : '';

            html += `
                <tr>
                    ${checkboxHtml}
                    <td class="text-center">${num}</td>
                    <td>${escapeHtml(user.nim || '-')}</td>
                    <td>
                        <div class="name-cell">
                            <span class="name-text" title="${escapeHtml(user.name || '')}">${escapeHtml(user.name || '-')}</span>
                            <button type="button" class="row-info-btn"
                                    data-detail-id="${user.id}"
                                    title="Lihat detail akun">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </td>
                    <td>${escapeHtml(user.email || '-')}</td>
                    <td>${levelCell}</td>
                    <td>${jabatanCell}</td>
                    <td>${periodeCell}</td>
                    <td>
                        ${hasPin
                            ? '<span class="badge-pin"><i class="fas fa-check-circle"></i> Ada</span>'
                            : '<span class="badge-pin badge-no-pin"><i class="fas fa-times-circle"></i> Tidak Ada</span>'}
                    </td>
                    <td>${user.total_points || 0}</td>
                    <td>${user.is_active ? 'Aktif' : 'Nonaktif'}</td>
                </tr>`;
        });

        tableBody.innerHTML = html;

        // Toggle checkbox column visibility
        document.querySelectorAll('.checkbox-column')
            .forEach(el => el.classList.toggle('hidden', !isCheckboxMode));
        thCheckbox.classList.toggle('hidden', !isCheckboxMode);

        // Bind checkbox listeners
        document.querySelectorAll('.user-checkbox:not(:disabled)')
            .forEach(cb => cb.addEventListener('change', handleCheckboxChange));
        selectAll.removeEventListener('change', handleSelectAll);
        selectAll.addEventListener('change', handleSelectAll);

        // Bind detail buttons
        tableBody.querySelectorAll('[data-detail-id]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const id = parseInt(this.dataset.detailId);
                openDetailCard(id);
            });
        });

        // Pagination
        renderPagination(totalPages);

        updatePangkatButton();
        updateSelectAllState();
        updateStats();
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let pagHtml = '';
        pagHtml += `<button data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>‹</button>`;
        for (let i = 1; i <= totalPages; i++) {
            pagHtml += `<button class="${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        pagHtml += `<button data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>›</button>`;

        paginationControls.innerHTML = pagHtml;

        paginationControls.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', function () {
                const page = parseInt(this.dataset.page);
                if (page >= 1 && page <= totalPages && page !== currentPage) {
                    currentPage = page;
                    renderTable();
                }
            });
        });
    }

    /* ═══════════════════════════════════════════════════════
       DETAIL CARD
       ═══════════════════════════════════════════════════════ */
    function openDetailCard(userId) {
        const user = allUsers.find(u => u.id === userId);
        if (!user) {
            showToast('User tidak ditemukan', 'error');
            return;
        }

        const levelInfo     = getLevelInfo(user.level_name);
        const jabatans      = sortJabatans(parseJabatans(user));
        const hasPin        = user.has_pin === true;
        const hasPassword   = user.has_password === true;
        const passwordKnown = user.has_password !== undefined && user.has_password !== null;

        /* Avatar */
        if (user.profile_photo) {
            detailAvatar.innerHTML = `<img src="${escapeHtml(user.profile_photo)}" alt="foto">`;
        } else {
            const initials = (user.name || '?')
                .split(/\s+/).filter(Boolean)
                .map(w => w[0]).slice(0, 2).join('').toUpperCase();
            detailAvatar.textContent = initials || '?';
        }

        /* Header */
        detailName.textContent = user.name || '-';
        detailSub.textContent  = user.email || '-';

        detailBadges.innerHTML = `
            <span class="badge-level ${levelInfo.cls}">
                <i class="fas ${levelInfo.icon}"></i> ${escapeHtml(levelInfo.label)}
            </span>
            <span class="badge-level ${user.is_active ? 'badge-level-anggota' : 'badge-level-pengunjung'}">
                <i class="fas ${user.is_active ? 'fa-check-circle' : 'fa-pause-circle'}"></i>
                ${user.is_active ? 'Aktif' : 'Nonaktif'}
            </span>`;

        /* Detail jabatan lengkap dengan periode */
        let jabatanDetailHtml = '<div class="periode-empty">Belum ada jabatan</div>';
        if (jabatans.length > 0) {
            jabatanDetailHtml = jabatans.map((jName, i) => {
                const fullJab = Array.isArray(user.jabatans)
                    ? user.jabatans.find(j =>
                        j && typeof j === 'object' && j.name === jName)
                    : null;

                const startDate = fullJab?.start_date;
                const endDate   = fullJab?.end_date;
                const isPrimary = fullJab?.is_primary === true || i === 0;
                const cls       = classifyJabatan(jName);
                const meta      = getJabatanClassMeta(cls);
                const periode   = formatPeriode(startDate, endDate);

                const fmtDate = (d) => {
                    if (!d) return '—';
                    const date = new Date(d);
                    if (isNaN(date.getTime())) return '—';
                    return date.toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'short', year: 'numeric'
                    });
                };

                return `
                    <div class="jabatan-detail-item ${isPrimary ? 'is-primary' : ''}">
                        <div class="jabatan-detail-name">
                            ${isPrimary
                                ? '<i class="fas fa-star text-amber-500"></i>'
                                : '<i class="fas fa-briefcase text-gray-400"></i>'}
                            ${escapeHtml(jName)}
                            ${meta.style
                                ? `<span class="jabatan-primary-badge ${meta.style}">
                                       <i class="fas ${meta.icon}"></i> ${escapeHtml(meta.label)}
                                   </span>`
                                : ''}
                        </div>
                        <div class="jabatan-detail-meta">
                            ${startDate
                                ? `<span class="jabatan-meta-item">
                                       <i class="fas fa-play-circle"></i>
                                       Mulai: ${escapeHtml(fmtDate(startDate))}
                                   </span>`
                                : ''}
                            ${endDate
                                ? `<span class="jabatan-meta-item">
                                       <i class="fas fa-stop-circle"></i>
                                       Selesai: ${escapeHtml(fmtDate(endDate))}
                                   </span>`
                                : ''}
                            ${periode?.durationText
                                ? `<span class="jabatan-meta-item">
                                       <i class="fas fa-clock ${periode.status === 'expired' ? 'text-red-500' : 'text-green-500'}"></i>
                                       ${escapeHtml(periode.durationText)}
                                   </span>`
                                : ''}
                            ${!startDate && !endDate
                                ? `<span class="jabatan-meta-item text-gray-400">
                                       <i class="fas fa-info-circle"></i>
                                       Data periode belum tersedia
                                   </span>`
                                : ''}
                        </div>
                    </div>`;
            }).join('');
        }

        /* PIN & Password pills */
        const pwPill = passwordKnown
            ? (hasPassword
                ? '<span class="status-pill ok"><i class="fas fa-check"></i> Tersedia</span>'
                : '<span class="status-pill warn"><i class="fas fa-exclamation-triangle"></i> Belum disetel</span>')
            : '<span class="status-pill warn"><i class="fas fa-question"></i> Tidak diketahui</span>';

        const pinPill = hasPin
            ? '<span class="status-pill ok"><i class="fas fa-check"></i> Tersedia</span>'
            : '<span class="status-pill warn"><i class="fas fa-exclamation-triangle"></i> Belum disetel</span>';

        /* Grid */
        detailGrid.innerHTML = `
            <div class="detail-row">
                <div class="detail-label">NIM</div>
                <div class="detail-value">${escapeHtml(user.nim || '-')}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Poin</div>
                <div class="detail-value">${user.total_points || 0}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Level</div>
                <div class="detail-value">
                    <span class="badge-level ${levelInfo.cls}">
                        <i class="fas ${levelInfo.icon}"></i> ${escapeHtml(levelInfo.label)}
                    </span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status</div>
                <div class="detail-value">${user.is_active ? 'Aktif' : 'Nonaktif'}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Password</div>
                <div class="detail-value">${pwPill}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">PIN</div>
                <div class="detail-value">${pinPill}</div>
            </div>
            <div class="detail-row full">
                <div class="detail-label">
                    <i class="fas fa-briefcase mr-1"></i>
                    Jabatan (${jabatans.length})
                </div>
                <div class="detail-value" style="margin-top:0.4rem;">
                    ${jabatanDetailHtml}
                </div>
            </div>`;

        detailModal.style.display = 'flex';
    }

    function closeDetailCard() { detailModal.style.display = 'none'; }

    /* ═══════════════════════════════════════════════════════
       CHECKBOX
       ═══════════════════════════════════════════════════════ */
    function handleCheckboxChange(e) {
        const id = parseInt(e.target.dataset.id);
        if (e.target.checked) selectedIds.add(id);
        else selectedIds.delete(id);
        updatePangkatButton();
        updateSelectAllState();
        updateStats();
    }

    function handleSelectAll(e) {
        const checked = e.target.checked;
        document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(cb => {
            cb.checked = checked;
            const id = parseInt(cb.dataset.id);
            if (checked) selectedIds.add(id);
            else selectedIds.delete(id);
        });
        updatePangkatButton();
        updateSelectAllState();
        updateStats();
    }

    function updateSelectAllState() {
        const checkboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
        const checked    = document.querySelectorAll('.user-checkbox:not(:disabled):checked');
        selectAll.checked = checkboxes.length > 0 && checkboxes.length === checked.length;
    }

    function updatePangkatButton() {
        const count = selectedIds.size;
        btnPangkat.disabled = count === 0;
        selectedBadge.textContent = count;
        selectedStats.textContent = count;
    }

    function updateStats() {
        totalUsersEl.textContent      = allUsers.length;
        selectedStats.textContent     = selectedIds.size;
        selectedBadge.textContent     = selectedIds.size;
        superadminCountEl.textContent = allUsers.filter(u => u.level_name === 'superadmin').length;
        pinCountEl.textContent        = allUsers.filter(u => u.has_pin === true).length;
    }

    /* ═══════════════════════════════════════════════════════
       MODAL PANGKAT
       ═══════════════════════════════════════════════════════ */
    function showModal(users) {
        selectedUsersList.innerHTML = users
            .map(u => `<span class="user-badge">${escapeHtml(u.name)}</span>`)
            .join('');
        selectedCount.textContent = users.length;

        modalError.classList.add('hidden');
        pinSuperadminInput.value = '';
        newPassword.value = '';
        newPin.value = '';
        updateSecurityCheck.checked = false;
        securityFields.classList.add('hidden');
        pinWarningGroup.classList.add('hidden');
        pinRequiredLabel.classList.add('hidden');
        pinHelperText.textContent = '* Kosongkan jika tidak ingin mengubah PIN.';
        actionSelect.value = 'promote';

        toggleModalFields();
        fetchJabatans();
        modal.style.display = 'flex';
    }

    function closeModal() { modal.style.display = 'none'; }

    function toggleModalFields() {
        const val = actionSelect.value;
        const targetRoleVal = targetRole.value;
        const isSuperadminTarget = targetRoleVal === 'superadmin';

        if (val === 'promote' || val === 'demote') {
            targetRoleGroup.classList.remove('hidden');

            if (isSuperadminTarget) {
                pinSuperadminGroup.classList.remove('hidden');
                pinWarningGroup.classList.remove('hidden');
                pinRequiredLabel.classList.remove('hidden');
                pinHelperText.textContent = '* Wajib diisi untuk Superadmin baru.';
                updateSecurityCheck.checked = true;
                securityFields.classList.remove('hidden');
            } else {
                pinSuperadminGroup.classList.add('hidden');
                pinWarningGroup.classList.add('hidden');
                pinRequiredLabel.classList.add('hidden');
                pinHelperText.textContent = '* Kosongkan jika tidak ingin mengubah PIN.';
            }
        } else {
            targetRoleGroup.classList.add('hidden');
            pinSuperadminGroup.classList.add('hidden');
            pinWarningGroup.classList.add('hidden');
            pinRequiredLabel.classList.add('hidden');
            pinHelperText.textContent = '* Kosongkan jika tidak ingin mengubah PIN.';
        }

        if (val === 'add_position' || val === 'remove_position') {
            jabatanGroup.classList.remove('hidden');
        } else {
            jabatanGroup.classList.add('hidden');
        }
    }

    function fetchJabatans() {
        fetch('{{ route("owner.promote-demote.jabatans") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            jabatanList.innerHTML = data.map(j =>
                `<label>
                    <input type="checkbox" name="jabatan_ids[]" value="${j.id}">
                    ${escapeHtml(j.name)}
                </label>`
            ).join('');
        })
        .catch(() => showToast('Gagal memuat jabatan', 'error'));
    }

    /* ═══════════════════════════════════════════════════════
       SAVE
       ═══════════════════════════════════════════════════════ */
    function handleSave() {
        if (isSaving) return;

        const action        = actionSelect.value;
        const userIds       = Array.from(selectedIds);
        const targetRoleVal = targetRole.value;
        const pinSuperadmin = pinSuperadminInput.value.trim();
        const updateSecurity= updateSecurityCheck.checked;
        const newPass       = newPassword.value.trim();
        const newPinVal     = newPin.value.trim();
        const isSuperadminTarget = targetRoleVal === 'superadmin';

        modalError.classList.add('hidden');

        /* Validasi */
        if (userIds.length === 0) {
            return showModalError('Tidak ada user yang dipilih.');
        }

        if ((action === 'promote' || action === 'add_position') && isSuperadminTarget) {
            if (!pinSuperadmin) {
                return showModalError('Masukkan password Owner untuk verifikasi keamanan.');
            }
        }

        if (action === 'promote' && isSuperadminTarget) {
            if (!newPinVal || newPinVal.length < 4) {
                return showModalError('PIN wajib diisi minimal 4 karakter untuk Superadmin baru.');
            }
        }

        if (action === 'add_position' || action === 'remove_position') {
            const checked = document.querySelectorAll('#jabatan-list input:checked');
            if (checked.length === 0) {
                return showModalError('Pilih minimal satu jabatan.');
            }
        }

        if (updateSecurity) {
            if (!newPass && !newPinVal) {
                return showModalError('Isi password atau PIN baru jika ingin memperbarui keamanan.');
            }
            if (newPass && newPass.length < 4) {
                return showModalError('Password minimal 4 karakter.');
            }
            if (newPinVal && (newPinVal.length < 4 || newPinVal.length > 10)) {
                return showModalError('PIN harus 4-10 karakter.');
            }
        }

        const selectedUsers = allUsers.filter(u => userIds.includes(u.id));
        if ((action === 'promote' || action === 'add_position') && isSuperadminTarget) {
            const hasSuperadmin = selectedUsers.some(u => u.level_name === 'superadmin');
            if (hasSuperadmin) {
                return showModalError('User sudah memiliki role Superadmin.');
            }
        }

        const actionLabel = {
            promote: 'menaikkan',
            demote: 'menurunkan',
            add_position: 'menambah',
            remove_position: 'mengurangi',
        }[action] || action;

        if (!confirm(`Yakin ingin ${actionLabel} pangkat/jabatan untuk ${userIds.length} user?`)) return;

        /* Lock UI */
        isSaving = true;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border"></span> Menyimpan...';

        const payload = {
            user_ids:        userIds,
            action:          action,
            target_role:     targetRoleVal,
            jabatan_ids:     Array.from(document.querySelectorAll('#jabatan-list input:checked'))
                                 .map(cb => parseInt(cb.value)),
            pin_superadmin:  pinSuperadmin,
            update_security: updateSecurity,
            new_password:    newPass,
            new_pin:         newPinVal,
            _token:          '{{ csrf_token() }}',
        };

        fetch('{{ route("owner.promote-demote.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                showToast(response.message, 'success');
                closeModal();
                selectedIds.clear();
                fetchUsers();
                updatePangkatButton();
            } else {
                showModalError(response.message || 'Terjadi kesalahan.');
                showToast(response.message || 'Gagal', 'error');
            }
        })
        .catch(() => {
            showModalError('Gagal menghubungi server.');
            showToast('Gagal menghubungi server', 'error');
        })
        .finally(() => {
            isSaving = false;
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan';
        });
    }

    function showModalError(msg) {
        modalError.textContent = '⚠️ ' + msg;
        modalError.classList.remove('hidden');
        modalError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ═══════════════════════════════════════════════════════
       EVENT LISTENERS
       ═══════════════════════════════════════════════════════ */

    /* Legend toggle */
    toggleLegend.addEventListener('click', () => {
        legendPanel.classList.toggle('hidden');
        if (!legendPanel.classList.contains('hidden')) {
            legendPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
    legendClose.addEventListener('click', () => legendPanel.classList.add('hidden'));

    /* Checkbox mode toggle */
    toggleCheckbox.addEventListener('click', function () {
        isCheckboxMode = !isCheckboxMode;
        this.innerHTML = isCheckboxMode
            ? '<i class="fas fa-times"></i> Batal'
            : '<i class="fas fa-check-square"></i> Pilih';
        renderTable();
    });

    /* Buka modal pangkat */
    btnPangkat.addEventListener('click', function () {
        if (selectedIds.size === 0) return;
        const selectedUsers = allUsers.filter(u => selectedIds.has(u.id));
        if (selectedUsers.some(u => u.level_name === 'owner')) {
            showToast('Tidak dapat mengubah Owner!', 'error');
            return;
        }
        showModal(selectedUsers);
    });

    /* Modal handlers */
    btnBatal.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    actionSelect.addEventListener('change', toggleModalFields);
    targetRole.addEventListener('change', toggleModalFields);

    updateSecurityCheck.addEventListener('change', function () {
        securityFields.classList.toggle('hidden', !this.checked);
    });

    btnSimpan.addEventListener('click', handleSave);

    /* Detail modal close */
    detailCloseBtn.addEventListener('click', closeDetailCard);
    detailModal.addEventListener('click', function (e) {
        if (e.target === this) closeDetailCard();
    });

    /* ESC key */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (detailModal.style.display === 'flex') closeDetailCard();
            else if (modal.style.display === 'flex') closeModal();
        }
    });

    /* ═══════════════════════════════════════════════════════
       INIT
       ═══════════════════════════════════════════════════════ */
    fetchUsers();
});
</script>
@endpush
@endsection
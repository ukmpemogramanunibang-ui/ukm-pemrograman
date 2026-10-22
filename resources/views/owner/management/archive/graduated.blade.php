@extends('layouts.roles.owner')

@section('title', ($title ?? 'Arsip') . ' – Owner UNICODE')
@section('page-title', $title ?? 'Arsip Anggota')

@push('styles')
<style>
    .arc-wrap { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .dark .arc-wrap { background:#1f2937; }

    .arc-head { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; }
    .arc-head h3 { font-size:1.2rem; font-weight:700; color:#111827; display:flex; align-items:center; gap:.5rem; }
    .dark .arc-head h3 { color:#f3f4f6; }
    .arc-head p { font-size:.8rem; color:#6b7280; margin-top:.2rem; }
    .dark .arc-head p { color:#9ca3af; }

    .arc-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:.75rem; margin-bottom:1.25rem; }
    .arc-stat {
        padding:.9rem 1rem; background:#f9fafb; border-radius:.6rem;
        border:1px solid #f3f4f6; display:flex; align-items:center; gap:.7rem;
    }
    .dark .arc-stat { background:#111827; border-color:#374151; }
    .arc-stat .icon {
        width:38px; height:38px; border-radius:.5rem;
        display:flex; align-items:center; justify-content:center;
        font-size:.95rem; color:#fff; flex-shrink:0;
    }
    .arc-stat .icon.blue   { background:linear-gradient(135deg,#6366f1,#8b5cf6); }
    .arc-stat .icon.green  { background:linear-gradient(135deg,#22c55e,#16a34a); }
    .arc-stat .icon.amber  { background:linear-gradient(135deg,#f59e0b,#d97706); }
    .arc-stat .icon.pink   { background:linear-gradient(135deg,#ec4899,#db2777); }
    .arc-stat .label { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; font-weight:600; }
    .dark .arc-stat .label { color:#9ca3af; }
    .arc-stat .value { font-size:1.35rem; font-weight:700; color:#111827; line-height:1.1; }
    .dark .arc-stat .value { color:#f3f4f6; }

    .arc-toolbar { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; align-items:center; }
    .arc-search {
        flex:1; min-width:220px; padding:.5rem .9rem;
        border:1px solid #d1d5db; border-radius:.5rem; font-size:.85rem;
        background:#fff;
    }
    .dark .arc-search { background:#374151; border-color:#4b5563; color:#e5e7eb; }
    .arc-search:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.15); }

    .arc-btn {
        padding:.5rem 1rem; border-radius:.5rem; font-size:.85rem; font-weight:600;
        border:1px solid #d1d5db; background:#fff; color:#4b5563; cursor:pointer;
        display:inline-flex; align-items:center; gap:.35rem; transition:.15s;
    }
    .arc-btn:hover { background:#f3f4f6; border-color:#9ca3af; }
    .dark .arc-btn { background:#374151; border-color:#4b5563; color:#e5e7eb; }
    .dark .arc-btn:hover { background:#4b5563; }

    .arc-table { overflow-x:auto; }
    .arc-table table { width:100%; border-collapse:collapse; font-size:.85rem; }
    .arc-table th {
        text-align:left; padding:.65rem .75rem; font-size:.7rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.05em; color:#6b7280;
        border-bottom:1px solid #e5e7eb; background:#f9fafb;
    }
    .dark .arc-table th { color:#9ca3af; border-color:#374151; background:#1f2937; }
    .arc-table td { padding:.65rem .75rem; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    .dark .arc-table td { border-color:#374151; color:#e5e7eb; }
    .arc-table tr:hover td { background:#f9fafb; }
    .dark .arc-table tr:hover td { background:#374151; }

    .arc-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.15rem .55rem; border-radius:9999px; font-size:.65rem; font-weight:700; }
    .arc-badge.level       { background:rgba(99,102,241,.12); color:#6366f1; }
    .arc-badge.status-ok   { background:rgba(34,197,94,.12); color:#16a34a; }
    .arc-badge.status-off  { background:rgba(107,114,128,.15); color:#6b7280; }
    .dark .arc-badge.level      { background:rgba(129,140,248,.2); color:#a5b4fc; }
    .dark .arc-badge.status-ok  { background:rgba(34,197,94,.2); color:#4ade80; }
    .dark .arc-badge.status-off { background:rgba(107,114,128,.25); color:#9ca3af; }

    .arc-empty { text-align:center; padding:3rem 1rem; color:#6b7280; }
    .arc-empty .em-icon { font-size:3rem; margin-bottom:.75rem; opacity:.5; }
    .dark .arc-empty { color:#9ca3af; }

    .arc-pagination { display:flex; justify-content:space-between; align-items:center; gap:.5rem; margin-top:1rem; font-size:.8rem; color:#6b7280; }
    .dark .arc-pagination { color:#9ca3af; }
</style>
@endpush

@section('content')
<div class="arc-wrap">

    {{-- HEADER --}}
    <div class="arc-head">
        <div>
            <h3>
                <i class="fas fa-user-graduate text-indigo-500"></i>
                {{ $title ?? 'Arsip Anggota' }}
            </h3>
            <p>Daftar akun yang telah {{ strtolower($title ?? 'diarsipkan') }}. Data tetap tersimpan untuk riwayat.</p>
        </div>
        <a href="{{ route('owner.management.dashboard') }}" class="arc-btn">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    {{-- STATS --}}
    <div class="arc-stats">
        <div class="arc-stat">
            <div class="icon blue"><i class="fas fa-users"></i></div>
            <div>
                <div class="label">Total</div>
                <div class="value">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="arc-stat">
            <div class="icon green"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="label">Tahun Ini</div>
                <div class="value">{{ $stats['this_year'] ?? 0 }}</div>
            </div>
        </div>
        <div class="arc-stat">
            <div class="icon amber"><i class="fas fa-mars"></i></div>
            <div>
                <div class="label">Pria</div>
                <div class="value">{{ $stats['pria'] ?? 0 }}</div>
            </div>
        </div>
        <div class="arc-stat">
            <div class="icon pink"><i class="fas fa-venus"></i></div>
            <div>
                <div class="label">Wanita</div>
                <div class="value">{{ $stats['wanita'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="arc-toolbar">
        <input type="text" id="arc-search" class="arc-search" placeholder="🔎 Cari NIM / Nama / Email...">
        <button type="button" class="arc-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button type="button" class="arc-btn" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i> Muat Ulang
        </button>
    </div>

    {{-- TABLE --}}
    <div class="arc-table">
        <table id="arc-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Level Terakhir</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Diarsipkan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $i => $m)
                    @php
                        $levelName = $m->level->display_name ?? $m->level->name ?? '-';
                        $jabatan   = $m->jabatans->pluck('name')->take(2)->implode(', ') ?: '—';
                        $statusNm  = $m->status->display_name ?? $m->status->name ?? '-';
                        $isActive  = (bool) ($m->is_active ?? false);
                    @endphp
                    <tr data-search="{{ strtolower(($m->nim ?? '') . ' ' . ($m->name ?? '') . ' ' . ($m->email ?? '')) }}">
                        <td class="text-center">{{ $members->firstItem() + $i }}</td>
                        <td>{{ $m->nim ?? '-' }}</td>
                        <td><strong>{{ $m->name ?? '-' }}</strong></td>
                        <td>{{ $m->email ?? '-' }}</td>
                        <td><span class="arc-badge level">{{ $levelName }}</span></td>
                        <td>{{ $jabatan }}</td>
                        <td>
                            <span class="arc-badge {{ $isActive ? 'status-ok' : 'status-off' }}">
                                {{ $statusNm }}
                            </span>
                        </td>
                        <td>{{ optional($m->updated_at)->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="arc-empty">
                                <div class="em-icon">📭</div>
                                <div style="font-weight:600; color:#374151;">Belum ada data arsip</div>
                                <div style="font-size:.8rem; margin-top:.35rem;">
                                    Belum ada akun dengan status <code>{{ $title ?? 'alumni' }}</code>.
                                </div>
                                <div style="font-size:.75rem; margin-top:.75rem; opacity:.7;">
                                    💡 Pastikan tabel <code>statuses</code> sudah di-seed dengan
                                    <code>alumni</code> / <code>graduated</code> / <code>lulus</code>.
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($members instanceof \Illuminate\Pagination\LengthAwarePaginator && $members->total() > 0)
        <div class="arc-pagination">
            <div>Menampilkan {{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }}</div>
            <div>{{ $members->withQueryString()->links() }}</div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('arc-search');
    const rows  = document.querySelectorAll('#arc-table tbody tr[data-search]');

    if (!input) return;
    input.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        rows.forEach(row => {
            row.style.display = !q || row.dataset.search.includes(q) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
@php
    use App\Models\User;
    use Illuminate\Support\Facades\Schema;

    $levelKey   = $levelKey   ?? 'index';
    $pageTitle  = $pageTitle  ?? 'Manajemen Pelanggaran';
    $pageDesc   = $pageDesc   ?? 'Kelola data pelanggaran anggota.';
    $pageIcon   = $pageIcon   ?? 'fa-exclamation-triangle';
    $pageColor  = $pageColor  ?? 'amber';

    try {
        $base = User::query()->with(['level', 'status', 'jabatans', 'university', 'major']);

        switch ($levelKey) {
            case 'warning':
            case 'light':
            case 'medium':
            case 'heavy':
                // Belum ada tabel violations → tampilkan empty state.
                // Setelah tabel violations dibuat, tinggal sesuaikan filternya.
                $base->whereRaw('1 = 0');
                break;

            case 'suspend':
                $base->where(function ($q) {
                    $q->where('is_active', false)
                      ->orWhereHas('status', fn ($s) =>
                            $s->whereIn('name', ['suspended', 'suspend', 'ditangguhkan'])
                      );
                });
                break;

            case 'block':
                $base->where(function ($q) {
                    $q->whereHas('status', fn ($s) =>
                            $s->whereIn('name', ['blocked', 'block', 'diblokir'])
                      );
                });
                break;

            case 'index':
            default:
                $base->where(function ($q) {
                    $q->where('is_active', false)
                      ->orWhereHas('status', fn ($s) =>
                            $s->whereIn('name', ['suspended', 'blocked', 'pending', 'expired'])
                      );
                });
                break;
        }

        $members = (clone $base)->orderByDesc('updated_at')->paginate(20);

        $stats = [
            'total'      => (clone $base)->count(),
            'bulan_ini'  => (clone $base)->whereMonth('updated_at', now()->month)
                                          ->whereYear('updated_at', now()->year)->count(),
            'aktif'      => (clone $base)->where('is_active', true)->count(),
            'nonaktif'   => (clone $base)->where('is_active', false)->count(),
        ];
    } catch (\Throwable $e) {
        $members = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $stats   = ['total' => 0, 'bulan_ini' => 0, 'aktif' => 0, 'nonaktif' => 0];
    }

    // Warna & konfigurasi ikon per level
    $palette = [
        'index'   => ['from' => '#6366f1', 'to' => '#8b5cf6', 'bg' => 'rgba(99,102,241,.12)',  'text' => '#6366f1'],
        'warning' => ['from' => '#f59e0b', 'to' => '#d97706', 'bg' => 'rgba(245,158,11,.12)',  'text' => '#d97706'],
        'light'   => ['from' => '#06b6d4', 'to' => '#0891b2', 'bg' => 'rgba(6,182,212,.12)',   'text' => '#0891b2'],
        'medium'  => ['from' => '#f97316', 'to' => '#ea580c', 'bg' => 'rgba(249,115,22,.12)',  'text' => '#ea580c'],
        'heavy'   => ['from' => '#ef4444', 'to' => '#dc2626', 'bg' => 'rgba(239,68,68,.12)',   'text' => '#dc2626'],
        'suspend' => ['from' => '#a855f7', 'to' => '#9333ea', 'bg' => 'rgba(168,85,247,.12)',  'text' => '#9333ea'],
        'block'   => ['from' => '#111827', 'to' => '#374151', 'bg' => 'rgba(17,24,39,.12)',    'text' => '#111827'],
    ];
    $theme = $palette[$levelKey] ?? $palette['index'];
@endphp

@push('styles')
<style>
    .viol-wrap { background:#fff; border-radius:.75rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .dark .viol-wrap { background:#1f2937; }

    .viol-head { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.25rem; }
    .viol-head-left { display:flex; align-items:center; gap:.85rem; min-width:0; }
    .viol-icon {
        width:48px; height:48px; border-radius:.75rem;
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-size:1.15rem; flex-shrink:0;
        background:linear-gradient(135deg, {{ $theme['from'] }}, {{ $theme['to'] }});
        box-shadow:0 4px 14px {{ $theme['bg'] }};
    }
    .viol-head h3 { font-size:1.15rem; font-weight:700; color:#111827; margin:0; line-height:1.25; }
    .dark .viol-head h3 { color:#f3f4f6; }
    .viol-head p  { font-size:.78rem; color:#6b7280; margin:.2rem 0 0; }
    .dark .viol-head p { color:#9ca3af; }

    .viol-nav { display:flex; flex-wrap:wrap; gap:.35rem; margin-bottom:1rem; }
    .viol-nav a {
        padding:.35rem .85rem; border-radius:9999px; font-size:.75rem; font-weight:600;
        background:#f3f4f6; color:#4b5563; text-decoration:none;
        border:1px solid transparent; transition:.15s;
    }
    .viol-nav a:hover { background:#e5e7eb; color:#111827; }
    .viol-nav a.active {
        background:linear-gradient(135deg, {{ $theme['from'] }}, {{ $theme['to'] }});
        color:#fff; box-shadow:0 2px 8px {{ $theme['bg'] }};
    }
    .dark .viol-nav a { background:#374151; color:#e5e7eb; }
    .dark .viol-nav a:hover { background:#4b5563; }
    .dark .viol-nav a.active { color:#fff; }

    .viol-stats { display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:.75rem; margin-bottom:1.25rem; }
    .viol-stat {
        padding:.9rem 1rem; background:#f9fafb; border-radius:.6rem;
        border:1px solid #f3f4f6; display:flex; align-items:center; gap:.7rem;
    }
    .dark .viol-stat { background:#111827; border-color:#374151; }
    .viol-stat .ic {
        width:38px; height:38px; border-radius:.5rem;
        display:flex; align-items:center; justify-content:center;
        font-size:.95rem; color:#fff; flex-shrink:0;
    }
    .viol-stat .ic.blue   { background:linear-gradient(135deg,#6366f1,#8b5cf6); }
    .viol-stat .ic.amber  { background:linear-gradient(135deg,#f59e0b,#d97706); }
    .viol-stat .ic.green  { background:linear-gradient(135deg,#22c55e,#16a34a); }
    .viol-stat .ic.red    { background:linear-gradient(135deg,#ef4444,#dc2626); }
    .viol-stat .lbl { font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; font-weight:700; }
    .dark .viol-stat .lbl { color:#9ca3af; }
    .viol-stat .val { font-size:1.35rem; font-weight:800; color:#111827; line-height:1.1; }
    .dark .viol-stat .val { color:#f3f4f6; }

    .viol-toolbar { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; align-items:center; }
    .viol-search {
        flex:1; min-width:220px; padding:.5rem .9rem;
        border:1px solid #d1d5db; border-radius:.5rem; font-size:.85rem;
        background:#fff;
    }
    .dark .viol-search { background:#374151; border-color:#4b5563; color:#e5e7eb; }
    .viol-search:focus { outline:none; border-color:{{ $theme['text'] }}; box-shadow:0 0 0 3px {{ $theme['bg'] }}; }

    .viol-btn {
        padding:.5rem 1rem; border-radius:.5rem; font-size:.85rem; font-weight:600;
        border:1px solid #d1d5db; background:#fff; color:#4b5563; cursor:pointer;
        display:inline-flex; align-items:center; gap:.35rem; transition:.15s; text-decoration:none;
    }
    .viol-btn:hover { background:#f3f4f6; border-color:#9ca3af; }
    .dark .viol-btn { background:#374151; border-color:#4b5563; color:#e5e7eb; }
    .dark .viol-btn:hover { background:#4b5563; }

    .viol-table { overflow-x:auto; }
    .viol-table table { width:100%; border-collapse:collapse; font-size:.85rem; }
    .viol-table th {
        text-align:left; padding:.65rem .75rem; font-size:.68rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.05em; color:#6b7280;
        border-bottom:1px solid #e5e7eb; background:#f9fafb; white-space:nowrap;
    }
    .dark .viol-table th { color:#9ca3af; border-color:#374151; background:#1f2937; }
    .viol-table td { padding:.65rem .75rem; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    .dark .viol-table td { border-color:#374151; color:#e5e7eb; }
    .viol-table tr:hover td { background:#f9fafb; }
    .dark .viol-table tr:hover td { background:#374151; }

    .viol-badge { display:inline-flex; align-items:center; gap:.28rem; padding:.15rem .55rem; border-radius:9999px; font-size:.65rem; font-weight:700; white-space:nowrap; }
    .viol-badge.level  { background:{{ $theme['bg'] }}; color:{{ $theme['text'] }}; }
    .viol-badge.ok     { background:rgba(34,197,94,.12); color:#16a34a; }
    .viol-badge.warn   { background:rgba(245,158,11,.12); color:#d97706; }
    .viol-badge.bad    { background:rgba(239,68,68,.12); color:#dc2626; }
    .dark .viol-badge.level { background:{{ $theme['bg'] }}; color:#e5e7eb; }
    .dark .viol-badge.ok    { background:rgba(34,197,94,.2); color:#4ade80; }
    .dark .viol-badge.warn  { background:rgba(245,158,11,.2); color:#fbbf24; }
    .dark .viol-badge.bad   { background:rgba(239,68,68,.2); color:#f87171; }

    .viol-empty { text-align:center; padding:3rem 1rem; color:#6b7280; }
    .dark .viol-empty { color:#9ca3af; }
    .viol-empty .em-ic {
        width:80px; height:80px; margin:0 auto .9rem;
        border-radius:50%; display:flex; align-items:center; justify-content:center;
        background:{{ $theme['bg'] }}; color:{{ $theme['text'] }}; font-size:2rem;
    }
    .viol-empty h4 { font-weight:700; color:#374151; font-size:1rem; margin:0 0 .3rem; }
    .dark .viol-empty h4 { color:#e5e7eb; }
    .viol-empty p  { font-size:.82rem; margin:0 0 1rem; }

    .viol-pagination { display:flex; justify-content:space-between; align-items:center; gap:.5rem; margin-top:1rem; font-size:.8rem; color:#6b7280; flex-wrap:wrap; }
    .dark .viol-pagination { color:#9ca3af; }
    .viol-pagination svg { width:1rem; height:1rem; }
</style>
@endpush

<div class="viol-wrap">

    {{-- HEADER --}}
    <div class="viol-head">
        <div class="viol-head-left">
            <div class="viol-icon"><i class="fas {{ $pageIcon }}"></i></div>
            <div>
                <h3>{{ $pageTitle }}</h3>
                <p>{{ $pageDesc }}</p>
            </div>
        </div>
        <a href="{{ route('owner.management.dashboard') }}" class="viol-btn">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    {{-- NAV TABS --}}
    <nav class="viol-nav">
        <a href="{{ route('owner.management.violations.index') }}"   class="{{ $levelKey === 'index'   ? 'active' : '' }}"><i class="fas fa-list"></i> Semua</a>
        <a href="{{ route('owner.management.violations.warning') }}" class="{{ $levelKey === 'warning' ? 'active' : '' }}"><i class="fas fa-exclamation-triangle"></i> Teguran</a>
        <a href="{{ route('owner.management.violations.light') }}"   class="{{ $levelKey === 'light'   ? 'active' : '' }}"><i class="fas fa-info-circle"></i> Ringan</a>
        <a href="{{ route('owner.management.violations.medium') }}"  class="{{ $levelKey === 'medium'  ? 'active' : '' }}"><i class="fas fa-bolt"></i> Sedang</a>
        <a href="{{ route('owner.management.violations.heavy') }}"   class="{{ $levelKey === 'heavy'   ? 'active' : '' }}"><i class="fas fa-fire"></i> Berat</a>
        <a href="{{ route('owner.management.violations.suspend') }}" class="{{ $levelKey === 'suspend' ? 'active' : '' }}"><i class="fas fa-pause-circle"></i> Suspend</a>
        <a href="{{ route('owner.management.violations.block') }}"   class="{{ $levelKey === 'block'   ? 'active' : '' }}"><i class="fas fa-ban"></i> Blokir</a>
    </nav>

    {{-- STATS --}}
    <div class="viol-stats">
        <div class="viol-stat">
            <div class="ic blue"><i class="fas fa-users"></i></div>
            <div><div class="lbl">Total</div><div class="val">{{ $stats['total'] }}</div></div>
        </div>
        <div class="viol-stat">
            <div class="ic amber"><i class="fas fa-calendar-day"></i></div>
            <div><div class="lbl">Bulan Ini</div><div class="val">{{ $stats['bulan_ini'] }}</div></div>
        </div>
        <div class="viol-stat">
            <div class="ic green"><i class="fas fa-check-circle"></i></div>
            <div><div class="lbl">Aktif</div><div class="val">{{ $stats['aktif'] }}</div></div>
        </div>
        <div class="viol-stat">
            <div class="ic red"><i class="fas fa-user-slash"></i></div>
            <div><div class="lbl">Non-Aktif</div><div class="val">{{ $stats['nonaktif'] }}</div></div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="viol-toolbar">
        <input type="text" id="viol-search" class="viol-search" placeholder="🔎 Cari NIM / Nama / Email...">
        <button type="button" class="viol-btn" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
        <button type="button" class="viol-btn" onclick="location.reload()"><i class="fas fa-sync-alt"></i> Muat Ulang</button>
    </div>

    {{-- TABLE --}}
    <div class="viol-table">
        <table id="viol-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Jabatan</th>
                    <th>Poin</th>
                    <th>Status</th>
                    <th>Terakhir Diubah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $i => $m)
                    @php
                        $lvlName   = $m->level->display_name ?? $m->level->name ?? '-';
                        $jabatan   = $m->jabatans->pluck('name')->take(2)->implode(', ') ?: '—';
                        $statusNm  = $m->status->display_name ?? $m->status->name ?? ($m->is_active ? 'Aktif' : 'Nonaktif');
                        $isActive  = (bool) ($m->is_active ?? false);
                        $statusCls = $isActive ? 'ok' : 'bad';
                    @endphp
                    <tr data-search="{{ strtolower(($m->nim ?? '') . ' ' . ($m->name ?? '') . ' ' . ($m->email ?? '')) }}">
                        <td class="text-center" style="color:#6b7280;">{{ $members->firstItem() + $i }}</td>
                        <td style="font-family:ui-monospace,monospace; font-size:.82rem;">{{ $m->nim ?? '-' }}</td>
                        <td><strong>{{ $m->name ?? '-' }}</strong></td>
                        <td style="font-size:.8rem; color:#6b7280;">{{ $m->email ?? '-' }}</td>
                        <td><span class="viol-badge level">{{ $lvlName }}</span></td>
                        <td style="font-size:.82rem;">{{ $jabatan }}</td>
                        <td><strong style="color:{{ $theme['text'] }};">{{ $m->total_points ?? 0 }}</strong></td>
                        <td><span class="viol-badge {{ $statusCls }}">{{ $statusNm }}</span></td>
                        <td style="font-size:.8rem; color:#6b7280;">{{ optional($m->updated_at)->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="viol-empty">
                                <div class="em-ic"><i class="fas {{ $pageIcon }}"></i></div>
                                <h4>Belum ada data untuk kategori ini</h4>
                                <p>{{ $pageDesc }}</p>
                                @if (in_array($levelKey, ['warning','light','medium','heavy']))
                                    <p style="font-size:.75rem; opacity:.75;">
                                        💡 Kategori ini memerlukan tabel <code>violations</code>.
                                        Setelah tabel tersebut dibuat, sesuaikan filter di
                                        <code>_content.blade.php</code>.
                                    </p>
                                @endif
                                <a href="{{ route('owner.management.violations.index') }}" class="viol-btn" style="margin-top:.25rem;">
                                    <i class="fas fa-arrow-left"></i> Lihat Semua
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($members instanceof \Illuminate\Pagination\LengthAwarePaginator && $members->total() > 0)
        <div class="viol-pagination">
            <div>Menampilkan {{ $members->firstItem() }}–{{ $members->lastItem() }} dari {{ $members->total() }}</div>
            <div>{{ $members->withQueryString()->links() }}</div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('viol-search');
    const rows  = document.querySelectorAll('#viol-table tbody tr[data-search]');
    if (!input) return;

    input.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        rows.forEach(row => {
            row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
        });
    });
});
</script>
@endpush
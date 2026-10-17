@extends('layouts.roles.owner')

@section('title', 'Manajemen Organisasi – Owner UNICODE')
@section('page-title', 'Manajemen Organisasi')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-color, #06b6d4), var(--accent-light, #3b82f6));
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .stat-card:hover::before { opacity: 1; }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08), 0 4px 10px rgba(0,0,0,0.02);
    }
    .dark .stat-card:hover {
        box-shadow: 0 12px 30px rgba(0,0,0,0.3), 0 4px 10px rgba(0,0,0,0.1);
    }
    .stat-number {
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
    }
    .stat-trend {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    .stat-trend.up { background: rgba(34,197,94,0.15); color: #22c55e; }
    .stat-trend.down { background: rgba(239,68,68,0.15); color: #ef4444; }
    .stat-trend.neutral { background: rgba(107,114,128,0.15); color: #6b7280; }
    .quick-action-card {
        transition: all 0.25s ease;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 0.75rem;
    }
    .quick-action-card:hover {
        transform: translateY(-3px);
        border-color: rgba(59,130,246,0.2);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }
    .dark .quick-action-card:hover {
        border-color: rgba(59,130,246,0.15);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    .activity-item {
        border-left: 3px solid transparent;
        padding-left: 1rem;
        transition: all 0.2s ease;
        border-radius: 0.5rem;
    }
    .activity-item:hover {
        border-left-color: #06b6d4;
        background: rgba(6,182,212,0.04);
    }
    .dark .activity-item:hover { background: rgba(6,182,212,0.06); }
    .progress-bar {
        height: 6px;
        border-radius: 9999px;
        background: #e5e7eb;
        overflow: hidden;
    }
    .dark .progress-bar { background: #374151; }
    .progress-bar .fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 1s ease;
    }
    .section-title {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9ca3af;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, #e5e7eb, transparent);
    }
    .dark .section-title::after { background: linear-gradient(90deg, #374151, transparent); }
    .badge-approval {
        background: rgba(234,179,8,0.15);
        color: #ca8a04;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .dark .badge-approval { background: rgba(234,179,8,0.2); color: #fbbf24; }
    .mini-chart {
        display: flex;
        align-items: flex-end;
        gap: 4px;
        height: 40px;
    }
    .mini-chart .bar {
        flex: 1;
        border-radius: 3px 3px 0 0;
        transition: height 0.5s ease;
        min-width: 6px;
    }

    /* Tabel */
    .table-container { overflow-x: auto; }
    .table-container table {
        min-width: 900px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 4px;
    }
    .table-container th {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding: 0.6rem 0.8rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    .dark .table-container th { color: #9ca3af; border-bottom-color: #374151; }
    .table-container td {
        padding: 0.6rem 0.8rem;
        font-size: 0.8rem;
        background: white;
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .dark .table-container td { background: #1f2937; color: #e5e7eb; }
    .table-container tr:hover td { background: #f9fafb; }
    .dark .table-container tr:hover td { background: #374151; }
    .badge-role {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        background: rgba(99,102,241,0.12);
        color: #6366f1;
    }
    .dark .badge-role { background: rgba(99,102,241,0.2); color: #818cf8; }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .btn-action.view {
        color: #3b82f6;
        background: rgba(59,130,246,0.08);
    }
    .btn-action.view:hover { background: rgba(59,130,246,0.16); }
</style>
@endpush

@section('content')
{{-- ============================================================ --}}
{{-- STATISTIK UTAMA --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    @foreach($stats as $stat)
    <div class="stat-card bg-white dark:bg-gray-800 shadow-sm p-5 border border-gray-200/60 dark:border-gray-700/60"
         style="--accent-color: #{{ ['blue'=>'3b82f6','green'=>'22c55e','yellow'=>'eab308','cyan'=>'06b6d4','amber'=>'f59e0b','red'=>'ef4444','purple'=>'8b5cf6','teal'=>'14b8a6','indigo'=>'6366f1','pink'=>'ec4899','gray'=>'6b7280'][$stat['color']] }}; --accent-light: #{{ ['blue'=>'93bbfc','green'=>'86efac','yellow'=>'fde047','cyan'=>'67e8f9','amber'=>'fcd34d','red'=>'fca5a5','purple'=>'c4b5fd','teal'=>'5eead4','indigo'=>'a5b4fc','pink'=>'f9a8d4','gray'=>'9ca3af'][$stat['color']] }};">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                <p class="stat-number text-gray-900 dark:text-white mt-1">{{ number_format($stat['value']) }}</p>
                @if(isset($stat['trend']))
                <span class="stat-trend {{ $stat['trendType'] }} mt-1">
                    <i class="fas fa-{{ $stat['trendType'] === 'up' ? 'arrow-up' : ($stat['trendType'] === 'down' ? 'arrow-down' : 'minus') }} text-[8px]"></i>
                    {{ $stat['trend'] }}
                </span>
                @endif
            </div>
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: rgba({{ ['blue'=>'59,130,246','green'=>'34,197,94','yellow'=>'234,179,8','cyan'=>'6,182,212','amber'=>'245,158,11','red'=>'239,68,68','purple'=>'139,92,246','teal'=>'20,184,166','indigo'=>'99,102,241','pink'=>'236,72,153','gray'=>'107,114,128'][$stat['color']] }}, 0.12);">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-500 dark:text-{{ $stat['color'] }}-400 text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ============================================================ --}}
{{-- STATISTIK TAMBAHAN --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 md:gap-4 mb-6">
    @foreach($extraStats as $stat)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 md:p-4 border border-gray-200/60 dark:border-gray-700/60 text-center hover:shadow-md transition-shadow">
        <div class="text-{{ $stat['color'] }}-500 dark:text-{{ $stat['color'] }}-400 text-lg mb-1">
            <i class="fas {{ $stat['icon'] }}"></i>
        </div>
        <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stat['value']) }}</p>
        <p class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ============================================================ --}}
{{-- AKTIVITAS & QUICK ACTIONS --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 md:p-6 border border-gray-200/60 dark:border-gray-700/60">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i> Aktivitas Terbaru
            </h3>
            <a href="{{ route('owner.audit.activity') }}" class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-medium flex items-center gap-1">
                Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        @if($activities->count() > 0)
        <div class="space-y-2">
            @foreach($activities as $activity)
            <div class="activity-item flex items-center gap-3 py-2 px-3 rounded-lg transition-colors" style="border-left-color: {{ ['blue'=>'#3b82f6','green'=>'#22c55e','gray'=>'#6b7280','cyan'=>'#06b6d4','amber'=>'#f59e0b','red'=>'#ef4444','purple'=>'#8b5cf6','teal'=>'#14b8a6','indigo'=>'#6366f1','pink'=>'#ec4899'][$activity->color] ?? '#6b7280' }};">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background: rgba({{ ['blue'=>'59,130,246','green'=>'34,197,94','gray'=>'107,114,128','cyan'=>'6,182,212','amber'=>'245,158,11','red'=>'239,68,68','purple'=>'139,92,246','teal'=>'20,184,166','indigo'=>'99,102,241','pink'=>'236,72,153'][$activity->color] ?? '107,114,128' }}, 0.12);">
                    <i class="fas {{ $activity->icon }} text-{{ $activity->color }}-500 dark:text-{{ $activity->color }}-400 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700 dark:text-gray-300 truncate">
                        <span class="font-semibold">{{ $activity->user }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $activity->action }}</span>
                    </p>
                </div>
                <span class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $activity->time }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-400 dark:text-gray-500">
            <i class="fas fa-inbox text-2xl mb-2 block"></i>
            <p class="text-sm">Belum ada aktivitas yang tercatat</p>
        </div>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 md:p-6 border border-gray-200/60 dark:border-gray-700/60">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white flex items-center gap-2 mb-4">
            <i class="fas fa-rocket text-amber-500"></i> Aksi Cepat
        </h3>
        <div class="grid grid-cols-2 gap-2">
            @foreach($quickActions as $action)
            <a href="{{ route($action['route']) }}"
               class="quick-action-card flex flex-col items-center justify-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center mb-1.5"
                     style="background: rgba({{ ['blue'=>'59,130,246','indigo'=>'99,102,241','cyan'=>'6,182,212','purple'=>'139,92,246','green'=>'34,197,94','amber'=>'245,158,11','pink'=>'236,72,153','gray'=>'107,114,128'][$action['color']] ?? '107,114,128' }}, 0.12);">
                    <i class="fas {{ $action['icon'] }} text-{{ $action['color'] }}-500 dark:text-{{ $action['color'] }}-400 text-sm"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-700 dark:text-gray-300 leading-tight">{{ $action['label'] }}</span>
            </a>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200/60 dark:border-gray-700/60">
            <a href="{{ route('owner.approvals.pending') }}"
               class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 hover:bg-amber-100 dark:hover:bg-amber-900/20 transition">
                <span class="text-sm font-medium text-amber-700 dark:text-amber-400 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Approval Pending
                </span>
                <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ 7 }}</span>
            </a>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- RINGKASAN ORGANISASI + PROGRESS --}}
{{-- ============================================================ --}}
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 md:p-6 border border-gray-200/60 dark:border-gray-700/60">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-simple text-gray-400"></i> Ringkasan User
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($allUsers->total()) }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($totalActive) }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">Nonaktif</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ number_format($allUsers->total() - $totalActive) }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">Terverifikasi</p>
                <p class="text-lg font-bold text-cyan-600 dark:text-cyan-400">{{ number_format($totalVerified) }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">Belum Verif</p>
                <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ number_format($allUsers->total() - $totalVerified) }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                <p class="text-xs text-gray-500 dark:text-gray-400">Event Aktif</p>
                <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalEvents) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 md:p-6 border border-gray-200/60 dark:border-gray-700/60">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line text-gray-400"></i> Statistik Keanggotaan
        </h4>
        <div class="space-y-3">
            @php
                $totalUsers = max($allUsers->total(), 1);
                $percentActive = round(($totalActive / $totalUsers) * 100);
                $percentVerified = round(($totalVerified / $totalUsers) * 100);
                $percentQR = round(($qrActive / max($totalQR, 1)) * 100);
            @endphp
            <div>
                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>Akun Aktif</span>
                    <span>{{ $percentActive }}%</span>
                </div>
                <div class="progress-bar mt-1">
                    <div class="fill bg-green-500" style="width: {{ $percentActive }}%;"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>Akun Terverifikasi</span>
                    <span>{{ $percentVerified }}%</span>
                </div>
                <div class="progress-bar mt-1">
                    <div class="fill bg-cyan-500" style="width: {{ $percentVerified }}%;"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>QR Aktif</span>
                    <span>{{ $percentQR }}%</span>
                </div>
                <div class="progress-bar mt-1">
                    <div class="fill bg-purple-500" style="width: {{ $percentQR }}%;"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>Event Aktif</span>
                    <span>{{ $totalEvents > 0 ? 'Ada '.$totalEvents.' event' : 'Tidak ada event' }}</span>
                </div>
                <div class="progress-bar mt-1">
                    <div class="fill bg-yellow-500" style="width: {{ min(100, $totalEvents * 10) }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- TABEL SEMUA PENGGUNA --}}
{{-- ============================================================ --}}
<div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 md:p-6 border border-gray-200/60 dark:border-gray-700/60">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-users text-indigo-500"></i> Semua Pengguna
        </h4>
        <p class="text-xs text-gray-500 dark:text-gray-400">Total {{ $allUsers->total() }} pengguna terdaftar</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Universitas</th>
                    <th>Level</th>
                    <th>Dibuat</th>
                    <th class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allUsers as $index => $user)
                <tr>
                    <td class="text-center font-mono text-sm text-gray-500">{{ $loop->iteration + ($allUsers->currentPage() - 1) * $allUsers->perPage() }}</td>
                    <td class="font-mono text-sm">{{ $user->nim ?? '-' }}</td>
                    <td class="font-medium">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366F1&color=fff&size=32" alt="Avatar" class="h-7 w-7 rounded-full border border-gray-200 dark:border-gray-600 flex-shrink-0">
                            <span>{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>{{ $user->email ?? '-' }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ optional($user->university)->name ?? '-' }}</td>
                    <td>
                        <span class="badge-role">
                            <i class="fas fa-{{ $user->level_id == 1 ? 'crown' : ($user->level_id == 2 ? 'shield-alt' : ($user->level_id == 3 ? 'users-cog' : ($user->level_id == 4 ? 'user-tie' : ($user->level_id == 5 ? 'user-graduate' : 'globe')))) }} mr-1 text-[10px]"></i>
                            {{ optional($user->level)->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex justify-center">
                            <a href="{{ route('owner.management.users.show', $user->id) }}" 
                               class="btn-action view" title="Lihat Detail Lengkap">
                                <i class="fas fa-eye"></i> <span class="hidden sm:inline">Detail</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Menampilkan {{ $allUsers->firstItem() ?? 0 }} - {{ $allUsers->lastItem() ?? 0 }} dari {{ $allUsers->total() }} pengguna
        </div>
        <div>
            {{ $allUsers->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection
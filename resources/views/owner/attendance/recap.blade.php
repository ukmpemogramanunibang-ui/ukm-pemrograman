@extends('layouts.roles.owner')

@section('content')
<div class="p-6 max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i> Rekap Absensi
            </h1>
            <p class="text-sm text-gray-500 mt-1">Rekap kehadiran per user dalam periode tertentu.</p>
        </div>
        <a href="{{ route('owner.attendance.history') }}"
           class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300">
            <i class="fas fa-history mr-1"></i> Riwayat
        </a>
    </div>

    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-500 text-white text-sm font-medium">
                    <i class="fas fa-filter mr-1"></i> Terapkan
                </button>
                <a href="{{ route('owner.attendance.recap') }}" class="px-4 py-2 rounded-lg border text-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 shadow border">
            <div class="text-xs uppercase text-gray-500">Total Sesi</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_sessions'] }}</div>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-gray-800 shadow border">
            <div class="text-xs uppercase text-gray-500">Total Record</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['total_records'] }}</div>
        </div>
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 shadow border border-emerald-200">
            <div class="text-xs uppercase text-emerald-600">Hadir</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['total_hadir'] }}</div>
        </div>
        <div class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 shadow border border-yellow-200">
            <div class="text-xs uppercase text-yellow-600">Izin</div>
            <div class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['total_izin'] }}</div>
        </div>
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 shadow border border-red-200">
            <div class="text-xs uppercase text-red-600">Bolos</div>
            <div class="text-2xl font-bold text-red-700 mt-1">{{ $stats['total_bolos'] }}</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <div class="font-semibold text-gray-800 dark:text-white">Rekap Per User</div>
            <div class="text-xs text-gray-500">Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">NIM</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Hadir</th>
                        <th class="px-4 py-3 text-center">Tepat</th>
                        <th class="px-4 py-3 text-center">Terlambat</th>
                        <th class="px-4 py-3 text-center">Izin</th>
                        <th class="px-4 py-3 text-center">Bolos</th>
                        <th class="px-4 py-3 text-center">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($records as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $r->snapshot_name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $r->snapshot_nim ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $r->total }}</td>
                            <td class="px-4 py-3 text-center text-emerald-600 font-semibold">{{ $r->hadir }}</td>
                            <td class="px-4 py-3 text-center text-green-600">{{ $r->tepat }}</td>
                            <td class="px-4 py-3 text-center text-yellow-600">{{ $r->terlambat }}</td>
                            <td class="px-4 py-3 text-center text-blue-600">{{ $r->izin }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ $r->bolos }}</td>
                            <td class="px-4 py-3 text-center font-bold text-purple-600">{{ $r->total_points }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">Belum ada data rekap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $records->links() }}</div>
        @endif
    </div>
</div>
@endsection
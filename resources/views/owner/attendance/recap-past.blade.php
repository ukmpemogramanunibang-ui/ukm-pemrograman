@extends('layouts.roles.owner')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-archive text-indigo-500 mr-2"></i> Rekap Masa Lalu
            </h1>
            <p class="text-sm text-gray-500 mt-1">Rekap absensi per tahun.</p>
        </div>
    </div>

    {{-- Pilih Tahun --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($availableYears as $y)
            <a href="{{ route('owner.attendance.recap.past', ['year' => $y]) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium
                      {{ $year == $y ? 'bg-indigo-500 text-white shadow' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-100' }}">
                {{ $y }}
            </a>
        @endforeach
    </div>

    {{-- Ringkasan Per Bulan --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Ringkasan Bulanan — {{ $year }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @php
                $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            @endphp
            @foreach($byMonth as $m => $data)
                <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                    <div class="text-xs font-semibold text-gray-500">{{ $namaBulan[$m-1] }}</div>
                    <div class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $data['sessions'] }} sesi</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $data['hadir'] }} hadir / {{ $data['records'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Daftar Sesi --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 font-semibold">
            Daftar Sesi Tahun {{ $year }}
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Judul</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3">{{ $s->scheduled_start_at?->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium">{{ $s->title }}</td>
                            <td class="px-4 py-3 text-xs">{{ ucfirst($s->attendance_type) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">{{ ucfirst($s->session_status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('owner.attendance.show', $s) }}" class="text-cyan-600 text-xs hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sessions->hasPages())
            <div class="px-4 py-3 border-t">{{ $sessions->links() }}</div>
        @endif
    </div>
</div>
@endsection
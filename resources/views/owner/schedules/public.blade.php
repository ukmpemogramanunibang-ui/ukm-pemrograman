@extends('layouts.roles.owner')

@section('title', 'Jadwal Publik – Owner')

@push('styles')
<style>
    .schedule-row {
        transition: all 0.15s ease;
    }
    .schedule-row:hover {
        background: rgba(6, 182, 212, 0.04);
    }
    .badge-status {
        display: inline-block;
        padding: 2px 12px;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-type {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 9999px;
        font-size: 0.6rem;
        font-weight: 500;
    }
    .fade-in {
        animation: fadeInUp 0.4s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in:nth-child(1) { animation-delay: 0.05s; }
    .fade-in:nth-child(2) { animation-delay: 0.10s; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 fade-in">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.schedules.index') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition">Jadwal</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Jadwal Publik</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 mt-2">
                <i class="fas fa-globe text-cyan-500 text-2xl"></i>
                Jadwal Publik
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Jadwal yang sedang aktif dan terlihat oleh publik.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('owner.schedules.index') }}" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
                <i class="fas fa-list"></i> Semua Jadwal
            </a>
        </div>
    </div>

    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 fade-in">
        @php
            $total = $schedules->total();
            $todayCount = $schedules->filter(fn($s) => $s->start_datetime->isToday())->count();
            $weekCount = $schedules->filter(fn($s) => $s->start_datetime->isCurrentWeek())->count();
            $monthCount = $schedules->filter(fn($s) => $s->start_datetime->isCurrentMonth())->count();
        @endphp
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Publik</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $total }}</p>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-day text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hari Ini</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $todayCount }}</p>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-week text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Minggu Ini</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $weekCount }}</p>
            </div>
        </div>
        <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bulan Ini</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $monthCount }}</p>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 fade-in">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    <i class="fas fa-search mr-1"></i> Cari
                </label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Judul, jenis, keterangan..."
                       class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Ruangan</label>
                <select name="room_id" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 transition min-w-[150px]">
                    <option value="">Semua Ruangan</option>
                    @foreach($rooms ?? [] as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hari</label>
                <select name="day" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 transition min-w-[120px]">
                    <option value="">Semua Hari</option>
                    @php $dayMap = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu']; @endphp
                    @foreach($dayMap as $key => $label)
                        <option value="{{ $key }}" {{ request('day') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white text-sm font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('owner.schedules.public') }}" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-undo-alt"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ===== HEADER TABEL ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 fade-in">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Jadwal Publik</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $schedules->total() }} jadwal publik ditemukan
                @if(request()->has('search') || request()->has('room_id') || request()->has('day'))
                    <a href="{{ route('owner.schedules.public') }}" class="text-xs text-red-400 hover:text-red-600 ml-2">
                        <i class="fas fa-times mr-1"></i> Hapus filter
                    </a>
                @endif
            </p>
        </div>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3.5">#</th>
                        <th class="px-4 py-3.5 min-w-[150px]">Judul</th>
                        <th class="px-4 py-3.5 min-w-[160px]">Ruangan</th>
                        <th class="px-4 py-3.5 text-center w-24">Hari</th>
                        <th class="px-4 py-3.5 min-w-[100px]">Jenis</th>
                        <th class="px-4 py-3.5 text-nowrap">Mulai</th>
                        <th class="px-4 py-3.5 text-nowrap">Selesai</th>
                        <th class="px-4 py-3.5 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($schedules as $schedule)
                    @php
                        $dayName = $schedule->start_datetime->isoFormat('dddd');
                        $dayColors = [
                            'Senin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'Selasa' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300',
                            'Rabu' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
                            'Kamis' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                            'Jumat' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'Sabtu' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'Minggu' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        ];
                        $dayColor = $dayColors[$dayName] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                    @endphp
                    <tr class="schedule-row">
                        <td class="px-4 py-3 text-center text-xs text-gray-400">{{ $loop->iteration + ($schedules->currentPage() - 1) * $schedules->perPage() }}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $schedule->title }}</p>
                            @if($schedule->description)
                                <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ Str::limit($schedule->description, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('owner.rooms.show', $schedule->room_id) }}"
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline font-medium transition flex items-center gap-1.5"
                               title="Lihat detail ruangan">
                                <i class="fas fa-door-open text-xs"></i>
                                <span class="truncate max-w-[120px]">{{ $schedule->room->name ?? '-' }}</span>
                            </a>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $schedule->room->room_code ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge-day {{ $dayColor }}">
                                {{ $dayName }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                <span class="badge-type bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300">
                                    {{ $schedule->activity_type ?: 'Umum' }}
                                </span>
                                @if($schedule->recurrence_type && $schedule->recurrence_type != 'Sekali')
                                    <span class="badge-type bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300">
                                        <i class="fas fa-sync-alt text-[8px]"></i> {{ $schedule->recurrence_type }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-nowrap text-sm">{{ $schedule->start_datetime ? $schedule->start_datetime->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-nowrap text-sm">{{ $schedule->end_datetime ? $schedule->end_datetime->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge-status bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                {{ $schedule->status ?: 'Aktif' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-4xl text-gray-300 dark:text-gray-500 mb-4">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <p class="text-base font-medium text-gray-600 dark:text-gray-300">Belum ada jadwal publik</p>
                                <p class="text-sm text-gray-400 mt-1">Jadwal yang dipublikasikan akan muncul di sini.</p>
                                <a href="{{ route('owner.schedules.upload') }}" class="mt-4 px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                                    <i class="fas fa-upload"></i> Upload Jadwal
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $schedules->firstItem() ?? 0 }} - {{ $schedules->lastItem() ?? 0 }} dari {{ $schedules->total() }} data
            </div>
            <div>
                {{ $schedules->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
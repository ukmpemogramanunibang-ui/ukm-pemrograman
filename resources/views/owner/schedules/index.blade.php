@extends('layouts.roles.owner')

@section('title', 'Semua Jadwal – Owner')

@push('styles')
<style>
    .schedule-row {
        transition: all 0.15s ease;
    }
    .schedule-row:hover {
        background: rgba(6, 182, 212, 0.04);
    }
    .badge-day {
        display: inline-block;
        padding: 2px 12px;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.02em;
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
    .stat-card {
        transition: all 0.3s;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
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
    .fade-in:nth-child(3) { animation-delay: 0.15s; }
    .dark .badge-day { background: rgba(255,255,255,0.08); }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 fade-in">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Semua Jadwal</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 mt-2">
                <i class="fas fa-calendar-alt text-green-500 text-2xl"></i>
                Semua Jadwal
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Kelola jadwal seluruh ruangan secara terpusat.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('owner.schedules.upload') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-upload"></i> Upload Jadwal
            </a>
            <a href="{{ route('owner.schedules.public') }}" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-globe"></i> Jadwal Publik
            </a>
            <a href="{{ route('owner.schedules.history') }}" class="px-5 py-2.5 bg-purple-500 hover:bg-purple-600 text-white text-sm font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-history"></i> Riwayat
            </a>
        </div>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>{!! session('success') !!}</div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700 dark:hover:text-green-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
            <div>{!! session('warning') !!}</div>
            <button onclick="this.parentElement.remove()" class="ml-auto text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 fade-in">
        @php
            $stats = [
                ['label' => 'Total Jadwal', 'value' => $schedules->total(), 'icon' => 'fa-calendar-alt', 'color' => 'blue'],
                ['label' => 'Aktif', 'value' => $schedules->where('status', 'Aktif')->count(), 'icon' => 'fa-play', 'color' => 'green'],
                ['label' => 'Selesai', 'value' => $schedules->where('status', 'Selesai')->count(), 'icon' => 'fa-check', 'color' => 'blue'],
                ['label' => 'Dibatalkan', 'value' => $schedules->where('status', 'Dibatalkan')->count(), 'icon' => 'fa-times', 'color' => 'red'],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                    @if($stat['color'] == 'blue') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                    @elseif($stat['color'] == 'green') bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                    @elseif($stat['color'] == 'red') bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 @endif
                ">
                    <i class="fas {{ $stat['icon'] }} text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $stat['value'] }}</p>
                </div>
            </div>
        @endforeach
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
                       class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 transition min-w-[130px]">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>🟢 Aktif</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>🔵 Selesai</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>🔴 Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Ruangan</label>
                <select name="room_id" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 transition min-w-[150px]">
                    <option value="">Semua Ruangan</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hari</label>
                <select name="day" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-green-500 transition min-w-[120px]">
                    <option value="">Semua Hari</option>
                    @php $dayMap = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu']; @endphp
                    @foreach($dayMap as $key => $label)
                        <option value="{{ $key }}" {{ request('day') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-sm font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('owner.schedules.index') }}" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-undo-alt"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ===== HEADER TABEL ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 fade-in">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Jadwal</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $schedules->total() }} jadwal ditemukan
                @if(request()->has('search') || request()->has('status') || request()->has('room_id') || request()->has('day'))
                    <span class="text-xs text-blue-500">(dengan filter)</span>
                    <a href="{{ route('owner.schedules.index') }}" class="text-xs text-red-400 hover:text-red-600 ml-2">
                        <i class="fas fa-times mr-1"></i> Hapus filter
                    </a>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400">Total: {{ $schedules->total() }}</span>
        </div>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden fade-in">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3.5">#</th>
                        <th class="px-4 py-3.5 min-w-[150px]">Judul</th>
                        <th class="px-4 py-3.5 min-w-[160px]">Ruangan</th>
                        <th class="px-4 py-3.5 text-center w-24">Hari</th>
                        <th class="px-4 py-3.5 min-w-[100px]">Jenis</th>
                        <th class="px-4 py-3.5 text-center w-12">Gambar</th>
                        <th class="px-4 py-3.5 text-nowrap">Mulai</th>
                        <th class="px-4 py-3.5 text-nowrap">Selesai</th>
                        <th class="px-4 py-3.5 text-center w-24">Status</th>
                        <th class="px-4 py-3.5 text-center w-44">Aksi</th>
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
                        $statusColors = [
                            'Aktif' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'Selesai' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'Dibatalkan' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                        ];
                        $statusColor = $statusColors[$schedule->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                        $images = is_array($schedule->images) ? $schedule->images : json_decode($schedule->images, true);
                        $firstImage = $images[0] ?? null;
                    @endphp
                    <tr class="schedule-row cursor-pointer" data-href="{{ route('owner.schedules.show', $schedule) }}" tabindex="0">
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
                        <td class="px-4 py-3 text-center">
                            @if($firstImage)
                                <a href="{{ asset('storage/' . $firstImage) }}" target="_blank"
                                   class="inline-block w-10 h-10 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 hover:shadow-md transition">
                                    <img src="{{ asset('storage/' . $firstImage) }}" alt="Gambar jadwal" class="w-full h-full object-cover">
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-nowrap text-sm">{{ $schedule->start_datetime ? $schedule->start_datetime->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-nowrap text-sm">{{ $schedule->end_datetime ? $schedule->end_datetime->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge-status {{ $statusColor }}">
                                {{ $schedule->status ?: 'Aktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Tombol Show --}}
                                <a href="{{ route('owner.schedules.show', $schedule) }}"
                                   class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-1">
                                    <i class="fas fa-eye"></i> Show
                                </a>

                                {{-- Tombol ke Detail Ruangan --}}
                                <a href="{{ route('owner.rooms.show', $schedule->room_id) }}"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                   title="Lihat detail ruangan">
                                    <i class="fas fa-door-open"></i>
                                </a>

                                {{-- 🔥 TOMBOL HAPUS --}}
                                <form action="{{ route('owner.schedules.destroy', $schedule) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus jadwal \'{{ $schedule->title }}\'? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                            title="Hapus jadwal">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-16 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-4xl text-gray-300 dark:text-gray-500 mb-4">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <p class="text-base font-medium text-gray-600 dark:text-gray-300">Belum ada jadwal</p>
                                <p class="text-sm text-gray-400 mt-1">Mulai upload jadwal ruangan sekarang.</p>
                                <a href="{{ route('owner.schedules.upload') }}" class="mt-4 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
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

{{-- ===== JAVASCRIPT ===== --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== Double Click / Tap untuk membuka Show =====
    document.querySelectorAll('.schedule-row').forEach(row => {
        const href = row.dataset.href;

        row.addEventListener('dblclick', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                window.location.href = href;
            }
        });

        let touchTimeout = null;
        row.addEventListener('touchstart', function(e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            if (touchTimeout) {
                clearTimeout(touchTimeout);
                touchTimeout = null;
                window.location.href = href;
                return;
            }
            touchTimeout = setTimeout(() => {
                touchTimeout = null;
            }, 300);
        });

        row.addEventListener('click', function(e) {
            if (window.innerWidth > 768) {
                if (!e.target.closest('a') && !e.target.closest('button')) {
                    window.location.href = href;
                }
            }
        });

        // Keyboard accessibility (Enter/Space)
        row.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.location.href = href;
            }
        });
    });
});
</script>
@endpush
@endsection
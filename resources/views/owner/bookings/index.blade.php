@extends('layouts.roles.owner')

@section('title', 'Daftar Booking – UNICODE')
@section('page-title', 'Daftar Booking')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📋 Daftar Booking</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola semua pemesanan ruangan di UNICODE.</p>
        </div>
        <a href="{{ route('owner.rooms.private') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition shadow-md shadow-cyan-500/20">
            <i class="fas fa-plus mr-2"></i> Booking Baru
        </a>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Menunggu</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['menunggu'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Konflik</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['konflik'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Disetujui</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['disetujui'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Selesai</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['selesai'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Ditolak</p>
            <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['ditolak'] }}</p>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700 mb-6">
        <form method="GET" action="{{ route('owner.bookings.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs text-gray-500 dark:text-gray-400">🔍 Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul / Pemesan / Telepon" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500 dark:text-gray-400">Status</label>
                <select name="status" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Persetujuan" {{ request('status') == 'Menunggu Persetujuan' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Konflik" {{ request('status') == 'Konflik' ? 'selected' : '' }}>Konflik</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('owner.bookings.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Judul</th>
                        <th class="px-4 py-3 text-left">Ruangan</th>
                        <th class="px-4 py-3 text-left">Pemesan</th>
                        <th class="px-4 py-3 text-left">Mulai</th>
                        <th class="px-4 py-3 text-left">Selesai</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Disetujui Oleh</th>
                        <th class="px-4 py-3 text-left">Waktu Disetujui</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ $booking->title }}</td>
                        <td class="px-4 py-3">{{ $booking->room->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $booking->requester_name }}</td>
                        <td class="px-4 py-3">{{ $booking->start_datetime->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $booking->end_datetime->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($booking->status == 'Menunggu Persetujuan') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                @elseif($booking->status == 'Konflik') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                @elseif($booking->status == 'Disetujui') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                @elseif($booking->status == 'Selesai') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($booking->status == 'Ditolak') bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @endif
                            ">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $booking->approver ? $booking->approver->name : '-' }}</td>
                        <td class="px-4 py-3">{{ $booking->approved_at ? $booking->approved_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('owner.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-times text-4xl block mb-3 opacity-30"></i>
                            Belum ada booking.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t dark:border-gray-700">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    </div>

</div>
@endsection
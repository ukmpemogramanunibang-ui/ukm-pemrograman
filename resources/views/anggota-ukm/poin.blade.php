@extends('layouts.role.anggota')
@section('title', 'Poin & Prestasi')
@section('breadcrumb', 'Poin & Prestasi')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">🏅 Poin & Prestasi</h2>

        @php
            $totalPoin = \App\Models\PointHistory::where('user_id', Auth::id())->sum('points') ?: 0;
            $histories = \App\Models\PointHistory::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        @endphp

        <div class="bg-gradient-to-r from-cyan/10 to-purple/10 rounded-xl p-4 mb-6">
            <p class="text-sm text-gray-500">Total Poin</p>
            <p class="text-3xl font-extrabold text-gray-800 dark:text-white">{{ number_format($totalPoin) }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr class="text-left text-gray-500">
                        <th class="py-2 px-3">Tanggal</th>
                        <th class="py-2 px-3">Keterangan</th>
                        <th class="py-2 px-3">Kategori</th>
                        <th class="py-2 px-3 text-right">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $h)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-3">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-3">{{ $h->description ?? '-' }}</td>
                            <td class="py-2 px-3">{{ $h->category ?? '-' }}</td>
                            <td class="py-2 px-3 text-right font-semibold {{ $h->type === 'plus' ? 'text-green-500' : 'text-red-500' }}">
                                {{ $h->type === 'plus' ? '+' : '-' }}{{ $h->points }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada riwayat poin.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $histories->links() }}
        </div>
    </div>
</div>
@endsection
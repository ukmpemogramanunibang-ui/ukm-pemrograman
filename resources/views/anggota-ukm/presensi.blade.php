@extends('layouts.role.anggota')
@section('title', 'Presensi')
@section('breadcrumb', 'Presensi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">📋 Riwayat Presensi</h2>

        @php
            // Asumsi ada model Presensi dengan relasi ke user
            $presensis = \App\Models\Presensi::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        @endphp

        @if($presensis->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr class="text-left text-gray-500">
                            <th class="py-2 px-3">Tanggal</th>
                            <th class="py-2 px-3">Event</th>
                            <th class="py-2 px-3">Status</th>
                            <th class="py-2 px-3">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensis as $p)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 px-3">{{ $p->created_at->format('d/m/Y') }}</td>
                                <td class="py-2 px-3">{{ $p->event->name ?? '-' }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Hadir</span>
                                </td>
                                <td class="py-2 px-3">{{ $p->created_at->format('H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $presensis->links() }}</div>
        @else
            <p class="text-sm text-gray-400">Belum ada data presensi.</p>
        @endif
    </div>
</div>
@endsection
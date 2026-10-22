@extends('layouts.role.anggota')
@section('title', 'Sertifikat')
@section('breadcrumb', 'Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">📜 Sertifikat Saya</h2>

        @php
            // Asumsi ada model Sertifikat
            $sertifikats = \App\Models\Sertifikat::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        @endphp

        @if($sertifikats->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($sertifikats as $s)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 flex items-center gap-4 hover:shadow-lg transition">
                        <i class="fas fa-certificate text-3xl text-yellow-500"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">{{ $s->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $s->issued_at ? $s->issued_at->format('d M Y') : '-' }}</p>
                            <a href="#" class="text-cyan text-xs hover:underline">Lihat Sertifikat</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $sertifikats->links() }}</div>
        @else
            <p class="text-sm text-gray-400">Belum ada sertifikat.</p>
        @endif
    </div>
</div>
@endsection
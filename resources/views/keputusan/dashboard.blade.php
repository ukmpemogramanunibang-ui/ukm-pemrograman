@extends('layouts.app')

@section('title', 'Keputusan Saya')

@section('content')
<section class="min-h-screen bg-gradient-to-br from-midnight via-deepblue to-electric py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white">KEPUTUSAN SAYA</h1>
                <p class="text-white/60 mt-2">Halo, {{ auth()->user()->name }} — kelola roda, polling & riwayat Anda.</p>
            </div>
            <a href="{{ route('keputusan.roda.create') }}"
               class="px-5 py-3 rounded-xl bg-gradient-to-r from-cyan to-electric text-midnight font-bold hover:scale-105 transition">
                + Buat Keputusan
            </a>
        </div>

        {{-- STATS --}}
        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6">
                <div class="text-4xl mb-2">🎡</div>
                <div class="text-3xl font-extrabold text-white">{{ $stats['wheels'] }}</div>
                <div class="text-white/60 text-sm">Roda Dibuat</div>
            </div>
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6">
                <div class="text-4xl mb-2">🗳️</div>
                <div class="text-3xl font-extrabold text-white">{{ $stats['polls'] }}</div>
                <div class="text-white/60 text-sm">Polling Aktif</div>
            </div>
            <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6">
                <div class="text-4xl mb-2">📊</div>
                <div class="text-3xl font-extrabold text-white">{{ $stats['decisions'] }}</div>
                <div class="text-white/60 text-sm">Total Keputusan</div>
            </div>
        </div>

        {{-- LIST --}}
        <h2 class="text-white text-xl font-bold mb-4">Keputusan Terbaru</h2>
        <div class="grid md:grid-cols-2 gap-4">
            @forelse($wheels as $w)
                <a href="{{ route('keputusan.roda.show', $w->public_token) }}"
                   class="block bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-5 hover:border-cyan-400/50 transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-white font-bold">{{ $w->title }}</h3>
                            <p class="text-white/50 text-xs mt-1">
                                {{ $w->entries_count }} peserta • {{ strtoupper($w->status) }} • {{ $w->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="text-cyan-300 text-sm font-bold">Buka →</span>
                    </div>
                </a>
            @empty
                <div class="text-white/50">Belum ada keputusan. Mulai buat sekarang.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
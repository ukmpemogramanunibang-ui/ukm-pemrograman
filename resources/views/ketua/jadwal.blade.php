@extends('layouts.roles.ketua')
@section('title', 'Jadwal - Ketua')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-clock text-neon-cyan"></i> Jadwal Kegiatan
    </h2>
    <p class="text-muted mt-2">Jadwal rutin dan event mendatang.</p>
    <div class="mt-4 space-y-3">
        <div class="p-4 bg-white/5 rounded-lg border border-custom flex justify-between">
            <div>
                <h4 class="text-primary font-semibold">Rapat Koordinasi</h4>
                <p class="text-sm text-muted">Setiap Senin, 09.00 - 11.00</p>
            </div>
            <span class="text-xs text-emerald">Berulang</span>
        </div>
        <div class="p-4 bg-white/5 rounded-lg border border-custom flex justify-between">
            <div>
                <h4 class="text-primary font-semibold">Workshop Laravel</h4>
                <p class="text-sm text-muted">Sabtu, 15 Agustus 2026, 13.00 - 17.00</p>
            </div>
            <span class="text-xs text-warning">Mendatang</span>
        </div>
    </div>
</div>
@endsection
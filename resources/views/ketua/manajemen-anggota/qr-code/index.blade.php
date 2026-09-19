@extends('layouts.roles.ketua')

@section('title', 'QR Code Anggota')

@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-qrcode text-neon-cyan"></i> QR Code Anggota
    </h2>
    <p class="text-muted mt-2">Kelola QR Code anggota UKM Pemrograman.</p>
    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('ketua.manajemen-anggota.qr.generate') }}" class="glass-card p-4 rounded-xl border-custom hover:border-primary transition text-center">
            <i class="fas fa-plus-circle text-3xl text-primary"></i>
            <p class="text-primary font-semibold mt-2">Generate QR Code</p>
        </a>
        <a href="#" class="glass-card p-4 rounded-xl border-custom hover:border-primary transition text-center">
            <i class="fas fa-list text-3xl text-soft-gray"></i>
            <p class="text-primary font-semibold mt-2">Daftar QR Code</p>
        </a>
        <a href="#" class="glass-card p-4 rounded-xl border-custom hover:border-primary transition text-center">
            <i class="fas fa-history text-3xl text-warning"></i>
            <p class="text-primary font-semibold mt-2">Riwayat Generate</p>
        </a>
    </div>
</div>
@endsection
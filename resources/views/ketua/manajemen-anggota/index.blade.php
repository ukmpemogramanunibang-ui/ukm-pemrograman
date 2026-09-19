@extends('layouts.roles.ketua')

@section('title', 'Manajemen Anggota')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
            <i class="fas fa-users-cog text-electric-blue"></i> Manajemen Anggota
        </h2>
        <span class="text-sm text-muted">Total Anggota: <span class="font-semibold text-primary">8</span></span>
    </div>

    {{-- Grid Kategori --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Data Anggota --}}
        <a href="{{ route('ketua.manajemen-anggota.data.semua') }}" class="glass-card p-5 rounded-xl border-custom hover:border-primary transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xl">
                    <i class="fas fa-address-book"></i>
                </div>
                <div>
                    <h3 class="text-primary font-semibold group-hover:text-electric-blue transition">📄 Data Anggota</h3>
                    <p class="text-xs text-muted">Kelola semua data anggota</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted group-hover:text-primary transition"></i>
            </div>
        </a>

        {{-- QR Code --}}
        <a href="{{ route('ketua.manajemen-anggota.qr.index') }}" class="glass-card p-5 rounded-xl border-custom hover:border-primary transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-neon-cyan/20 flex items-center justify-center text-neon-cyan text-xl">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <h3 class="text-primary font-semibold group-hover:text-neon-cyan transition">🔳 QR Code Anggota</h3>
                    <p class="text-xs text-muted">Generate & kelola QR</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted group-hover:text-primary transition"></i>
            </div>
        </a>

        {{-- Testing --}}
        <a href="{{ route('ketua.manajemen-anggota.testing.logika') }}" class="glass-card p-5 rounded-xl border-custom hover:border-primary transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-royal-purple/20 flex items-center justify-center text-royal-purple text-xl">
                    <i class="fas fa-flask"></i>
                </div>
                <div>
                    <h3 class="text-primary font-semibold group-hover:text-royal-purple transition">📊 Testing</h3>
                    <p class="text-xs text-muted">Tes logika, pemrograman, AI, dll</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted group-hover:text-primary transition"></i>
            </div>
        </a>

        {{-- Leaderboard --}}
        <a href="{{ route('ketua.manajemen-anggota.leaderboard.poin') }}" class="glass-card p-5 rounded-xl border-custom hover:border-primary transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-warning/20 flex items-center justify-center text-warning text-xl">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <h3 class="text-primary font-semibold group-hover:text-warning transition">🏆 Leaderboard</h3>
                    <p class="text-xs text-muted">Peringkat & reward</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted group-hover:text-primary transition"></i>
            </div>
        </a>

        {{-- Contact --}}
        <a href="{{ route('ketua.manajemen-anggota.contact.whatsapp') }}" class="glass-card p-5 rounded-xl border-custom hover:border-primary transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-success/20 flex items-center justify-center text-success text-xl">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                    <h3 class="text-primary font-semibold group-hover:text-success transition">💬 Contact Anggota</h3>
                    <p class="text-xs text-muted">WhatsApp, Email, Broadcast</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted group-hover:text-primary transition"></i>
            </div>
        </a>

        {{-- Pelanggaran --}}
        <a href="{{ route('ketua.manajemen-anggota.pelanggaran.ringan') }}" class="glass-card p-5 rounded-xl border-custom hover:border-primary transition group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-danger/20 flex items-center justify-center text-danger text-xl">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h3 class="text-primary font-semibold group-hover:text-danger transition">⚠️ Pelanggaran</h3>
                    <p class="text-xs text-muted">Peringatan & sanksi</p>
                </div>
                <i class="fas fa-chevron-right ml-auto text-muted group-hover:text-primary transition"></i>
            </div>
        </a>
    </div>

    {{-- Statistik Cepat --}}
    <div class="glass-card p-5 rounded-xl border-custom mt-4">
        <h4 class="text-primary font-semibold mb-3">📊 Statistik Anggota</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-primary">8</p>
                <p class="text-xs text-muted">Total</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-emerald">6</p>
                <p class="text-xs text-muted">Aktif</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-danger">1</p>
                <p class="text-xs text-muted">Tidak Aktif</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-royal-purple">1</p>
                <p class="text-xs text-muted">Alumni</p>
            </div>
        </div>
    </div>
</div>
@endsection
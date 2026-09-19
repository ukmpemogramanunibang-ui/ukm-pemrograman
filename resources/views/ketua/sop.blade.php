@extends('layouts.roles.ketua')
@section('title', 'SOP - Ketua')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-clipboard-list text-electric-blue"></i> Standar Operasional Prosedur (SOP)
    </h2>
    <p class="text-muted mt-2">Daftar SOP yang berlaku di UKM Pemrograman UNICODE.</p>
    <div class="mt-4 space-y-3">
        <div class="p-4 bg-white/5 rounded-lg border border-custom">
            <h4 class="text-primary font-semibold">SOP Pendaftaran Anggota</h4>
            <p class="text-sm text-muted">Prosedur pendaftaran anggota baru secara online.</p>
        </div>
        <div class="p-4 bg-white/5 rounded-lg border border-custom">
            <h4 class="text-primary font-semibold">SOP Pengajuan Proyek</h4>
            <p class="text-sm text-muted">Langkah-langkah pengajuan proyek internal.</p>
        </div>
    </div>
</div>
@endsection
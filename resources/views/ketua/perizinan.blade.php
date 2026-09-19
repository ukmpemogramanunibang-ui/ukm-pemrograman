@extends('layouts.roles.ketua')
@section('title', 'Perizinan - Ketua')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-file-signature text-royal-purple"></i> Perizinan
    </h2>
    <p class="text-muted mt-2">Kelola perizinan kegiatan dan penggunaan fasilitas.</p>
    <div class="mt-4 space-y-3">
        <div class="p-4 bg-white/5 rounded-lg border border-custom">
            <h4 class="text-primary font-semibold">Izin Penggunaan Ruang</h4>
            <p class="text-sm text-muted">Formulir peminjaman ruang untuk kegiatan.</p>
        </div>
        <div class="p-4 bg-white/5 rounded-lg border border-custom">
            <h4 class="text-primary font-semibold">Izin Kegiatan Luar</h4>
            <p class="text-sm text-muted">Prosedur perizinan kegiatan di luar kampus.</p>
        </div>
    </div>
</div>
@endsection
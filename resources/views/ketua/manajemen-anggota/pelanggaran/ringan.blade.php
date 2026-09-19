@extends('layouts.roles.ketua')
@section('title', 'Peringatan Ringan')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-warning flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i> Peringatan Ringan
    </h2>
    <p class="text-muted mt-1">Daftar pelanggaran ringan yang tercatat.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Anggota</th><th class="py-2 px-3">Pelanggaran</th><th class="py-2 px-3">Tanggal</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr><td class="py-2 px-3">Pengunjung</td><td class="py-2 px-3">Terlambat 3x</td><td class="py-2 px-3">01 Aug 2026</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
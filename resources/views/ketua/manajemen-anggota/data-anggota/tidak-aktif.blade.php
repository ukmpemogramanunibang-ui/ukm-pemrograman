@extends('layouts.roles.ketua')
@section('title', 'Anggota Tidak Aktif')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-danger flex items-center gap-2">
        <i class="fas fa-user-slash"></i> Anggota Tidak Aktif
    </h2>
    <p class="text-muted mt-1">Anggota yang sudah tidak aktif.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Nama</th><th class="py-2 px-3">NIM</th><th class="py-2 px-3">Alasan</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr><td class="py-2 px-3">Anggota Umum</td><td class="py-2 px-3">20220103</td><td class="py-2 px-3">Mengundurkan diri</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
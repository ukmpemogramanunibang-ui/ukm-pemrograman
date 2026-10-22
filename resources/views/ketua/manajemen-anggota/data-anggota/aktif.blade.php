@extends('layouts.roles.ketua')
@section('title', 'Anggota Aktif')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-emerald flex items-center gap-2">
        <i class="fas fa-user-check"></i> Anggota Aktif
    </h2>
    <p class="text-muted mt-1">Anggota yang masih aktif dan terdaftar.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Nama</th><th class="py-2 px-3">NIM</th><th class="py-2 px-3">Jabatan</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr><td class="py-2 px-3">Bima</td><td class="py-2 px-3">20220101</td><td class="py-2 px-3">Ketua</td></tr>
                <tr><td class="py-2 px-3">Nisa</td><td class="py-2 px-3">20220102</td><td class="py-2 px-3">Wakil Ketua</td></tr>
                <tr><td class="py-2 px-3">Pengunjung</td><td class="py-2 px-3">20220104</td><td class="py-2 px-3">Anggota</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
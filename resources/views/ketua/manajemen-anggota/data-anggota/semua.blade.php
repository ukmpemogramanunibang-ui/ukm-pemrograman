@extends('layouts.roles.ketua')
@section('title', 'Semua Anggota')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-users text-electric-blue"></i> Semua Anggota
    </h2>
    <p class="text-muted mt-1">Daftar lengkap seluruh anggota UKM Pemrograman.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr>
                    <th class="py-2 px-3">Nama</th>
                    <th class="py-2 px-3">NIM</th>
                    <th class="py-2 px-3">Jabatan</th>
                    <th class="py-2 px-3">Status</th>
                </tr>
            </thead>
            <tbody class="text-primary">
                <tr class="border-b border-custom">
                    <td class="py-2 px-3">Bima</td>
                    <td class="py-2 px-3">20220101</td>
                    <td class="py-2 px-3">Ketua</td>
                    <td class="py-2 px-3 text-emerald">Aktif</td>
                </tr>
                <tr class="border-b border-custom">
                    <td class="py-2 px-3">Nisa</td>
                    <td class="py-2 px-3">20220102</td>
                    <td class="py-2 px-3">Wakil Ketua</td>
                    <td class="py-2 px-3 text-emerald">Aktif</td>
                </tr>
                <tr class="border-b border-custom">
                    <td class="py-2 px-3">Anggota Umum</td>
                    <td class="py-2 px-3">20220103</td>
                    <td class="py-2 px-3">Anggota</td>
                    <td class="py-2 px-3 text-warning">Tidak Aktif</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
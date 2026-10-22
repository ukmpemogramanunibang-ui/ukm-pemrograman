@extends('layouts.roles.ketua')
@section('title', 'Anggota Baru')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-neon-cyan flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Anggota Baru
    </h2>
    <p class="text-muted mt-1">Anggota yang baru bergabung dalam 30 hari terakhir.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Nama</th><th class="py-2 px-3">NIM</th><th class="py-2 px-3">Tanggal Gabung</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr><td class="py-2 px-3">Nisa</td><td class="py-2 px-3">20220102</td><td class="py-2 px-3">30 Jul 2026</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
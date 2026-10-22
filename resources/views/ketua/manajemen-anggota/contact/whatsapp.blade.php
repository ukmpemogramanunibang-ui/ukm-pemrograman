@extends('layouts.roles.ketua')
@section('title', 'WhatsApp Anggota')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-success flex items-center gap-2">
        <i class="fab fa-whatsapp"></i> WhatsApp Anggota
    </h2>
    <p class="text-muted mt-1">Daftar nomor WhatsApp anggota.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Nama</th><th class="py-2 px-3">Nomor</th><th class="py-2 px-3">Divisi</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr><td class="py-2 px-3">Bima</td><td class="py-2 px-3">0812-3456-7890</td><td class="py-2 px-3">Inti</td></tr>
                <tr><td class="py-2 px-3">Nisa</td><td class="py-2 px-3">0812-3456-7891</td><td class="py-2 px-3">Inti</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
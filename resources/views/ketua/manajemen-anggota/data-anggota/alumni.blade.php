@extends('layouts.roles.ketua')
@section('title', 'Alumni')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-royal-purple flex items-center gap-2">
        <i class="fas fa-graduation-cap"></i> Alumni
    </h2>
    <p class="text-muted mt-1">Mantan anggota yang telah lulus.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Nama</th><th class="py-2 px-3">NIM</th><th class="py-2 px-3">Tahun Lulus</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr><td class="py-2 px-3">Alumni 1</td><td class="py-2 px-3">20190101</td><td class="py-2 px-3">2025</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
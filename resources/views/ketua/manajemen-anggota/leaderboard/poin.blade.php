@extends('layouts.roles.ketua')
@section('title', 'Leaderboard Poin')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-warning flex items-center gap-2">
        <i class="fas fa-trophy"></i> Leaderboard Poin
    </h2>
    <p class="text-muted mt-1">Peringkat anggota berdasarkan akumulasi poin.</p>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Rank</th><th class="py-2 px-3">Nama</th><th class="py-2 px-3">Poin</th></tr>
            </thead>
            <tbody class="text-primary">
                <tr class="border-b border-custom"><td class="py-2 px-3">🏆 1</td><td class="py-2 px-3">Bima</td><td class="py-2 px-3">250</td></tr>
                <tr class="border-b border-custom"><td class="py-2 px-3">2</td><td class="py-2 px-3">Nisa</td><td class="py-2 px-3">200</td></tr>
                <tr><td class="py-2 px-3">3</td><td class="py-2 px-3">Pengunjung</td><td class="py-2 px-3">150</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
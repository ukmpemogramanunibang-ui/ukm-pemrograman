@extends('layouts.roles.ketua')
@section('title', 'Tes Logika')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-brain text-electric-blue"></i> Tes Logika
    </h2>
    <p class="text-muted mt-1">Soal-soal tes logika untuk anggota.</p>
    <div class="mt-4 space-y-3">
        <div class="p-3 bg-white/5 rounded-lg border border-custom">
            <p class="text-primary font-medium">Soal 1: Jika 2 + 2 = 5, maka ...?</p>
            <p class="text-sm text-muted">Jawaban: Salah, karena 2+2=4</p>
        </div>
        <div class="p-3 bg-white/5 rounded-lg border border-custom">
            <p class="text-primary font-medium">Soal 2: Semua manusia adalah makhluk hidup. Budi adalah manusia. Jadi?</p>
            <p class="text-sm text-muted">Jawaban: Budi adalah makhluk hidup.</p>
        </div>
    </div>
</div>
@endsection
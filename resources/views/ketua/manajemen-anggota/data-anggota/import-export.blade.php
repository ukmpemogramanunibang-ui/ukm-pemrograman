@extends('layouts.roles.ketua')
@section('title', 'Import / Export Data')
@section('content')
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
        <i class="fas fa-file-import"></i> Import / Export Data
    </h2>
    <p class="text-muted mt-1">Impor atau ekspor data anggota dalam format CSV/Excel.</p>
    <div class="mt-4 flex flex-wrap gap-4">
        <button class="px-5 py-2 bg-primary/20 text-primary rounded-lg hover:bg-primary/30 transition">📥 Import CSV</button>
        <button class="px-5 py-2 bg-success/20 text-success rounded-lg hover:bg-success/30 transition">📤 Export CSV</button>
        <button class="px-5 py-2 bg-royal-purple/20 text-royal-purple rounded-lg hover:bg-royal-purple/30 transition">📤 Export Excel</button>
    </div>
</div>
@endsection
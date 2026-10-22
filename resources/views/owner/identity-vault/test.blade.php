@extends('layouts.roles.owner')

@section('title', 'Test Recovery')
@section('page-title', 'Simulasi Recovery Identitas')

@section('content')
<div class="container">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h3 class="text-xl font-bold mb-4">Hasil Simulasi Recovery</h3>
        <p>Proses recovery telah dijalankan terhadap seluruh identitas di vault.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">
            <div class="bg-green-50 p-4 rounded border border-green-200">
                <p class="text-sm text-gray-600">Berhasil</p>
                <p class="text-2xl font-bold text-green-700">{{ collect($results)->filter(fn($r)=>$r==='success')->count() }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded border border-red-200">
                <p class="text-sm text-gray-600">Gagal</p>
                <p class="text-2xl font-bold text-red-700">{{ collect($results)->filter(fn($r)=>$r!=='success')->count() }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded border border-blue-200">
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-2xl font-bold text-blue-700">{{ count($results) }}</p>
            </div>
        </div>

        <div class="overflow-auto max-h-96 bg-gray-100 dark:bg-gray-700 p-4 rounded">
            <pre class="text-xs">{{ json_encode($results, JSON_PRETTY_PRINT) }}</pre>
        </div>

        <a href="{{ route('owner.identity-vault.index') }}" class="mt-4 inline-block bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700">← Kembali</a>
    </div>
</div>
@endsection
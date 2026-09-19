@extends('layouts.roles.owner')

@section('title', 'Detail Kampanye - UNICODE')

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.campaigns.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-eye text-purple-500"></i> Detail Kampanye
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $campaign->title }}</p>
        </div>
        <span class="ml-auto px-3 py-1 text-xs rounded-full 
            {{ $campaign->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
               ($campaign->status == 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' : 
               ($campaign->status == 'closed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 
               'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400')) }}">
            {{ ucfirst($campaign->status) }}
        </span>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">

        {{-- Informasi --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Judul</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $campaign->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kategori</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ str_replace('_', ' ', ucfirst($campaign->category)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nominal</p>
                <p class="text-lg font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($campaign->amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Wajib</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $campaign->is_required ? 'Ya' : 'Tidak' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Target</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">
                    @if($campaign->target_type == 'all')
                        Semua Anggota
                    @elseif($campaign->target_type == 'generation')
                        Generasi Tertentu
                    @else
                        Individu Tertentu
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ ucfirst($campaign->status) }}</p>
            </div>
        </div>

        {{-- Tanggal --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Mulai</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $campaign->start_date ? $campaign->start_date->format('d M Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Berakhir</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $campaign->end_date ? $campaign->end_date->format('d M Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Jatuh Tempo</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $campaign->due_date ? $campaign->due_date->format('d M Y') : '-' }}</p>
            </div>
        </div>

        {{-- Deskripsi --}}
        @if($campaign->description)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
            <p class="text-gray-800 dark:text-white">{{ $campaign->description }}</p>
        </div>
        @endif

        {{-- Aksi --}}
        <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('owner.financial.campaigns.edit', $campaign) }}" class="btn-primary">
                <i class="fas fa-edit"></i> Edit Kampanye
            </a>
            <a href="{{ route('owner.financial.campaigns.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-primary {
        background: linear-gradient(145deg, #2563eb, #1d4ed8);
        color: white;
        padding: 12px 32px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.25s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(37,99,235,0.25);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37,99,235,0.35);
    }
    .btn-secondary {
        background: #e5e7eb;
        color: #1f2937;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .dark .btn-secondary { background: #374151; color: #f3f4f6; }
    .btn-secondary:hover { background: #d1d5db; }
    .dark .btn-secondary:hover { background: #4b5563; }
</style>
@endpush
@endsection
@extends('layouts.roles.owner')

@section('title', 'Edit Kampanye - UNICODE')

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.campaigns.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-edit text-blue-500"></i> Edit Kampanye
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui data kampanye</p>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-times-circle text-red-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-red-700 dark:text-red-400">Gagal menyimpan!</h4>
                    <ul class="mt-1 space-y-1 text-sm text-red-600 dark:text-red-300 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form action="{{ route('owner.financial.campaigns.update', $campaign) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="form-label">Judul Kampanye <span class="required">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $campaign->title) }}" class="form-control @error('title') error @enderror" placeholder="Contoh: Pembuatan ID Card 2026" required>
                    @error('title') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label class="form-label">Kategori <span class="required">*</span></label>
                    <select name="category" class="form-control @error('category') error @enderror" required>
                        <option value="">Pilih Kategori</option>
                        <option value="id_card" {{ old('category', $campaign->category) == 'id_card' ? 'selected' : '' }}>ID Card</option>
                        <option value="event" {{ old('category', $campaign->category) == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="reunion" {{ old('category', $campaign->category) == 'reunion' ? 'selected' : '' }}>Reuni</option>
                        <option value="merchandise" {{ old('category', $campaign->category) == 'merchandise' ? 'selected' : '' }}>Merchandise</option>
                        <option value="registration" {{ old('category', $campaign->category) == 'registration' ? 'selected' : '' }}>Pendaftaran</option>
                        <option value="other" {{ old('category', $campaign->category) == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Amount --}}
                <div>
                    <label class="form-label">Nominal (Rp) <span class="required">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $campaign->amount) }}" class="form-control @error('amount') error @enderror" placeholder="20000" min="0" required>
                    @error('amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Is Required --}}
                <div class="flex items-center gap-3 mt-2">
                    <input type="checkbox" name="is_required" value="1" id="is_required" {{ old('is_required', $campaign->is_required) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-purple-500 focus:ring-purple-500">
                    <label for="is_required" class="text-sm font-medium text-gray-700 dark:text-gray-300">Wajib diikuti</label>
                </div>

                {{-- Target Type --}}
                <div>
                    <label class="form-label">Target <span class="required">*</span></label>
                    <select name="target_type" class="form-control @error('target_type') error @enderror" required>
                        <option value="all" {{ old('target_type', $campaign->target_type) == 'all' ? 'selected' : '' }}>Semua Anggota</option>
                        <option value="generation" {{ old('target_type', $campaign->target_type) == 'generation' ? 'selected' : '' }}>Generasi Tertentu</option>
                        <option value="specific" {{ old('target_type', $campaign->target_type) == 'specific' ? 'selected' : '' }}>Individu Tertentu</option>
                    </select>
                    @error('target_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Dates --}}
                <div>
                    <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" class="form-control @error('start_date') error @enderror" required>
                    @error('start_date') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}" class="form-control @error('end_date') error @enderror">
                    @error('end_date') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Kosongkan jika tidak ada batas akhir</div>
                </div>

                <div>
                    <label class="form-label">Tanggal Jatuh Tempo <span class="required">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date', $campaign->due_date->format('Y-m-d')) }}" class="form-control @error('due_date') error @enderror" required>
                    @error('due_date') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control @error('status') error @enderror">
                        <option value="draft" {{ old('status', $campaign->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $campaign->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="closed" {{ old('status', $campaign->status) == 'closed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ old('status', $campaign->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control @error('description') error @enderror" placeholder="Deskripsi kampanye">{{ old('description', $campaign->description) }}</textarea>
                    @error('description') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

            </div>

            {{-- Submit --}}
            <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Update Kampanye
                </button>
                <a href="{{ route('owner.financial.campaigns.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 5px;
    }
    .dark .form-label { color: #d1d5db; }
    .form-label .required { color: #ef4444; margin-left: 2px; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.95rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .dark .form-control {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        outline: none;
    }
    .dark .form-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96,165,250,0.25);
    }
    .form-control.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.15);
    }
    .form-error {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .form-hint {
        color: #6b7280;
        font-size: 0.75rem;
        margin-top: 4px;
    }
    .dark .form-hint { color: #9ca3af; }
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
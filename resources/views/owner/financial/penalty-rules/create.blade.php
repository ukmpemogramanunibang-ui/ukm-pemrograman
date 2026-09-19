@extends('layouts.roles.owner')

@section('title', 'Tambah Aturan Denda - UNICODE')

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.penalty-rules.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-gavel text-red-500"></i> Tambah Aturan Denda
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat aturan denda untuk keterlambatan pembayaran</p>
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
        <form action="{{ route('owner.financial.penalty-rules.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nama --}}
                <div>
                    <label class="form-label">Nama Aturan <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') error @enderror" placeholder="Contoh: Denda Level 1" required>
                    @error('name') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Level --}}
                <div>
                    <label class="form-label">Level <span class="required">*</span></label>
                    <input type="number" name="level" value="{{ old('level') }}" class="form-control @error('level') error @enderror" placeholder="1" min="1" required>
                    @error('level') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Level 1 = denda pertama, Level 2 = denda kedua, dst.</div>
                </div>

                {{-- Start Day --}}
                <div>
                    <label class="form-label">Hari Mulai <span class="required">*</span></label>
                    <input type="number" name="start_day" value="{{ old('start_day', 0) }}" class="form-control @error('start_day') error @enderror" placeholder="0" min="0" required>
                    @error('start_day') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Hari keterlambatan mulai dikenakan (0 = hari jatuh tempo)</div>
                </div>

                {{-- End Day --}}
                <div>
                    <label class="form-label">Hari Berakhir</label>
                    <input type="number" name="end_day" value="{{ old('end_day') }}" class="form-control @error('end_day') error @enderror" placeholder="10" min="0">
                    @error('end_day') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Kosongkan jika tidak ada batas akhir</div>
                </div>

                {{-- Fixed Amount --}}
                <div>
                    <label class="form-label">Denda Tetap (Rp)</label>
                    <input type="number" name="fixed_amount" value="{{ old('fixed_amount') }}" class="form-control @error('fixed_amount') error @enderror" placeholder="3000" min="0">
                    @error('fixed_amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Daily Amount --}}
                <div>
                    <label class="form-label">Denda Harian (Rp)</label>
                    <input type="number" name="daily_amount" value="{{ old('daily_amount') }}" class="form-control @error('daily_amount') error @enderror" placeholder="1000" min="0">
                    @error('daily_amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Max Amount --}}
                <div>
                    <label class="form-label">Maksimal Denda (Rp)</label>
                    <input type="number" name="max_amount" value="{{ old('max_amount') }}" class="form-control @error('max_amount') error @enderror" placeholder="7000" min="0">
                    @error('max_amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Batas maksimal denda untuk level ini</div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-control @error('is_active') error @enderror">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('is_active') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan Aturan
                </button>
                <a href="{{ route('owner.financial.penalty-rules.index') }}" class="btn-secondary">
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
    .dark .form-label {
        color: #d1d5db;
    }
    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }
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
    .form-error i {
        font-size: 12px;
    }
    .form-hint {
        color: #6b7280;
        font-size: 0.75rem;
        margin-top: 4px;
    }
    .dark .form-hint {
        color: #9ca3af;
    }
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
    .dark .btn-secondary {
        background: #374151;
        color: #f3f4f6;
    }
    .btn-secondary:hover {
        background: #d1d5db;
    }
    .dark .btn-secondary:hover {
        background: #4b5563;
    }
</style>
@endpush
@endsection
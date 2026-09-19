@extends('layouts.roles.owner')

@section('title', 'Edit Skema Biaya - UNICODE')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-section {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 24px 20px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: border-color 0.2s;
    }
    .dark .form-section {
        background: #1f2937;
        border-color: #374151;
    }
    .form-section:hover {
        border-color: #93c5fd;
    }
    .dark .form-section:hover {
        border-color: #60a5fa;
    }
    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dark .form-section-title {
        color: #9ca3af;
        border-bottom-color: #374151;
    }
    .form-section-title i {
        color: #3b82f6;
        font-size: 16px;
        width: 24px;
        text-align: center;
    }
    .dark .form-section-title i {
        color: #60a5fa;
    }
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
    .form-control:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
        opacity: 0.7;
    }
    .dark .form-control:disabled {
        background-color: #1f2937;
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
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }
    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        accent-color: #3b82f6;
        cursor: pointer;
    }
    .dark .checkbox-group input[type="checkbox"] {
        border-color: #4b5563;
    }
    .checkbox-group label {
        font-size: 0.9rem;
        color: #374151;
        cursor: pointer;
    }
    .dark .checkbox-group label {
        color: #d1d5db;
    }
    .select2-container--default .select2-selection--multiple {
        border-radius: 10px !important;
        border-color: #d1d5db !important;
        padding: 4px 8px !important;
        min-height: 44px;
    }
    .dark .select2-container--default .select2-selection--multiple {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
    }
    .dark .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4b5563 !important;
        color: #f3f4f6 !important;
        border-color: #6b7280 !important;
    }
    .dark .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #f3f4f6 !important;
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
        background: linear-gradient(145deg, #1d4ed8, #1e40af);
    }
    .btn-primary:active {
        transform: translateY(0);
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
    .badge-version {
        background: #e0e7ff;
        color: #3730a3;
        padding: 2px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .dark .badge-version {
        background: #312e81;
        color: #c7d2fe;
    }
    @media (max-width: 640px) {
        .form-section { padding: 16px; }
        .btn-primary, .btn-secondary { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('owner.financial.fee-plans.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                    <i class="fas fa-edit text-blue-500"></i> Edit Skema Biaya
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui data skema pembayaran</p>
            </div>
        </div>
        <span class="badge-version"><i class="fas fa-code-branch"></i> Versi {{ $feePlan->version ?? 1 }}</span>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-5 mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-times-circle text-red-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-red-700 dark:text-red-400">Gagal memperbarui skema!</h4>
                    <p class="text-sm text-red-600 dark:text-red-300">Terdapat {{ $errors->count() }} kesalahan validasi.</p>
                    <ul class="mt-2 space-y-1 text-sm text-red-600 dark:text-red-300 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('owner.financial.fee-plans.update', $feePlan) }}" method="POST" id="feePlanForm" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="period_type" value="{{ old('period_type', $feePlan->period_type ?? 'monthly') }}">

        <!-- SECTION 1: IDENTITAS SKEMA -->
        <div class="form-section">
            <div class="form-section-title"><i class="fas fa-tag"></i> 1. Identitas Skema</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nama Skema <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $feePlan->name) }}" class="form-control @error('name') error @enderror" placeholder="Contoh: Kas Bulanan Gen 1" required>
                    @error('name') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Generasi <span class="required">*</span></label>
                    <select name="generation_id" class="form-control @error('generation_id') error @enderror" required>
                        <option value="">Pilih Generasi</option>
                        @foreach($generations as $gen)
                            <option value="{{ $gen->id }}" {{ old('generation_id', $feePlan->generation_id) == $gen->id ? 'selected' : '' }}>{{ $gen->name }} ({{ $gen->code }})</option>
                        @endforeach
                    </select>
                    @error('generation_id') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Jenis Biaya <span class="required">*</span></label>
                    <select name="fee_type" class="form-control @error('fee_type') error @enderror">
                        <option value="monthly_cash" {{ old('fee_type', $feePlan->fee_type) == 'monthly_cash' ? 'selected' : '' }}>Kas Bulanan</option>
                        <option value="registration" {{ old('fee_type', $feePlan->fee_type) == 'registration' ? 'selected' : '' }}>Uang Pangkal</option>
                        <option value="special_contribution" {{ old('fee_type', $feePlan->fee_type) == 'special_contribution' ? 'selected' : '' }}>Iuran Khusus</option>
                        <option value="penalty" {{ old('fee_type', $feePlan->fee_type) == 'penalty' ? 'selected' : '' }}>Denda</option>
                        <option value="activity" {{ old('fee_type', $feePlan->fee_type) == 'activity' ? 'selected' : '' }}>Kegiatan</option>
                        <option value="other" {{ old('fee_type', $feePlan->fee_type) == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('fee_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Kode Skema</label>
                    <input type="text" value="{{ $feePlan->code }}" class="form-control" disabled>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="2" class="form-control @error('description') error @enderror" placeholder="Deskripsikan tujuan skema ini">{{ old('description', $feePlan->description) }}</textarea>
                    @error('description') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 2: TARGET PEMBAYAR -->
        <div class="form-section">
            <div class="form-section-title"><i class="fas fa-bullseye"></i> 2. Target Pembayar</div>
            <div>
                <label class="form-label">Target <span class="required">*</span></label>
                <select name="target_type" id="target_type" class="form-control @error('target_type') error @enderror">
                    <option value="all_members" {{ old('target_type', $feePlan->target_type) == 'all_members' ? 'selected' : '' }}>Semua Anggota</option>
                    <option value="general_members" {{ old('target_type', $feePlan->target_type) == 'general_members' ? 'selected' : '' }}>Anggota Umum</option>
                    <option value="officials" {{ old('target_type', $feePlan->target_type) == 'officials' ? 'selected' : '' }}>Pengurus</option>
                    <option value="core_officials" {{ old('target_type', $feePlan->target_type) == 'core_officials' ? 'selected' : '' }}>Pengurus Inti</option>
                    <option value="group" {{ old('target_type', $feePlan->target_type) == 'group' ? 'selected' : '' }}>Kelompok Tertentu</option>
                    <option value="individual" {{ old('target_type', $feePlan->target_type) == 'individual' ? 'selected' : '' }}>Individu Tertentu</option>
                </select>
                @error('target_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <!-- Target Group -->
            <div id="target_group_container" class="mt-3 {{ old('target_type', $feePlan->target_type) == 'group' ? '' : 'hidden' }}">
                <label class="form-label">Pilih Kelompok <span class="required">*</span></label>
                <select name="target_data[group_ids][]" id="target_group_select" class="form-control" multiple>
                    @foreach($groups ?? [] as $group)
                        <option value="{{ $group->id }}" {{ isset($feePlan->target_data['group_ids']) && in_array($group->id, $feePlan->target_data['group_ids']) ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('target_data.group_ids') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <!-- Target Individual -->
            <div id="target_individual_container" class="mt-3 {{ old('target_type', $feePlan->target_type) == 'individual' ? '' : 'hidden' }}">
                <label class="form-label">Pilih Anggota <span class="required">*</span></label>
                <select name="target_data[user_ids][]" id="target_individual_select" class="form-control" multiple>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ isset($feePlan->target_data['user_ids']) && in_array($user->id, $feePlan->target_data['user_ids']) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->nim ?? 'N/A' }})</option>
                    @endforeach
                </select>
                @error('target_data.user_ids') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>
        </div>

        <!-- SECTION 3: NOMINAL -->
        <div class="form-section">
            <div class="form-section-title"><i class="fas fa-dollar-sign"></i> 3. Nominal</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nominal Dasar (Rp) <span class="required">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $feePlan->amount) }}" class="form-control @error('amount') error @enderror" placeholder="5000" min="0" required>
                    @error('amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Jenis Nominal</label>
                    <select name="nominal_type" id="nominal_type" class="form-control @error('nominal_type') error @enderror">
                        <option value="fixed" {{ old('nominal_type', $feePlan->nominal_type) == 'fixed' ? 'selected' : '' }}>Tetap (semua sama)</option>
                        <option value="by_position" {{ old('nominal_type', $feePlan->nominal_type) == 'by_position' ? 'selected' : '' }}>Berbeda berdasarkan Jabatan</option>
                        <option value="by_member" {{ old('nominal_type', $feePlan->nominal_type) == 'by_member' ? 'selected' : '' }}>Berbeda per Anggota</option>
                    </select>
                    @error('nominal_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>

            <!-- by position -->
            <div id="nominal_position_container" class="mt-4 {{ old('nominal_type', $feePlan->nominal_type) == 'by_position' ? '' : 'hidden' }}">
                <label class="form-label">Nominal per Jabatan <span class="required">*</span></label>
                <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-xl p-3 space-y-2 bg-gray-50 dark:bg-gray-800/50">
                    @php $positions = \App\Models\Jabatan::all(); @endphp
                    @foreach($positions as $pos)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-36">{{ $pos->name }}</span>
                        <input type="number" name="nominal_data[{{ $pos->id }}]" value="{{ old('nominal_data.'.$pos->id, $feePlan->nominal_data[$pos->id] ?? '') }}" class="form-control w-40" placeholder="Nominal">
                    </div>
                    @endforeach
                </div>
                @error('nominal_data') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <!-- by member -->
            <div id="nominal_member_container" class="mt-4 {{ old('nominal_type', $feePlan->nominal_type) == 'by_member' ? '' : 'hidden' }}">
                <label class="form-label">Nominal per Anggota <span class="required">*</span></label>
                <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-xl p-3 space-y-1 bg-gray-50 dark:bg-gray-800/50">
                    @foreach($users ?? [] as $user)
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 dark:text-gray-300 w-48 truncate">{{ $user->name }}</span>
                        <input type="number" name="nominal_data[{{ $user->id }}]" value="{{ old('nominal_data.'.$user->id, $feePlan->nominal_data[$user->id] ?? '') }}" class="form-control w-40" placeholder="Nominal">
                    </div>
                    @endforeach
                </div>
                @error('nominal_data') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>
        </div>

        <!-- SECTION 4-8: Sama seperti create.blade.php, cukup copy dari create dan ubah old() dengan old('field', $feePlan->field) -->

        <!-- ... (lanjutkan semua section dengan nilai old dari $feePlan) ... -->

        <!-- SUBMIT -->
        <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update Skema
            </button>
            <a href="{{ route('owner.financial.fee-plans.index') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Target Type
    const targetType = document.getElementById('target_type');
    const groupContainer = document.getElementById('target_group_container');
    const individualContainer = document.getElementById('target_individual_container');

    targetType.addEventListener('change', function() {
        groupContainer.classList.add('hidden');
        individualContainer.classList.add('hidden');
        if (this.value === 'group') groupContainer.classList.remove('hidden');
        else if (this.value === 'individual') individualContainer.classList.remove('hidden');
    });

    // Toggle Nominal Type
    const nominalType = document.getElementById('nominal_type');
    const posContainer = document.getElementById('nominal_position_container');
    const memContainer = document.getElementById('nominal_member_container');

    nominalType.addEventListener('change', function() {
        posContainer.classList.add('hidden');
        memContainer.classList.add('hidden');
        if (this.value === 'by_position') posContainer.classList.remove('hidden');
        else if (this.value === 'by_member') memContainer.classList.remove('hidden');
    });

    // Toggle Penalty
    const penaltyEnabled = document.getElementById('penalty_enabled');
    const penaltyFields = document.getElementById('penalty_fields');
    if (penaltyEnabled) {
        penaltyEnabled.addEventListener('change', function() {
            penaltyFields.classList.toggle('hidden', !this.checked);
        });
    }

    // Toggle Partial
    const allowPartial = document.getElementById('allow_partial');
    const partialFields = document.getElementById('partial_fields');
    if (allowPartial) {
        allowPartial.addEventListener('change', function() {
            partialFields.classList.toggle('hidden', !this.checked);
        });
    }

    // Select2
    if (document.getElementById('target_group_select')) {
        $('#target_group_select').select2({ placeholder: 'Pilih kelompok...', allowClear: true, theme: 'default' });
    }
    if (document.getElementById('target_individual_select')) {
        $('#target_individual_select').select2({ placeholder: 'Pilih anggota...', allowClear: true, theme: 'default' });
    }
});
</script>
@endpush
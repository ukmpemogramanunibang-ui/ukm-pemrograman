@extends('layouts.roles.owner')

@section('title', 'Tambah Skema Biaya - UNICODE')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<style>
    /* ===== GLOBAL ===== */
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

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 420px;
        width: 100%;
    }
    .toast {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideInRight 0.4s ease;
        background: #ffffff;
        border-left: 5px solid;
    }
    .dark .toast {
        background: #1f2937;
        color: #f3f4f6;
    }
    .toast-success { border-left-color: #22c55e; }
    .toast-error { border-left-color: #ef4444; }
    .toast-info { border-left-color: #3b82f6; }
    .toast i { font-size: 22px; margin-top: 2px; }
    .toast .toast-close { cursor: pointer; margin-left: auto; font-size: 20px; opacity: 0.6; }
    .toast .toast-close:hover { opacity: 1; }

    @keyframes slideInRight {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
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
                    <i class="fas fa-file-invoice-dollar text-blue-500"></i> Tambah Skema Biaya
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Isi semua data dengan teliti untuk membuat skema pembayaran baru</p>
            </div>
        </div>
        <span class="badge-version"><i class="fas fa-code-branch"></i> Versi 1.0</span>
    </div>

    {{-- ERROR SUMMARY (jika ada) --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-5 mb-6 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-times-circle text-red-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-red-700 dark:text-red-400">Gagal menyimpan skema!</h4>
                    <p class="text-sm text-red-600 dark:text-red-300">Terdapat {{ $errors->count() }} kesalahan validasi. Perbaiki field yang bermasalah.</p>
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
    <form action="{{ route('owner.financial.fee-plans.store') }}" method="POST" id="feePlanForm" novalidate>
        @csrf
        <input type="hidden" name="period_type" value="{{ old('period_type', 'monthly') }}">

        <!-- ======================================== -->
        <!-- SECTION 1: IDENTITAS SKEMA -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-tag"></i> 1. Identitas Skema
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nama Skema <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') error @enderror" placeholder="Contoh: Kas Bulanan Gen 1" required>
                    @error('name') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Generasi <span class="required">*</span></label>
                    <select name="generation_id" class="form-control @error('generation_id') error @enderror" required>
    <option value="">Pilih Generasi</option>
    @foreach($generations as $gen)
        <option value="{{ $gen->id }}" {{ old('generation_id') == $gen->id ? 'selected' : '' }}>
            {{ $gen->name }} ({{ $gen->code }})
        </option>
    @endforeach
</select>
                    @error('generation_id') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Jenis Biaya <span class="required">*</span></label>
                    <select name="fee_type" class="form-control @error('fee_type') error @enderror">
                        <option value="monthly_cash" {{ old('fee_type') == 'monthly_cash' ? 'selected' : '' }}>Kas Bulanan</option>
                        <option value="registration" {{ old('fee_type') == 'registration' ? 'selected' : '' }}>Uang Pangkal</option>
                        <option value="special_contribution" {{ old('fee_type') == 'special_contribution' ? 'selected' : '' }}>Iuran Khusus</option>
                        <option value="penalty" {{ old('fee_type') == 'penalty' ? 'selected' : '' }}>Denda</option>
                        <option value="activity" {{ old('fee_type') == 'activity' ? 'selected' : '' }}>Kegiatan</option>
                        <option value="other" {{ old('fee_type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('fee_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Kode Skema</label>
                    <input type="text" value="(Otomatis)" class="form-control" disabled>
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Akan digenerate otomatis saat disimpan</div>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="2" class="form-control @error('description') error @enderror" placeholder="Deskripsikan tujuan skema ini">{{ old('description') }}</textarea>
                    @error('description') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 2: TARGET PEMBAYAR -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-bullseye"></i> 2. Target Pembayar
            </div>
            <div>
                <label class="form-label">Target <span class="required">*</span></label>
                <select name="target_type" id="target_type" class="form-control @error('target_type') error @enderror">
                    <option value="all_members" {{ old('target_type') == 'all_members' ? 'selected' : '' }}>Semua Anggota</option>
                    <option value="general_members" {{ old('target_type') == 'general_members' ? 'selected' : '' }}>Anggota Umum</option>
                    <option value="officials" {{ old('target_type') == 'officials' ? 'selected' : '' }}>Pengurus</option>
                    <option value="core_officials" {{ old('target_type') == 'core_officials' ? 'selected' : '' }}>Pengurus Inti</option>
                    <option value="group" {{ old('target_type') == 'group' ? 'selected' : '' }}>Kelompok Tertentu</option>
                    <option value="individual" {{ old('target_type') == 'individual' ? 'selected' : '' }}>Individu Tertentu</option>
                </select>
                @error('target_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <!-- Group -->
            <div id="target_group_container" class="mt-3 {{ old('target_type') == 'group' ? '' : 'hidden' }}">
                <label class="form-label">Pilih Kelompok <span class="required">*</span></label>
                <select name="target_data[group_ids][]" id="target_group_select" class="form-control" multiple>
                    @foreach($groups ?? [] as $group)
                        <option value="{{ $group->id }}" {{ in_array($group->id, old('target_data.group_ids', [])) ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('target_data.group_ids') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <!-- Individual -->
            <div id="target_individual_container" class="mt-3 {{ old('target_type') == 'individual' ? '' : 'hidden' }}">
                <label class="form-label">Pilih Anggota <span class="required">*</span></label>
                <select name="target_data[user_ids][]" id="target_individual_select" class="form-control" multiple>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, old('target_data.user_ids', [])) ? 'selected' : '' }}>{{ $user->name }} ({{ $user->nim ?? 'N/A' }})</option>
                    @endforeach
                </select>
                @error('target_data.user_ids') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 3: NOMINAL -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-dollar-sign"></i> 3. Nominal
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nominal Dasar (Rp) <span class="required">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') error @enderror" placeholder="5000" min="0" required>
                    @error('amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Jenis Nominal</label>
                    <select name="nominal_type" id="nominal_type" class="form-control @error('nominal_type') error @enderror">
                        <option value="fixed" {{ old('nominal_type') == 'fixed' ? 'selected' : '' }}>Tetap (semua sama)</option>
                        <option value="by_position" {{ old('nominal_type') == 'by_position' ? 'selected' : '' }}>Berbeda berdasarkan Jabatan</option>
                        <option value="by_member" {{ old('nominal_type') == 'by_member' ? 'selected' : '' }}>Berbeda per Anggota</option>
                    </select>
                    @error('nominal_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>

            <!-- by position -->
            <div id="nominal_position_container" class="mt-4 {{ old('nominal_type') == 'by_position' ? '' : 'hidden' }}">
                <label class="form-label">Nominal per Jabatan <span class="required">*</span></label>
                <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-xl p-3 space-y-2 bg-gray-50 dark:bg-gray-800/50">
                    @php $positions = \App\Models\Jabatan::all(); @endphp
                    @foreach($positions as $pos)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-36">{{ $pos->name }}</span>
                        <input type="number" name="nominal_data[{{ $pos->id }}]" value="{{ old('nominal_data.'.$pos->id) }}" class="form-control w-40" placeholder="Nominal">
                    </div>
                    @endforeach
                </div>
                @error('nominal_data') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>

            <!-- by member -->
            <div id="nominal_member_container" class="mt-4 {{ old('nominal_type') == 'by_member' ? '' : 'hidden' }}">
                <label class="form-label">Nominal per Anggota <span class="required">*</span></label>
                <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-xl p-3 space-y-1 bg-gray-50 dark:bg-gray-800/50">
                    @foreach($users ?? [] as $user)
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-700 dark:text-gray-300 w-48 truncate">{{ $user->name }}</span>
                        <input type="number" name="nominal_data[{{ $user->id }}]" value="{{ old('nominal_data.'.$user->id) }}" class="form-control w-40" placeholder="Nominal">
                    </div>
                    @endforeach
                </div>
                @error('nominal_data') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 4: PERIODE TAGIHAN & JATUH TEMPO -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-calendar-alt"></i> 4. Periode Tagihan & Jatuh Tempo
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Frekuensi <span class="required">*</span></label>
                    <select name="frequency" class="form-control @error('frequency') error @enderror">
                        <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="biweekly" {{ old('frequency') == 'biweekly' ? 'selected' : '' }}>2 Minggu</option>
                        <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="one_time" {{ old('frequency') == 'one_time' ? 'selected' : '' }}>Sekali Bayar</option>
                    </select>
                    @error('frequency') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Tanggal Jatuh Tempo <span class="required">*</span></label>
                    <input type="number" name="due_day" value="{{ old('due_day', 28) }}" class="form-control @error('due_day') error @enderror" min="1" max="31" required>
                    @error('due_day') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Masa Toleransi (hari)</label>
                    <input type="number" name="grace_period" value="{{ old('grace_period', 3) }}" class="form-control @error('grace_period') error @enderror" min="0" max="60">
                    @error('grace_period') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint"><i class="fas fa-info-circle"></i> Jumlah hari setelah jatuh tempo sebelum dianggap terlambat</div>
                </div>
                <div>
                    <label class="form-label">Kebijakan Hari Nonaktif</label>
                    <select name="non_active_day_policy" class="form-control @error('non_active_day_policy') error @enderror">
                        <option value="next_active" {{ old('non_active_day_policy', 'next_active') == 'next_active' ? 'selected' : '' }}>Geser ke hari aktif berikutnya</option>
                        <option value="prev_active" {{ old('non_active_day_policy') == 'prev_active' ? 'selected' : '' }}>Geser ke hari aktif sebelumnya</option>
                        <option value="fixed" {{ old('non_active_day_policy') == 'fixed' ? 'selected' : '' }}>Tetap pada tanggal</option>
                    </select>
                    @error('non_active_day_policy') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="form-label">Sumber Kalender Nonaktif</label>
                <div class="flex flex-wrap gap-5 mt-1">
                    <label class="checkbox-group">
                        <input type="checkbox" name="calendar_sources[national_holidays]" value="1" {{ old('calendar_sources.national_holidays', true) ? 'checked' : '' }}>
                        <span>Hari Libur Nasional</span>
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" name="calendar_sources[weekends]" value="1" {{ old('calendar_sources.weekends', true) ? 'checked' : '' }}>
                        <span>Sabtu &amp; Minggu</span>
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" name="calendar_sources[academic_calendar]" value="1" {{ old('calendar_sources.academic_calendar') ? 'checked' : '' }}>
                        <span>Kalender Akademik (UTS/UAS)</span>
                    </label>
                    <label class="checkbox-group">
                        <input type="checkbox" name="calendar_sources[ukm_activities]" value="1" {{ old('calendar_sources.ukm_activities') ? 'checked' : '' }}>
                        <span>Kegiatan UKM</span>
                    </label>
                </div>
                @error('calendar_sources') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 5: ATURAN DENDA & SANKSI -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-exclamation-triangle"></i> 5. Aturan Denda &amp; Sanksi
            </div>

            <div class="checkbox-group mb-4">
                <input type="checkbox" name="penalty_enabled" value="1" id="penalty_enabled" {{ old('penalty_enabled') ? 'checked' : '' }}>
                <label for="penalty_enabled" class="font-semibold">Aktifkan Denda Keterlambatan</label>
            </div>

            <div id="penalty_fields" class="{{ old('penalty_enabled') ? '' : 'hidden' }} space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Jenis Denda</label>
                        <select name="penalty_type" class="form-control @error('penalty_type') error @enderror">
                            <option value="fixed" {{ old('penalty_type') == 'fixed' ? 'selected' : '' }}>Nominal Tetap</option>
                            <option value="percentage" {{ old('penalty_type') == 'percentage' ? 'selected' : '' }}>Persentase</option>
                        </select>
                        @error('penalty_type') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Besaran</label>
                        <input type="number" name="penalty_amount" value="{{ old('penalty_amount') }}" class="form-control @error('penalty_amount') error @enderror" placeholder="1000 atau 5" step="0.01">
                        @error('penalty_amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        <div class="form-hint"><i class="fas fa-info-circle"></i> Jika persentase, masukkan angka (misal 5 = 5%)</div>
                    </div>
                </div>

                <div>
                    <label class="form-label">Eskalasi Sanksi per Tingkat Keterlambatan</label>
                    <div class="space-y-3 border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-800/30">
                        @php
                            $escalations = old('penalty_escalation', $defaultEscalation ?? [
                                ['level' => 1, 'late_count' => 1, 'actions' => ['warning']],
                                ['level' => 2, 'late_count' => 2, 'actions' => ['warning', 'penalty'], 'penalty_amount' => 1000],
                                ['level' => 3, 'late_count' => 3, 'actions' => ['warning', 'penalty', 'task'], 'penalty_amount' => 2500],
                                ['level' => 4, 'late_count' => 4, 'actions' => ['warning', 'review', 'evaluation']],
                            ]);
                        @endphp
                        @foreach($escalations as $index => $esc)
                        <div class="flex flex-wrap items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 w-20">Level {{ $esc['level'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 w-28">Terlambat {{ $esc['late_count'] }}x</span>
                            <select name="penalty_escalation[{{ $index }}][actions][]" multiple class="form-control w-48">
                                <option value="warning" {{ in_array('warning', $esc['actions'] ?? []) ? 'selected' : '' }}>Peringatan</option>
                                <option value="penalty" {{ in_array('penalty', $esc['actions'] ?? []) ? 'selected' : '' }}>Denda</option>
                                <option value="task" {{ in_array('task', $esc['actions'] ?? []) ? 'selected' : '' }}>Tugas</option>
                                <option value="review" {{ in_array('review', $esc['actions'] ?? []) ? 'selected' : '' }}>Review</option>
                                <option value="evaluation" {{ in_array('evaluation', $esc['actions'] ?? []) ? 'selected' : '' }}>Evaluasi</option>
                                <option value="suspension" {{ in_array('suspension', $esc['actions'] ?? []) ? 'selected' : '' }}>Pembekuan</option>
                            </select>
                            @if(isset($esc['penalty_amount']))
                            <input type="number" name="penalty_escalation[{{ $index }}][penalty_amount]" value="{{ $esc['penalty_amount'] }}" class="form-control w-28" placeholder="Denda">
                            @endif
                            <input type="hidden" name="penalty_escalation[{{ $index }}][level]" value="{{ $esc['level'] }}">
                            <input type="hidden" name="penalty_escalation[{{ $index }}][late_count]" value="{{ $esc['late_count'] }}">
                        </div>
                        @endforeach
                    </div>
                    @error('penalty_escalation') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    <div class="form-hint mt-2"><i class="fas fa-info-circle"></i> Pilih tindakan untuk setiap tingkat keterlambatan (tahan Ctrl untuk multi pilih)</div>
                </div>
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 6: CICILAN & PEMBEBASAN -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-hand-holding-usd"></i> 6. Cicilan &amp; Pembebasan
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="allow_partial" value="1" id="allow_partial" {{ old('allow_partial') ? 'checked' : '' }}>
                        <label for="allow_partial" class="font-semibold">Boleh Dicicil</label>
                    </div>
                    <div id="partial_fields" class="{{ old('allow_partial') ? '' : 'hidden' }} space-y-3 ml-7 mt-2">
                        <div>
                            <label class="form-label">Minimal Cicilan (Rp)</label>
                            <input type="number" name="min_partial_amount" value="{{ old('min_partial_amount') }}" class="form-control @error('min_partial_amount') error @enderror" placeholder="5000">
                            @error('min_partial_amount') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Maksimal Cicilan</label>
                            <input type="number" name="max_installments" value="{{ old('max_installments', 3) }}" class="form-control @error('max_installments') error @enderror" min="1" max="12">
                            @error('max_installments') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="allow_exemption" value="1" id="allow_exemption" {{ old('allow_exemption') ? 'checked' : '' }}>
                        <label for="allow_exemption" class="font-semibold">Boleh Dibebaskan</label>
                    </div>
                    <div class="ml-7 mt-2 space-y-2">
                        <div class="checkbox-group">
                            <input type="checkbox" name="exemption_require_approval" value="1" {{ old('exemption_require_approval', true) ? 'checked' : '' }}>
                            <label>Wajib Persetujuan</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" name="exemption_reason_required" value="1" {{ old('exemption_reason_required', true) ? 'checked' : '' }}>
                            <label>Wajib Alasan</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 7: MASA BERLAKU & KEANGGOTAAN -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-clock"></i> 7. Masa Berlaku &amp; Keanggotaan
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Berlaku Mulai <span class="required">*</span></label>
                    <input type="date" name="effective_from" value="{{ old('effective_from', date('Y-m-d')) }}" class="form-control @error('effective_from') error @enderror" required>
                    @error('effective_from') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Berlaku Sampai</label>
                    <input type="date" name="effective_until" value="{{ old('effective_until') }}" class="form-control @error('effective_until') error @enderror">
                    @error('effective_until') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Aturan Keanggotaan</label>
                    <div class="space-y-2 ml-2">
                        <div class="checkbox-group">
                            <input type="checkbox" name="membership_rule[require_active_membership]" value="1" {{ old('membership_rule.require_active_membership', true) ? 'checked' : '' }}>
                            <label>Tagihan hanya untuk anggota aktif</label>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Mulai tagihan untuk anggota baru:</span>
                            <select name="membership_rule[start_billing]" class="form-control w-auto">
                                <option value="current_month" {{ old('membership_rule.start_billing', 'current_month') == 'current_month' ? 'selected' : '' }}>Bulan bergabung</option>
                                <option value="next_month" {{ old('membership_rule.start_billing') == 'next_month' ? 'selected' : '' }}>Bulan berikutnya</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Cut-off date:</span>
                            <input type="number" name="membership_rule[cutoff_day]" value="{{ old('membership_rule.cutoff_day', 15) }}" class="form-control w-24" min="1" max="31">
                            <span class="text-sm text-gray-500 dark:text-gray-400">(Jika masuk setelah tanggal ini, tagihan mulai bulan depan)</span>
                        </div>
                    </div>
                    @error('membership_rule') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SECTION 8: STATUS & CATATAN -->
        <!-- ======================================== -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-check-circle"></i> 8. Status &amp; Catatan
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control @error('status') error @enderror">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Arsip</option>
                        <option value="superseded" {{ old('status') == 'superseded' ? 'selected' : '' }}>Digantikan</option>
                    </select>
                    @error('status') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Versi</label>
                    <input type="text" value="1 (Otomatis)" class="form-control" disabled>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') error @enderror" placeholder="Catatan tambahan untuk administrasi internal">{{ old('notes') }}</textarea>
                    @error('notes') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- ======================================== -->
        <!-- SUBMIT -->
        <!-- ======================================== -->
        <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Simpan Skema
            </button>
            <a href="{{ route('owner.financial.fee-plans.index') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="reset" class="btn-secondary" style="background: #f3f4f6; color: #6b7280;">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Toggle: Target Type ---
    const targetType = document.getElementById('target_type');
    const groupContainer = document.getElementById('target_group_container');
    const individualContainer = document.getElementById('target_individual_container');
    targetType.addEventListener('change', function() {
        groupContainer.classList.add('hidden');
        individualContainer.classList.add('hidden');
        if (this.value === 'group') groupContainer.classList.remove('hidden');
        else if (this.value === 'individual') individualContainer.classList.remove('hidden');
    });

    // --- Toggle: Nominal Type ---
    const nominalType = document.getElementById('nominal_type');
    const posContainer = document.getElementById('nominal_position_container');
    const memContainer = document.getElementById('nominal_member_container');
    nominalType.addEventListener('change', function() {
        posContainer.classList.add('hidden');
        memContainer.classList.add('hidden');
        if (this.value === 'by_position') posContainer.classList.remove('hidden');
        else if (this.value === 'by_member') memContainer.classList.remove('hidden');
    });

    // --- Toggle: Penalty ---
    const penaltyEnabled = document.getElementById('penalty_enabled');
    const penaltyFields = document.getElementById('penalty_fields');
    penaltyEnabled.addEventListener('change', function() {
        penaltyFields.classList.toggle('hidden', !this.checked);
    });

    // --- Toggle: Partial ---
    const allowPartial = document.getElementById('allow_partial');
    const partialFields = document.getElementById('partial_fields');
    allowPartial.addEventListener('change', function() {
        partialFields.classList.toggle('hidden', !this.checked);
    });

    // --- Select2 initialization ---
    if (document.getElementById('target_group_select')) {
        $('#target_group_select').select2({
            placeholder: 'Pilih kelompok...',
            allowClear: true,
            theme: 'default'
        });
    }
    if (document.getElementById('target_individual_select')) {
        $('#target_individual_select').select2({
            placeholder: 'Pilih anggota...',
            allowClear: true,
            theme: 'default'
        });
    }
    document.querySelectorAll('select[name*="[actions][]"]').forEach(function(sel) {
        $(sel).select2({
            placeholder: 'Pilih tindakan...',
            allowClear: true,
            theme: 'default',
            tags: true
        });
    });

    // --- Auto-hide error summary after 6 seconds ---
    const errorSummary = document.querySelector('.bg-red-50');
    if (errorSummary) {
        setTimeout(() => {
            errorSummary.style.transition = 'opacity 0.6s';
            errorSummary.style.opacity = '0';
            setTimeout(() => errorSummary.style.display = 'none', 700);
        }, 8000);
    }

    // --- Confirm before submit (optional) ---
    const form = document.getElementById('feePlanForm');
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        // Re-enable after 3 seconds if validation fails (prevent double submit)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Skema';
        }, 3000);
    });
});
</script>
@endpush
@extends('layouts.roles.owner')

@section('title', 'Tambah Ruangan – Owner')

@push('styles')
<style>
    .form-section {
        transition: all 0.3s ease;
        scroll-margin-top: 80px;
    }
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .input-group-focus:focus-within {
        border-color: #06b6d4;
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.15);
    }
    .step-dot {
        width: 40px;
        height: 4px;
        border-radius: 4px;
        background: #e5e7eb;
        transition: all 0.3s;
    }

    .step-dot.active { background: #3b82f6; }
    .step-dot.done { background: #10b981; }
    .check-icon.valid { color: #10b981; }
    .check-icon.invalid { color: #ef4444; }
    .check-icon.loading { color: #f59e0b; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    
    .file-preview-item {
        position: relative;
        transition: all 0.3s;
    }
    .file-preview-item:hover .remove-file {
        opacity: 1;
    }
    .remove-file {
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .fade-in {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .qr-preview {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-plus-circle text-cyan-500"></i>
                Tambah Ruangan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftarkan ruangan baru dengan sistem cerdas.</p>
        </div>
        <a href="{{ route('owner.rooms.index') }}" class="mt-3 md:mt-0 px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Step Indicator --}}
    <div class="step-indicator flex gap-2 mb-6">
        @for($i = 1; $i <= 7; $i++)
            <div class="step-dot {{ $i == 1 ? 'active' : '' }}" data-step="{{ $i }}"></div>
        @endfor
    </div>

   @if(session('error'))
    <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 px-4 py-3 rounded shadow-sm mb-6">
        {{ session('error') }}
    </div>
@endif

    <form action="{{ route('owner.rooms.store') }}" method="POST" enctype="multipart/form-data" id="roomForm" class="space-y-6">
        @csrf

        {{-- ============================================================ --}}
        {{-- BAGIAN 1: IDENTITAS --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-1">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    Identitas Ruangan
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Ruangan <span class="text-red-500">*</span></label>
                    <div class="input-group-focus mt-1 flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus-within:ring-2 focus-within:ring-cyan-500">
                        <span class="pl-3 text-gray-400"><i class="fas fa-tag"></i></span>
                        <input type="text" name="name" id="roomName" value="{{ old('name') }}" 
                               class="w-full rounded-r-lg border-0 bg-transparent px-4 py-2 text-gray-900 dark:text-white focus:outline-none" 
                               placeholder="Contoh: Ruang Rapat Utama" required>
                    </div>
                    <div id="nameFeedback" class="text-xs mt-1 flex items-center gap-1">
                        <span id="nameIcon" class="check-icon"><i class="fas fa-circle text-gray-300"></i></span>
                        <span id="nameMessage" class="text-gray-400">Periksa ketersediaan nama</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                    <select name="category" id="roomCategory" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        @foreach(['Ruang Rapat', 'Ruang Kelas', 'Laboratorium', 'Studio', 'Aula', 'Sekretariat', 'Ruang Penyimpanan', 'Ruang Kreatif', 'Ruang Serbaguna', 'Ruang Komputer', 'Ruang Podcast', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Kode ruangan akan dibuat otomatis dari kategori, lantai, dan kapasitas.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" rows="2" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-700/30 rounded-lg p-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
                            <i class="fas fa-robot"></i>
                            <strong>Smart Detect:</strong> 
                            <span id="smartCodePreview">Kode akan muncul otomatis</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BAGIAN 2: LOKASI --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-2">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    Lokasi
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gedung <span class="text-red-500">*</span></label>
                    <input type="text" name="building" id="building" value="{{ old('building') }}" 
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lantai <span class="text-red-500">*</span></label>
                    <select name="floor" id="floor" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Pilih Lantai</option>
                        @foreach(['Basement 3', 'Basement 2', 'Basement 1', 'Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4', 'Lantai 5', 'Lantai 6', 'Lantai 7', 'Lantai 8', 'Lantai 9', 'Lantai 10', 'Lantai 11', 'Lantai 12', 'Loteng', 'Aula'] as $floor)
                            <option value="{{ $floor }}" {{ old('floor') == $floor ? 'selected' : '' }}>{{ $floor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Jl. Contoh No. 123, Kampus" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BAGIAN 3: KAPASITAS --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-3">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    Kapasitas
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kapasitas Normal <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity_normal" id="capacityNormal" value="{{ old('capacity_normal') }}" 
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500" required min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kapasitas Maksimum</label>
                    <input type="number" name="capacity_max" id="capacityMax" value="{{ old('capacity_max') }}" 
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500" min="0">
                    <p class="text-xs text-gray-400 mt-1">Jika kosong, sama dengan kapasitas normal.</p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BAGIAN 4: FASILITAS --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-4">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                    Fasilitas (dengan Jumlah)
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $facilityOptions = ['AC', 'WiFi', 'Proyektor', 'Whiteboard', 'Sound System', 'Smart TV', 'Kamera', 'Mikrofon', 'Komputer', 'Printer', 'CCTV', 'Meja', 'Kursi', 'Lemari', 'Papan Tulis', 'Speaker', 'Dispenser', 'Kipas Angin', 'Pemanas', 'Pendingin', 'Rak Buku', 'Panggung', 'Layar', 'Podium', 'Karpet', 'Gorden'];
                    @endphp
                    @foreach($facilityOptions as $fac)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="facilities[{{ $loop->index }}][name]" value="{{ $fac }}" id="fac-{{ $loop->index }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 facility-check" 
                                   {{ old("facilities.{$loop->index}.name") == $fac ? 'checked' : '' }}>
                            <label for="fac-{{ $loop->index }}" class="text-sm text-gray-700 dark:text-gray-300">{{ $fac }}</label>
                            <input type="number" name="facilities[{{ $loop->index }}][quantity]" value="{{ old("facilities.{$loop->index}.quantity", 1) }}" min="1" class="w-16 text-center rounded border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500 facility-qty">
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-3">Centang fasilitas dan masukkan jumlah yang dibutuhkan.</p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BAGIAN 5: OPERASIONAL & JAM (SMART) --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-5">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">5</span>
                    Operasional & Jam
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Awal</label>
                    <select name="status" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        @foreach(['Tersedia', 'Sedang Dibangun', 'Direnovasi', 'Maintenance'] as $status)
                            <option value="{{ $status }}" {{ old('status', 'Tersedia') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi Awal</label>
                    <select name="condition" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        @foreach(['Baik', 'Perlu Perbaikan', 'Rusak', 'Renovasi', 'Bersih'] as $condition)
                            <option value="{{ $condition }}" {{ old('condition', 'Baik') == $condition ? 'selected' : '' }}>{{ $condition }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Jam Operasional --}}
            <div class="px-6 pb-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🕒 Jam Operasional (centang hari aktif)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @php
                        $days = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'];
                        $defaultHours = old('operational_hours', []);
                        $oldDays = old('operational_days', []);
                    @endphp
                    @foreach($days as $key => $label)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <input type="checkbox" name="operational_days[]" value="{{ $key }}" id="day-{{ $key }}"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 day-checkbox"
                                   {{ in_array($key, $oldDays) ? 'checked' : '' }}>
                            <label for="day-{{ $key }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[50px]">{{ $label }}</label>
                            <input type="time" name="operational_hours[{{ $key }}]"
                                   value="{{ $defaultHours[$key] ?? '08:00' }}"
                                   class="day-hour-input w-24 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500"
                                   {{ in_array($key, $oldDays) ? '' : 'disabled' }}>
                            <span class="text-xs text-gray-400">sd</span>
                            <input type="time" name="operational_hours_end[{{ $key }}]"
                                   value="{{ isset($defaultHours[$key]) && str_contains($defaultHours[$key], '-') ? explode('-', $defaultHours[$key])[1] : '20:00' }}"
                                   class="day-hour-end w-24 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500"
                                   {{ in_array($key, $oldDays) ? '' : 'disabled' }}>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex items-center gap-3">
                    <button type="button" id="applyAllHours" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                        <i class="fas fa-copy mr-1"></i> Terapkan ke semua hari
                    </button>
                    <button type="button" id="clearAllHours" class="text-xs text-red-500 hover:underline">
                        <i class="fas fa-trash mr-1"></i> Kosongkan semua
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-2">Centang hari, lalu isi jam mulai dan selesai. Kosongkan semua untuk 24 jam.</p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BAGIAN 6: PENANGGUNG JAWAB --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-6">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">6</span>
                    Penanggung Jawab
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Penanggung Jawab</label>
                    <input type="text" name="responsible_person" value="{{ old('responsible_person') }}" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama penanggung jawab">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
                    <input type="text" name="responsible_phone" value="{{ old('responsible_phone') }}" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- BAGIAN 7: QR & MEDIA --}}
        {{-- ============================================================ --}}
        <div class="form-section bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden fade-in" id="section-7">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">7</span>
                    QR Code & Media
                </h2>
            </div>
            <div class="p-6 space-y-6">
                {{-- QR Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buat QR Code Ruangan?</label>
                    <div class="mt-2 flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="radio" name="has_qr" value="1" id="qrYes" {{ old('has_qr') == '1' ? 'checked' : '' }}> 
                            <i class="fas fa-check-circle text-green-500"></i> Ya
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="radio" name="has_qr" value="0" id="qrNo" {{ old('has_qr') == '0' || old('has_qr') === null ? 'checked' : '' }}> 
                            <i class="fas fa-times-circle text-red-400"></i> Tidak
                        </label>
                    </div>
                    <div id="qrPreviewContainer" class="mt-3 {{ old('has_qr') == '1' ? '' : 'hidden' }}">
                        <div class="qr-preview">
                            <i class="fas fa-qrcode text-6xl text-gray-300"></i>
                            <p class="text-xs text-gray-400 mt-2">QR Code akan dibuat otomatis</p>
                        </div>
                    </div>
                </div>

                {{-- Foto --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Ruangan (max 5 file)</label>
                    <div class="mt-1">
                        <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" id="imageInput"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-blue-300">
                        <div id="imagePreviewContainer" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal 10MB per file. <span class="text-blue-500">(Pilih 5 file sekaligus dengan Ctrl+Click)</span></p>
                </div>

                {{-- Dokumen --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dokumen (max 2 file)</label>
                    <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.xlsx" id="documentInput"
                           class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-blue-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-blue-300">
                    <div id="documentPreviewContainer" class="flex flex-wrap gap-2 mt-2"></div>
                    <p class="text-xs text-gray-400 mt-1">Format: PDF, DOC, DOCX, XLSX. Maksimal 10MB per file.</p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- TOMBOL SUBMIT --}}
        {{-- ============================================================ --}}
        <div class="flex flex-wrap gap-3">
            <button type="submit" id="submitBtn" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan & Aktifkan
            </button>
            <button type="reset" id="resetBtn" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition">
                <i class="fas fa-undo"></i> Reset
            </button>
            <button type="button" id="previewBtn" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition">
                <i class="fas fa-eye"></i> Preview
            </button>
        </div>
    </form>
</div>

{{-- ============================================================ --}}
{{-- JAVASCRIPT LENGKAP --}}
{{-- ============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==============================
    // AUTO GENERATE ROOM CODE
    // ==============================
    function generateRoomCode() {
        const category = document.getElementById('roomCategory').value;
        const floor = document.getElementById('floor').value;
        const capacity = document.getElementById('capacityNormal').value || 0;
        
        if (!category || !floor || !capacity) {
            document.getElementById('smartCodePreview').textContent = 'Lengkapi kategori, lantai, dan kapasitas';
            return;
        }
        
        let catCode = category.substring(0, 2).toUpperCase();
        if (catCode.length < 2) catCode = 'RU';
        
        let floorMatch = floor.match(/\d+/);
        let floorNum = floorMatch ? floorMatch[0] : '0';
        if (floor.toLowerCase().includes('basement')) floorNum = 'B' + floorNum;
        
        let capStr = String(capacity % 100).padStart(2, '0');
        let seq = String(Date.now()).slice(-3);
        
        let code = catCode + '-' + floorNum + '-' + capStr + '-' + seq;
        document.getElementById('smartCodePreview').textContent = 'Kode: ' + code;
        return code;
    }

    ['roomCategory', 'floor', 'capacityNormal'].forEach(id => {
        document.getElementById(id).addEventListener('change', generateRoomCode);
        document.getElementById(id).addEventListener('input', generateRoomCode);
    });

    // ==============================
    // LIVE CHECK NAMA
  // ==============================
// INFORMASI NAMA (HANYA TAMPIL, TIDAK MEMPENGARUHI TOMBOL)
// ==============================
const nameInput = document.getElementById('roomName');
const nameIcon = document.getElementById('nameIcon');
const nameMessage = document.getElementById('nameMessage');
let nameTimeout = null;

nameInput.addEventListener('input', function() {
    clearTimeout(nameTimeout);
    const value = this.value.trim();
    if (value.length < 3) {
        nameIcon.innerHTML = '<i class="fas fa-circle text-gray-300"></i>';
        nameMessage.textContent = 'Minimal 3 karakter';
        nameMessage.className = 'text-gray-400';
        return;
    }

    nameIcon.innerHTML = '<i class="fas fa-spinner fa-spin text-yellow-500"></i>';
    nameMessage.textContent = 'Memeriksa...';
    nameMessage.className = 'text-yellow-500';

    nameTimeout = setTimeout(() => {
        fetch('{{ route("owner.rooms.check") }}?field=name&value=' + encodeURIComponent(value), {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                nameIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                nameMessage.textContent = '⚠️ Nama sudah digunakan (tetap bisa disimpan)';
                nameMessage.className = 'text-red-500';
            } else {
                nameIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                nameMessage.textContent = '✅ Nama tersedia';
                nameMessage.className = 'text-green-500';
            }
        })
        .catch(() => {
            nameIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-red-500"></i>';
            nameMessage.textContent = 'Gagal memeriksa';
            nameMessage.className = 'text-red-500';
        });
    }, 500);
});

    // ==============================
    // QR PREVIEW TOGGLE
    // ==============================
    document.querySelectorAll('input[name="has_qr"]').forEach(el => {
        el.addEventListener('change', function() {
            const container = document.getElementById('qrPreviewContainer');
            if (this.value === '1' && this.checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    });

    // ==============================
    // OPERATIONAL HOURS - CHECKBOX TOGGLE
    // ==============================
    document.querySelectorAll('.day-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const container = this.closest('div');
            const startInput = container.querySelector('.day-hour-input');
            const endInput = container.querySelector('.day-hour-end');
            if (this.checked) {
                startInput.disabled = false;
                endInput.disabled = false;
                if (!startInput.value) startInput.value = '08:00';
                if (!endInput.value) endInput.value = '20:00';
            } else {
                startInput.disabled = true;
                endInput.disabled = true;
                startInput.value = '';
                endInput.value = '';
            }
        });
    });

    // ==============================
    // APPLY ALL HOURS
    // ==============================
    document.getElementById('applyAllHours')?.addEventListener('click', function() {
        const firstChecked = document.querySelector('.day-checkbox:checked');
        if (!firstChecked) {
            alert('Centang minimal satu hari terlebih dahulu!');
            return;
        }
        const container = firstChecked.closest('div');
        const startVal = container.querySelector('.day-hour-input').value;
        const endVal = container.querySelector('.day-hour-end').value;
        
        if (!startVal || !endVal) {
            alert('Isi jam mulai dan selesai pada hari yang dicentang!');
            return;
        }
        
        document.querySelectorAll('.day-checkbox:checked').forEach(cb => {
            const cont = cb.closest('div');
            cont.querySelector('.day-hour-input').value = startVal;
            cont.querySelector('.day-hour-end').value = endVal;
        });
    });

    document.getElementById('clearAllHours')?.addEventListener('click', function() {
        document.querySelectorAll('.day-checkbox:checked').forEach(cb => {
            cb.checked = false;
            cb.dispatchEvent(new Event('change'));
        });
    });

    // ==============================
    // FILE PREVIEW - IMAGES
    // ==============================
    document.getElementById('imageInput')?.addEventListener('change', function(e) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        const files = this.files;
        if (files.length === 0) {
            container.innerHTML = '<span class="text-xs text-gray-400">Belum ada foto dipilih</span>';
            return;
        }
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(ev) {
                const div = document.createElement('div');
                div.className = 'file-preview-item relative w-20 h-20 rounded border border-gray-300 overflow-hidden';
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'w-full h-full object-cover';
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-file absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center hover:bg-red-600 opacity-0 transition-opacity';
                removeBtn.textContent = '×';
                removeBtn.onclick = function() {
                    const dt = new DataTransfer();
                    const input = document.getElementById('imageInput');
                    const filesArray = Array.from(input.files);
                    filesArray.splice(i, 1);
                    filesArray.forEach(f => dt.items.add(f));
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change'));
                };
                div.appendChild(img);
                div.appendChild(removeBtn);
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });

    // ==============================
    // FILE PREVIEW - DOCUMENTS
    // ==============================
    document.getElementById('documentInput')?.addEventListener('change', function(e) {
        const container = document.getElementById('documentPreviewContainer');
        container.innerHTML = '';
        const files = this.files;
        if (files.length === 0) {
            container.innerHTML = '<span class="text-xs text-gray-400">Belum ada dokumen dipilih</span>';
            return;
        }
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full text-sm';
            const icon = document.createElement('i');
            icon.className = 'fas fa-file-pdf text-red-500';
            const span = document.createElement('span');
            span.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            const removeBtn = document.createElement('button');
            removeBtn.className = 'ml-1 text-red-500 hover:text-red-700 text-xs';
            removeBtn.textContent = '×';
            removeBtn.onclick = function() {
                const dt = new DataTransfer();
                const input = document.getElementById('documentInput');
                const filesArray = Array.from(input.files);
                filesArray.splice(i, 1);
                filesArray.forEach(f => dt.items.add(f));
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            };
            div.appendChild(icon);
            div.appendChild(span);
            div.appendChild(removeBtn);
            container.appendChild(div);
        }
    });

    const sections = document.querySelectorAll('.form-section');
    const dots = document.querySelectorAll('.step-dot');
    
    function updateSteps() {
        let activeIndex = 0;
        sections.forEach((section, i) => {
            const rect = section.getBoundingClientRect();
            if (rect.top < window.innerHeight * 0.6) {
                activeIndex = i;
            }
        });
        dots.forEach((dot, i) => {
            dot.classList.remove('active', 'done');
            if (i < activeIndex) dot.classList.add('done');
            else if (i === activeIndex) dot.classList.add('active');
        });
    }
    
    window.addEventListener('scroll', updateSteps);
    updateSteps();

    // ==============================
    // SUBMIT LOADING STATE
    // ==============================
    document.getElementById('roomForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;
    });

    // ==============================
    // PREVIEW DATA
    // ==============================
    document.getElementById('previewBtn').addEventListener('click', function() {
        const checkedDays = [];
        document.querySelectorAll('.day-checkbox:checked').forEach(cb => {
            const label = cb.nextElementSibling.textContent;
            const cont = cb.closest('div');
            const start = cont.querySelector('.day-hour-input').value || '--:--';
            const end = cont.querySelector('.day-hour-end').value || '--:--';
            checkedDays.push(label + ': ' + start + ' - ' + end);
        });
        
        const data = {
            nama: document.getElementById('roomName').value || '-',
            kategori: document.getElementById('roomCategory').value || '-',
            gedung: document.getElementById('building').value || '-',
            lantai: document.getElementById('floor').value || '-',
            kapasitas: document.getElementById('capacityNormal').value || '0',
            kapasitas_max: document.getElementById('capacityMax').value || '0',
            status: document.querySelector('select[name="status"]').value || '-',
            kondisi: document.querySelector('select[name="condition"]').value || '-',
            jam: checkedDays.length ? checkedDays.join('\n') : '24 Jam',
            qr: document.querySelector('input[name="has_qr"]:checked')?.value === '1' ? 'Ya' : 'Tidak',
        };
        
        alert('📋 Preview Data Ruangan:\n\n' +
              'Nama: ' + data.nama + '\n' +
              'Kategori: ' + data.kategori + '\n' +
              'Gedung: ' + data.gedung + '\n' +
              'Lantai: ' + data.lantai + '\n' +
              'Kapasitas: ' + data.kapasitas + ' orang' +
              (data.kapasitas_max > 0 ? ' (max ' + data.kapasitas_max + ')' : '') + '\n' +
              'Status: ' + data.status + '\n' +
              'Kondisi: ' + data.kondisi + '\n' +
              'Jam Operasional:\n' + data.jam + '\n' +
              'QR Code: ' + data.qr);
    });

    // ==============================
    // RESET BUTTON
    // ==============================
    document.getElementById('resetBtn').addEventListener('click', function(e) {
        if (!confirm('Reset semua input?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
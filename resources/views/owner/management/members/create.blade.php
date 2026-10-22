@extends('layouts.roles.owner')

@section('title', 'Tambah Anggota – Owner UNICODE')
@section('page-title', 'Tambah Anggota Baru')

@push('styles')
<style>
    .form-card {
        transition: all 0.3s ease;
        border-radius: 1rem;
    }
    .form-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
    }
    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    .dark .form-label {
        color: #9ca3af;
    }
    .form-control {
        width: 100%;
        padding: 0.6rem 0.8rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: white;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-size: 0.9rem;
    }
    .dark .form-control {
        background: #1f2937;
        border-color: #374151;
        color: #e5e7eb;
    }
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .dark .form-control:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
    }
    .btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: white;
        padding: 0.7rem 2rem;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99,102,241,0.3);
    }
    .btn-primary:disabled {
        background: #9ca3af !important;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }
    .btn-primary:disabled:hover {
        transform: none;
        box-shadow: none;
    }
    .btn-primary:enabled {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }
    .btn-primary:enabled:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(34,197,94,0.3);
    }
    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
        padding: 0.7rem 2rem;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    .dark .btn-secondary {
        background: #374151;
        color: #e5e7eb;
    }
    .btn-secondary:hover {
        background: #d1d5db;
        transform: translateY(-2px);
    }
    .dark .btn-secondary:hover {
        background: #4b5563;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .dark .alert-success {
        background: #064e3b;
        color: #d1fae5;
    }
    .error-text {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .is-valid {
        border-color: #22c55e !important;
    }
    .is-invalid {
        border-color: #ef4444 !important;
    }
    .help-text {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 0.2rem;
    }
    .dark .help-text {
        color: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="form-card bg-white dark:bg-gray-800 p-6 md:p-8 shadow-sm border border-gray-200/60 dark:border-gray-700/60">
        <div class="flex items-center gap-2 mb-6">
            <i class="fas fa-user-plus text-indigo-500 text-xl"></i>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Formulir Pendaftaran Anggota</h2>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('owner.management.members.store') }}" enctype="multipart/form-data" id="member-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- NIM --}}
                <div>
                    <label for="nim" class="form-label block">NIM</label>
                    <input type="text" name="nim" id="nim"
                           class="form-control @error('nim') is-invalid @enderror"
                           value="{{ old('nim') }}"
                           placeholder="Masukkan NIM (opsional)"
                           maxlength="20">
                    @error('nim')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Hanya angka, maksimal 20 digit</p>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="form-label block">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Nama lengkap" required>
                    @error('name')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Hanya huruf, spasi, dan titik. Minimal 3 huruf</p>
                </div>

                {{-- Telepon --}}
                <div>
                    <label for="phone" class="form-label block">Nomor Telepon Aktif</label>
                    <input type="text" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}"
                           placeholder="08xx-xxxx-xxxx" maxlength="20">
                    @error('phone')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Hanya angka dan tanda hubung (-)</p>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="form-label block">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="email@domain.com" required>
                    @error('email')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Wajib mengandung @ dan . (contoh: nama@domain.com)</p>
                </div>

                {{-- Jenis Kelamin --}}
                <div>
                    <label for="gender" class="form-label block">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Laptop --}}
                <div>
                    <label for="has_laptop" class="form-label block">Memiliki Laptop?</label>
                    <select name="has_laptop" id="has_laptop" class="form-control @error('has_laptop') is-invalid @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('has_laptop') == '1' ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ old('has_laptop') == '0' ? 'selected' : '' }}>Tidak</option>
                    </select>
                    @error('has_laptop')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div class="md:col-span-2">
                    <label for="bio" class="form-label block">Bio / Deskripsi</label>
                    <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Program Studi (input teks) --}}
                <div>
                    <label for="major" class="form-label block">Program Studi</label>
                    <input type="text" name="major" id="major"
                           class="form-control @error('major') is-invalid @enderror"
                           value="{{ old('major') }}"
                           placeholder="Contoh: Teknik Informatika">
                    @error('major')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Tulis nama program studi Anda</p>
                </div>

                {{-- Universitas (input teks) --}}
                <div>
                    <label for="university" class="form-label block">Universitas</label>
                    <input type="text" name="university" id="university"
                           class="form-control @error('university') is-invalid @enderror"
                           value="{{ old('university') }}"
                           placeholder="Contoh: Universitas Indonesia">
                    @error('university')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Tulis nama universitas Anda</p>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="form-label block">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter" required minlength="8">
                    @error('password')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                    <p class="help-text">* Minimal 8 karakter</p>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="form-label block">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" placeholder="Ulangi password" required minlength="8">
                </div>

                {{-- Upload Avatar --}}
                <div class="md:col-span-2">
                    <label for="avatar" class="form-label block">Foto Profil / Avatar</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*"
                           class="form-control @error('avatar') is-invalid @enderror"
                           onchange="previewImage(event)">
                    <p class="help-text">Format: JPG, PNG, JPEG, WEBP, GIF. Maks 2MB. (Opsional)</p>
                    <div id="avatar-preview" class="mt-2 hidden">
                        <img id="avatar-preview-img" src="#" alt="Preview Avatar" class="h-20 w-20 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600">
                    </div>
                    @error('avatar')
                        <p class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Tombol --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('owner.management.members.index') }}" class="btn-secondary text-center">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" id="submit-btn" class="btn-primary" disabled>
                    <i class="fas fa-save mr-2"></i> Buat Anggota
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // === PREVIEW AVATAR ===
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('avatar-preview');
            const img = document.getElementById('avatar-preview-img');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
        window.previewImage = previewImage;

        // === ELEMEN ===
        const form = document.getElementById('member-form');
        const submitBtn = document.getElementById('submit-btn');
        const requiredFields = form.querySelectorAll('[required]');
        const allInputs = form.querySelectorAll('input, select, textarea');

        // === VALIDASI FIELD (real-time) ===
        function validateField(field) {
            let isValid = true;
            let errorMsg = '';

            if (field.id === 'nim' && field.value && !/^[0-9]+$/.test(field.value)) {
                isValid = false;
                errorMsg = 'NIM hanya boleh berisi angka.';
            }
            if (field.id === 'name' && field.value) {
                if (!/^[a-zA-Z\s\.]+$/.test(field.value)) {
                    isValid = false;
                    errorMsg = 'Nama hanya boleh huruf, spasi, dan titik.';
                } else if (field.value.length < 3) {
                    isValid = false;
                    errorMsg = 'Nama minimal 3 huruf.';
                }
            }
            if (field.id === 'phone' && field.value && !/^[0-9\-]+$/.test(field.value)) {
                isValid = false;
                errorMsg = 'Telepon hanya boleh angka dan tanda hubung (-).';
            }
            if (field.id === 'email' && field.value) {
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                    isValid = false;
                    errorMsg = 'Email harus mengandung @ dan domain valid.';
                }
            }
            if (field.id === 'password' && field.value && field.value.length < 8) {
                isValid = false;
                errorMsg = 'Password minimal 8 karakter.';
            }
            if (field.id === 'password_confirmation') {
                const pass = document.getElementById('password');
                if (field.value !== pass.value) {
                    isValid = false;
                    errorMsg = 'Konfirmasi password tidak cocok.';
                }
            }

            // Tampilkan error inline
            let errorEl = field.parentElement.querySelector('.error-text');
            if (!isValid && field.value) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                if (!errorEl) {
                    const p = document.createElement('p');
                    p.className = 'error-text';
                    p.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errorMsg;
                    field.parentElement.appendChild(p);
                } else {
                    errorEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errorMsg;
                }
            } else if (isValid && field.value) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                if (errorEl) errorEl.remove();
            } else {
                field.classList.remove('is-invalid', 'is-valid');
                if (errorEl) errorEl.remove();
            }

            // Untuk required field, cek kosong
            if (field.hasAttribute('required') && !field.value.trim()) {
                field.classList.remove('is-valid');
                if (!isValid) field.classList.add('is-invalid');
            }

            return isValid;
        }

        // === CEK UNIK NIM & NAMA (AJAX) ===
        function checkUnique(field, value, callback) {
            if (!value || value.length < 2) {
                callback(true, null);
                return;
            }
            fetch('{{ route("owner.management.members.check") }}?field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                callback(data.valid, data.message);
            })
            .catch(() => {
                callback(true, null);
            });
        }

        // === EVENT BLUR UNTUK NIM & NAMA ===
        const nimInput = document.getElementById('nim');
        nimInput.addEventListener('blur', function() {
            const self = this;
            checkUnique('nim', this.value, function(valid, message) {
                let errorEl = self.parentElement.querySelector('.error-text');
                if (!valid && message) {
                    self.classList.add('is-invalid');
                    self.classList.remove('is-valid');
                    if (!errorEl) {
                        const p = document.createElement('p');
                        p.className = 'error-text';
                        p.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                        self.parentElement.appendChild(p);
                    } else {
                        errorEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                    }
                } else {
                    self.classList.remove('is-invalid');
                    if (self.value.trim()) self.classList.add('is-valid');
                    if (errorEl) errorEl.remove();
                }
                checkSubmit();
            });
        });

        const nameInput = document.getElementById('name');
        nameInput.addEventListener('blur', function() {
            const self = this;
            checkUnique('name', this.value, function(valid, message) {
                let errorEl = self.parentElement.querySelector('.error-text');
                if (!valid && message) {
                    self.classList.add('is-invalid');
                    self.classList.remove('is-valid');
                    if (!errorEl) {
                        const p = document.createElement('p');
                        p.className = 'error-text';
                        p.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                        self.parentElement.appendChild(p);
                    } else {
                        errorEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                    }
                } else {
                    self.classList.remove('is-invalid');
                    if (self.value.trim()) self.classList.add('is-valid');
                    if (errorEl) errorEl.remove();
                }
                checkSubmit();
            });
        });

        // === CEK SUBMIT ===
        function checkSubmit() {
            let allFilled = true;
            requiredFields.forEach(field => {
                if (field.type === 'file') return;
                if (!field.value.trim()) {
                    allFilled = false;
                }
            });
            // Cek apakah ada error inline (selain dari validasi unik yang sudah dihandle)
            const hasInlineError = document.querySelectorAll('.error-text').length > 0;
            // Juga cek apakah ada field yang invalid
            const hasInvalid = document.querySelectorAll('.is-invalid').length > 0;
            submitBtn.disabled = !allFilled || hasInlineError || hasInvalid;
        }

        // === EVENT LISTENER SEMUA INPUT ===
        allInputs.forEach(input => {
            input.addEventListener('input', function() {
                validateField(this);
                checkSubmit();
            });
            input.addEventListener('change', function() {
                validateField(this);
                checkSubmit();
            });
        });

        // === SUBMIT LOADING ===
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // === INISIALISASI ===
        // Validasi awal untuk semua field
        allInputs.forEach(field => validateField(field));
        checkSubmit();
    });
</script>
@endpush
@endsection
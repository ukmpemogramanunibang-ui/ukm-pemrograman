@extends('layouts.roles.owner')

@section('title', 'Tambah Ketua / Pengurus')
@section('page-title', 'Tambah Ketua / Pengurus')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold">Form Tambah Ketua / Pengurus</h3>
        <a href="{{ route('owner.officials.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('owner.officials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- ========== DATA PRIBADI ========== -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-user text-cyan-600 mr-2"></i> Data Pribadi
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-cyan-500" required>
                </div>
                <!-- NIM -->
                <div>
                    <label for="nim" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIM <span class="text-red-500">*</span></label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-cyan-500" required>
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-cyan-500" required>
                </div>
                <!-- No. Telepon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-cyan-500" required>
                </div>
                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <option value="">-- Pilih --</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                </div>
                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="2"
                              class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">{{ old('address') }}</textarea>
                </div>
                <!-- Bio -->
                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio / Deskripsi Singkat</label>
                    <textarea id="bio" name="bio" rows="2"
                              class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">{{ old('bio') }}</textarea>
                </div>
                <!-- Avatar -->
                <div class="md:col-span-2">
                    <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Profil (Opsional)</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*"
                           class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                </div>
            </div>
        </div>

        <!-- ========== DATA AKADEMIK ========== -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-graduation-cap text-indigo-600 mr-2"></i> Data Akademik
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="universitas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Universitas <span class="text-red-500">*</span></label>
                    <input type="text" id="universitas" name="universitas" value="{{ old('universitas') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-cyan-500"
                           placeholder="Contoh: Universitas Bhakti Asih" required>
                </div>
                <div>
                    <label for="program_studi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Program Studi <span class="text-red-500">*</span></label>
                    <input type="text" id="program_studi" name="program_studi" value="{{ old('program_studi') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 focus:ring-cyan-500"
                           placeholder="Contoh: Manajemen, Teknik Informatika, dll." required>
                </div>
            </div>
        </div>

        <!-- ========== DATA ORGANISASI ========== -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-users-cog text-purple-600 mr-2"></i> Data Organisasi
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="level_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Level / Role <span class="text-red-500">*</span></label>
                    <select id="level_id" name="level_id" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                        <option value="3" {{ old('level_id') == '3' ? 'selected' : '' }}>Ketua</option>
                        <option value="4" {{ old('level_id') == '4' ? 'selected' : '' }}>Pengurus</option>
                    </select>
                </div>
                <div>
                    <label for="jabatan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan (Opsional)</label>
                    <select id="jabatan_id" name="jabatan_id" class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <option value="">-- Tanpa Jabatan --</option>
                        @foreach($jabatans ?? [] as $jabatan)
                            <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>{{ $jabatan->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- ========== AKUN LOGIN ========== -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-lock text-emerald-600 mr-2"></i> Akun Login
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika ingin dibuat otomatis dari NIM.</p>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="mt-1 w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" required>
                </div>
            </div>
        </div>

        <!-- ========== STATUS ========== -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-user-check text-amber-600 mr-2"></i> Status Akun
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Aktif</label>
                    <div class="mt-2 space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="1" checked class="form-radio text-cyan-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="0" class="form-radio text-red-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Verifikasi</label>
                    <div class="mt-2 space-y-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_verified" value="1" class="form-checkbox text-cyan-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tandai sebagai terverifikasi</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                <i class="fas fa-save mr-1"></i> Simpan Ketua / Pengurus
            </button>
            <a href="{{ route('owner.officials.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white">🌐 Tambah Game Eksternal</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Masukkan link game dari website lain</p>
            </div>
            <a href="{{ route('owner.game.games.create') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ERROR SUMMARY --}}
        @if ($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <h4 class="text-red-700 dark:text-red-300 font-bold mb-2">⚠️ Terjadi kesalahan:</h4>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
            <form action="{{ route('owner.game.games.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="source_type" value="external">
                <input type="hidden" name="game_type" value="external">

                {{-- IDENTITAS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Game <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Contoh: game-eksternal-1</p>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Icon (Emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon', '🎮') }}" maxlength="10"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Banner (URL)</label>
                        <input type="text" name="banner" value="{{ old('banner') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('banner') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Developer</label>
                        <input type="text" name="developer" value="{{ old('developer', 'UNICODE') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('developer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Version</label>
                        <input type="text" name="version" value="{{ old('version', '1.0.0') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('version') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="testing" {{ old('status') == 'testing' ? 'selected' : '' }}>Testing</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                        <textarea name="description" rows="4"
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- FIELD EXTERNAL --}}
                <div class="border-t border-slate-200 dark:border-slate-700 pt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">URL Game <span class="text-red-500">*</span></label>
                        <input type="url" name="url" value="{{ old('url') }}" required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition" placeholder="https://example.com/game">
                        @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tingkat Integrasi <span class="text-red-500">*</span></label>
                        <select name="integration_level" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="link" {{ old('integration_level') == 'link' ? 'selected' : '' }}>Link (redirect ke URL)</option>
                            <option value="embedded" {{ old('integration_level') == 'embedded' ? 'selected' : '' }}>Embedded (iframe)</option>
                            <option value="api" {{ old('integration_level') == 'api' ? 'selected' : '' }}>API (koneksi data)</option>
                        </select>
                        @error('integration_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- UPLOAD GAMBAR (MAKS 4 FILE) --}}
                {{-- ============================================================ --}}
                <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">🖼️ Upload Gambar Game</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Upload maksimal <strong>4 gambar</strong> (JPG, PNG, JPEG, WEBP, GIF, dll).
                        Anda bisa pilih beberapa file sekaligus atau tambahkan satu per satu.
                    </p>

                    {{-- Input file (multiple) --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <label for="game_images" class="cursor-pointer px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Pilih Gambar
                            <input type="file" name="game_images[]" id="game_images" accept="image/*" multiple
                                   class="hidden" onchange="handleFileSelect(event)">
                        </label>
                        <span id="file-count" class="text-sm text-slate-500 dark:text-slate-400">Belum ada file dipilih</span>
                        <button type="button" onclick="addMoreFiles()" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition text-sm">
                            <i class="fas fa-plus-circle"></i> Tambah File (opsional)
                        </button>
                    </div>

                    {{-- Preview galeri --}}
                    <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <!-- Preview akan ditambahkan oleh JavaScript -->
                    </div>

                    {{-- Error --}}
                    @error('game_images.*')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">* Maksimal 4 file, ukuran maksimal 2MB per file.</p>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" name="action" value="save" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Game Eksternal
                    </button>
                    <a href="{{ route('owner.game.games.index') }}" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- JAVASCRIPT UNTUK UPLOAD & PREVIEW --}}
{{-- ============================================================ --}}
<script>
    // Variabel untuk menyimpan file yang dipilih (agar bisa ditambah)
    let selectedFiles = [];

    // Fungsi saat user memilih file melalui input
    function handleFileSelect(event) {
        const files = event.target.files;
        if (files.length === 0) return;

        // Tambahkan ke array, tapi batasi total 4
        for (let i = 0; i < files.length; i++) {
            if (selectedFiles.length >= 4) {
                alert('Maksimal 4 gambar!');
                break;
            }
            // Cek duplikat (berdasarkan nama dan ukuran sederhana)
            const duplicate = selectedFiles.some(f => f.name === files[i].name && f.size === files[i].size);
            if (!duplicate) {
                selectedFiles.push(files[i]);
            }
        }

        // Update input files (agar form mengirim semua file)
        updateInputFiles();
        renderPreviews();

        // Reset input agar bisa pilih file yang sama lagi
        document.getElementById('game_images').value = '';
    }

    // Fungsi untuk menambah file (panggil input secara manual)
    function addMoreFiles() {
        document.getElementById('game_images').click();
    }

    // Update input[type=file] dengan file yang ada di selectedFiles
    function updateInputFiles() {
        const input = document.getElementById('game_images');
        // Karena FileList tidak bisa dimodifikasi, kita buat DataTransfer
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        input.files = dataTransfer.files;

        // Update counter
        document.getElementById('file-count').textContent = selectedFiles.length + ' file dipilih (maks 4)';
    }

    // Render preview gambar
    function renderPreviews() {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 aspect-square';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index+1}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeFile(${index})" 
                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold shadow-md transition">
                        ✕
                    </button>
                    <span class="absolute bottom-1 left-2 text-xs text-white bg-black/50 px-2 py-0.5 rounded">${file.name}</span>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        // Jika tidak ada file, tampilkan pesan
        if (selectedFiles.length === 0) {
            container.innerHTML = `<div class="col-span-4 text-center py-8 text-slate-400 dark:text-slate-500 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl">
                                        <i class="fas fa-image text-4xl mb-2 block"></i>
                                        <span class="text-sm">Belum ada gambar dipilih</span>
                                    </div>`;
        }
    }

    // Hapus file berdasarkan index
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateInputFiles();
        renderPreviews();
    }

    // Inisialisasi: render awal
    document.addEventListener('DOMContentLoaded', function() {
        // Jika ada old value dari validasi gagal, tidak ada preview, jadi kita reset
        renderPreviews();
    });
</script>
@endsection
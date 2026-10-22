@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- ============================================================ --}}
        {{-- HEADER --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <span>🌐</span> Tambah Game Eksternal
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Integrasikan game dari luar (link, embed, atau API) ke Game Center UNICODE
                </p>
            </div>
            <a href="{{ route('owner.game.games.create') }}" 
               class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ============================================================ --}}
        {{-- ERROR SUMMARY --}}
        {{-- ============================================================ --}}
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

        {{-- ============================================================ --}}
        {{-- FORM UTAMA --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
            <form action="{{ route('owner.game.games.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="external">
                <input type="hidden" name="game_type" value="external">
                <input type="hidden" name="source_type" value="external">

                {{-- ============================================================ --}}
                {{-- TAB 1: IDENTITAS GAME --}}
                {{-- ============================================================ --}}
                <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-id-card text-indigo-500"></i> Identitas Game
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Informasi dasar game eksternal</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Game --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Nama Game <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                               placeholder="Contoh: Quiz Unicorn">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="slug" 
                               value="{{ old('slug') }}" 
                               required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                               placeholder="Contoh: quiz-unicorn-external">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Akan digunakan untuk URL, contoh: /game/quiz-unicorn-external</p>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Icon --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Icon (Emoji) 
                            <span class="text-xs text-slate-400">(maks 10 karakter)</span>
                        </label>
                        <input type="text" 
                               name="icon" 
                               value="{{ old('icon', '🎮') }}" 
                               maxlength="10"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Banner --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Banner (URL Gambar)
                        </label>
                        <input type="url" 
                               name="banner" 
                               value="{{ old('banner') }}" 
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                               placeholder="https://example.com/banner.jpg">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukkan URL gambar banner (opsional)</p>
                        @error('banner') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Developer --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Developer
                        </label>
                        <input type="text" 
                               name="developer" 
                               value="{{ old('developer', 'UNICODE') }}" 
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('developer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Version --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Version
                        </label>
                        <input type="text" 
                               name="version" 
                               value="{{ old('version', '1.0.0') }}" 
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('version') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required 
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
                            <option value="testing" {{ old('status') == 'testing' ? 'selected' : '' }}>🧪 Testing</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>🚀 Published</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>🔧 Maintenance</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>📦 Archived</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="description" rows="4" 
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                                  placeholder="Deskripsikan game ini...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- TAB 2: KONFIGURASI EXTERNAL --}}
                {{-- ============================================================ --}}
                <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-link text-cyan-500"></i> Konfigurasi External
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Atur koneksi ke game eksternal</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- URL Game --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            URL Game <span class="text-red-500">*</span>
                        </label>
                        <input type="url" 
                               name="url" 
                               value="{{ old('url') }}" 
                               required
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                               placeholder="https://example.com/game">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">URL lengkap ke game eksternal</p>
                        @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tingkat Integrasi --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Tingkat Integrasi <span class="text-red-500">*</span>
                        </label>
                        <select name="integration_level" required 
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="link" {{ old('integration_level') == 'link' ? 'selected' : '' }}>🔗 Link (redirect ke URL)</option>
                            <option value="embedded" {{ old('integration_level') == 'embedded' ? 'selected' : '' }}>📦 Embedded (iframe)</option>
                            <option value="api" {{ old('integration_level') == 'api' ? 'selected' : '' }}>⚡ API (koneksi data)</option>
                        </select>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            <strong>Link:</strong> redirect ke URL asli · 
                            <strong>Embedded:</strong> tampil di dalam iframe · 
                            <strong>API:</strong> koneksi data via API
                        </p>
                        @error('integration_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- API Key (optional) --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            API Key <span class="text-xs text-slate-400">(opsional)</span>
                        </label>
                        <input type="text" 
                               name="api_key" 
                               value="{{ old('api_key') }}" 
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                               placeholder="Masukkan API key jika diperlukan">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Diperlukan jika integrasi menggunakan API</p>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- TAB 3: UPLOAD GAMBAR --}}
                {{-- ============================================================ --}}
                <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-images text-teal-500"></i> Upload Gambar Game
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Upload maksimal <strong>4 gambar</strong> (JPG, PNG, JPEG, WEBP, GIF). 
                        Ukuran maksimal <strong>2MB</strong> per file.
                    </p>
                </div>

                <div class="space-y-4">
                    {{-- Tombol upload --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <label for="game_images" 
                               class="cursor-pointer px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md flex items-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Pilih Gambar
                            <input type="file" 
                                   name="cover_images[]" 
                                   id="game_images" 
                                   accept="image/*" 
                                   multiple 
                                   class="hidden" 
                                   onchange="handleFileSelect(event)">
                        </label>
                        <span id="file-count" class="text-sm text-slate-500 dark:text-slate-400">Belum ada file dipilih</span>
                        <button type="button" 
                                onclick="addMoreFiles()" 
                                class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition text-sm">
                            <i class="fas fa-plus-circle"></i> Tambah File (opsional)
                        </button>
                    </div>

                    {{-- Preview --}}
                    <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <!-- Preview akan ditambahkan oleh JavaScript -->
                    </div>

                    @error('cover_images.*')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- TOMBOL SUBMIT --}}
                {{-- ============================================================ --}}
                <div class="flex flex-wrap gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" 
                            name="action" 
                            value="save" 
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Game Eksternal
                    </button>
                    <a href="{{ route('owner.game.games.index') }}" 
                       class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">
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
    // Variabel untuk menyimpan file yang dipilih
    let selectedFiles = [];

    // Fungsi saat user memilih file melalui input
    function handleFileSelect(event) {
        const files = event.target.files;
        if (files.length === 0) return;

        for (let i = 0; i < files.length; i++) {
            if (selectedFiles.length >= 4) {
                alert('⚠️ Maksimal 4 gambar!');
                break;
            }
            // Cek duplikat
            const duplicate = selectedFiles.some(f => f.name === files[i].name && f.size === files[i].size);
            if (!duplicate) {
                selectedFiles.push(files[i]);
            }
        }

        updateInputFiles();
        renderPreviews();
        document.getElementById('game_images').value = '';
    }

    // Tambah file
    function addMoreFiles() {
        document.getElementById('game_images').click();
    }

    // Update input file
    function updateInputFiles() {
        const input = document.getElementById('game_images');
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
        document.getElementById('file-count').textContent = selectedFiles.length + ' file dipilih (maks 4)';
    }

    // Render preview
    function renderPreviews() {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';

        if (selectedFiles.length === 0) {
            container.innerHTML = `
                <div class="col-span-4 text-center py-8 text-slate-400 dark:text-slate-500 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl">
                    <i class="fas fa-image text-4xl mb-2 block"></i>
                    <span class="text-sm">Belum ada gambar dipilih</span>
                    <p class="text-xs mt-1">Klik tombol "Pilih Gambar" untuk upload</p>
                </div>
            `;
            return;
        }

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 aspect-square group';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index+1}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeFile(${index})" 
                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold shadow-md transition opacity-0 group-hover:opacity-100">
                        ✕
                    </button>
                    <span class="absolute bottom-1 left-2 text-xs text-white bg-black/60 px-2 py-0.5 rounded truncate max-w-[80%]">
                        ${file.name}
                    </span>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // Hapus file
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateInputFiles();
        renderPreviews();
    }

    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        renderPreviews();
    });
</script>
@endsection
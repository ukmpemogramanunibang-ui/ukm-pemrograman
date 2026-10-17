@extends('layouts.roles.owner')

@section('title', 'Buat Kelompok Baru')

@push('styles')
<style>
.form-card {
    transition: box-shadow .2s ease;
}
.form-card:focus-within {
    box-shadow: 0 0 0 4px rgba(147, 51, 234, .08);
}

.preview-card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.preview-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px -10px rgba(0,0,0,.15);
}

.char-counter {
    font-variant-numeric: tabular-nums;
}

.btn-loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}
.btn-loading::after {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    width: 16px; height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: btnSpin .6s linear infinite;
}
@keyframes btnSpin { to { transform: rotate(360deg); } }

.preview-avatar {
    transition: transform .2s ease;
}
.preview-card:hover .preview-avatar {
    transform: scale(1.05);
}
</style>
@endpush

@section('content')

<div class="p-4 md:p-6 max-w-[1200px] mx-auto">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}"
           class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-700
                  hover:bg-gray-200 dark:hover:bg-gray-600
                  flex items-center justify-center text-gray-600 dark:text-gray-300
                  transition"
           aria-label="Kembali">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white
                       flex items-center gap-2">
                <i class="fas fa-plus-circle text-purple-500"></i>
                Buat Kelompok Baru
            </h1>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Tentukan nama, deskripsi, dan status kelompok baru.
            </p>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- SESSION ALERTS
    {{-- ========================================================= --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500
                    text-green-700 dark:text-green-300 p-4 rounded-xl
                    flex items-start gap-3">
            <i class="fas fa-check-circle mt-0.5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500
                    text-red-700 dark:text-red-300 p-4 rounded-xl">
            <div class="font-bold mb-1 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                Periksa kembali data berikut:
            </div>
            <ul class="list-disc ml-5 text-xs space-y-0.5 mt-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- LAYOUT 2 KOLOM: FORM + PREVIEW
    {{-- ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ===================================================== --}}
        {{-- FORM (2/3)
        {{-- ===================================================== --}}
        <form action="{{ route('owner.kelompok.store') }}"
              method="POST"
              id="createGroupForm"
              class="lg:col-span-2 space-y-5">

            @csrf

            {{-- NAMA --}}
            <div class="form-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700 p-5 md:p-6">
                <label for="name"
                       class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Nama Kelompok <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       maxlength="100"
                       autocomplete="off"
                       placeholder="Contoh: Web Development"
                       class="w-full rounded-xl border-gray-300 dark:border-gray-600
                              bg-gray-50 dark:bg-gray-700
                              text-gray-900 dark:text-white px-4 py-3
                              focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                              text-sm">
                <div class="mt-1.5 flex justify-between text-xs text-gray-400">
                    <span>Gunakan nama yang jelas dan mudah diingat</span>
                    <span class="char-counter"><span id="nameCount">0</span>/100</span>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="form-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700 p-5 md:p-6">
                <label for="description"
                       class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi
                </label>
                <textarea id="description"
                          name="description"
                          rows="5"
                          maxlength="500"
                          placeholder="Jelaskan fokus, tujuan, atau kegiatan kelompok ini..."
                          class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                 bg-gray-50 dark:bg-gray-700
                                 text-gray-900 dark:text-white px-4 py-3
                                 focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                 text-sm resize-none">{{ old('description') }}</textarea>
                <div class="mt-1.5 flex justify-between text-xs text-gray-400">
                    <span>Opsional — bisa diisi nanti</span>
                    <span class="char-counter"><span id="descCount">0</span>/500</span>
                </div>
            </div>

            {{-- STATUS + MAX MEMBERS --}}
            <div class="form-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                        border border-gray-200 dark:border-gray-700 p-5 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- STATUS --}}
                    <div>
                        <label for="status"
                               class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Status Kelompok <span class="text-red-500">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                required
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-700
                                       text-gray-900 dark:text-white px-4 py-3
                                       focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                       text-sm">
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>
                        <p class="mt-1.5 text-xs text-gray-400">
                            Kelompok nonaktif tidak akan muncul di daftar utama.
                        </p>
                    </div>

                    {{-- MAX MEMBERS --}}
                    <div>
                        <label for="max_members"
                               class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Kapasitas Maksimal
                        </label>
                        <input type="number"
                               id="max_members"
                               name="max_members"
                               value="{{ old('max_members') }}"
                               min="1"
                               max="999"
                               placeholder="Kosongkan = tidak dibatasi"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-700
                                      text-gray-900 dark:text-white px-4 py-3
                                      focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                      text-sm">
                        <p class="mt-1.5 text-xs text-gray-400">
                            Biarkan kosong jika kapasitas tidak dibatasi.
                        </p>
                    </div>
                </div>
            </div>

            {{-- INFO NOTE --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl
                        border border-blue-200 dark:border-blue-800 p-4 md:p-5">
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 text-white shrink-0
                                flex items-center justify-center">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <div class="font-bold mb-1">Setelah kelompok dibuat</div>
                        <ul class="list-disc ml-4 space-y-0.5 text-xs text-blue-700 dark:text-blue-300">
                            <li>Kode kelompok akan digenerate otomatis (format <code>KLP-XXXXXX</code>).</li>
                            <li>Jika kamu bukan Owner/Superadmin, kamu otomatis menjadi <strong>Ketua</strong> kelompok ini.</li>
                            <li>Kamu bisa langsung menambahkan anggota dari halaman undang anggota.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                <a href="{{ route('owner.kelompok.index') }}"
                   class="px-6 py-3 rounded-xl
                          bg-gray-100 dark:bg-gray-700
                          hover:bg-gray-200 dark:hover:bg-gray-600
                          text-gray-700 dark:text-gray-200 text-sm font-semibold
                          text-center transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>

                <button type="submit"
                        class="px-8 py-3 rounded-xl
                               bg-purple-600 hover:bg-purple-700
                               text-white text-sm font-bold
                               shadow-sm transition
                               flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Kelompok
                </button>
            </div>
        </form>

        {{-- ===================================================== --}}
        {{-- PREVIEW (1/3) — sticky
        {{-- ===================================================== --}}
        <div class="lg:col-span-1">
            <div class="lg:sticky lg:top-6">
                <div class="text-xs font-bold uppercase tracking-wider
                            text-gray-400 dark:text-gray-500 mb-3 px-1">
                    Preview Kartu
                </div>

                <div class="preview-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm
                            border border-gray-200 dark:border-gray-700
                            overflow-hidden">

                    <div class="p-5 pb-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                <div class="preview-avatar w-11 h-11 rounded-xl shrink-0
                                            bg-gradient-to-br from-purple-500 to-indigo-600
                                            flex items-center justify-center
                                            text-white font-bold text-lg shadow-sm"
                                     id="previewAvatar">
                                    ?
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 id="previewName"
                                        class="font-bold text-base text-gray-800 dark:text-white
                                               truncate">
                                        Nama Kelompok
                                    </h2>
                                    <p class="text-[11px] text-gray-400 font-mono truncate">
                                        KLP-XXXXXX
                                    </p>
                                </div>
                            </div>

                            <span id="previewStatusBadge"
                                  class="shrink-0 px-2.5 py-1 rounded-full
                                         text-[10px] font-bold uppercase tracking-wide
                                         bg-emerald-100 text-emerald-700
                                         dark:bg-emerald-900/30 dark:text-emerald-300">
                                Aktif
                            </span>
                        </div>

                        <p id="previewDescription"
                           class="mt-3 text-xs text-gray-500 dark:text-gray-400 line-clamp-2 italic opacity-60">
                            Deskripsi kelompok akan muncul di sini...
                        </p>
                    </div>

                    <div class="px-5 pb-3">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-semibold text-gray-600 dark:text-gray-300">
                                <i class="fas fa-users text-[10px] mr-1"></i> Anggota
                            </span>
                            <span class="font-bold text-gray-800 dark:text-white">
                                0
                                <span class="text-gray-400 font-medium" id="previewMax">• ∞</span>
                            </span>
                        </div>

                        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700"></div>
                        <div class="mt-1 text-[10px] text-gray-400" id="previewCapacity">
                            Tidak dibatasi kapasitas
                        </div>
                    </div>

                    <div class="px-5 pb-3 flex flex-wrap items-center gap-x-4 gap-y-1.5
                                text-[11px] text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            <span class="font-semibold text-gray-600 dark:text-gray-300">
                                Kosong
                            </span>
                        </span>
                        <span class="inline-flex items-center gap-1 text-gray-400">
                            <i class="fas fa-user-slash"></i>
                            <span>Belum ada ketua</span>
                        </span>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700
                                bg-gray-50/60 dark:bg-gray-900/30
                                flex items-center gap-2 pointer-events-none opacity-60">
                        <span class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center
                                     bg-blue-50 dark:bg-blue-900/30
                                     text-blue-700 dark:text-blue-300">
                            <i class="fas fa-info-circle mr-1"></i> Detail
                        </span>
                        <span class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center
                                     bg-red-50 dark:bg-red-900/30
                                     text-red-700 dark:text-red-300">
                            <i class="fas fa-key mr-1"></i> Akses
                        </span>
                        <span class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold text-center
                                     bg-purple-50 dark:bg-purple-900/30
                                     text-purple-700 dark:text-purple-300">
                            <i class="fas fa-user-plus mr-1"></i> Undang
                        </span>
                    </div>
                </div>

                <p class="mt-3 text-[11px] text-gray-400 dark:text-gray-500
                          text-center px-2">
                    <i class="fas fa-lightbulb mr-1"></i>
                    Preview berubah otomatis saat kamu mengisi form.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const form = document.getElementById('createGroupForm');
    if (!form) return;

    /* =========================================================
     | LIVE PREVIEW
     * ========================================================= */
    const nameInput        = document.getElementById('name');
    const descInput        = document.getElementById('description');
    const statusInput      = document.getElementById('status');
    const maxInput         = document.getElementById('max_members');

    const previewName      = document.getElementById('previewName');
    const previewDesc      = document.getElementById('previewDescription');
    const previewAvatar    = document.getElementById('previewAvatar');
    const previewBadge     = document.getElementById('previewStatusBadge');
    const previewMax       = document.getElementById('previewMax');
    const previewCapacity  = document.getElementById('previewCapacity');

    const nameCount        = document.getElementById('nameCount');
    const descCount        = document.getElementById('descCount');

    function updateNamePreview() {
        const val = nameInput.value.trim();

        previewName.textContent = val || 'Nama Kelompok';
        previewAvatar.textContent = val ? val.charAt(0).toUpperCase() : '?';

        // Warna avatar bervariasi by huruf
        if (val) {
            const colors = [
                ['#a855f7', '#6366f1'], // purple → indigo
                ['#10b981', '#14b8a6'], // emerald → teal
                ['#f59e0b', '#ef4444'], // amber → red
                ['#3b82f6', '#8b5cf6'], // blue → violet
                ['#ec4899', '#f43f5e'], // pink → rose
            ];
            const idx = val.charCodeAt(0) % colors.length;
            const [c1, c2] = colors[idx];
            previewAvatar.style.background = `linear-gradient(135deg, ${c1}, ${c2})`;
        } else {
            previewAvatar.style.background = '';
        }
    }

    function updateDescPreview() {
        const val = descInput.value.trim();
        if (val) {
            previewDesc.textContent = val;
            previewDesc.classList.remove('italic', 'opacity-60');
        } else {
            previewDesc.textContent = 'Deskripsi kelompok akan muncul di sini...';
            previewDesc.classList.add('italic', 'opacity-60');
        }
    }

    function updateStatusPreview() {
        const val = statusInput.value;
        if (val === 'aktif') {
            previewBadge.textContent = 'Aktif';
            previewBadge.className =
                'shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide ' +
                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
        } else {
            previewBadge.textContent = 'Nonaktif';
            previewBadge.className =
                'shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide ' +
                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
        }
    }

    function updateCapacityPreview() {
        const val = parseInt(maxInput.value, 10);

        if (val && val > 0) {
            previewMax.textContent = `/ ${val}`;
            previewMax.className = 'text-gray-400 font-medium';
            previewCapacity.textContent = `Tersedia ${val} slot`;
        } else {
            previewMax.textContent = '• ∞';
            previewMax.className = 'text-gray-400 font-medium';
            previewCapacity.textContent = 'Tidak dibatasi kapasitas';
        }
    }

    function updateCounters() {
        if (nameCount) nameCount.textContent = nameInput.value.length;
        if (descCount) descCount.textContent = descInput.value.length;
    }

    /* Bind events */
    nameInput?.addEventListener('input', function () {
        updateNamePreview();
        updateCounters();
    });

    descInput?.addEventListener('input', function () {
        updateDescPreview();
        updateCounters();
    });

    statusInput?.addEventListener('change', updateStatusPreview);

    maxInput?.addEventListener('input', updateCapacityPreview);

    /* Initial state */
    updateNamePreview();
    updateDescPreview();
    updateStatusPreview();
    updateCapacityPreview();
    updateCounters();

    /* =========================================================
     | SUBMIT LOADING STATE
     * ========================================================= */
    form.addEventListener('submit', function () {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('btn-loading');
        }
    });

})();
</script>
@endpush
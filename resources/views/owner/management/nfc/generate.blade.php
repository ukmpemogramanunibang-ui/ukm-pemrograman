@extends('layouts.roles.owner')

@section('title', 'Generate NFC')
@section('page-title', 'Generate NFC Credential')

@section('content')
<div class="space-y-5 sm:space-y-6" x-data="nfcGenerate()" x-cloak>

    {{-- ============================================================ --}}
    {{-- FLASH & VALIDATION ERRORS --}}
    {{-- ============================================================ --}}
    @if($errors->any())
        <div class="rounded-2xl border-2 border-rose-300 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/20 p-4 shadow-sm animate-fadeIn">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-rose-500 text-xl mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <div class="font-bold text-rose-800 dark:text-rose-300 mb-1">
                        Gagal menyimpan NFC
                    </div>
                    <ul class="list-disc list-inside text-sm text-rose-700 dark:text-rose-400 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border-2 border-rose-300 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/20 p-4 shadow-sm animate-fadeIn">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-rose-500 text-xl mt-0.5 flex-shrink-0"></i>
                <div class="flex-1 text-sm text-rose-700 dark:text-rose-400">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- HEADER + STEPPER --}}
    {{-- ============================================================ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-600 via-blue-600 to-indigo-700 text-white shadow-xl">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative p-5 sm:p-7">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-lg flex-shrink-0">
                        <i class="fas fa-id-card text-xl sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight">Daftarkan NFC Baru</h1>
                        <p class="text-white/75 text-xs sm:text-sm mt-1 max-w-lg">
                            Satu kartu hanya boleh terhubung ke satu akun. Ikuti langkah di bawah.
                        </p>
                    </div>
                </div>
                <a href="{{ route('owner.management.nfc.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl text-sm hover:bg-white/25 active:scale-95 transition-all duration-150 self-start sm:self-auto">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>

            {{-- Stepper --}}
            <div class="mt-6 grid grid-cols-4 gap-2 sm:gap-3">
                <template x-for="(s, i) in steps" :key="i">
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-full">
                            <div class="h-1.5 rounded-full bg-white/20 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 ease-out"
                                     :class="s.done ? 'bg-emerald-400 w-full' : (s.active ? 'bg-white w-1/2' : 'bg-transparent w-0')"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold whitespace-nowrap"
                             :class="s.done || s.active ? 'text-white' : 'text-white/50'">
                            <i class="fas text-[9px]"
                               :class="s.done ? 'fa-check-circle' : s.icon"></i>
                            <span class="hidden sm:inline" x-text="s.label"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- EMPTY STATE --}}
    {{-- ============================================================ --}}
    @if($users->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-8 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-info-circle text-3xl text-amber-500"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-2">
                    @if($blockedUsers->isNotEmpty())
                        Semua akun sudah terdaftar NFC
                    @else
                        Belum ada akun yang dipilih
                    @endif
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    @if($blockedUsers->isNotEmpty())
                        {{ $blockedUsers->count() }} akun yang dipilih sudah memiliki NFC aktif.
                        Untuk menggantinya, cabut dulu NFC lama di halaman Manajemen NFC.
                    @else
                        Pilih akun terlebih dahulu di halaman <strong>Manajemen NFC</strong>,
                        kemudian klik tombol <strong>"Daftarkan yang Dipilih"</strong>.
                    @endif
                </p>

                @if($blockedUsers->isNotEmpty())
                    <div class="mb-5 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 text-left">
                        <div class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase mb-2">
                            <i class="fas fa-ban mr-1"></i> Akun yang di-skip
                        </div>
                        <ul class="space-y-1 text-xs text-amber-700 dark:text-amber-300">
                            @foreach($blockedUsers as $bu)
                                <li>
                                    <i class="fas fa-circle text-[6px] mr-2"></i>
                                    {{ $bu->name }} — {{ $bu->nfc_status }}
                                    @if($bu->nfc_identifier)
                                        (UID: <span class="font-mono">{{ $bu->nfc_identifier }}</span>)
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="{{ route('owner.management.nfc.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-xl font-bold shadow-lg shadow-cyan-500/30 hover:from-cyan-700 hover:to-blue-700 active:scale-[0.98] transition-all">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Manajemen NFC</span>
                </a>
            </div>
        </div>
    @else

    {{-- ============================================================ --}}
    {{-- BANNER --}}
    {{-- ============================================================ --}}
    <div class="flex items-start gap-3 p-4 rounded-2xl bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border border-cyan-200 dark:border-cyan-700/40">
        <div class="w-9 h-9 rounded-xl bg-cyan-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-wand-magic-sparkles text-cyan-600 dark:text-cyan-400"></i>
        </div>
        <div class="flex-1 text-sm text-cyan-800 dark:text-cyan-300">
            <strong>{{ $users->count() }} akun</strong> siap didaftarkan (dari halaman sebelumnya).
            @if($blockedUsers->isNotEmpty())
                <span class="text-amber-600 dark:text-amber-400 font-semibold">
                    · {{ $blockedUsers->count() }} akun di-skip karena sudah punya NFC.
                </span>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STEP 1: METODE --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden transition-shadow duration-300"
         :class="method ? 'ring-2 ring-cyan-500/20' : ''">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition-all duration-300"
                 :class="method
                    ? 'bg-emerald-500 text-white'
                    : 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400'">
                <i class="fas" :class="method ? 'fa-check' : 'fa-th-large'"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Pilih Metode Pendaftaran</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Wajib dipilih sebelum melanjutkan</p>
            </div>
            <span x-show="method" x-transition class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold">
                <i class="fas fa-check-circle mr-1"></i> OK
            </span>
        </div>

        <div class="p-5 sm:p-6">
            <div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
                {{-- AUTO --}}
                <button type="button" @click="setMethod('auto')"
                        :class="method === 'auto'
                            ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20 ring-2 ring-cyan-500/30 shadow-md'
                            : 'border-gray-200 dark:border-gray-700 hover:border-cyan-400 hover:shadow-sm'"
                        class="group relative text-left p-5 rounded-2xl border-2 transition-all duration-200 active:scale-[0.98]">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'auto'
                                    ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/30'
                                    : 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400'">
                                <i class="fas fa-wifi text-lg"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 dark:text-white text-base">Otomatis</div>
                                <div class="text-[10px] text-cyan-600 dark:text-cyan-400 font-semibold uppercase tracking-wider">NFC Reader</div>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center"
                             :class="method === 'auto' ? 'border-cyan-500 bg-cyan-500' : 'border-gray-300 dark:border-gray-600'">
                            <i x-show="method === 'auto'" class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Tap kartu KTM ke reader. UID akan terisi otomatis dari perangkat.
                    </p>
                </button>

                {{-- MANUAL --}}
                <button type="button" @click="setMethod('manual')"
                        :class="method === 'manual'
                            ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20 ring-2 ring-cyan-500/30 shadow-md'
                            : 'border-gray-200 dark:border-gray-700 hover:border-cyan-400 hover:shadow-sm'"
                        class="group relative text-left p-5 rounded-2xl border-2 transition-all duration-200 active:scale-[0.98]">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'manual'
                                    ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/30'
                                    : 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400'">
                                <i class="fas fa-keyboard text-lg"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 dark:text-white text-base">Manual</div>
                                <div class="text-[10px] text-cyan-600 dark:text-cyan-400 font-semibold uppercase tracking-wider">Input UID</div>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center"
                             :class="method === 'manual' ? 'border-cyan-500 bg-cyan-500' : 'border-gray-300 dark:border-gray-600'">
                            <i x-show="method === 'manual'" class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Masukkan UID kartu secara manual — ketik, paste, atau upload dari file.
                    </p>
                </button>
            </div>

            {{-- UID input --}}
            <div x-show="method === 'manual'" x-transition.duration.300ms class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    UID Kartu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fas fa-fingerprint absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                           x-model="uid"
                           @input="uid = uid.toUpperCase().replace(/[^A-F0-9]/g, '')"
                           @keydown.enter.prevent="canSubmit && submitForm()"
                           placeholder="Contoh: 04AABBCCDD1122"
                           maxlength="64"
                           autocomplete="off"
                           spellcheck="false"
                           class="w-full pl-11 pr-4 py-3.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 text-gray-800 dark:text-white font-mono tracking-widest text-base
                                  focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition-all duration-200">
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition text-xs font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fas fa-file-upload"></i>
                        <span>Upload File UID (.txt)</span>
                        <input type="file" accept=".txt,text/plain" class="hidden" @change="handleUidFile($event)">
                    </label>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400">
                        File berisi 1 UID (hex 0-9, A-F).
                    </span>
                </div>

                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Hanya karakter hex 0-9, A-F (min. 8 karakter)
                    </span>
                    <span :class="uid.length >= 8 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'" class="font-mono font-semibold">
                        <span x-text="uid.length"></span>/64
                    </span>
                </div>
                <p x-show="uid.length > 0 && uid.length < 8" class="text-xs text-red-500 mt-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i> UID terlalu pendek
                </p>
            </div>

            {{-- Auto waiting --}}
            <div x-show="method === 'auto'" x-transition.duration.300ms class="mt-5">
                <div class="p-5 rounded-xl bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border border-cyan-200 dark:border-cyan-800 flex items-center gap-4">
                    <div class="relative w-12 h-12 flex items-center justify-center flex-shrink-0">
                        <span class="absolute inset-0 rounded-full bg-cyan-500/20 animate-ping"></span>
                        <span class="relative w-10 h-10 rounded-full bg-cyan-500 text-white flex items-center justify-center">
                            <i class="fas fa-satellite-dish"></i>
                        </span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-cyan-800 dark:text-cyan-300">
                            Menunggu tap kartu dari reader...
                        </p>
                        <p class="text-xs text-cyan-600 dark:text-cyan-400 mt-0.5">
                            UID akan terisi otomatis. Pastikan reader terhubung.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STEP 2: PREVIEW --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-cyan-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                <i class="fas fa-eye"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Preview Akun Terpilih</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Periksa detail akun sebelum didaftarkan</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 font-bold tabular-nums">
                {{ $users->count() }} akun
            </span>
        </div>

        <div class="p-5 sm:p-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($users as $u)
                    <div class="relative rounded-2xl border-2 p-5 transition-all duration-200 hover:shadow-lg border-emerald-200 dark:border-emerald-800/50 bg-emerald-50/30 dark:bg-emerald-900/10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="relative w-12 h-12 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 shadow-md bg-gradient-to-br from-emerald-400 to-emerald-600">
                                <span class="text-base">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-gray-800 bg-emerald-500"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-gray-800 dark:text-white truncate text-sm">{{ $u->name }}</div>
                                <div class="text-xs text-gray-500 font-mono truncate">{{ $u->nim ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 flex items-center gap-1.5">
                                    <i class="fas fa-layer-group text-[10px]"></i> Level
                                </span>
                                <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $u->level }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 flex items-center gap-1.5">
                                    <i class="fas fa-circle text-[10px]"></i> Status
                                </span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $u->status }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 flex items-center gap-1.5">
                                    <i class="fas fa-briefcase text-[10px]"></i> Jabatan
                                </span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300 truncate max-w-[130px] {{ !$u->jabatan ? 'italic opacity-50' : '' }}">
                                    {{ $u->jabatan ?? 'Tidak ada' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 flex items-center gap-1.5">
                                    <i class="fas fa-star text-[10px]"></i> Poin
                                </span>
                                <span class="font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                    {{ number_format($u->poin, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-emerald-200 dark:border-emerald-800/50">
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-[10px]"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-emerald-700 dark:text-emerald-400">Siap didaftarkan</div>
                                    <div class="text-[10px] text-emerald-600 dark:text-emerald-500">Belum terdaftar</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STEP 3: OPSI --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                <i class="fas fa-cog"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Opsi Tambahan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Semua opsional</p>
            </div>
        </div>

        <div class="p-5 sm:p-6 grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-calendar-day mr-1 text-cyan-500"></i>
                    Tanggal Kedaluwarsa
                </label>
                <input type="date" x-model="expiredAt"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 dark:text-white
                              focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition-all duration-200">
                <p class="text-xs text-gray-500 mt-1.5">
                    <i class="fas fa-info-circle mr-1"></i> Kosongkan jika tidak ada kedaluwarsa.
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-clipboard-list mr-1 text-cyan-500"></i>
                    Ringkasan
                </label>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-600 space-y-1.5 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-bold text-gray-800 dark:text-white" x-text="method ? method.toUpperCase() : '—'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Akun</span>
                        <span class="font-bold text-cyan-600 dark:text-cyan-400 tabular-nums">{{ $users->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">UID</span>
                        <span class="font-mono font-bold text-gray-800 dark:text-white text-xs truncate max-w-[120px]" x-text="uid || '—'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STEP 4: KONFIRMASI --}}
    {{-- ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition-all duration-300"
                 :class="canSubmit
                    ? 'bg-emerald-500 text-white'
                    : 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400'">
                <i class="fas" :class="canSubmit ? 'fa-rocket' : 'fa-clipboard-check'"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 dark:text-white">Konfirmasi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <span x-show="!canSubmit" x-text="`${remainingSteps} langkah lagi`"></span>
                    <span x-show="canSubmit" class="text-emerald-600 dark:text-emerald-400 font-semibold">
                        <i class="fas fa-check-circle mr-1"></i> Siap di-submit
                    </span>
                </p>
            </div>
        </div>

        <div class="p-5 sm:p-6">
            <ul class="space-y-2 mb-6">
                <template x-for="(c, i) in checklist" :key="i">
                    <li x-show="c.show !== false"
                        class="flex items-center gap-2.5 text-sm transition-all duration-200"
                        :class="c.ok ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-300"
                             :class="c.ok
                                ? 'bg-emerald-500 text-white'
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-400'">
                            <i class="fas text-[10px]"
                               :class="c.ok ? 'fa-check' : 'fa-circle'"></i>
                        </div>
                        <span class="font-medium" x-text="c.label"></span>
                    </li>
                </template>
            </ul>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                <a href="{{ route('owner.management.nfc.index') }}"
                   class="px-5 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-[0.98] transition-all duration-150 font-semibold text-center">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <div class="flex-1 hidden sm:block"></div>
                <button type="button"
                        @click="submitForm()"
                        :disabled="!canSubmit || submitting"
                        :class="canSubmit && !submitting
                            ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-lg shadow-cyan-500/30 active:scale-95 cursor-pointer'
                            : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'"
                        class="px-6 py-3 rounded-xl font-bold transition-all duration-150 inline-flex items-center justify-center gap-2">
                    <template x-if="submitting">
                        <span><i class="fas fa-circle-notch fa-spin mr-1"></i> Memproses...</span>
                    </template>
                    <template x-if="!submitting">
                        <span><i class="fas fa-rocket mr-1"></i> Generate NFC</span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    <div class="h-20 sm:hidden"></div>

    {{-- ============================================================ --}}
    {{-- FORM — ✅ SUDAH DI DALAM x-data SCOPE --}}
    {{-- ============================================================ --}}
    <form action="{{ route('owner.management.nfc.generate.store') }}" method="POST" id="nfc-form" x-ref="nfcForm">
        @csrf
        <input type="hidden" name="nfc_identifier" :value="uid"     x-ref="uidInput">
        <input type="hidden" name="expired_at"     :value="expiredAt">
        <input type="hidden" name="replace"        :value="replace ? 1 : 0">
        @foreach($preselectIds as $pid)
            <input type="hidden" name="user_ids[]" value="{{ $pid }}">
        @endforeach
    </form>

    @endif

    {{-- ============================================================ --}}
    {{-- MOBILE STICKY SUBMIT BAR --}}
    {{-- ============================================================ --}}
    @if($users->isNotEmpty())
    <div class="fixed bottom-0 left-0 right-0 sm:hidden z-40 px-4 py-3 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 shadow-2xl">
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Siap Daftar</div>
                <div class="font-black text-lg text-cyan-600 dark:text-cyan-400 tabular-nums">{{ $users->count() }}</div>
            </div>
            <button type="button"
                    @click="submitForm()"
                    :disabled="!canSubmit || submitting"
                    :class="canSubmit && !submitting
                        ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-lg shadow-cyan-500/30 active:scale-95'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'"
                    class="px-5 py-3 rounded-xl font-bold transition-all duration-150 inline-flex items-center gap-2">
                <template x-if="submitting">
                    <span><i class="fas fa-circle-notch fa-spin"></i></span>
                </template>
                <template x-if="!submitting">
                    <span><i class="fas fa-rocket"></i> Generate</span>
                </template>
            </button>
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- LOCAL TOAST --}}
    {{-- ============================================================ --}}
    <div x-show="localToast.show" x-transition.opacity
         class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] px-5 py-3 rounded-xl shadow-2xl flex items-center gap-2 max-w-md text-sm text-white"
         :class="localToast.type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'">
        <i class="fas" :class="localToast.type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'"></i>
        <span x-text="localToast.message"></span>
    </div>
</div>

@push('scripts')
<script>
function nfcGenerate() {
    return {
        // ---------- STATE ----------
        method: null,
        uid: '',
        replace: false,
        expiredAt: '',
        submitting: false,
        localToast: { show: false, message: '', type: 'success' },

        // ---------- METHODS ----------
        setMethod(m) {
            this.method = m;
            if (m === 'auto') this.uid = '';
        },

        handleUidFile(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const content = String(e.target.result || '').trim();
                const match = content.match(/[A-F0-9]{8,64}/i);
                if (match) {
                    this.uid = match[0].toUpperCase();
                    this.showToast('UID berhasil dibaca dari file', 'success');
                    if (!this.method) this.method = 'manual';
                } else {
                    this.showToast('File tidak berisi UID yang valid (hex 8-64 karakter).', 'error');
                }
            };
            reader.readAsText(file);
            event.target.value = '';
        },

        /**
         * Submit form dengan safety check.
         * Form ada di dalam Alpine scope, jadi binding :value bekerja.
         */
        submitForm() {
            // ----- Check 1: metode sudah dipilih? -----
            if (!this.method) {
                this.showToast('Pilih metode pendaftaran dulu.', 'error');
                return;
            }

            // ----- Check 2: UID untuk mode manual -----
            if (this.method === 'manual') {
                if (this.uid.trim().length === 0) {
                    this.showToast('UID Kartu wajib diisi.', 'error');
                    return;
                }
                if (this.uid.trim().length < 8) {
                    this.showToast('UID minimal 8 karakter.', 'error');
                    return;
                }
                if (!/^[A-F0-9]+$/.test(this.uid)) {
                    this.showToast('UID hanya boleh karakter hex (0-9, A-F).', 'error');
                    return;
                }
            }

            // ----- Check 3: form punya user_ids -----
            const form = document.getElementById('nfc-form');
            if (!form) {
                this.showToast('Error: form tidak ditemukan.', 'error');
                console.error('Form #nfc-form tidak ditemukan.');
                return;
            }

            const userInputs = form.querySelectorAll('input[name="user_ids[]"]');
            if (userInputs.length === 0) {
                this.showToast('Tidak ada user yang dipilih. Kembali ke Manajemen NFC.', 'error');
                return;
            }

            // ----- Check 4: pastikan hidden input ter-sync (double safety) -----
            const uidHidden = form.querySelector('input[name="nfc_identifier"]');
            if (uidHidden) {
                uidHidden.value = this.uid;
            }

            const expiredHidden = form.querySelector('input[name="expired_at"]');
            if (expiredHidden) {
                expiredHidden.value = this.expiredAt;
            }

            const replaceHidden = form.querySelector('input[name="replace"]');
            if (replaceHidden) {
                replaceHidden.value = this.replace ? '1' : '0';
            }

            // ----- Debug log (bisa dihapus nanti) -----
            console.log('[NFC] Submitting form:', {
                method: this.method,
                uid: this.uid,
                expiredAt: this.expiredAt,
                replace: this.replace,
                userCount: userInputs.length,
            });

            // ----- Submit -----
            this.submitting = true;
            form.submit();
        },

        showToast(message, type = 'success') {
            this.localToast = { show: true, message, type };
            setTimeout(() => { this.localToast.show = false; }, 3000);
        },

        // ---------- COMPUTED ----------
        get steps() {
            return [
                { label: 'Metode',  icon: 'fa-th-large', active: !this.method,                    done: !!this.method },
                { label: 'Preview', icon: 'fa-eye',      active: !!this.method && !this.canSubmit, done: !!this.method },
                { label: 'Opsi',    icon: 'fa-cog',      active: false,                           done: false },
                { label: 'Submit',  icon: 'fa-rocket',   active: this.canSubmit,                  done: false },
            ];
        },

        get canSubmit() {
            if (!this.method) return false;
            if (this.method === 'manual' && this.uid.trim().length < 8) return false;
            if (this.method === 'manual' && !/^[A-F0-9]+$/.test(this.uid)) return false;
            return true;
        },

        get remainingSteps() {
            let n = 0;
            if (!this.method) n++;
            if (this.method === 'manual' && this.uid.trim().length < 8) n++;
            return n;
        },

        get checklist() {
            return [
                { label: 'Pilih metode pendaftaran (Otomatis / Manual)', ok: !!this.method, show: true },
                { label: 'Isi UID Kartu (khusus mode Manual)',           ok: this.uid.trim().length >= 8, show: this.method === 'manual' },
            ];
        },
    };
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    @media (hover: none) { button:active { transform: scale(.97); } }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn .25s ease-out; }
</style>
@endpush
@endsection
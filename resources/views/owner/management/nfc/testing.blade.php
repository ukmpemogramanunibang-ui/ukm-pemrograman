@extends('layouts.roles.owner')

@section('title', 'Testing NFC')
@section('page-title', 'Testing NFC Credential')

@section('content')
<div class="space-y-5 sm:space-y-6" x-data="nfcTesting()" x-cloak>

    {{-- ============================================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================================ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 text-white shadow-xl">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl"></div>

        <div class="relative p-5 sm:p-7">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-lg flex-shrink-0">
                        <i class="fas fa-flask text-xl sm:text-2xl"></i>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-amber-400 border-2 border-white dark:border-gray-900 animate-pulse"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-black tracking-tight">NFC Testing Lab</h1>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-400 text-amber-900 font-black uppercase tracking-wider">
                                Test Mode
                            </span>
                        </div>
                        <p class="text-white/75 text-xs sm:text-sm mt-1 max-w-lg">
                            Uji keaslian NFC credential dengan 5 metode validasi. Aman untuk production.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 border border-white/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Reader Ready
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 border border-white/20">
                                <i class="fas fa-shield-alt text-[10px]"></i>
                                Read-only Mode
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 sm:flex-col sm:items-end">
                    <a href="{{ route('owner.management.nfc.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl text-sm hover:bg-white/25 active:scale-95 transition-all duration-150">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                    <a href="{{ route('owner.management.nfc.generate') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-purple-700 rounded-xl text-sm font-bold shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="fas fa-plus-circle"></i>
                        <span>Generate NFC</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- QUICK SAMPLES --}}
    {{-- ============================================================ --}}
    @php
        $sampleNfcs = \App\Models\NfcCredential::active()
            ->with('user:id,name,nim')
            ->inRandomOrder()
            ->take(3)
            ->get();
    @endphp

    @if($sampleNfcs->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-lightbulb text-amber-500"></i>
                    <span class="text-sm font-bold text-gray-800 dark:text-white">Coba dengan sampel</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">— klik untuk mengisi otomatis</span>
                </div>
                <span class="text-xs text-gray-400">{{ $sampleNfcs->count() }} dari database</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($sampleNfcs as $sample)
                    <button type="button"
                            @click="fillUuid('{{ $sample->nfc_uuid }}'); validate()"
                            class="group inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 hover:border-cyan-400 dark:hover:border-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition-all text-xs">
                        <i class="fas fa-wifi text-cyan-500"></i>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 group-hover:text-cyan-700 dark:group-hover:text-cyan-400">
                            {{ $sample->user?->name ?? 'Unknown' }}
                        </span>
                        <span class="font-mono text-[10px] text-gray-400">
                            {{ \Illuminate\Support\Str::limit($sample->nfc_uuid, 8) }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MAIN GRID --}}
    {{-- ============================================================ --}}
    <div class="grid lg:grid-cols-5 gap-5">

        {{-- LEFT: 5-column wide --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- STEP 1: METHOD --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden transition-shadow duration-300"
                 :class="method ? 'ring-2 ring-cyan-500/20' : ''">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition-all duration-300"
                         :class="method ? 'bg-emerald-500 text-white' : 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400'">
                        <i class="fas" :class="method ? 'fa-check' : 'fa-th-large'"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 dark:text-white">Pilih Metode Validasi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">5 metode tersedia — pilih sesuai kebutuhan</p>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3">
                        {{-- Manual --}}
                        <button type="button" @click="setMethod('manual')"
                                :class="method === 'manual'
                                    ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20 ring-2 ring-cyan-500/30 shadow-md'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-cyan-400 hover:shadow-sm'"
                                class="group relative flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl border-2 transition-all duration-200 active:scale-[0.97]">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'manual'
                                    ? 'bg-cyan-500 text-white shadow-md shadow-cyan-500/30'
                                    : 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400'">
                                <i class="fas fa-keyboard text-lg"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold text-gray-700 dark:text-gray-300 text-center leading-tight">Manual</span>
                            <i x-show="method === 'manual'" class="fas fa-check-circle text-cyan-500 text-xs absolute top-2 right-2"></i>
                        </button>

                        {{-- NFC --}}
                        <button type="button" @click="setMethod('nfc')"
                                :class="method === 'nfc'
                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 ring-2 ring-purple-500/30 shadow-md'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-purple-400 hover:shadow-sm'"
                                class="group relative flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl border-2 transition-all duration-200 active:scale-[0.97]">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'nfc'
                                    ? 'bg-purple-500 text-white shadow-md shadow-purple-500/30'
                                    : 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'">
                                <i class="fas fa-wifi text-lg"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold text-gray-700 dark:text-gray-300 text-center leading-tight">NFC Reader</span>
                            <i x-show="method === 'nfc'" class="fas fa-check-circle text-purple-500 text-xs absolute top-2 right-2"></i>
                        </button>

                        {{-- QR --}}
                        <button type="button" @click="setMethod('qr')"
                                :class="method === 'qr'
                                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 ring-2 ring-emerald-500/30 shadow-md'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-emerald-400 hover:shadow-sm'"
                                class="group relative flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl border-2 transition-all duration-200 active:scale-[0.97]">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'qr'
                                    ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30'
                                    : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'">
                                <i class="fas fa-qrcode text-lg"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold text-gray-700 dark:text-gray-300 text-center leading-tight">Scan QR</span>
                            <i x-show="method === 'qr'" class="fas fa-check-circle text-emerald-500 text-xs absolute top-2 right-2"></i>
                        </button>

                        {{-- File --}}
                        <button type="button" @click="setMethod('file')"
                                :class="method === 'file'
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500/30 shadow-md'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-blue-400 hover:shadow-sm'"
                                class="group relative flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl border-2 transition-all duration-200 active:scale-[0.97]">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'file'
                                    ? 'bg-blue-500 text-white shadow-md shadow-blue-500/30'
                                    : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'">
                                <i class="fas fa-file-alt text-lg"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold text-gray-700 dark:text-gray-300 text-center leading-tight">File .txt</span>
                            <i x-show="method === 'file'" class="fas fa-check-circle text-blue-500 text-xs absolute top-2 right-2"></i>
                        </button>

                        {{-- Image --}}
                        <button type="button" @click="setMethod('image')"
                                :class="method === 'image'
                                    ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 ring-2 ring-amber-500/30 shadow-md'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-amber-400 hover:shadow-sm'"
                                class="group relative flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl border-2 transition-all duration-200 active:scale-[0.97]">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                 :class="method === 'image'
                                    ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30'
                                    : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'">
                                <i class="fas fa-image text-lg"></i>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold text-gray-700 dark:text-gray-300 text-center leading-tight">Gambar QR</span>
                            <i x-show="method === 'image'" class="fas fa-check-circle text-amber-500 text-xs absolute top-2 right-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- STEP 2: INPUT --}}
            <div x-show="method !== null" x-transition.duration.300ms
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cyan-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 dark:text-white">
                            <span x-text="`Input — ${methodLabel}`"></span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="methodHint"></p>
                    </div>
                </div>

                <div class="p-5 sm:p-6 space-y-4">

                    {{-- UUID/UID Input --}}
                    <div x-show="['manual', 'nfc', 'qr'].includes(method)" x-transition>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            UUID NFC atau UID Fisik <span class="text-red-500">*</span>
                        </label>

                        <div class="flex items-center gap-1 mb-3 p-1 bg-gray-100 dark:bg-gray-900/40 rounded-lg w-fit">
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded text-gray-500 dark:text-gray-400">
                                Terima:
                            </span>
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-sm">
                                UUID
                            </span>
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 shadow-sm">
                                UID Fisik
                            </span>
                        </div>

                        <div class="relative">
                            <i class="fas fa-fingerprint absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                   x-model="uuid"
                                   @input="uuid = uuid.toUpperCase().trim()"
                                   @keydown.enter.prevent="canValidate && validate()"
                                   placeholder="UUID v4 atau UID kartu (hex)"
                                   autocomplete="off"
                                   spellcheck="false"
                                   class="w-full pl-11 pr-24 py-3.5 rounded-xl border-2 transition-all duration-200
                                          text-gray-800 dark:text-white font-mono tracking-wider text-sm
                                          focus:outline-none focus:ring-2 focus:ring-cyan-500/40
                                          bg-gray-50 dark:bg-gray-900/40"
                                   :class="{
                                       'border-cyan-500': inputType === 'uuid',
                                       'border-emerald-500': inputType === 'physical_uid',
                                       'border-rose-400': inputType === 'invalid',
                                       'border-gray-200 dark:border-gray-600': !inputType
                                   }">

                            <button type="button" @click="pasteFromClipboard()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <i class="fas fa-paste mr-1"></i> Paste
                            </button>
                        </div>

                        <template x-if="inputType === 'uuid'">
                            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-700/50">
                                <i class="fas fa-check-circle text-cyan-500 text-xs"></i>
                                <span class="text-xs font-semibold text-cyan-700 dark:text-cyan-400">
                                    Terdeteksi: <strong>System UUID</strong> (v4)
                                </span>
                            </div>
                        </template>

                        <template x-if="inputType === 'physical_uid'">
                            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50">
                                <i class="fas fa-microchip text-emerald-500 text-xs"></i>
                                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                    Terdeteksi: <strong>UID Fisik Kartu</strong>
                                </span>
                            </div>
                        </template>

                        <template x-if="inputType === 'invalid'">
                            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/50">
                                <i class="fas fa-exclamation-triangle text-rose-500 text-xs"></i>
                                <span class="text-xs font-semibold text-rose-700 dark:text-rose-400">
                                    Format tidak valid — harus UUID v4 atau hex 8-64 karakter
                                </span>
                            </div>
                        </template>

                        <div class="flex items-center justify-between mt-2 text-xs">
                            <span class="text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                UUID v4 (36 char) atau UID fisik kartu (hex 8-14 char)
                            </span>
                            <span class="font-mono font-semibold text-gray-400">
                                <span x-text="uuid.length"></span> char
                            </span>
                        </div>
                    </div>

                    {{-- FILE --}}
                    <div x-show="method === 'file'" x-transition>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Upload File UUID (.txt)
                        </label>
                        <label class="block relative cursor-pointer">
                            <input type="file" accept=".txt,.text,text/plain"
                                   @change="handleFileUpload($event)"
                                   class="hidden">
                            <div class="flex flex-col items-center justify-center gap-3 p-8 rounded-2xl border-2 border-dashed border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-900/10 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                                <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <i class="fas fa-file-upload text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-gray-800 dark:text-white text-sm">
                                        Klik untuk pilih file
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        File .txt berisi UUID NFC (maks. 1 MB)
                                    </div>
                                </div>
                            </div>
                        </label>
                        <div x-show="fileName" x-transition
                             class="mt-3 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span class="text-sm text-emerald-700 dark:text-emerald-400 font-mono truncate" x-text="fileName"></span>
                        </div>
                    </div>

                    {{-- IMAGE --}}
                    <div x-show="method === 'image'" x-transition>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Upload Gambar QR
                        </label>
                        <label class="block relative cursor-pointer">
                            <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp"
                                   @change="handleImageUpload($event)"
                                   class="hidden">
                            <div class="flex flex-col items-center justify-center gap-3 p-8 rounded-2xl border-2 border-dashed border-amber-300 dark:border-amber-700 bg-amber-50/50 dark:bg-amber-900/10 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all">
                                <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                    <i class="fas fa-image text-amber-600 dark:text-amber-400 text-xl"></i>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-gray-800 dark:text-white text-sm">
                                        Klik untuk pilih gambar
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        PNG / JPG / WEBP (maks. 2 MB)
                                    </div>
                                </div>
                            </div>
                        </label>
                        <div x-show="imageName" x-transition
                             class="mt-3 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span class="text-sm text-emerald-700 dark:text-emerald-400 font-mono truncate" x-text="imageName"></span>
                        </div>
                    </div>

                    {{-- NFC waiting --}}
                    <div x-show="method === 'nfc' && !uuid" x-transition
                         class="p-5 rounded-xl bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-200 dark:border-purple-800 flex items-center gap-4">
                        <div class="relative w-12 h-12 flex items-center justify-center flex-shrink-0">
                            <span class="absolute inset-0 rounded-full bg-purple-500/20 animate-ping"></span>
                            <span class="relative w-10 h-10 rounded-full bg-purple-500 text-white flex items-center justify-center">
                                <i class="fas fa-wifi"></i>
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-purple-800 dark:text-purple-300">
                                Menunggu NFC...
                            </p>
                            <p class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">
                                Dekatkan kartu ke belakang HP. Data akan terisi otomatis.
                            </p>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-2">
                        <button type="button"
                                x-show="method === 'nfc'"
                                @click="startNfcScan()"
                                :disabled="nfcScanning"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 disabled:opacity-50 active:scale-[0.98] transition-all">
                            <i class="fas" :class="nfcScanning ? 'fa-circle-notch fa-spin' : 'fa-wifi'"></i>
                            <span x-text="nfcScanning ? 'Scanning...' : 'Mulai Scan NFC'"></span>
                        </button>

                        <button type="button"
                                x-show="method === 'qr'"
                                @click="startQrScan()"
                                :disabled="qrScanning"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 disabled:opacity-50 active:scale-[0.98] transition-all">
                            <i class="fas" :class="qrScanning ? 'fa-circle-notch fa-spin' : 'fa-qrcode'"></i>
                            <span x-text="qrScanning ? 'Scanning...' : 'Buka Kamera'"></span>
                        </button>

                        <button type="button"
                                @click="validate()"
                                :disabled="!canValidate || validating"
                                :class="canValidate && !validating
                                    ? 'bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white cursor-pointer shadow-lg shadow-cyan-500/30 active:scale-[0.98]'
                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-bold transition-all duration-200">
                            <template x-if="validating">
                                <span><i class="fas fa-circle-notch fa-spin mr-1"></i> Memeriksa...</span>
                            </template>
                            <template x-if="!validating">
                                <span><i class="fas fa-shield-alt mr-1"></i> Validasi Sekarang</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            {{-- STEP 3: RESULT --}}
            <div x-show="result !== null" x-transition.duration.400ms
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">

                <div class="px-5 sm:px-6 py-4 border-b flex items-center gap-3"
                     :class="result?.valid
                        ? 'bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border-emerald-200 dark:border-emerald-700/40'
                        : 'bg-gradient-to-r from-rose-50 to-orange-50 dark:from-rose-900/20 dark:to-orange-900/20 border-rose-200 dark:border-rose-700/40'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
                         :class="result?.valid ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'">
                        <i class="fas" :class="result?.valid ? 'fa-check' : 'fa-times'"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 dark:text-white"
                            x-text="result?.valid ? 'NFC Valid' : 'NFC Tidak Valid'"></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2 flex-wrap">
                            <span x-text="result?.checked_at || ''"></span>
                            <template x-if="result?.elapsed_ms">
                                <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700">
                                    <i class="fas fa-bolt text-[8px] text-amber-500"></i>
                                    <span x-text="`${result.elapsed_ms} ms`"></span>
                                </span>
                            </template>
                        </p>
                    </div>
                    <button type="button" @click="clearResult()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-5 sm:p-6">

                    {{-- VALID --}}
                    <template x-if="result?.valid">
                        <div class="space-y-4">
                            {{-- User card --}}
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200 dark:border-emerald-700/40">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-2xl font-black shadow-lg flex-shrink-0">
                                    <span x-text="(result.user?.name || '?').charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-black text-lg text-gray-800 dark:text-white truncate" x-text="result.user?.name"></div>
                                    <div class="text-sm text-gray-500 font-mono" x-text="result.user?.nim || '-'"></div>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 text-xs font-semibold">
                                            <i class="fas fa-layer-group text-[10px]"></i>
                                            <span x-text="result.user?.level || '-'"></span>
                                        </span>
                                        <template x-if="result.user?.jabatan">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-semibold">
                                                <i class="fas fa-briefcase text-[10px]"></i>
                                                <span x-text="result.user.jabatan"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Matched by --}}
                            <template x-if="result.matched_by">
                                <div class="flex items-center gap-2 p-3 rounded-lg border"
                                     :class="result.matched_by === 'uuid'
                                        ? 'bg-cyan-50 dark:bg-cyan-900/20 border-cyan-200 dark:border-cyan-700/40'
                                        : 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700/40'">
                                    <i class="fas text-sm"
                                       :class="result.matched_by === 'uuid'
                                          ? 'fa-fingerprint text-cyan-500'
                                          : 'fa-microchip text-emerald-500'"></i>
                                    <div class="flex-1 text-xs">
                                        <div class="font-bold"
                                             :class="result.matched_by === 'uuid'
                                                ? 'text-cyan-700 dark:text-cyan-400'
                                                : 'text-emerald-700 dark:text-emerald-400'">
                                            <span x-text="result.matched_by === 'uuid'
                                                ? 'Cocok dengan System UUID'
                                                : 'Cocok dengan UID Fisik Kartu'"></span>
                                        </div>
                                        <div class="text-gray-500 mt-0.5">
                                            Input Anda terdeteksi sebagai <strong x-text="result.input_type === 'uuid' ? 'UUID v4' : 'UID Fisik'"></strong>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Detail grid --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
                                    <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Status NFC</div>
                                    <div class="font-bold text-emerald-600 dark:text-emerald-400 text-sm" x-text="result.nfc?.status || 'Aktif'"></div>
                                </div>
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
                                    <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Didaftarkan</div>
                                    <div class="font-bold text-gray-800 dark:text-white text-xs" x-text="result.nfc?.registered_at || '—'"></div>
                                </div>
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
                                    <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Scan Count</div>
                                    <div class="font-bold text-gray-800 dark:text-white text-sm tabular-nums" x-text="result.nfc?.scan_count ?? 0"></div>
                                </div>
                            </div>

                            {{-- Expiry warning --}}
                            <template x-if="result.nfc?.expiry_info">
                                <div class="p-3 rounded-lg border flex items-center gap-2"
                                     :class="result.nfc.expiry_info.is_expired
                                        ? 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-700/40'
                                        : (result.nfc.expiry_info.days_left < 30
                                            ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700/40'
                                            : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700/40')">
                                    <i class="fas fa-clock"
                                       :class="result.nfc.expiry_info.is_expired
                                            ? 'text-rose-500'
                                            : (result.nfc.expiry_info.days_left < 30 ? 'text-amber-500' : 'text-blue-500')"></i>
                                    <div class="flex-1 text-xs">
                                        <div class="font-bold"
                                             :class="result.nfc.expiry_info.is_expired
                                                ? 'text-rose-700 dark:text-rose-400'
                                                : (result.nfc.expiry_info.days_left < 30 ? 'text-amber-700 dark:text-amber-400' : 'text-blue-700 dark:text-blue-400')">
                                            <span x-show="result.nfc.expiry_info.is_expired">NFC Sudah Kedaluwarsa</span>
                                            <span x-show="!result.nfc.expiry_info.is_expired">
                                                Berlaku sampai <span x-text="result.nfc.expiry_info.date"></span>
                                            </span>
                                        </div>
                                        <div class="text-gray-500 mt-0.5" x-show="!result.nfc.expiry_info.is_expired">
                                            <span x-text="`${result.nfc.expiry_info.days_left} hari lagi`"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Identitas dua kolom --}}
                            <div class="grid sm:grid-cols-2 gap-3">
                                <div class="p-3 rounded-lg bg-gray-900 dark:bg-black border border-gray-700">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="text-[10px] uppercase font-bold text-cyan-400 tracking-wider">
                                            <i class="fas fa-fingerprint mr-1"></i> System UUID
                                        </div>
                                        <button type="button" @click="copyToClipboard(result.nfc?.uuid)"
                                                class="text-gray-500 hover:text-cyan-400 transition">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                    <code class="text-cyan-400 text-[11px] font-mono break-all" x-text="result.nfc?.uuid || '—'"></code>
                                </div>

                                <div class="p-3 rounded-lg bg-gray-900 dark:bg-black border border-gray-700">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">
                                            <i class="fas fa-microchip mr-1"></i> UID Fisik Kartu
                                        </div>
                                        <template x-if="result.nfc?.identifier">
                                            <button type="button" @click="copyToClipboard(result.nfc.identifier)"
                                                    class="text-gray-500 hover:text-emerald-400 transition">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </template>
                                    </div>
                                    <code class="text-emerald-400 text-[11px] font-mono break-all"
                                          :class="!result.nfc?.identifier ? 'italic text-amber-400' : ''"
                                          x-text="result.nfc?.identifier || 'Belum di-set'"></code>
                                </div>
                            </div>

                            {{-- PANEL SET UID (muncul kalau UID kosong) — SUDAH DI TEMPAT BENAR --}}
                            <template x-if="!result.nfc?.identifier">
                                <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-2 border-amber-300 dark:border-amber-700/50">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-amber-800 dark:text-amber-300">
                                                NFC ini belum punya UID Fisik
                                            </div>
                                            <div class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                                                Tap kartu NFC tidak akan terbaca. Set UID fisik biar kartu bisa di-tap.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="relative">
                                            <i class="fas fa-microchip absolute left-4 top-1/2 -translate-y-1/2 text-amber-600 dark:text-amber-400"></i>
                                            <input type="text"
                                                   x-model="newUid"
                                                   @input="newUid = newUid.toUpperCase().replace(/[^A-F0-9]/g, '')"
                                                   @keydown.enter.prevent="saveUid()"
                                                   placeholder="Contoh: A7B9A92B"
                                                   maxlength="64"
                                                   class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-amber-300 dark:border-amber-700 bg-white dark:bg-gray-900 font-mono tracking-wider
                                                          focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500">
                                        </div>

                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-amber-600 dark:text-amber-400">
                                                <i class="fas fa-info-circle mr-1"></i> Ketik manual atau tap kartu NFC
                                            </span>
                                            <span class="font-mono font-semibold" :class="newUid.length >= 8 ? 'text-emerald-600' : 'text-amber-600'">
                                                <span x-text="newUid.length"></span>/64
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <button type="button"
                                                    @click="saveUid()"
                                                    :disabled="!canSaveUid || savingUid"
                                                    :class="canSaveUid && !savingUid
                                                        ? 'bg-amber-500 hover:bg-amber-600 text-white active:scale-[0.98]'
                                                        : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'"
                                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition">
                                                <template x-if="savingUid">
                                                    <span><i class="fas fa-circle-notch fa-spin mr-1"></i> Menyimpan...</span>
                                                </template>
                                                <template x-if="!savingUid">
                                                    <span><i class="fas fa-save mr-1"></i> Simpan UID Fisik</span>
                                                </template>
                                            </button>

                                            <button type="button"
                                                    @click="scanUidFromNfc()"
                                                    :disabled="nfcScanning"
                                                    class="px-4 py-2.5 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 active:scale-[0.98] transition">
                                                <i class="fas" :class="nfcScanning ? 'fa-circle-notch fa-spin' : 'fa-wifi'"></i>
                                                <span class="ml-1">Tap Kartu</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Link ke detail --}}
                            <a :href="`{{ route('owner.management.nfc.index') }}?q=${result.user?.nim}`"
                               class="flex items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 hover:border-cyan-400 dark:hover:border-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 transition group">
                                <i class="fas fa-external-link-alt text-cyan-500 group-hover:scale-110 transition"></i>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 group-hover:text-cyan-700 dark:group-hover:text-cyan-400">
                                    Lihat detail di Manajemen NFC
                                </span>
                            </a>
                        </div>
                    </template>

                    {{-- INVALID --}}
                    <template x-if="result && !result.valid">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40">
                                <i class="fas fa-exclamation-triangle text-rose-500 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="font-bold text-rose-800 dark:text-rose-300 mb-1">
                                        <span x-text="result.message || 'NFC tidak valid'"></span>
                                    </div>
                                    <div class="text-xs text-rose-600 dark:text-rose-400">
                                        Input yang diperiksa tidak ditemukan atau sudah tidak aktif.
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-gray-900 dark:bg-black border border-gray-700 flex items-center gap-2">
                                <i class="fas fa-fingerprint text-rose-400 flex-shrink-0"></i>
                                <code class="text-rose-400 text-xs font-mono flex-1 truncate" x-text="result.uuid"></code>
                            </div>

                            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40">
                                <div class="text-xs font-bold uppercase text-amber-700 dark:text-amber-400 mb-2">
                                    <i class="fas fa-lightbulb mr-1"></i> Kemungkinan penyebab
                                </div>
                                <ul class="space-y-1 text-xs text-amber-700 dark:text-amber-300">
                                    <li><i class="fas fa-circle text-[6px] mr-2"></i> NFC belum didaftarkan di sistem</li>
                                    <li><i class="fas fa-circle text-[6px] mr-2"></i> NFC sudah dicabut / tidak aktif</li>
                                    <li><i class="fas fa-circle text-[6px] mr-2"></i> UID terbaca salah (coba scan ulang)</li>
                                    <li><i class="fas fa-circle text-[6px] mr-2"></i> Bukan NFC UNICODE (mungkin QR biasa)</li>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- STATS --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-2">
                    <i class="fas fa-chart-line text-cyan-500"></i>
                    <h3 class="font-bold text-gray-800 dark:text-white text-sm">Statistik NFC</h3>
                </div>
                <div class="p-5 space-y-3">
                    @php
                        $totalNfc      = \App\Models\NfcCredential::count();
                        $activeNfc     = \App\Models\NfcCredential::active()->count();
                        $revokedNfc    = \App\Models\NfcCredential::where('is_revoked', true)->count();
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Total NFC</span>
                        <span class="font-bold text-gray-800 dark:text-white tabular-nums">{{ number_format($totalNfc) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Aktif</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($activeNfc) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Dicabut</span>
                        <span class="font-bold text-rose-600 dark:text-rose-400 tabular-nums">{{ number_format($revokedNfc) }}</span>
                    </div>

                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] uppercase font-bold text-gray-500">Aktif Rate</span>
                            <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400">
                                {{ $totalNfc > 0 ? round(($activeNfc / $totalNfc) * 100) : 0 }}%
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 to-blue-500 transition-all duration-500"
                                 style="width: {{ $totalNfc > 0 ? round(($activeNfc / $totalNfc) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HISTORY --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-history text-purple-500"></i>
                        <h3 class="font-bold text-gray-800 dark:text-white text-sm">Riwayat Sesi Ini</h3>
                        <template x-if="history.length > 0">
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 font-bold tabular-nums"
                                  x-text="history.length"></span>
                        </template>
                    </div>
                    <button type="button" x-show="history.length > 0" @click="history = []"
                            class="text-xs text-gray-400 hover:text-rose-500 transition">
                        Clear
                    </button>
                </div>
                <div class="p-3">
                    <template x-if="history.length === 0">
                        <div class="py-8 text-center">
                            <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-xs text-gray-400">Belum ada validasi</p>
                        </div>
                    </template>
                    <div class="space-y-1.5 max-h-80 overflow-y-auto">
                        <template x-for="(h, i) in history" :key="i">
                            <div class="flex items-center gap-2 p-2.5 rounded-lg transition cursor-pointer hover:scale-[1.01]"
                                 @click="fillUuid(h.uuid); validate()"
                                 :class="h.valid
                                    ? 'bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30'
                                    : 'bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/30'">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0"
                                     :class="h.valid ? 'bg-emerald-500' : 'bg-rose-500'">
                                    <i class="fas text-white text-[10px]"
                                       :class="h.valid ? 'fa-check' : 'fa-times'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-semibold text-gray-800 dark:text-white truncate"
                                         x-text="h.valid ? h.name : 'Tidak valid'"></div>
                                    <div class="text-[10px] text-gray-500 font-mono truncate" x-text="h.uuid?.slice(0, 16) + '…'"></div>
                                </div>
                                <div class="text-[10px] text-gray-400 flex-shrink-0" x-text="h.time"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- GUIDE --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center gap-2">
                    <i class="fas fa-book text-amber-500"></i>
                    <h3 class="font-bold text-gray-800 dark:text-white text-sm">Panduan Singkat</h3>
                </div>
                <div class="p-5 space-y-3 text-xs">
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0">1</div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            <strong class="text-gray-800 dark:text-white">NFC Reader</strong> — paling akurat, butuh Chrome Android + HTTPS.
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0">2</div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            <strong class="text-gray-800 dark:text-white">Scan QR</strong> — pakai kamera belakang.
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0">3</div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            <strong class="text-gray-800 dark:text-white">Manual</strong> — UUID v4 atau UID fisik.
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-[10px] font-bold flex-shrink-0">4</div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            <strong class="text-gray-800 dark:text-white">File / Gambar</strong> — batch testing.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- QR SCANNER MODAL --}}
    {{-- ============================================================ --}}
    <div x-show="qrModalOpen" x-transition.opacity
         class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div x-show="qrModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-qrcode text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-800 dark:text-white">Scan QR Code</h3>
                    <p class="text-xs text-gray-500">Arahkan kamera ke QR NFC</p>
                </div>
                <button type="button" @click="closeQrModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5">
                <div id="qr-reader" class="w-full rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700" style="min-height: 250px;"></div>

                <div class="mt-4 flex items-center gap-2 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs text-emerald-700 dark:text-emerald-300 font-medium"
                          x-text="qrStatus"></span>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex gap-2">
                <button type="button" @click="closeQrModal()"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div x-show="toast.show" x-transition.opacity
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[80] px-4 py-3 rounded-xl bg-gray-900 text-white text-sm shadow-2xl flex items-center max-w-sm">
        <i class="fas mr-2"
           :class="toast.type === 'error' ? 'fa-exclamation-circle text-rose-400' : 'fa-check-circle text-emerald-400'"></i>
        <span x-text="toast.message"></span>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

@push('scripts')
<script>
function nfcTesting() {
    return {
        // STATE
        method: null,
        uuid: '',
        fileName: '',
        imageName: '',
        newUid: '',
        savingUid: false,
        validating: false,
        nfcScanning: false,
        qrScanning: false,
        qrModalOpen: false,
        qrStatus: 'Siap memindai...',
        result: null,
        history: [],
        toast: { show: false, message: '', type: 'success' },
        html5QrCode: null,

        // COMPUTED
        get methodLabel() {
            return {
                manual: 'Ketik Manual',
                nfc: 'Scan NFC',
                qr: 'Scan QR',
                file: 'Upload File',
                image: 'Upload Gambar',
            }[this.method] || '';
        },

        get methodHint() {
            return {
                manual: 'Ketik atau paste UUID / UID fisik langsung.',
                nfc: 'Gunakan reader NFC di HP Android Anda.',
                qr: 'Gunakan kamera untuk memindai QR NFC.',
                file: 'Unggah file .txt berisi UUID.',
                image: 'Unggah gambar QR untuk dibaca.',
            }[this.method] || '';
        },

        get inputType() {
            const v = this.uuid.trim();
            if (!v) return null;
            if (/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i.test(v)) return 'uuid';
            if (/^[A-F0-9]{8,64}$/i.test(v)) return 'physical_uid';
            return 'invalid';
        },

        get canValidate() {
            if (!this.method) return false;
            if (['manual', 'nfc', 'qr'].includes(this.method)) {
                return ['uuid', 'physical_uid'].includes(this.inputType);
            }
            if (this.method === 'file') return this.fileName !== '';
            if (this.method === 'image') return this.imageName !== '';
            return false;
        },

        get canSaveUid() {
            return this.newUid.length >= 8 && /^[A-F0-9]+$/.test(this.newUid);
        },

        // METHODS
        setMethod(m) {
            this.method = m;
            this.result = null;
            this.uuid = '';
            this.fileName = '';
            this.imageName = '';

            if (m === 'qr') {
                this.$nextTick(() => this.startQrScan());
            }
        },

        fillUuid(value) {
            this.uuid = String(value).toUpperCase().trim();
            if (!this.method) this.method = 'manual';
        },

        async pasteFromClipboard() {
            try {
                const text = await navigator.clipboard.readText();
                this.uuid = text.toUpperCase().trim();
                this.showToast('Berhasil di-paste');
            } catch {
                this.showToast('Tidak dapat mengakses clipboard', 'error');
            }
        },

        async handleFileUpload(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            this.fileName = file.name;

            try {
                const formData = new FormData();
                formData.append('file', file);

                const response = await fetch('{{ route('owner.management.nfc.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error('Server menolak file');

                const data = await response.json();
                if (data.uuid) {
                    this.uuid = data.uuid;
                    this.showToast('UUID diekstrak dari file');
                    setTimeout(() => this.validate(), 300);
                } else {
                    this.showToast('File tidak berisi UUID valid', 'error');
                }
            } catch (err) {
                this.showToast('Gagal upload: ' + err.message, 'error');
            }

            event.target.value = '';
        },

        async handleImageUpload(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            this.imageName = file.name;

            try {
                const formData = new FormData();
                formData.append('image', file);

                const response = await fetch('{{ route('owner.management.nfc.upload-image') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error('Server menolak gambar');

                const data = await response.json();
                if (data.uuid) {
                    this.uuid = data.uuid;
                    this.showToast('QR berhasil dibaca');
                    setTimeout(() => this.validate(), 300);
                } else {
                    this.showToast(data.error || 'QR tidak terbaca', 'error');
                }
            } catch (err) {
                this.showToast('Gagal upload: ' + err.message, 'error');
            }

            event.target.value = '';
        },

        async startNfcScan() {
            if (!('NDEFReader' in window)) {
                this.showToast('Browser tidak mendukung Web NFC. Gunakan Chrome Android.', 'error');
                return;
            }

            this.nfcScanning = true;

            try {
                const ndef = new NDEFReader();
                await ndef.scan();
                this.showToast('Dekatkan kartu NFC ke belakang HP...');

                ndef.onreading = (event) => {
                    const decoder = new TextDecoder();
                    let text = '';

                    for (const record of event.message.records) {
                        if (record.data) text += decoder.decode(record.data);
                    }

                    if (text.trim()) {
                        this.uuid = text.trim().toUpperCase();
                        this.nfcScanning = false;
                        this.showToast('NFC berhasil dibaca');
                        setTimeout(() => this.validate(), 300);
                    }
                };

                ndef.onreadingerror = () => {
                    this.nfcScanning = false;
                    this.showToast('Gagal membaca NFC', 'error');
                };
            } catch (err) {
                this.nfcScanning = false;
                this.showToast('Gagal scan NFC: ' + err.message, 'error');
            }
        },

        async startQrScan() {
            if (!navigator.mediaDevices?.getUserMedia) {
                this.showToast('Browser tidak mendukung kamera', 'error');
                return;
            }

            if (!window.isSecureContext) {
                this.showToast('Kamera butuh HTTPS atau localhost', 'error');
                return;
            }

            this.qrModalOpen = true;
            this.qrScanning = true;
            this.qrStatus = 'Memulai kamera...';

            await this.$nextTick();
            await new Promise(r => setTimeout(r, 200));

            try {
                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode('qr-reader');
                }

                await this.html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        this.qrStatus = 'QR terdeteksi!';
                        this.uuid = decodedText.trim().toUpperCase();

                        this.stopQrScan().then(() => {
                            this.qrModalOpen = false;
                            this.showToast('QR berhasil dipindai');
                            setTimeout(() => this.validate(), 300);
                        });
                    },
                    () => {}
                );

                this.qrStatus = 'Arahkan kamera ke QR NFC...';
            } catch (err) {
                this.qrScanning = false;
                this.qrStatus = 'Gagal memulai kamera';
                this.showToast('Gagal buka kamera: ' + err.message, 'error');
            }
        },

        async stopQrScan() {
            this.qrScanning = false;
            if (this.html5QrCode) {
                try {
                    await this.html5QrCode.stop();
                    this.html5QrCode.clear();
                } catch (e) { /* ignore */ }
            }
        },

        async closeQrModal() {
            await this.stopQrScan();
            this.qrModalOpen = false;
        },

        async validate() {
            if (!this.canValidate) return;
            this.validating = true;
            this.result = null;

            try {
                const response = await fetch('{{ route('owner.management.nfc.testing.validate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ nfc_uuid: this.uuid }),
                });

                const data = await response.json();

                data.checked_at = new Date().toLocaleTimeString('id-ID');
                data.uuid = this.uuid;

                this.result = data;

                this.history.unshift({
                    valid: data.valid,
                    name: data.user?.name || '—',
                    uuid: this.uuid,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                });

                if (this.history.length > 20) this.history.pop();

                this.showToast(data.valid ? 'NFC Valid ✓' : 'NFC tidak valid', data.valid ? 'success' : 'error');
            } catch (err) {
                this.result = {
                    valid: false,
                    message: 'Gagal menghubungi server: ' + err.message,
                    uuid: this.uuid,
                    checked_at: new Date().toLocaleTimeString('id-ID'),
                };
                this.showToast('Gagal validasi', 'error');
            } finally {
                this.validating = false;
            }
        },

        clearResult() {
            this.result = null;
            this.newUid = '';
        },

        async saveUid() {
            if (!this.canSaveUid || !this.result?.nfc?.uuid) return;

            this.savingUid = true;

            try {
                const response = await fetch(
                    `/owner/management/nfc/${encodeURIComponent(this.result.nfc.uuid)}/assign-identifier`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ nfc_identifier: this.newUid }),
                    }
                );

                const data = await response.json();

                if (data.success) {
                    this.result.nfc.identifier = data.identifier;
                    this.showToast('UID Fisik berhasil disimpan');
                    this.newUid = '';
                } else {
                    this.showToast(data.message || 'Gagal menyimpan UID', 'error');
                }
            } catch (err) {
                this.showToast('Gagal menyimpan: ' + err.message, 'error');
            } finally {
                this.savingUid = false;
            }
        },

        async scanUidFromNfc() {
            if (!('NDEFReader' in window)) {
                this.showToast('Browser tidak mendukung Web NFC', 'error');
                return;
            }

            this.nfcScanning = true;

            try {
                const ndef = new NDEFReader();
                await ndef.scan();
                this.showToast('Dekatkan kartu NFC...');

                ndef.onreading = (event) => {
                    const decoder = new TextDecoder();
                    let text = '';

                    for (const record of event.message.records) {
                        if (record.data) text += decoder.decode(record.data);
                    }

                    if (text.trim()) {
                        this.newUid = text.trim().toUpperCase().replace(/[^A-F0-9]/g, '');
                        this.nfcScanning = false;
                        this.showToast('UID terbaca dari kartu');
                        setTimeout(() => this.saveUid(), 300);
                    }
                };
            } catch (err) {
                this.nfcScanning = false;
                this.showToast('Gagal scan: ' + err.message, 'error');
            }
        },

        async copyToClipboard(text) {
            if (!text) return;
            try {
                await navigator.clipboard.writeText(text);
                this.showToast('Tersalin ke clipboard');
            } catch {
                this.showToast('Gagal copy', 'error');
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 2500);
        },
    };
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    @media (hover: none) {
        button:active, a:active { transform: scale(.97); }
    }
    #qr-reader video {
        border-radius: 0.75rem;
    }
</style>
@endpush
@endsection
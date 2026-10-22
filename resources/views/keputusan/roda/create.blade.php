@extends('layouts.app')

@section('title', 'Buat Roda Keberuntungan')

@section('content')
<section class="relative min-h-screen overflow-hidden pb-32 pt-6 sm:pb-24 sm:pt-10">

    {{-- DECORATIVE BACKGROUND (theme-aware) --}}
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-40 -left-40 h-[32rem] w-[32rem] rounded-full bg-cyan-500/10 blur-[120px] dark:bg-cyan-500/10"></div>
        <div class="absolute -bottom-40 -right-40 h-[32rem] w-[32rem] rounded-full bg-fuchsia-500/10 blur-[120px] dark:bg-fuchsia-500/10"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- =====================================================
             HEADER
        ====================================================== --}}
        <header class="mb-8 flex flex-col gap-4 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('keputusan.index') }}"
                   class="mb-3 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition hover:text-cyan-600 dark:text-slate-400 dark:hover:text-cyan-300">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Keputusan
                </a>

                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-cyan-600 dark:border-cyan-400/20 dark:bg-cyan-400/5 dark:text-cyan-300">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-cyan-500 dark:bg-cyan-400"></span>
                    Decision Studio
                </div>

                <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                    Buat Roda Keberuntungan
                </h1>

                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-600 dark:text-slate-400">
                    Rancang roda, atur peluang, dan bagikan tautannya.
                    Server akan menentukan hasil — semua perangkat melihat pemenang yang sama.
                </p>
            </div>

            <div class="hidden shrink-0 items-center gap-3 sm:flex">
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-right shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Mode</div>
                    <div class="mt-0.5 text-sm font-black text-cyan-600 dark:text-cyan-300">PUBLIC WHEEL</div>
                </div>
                <button type="submit" form="wheel-form" id="submit-btn-top"
                        class="rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-6 py-3.5 text-sm font-black text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:-translate-y-0.5 hover:shadow-cyan-500/40 disabled:cursor-not-allowed disabled:opacity-40">
                    💾 SIMPAN
                </button>
            </div>
        </header>

        {{-- ERROR ALERT --}}
        @if($errors->any())
            <div class="mb-6 overflow-hidden rounded-3xl border border-red-500/30 bg-red-50 p-5 dark:border-red-400/30 dark:bg-red-500/10 dark:backdrop-blur-xl">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-xl dark:bg-red-500/20">⚠️</div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-black text-red-700 dark:text-red-200">Pengaturan belum dapat disimpan</h2>
                        <ul class="mt-2 space-y-1 text-xs leading-5 text-red-700/80 dark:text-red-100/80">
                            @foreach($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-red-500 dark:bg-red-400"></span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @php
            $seedEntries = old('entries', [
                ['name' => 'Ibni',  'weight' => 60],
                ['name' => 'Fatih', 'weight' => 15],
                ['name' => 'Dimas', 'weight' => 25],
            ]);
        @endphp

        {{-- FORM --}}
        <form method="POST" action="{{ route('keputusan.roda.store') }}"
              enctype="multipart/form-data" id="wheel-form" novalidate>
            @csrf

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px] xl:grid-cols-[minmax(0,1fr)_400px]">

                {{-- LEFT --}}
                <div class="space-y-6">

                    {{-- STEP 01 — IDENTITAS --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl sm:p-7">
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Langkah 01</div>
                                <h2 class="mt-1 text-lg font-black text-slate-900 dark:text-white">Identitas Roda</h2>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Nama yang akan tampil pada halaman publik.</p>
                            </div>
                            <div class="hidden shrink-0 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400 sm:block">
                                120 KARAKTER
                            </div>
                        </div>

                        <label for="title" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Judul Roda</label>
                        <input type="text" id="title" name="title"
                               value="{{ old('title', 'Roda Keberuntungan Tugas 1') }}"
                               maxlength="120" required autocomplete="off"
                               placeholder="Contoh: Roda Keberuntungan Tugas 1"
                               class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-black/30 dark:text-white dark:placeholder:text-slate-600 dark:focus:border-cyan-400/60 dark:focus:bg-black/40">

                        <div class="mt-2 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 dark:text-slate-500">Judul akan disimpan sebagai identifier roda.</span>
                            <span id="title-counter" class="font-mono text-slate-400 dark:text-slate-500">0 / 120</span>
                        </div>
                    </div>

                    {{-- STEP 02 — PESERTA --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl sm:p-7">
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Langkah 02</div>
                                <h2 class="mt-1 text-lg font-black text-slate-900 dark:text-white">Daftar Peserta</h2>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Minimal 2, maksimal 50 peserta.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="btn-shuffle"
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-cyan-500/40 hover:text-cyan-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:border-cyan-400/40 dark:hover:text-cyan-300">
                                    🔀 Acak
                                </button>
                                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs dark:border-white/10 dark:bg-white/5">
                                    <span id="count" class="font-black text-cyan-600 dark:text-cyan-300">0</span>
                                    <span class="text-slate-400 dark:text-slate-500">/ 50</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-2 dark:border-white/5 dark:bg-black/20">
                            <button type="button" id="btn-distribute"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-bold text-slate-600 transition hover:border-cyan-500/40 hover:text-cyan-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:border-cyan-400/40 dark:hover:text-cyan-300">
                                ⚖️ Bagi rata
                            </button>
                            <button type="button" id="btn-normalize"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-bold text-slate-600 transition hover:border-cyan-500/40 hover:text-cyan-600 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:border-cyan-400/40 dark:hover:text-cyan-300">
                                🎯 Normalisasi 100%
                            </button>
                            <button type="button" id="btn-clear"
                                    class="ml-auto rounded-lg border border-red-400/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-red-600 transition hover:bg-red-100 dark:border-red-400/20 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20">
                                🗑️ Kosongkan
                            </button>
                        </div>

                        <div id="entries" class="space-y-2.5"></div>

                        <button type="button" id="add-entry"
                                class="mt-3 flex w-full items-center justify-center gap-2 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3.5 text-sm font-bold text-slate-500 transition hover:border-cyan-500/50 hover:bg-cyan-50 hover:text-cyan-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/15 dark:bg-white/[0.02] dark:text-slate-300 dark:hover:border-cyan-400/50 dark:hover:bg-cyan-400/5 dark:hover:text-cyan-300">
                            <span class="text-base">+</span>
                            <span>Tambah Nama</span>
                        </button>

                        <div id="duplicate-warning" class="mt-3 hidden rounded-xl border border-amber-400/30 bg-amber-50 p-3 text-xs text-amber-700 dark:border-amber-400/20 dark:bg-amber-400/5 dark:text-amber-200">
                            ⚠️ Ada nama duplikat. Sistem tetap mengizinkan, tetapi hasil bisa membingungkan.
                        </div>
                    </div>

                    {{-- STEP 03 — ENGINE --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl sm:p-7">
                        <div class="mb-5">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Langkah 03</div>
                            <h2 class="mt-1 text-lg font-black text-slate-900 dark:text-white">Mesin Pemilihan</h2>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tentukan cara sistem menentukan pemenang.</p>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="selection_mode" value="weighted" class="peer sr-only"
                                       {{ old('selection_mode', 'weighted') === 'weighted' ? 'checked' : '' }}>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition peer-checked:border-cyan-500/60 peer-checked:bg-cyan-50 peer-checked:ring-4 peer-checked:ring-cyan-500/10 dark:border-white/10 dark:bg-white/[0.03] dark:peer-checked:border-cyan-400/60 dark:peer-checked:bg-cyan-400/[0.06] dark:peer-checked:ring-cyan-400/10">
                                    <div class="flex items-start gap-3">
                                        <div class="text-2xl">⚖️</div>
                                        <div>
                                            <div class="text-sm font-black text-slate-900 dark:text-white">Bobot Peluang</div>
                                            <div class="mt-1 text-[11px] leading-5 text-slate-500 dark:text-slate-500">Anda menentukan persentase tiap peserta. Total harus 100%.</div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="selection_mode" value="secret_random" class="peer sr-only"
                                       {{ old('selection_mode') === 'secret_random' ? 'checked' : '' }}>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition peer-checked:border-fuchsia-500/60 peer-checked:bg-fuchsia-50 peer-checked:ring-4 peer-checked:ring-fuchsia-500/10 dark:border-white/10 dark:bg-white/[0.03] dark:peer-checked:border-fuchsia-400/60 dark:peer-checked:bg-fuchsia-400/[0.06] dark:peer-checked:ring-fuchsia-400/10">
                                    <div class="flex items-start gap-3">
                                        <div class="text-2xl">🎲</div>
                                        <div>
                                            <div class="text-sm font-black text-slate-900 dark:text-white">Acak Rahasia</div>
                                            <div class="mt-1 text-[11px] leading-5 text-slate-500 dark:text-slate-500">Bobot tidak ditampilkan. Server menentukan hasil secara rahasia.</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div id="weighted-panel" class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-black/20">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-black text-slate-900 dark:text-white">Status Bobot</div>
                                    <div class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-500">Total harus tepat 100%.</div>
                                </div>
                                <div id="weight-status" class="rounded-full border border-amber-400/30 bg-amber-50 px-3 py-1 text-xs font-black text-amber-700 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-300">
                                    Total 0%
                                </div>
                            </div>
                            <div id="weight-bar" class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200 dark:bg-white/5">
                                <div id="weight-bar-fill" class="h-full w-0 bg-gradient-to-r from-cyan-400 to-emerald-400 transition-all duration-300"></div>
                            </div>
                            <div class="mt-4 rounded-xl border border-amber-400/20 bg-amber-50 p-3 text-[11px] leading-5 text-slate-600 dark:border-amber-400/10 dark:bg-amber-400/[0.03] dark:text-slate-400">
                                <strong class="text-amber-700 dark:text-amber-300">Aturan 100%:</strong>
                                jika satu peserta diberi 100%, peserta lain otomatis 0% dan terkunci.
                            </div>
                        </div>

                        <div id="random-panel" class="mt-5 hidden rounded-2xl border border-fuchsia-500/30 bg-fuchsia-50 p-4 dark:border-fuchsia-400/20 dark:bg-fuchsia-400/[0.05]">
                            <div class="flex items-start gap-3">
                                <div class="text-2xl">🎲</div>
                                <div>
                                    <div class="text-sm font-black text-fuchsia-700 dark:text-fuchsia-200">Acak Rahasia Aktif</div>
                                    <p class="mt-1 text-[11px] leading-5 text-slate-600 dark:text-slate-400">
                                        Bobot internal tidak akan pernah dikirim ke browser.
                                        Server menentukan pemenang saat tombol PUTAR ditekan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="duration" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Durasi Putaran</label>
                                <select id="duration" name="duration" required
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:focus:border-cyan-400/60">
                                    @foreach([10, 30, 60, 120, 150] as $duration)
                                        <option value="{{ $duration }}" {{ (string) old('duration', 60) === (string) $duration ? 'selected' : '' }}>
                                            {{ $duration }} detik
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="result_mode" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Mode Hasil</label>
                                <select id="result_mode" name="result_mode" required
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:focus:border-cyan-400/60">
                                    <option value="single"  {{ old('result_mode', 'single') === 'single'  ? 'selected' : '' }}>1 Terpilih</option>
                                    <option value="multi"   {{ old('result_mode') === 'multi'   ? 'selected' : '' }}>Beberapa Terpilih</option>
                                    <option value="ranking" {{ old('result_mode') === 'ranking' ? 'selected' : '' }}>Urutan Lengkap</option>
                                </select>
                            </div>
                        </div>

                        <div id="winner-count-panel" class="mt-4">
                            <label for="winner_count" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Jumlah Pemenang</label>
                            <input type="number" id="winner_count" name="winner_count"
                                   value="{{ old('winner_count', 1) }}" min="1" max="50"
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-black/30 dark:text-white dark:focus:border-cyan-400/60">
                            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-500">Hanya berlaku untuk mode "Beberapa Terpilih".</p>
                        </div>
                    </div>

                    {{-- STEP 04 — HASIL --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl sm:p-7">
                        <div class="mb-5">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Langkah 04</div>
                            <h2 class="mt-1 text-lg font-black text-slate-900 dark:text-white">Tampilan Hasil</h2>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Atur tulisan yang muncul setelah roda berhenti.</p>
                        </div>

                        <label for="result_template" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Template Tulisan</label>
                        <input type="text" id="result_template" name="result_template"
                               value="{{ old('result_template', 'Sukses kepada {name}!') }}"
                               maxlength="160"
                               class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-cyan-400 focus:bg-white focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-black/30 dark:text-white dark:placeholder:text-slate-600 dark:focus:border-cyan-400/60"
                               placeholder="Sukses kepada {name}!">

                        <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-500 dark:text-slate-500">
                            Gunakan
                            <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-cyan-600 dark:bg-white/5 dark:text-cyan-300">{name}</code>
                            sebagai placeholder.
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <button type="button" class="preset-template text-cyan-600 hover:underline dark:text-cyan-300" data-template="Sukses kepada {name}!">Sukses kepada {name}!</button>
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <button type="button" class="preset-template text-cyan-600 hover:underline dark:text-cyan-300" data-template="🎉 Pemenang: {name}">🎉 Pemenang: {name}</button>
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <button type="button" class="preset-template text-cyan-600 hover:underline dark:text-cyan-300" data-template="Selamat {name}!">Selamat {name}!</button>
                        </div>

                        {{-- Preview Hasil — tetap dark supaya konsisten dengan roda asli --}}
                        <div class="mt-5 overflow-hidden rounded-3xl border border-cyan-500/20 bg-gradient-to-br from-cyan-50 via-white to-fuchsia-50 p-5 dark:border-cyan-400/20 dark:from-cyan-400/[0.06] dark:via-white/[0.02] dark:to-fuchsia-400/[0.05]">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Preview Hasil</span>
                                <span class="flex items-center gap-1.5 rounded-full bg-white/80 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-slate-400">
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                    Live
                                </span>
                            </div>
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 text-center dark:border-white/10 dark:bg-black/30">
                                <div class="text-[10px] uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500">Hasil Putaran</div>
                                <div id="result-preview" class="mt-3 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                                    Sukses kepada Ibni!
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 05 — SUARA --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl sm:p-7">
                        <div class="mb-5">
                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Langkah 05</div>
                            <h2 class="mt-1 text-lg font-black text-slate-900 dark:text-white">Efek Suara</h2>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pilih efek bawaan atau unggah audio Anda sendiri.</p>
                        </div>

                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="sound_mode" value="system" class="peer sr-only"
                                       {{ old('sound_mode', 'system') === 'system' ? 'checked' : '' }}>
                                <div class="h-full rounded-2xl border border-slate-200 bg-slate-50 p-4 transition peer-checked:border-cyan-500/60 peer-checked:bg-cyan-50 peer-checked:ring-4 peer-checked:ring-cyan-500/10 dark:border-white/10 dark:bg-white/[0.03] dark:peer-checked:border-cyan-400/60 dark:peer-checked:bg-cyan-400/[0.06] dark:peer-checked:ring-cyan-400/10">
                                    <div class="text-2xl">🔊</div>
                                    <div class="mt-2 text-sm font-black text-slate-900 dark:text-white">Suara Sistem</div>
                                    <div class="mt-1 text-[11px] leading-5 text-slate-500 dark:text-slate-500">Efek tick + fanfare dari Web Audio API.</div>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="sound_mode" value="upload" class="peer sr-only"
                                       {{ old('sound_mode') === 'upload' ? 'checked' : '' }}>
                                <div class="h-full rounded-2xl border border-slate-200 bg-slate-50 p-4 transition peer-checked:border-fuchsia-500/60 peer-checked:bg-fuchsia-50 peer-checked:ring-4 peer-checked:ring-fuchsia-500/10 dark:border-white/10 dark:bg-white/[0.03] dark:peer-checked:border-fuchsia-400/60 dark:peer-checked:bg-fuchsia-400/[0.06] dark:peer-checked:ring-fuchsia-400/10">
                                    <div class="text-2xl">🎵</div>
                                    <div class="mt-2 text-sm font-black text-slate-900 dark:text-white">Audio Sendiri</div>
                                    <div class="mt-1 text-[11px] leading-5 text-slate-500 dark:text-slate-500">Unggah MP3/WAV/OGG Anda.</div>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="sound_mode" value="none" class="peer sr-only"
                                       {{ old('sound_mode') === 'none' ? 'checked' : '' }}>
                                <div class="h-full rounded-2xl border border-slate-200 bg-slate-50 p-4 transition peer-checked:border-slate-400/60 peer-checked:bg-slate-100 peer-checked:ring-4 peer-checked:ring-slate-500/10 dark:border-white/10 dark:bg-white/[0.03] dark:peer-checked:border-slate-400/40 dark:peer-checked:bg-white/[0.06] dark:peer-checked:ring-white/5">
                                    <div class="text-2xl">🔇</div>
                                    <div class="mt-2 text-sm font-black text-slate-900 dark:text-white">Tanpa Suara</div>
                                    <div class="mt-1 text-[11px] leading-5 text-slate-500 dark:text-slate-500">Semua audio dinonaktifkan.</div>
                                </div>
                            </label>
                        </div>

                        <div id="system-sound-panel" class="mt-5 rounded-2xl border border-cyan-500/20 bg-cyan-50 p-4 dark:border-cyan-400/15 dark:bg-cyan-400/[0.03]">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="text-sm font-black text-slate-900 dark:text-white">🔊 Tes Suara Sistem</div>
                                    <div class="mt-0.5 text-[11px] leading-5 text-slate-500 dark:text-slate-500">Pastikan browser Anda mengizinkan audio sebelum publikasi.</div>
                                </div>
                                <button type="button" id="test-system-sound"
                                        class="shrink-0 rounded-xl border border-cyan-500/30 bg-cyan-100 px-4 py-2.5 text-xs font-black text-cyan-700 transition hover:bg-cyan-200 dark:border-cyan-400/20 dark:bg-cyan-400/10 dark:text-cyan-300 dark:hover:bg-cyan-400/20">
                                    ▶ TES SUARA
                                </button>
                            </div>
                        </div>

                        <div id="upload-sound-panel" class="mt-5 hidden rounded-2xl border border-fuchsia-500/20 bg-fuchsia-50 p-4 dark:border-fuchsia-400/15 dark:bg-fuchsia-400/[0.03]">
                            <label for="sound_file" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">File Audio</label>
                            <input type="file" id="sound_file" name="sound_file"
                                   accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg"
                                   class="block w-full rounded-2xl border border-slate-200 bg-white p-3 text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-fuchsia-100 file:px-4 file:py-2 file:text-xs file:font-black file:text-fuchsia-700 hover:file:bg-fuchsia-200 dark:border-white/10 dark:bg-black/30 dark:text-slate-300 dark:file:bg-fuchsia-500/20 dark:file:text-fuchsia-200 dark:hover:file:bg-fuchsia-500/30">
                            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-500">Format: MP3, WAV, OGG. Maksimum 5 MB (validasi server).</p>
                        </div>
                    </div>

                    {{-- FINAL SUBMIT --}}
                    <div class="rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-cyan-50 via-white to-blue-50 p-5 shadow-sm dark:border-cyan-400/20 dark:from-cyan-400/[0.08] dark:via-white/[0.02] dark:to-blue-500/[0.06] dark:shadow-none dark:backdrop-blur-xl sm:p-7">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-white">Siap dipublikasikan?</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-400">
                                    Roda akan tersimpan dan Anda akan diarahkan ke halaman publiknya secara otomatis.
                                </p>
                            </div>
                            <button type="submit" id="submit-btn-bottom"
                                    class="shrink-0 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-8 py-4 text-sm font-black text-slate-950 shadow-lg shadow-cyan-500/30 transition hover:-translate-y-0.5 hover:shadow-cyan-500/50 disabled:cursor-not-allowed disabled:opacity-40">
                                💾 SIMPAN &amp; BUKA LINK
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT — SIDEBAR --}}
                <aside class="space-y-5 lg:sticky lg:top-6 lg:self-start">

                    {{-- LIVE WHEEL PREVIEW --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Preview</div>
                                <h3 class="mt-1 text-sm font-black text-slate-900 dark:text-white">Bentuk Roda</h3>
                            </div>
                            <div class="text-2xl">🎡</div>
                        </div>

                        <div class="relative mx-auto aspect-square w-full max-w-[280px]">
                            <div class="absolute left-1/2 top-0 z-10 -translate-x-1/2 -translate-y-1 text-xl text-slate-900 drop-shadow dark:text-white">▼</div>
                            <canvas id="preview-canvas" width="560" height="560"
                                    class="h-full w-full rounded-full ring-1 ring-slate-200 dark:ring-white/10"></canvas>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-[11px]">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-2.5 dark:border-white/5 dark:bg-black/20">
                                <div class="text-slate-500 dark:text-slate-500">Peserta</div>
                                <div id="pv-count" class="mt-0.5 font-black text-slate-900 dark:text-white">0</div>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-2.5 dark:border-white/5 dark:bg-black/20">
                                <div class="text-slate-500 dark:text-slate-500">Mode</div>
                                <div id="pv-mode" class="mt-0.5 font-black text-cyan-600 dark:text-cyan-300">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- SUMMARY --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">Ringkasan</div>
                                <h3 class="mt-1 text-sm font-black text-slate-900 dark:text-white">Konfigurasi Final</h3>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/5 dark:bg-black/20">
                                <span class="text-slate-500 dark:text-slate-500">Judul</span>
                                <span id="summary-title" class="ml-3 truncate font-bold text-slate-900 dark:text-white">—</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/5 dark:bg-black/20">
                                <span class="text-slate-500 dark:text-slate-500">Peserta</span>
                                <span id="summary-count" class="font-black text-slate-900 dark:text-white">0</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/5 dark:bg-black/20">
                                <span class="text-slate-500 dark:text-slate-500">Durasi</span>
                                <span id="summary-duration" class="font-black text-slate-900 dark:text-white">60s</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/5 dark:bg-black/20">
                                <span class="text-slate-500 dark:text-slate-500">Sistem</span>
                                <span id="summary-mode" class="font-bold text-cyan-600 dark:text-cyan-300">—</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/5 dark:bg-black/20">
                                <span class="text-slate-500 dark:text-slate-500">Hasil</span>
                                <span id="summary-result" class="font-bold text-slate-900 dark:text-white">—</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/5 dark:bg-black/20">
                                <span class="text-slate-500 dark:text-slate-500">Audio</span>
                                <span id="summary-sound" class="font-bold text-slate-900 dark:text-white">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- VALIDATION --}}
                    <div id="validation-card" class="rounded-3xl border border-emerald-500/30 bg-emerald-50 p-5 dark:border-emerald-400/20 dark:bg-emerald-400/[0.05]">
                        <div class="flex items-start gap-3">
                            <div id="validation-icon" class="text-lg">✅</div>
                            <div class="min-w-0 flex-1">
                                <div id="validation-title" class="text-sm font-black text-emerald-700 dark:text-emerald-300">Siap disimpan</div>
                                <p id="validation-message" class="mt-1 text-[11px] leading-5 text-slate-600 dark:text-slate-400">
                                    Konfigurasi akan diverifikasi kembali oleh server.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- SUBMIT (SIDEBAR) --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[0.03] dark:shadow-none dark:backdrop-blur-xl">
                        <button type="submit" id="submit-btn"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-5 py-4 text-sm font-black text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:-translate-y-0.5 hover:shadow-cyan-500/40 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0">
                            <span>💾</span>
                            <span>SIMPAN &amp; BUAT LINK</span>
                        </button>
                        <p class="mt-3 text-center text-[11px] leading-5 text-slate-500 dark:text-slate-500">
                            Setelah tersimpan, roda mendapatkan tautan publik dengan token unik.
                        </p>
                    </div>
                </aside>
            </div>
        </form>
    </div>

    {{-- FLOATING ACTION BAR --}}
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur-xl dark:border-white/10 dark:bg-[#0a1424]/95">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <div class="hidden min-w-0 flex-1 sm:block">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Konfigurasi</div>
                <div id="fab-summary" class="mt-0.5 truncate text-sm font-black text-slate-900 dark:text-white">
                    Roda Keberuntungan Tugas 1
                </div>
            </div>

            <div id="fab-validation" class="flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-300">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                <span class="hidden sm:inline">Siap disimpan</span>
                <span class="sm:hidden">OK</span>
            </div>

            <button type="submit" form="wheel-form" id="submit-btn-fab"
                    class="group flex shrink-0 items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-5 py-3 text-sm font-black text-slate-950 shadow-lg shadow-cyan-500/30 transition hover:-translate-y-0.5 hover:shadow-cyan-500/50 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span id="fab-btn-label">SIMPAN &amp; BUKA</span>
            </button>
        </div>
    </div>
</section>

<style>
    .entry-row { animation: entryIn .18s ease-out; }
    @keyframes entryIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    #preview-canvas { transition: transform .3s ease; }
    #wheel-form.loading { pointer-events: none; opacity: .85; }
    #wheel-form.loading::after {
        content: ''; position: fixed; inset: 0;
        background: rgba(5,11,24,.4);
        backdrop-filter: blur(2px);
        z-index: 100; cursor: wait;
    }
    .spinner {
        width: 14px; height: 14px;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spin .6s linear infinite;
        display: inline-block;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ============================================================
       CONSTANTS
    ============================================================ */
    const MAX_ENTRIES = 50;
    const COLORS = [
        '#00d9ff', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981',
        '#3b82f6', '#ef4444', '#06b6d4', '#a855f7', '#14b8a6',
        '#f97316', '#84cc16', '#e11d48', '#6366f1', '#0891b2',
    ];

    /* ============================================================
       DOM
    ============================================================ */
    const $ = (id) => document.getElementById(id);

    const form             = $('wheel-form');
    const entriesContainer = $('entries');
    const addBtn           = $('add-entry');
    const countEl          = $('count');
    const duplicateWarn    = $('duplicate-warning');
    const titleInput       = $('title');
    const titleCounter     = $('title-counter');
    const weightedPanel    = $('weighted-panel');
    const randomPanel      = $('random-panel');
    const weightStatus     = $('weight-status');
    const weightBarFill    = $('weight-bar-fill');
    const winnerCountPanel = $('winner-count-panel');
    const winnerCountInput = $('winner_count');
    const resultModeSel    = $('result_mode');
    const durationSel      = $('duration');
    const resultTemplateInp= $('result_template');
    const resultPreview    = $('result-preview');
    const systemSoundPanel = $('system-sound-panel');
    const uploadSoundPanel = $('upload-sound-panel');
    const testSoundBtn     = $('test-system-sound');
    const summaryTitle     = $('summary-title');
    const summaryCount     = $('summary-count');
    const summaryDuration  = $('summary-duration');
    const summaryMode      = $('summary-mode');
    const summaryResult    = $('summary-result');
    const summarySound     = $('summary-sound');
    const pvCount          = $('pv-count');
    const pvMode           = $('pv-mode');
    const previewCanvas    = $('preview-canvas');
    const previewCtx       = previewCanvas.getContext('2d');
    const validationCard   = $('validation-card');
    const validationIcon   = $('validation-icon');
    const validationTitle  = $('validation-title');
    const validationMsg    = $('validation-message');
    const submitBtn        = $('submit-btn');
    const submitBtnTop     = $('submit-btn-top');
    const submitBtnBottom  = $('submit-btn-bottom');
    const submitBtnFab     = $('submit-btn-fab');
    const fabSummary       = $('fab-summary');
    const fabValidation    = $('fab-validation');
    const fabBtnLabel      = $('fab-btn-label');

    const allSubmitBtns = [submitBtn, submitBtnTop, submitBtnBottom, submitBtnFab].filter(Boolean);

    /* ============================================================
       HELPERS
    ============================================================ */
    const getRadio = (name) => document.querySelector(`input[name="${name}"]:checked`)?.value;
    const getRows = () => [...entriesContainer.querySelectorAll('.entry-row')];
    const getNames = () => getRows().map(r => r.querySelector('.name-input').value.trim()).filter(Boolean);
    const getTotalWeight = () => getRows().reduce((sum, r) => sum + Number(r.querySelector('.weight-input').value || 0), 0);
    const hasDuplicateNames = () => {
        const names = getNames().map(n => n.toLowerCase());
        return new Set(names).size !== names.length;
    };
    const getSelectionMode = () => getRadio('selection_mode') || 'weighted';
    const getSoundMode     = () => getRadio('sound_mode') || 'system';
    const isDarkMode = () => document.documentElement.classList.contains('dark');
    const escapeHtml = (str) => String(str || '').replace(/[<>"'&]/g, '');

    /* ============================================================
       WEB AUDIO
    ============================================================ */
    let audioCtx = null;
    function getAudioContext() {
        if (!audioCtx) {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return null;
            audioCtx = new Ctx();
        }
        return audioCtx;
    }
    async function unlockAudio() {
        const ctx = getAudioContext();
        if (!ctx) return null;
        if (ctx.state === 'suspended') await ctx.resume();
        return ctx;
    }
    function beep(ctx, freq, start, dur, vol = 0.05, type = 'sine') {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, start);
        gain.gain.setValueAtTime(0.0001, start);
        gain.gain.exponentialRampToValueAtTime(vol, start + 0.015);
        gain.gain.exponentialRampToValueAtTime(0.0001, start + dur);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(start);
        osc.stop(start + dur + 0.03);
    }
    async function playFinishSound() {
        const ctx = await unlockAudio();
        if (!ctx) return;
        const t = ctx.currentTime;
        beep(ctx, 523.25, t,        0.15, 0.05, 'sine');
        beep(ctx, 659.25, t + 0.14, 0.15, 0.05, 'sine');
        beep(ctx, 783.99, t + 0.28, 0.22, 0.06, 'sine');
        beep(ctx, 1046.5, t + 0.52, 0.40, 0.05, 'triangle');
    }
    testSoundBtn?.addEventListener('click', async () => {
        testSoundBtn.disabled = true;
        const original = testSoundBtn.textContent;
        testSoundBtn.textContent = '🔊 Memutar...';
        await playFinishSound();
        setTimeout(() => {
            testSoundBtn.disabled = false;
            testSoundBtn.textContent = original;
        }, 1100);
    });

    /* ============================================================
       ENTRY MANAGEMENT
    ============================================================ */
    function createEntryRow(name = '', weight = 0) {
        if (getRows().length >= MAX_ENTRIES) return;

        const row = document.createElement('div');
        row.className = 'entry-row group flex flex-col gap-2.5 rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-black/20 sm:flex-row sm:items-center';

        row.innerHTML = `
            <div class="flex items-center gap-2">
                <div data-entry-number class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white font-mono text-[11px] font-black text-slate-500 ring-1 ring-slate-200 dark:bg-white/5 dark:text-slate-500 dark:ring-0">01</div>
            </div>
            <div class="min-w-0 flex-1">
                <input type="text" class="name-input w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:placeholder:text-slate-600 dark:focus:border-cyan-400/60"
                       placeholder="Nama peserta" maxlength="100" required value="${escapeHtml(name)}">
            </div>
            <div class="weight-wrap w-full sm:w-24">
                <div class="relative">
                    <input type="number" class="weight-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 pr-7 text-center text-sm font-bold text-slate-900 outline-none focus:border-cyan-400 focus:ring-4 focus:ring-cyan-400/10 dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:focus:border-cyan-400/60"
                           placeholder="0" min="0" max="100" step="1" value="${Number(weight ?? 0)}">
                    <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] font-black text-slate-400 dark:text-slate-500">%</span>
                </div>
            </div>
            <button type="button" class="remove-entry flex h-10 w-full shrink-0 items-center justify-center rounded-xl border border-red-400/30 bg-red-50 px-4 text-red-600 transition hover:bg-red-100 dark:border-red-400/10 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20 sm:w-10 sm:px-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

        row.querySelector('.remove-entry').addEventListener('click', () => { row.remove(); render(); });
        row.querySelector('.name-input').addEventListener('input', render);
        row.querySelector('.weight-input').addEventListener('input', onWeightInput);

        entriesContainer.appendChild(row);
        render();
    }

    function onWeightInput(e) {
        let v = Number(e.target.value || 0);
        if (v < 0) v = 0;
        if (v > 100) v = 100;
        if (String(v) !== e.target.value) e.target.value = v;
        if (Number(e.target.value) === 100) {
            getRows().forEach(r => {
                const inp = r.querySelector('.weight-input');
                if (inp !== e.target) inp.value = 0;
            });
        }
        render();
    }

    function reindexEntries() {
        getRows().forEach((row, i) => {
            row.querySelector('[data-entry-number]').textContent = String(i + 1).padStart(2, '0');
            row.querySelector('.name-input').name   = `entries[${i}][name]`;
            row.querySelector('.weight-input').name = `entries[${i}][weight]`;
        });
    }

    /* ============================================================
       WEIGHT TOOLS
    ============================================================ */
    $('btn-distribute')?.addEventListener('click', () => {
        const rows = getRows();
        if (!rows.length) return;
        const base = Math.floor(100 / rows.length);
        let remainder = 100 - base * rows.length;
        rows.forEach((r, i) => {
            r.querySelector('.weight-input').value = String(base + (i < remainder ? 1 : 0));
        });
        render();
    });

    $('btn-normalize')?.addEventListener('click', () => {
        const rows = getRows();
        if (!rows.length) return;
        const total = getTotalWeight();
        if (total === 0) { $('btn-distribute').click(); return; }
        let allocated = 0;
        rows.forEach((r, i) => {
            const inp = r.querySelector('.weight-input');
            const curr = Number(inp.value || 0);
            let next;
            if (i === rows.length - 1) next = Math.max(0, 100 - allocated);
            else { next = Math.round((curr / total) * 100); allocated += next; }
            inp.value = next;
        });
        render();
    });

    $('btn-clear')?.addEventListener('click', () => {
        if (!confirm('Kosongkan semua peserta?')) return;
        entriesContainer.innerHTML = '';
        createEntryRow('', 0);
    });

    $('btn-shuffle')?.addEventListener('click', () => {
        const rows = getRows();
        if (rows.length < 2) return;
        const data = rows.map(r => ({
            name: r.querySelector('.name-input').value,
            weight: r.querySelector('.weight-input').value,
        }));
        for (let i = data.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [data[i], data[j]] = [data[j], data[i]];
        }
        entriesContainer.innerHTML = '';
        data.forEach(d => createEntryRow(d.name, d.weight));
    });

    /* ============================================================
       TEMPLATE PRESETS
    ============================================================ */
    document.querySelectorAll('.preset-template').forEach(btn => {
        btn.addEventListener('click', () => {
            resultTemplateInp.value = btn.dataset.template;
            render();
        });
    });

    /* ============================================================
       WHEEL PREVIEW — theme-aware background
    ============================================================ */
    function drawPreview() {
        const size = previewCanvas.width;
        const r = size / 2, cx = r, cy = r;
        previewCtx.clearRect(0, 0, size, size);

        const dark = isDarkMode();
        const emptyBg   = dark ? '#0a1424' : '#f1f5f9';
        const emptyText = dark ? '#334155' : '#94a3b8';
        const hubBg     = dark ? '#050B18' : '#ffffff';
        const hubBorder = dark ? '#00d9ff' : '#06b6d4';
        const ringOuter = dark ? 'rgba(0,217,255,0.6)' : 'rgba(6,182,212,0.7)';
        const dotColor  = dark ? 'rgba(0,217,255,0.6)' : 'rgba(6,182,212,0.6)';
        const nameShadow = dark ? 'rgba(0,0,0,0.5)' : 'rgba(255,255,255,0.6)';

        const entries = getRows().map(r => ({
            name: r.querySelector('.name-input').value.trim() || '—',
            weight: Number(r.querySelector('.weight-input').value || 0),
        }));

        if (entries.length === 0) {
            previewCtx.beginPath();
            previewCtx.arc(cx, cy, r - 10, 0, Math.PI * 2);
            previewCtx.fillStyle = emptyBg;
            previewCtx.fill();
            previewCtx.fillStyle = emptyText;
            previewCtx.font = 'bold 22px system-ui';
            previewCtx.textAlign = 'center';
            previewCtx.textBaseline = 'middle';
            previewCtx.fillText('Belum ada peserta', cx, cy);
            return;
        }

        const mode = getSelectionMode();
        const totalWeight = mode === 'weighted'
            ? (entries.reduce((s, e) => s + e.weight, 0) || entries.length)
            : entries.length;

        let acc = 0;
        entries.forEach((entry, i) => {
            const seg = mode === 'weighted'
                ? (entry.weight / totalWeight) * Math.PI * 2
                : (Math.PI * 2) / entries.length;

            if (seg <= 0) return;

            previewCtx.beginPath();
            previewCtx.moveTo(cx, cy);
            previewCtx.arc(cx, cy, r - 12, acc, acc + seg);
            previewCtx.closePath();
            previewCtx.fillStyle = COLORS[i % COLORS.length];
            previewCtx.fill();
            previewCtx.strokeStyle = 'rgba(255,255,255,0.15)';
            previewCtx.lineWidth = 2;
            previewCtx.stroke();

            if (seg > 0.15) {
                previewCtx.save();
                previewCtx.translate(cx, cy);
                previewCtx.rotate(acc + seg / 2);
                previewCtx.textAlign = 'right';
                previewCtx.textBaseline = 'middle';
                previewCtx.fillStyle = '#fff';
                previewCtx.font = 'bold 18px system-ui';
                previewCtx.shadowColor = nameShadow;
                previewCtx.shadowBlur = 3;
                previewCtx.fillText(entry.name.slice(0, 14), r - 28, 0);
                previewCtx.restore();
            }
            acc += seg;
        });

        previewCtx.beginPath();
        previewCtx.arc(cx, cy, r - 6, 0, Math.PI * 2);
        previewCtx.strokeStyle = ringOuter;
        previewCtx.lineWidth = 5;
        previewCtx.stroke();

        previewCtx.beginPath();
        previewCtx.arc(cx, cy, 26, 0, Math.PI * 2);
        previewCtx.fillStyle = hubBg;
        previewCtx.fill();
        previewCtx.strokeStyle = hubBorder;
        previewCtx.lineWidth = 3;
        previewCtx.stroke();
    }

    /* ============================================================
       VALIDATION
    ============================================================ */
    function validate() {
        const rows = getRows();
        const mode = getSelectionMode();
        const resultMode = resultModeSel.value;

        let valid = true;
        let title = 'Siap disimpan';
        let message = 'Konfigurasi akan diverifikasi kembali oleh server.';
        let icon = '✅';
        let theme = 'emerald';

        if (!titleInput.value.trim()) {
            valid = false; title = 'Judul belum diisi';
            message = 'Isi judul roda terlebih dahulu.';
            icon = '⚠️'; theme = 'red';
        } else if (rows.length < 2) {
            valid = false; title = 'Peserta kurang';
            message = 'Tambahkan minimal 2 peserta.';
            icon = '⚠️'; theme = 'red';
        } else if (rows.some(r => !r.querySelector('.name-input').value.trim())) {
            valid = false; title = 'Nama kosong';
            message = 'Semua baris peserta harus memiliki nama.';
            icon = '⚠️'; theme = 'red';
        } else if (mode === 'weighted') {
            const total = getTotalWeight();
            const has100 = rows.some(r => Number(r.querySelector('.weight-input').value) === 100);

            if (total !== 100) {
                valid = false; title = 'Bobot belum 100%';
                message = `Total bobot saat ini ${total}%. Harus tepat 100%.`;
                icon = '⚠️'; theme = 'red';
            } else if (has100) {
                const invalidOther = rows.some(r => {
                    const w = Number(r.querySelector('.weight-input').value);
                    return w !== 0 && w !== 100;
                });
                if (invalidOther) {
                    valid = false; title = 'Aturan 100% dilanggar';
                    message = 'Jika satu peserta 100%, peserta lain harus 0%.';
                    icon = '⚠️'; theme = 'red';
                }
            }
        }

        if (valid && resultMode === 'multi') {
            const wc = Number(winnerCountInput.value || 1);
            if (wc < 1 || wc > rows.length) {
                valid = false; title = 'Jumlah pemenang tidak valid';
                message = `Antara 1 sampai ${rows.length} peserta.`;
                icon = '⚠️'; theme = 'red';
            }
        }

        const themeMap = {
            emerald: 'rounded-3xl border p-5 border-emerald-500/30 bg-emerald-50 dark:border-emerald-400/20 dark:bg-emerald-400/[0.05]',
            red:     'rounded-3xl border p-5 border-red-500/30 bg-red-50 dark:border-red-400/20 dark:bg-red-400/[0.05]',
        };
        validationCard.className = themeMap[theme];
        validationIcon.textContent = icon;
        validationTitle.textContent = title;
        validationTitle.className = 'text-sm font-black ' + (valid ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300');
        validationMsg.textContent = message;

        allSubmitBtns.forEach(btn => { btn.disabled = !valid; });

        if (fabValidation) {
            fabValidation.className = valid
                ? 'flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-300'
                : 'flex items-center gap-2 text-xs font-bold text-red-600 dark:text-red-300';
            fabValidation.innerHTML = valid
                ? '<span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500 dark:bg-emerald-400"></span><span class="hidden sm:inline">Siap disimpan</span><span class="sm:hidden">OK</span>'
                : '<span class="h-2 w-2 animate-pulse rounded-full bg-red-500 dark:bg-red-400"></span><span class="hidden sm:inline">Periksa pengaturan</span><span class="sm:hidden">!</span>';
        }

        return valid;
    }

    /* ============================================================
       RENDER
    ============================================================ */
    function render() {
        reindexEntries();

        const rows = getRows();
        const count = rows.length;

        countEl.textContent = count;
        addBtn.disabled = count >= MAX_ENTRIES;
        addBtn.innerHTML = count >= MAX_ENTRIES
            ? '<span>Batas maksimal peserta tercapai (50/50)</span>'
            : '<span class="text-base">+</span><span>Tambah Nama</span>';

        duplicateWarn.classList.toggle('hidden', !hasDuplicateNames() || count < 2);
        titleCounter.textContent = `${titleInput.value.length} / 120`;

        const mode = getSelectionMode();
        const isWeighted = mode === 'weighted';
        weightedPanel.classList.toggle('hidden', !isWeighted);
        randomPanel.classList.toggle('hidden', isWeighted);

        rows.forEach(r => {
            const ww = r.querySelector('.weight-wrap');
            const wi = r.querySelector('.weight-input');
            ww.classList.toggle('hidden', !isWeighted);
            wi.disabled = !isWeighted;
        });

        if (isWeighted) {
            const total = getTotalWeight();
            let theme;
            if (total === 100) theme = 'border-emerald-500/30 bg-emerald-50 text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300';
            else if (total < 100) theme = 'border-amber-400/30 bg-amber-50 text-amber-700 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-300';
            else theme = 'border-red-500/30 bg-red-50 text-red-700 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-300';

            weightStatus.className = `rounded-full border px-3 py-1 text-xs font-black ${theme}`;
            weightStatus.textContent = `Total ${total}%`;

            weightBarFill.style.width = Math.min(total, 100) + '%';
            weightBarFill.className = 'h-full transition-all duration-300 bg-gradient-to-r ' +
                (total === 100 ? 'from-emerald-400 to-cyan-400'
                    : total > 100 ? 'from-red-500 to-orange-500'
                        : 'from-amber-400 to-cyan-400');
        }

        const resultMode = resultModeSel.value;
        if (resultMode === 'single') {
            winnerCountPanel.classList.add('hidden');
            winnerCountInput.value = '1';
        } else if (resultMode === 'multi') {
            winnerCountPanel.classList.remove('hidden');
            const wc = Math.max(1, Math.min(Number(winnerCountInput.value || 1), Math.max(count, 1)));
            winnerCountInput.value = String(wc);
            winnerCountInput.max = String(Math.max(count, 1));
        } else {
            winnerCountPanel.classList.add('hidden');
            winnerCountInput.value = '1';
        }

        const soundMode = getSoundMode();
        systemSoundPanel.classList.toggle('hidden', soundMode !== 'system');
        uploadSoundPanel.classList.toggle('hidden', soundMode !== 'upload');

        const template = resultTemplateInp.value.trim() || 'Sukses kepada {name}!';
        const first = getNames()[0] || 'Bima';
        resultPreview.textContent = template.replaceAll('{name}', escapeHtml(first));

        summaryTitle.textContent = titleInput.value.trim() || '—';
        summaryCount.textContent = count;
        summaryDuration.textContent = durationSel.value + 's';

        const modeLabels = { weighted: '⚖️ Bobot Peluang', secret_random: '🎲 Acak Rahasia' };
        summaryMode.textContent = modeLabels[mode] || '—';

        const resultLabels = {
            single: '1 Terpilih',
            multi: `${winnerCountInput.value} Terpilih`,
            ranking: 'Urutan Lengkap',
        };
        summaryResult.textContent = resultLabels[resultMode] || '—';

        const soundLabels = {
            system: '🔊 Suara Sistem',
            upload: '🎵 Audio Sendiri',
            none: '🔇 Tanpa Suara',
        };
        summarySound.textContent = soundLabels[soundMode] || '—';

        pvCount.textContent = count;
        pvMode.textContent = isWeighted ? 'Bobot' : 'Rahasia';

        if (fabSummary) {
            fabSummary.textContent = `${titleInput.value.trim() || 'Roda'} • ${count} peserta • ${durationSel.value}s`;
        }

        drawPreview();
        validate();
    }

    /* ============================================================
       EVENTS
    ============================================================ */
    addBtn.addEventListener('click', () => {
        if (getRows().length < MAX_ENTRIES) createEntryRow('', 0);
    });
    titleInput.addEventListener('input', render);
    resultTemplateInp.addEventListener('input', render);
    resultModeSel.addEventListener('change', render);
    durationSel.addEventListener('change', render);
    winnerCountInput.addEventListener('input', render);
    document.querySelectorAll('input[name="selection_mode"]').forEach(i => i.addEventListener('change', render));
    document.querySelectorAll('input[name="sound_mode"]').forEach(i => i.addEventListener('change', render));

    /* ============================================================
       🔥 RE-DRAW PREVIEW saat theme berubah
    ============================================================ */
    const observer = new MutationObserver((mutations) => {
        for (const m of mutations) {
            if (m.attributeName === 'class') {
                drawPreview();
                break;
            }
        }
    });
    observer.observe(document.documentElement, { attributes: true });

    /* ============================================================
       SUBMIT — dengan loading state
    ============================================================ */
    form.addEventListener('submit', (e) => {
        if (!validate()) {
            e.preventDefault();
            validationCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        form.classList.add('loading');
        allSubmitBtns.forEach(btn => {
            btn.disabled = true;
            const label = btn.querySelector('span:last-child') || btn;
            if (label && label !== btn) {
                label.innerHTML = '<span class="spinner"></span> Menyimpan...';
            }
        });
        if (fabBtnLabel) {
            fabBtnLabel.innerHTML = '<span class="spinner"></span> Menyimpan...';
        }
    });

    /* ============================================================
       KEYBOARD SHORTCUT: Ctrl/Cmd + S
    ============================================================ */
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            if (validate()) form.requestSubmit();
        }
    });

    /* ============================================================
       INITIAL STATE
    ============================================================ */
    const seed = @json(array_values($seedEntries));
    if (seed.length) {
        seed.forEach(e => createEntryRow(e.name || '', e.weight ?? 0));
    } else {
        createEntryRow('Ibni', 60);
        createEntryRow('Fatih', 15);
        createEntryRow('Dimas', 25);
    }

    render();
});
</script>
@endsection
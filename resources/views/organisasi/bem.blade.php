@extends('layouts.app')

@section('title', 'BEM — Organisasi UNICODE')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    {{-- ► Ubah bagian ini per file --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/4 h-96 w-96 rounded-full bg-cyan/15 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/4 h-96 w-96 rounded-full bg-violet/15 blur-3xl"></div>
        </div>

        <div class="relative mx-auto w-full max-w-[1800px] px-4 py-14 sm:px-6 md:py-20 lg:px-10">

            <nav aria-label="Breadcrumb" class="mb-8 text-xs text-white/50">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('landing') }}" class="hover:text-cyan">Beranda</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li><a href="{{ route('organisasi.index') }}" class="hover:text-cyan">Organisasi</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li class="text-white/80" aria-current="page">BEM</li>
                </ol>
            </nav>

            <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto] lg:gap-12">
                <div class="mx-auto lg:mx-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-cyan-500 to-electric opacity-40 blur-xl"></div>
                        <div class="relative grid h-32 w-32 place-items-center overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 backdrop-blur-md md:h-40 md:w-40">
                            <i class="fas fa-users-line text-5xl text-cyan-200"></i>
                        </div>
                    </div>
                </div>

                <div class="min-w-0 text-center lg:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-cyan-300/80">Organisasi Mahasiswa</p>
                    <h1 class="mt-2 bg-gradient-to-r from-white via-cyan-100 to-cyan bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-5xl">
                        BEM
                    </h1>
                    <p class="mt-2 text-sm text-white/60 md:text-base">Badan Eksekutif Mahasiswa — Universitas Bhakti Asih</p>
                    <p class="mt-4 text-base italic text-cyan-200/90 md:text-lg">&ldquo;Suara Mahasiswa, Aksi Nyata&rdquo;</p>

                    <div class="mt-6 inline-flex flex-wrap gap-3 sm:gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                            <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">48</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/50">Anggota</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                            <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">12</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/50">Proker</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                            <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">5</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/50">Prestasi</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:w-48">
                    <a href="https://instagram.com/bem_unibang" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-pink-400/40 hover:bg-pink-500/10">
                        <span class="flex items-center gap-3">
                            <i class="fab fa-instagram text-pink-400"></i>
                            <span class="truncate">@bem_unibang</span>
                        </span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                    </a>
                    <a href="mailto:bem@unibang.ac.id"
                       class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-cyan/40 hover:bg-cyan/10">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-envelope text-cyan"></i>
                            <span class="truncate">bem@unibang.ac.id</span>
                        </span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════ KONTEN ═══════════════════ --}}
    {{-- ► Isi di sini sebebas mungkin --}}
    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 sm:px-6 lg:px-10">
        <div class="grid gap-10 lg:grid-cols-[2fr_1fr]">

            <div class="space-y-10">
                {{-- Tentang --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan/15 text-cyan">
                            <i class="fas fa-circle-info"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Tentang</h2>
                    </header>
                    <p class="leading-relaxed text-white/75">
                        Tulis deskripsi lengkap BEM di sini. Bisa diubah berbeda dari HIMA lain.
                    </p>
                </article>

                {{-- Visi Misi --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-5 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-violet/15 text-violet-300">
                            <i class="fas fa-bullseye"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Visi &amp; Misi</h2>
                    </header>
                    <div class="mb-6 rounded-2xl border border-cyan/20 bg-gradient-to-br from-cyan/10 to-electric/10 p-5">
                        <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-cyan-300">Visi</p>
                        <p class="text-white/90">Tulis visi BEM di sini.</p>
                    </div>
                    <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-violet-300">Misi</p>
                    <ol class="space-y-3">
                        @foreach(['Misi pertama.', 'Misi kedua.', 'Misi ketiga.'] as $i => $misi)
                            <li class="flex gap-3">
                                <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-violet/20 text-[11px] font-bold text-violet-200">{{ $i + 1 }}</span>
                                <span class="text-sm leading-relaxed text-white/75">{{ $misi }}</span>
                            </li>
                        @endforeach
                    </ol>
                </article>
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-400/15 text-emerald-300">
                            <i class="fas fa-user-tie"></i>
                        </span>
                        <h2 class="text-base font-bold">Pengurus</h2>
                    </header>
                    <p class="text-sm text-white/60">Tulis daftar pengurus di sini.</p>
                </div>

                <a href="{{ route('organisasi.index') }}"
                   class="group flex items-center justify-between rounded-3xl border border-white/10 bg-white/5 p-5 text-sm font-semibold transition hover:border-cyan/40 hover:bg-cyan/10">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-arrow-left text-cyan transition group-hover:-translate-x-1"></i>
                        Kembali ke Direktori
                    </span>
                    <i class="fas fa-chevron-right text-[10px] text-white/30"></i>
                </a>
            </aside>
        </div>
    </section>
</div>
@endsection
@extends('layouts.app')
@section('title', 'HIMA ARS — Organisasi UNICODE')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/4 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/4 h-96 w-96 rounded-full bg-blue-400/20 blur-3xl"></div>
        </div>
        <div class="relative mx-auto w-full max-w-[1800px] px-4 py-14 sm:px-6 md:py-20 lg:px-10">
            <nav aria-label="Breadcrumb" class="mb-8 text-xs text-white/50">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('landing') }}" class="hover:text-cyan">Beranda</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li><a href="{{ route('organisasi.index') }}" class="hover:text-cyan">Organisasi</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li class="text-white/80" aria-current="page">HIMA ARS</li>
                </ol>
            </nav>

            <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto] lg:gap-12">
                <div class="mx-auto lg:mx-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-sky-500 to-blue-400 opacity-40 blur-xl"></div>
                        <div class="relative grid h-32 w-32 place-items-center overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 backdrop-blur-md md:h-40 md:w-40">
                            <i class="fas fa-hospital text-5xl text-sky-200"></i>
                        </div>
                    </div>
                </div>

                <div class="min-w-0 text-center lg:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-sky-300/80">Organisasi Mahasiswa</p>
                    <h1 class="mt-2 bg-gradient-to-r from-white via-sky-100 to-sky-300 bg-clip-text text-3xl font-extrabold text-transparent md:text-5xl">HIMA ARS</h1>
                    <p class="mt-2 text-sm text-white/60 md:text-base">Himpunan Mahasiswa Administrasi Rumah Sakit — Universitas Bhakti Asih</p>
                    <p class="mt-4 text-base italic text-sky-200/90 md:text-lg">&ldquo;Manajemen Sehat, Layanan Bermutu&rdquo;</p>

                    <div class="mt-6 inline-flex flex-wrap gap-3 sm:gap-4">
                        @foreach(['Anggota' => 60, 'Proker' => 10, 'Prestasi' => 4] as $lbl => $v)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                                <p class="text-xl font-extrabold text-sky-200 md:text-2xl">{{ $v }}</p>
                                <p class="text-[10px] uppercase tracking-widest text-white/50">{{ $lbl }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:w-48">
                    <a href="https://instagram.com/hima_ars" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-pink-400/40 hover:bg-pink-500/10">
                        <span class="flex items-center gap-3"><i class="fab fa-instagram text-pink-400"></i><span class="truncate">@hima_ars</span></span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                    </a>
                    <a href="mailto:hima.ars@unibang.ac.id"
                       class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-sky-400/40 hover:bg-sky-500/10">
                        <span class="flex items-center gap-3"><i class="fas fa-envelope text-sky-400"></i><span class="truncate">hima.ars@unibang.ac.id</span></span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 sm:px-6 lg:px-10">
        <div class="grid gap-10 lg:grid-cols-[2fr_1fr]">
            <div class="space-y-10">

                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-400/15 text-sky-300"><i class="fas fa-circle-info"></i></span>
                        <h2 class="text-lg font-bold md:text-xl">Tentang HIMA ARS</h2>
                    </header>
                    <p class="leading-relaxed text-white/75">
                        HIMA ARS menyiapkan mahasiswa Administrasi Rumah Sakit untuk menjadi manajer kesehatan
                        yang profesional dan berorientasi mutu layanan. Kami membekali mahasiswa dengan kemampuan
                        manajerial, analisis sistem kesehatan, dan pemahaman standar akreditasi rumah sakit.
                    </p>
                </article>

                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-5 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-400/15 text-blue-300"><i class="fas fa-bullseye"></i></span>
                        <h2 class="text-lg font-bold md:text-xl">Visi &amp; Misi</h2>
                    </header>
                    <div class="mb-6 rounded-2xl border border-sky-400/20 bg-gradient-to-br from-sky-500/10 to-blue-400/10 p-5">
                        <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-sky-300">Visi</p>
                        <p class="text-white/90">Melahirkan administrator rumah sakit yang kompeten dan berintegritas.</p>
                    </div>
                    <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-blue-300">Misi</p>
                    <ol class="space-y-3">
                        @foreach([
                            'Meningkatkan pemahaman manajemen pelayanan kesehatan.',
                            'Menyelenggarakan studi banding ke fasilitas kesehatan.',
                            'Mendukung riset administrasi rumah sakit.',
                            'Membangun jaringan dengan manajemen RS.',
                        ] as $i => $m)
                            <li class="flex gap-3">
                                <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-sky-400/20 text-[11px] font-bold text-sky-200">{{ $i+1 }}</span>
                                <span class="text-sm leading-relaxed text-white/75">{{ $m }}</span>
                            </li>
                        @endforeach
                    </ol>
                </article>

                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-5 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan/15 text-cyan"><i class="fas fa-list-check"></i></span>
                        <h2 class="text-lg font-bold md:text-xl">Program Kerja</h2>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach([
                            ['icon'=>'fas fa-hospital','name'=>'Studi Banding RS','desc'=>'Kunjungan ke rumah sakit mitra.'],
                            ['icon'=>'fas fa-clipboard-list','name'=>'Manajemen Mutu','desc'=>'Pelatihan standar akreditasi RS.'],
                            ['icon'=>'fas fa-database','name'=>'Rekam Medis Elektronik','desc'=>'Workshop SIMRS dan RME.'],
                            ['icon'=>'fas fa-user-nurse','name'=>'Healthcare Leadership','desc'=>'Pelatihan kepemimpinan kesehatan.'],
                        ] as $p)
                            <div class="group rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:-translate-y-0.5 hover:border-sky-400/30 hover:bg-white/10">
                                <div class="mb-2 grid h-10 w-10 place-items-center rounded-xl bg-sky-400/15 text-sky-300"><i class="{{ $p['icon'] }}"></i></div>
                                <h3 class="font-bold text-white">{{ $p['name'] }}</h3>
                                <p class="mt-1 text-sm text-white/60">{{ $p['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-400/15 text-sky-300"><i class="fas fa-user-tie"></i></span>
                        <h2 class="text-base font-bold">Pengurus Inti</h2>
                    </header>
                    <ul class="space-y-3">
                        @foreach([
                            ['name'=>'Eko Purnomo','role'=>'Ketua','nim'=>'2023070001'],
                            ['name'=>'Ayu Wulandari','role'=>'Wakil Ketua','nim'=>'2023070002'],
                        ] as $m)
                            <li class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($m['name']) }}&background=0EA5E9&color=fff&size=40" alt="{{ $m['name'] }}" class="h-10 w-10 rounded-full border border-white/20" loading="lazy">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold">{{ $m['name'] }}</p>
                                    <p class="truncate text-[11px] text-sky-300/80">{{ $m['role'] }} · {{ $m['nim'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <a href="{{ route('organisasi.index') }}"
                   class="group flex items-center justify-between rounded-3xl border border-white/10 bg-white/5 p-5 text-sm font-semibold transition hover:border-sky-400/40 hover:bg-sky-500/10">
                    <span class="flex items-center gap-3"><i class="fas fa-arrow-left text-sky-400 transition group-hover:-translate-x-1"></i>Kembali ke Direktori</span>
                    <i class="fas fa-chevron-right text-[10px] text-white/30"></i>
                </a>
            </aside>
        </div>
    </section>
</div>
@endsection
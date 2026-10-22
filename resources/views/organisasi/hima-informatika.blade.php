@extends('layouts.app')

@section('title', 'HIMA Informatika — Organisasi UNICODE')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/4 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/4 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl"></div>
        </div>

        <div class="relative mx-auto w-full max-w-[1800px] px-4 py-14 sm:px-6 md:py-20 lg:px-10">

            <nav aria-label="Breadcrumb" class="mb-8 text-xs text-white/50">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('landing') }}" class="hover:text-cyan">Beranda</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li><a href="{{ route('organisasi.index') }}" class="hover:text-cyan">Organisasi</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li class="text-white/80" aria-current="page">HIMA Informatika</li>
                </ol>
            </nav>

            <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto] lg:gap-12">
                <div class="mx-auto lg:mx-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-blue-500 to-cyan-400 opacity-40 blur-xl"></div>
                        <div class="relative grid h-32 w-32 place-items-center overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 backdrop-blur-md md:h-40 md:w-40">
                            <i class="fas fa-laptop-code text-5xl text-cyan-200"></i>
                        </div>
                    </div>
                </div>

                <div class="min-w-0 text-center lg:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-cyan-300/80">Organisasi Mahasiswa</p>
                    <h1 class="mt-2 bg-gradient-to-r from-white via-cyan-100 to-cyan bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-5xl">
                        HIMA Informatika
                    </h1>
                    <p class="mt-2 text-sm text-white/60 md:text-base">Himpunan Mahasiswa Informatika — Universitas Bhakti Asih</p>
                    <p class="mt-4 text-base italic text-cyan-200/90 md:text-lg">&ldquo;Koding Hari Ini, Inovasi Esok Hari&rdquo;</p>

                    <div class="mt-6 inline-flex flex-wrap gap-3 sm:gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                            <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">120</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/50">Anggota</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                            <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">18</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/50">Proker</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                            <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">12</p>
                            <p class="text-[10px] uppercase tracking-widest text-white/50">Prestasi</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:w-48">
                    <a href="https://instagram.com/hima_informatika" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-pink-400/40 hover:bg-pink-500/10">
                        <span class="flex items-center gap-3">
                            <i class="fab fa-instagram text-pink-400"></i>
                            <span class="truncate">@hima_informatika</span>
                        </span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                    </a>
                    <a href="mailto:hima.if@unibang.ac.id"
                       class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-cyan/40 hover:bg-cyan/10">
                        <span class="flex items-center gap-3">
                            <i class="fas fa-envelope text-cyan"></i>
                            <span class="truncate">hima.if@unibang.ac.id</span>
                        </span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════ KONTEN UTAMA ═══════════════════ --}}
    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 sm:px-6 lg:px-10">
        <div class="grid gap-10 lg:grid-cols-[2fr_1fr]">

            <div class="space-y-10">

                {{-- Tentang --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan/15 text-cyan">
                            <i class="fas fa-circle-info"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Tentang HIMA Informatika</h2>
                    </header>
                    <p class="leading-relaxed text-white/75">
                        HIMA Informatika adalah himpunan mahasiswa program studi Informatika yang berfokus pada
                        pengembangan skill teknologi, competitive programming, dan kolaborasi proyek nyata.
                        Kami membina mahasiswa untuk siap bersaing di industri digital — dari web, mobile,
                        AI, hingga cybersecurity.
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
                        <p class="text-white/90">
                            Menjadi himpunan yang unggul dalam teknologi informasi dan melahirkan
                            talenta digital berdaya saing nasional.
                        </p>
                    </div>

                    <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-violet-300">Misi</p>
                    <ol class="space-y-3">
                        @foreach([
                            'Menyelenggarakan pelatihan coding & workshop teknologi terkini.',
                            'Membina tim competitive programming tingkat nasional.',
                            'Membangun ekosistem kolaborasi proyek antar mahasiswa.',
                            'Menjadi mitra industri teknologi terdepan.',
                        ] as $i => $misi)
                            <li class="flex gap-3">
                                <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-violet/20 text-[11px] font-bold text-violet-200">{{ $i + 1 }}</span>
                                <span class="text-sm leading-relaxed text-white/75">{{ $misi }}</span>
                            </li>
                        @endforeach
                    </ol>
                </article>

                {{-- Program Kerja --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-5 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-400/15 text-amber-300">
                            <i class="fas fa-list-check"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Program Kerja Unggulan</h2>
                    </header>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach([
                            ['icon' => 'fas fa-code',              'name' => 'Coding Bootcamp',        'desc' => 'Bootcamp intensif Laravel & JavaScript selama 4 minggu.'],
                            ['icon' => 'fas fa-trophy',            'name' => 'Competitive Programming','desc' => 'Latihan rutin & kontes internal untuk ICPC/ACM.'],
                            ['icon' => 'fas fa-project-diagram',   'name' => 'Proyek Kolaboratif',     'desc' => 'Membangun produk nyata bersama tim lintas divisi.'],
                            ['icon' => 'fas fa-chalkboard-teacher','name' => 'Guest Lecture',          'desc' => 'Menghadirkan praktisi industri teknologi ternama.'],
                        ] as $p)
                            <div class="group rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition hover:-translate-y-0.5 hover:border-cyan/30 hover:bg-white/10">
                                <div class="mb-2 grid h-10 w-10 place-items-center rounded-xl bg-cyan/15 text-cyan transition group-hover:bg-cyan/25">
                                    <i class="{{ $p['icon'] }}"></i>
                                </div>
                                <h3 class="font-bold text-white">{{ $p['name'] }}</h3>
                                <p class="mt-1 text-sm text-white/60">{{ $p['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                {{-- Agenda Terdekat (contoh section unik khusus HIMA IF) --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-5 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-400/15 text-emerald-300">
                            <i class="fas fa-calendar-days"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Agenda Terdekat</h2>
                    </header>
                    <ul class="space-y-3">
                        @foreach([
                            ['date' => '05 Nov 2026', 'title' => 'Workshop Laravel 11',       'loc' => 'Lab Komputer 3'],
                            ['date' => '12 Nov 2026', 'title' => 'Internal Contest CP',        'loc' => 'Lab Komputer 1'],
                            ['date' => '20 Nov 2026', 'title' => 'Tech Talk: AI & Career',      'loc' => 'Aula Utama'],
                        ] as $ev)
                            <li class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                                <div class="grid h-14 w-16 shrink-0 place-items-center rounded-xl bg-cyan/15 text-center">
                                    <p class="text-[9px] uppercase tracking-widest text-cyan-300">{{ explode(' ', $ev['date'])[1] }}</p>
                                    <p class="text-lg font-extrabold text-cyan-100">{{ explode(' ', $ev['date'])[0] }}</p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-white">{{ $ev['title'] }}</p>
                                    <p class="mt-0.5 truncate text-xs text-white/50"><i class="fas fa-location-dot mr-1"></i>{{ $ev['loc'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </article>
            </div>

            {{-- SIDEBAR --}}
            <aside class="space-y-6">

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-400/15 text-emerald-300">
                            <i class="fas fa-user-tie"></i>
                        </span>
                        <h2 class="text-base font-bold">Pengurus Inti 2025/2026</h2>
                    </header>
                    <ul class="space-y-3">
                        @foreach([
                            ['name' => 'Rizky Pratama', 'role' => 'Ketua',       'nim' => '2023020001'],
                            ['name' => 'Anisa Putri',   'role' => 'Wakil Ketua', 'nim' => '2023020002'],
                            ['name' => 'Fajar Nugroho', 'role' => 'Sekretaris',  'nim' => '2023020003'],
                            ['name' => 'Citra Dewi',    'role' => 'Bendahara',   'nim' => '2023020004'],
                        ] as $m)
                            <li class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($m['name']) }}&background=0D8ABC&color=fff&size=40"
                                     alt="{{ $m['name'] }}" class="h-10 w-10 rounded-full border border-white/20" loading="lazy">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-white">{{ $m['name'] }}</p>
                                    <p class="truncate text-[11px] text-cyan-300/80">{{ $m['role'] }} · {{ $m['nim'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-pink-400/15 text-pink-300">
                            <i class="fas fa-share-nodes"></i>
                        </span>
                        <h2 class="text-base font-bold">Ikuti Kami</h2>
                    </header>
                    <div class="space-y-3 text-sm">
                        <a href="https://instagram.com/hima_informatika" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-3 rounded-lg px-2 py-2 text-white/75 transition hover:bg-pink-500/10 hover:text-white">
                            <i class="fab fa-instagram w-4 text-pink-400"></i> Instagram
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-lg px-2 py-2 text-white/75 transition hover:bg-cyan/10 hover:text-white">
                            <i class="fab fa-github w-4 text-cyan"></i> GitHub Organisasi
                        </a>
                        <a href="mailto:hima.if@unibang.ac.id"
                           class="flex items-center gap-3 rounded-lg px-2 py-2 text-white/75 transition hover:bg-cyan/10 hover:text-white">
                            <i class="fas fa-envelope w-4 text-cyan"></i> hima.if@unibang.ac.id
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-lg px-2 py-2 text-white/75 transition hover:bg-emerald-500/10 hover:text-white">
                            <i class="fab fa-whatsapp w-4 text-emerald-400"></i> WhatsApp
                        </a>
                    </div>
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
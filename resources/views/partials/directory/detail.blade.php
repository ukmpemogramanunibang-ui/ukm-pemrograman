{{-- Template detail unit — dipakai oleh organisasi/show & ukm/show --}}
@props([
    'unit'       => [],
    'allItems'   => [],
    'unitType'   => 'Unit',
    'indexRoute' => 'organisasi.index',
    'showRoute'  => 'organisasi.show',
])

@php
    $slug     = $unit['slug'];
    $grad     = $unit['gradient'] ?? 'from-cyan to-electric';
    $logo     = $unit['logo'] ?? null;
    $hasLogo  = $logo && file_exists(public_path($logo));

    /* Unit lain untuk section "Lihat Juga" */
    $others = collect($allItems)->except($slug)->take(3);
@endphp

<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-32 left-1/4 h-96 w-96 rounded-full bg-cyan/15 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/4 h-96 w-96 rounded-full bg-violet/15 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-midnight via-deepblue/90 to-midnight"></div>
        </div>

        <div class="relative mx-auto w-full max-w-[1800px] px-4 py-14 sm:px-6 md:py-20 lg:px-10">

            {{-- Breadcrumb --}}
            <nav aria-label="Breadcrumb" class="mb-8 text-xs text-white/50">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('landing') }}" class="transition hover:text-cyan">Beranda</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li><a href="{{ route($indexRoute) }}" class="transition hover:text-cyan">{{ $unitType }}</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li class="text-white/80" aria-current="page">{{ $unit['name'] }}</li>
                </ol>
            </nav>

            <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto] lg:gap-12">

                {{-- Logo --}}
                <div class="mx-auto lg:mx-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br {{ $grad }} opacity-40 blur-xl"></div>
                        <div class="relative grid h-32 w-32 place-items-center overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 backdrop-blur-md md:h-40 md:w-40">
                            @if($hasLogo)
                                <img src="{{ asset($logo) }}" alt="Logo {{ $unit['name'] }}"
                                     class="h-full w-full object-cover" loading="eager" decoding="async">
                            @else
                                <i class="fas fa-shield-halved text-4xl text-cyan-200"></i>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="min-w-0 text-center lg:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-cyan-300/80">{{ $unitType }}</p>
                    <h1 class="mt-2 bg-gradient-to-r from-white via-cyan-100 to-cyan bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-5xl">
                        {{ $unit['name'] }}
                    </h1>
                    <p class="mt-2 text-sm text-white/60 md:text-base">{{ $unit['full_name'] ?? '' }}</p>
                    <p class="mt-4 text-base italic text-cyan-200/90 md:text-lg">&ldquo;{{ $unit['tagline'] ?? '' }}&rdquo;</p>

                    {{-- Stats --}}
                    <div class="mt-6 grid grid-cols-3 gap-3 sm:inline-grid sm:gap-4">
                        @foreach(($unit['stats'] ?? []) as $label => $value)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                                <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">{{ $value }}</p>
                                <p class="text-[10px] uppercase tracking-widest text-white/50">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CTA kontak --}}
                <div class="flex flex-col gap-3 lg:w-48">
                    @if(!empty($unit['contact']['instagram']))
                        <a href="https://instagram.com/{{ ltrim($unit['contact']['instagram'], '@') }}"
                           target="_blank" rel="noopener noreferrer"
                           class="group flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition-all hover:border-pink-400/40 hover:bg-pink-500/10">
                            <span class="flex items-center gap-3">
                                <i class="fab fa-instagram text-pink-400"></i>
                                <span class="truncate">{{ $unit['contact']['instagram'] }}</span>
                            </span>
                            <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30 transition group-hover:text-pink-400"></i>
                        </a>
                    @endif

                    @if(!empty($unit['contact']['email']))
                        <a href="mailto:{{ $unit['contact']['email'] }}"
                           class="group flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition-all hover:border-cyan/40 hover:bg-cyan/10">
                            <span class="flex items-center gap-3">
                                <i class="fas fa-envelope text-cyan"></i>
                                <span class="truncate">{{ $unit['contact']['email'] }}</span>
                            </span>
                            <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30 transition group-hover:text-cyan"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════ KONTEN ═══════════════════ --}}
    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 sm:px-6 lg:px-10">
        <div class="grid gap-10 lg:grid-cols-[2fr_1fr]">

            {{-- Kolom kiri --}}
            <div class="space-y-10">

                {{-- Tentang --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-4 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan/15 text-cyan">
                            <i class="fas fa-circle-info"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Tentang</h2>
                    </header>
                    <p class="leading-relaxed text-white/75">{{ $unit['about'] ?? '-' }}</p>
                </article>

                {{-- Visi Misi --}}
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                    <header class="mb-5 flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-violet/15 text-violet-300">
                            <i class="fas fa-bullseye"></i>
                        </span>
                        <h2 class="text-lg font-bold md:text-xl">Visi &amp; Misi</h2>
                    </header>

                    @if(!empty($unit['visi']))
                        <div class="mb-6 rounded-2xl border border-cyan/20 bg-gradient-to-br from-cyan/10 to-electric/10 p-5">
                            <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-cyan-300">Visi</p>
                            <p class="text-white/90">{{ $unit['visi'] }}</p>
                        </div>
                    @endif

                    @if(!empty($unit['misi']))
                        <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-violet-300">Misi</p>
                        <ol class="space-y-3">
                            @foreach($unit['misi'] as $i => $misi)
                                <li class="flex gap-3">
                                    <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-violet/20 text-[11px] font-bold text-violet-200">
                                        {{ $i + 1 }}
                                    </span>
                                    <span class="text-sm leading-relaxed text-white/75">{{ $misi }}</span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </article>

                {{-- Program Kerja --}}
                @if(!empty($unit['programs']))
                    <article class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl md:p-8">
                        <header class="mb-5 flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-400/15 text-amber-300">
                                <i class="fas fa-list-check"></i>
                            </span>
                            <h2 class="text-lg font-bold md:text-xl">Program Kerja Unggulan</h2>
                        </header>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach($unit['programs'] as $p)
                                <div class="group rounded-2xl border border-white/10 bg-white/[0.03] p-4 transition-all hover:-translate-y-0.5 hover:border-cyan/30 hover:bg-white/10">
                                    <div class="mb-2 grid h-10 w-10 place-items-center rounded-xl bg-cyan/15 text-cyan transition group-hover:bg-cyan/25">
                                        <i class="{{ $p['icon'] ?? 'fas fa-star' }}"></i>
                                    </div>
                                    <h3 class="font-bold text-white">{{ $p['name'] }}</h3>
                                    <p class="mt-1 text-sm text-white/60">{{ $p['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endif
            </div>

            {{-- Kolom kanan --}}
            <aside class="space-y-6">

                {{-- Pengurus --}}
                @if(!empty($unit['members']))
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <header class="mb-4 flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-400/15 text-emerald-300">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <h2 class="text-base font-bold">Pengurus Inti</h2>
                        </header>
                        <ul class="space-y-3">
                            @foreach($unit['members'] as $m)
                                <li class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($m['name']) }}&background=0D8ABC&color=fff&size=40"
                                         alt="{{ $m['name'] }}"
                                         class="h-10 w-10 rounded-full border border-white/20" loading="lazy">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-white">{{ $m['name'] }}</p>
                                        <p class="truncate text-[11px] text-cyan-300/80">{{ $m['role'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Kontak --}}
                @if(!empty($unit['contact']))
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                        <header class="mb-4 flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-pink-400/15 text-pink-300">
                                <i class="fas fa-address-book"></i>
                            </span>
                            <h2 class="text-base font-bold">Kontak</h2>
                        </header>
                        <ul class="space-y-3 text-sm">
                            @if(!empty($unit['contact']['instagram']))
                                <li class="flex items-center gap-3 text-white/75">
                                    <i class="fab fa-instagram w-4 text-pink-400"></i>
                                    <span class="truncate">{{ $unit['contact']['instagram'] }}</span>
                                </li>
                            @endif
                            @if(!empty($unit['contact']['email']))
                                <li class="flex items-center gap-3 text-white/75">
                                    <i class="fas fa-envelope w-4 text-cyan"></i>
                                    <span class="truncate">{{ $unit['contact']['email'] }}</span>
                                </li>
                            @endif
                            @if(!empty($unit['contact']['whatsapp']))
                                <li class="flex items-center gap-3 text-white/75">
                                    <i class="fab fa-whatsapp w-4 text-emerald-400"></i>
                                    <span class="truncate">{{ $unit['contact']['whatsapp'] }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                {{-- Kembali --}}
                <a href="{{ route($indexRoute) }}"
                   class="group flex items-center justify-between rounded-3xl border border-white/10 bg-white/5 p-5 text-sm font-semibold transition-all hover:border-cyan/40 hover:bg-cyan/10">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-arrow-left text-cyan transition group-hover:-translate-x-1"></i>
                        Kembali ke Direktori
                    </span>
                    <i class="fas fa-chevron-right text-[10px] text-white/30"></i>
                </a>
            </aside>
        </div>

        {{-- ═══════════════════ LIHAT JUGA ═══════════════════ --}}
        @if($others->isNotEmpty())
            <div class="mt-16 border-t border-white/10 pt-12">
                <div class="mb-6 flex items-end justify-between">
                    <h2 class="text-lg font-bold md:text-xl">Lihat {{ $unitType }} Lainnya</h2>
                    <a href="{{ route($indexRoute) }}"
                       class="text-xs font-semibold text-cyan transition hover:text-cyan-200">
                        Lihat Semua →
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($others as $other)
                        @include('partials.directory.card', [
                            'item'      => $other,
                            'showRoute' => $showRoute,
                        ])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>
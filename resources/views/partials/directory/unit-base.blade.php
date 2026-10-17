{{--
 | Base layout untuk halaman unit (Organisasi/UKM).
 | Setiap unit view dapat memakai ini ATAU menulis HTML sendiri.
 |
 | Cara pakai:
 |   @include('partials.directory.unit-base', [
 |       'slug' => 'hima-informatika',
 |       'name' => 'HIMA Informatika',
 |       'fullName' => 'Himpunan Mahasiswa Informatika',
 |       'tagline' => 'Koding Hari Ini, Inovasi Esok Hari',
 |       'unitType' => 'Organisasi',
 |       'indexRoute' => 'organisasi.index',
 |       'accent' => 'from-blue-500 to-cyan-400',
 |       'logo' => 'images/organisasi/hima-informatika.png',
 |       'stats' => ['Anggota' => 120, 'Proker' => 18, 'Prestasi' => 12],
 |       'contact' => ['instagram' => '@...', 'email' => '...', 'whatsapp' => '...'],
 |   ])
--}}

@php
    $hasLogo = !empty($logo) && file_exists(public_path($logo));
    $accent  = $accent ?? 'from-cyan to-electric';
@endphp

<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    {{-- ═══════ HERO ═══════ --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 left-1/4 h-96 w-96 rounded-full bg-cyan/15 blur-3xl"></div>
            <div class="absolute -bottom-32 right-1/4 h-96 w-96 rounded-full bg-violet/15 blur-3xl"></div>
        </div>

        <div class="relative mx-auto w-full max-w-[1800px] px-4 py-14 sm:px-6 md:py-20 lg:px-10">
            <nav aria-label="Breadcrumb" class="mb-8 text-xs text-white/50">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('landing') }}" class="transition hover:text-cyan">Beranda</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li><a href="{{ route($indexRoute) }}" class="transition hover:text-cyan">{{ $unitType }}</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                    <li class="text-white/80" aria-current="page">{{ $name }}</li>
                </ol>
            </nav>

            <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto] lg:gap-12">
                {{-- Logo --}}
                <div class="mx-auto lg:mx-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br {{ $accent }} opacity-40 blur-xl"></div>
                        <div class="relative grid h-32 w-32 place-items-center overflow-hidden rounded-3xl border-2 border-white/20 bg-white/10 backdrop-blur-md md:h-40 md:w-40">
                            @if($hasLogo)
                                <img src="{{ asset($logo) }}" alt="Logo {{ $name }}" class="h-full w-full object-cover">
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
                        {{ $name }}
                    </h1>
                    <p class="mt-2 text-sm text-white/60 md:text-base">{{ $fullName }}</p>
                    <p class="mt-4 text-base italic text-cyan-200/90 md:text-lg">&ldquo;{{ $tagline }}&rdquo;</p>

                    @if(!empty($stats))
                        <div class="mt-6 inline-flex flex-wrap gap-3 sm:gap-4">
                            @foreach($stats as $label => $value)
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-md">
                                    <p class="text-xl font-extrabold text-cyan-200 md:text-2xl">{{ $value }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-white/50">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Kontak cepat --}}
                @if(!empty($contact))
                    <div class="flex flex-col gap-3 lg:w-48">
                        @if(!empty($contact['instagram']))
                            <a href="https://instagram.com/{{ ltrim($contact['instagram'], '@') }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-pink-400/40 hover:bg-pink-500/10">
                                <span class="flex items-center gap-3">
                                    <i class="fab fa-instagram text-pink-400"></i>
                                    <span class="truncate">{{ $contact['instagram'] }}</span>
                                </span>
                                <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                            </a>
                        @endif
                        @if(!empty($contact['email']))
                            <a href="mailto:{{ $contact['email'] }}"
                               class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-cyan/40 hover:bg-cyan/10">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-envelope text-cyan"></i>
                                    <span class="truncate">{{ $contact['email'] }}</span>
                                </span>
                                <i class="fas fa-arrow-up-right-from-square text-[10px] text-white/30"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════ KONTEN (diisi oleh view pemanggil) ═══════ --}}
    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 sm:px-6 lg:px-10">
        {{ $slot ?? '' }}
    </section>

    {{-- Tombol kembali --}}
    <div class="mx-auto w-full max-w-[1800px] px-4 pb-16 sm:px-6 lg:px-10">
        <a href="{{ route($indexRoute) }}"
           class="group flex items-center justify-between rounded-3xl border border-white/10 bg-white/5 p-5 text-sm font-semibold transition hover:border-cyan/40 hover:bg-cyan/10">
            <span class="flex items-center gap-3">
                <i class="fas fa-arrow-left text-cyan transition group-hover:-translate-x-1"></i>
                Kembali ke Direktori {{ $unitType }}
            </span>
            <i class="fas fa-chevron-right text-[10px] text-white/30"></i>
        </a>
    </div>
</div>
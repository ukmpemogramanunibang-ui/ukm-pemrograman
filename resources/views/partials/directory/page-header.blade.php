@props([
    'title' => 'Direktori', 'subtitle' => '', 'count' => 0, 'icon' => 'fas fa-sitemap',
])

<section class="relative overflow-hidden bg-gradient-to-br from-midnight via-deepblue to-electric">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-cyan/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-violet/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto w-full max-w-[1800px] px-4 py-14 sm:px-6 md:py-20 lg:px-10">
        <nav aria-label="Breadcrumb" class="mb-6 text-xs text-white/50">
            <ol class="flex items-center gap-2">
                <li><a href="{{ route('landing') }}" class="transition hover:text-cyan">Beranda</a></li>
                <li aria-hidden="true"><i class="fas fa-chevron-right text-[8px]"></i></li>
                <li class="text-white/80" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>

        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
            <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white/10 ring-1 ring-white/20 backdrop-blur-md md:h-16 md:w-16">
                <i class="{{ $icon }} text-2xl text-cyan md:text-3xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="bg-gradient-to-r from-white via-cyan-100 to-cyan bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-4xl lg:text-5xl">
                    {{ $title }}
                </h1>
                @if($subtitle)
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/70 md:text-base">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($count > 0)
            <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-cyan/30 bg-cyan/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-cyan-200 backdrop-blur-sm">
                <i class="fas fa-circle text-[6px]"></i>
                {{ $count }} Unit Terdaftar
            </div>
        @endif
    </div>
</section>
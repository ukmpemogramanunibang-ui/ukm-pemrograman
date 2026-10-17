@props(['item' => [], 'showRoute' => 'organisasi.show'])

@php
    $logo = $item['logo'] ?? null;
    $has  = $logo && file_exists(public_path($logo));
    $grad = $item['gradient'] ?? 'from-cyan to-electric';
@endphp

<article class="group relative h-full">
    <div aria-hidden="true"
         class="pointer-events-none absolute -inset-0.5 rounded-3xl bg-gradient-to-r {{ $grad }} opacity-0 blur-lg transition-opacity duration-500 group-hover:opacity-40"></div>

    <a href="{{ route($showRoute) }}"
       class="relative flex h-full flex-col overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-white/5 to-white/[0.02] p-6 backdrop-blur-xl transition-all duration-500
              hover:-translate-y-1.5 hover:border-white/25 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan">

        <div aria-hidden="true"
             class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-br {{ $grad }} opacity-0 transition-opacity duration-500 group-hover:opacity-[0.08]"></div>

        <div class="mb-5 flex items-start justify-between">
            <div class="relative">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br {{ $grad }} opacity-30 blur-md transition-opacity duration-500 group-hover:opacity-60"></div>
                <div class="relative grid h-16 w-16 place-items-center overflow-hidden rounded-2xl border border-white/20 bg-white/10 backdrop-blur-md">
                    @if($has)
                        <img src="{{ asset($logo) }}" alt="Logo {{ $item['name'] }}" class="h-full w-full object-cover" loading="lazy">
                    @else
                        <i class="fas fa-shield-halved text-2xl text-cyan-200"></i>
                    @endif
                </div>
            </div>
            <span class="rounded-full border border-white/15 bg-white/5 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-white/60">
                {{ $item['stats']['Anggota'] ?? 0 }} anggota
            </span>
        </div>

        <h3 class="text-lg font-extrabold leading-tight text-white transition-colors group-hover:text-cyan-200 md:text-xl">
            {{ $item['name'] }}
        </h3>
        <p class="mt-1 text-[11px] font-semibold uppercase tracking-wider text-cyan-300/80">
            {{ $item['full_name'] ?? '' }}
        </p>
        <p class="mt-3 line-clamp-2 text-sm italic text-white/60">&ldquo;{{ $item['tagline'] }}&rdquo;</p>

        <div class="mt-5 grid grid-cols-3 gap-2 border-t border-white/10 pt-4">
            @foreach(($item['stats'] ?? []) as $label => $value)
                <div class="text-center">
                    <p class="text-lg font-extrabold text-cyan-200">{{ $value }}</p>
                    <p class="text-[9px] uppercase tracking-widest text-white/40">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 flex items-center justify-between text-xs font-bold uppercase tracking-widest text-cyan-300 transition-colors group-hover:text-cyan-100">
            <span>Lihat Detail</span>
            <span class="grid h-7 w-7 place-items-center rounded-full bg-cyan/15 transition-transform duration-300 group-hover:translate-x-1 group-hover:bg-cyan/30">
                <i class="fas fa-arrow-right text-[10px]"></i>
            </span>
        </div>
    </a>
</article>
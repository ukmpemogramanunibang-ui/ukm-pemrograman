@php
    $exploreTarget = $exploreTarget ?? null;
    $isExploring   = $exploreTarget && session('owner.explore.active');
@endphp

@if($isExploring)
    {{-- BANNER MODE JELAJAH --}}
    <div class="sticky top-0 z-50 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="flex items-center justify-between px-4 py-2 text-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-eye"></i>
                <span>
                    Mode Jelajah — <b>{{ $exploreTarget->name }}</b>
                    <span class="opacity-75">({{ ucfirst($exploreRole ?? '-') }})</span>
                </span>
            </div>
            <form method="POST" action="{{ route('owner.settings.explore.exit') }}">
                @csrf
                <button type="submit"
                        class="px-3 py-1 rounded-lg bg-white/20 hover:bg-white/30 text-xs font-bold">
                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                </button>
            </form>
        </div>
    </div>
@endif

{{-- SIDEBAR & HEADER SEPERTI BIASA --}}
@include('partials.roles.ketua.sidebar', ['user' => $exploreTarget ?? auth()->user()])
@include('partials.roles.ketua.header',  ['user' => $exploreTarget ?? auth()->user()])
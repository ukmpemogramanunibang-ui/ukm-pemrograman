@extends('layouts.app')

@section('title', $poll->title)

@section('content')
<section class="min-h-screen bg-gradient-to-br from-midnight via-deepblue to-electric py-16">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 p-8 text-white">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('keputusan.index') }}" class="text-white/60 hover:text-white text-sm">← Keputusan</a>
                <button onclick="navigator.clipboard.writeText('{{ url()->current() }}').then(()=>alert('Link disalin!'))"
                        class="text-xs text-cyan-300 hover:text-cyan-200">🔗 Bagikan</button>
            </div>

            <h1 class="text-2xl md:text-3xl font-extrabold mb-2">{{ $poll->title }}</h1>
            @if($poll->description)
                <p class="text-white/60 mb-6 text-sm">{{ $poll->description }}</p>
            @endif

            <div class="text-xs text-white/50 mb-6">
                Status: <span class="text-white">{{ strtoupper($poll->status) }}</span> •
                Total suara: <span class="text-white">{{ $poll->totalVotes() }}</span>
                @if($poll->closes_at)
                    • Ditutup: {{ $poll->closes_at->diffForHumans() }}
                @endif
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-500/20 border border-green-400/40 text-green-100 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($poll->isOpen())
                <form method="POST" action="{{ route('keputusan.poll.vote', $poll->public_token) }}" class="space-y-3">
                    @csrf
                    @foreach($poll->options as $opt)
                        @php $pct = $poll->totalVotes() > 0 ? round(($opt->votes / $poll->totalVotes()) * 100, 1) : 0; @endphp
                        <label class="block cursor-pointer">
                            <div class="relative rounded-xl overflow-hidden border border-white/20 hover:border-cyan-400/50 transition">
                                <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-cyan/30 to-electric/30 transition-all"
                                     style="width: {{ $pct }}%"></div>
                                <div class="relative flex items-center justify-between px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <input type="{{ $poll->allow_multiple ? 'checkbox' : 'radio' }}"
                                               name="option_ids[]" value="{{ $opt->id }}"
                                               class="w-5 h-5 accent-cyan-400">
                                        <span class="font-medium">{{ $opt->label }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-cyan-300">{{ $pct }}% ({{ $opt->votes }})</span>
                                </div>
                            </div>
                        </label>
                    @endforeach

                    <button type="submit"
                            class="mt-6 w-full py-4 rounded-2xl bg-gradient-to-r from-cyan to-electric text-midnight font-extrabold text-lg hover:shadow-[0_0_40px_rgba(0,217,255,0.5)] transition">
                        🗳️ KIRIM SUARA
                    </button>
                </form>
            @else
                <div class="text-center py-6">
                    <p class="text-white/70 mb-4">Polling ini sudah ditutup.</p>
                    @foreach($poll->options as $opt)
                        @php $pct = $poll->totalVotes() > 0 ? round(($opt->votes / $poll->totalVotes()) * 100, 1) : 0; @endphp
                        <div class="text-left mb-2">
                            <div class="flex justify-between text-sm mb-1"><span>{{ $opt->label }}</span><span>{{ $pct }}%</span></div>
                            <div class="h-2 rounded-full bg-white/10 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-cyan to-electric" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
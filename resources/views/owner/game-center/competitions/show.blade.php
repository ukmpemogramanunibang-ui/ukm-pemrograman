{{-- resources/views/owner/game-center/competitions/show.blade.php --}}
@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-7xl">

        {{-- HEADER --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🏆</span>
                        <h1 class="text-2xl font-black text-slate-800 dark:text-white">{{ $competition->name }}</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $competition->status === 'active' ? 'bg-emerald-500/20 text-emerald-600' : 
                               ($competition->status === 'live' ? 'bg-red-500/20 text-red-600 animate-pulse' : 
                               ($competition->status === 'finished' ? 'bg-blue-500/20 text-blue-600' : 
                               'bg-slate-500/20 text-slate-600')) }}">
                            {{ ucfirst($competition->status) }}
                            @if($competition->is_live) 🔴 LIVE @endif
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1">{{ $competition->description }}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-slate-500">
                        <span>🎮 {{ $competition->game->name }}</span>
                        <span>👥 {{ $competition->participants->count() }}/{{ $competition->max_participants }} peserta</span>
                        <span>📅 {{ $competition->starts_at ? $competition->starts_at->format('d M Y') : 'Belum diatur' }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($competition->status === 'draft')
                        <a href="{{ route('owner.game.competitions.edit', $competition) }}" class="px-4 py-2 bg-amber-500 text-white rounded-xl font-bold hover:bg-amber-600 transition">✏️ Edit</a>
                        <form action="{{ route('owner.game.competitions.bracket.generate', $competition) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">🎯 Generate Bracket</button>
                        </form>
                    @endif
                    @if($competition->status === 'active')
                        <a href="{{ route('owner.game.competitions.live', $competition) }}" class="px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition animate-pulse">🔴 Live</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- SPONSOR --}}
        @if($competition->sponsors->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 mb-6">
            <div class="flex flex-wrap items-center gap-6 justify-center">
                <span class="text-sm text-slate-500">Sponsored by</span>
                @foreach($competition->sponsors as $sponsor)
                    <div class="flex items-center gap-2">
                        @if($sponsor->logo)
                            <img src="{{ asset('storage/' . $sponsor->logo) }}" alt="{{ $sponsor->name }}" class="h-8 object-contain">
                        @else
                            <span class="font-bold text-indigo-600">{{ $sponsor->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- BRACKET VISUAL --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>📋</span> Bracket Pertandingan
                </h2>
            </div>
            <div class="p-6 overflow-x-auto">
                @if(!empty($bracket))
                <div class="flex gap-8 min-w-[800px]">
                    @foreach($bracket as $roundNumber => $round)
                    <div class="flex-1 min-w-[180px]">
                        <div class="text-center font-bold text-sm text-slate-600 dark:text-slate-400 mb-3">
                            {{ $round['name'] }}
                        </div>
                        <div class="space-y-3">
                            @foreach($round['matches'] as $match)
                            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 cursor-pointer hover:shadow-md transition" 
                                 onclick="openMatchModal('{{ $competition->slug }}', {{ $match['id'] }})">
                                <div class="flex items-center justify-between gap-2 text-sm">
                                    <span class="font-medium truncate flex-1">
                                        {{ $match['participant_a'] ?? '—' }}
                                        @if($match['winner'] === $match['participant_a'])
                                            <span class="text-emerald-500">🏆</span>
                                        @endif
                                    </span>
                                    <span class="font-mono font-bold px-2 py-0.5 rounded {{ $match['score_a'] > $match['score_b'] ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $match['score_a'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2 text-sm border-t border-slate-200 dark:border-slate-700 pt-1 mt-1">
                                    <span class="font-medium truncate flex-1">
                                        {{ $match['participant_b'] ?? '—' }}
                                        @if($match['winner'] === $match['participant_b'])
                                            <span class="text-emerald-500">🏆</span>
                                        @endif
                                    </span>
                                    <span class="font-mono font-bold px-2 py-0.5 rounded {{ $match['score_b'] > $match['score_a'] ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $match['score_b'] }}
                                    </span>
                                </div>
                                <div class="mt-1 text-xs flex items-center justify-between">
                                    <span class="text-slate-400">Match #{{ $match['match_number'] }}</span>
                                    <span class="inline-block w-2 h-2 rounded-full 
                                        {{ $match['status'] === 'active' ? 'bg-yellow-500 animate-pulse' : 
                                           ($match['status'] === 'completed' ? 'bg-emerald-500' : 
                                           ($match['status'] === 'upcoming' ? 'bg-gray-300' : 'bg-red-500')) }}">
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-10 text-slate-500">
                    <p>Belum ada bracket. Generate bracket terlebih dahulu.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- RANKING --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span>🏅</span> Ranking Peserta
                </h2>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Peserta</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Total Skor</th>
                            <th class="px-4 py-3 text-left">XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ranking as $participant)
                        <tr class="border-t border-slate-200 dark:border-slate-700">
                            <td class="px-4 py-3 font-bold">
                                @if($participant->rank == 1) 🥇
                                @elseif($participant->rank == 2) 🥈
                                @elseif($participant->rank == 3) 🥉
                                @else #{{ $participant->rank }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $participant->user->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block w-2 h-2 rounded-full 
                                    {{ $participant->status === 'winner' ? 'bg-emerald-500' : 
                                       ($participant->status === 'active' ? 'bg-blue-500' : 
                                       ($participant->status === 'eliminated' ? 'bg-red-500' : 
                                       ($participant->status === 'registered' ? 'bg-gray-300' : 'bg-gray-500'))) }}">
                                </span>
                                {{ ucfirst($participant->status) }}
                            </td>
                            <td class="px-4 py-3 font-bold">{{ number_format($participant->total_score, 0) }}</td>
                            <td class="px-4 py-3">{{ number_format($participant->total_xp) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada peserta.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal Match Detail --}}
<div id="matchModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Detail Match</h2>
            <button onclick="closeMatchModal()" class="text-3xl text-slate-400 hover:text-slate-600 transition">&times;</button>
        </div>
        <div id="matchDetailContent" class="p-6">
            <div class="text-center py-8 text-slate-500">Memuat data...</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMatchModal(slug, matchId) {
    const modal = document.getElementById('matchModal');
    const content = document.getElementById('matchDetailContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    content.innerHTML = '<div class="text-center py-8 text-slate-500">Memuat data...</div>';

    fetch(`/owner/game/competitions/${slug}/match/${matchId}`)
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(() => {
            content.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat data.</div>';
        });
}

function closeMatchModal() {
    document.getElementById('matchModal').classList.add('hidden');
    document.getElementById('matchModal').classList.remove('flex');
}

// Close modal on click outside
document.getElementById('matchModal').addEventListener('click', function(e) {
    if (e.target === this) closeMatchModal();
});
</script>
@endpush
@endsection
{{-- ============================================================ --}}
{{-- REACTION TEST - PARTIAL                                    --}}
{{-- ============================================================ --}}
<div class="p-4 md:p-8 text-center">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">⚡ Reaction Test</h2>
    <div id="reaction-box" class="w-64 h-64 md:w-72 md:h-72 mx-auto rounded-3xl bg-indigo-500 flex items-center justify-center cursor-pointer transition-colors duration-300 select-none shadow-xl">
        <span id="reaction-text" class="text-white text-2xl font-bold">Klik untuk mulai</span>
    </div>
    <div class="mt-6 flex flex-wrap justify-center gap-6 md:gap-8 text-sm">
        <div>
            <span class="block text-slate-500 dark:text-slate-400">⚡ Waktu Reaksi</span>
            <span id="reaction-time" class="text-3xl font-black text-indigo-600 dark:text-indigo-400">0</span>
            <span class="text-xs text-slate-500">ms</span>
        </div>
        <div>
            <span class="block text-slate-500 dark:text-slate-400">🔄 Rounds</span>
            <span id="reaction-rounds" class="text-3xl font-black text-slate-800 dark:text-white">0</span>
            <span class="text-xs text-slate-500">/ {{ $gameData['settings']['rounds'] ?? 10 }}</span>
        </div>
        <div>
            <span class="block text-slate-500 dark:text-slate-400">📊 Rata-rata</span>
            <span id="reaction-avg" class="text-3xl font-black text-emerald-600 dark:text-emerald-400">0</span>
            <span class="text-xs text-slate-500">ms</span>
        </div>
    </div>
    <button id="reaction-reset" class="mt-6 px-6 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition font-bold">
        ⟳ Reset
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const box = document.getElementById('reaction-box');
    const text = document.getElementById('reaction-text');
    const timeDisplay = document.getElementById('reaction-time');
    const roundsDisplay = document.getElementById('reaction-rounds');
    const avgDisplay = document.getElementById('reaction-avg');

    let state = 'waiting';
    let startTime = 0;
    let round = 0;
    const maxRounds = {{ $gameData['settings']['rounds'] ?? 10 }};
    let times = [];
    let timeoutId = null;

    function resetRound() {
        state = 'waiting';
        text.textContent = 'Klik untuk mulai';
        box.style.backgroundColor = '#6366f1';
        if (timeoutId) { clearTimeout(timeoutId); timeoutId = null; }
    }

    function startRound() {
        state = 'ready';
        const delay = Math.floor(Math.random() * 3000) + 1000;
        text.textContent = '⏳ Tunggu...';
        box.style.backgroundColor = '#f59e0b';
        timeoutId = setTimeout(() => {
            if (state === 'ready') {
                state = 'click';
                startTime = performance.now();
                text.textContent = '🟢 KLIK!';
                box.style.backgroundColor = '#10b981';
            }
        }, delay);
    }

    box.addEventListener('click', function() {
        if (state === 'waiting') {
            if (round >= maxRounds) {
                const avg = times.length > 0 ? (times.reduce((a,b) => a+b, 0) / times.length) : 0;
                alert('🎉 Selesai!\nRata-rata: ' + avg.toFixed(0) + ' ms');
                // Simpan hasil
                fetch('{{ route("owner.game.games.submit-result") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        game_id: {{ $game->id }},
                        token: '{{ $token }}',
                        result: {
                            avg_reaction: avg,
                            rounds: round,
                            times: times,
                            mode: 'reaction',
                        }
                    })
                });
                return;
            }
            round++;
            roundsDisplay.textContent = round;
            startRound();
            return;
        }
        if (state === 'ready') {
            if (timeoutId) { clearTimeout(timeoutId); timeoutId = null; }
            text.textContent = '❌ Terlalu cepat!';
            box.style.backgroundColor = '#ef4444';
            setTimeout(resetRound, 800);
            return;
        }
        if (state === 'click') {
            const elapsed = performance.now() - startTime;
            times.push(elapsed);
            const avg = times.reduce((a,b) => a+b, 0) / times.length;
            timeDisplay.textContent = elapsed.toFixed(0);
            avgDisplay.textContent = avg.toFixed(0);
            text.textContent = elapsed.toFixed(0) + ' ms';
            box.style.backgroundColor = '#6366f1';
            setTimeout(() => {
                if (round < maxRounds) {
                    resetRound();
                } else {
                    text.textContent = '🎉 Selesai!';
                    box.style.backgroundColor = '#10b981';
                }
            }, 800);
        }
    });

    document.getElementById('reaction-reset').addEventListener('click', function() {
        if (timeoutId) { clearTimeout(timeoutId); timeoutId = null; }
        round = 0;
        times = [];
        roundsDisplay.textContent = '0';
        timeDisplay.textContent = '0';
        avgDisplay.textContent = '0';
        resetRound();
    });

    resetRound();
});
</script>
@endpush
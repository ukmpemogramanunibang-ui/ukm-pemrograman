@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $game->name }}</h1>
                <p class="text-sm text-slate-500">Puzzle Game</p>
            </div>
            <a href="{{ route('owner.game.games.show', $game) }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-xl">← Keluar</a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-6">
            <p class="text-center text-slate-500 mb-4">Klik kartu untuk mencari pasangan.</p>
            <div id="puzzle-grid" class="grid grid-cols-4 gap-4">
                <!-- Akan diisi JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('puzzle-grid');
    const cards = ['A','A','B','B','C','C','D','D','E','E','F','F'];
    // Acak kartu
    for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
    }

    let firstCard = null;
    let secondCard = null;
    let lock = false;
    let matched = 0;

    cards.forEach((value, index) => {
        const card = document.createElement('div');
        card.className = 'p-8 bg-slate-200 rounded-xl text-2xl font-bold flex items-center justify-center cursor-pointer hover:bg-indigo-200 transition';
        card.dataset.value = value;
        card.dataset.index = index;
        card.textContent = '?';
        card.addEventListener('click', function() {
            if (lock || card.classList.contains('matched') || card === firstCard) return;
            card.textContent = value;
            card.classList.add('bg-indigo-500', 'text-white');

            if (!firstCard) {
                firstCard = card;
            } else {
                secondCard = card;
                lock = true;
                if (firstCard.dataset.value === secondCard.dataset.value) {
                    firstCard.classList.add('matched');
                    secondCard.classList.add('matched');
                    matched++;
                    firstCard = null;
                    secondCard = null;
                    lock = false;
                    if (matched === cards.length / 2) {
                        alert('🎉 Puzzle selesai!');
                    }
                } else {
                    setTimeout(() => {
                        firstCard.textContent = '?';
                        secondCard.textContent = '?';
                        firstCard.classList.remove('bg-indigo-500', 'text-white');
                        secondCard.classList.remove('bg-indigo-500', 'text-white');
                        firstCard = null;
                        secondCard = null;
                        lock = false;
                    }, 1000);
                }
            }
        });
        grid.appendChild(card);
    });
});
</script>
@endsection
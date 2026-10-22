@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $game->name }}</h1>
                <p class="text-sm text-slate-500">Geometry Dash</p>
            </div>
            <a href="{{ route('owner.game.games.show', $game) }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-xl">← Keluar</a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-6">
            <canvas id="gameCanvas" width="800" height="400" class="w-full border rounded-xl bg-slate-100"></canvas>
            <div class="text-center mt-4">
                <button id="startGameBtn" class="px-8 py-3 bg-indigo-600 text-white rounded-xl">Mulai Game</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    let startBtn = document.getElementById('startGameBtn');

    let gameRunning = false;
    let playerX = 50;
    let playerY = 300;
    let playerSpeed = 3;
    let obstacles = [];
    let score = 0;
    let animationId;

    function createObstacle() {
        obstacles.push({
            x: canvas.width,
            y: 300,
            width: 20,
            height: 50,
            speed: playerSpeed
        });
    }

    function update() {
        // Bersihkan canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        // Gambar player
        ctx.fillStyle = '#6366f1';
        ctx.fillRect(playerX, playerY, 30, 30);
        // Gambar obstacles
        ctx.fillStyle = '#ef4444';
        obstacles.forEach(obs => {
            obs.x -= obs.speed;
            ctx.fillRect(obs.x, obs.y, obs.width, obs.height);
            // Deteksi tabrakan
            if (obs.x < playerX + 30 && obs.x + obs.width > playerX && obs.y < playerY + 30 && obs.y + obs.height > playerY) {
                gameOver();
                return;
            }
            if (obs.x + obs.width < 0) {
                score++;
            }
        });
        obstacles = obstacles.filter(obs => obs.x + obs.width > 0);
        // Generate obstacle baru
        if (Math.random() < 0.01) {
            createObstacle();
        }
        // Score
        ctx.fillStyle = '#000';
        ctx.font = '20px sans-serif';
        ctx.fillText('Score: ' + score, 10, 30);

        if (gameRunning) {
            requestAnimationFrame(update);
        }
    }

    function gameOver() {
        gameRunning = false;
        cancelAnimationFrame(animationId);
        alert('Game Over! Score: ' + score);
        startBtn.style.display = 'block';
    }

    startBtn.addEventListener('click', function() {
        gameRunning = true;
        startBtn.style.display = 'none';
        score = 0;
        obstacles = [];
        playerY = 300;
        playerSpeed = 3;
        requestAnimationFrame(update);
    });

    // Kontrol keyboard untuk lompat
    document.addEventListener('keydown', function(e) {
        if (e.code === 'Space' && gameRunning) {
            playerY -= 80;
            setTimeout(() => { playerY = 300; }, 300);
        }
    });
});
</script>
@endsection
{{-- ============================================================ --}}
{{-- TYPING GAME - FULL FEATURES                                  --}}
{{-- ============================================================ --}}

<div id="typing-game" class="p-4 md:p-8" data-game-id="{{ $game->id }}" data-token="{{ $token }}">

    {{-- STATISTIK UTAMA --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">⏱️ Waktu</p>
            <p id="timer-display" class="text-2xl md:text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $gameData['settings']['duration'] ?? 60 }}</p>
            <p class="text-xs text-slate-500">detik</p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">🚀 WPM</p>
            <p id="gross-wpm-display" class="text-2xl md:text-3xl font-black text-cyan-600 dark:text-cyan-400">0</p>
            <p class="text-xs text-slate-500">kata/menit</p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">🎯 Akurasi</p>
            <p id="accuracy-display" class="text-2xl md:text-3xl font-black text-amber-600 dark:text-amber-400">0%</p>
            <p class="text-xs text-slate-500">dari total teks</p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">📊 Progress</p>
            <p id="progress-display" class="text-2xl md:text-3xl font-black text-purple-600 dark:text-purple-400">0%</p>
            <p class="text-xs text-slate-500">teks selesai</p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
            <p class="text-xs text-slate-500 dark:text-slate-400">🏆 Skor</p>
            <p id="score-display" class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400">0</p>
            <p class="text-xs text-slate-500">poin</p>
        </div>
    </div>

    {{-- STATISTIK DETAIL --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-6 text-sm">
        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
            <span class="text-slate-500 dark:text-slate-400">✅ Benar</span>
            <span id="correct-chars" class="font-bold text-emerald-600 dark:text-emerald-400">0</span>
        </div>
        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
            <span class="text-slate-500 dark:text-slate-400">❌ Salah</span>
            <span id="wrong-chars" class="font-bold text-red-600 dark:text-red-400">0</span>
        </div>
        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
            <span class="text-slate-500 dark:text-slate-400">⌫ Backspace</span>
            <span id="backspace-count" class="font-bold text-orange-600 dark:text-orange-400">0</span>
        </div>
        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
            <span class="text-slate-500 dark:text-slate-400">📝 Total Diketik</span>
            <span id="total-chars" class="font-bold text-slate-700 dark:text-slate-300">0</span>
        </div>
        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
            <span class="text-slate-500 dark:text-slate-400">📄 Total Teks</span>
            <span id="total-text-length" class="font-bold text-slate-700 dark:text-slate-300">0</span>
        </div>
    </div>

    {{-- TEKS SUMBER --}}
    <div id="text-display-wrapper" class="relative">
        <div id="text-display"
             class="bg-slate-100 dark:bg-slate-800 rounded-xl p-4 md:p-6 font-mono text-base md:text-lg leading-relaxed min-h-[140px] select-none whitespace-pre-wrap break-words border border-slate-200 dark:border-slate-700">
            {{-- Akan diisi JavaScript --}}
        </div>
        <div id="text-progress-bar" class="mt-2 h-1 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
            <div id="text-progress-fill" class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    {{-- AREA INPUT --}}
    <div class="mt-4">
        <textarea id="typing-input"
                  class="w-full rounded-xl border-2 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white font-mono text-base md:text-lg focus:ring-4 focus:ring-indigo-500/30 focus:border-indigo-500 transition resize-none"
                  rows="4"
                  placeholder="Klik tombol 'Mulai' untuk mulai mengetik..."
                  disabled></textarea>

        <div class="mt-4 flex flex-wrap gap-3">
            <button id="start-btn"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/30 flex items-center gap-2">
                <span>▶</span> Mulai
            </button>
            <button id="reset-btn"
                    class="px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition flex items-center gap-2">
                <span>⟳</span> Reset
            </button>
            <button id="copy-text-btn"
                    class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold transition flex items-center gap-2 text-sm">
                <span>📋</span> Salin Teks
            </button>
        </div>
    </div>

    {{-- STATUS --}}
    <div id="game-status" class="mt-4 text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
        <span id="status-dot" class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
        <span id="status-text">Belum dimulai</span>
    </div>

    {{-- MULTIPLAYER (simulasi) --}}
    @if(isset($gameData['settings']['multiplayer']['enabled']) && $gameData['settings']['multiplayer']['enabled'])
    <div class="mt-6 border-t border-slate-200 dark:border-slate-700 pt-6">
        <h3 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
            <span>👥</span> Pemain Lain
        </h3>
        <div id="multiplayer-players" class="grid grid-cols-1 md:grid-cols-2 gap-2">
            {{-- Akan diisi JavaScript --}}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const container = document.getElementById('typing-game');
    if (!container) return;

    // ==============================================================
    // KONFIGURASI
    // ==============================================================
    const gameId = {{ $game->id }};
    const token = '{{ $token }}';
    const userId = {{ auth()->id() ?? 0 }};
    const duration = {{ $gameData['settings']['duration'] ?? 60 }};
    const isMultiplayer = {{ isset($gameData['settings']['multiplayer']['enabled']) && $gameData['settings']['multiplayer']['enabled'] ? 'true' : 'false' }};
    const originalText = `{{ addslashes($gameData['settings']['custom_text'] ?? 'Ini adalah contoh teks untuk latihan mengetik. Cobalah untuk mengetik dengan cepat dan akurat. Semakin cepat dan akurat, semakin tinggi skor Anda.') }}`;

    // ==============================================================
    // DOM ELEMENTS
    // ==============================================================
    const textDisplay = document.getElementById('text-display');
    const inputArea = document.getElementById('typing-input');
    const startBtn = document.getElementById('start-btn');
    const resetBtn = document.getElementById('reset-btn');
    const copyBtn = document.getElementById('copy-text-btn');

    const timerDisplay = document.getElementById('timer-display');
    const grossWpmDisplay = document.getElementById('gross-wpm-display');
    const accuracyDisplay = document.getElementById('accuracy-display');
    const progressDisplay = document.getElementById('progress-display');
    const scoreDisplay = document.getElementById('score-display');
    const correctCharsDisplay = document.getElementById('correct-chars');
    const wrongCharsDisplay = document.getElementById('wrong-chars');
    const backspaceDisplay = document.getElementById('backspace-count');
    const totalCharsDisplay = document.getElementById('total-chars');
    const totalTextLengthDisplay = document.getElementById('total-text-length');
    const statusDot = document.getElementById('status-dot');
    const statusText = document.getElementById('status-text');
    const progressFill = document.getElementById('text-progress-fill');

    // ==============================================================
    // STATE
    // ==============================================================
    let timeLeft = duration;
    let timerInterval = null;
    let isStarted = false;
    let isFinished = false;
    let isEnding = false;
    let startTimestamp = 0;

    let totalKeystrokes = 0;
    let correctKeystrokes = 0;
    let wrongKeystrokes = 0;
    let backspaceCount = 0;
    let lastCharTime = 0;
    let slowKeys = {};
    let allKeys = {};
    let totalTextLength = originalText.length;
    let score = 0;
    let penalty = 0;
    let violations = 0;

    // ==============================================================
    // FUNGSI RENDER TEKS
    // ==============================================================
    function renderText(typed) {
        if (!originalText) return;
        const typedLen = typed.length;
        let html = '';
        for (let i = 0; i < originalText.length; i++) {
            const char = originalText[i];
            if (i < typedLen) {
                const isCorrect = typed[i] === char;
                if (isCorrect) {
                    html += `<span class="text-emerald-500 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-0.5 rounded">${escapeHtml(char)}</span>`;
                } else {
                    html += `<span class="text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-950/30 px-0.5 rounded line-through">${escapeHtml(char)}</span>`;
                }
            } else {
                html += `<span class="text-slate-400 dark:text-slate-500">${escapeHtml(char)}</span>`;
            }
        }
        textDisplay.innerHTML = html;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ==============================================================
    // FUNGSI HITUNG STATISTIK
    // ==============================================================
    function countCorrect(typed) {
        let count = 0;
        const len = Math.min(typed.length, originalText.length);
        for (let i = 0; i < len; i++) {
            if (typed[i] === originalText[i]) count++;
        }
        return count;
    }

    function updateStats() {
        const typed = inputArea.value;
        const len = typed.length;
        const total = originalText.length;

        // Progress
        const pct = total > 0 ? Math.min(Math.round((len / total) * 100), 100) : 0;
        progressDisplay.textContent = pct + '%';
        progressFill.style.width = pct + '%';

        // Correct / Wrong
        const correct = countCorrect(typed);
        const wrong = len - correct;
        correctKeystrokes = correct;
        wrongKeystrokes = wrong;
        correctCharsDisplay.textContent = correct;
        wrongCharsDisplay.textContent = wrong;
        totalCharsDisplay.textContent = len;
        totalTextLengthDisplay.textContent = total;

        // Akurasi
        const totalTyped = correct + wrong;
        const accuracy = total > 0 ? Math.round((totalTyped / total) * 100) : 0;
        accuracyDisplay.textContent = accuracy + '%';

        // WPM
        const elapsedSeconds = (duration - timeLeft);
        const elapsedMinutes = Math.max(0.01, elapsedSeconds / 60);
        const grossWpm = elapsedMinutes > 0 ? Math.round((len / 5) / elapsedMinutes) : 0;
        grossWpmDisplay.textContent = grossWpm;

        // Skor
        const points = GAME_CONFIG?.points || {};
        const pointCorrect = points.correct || 10;
        const pointWrong = points.wrong || 0;
        const pointEmpty = points.empty || 0;
        const maxPoints = points.max_points || 1000;

        // Skor = (benar * poin_benar) - (salah * poin_salah) - penalty
        let newScore = (correct * pointCorrect) - (wrong * pointWrong) - penalty;
        if (newScore > maxPoints) newScore = maxPoints;
        if (newScore < 0) newScore = 0;
        score = newScore;
        scoreDisplay.textContent = score;

        // Timer
        timerDisplay.textContent = Math.max(0, timeLeft);
    }

    // ==============================================================
    // FUNGSI GAME
    // ==============================================================
    function startGame() {
        if (isStarted || isFinished || isEnding) return;
        if (!originalText || originalText.length === 0) {
            alert('❌ Teks kosong! Silakan reset dan coba lagi.');
            return;
        }

        isStarted = true;
        startTimestamp = Date.now();
        lastCharTime = startTimestamp;
        inputArea.disabled = false;
        inputArea.value = '';
        inputArea.focus();

        startBtn.innerHTML = '<span>⏹</span> Selesai';
        startBtn.className = 'px-6 py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-red-500/30 flex items-center gap-2';

        setStatus('running', '🟢 Sedang berjalan...');

        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            timeLeft--;
            timerDisplay.textContent = Math.max(0, timeLeft);

            if (timeLeft <= 3 && timeLeft > 0) {
                timerDisplay.classList.add('text-red-600', 'dark:text-red-400', 'animate-pulse');
            } else {
                timerDisplay.classList.remove('text-red-600', 'dark:text-red-400', 'animate-pulse');
            }

            if (timeLeft <= 0) {
                if (!isEnding && !isFinished) {
                    startEndingProcess();
                }
            }
        }, 1000);

        updateStats();
    }

    function startEndingProcess() {
        if (isEnding || isFinished) return;
        isEnding = true;
        isStarted = false;

        inputArea.disabled = true;

        let countdown = 3;
        setStatus('ending', `⏳ Game selesai dalam ${countdown}...`);

        const endTimer = setInterval(() => {
            countdown--;
            if (countdown > 0) {
                setStatus('ending', `⏳ Game selesai dalam ${countdown}...`);
                timerDisplay.classList.add('animate-bounce');
            } else {
                clearInterval(endTimer);
                timerDisplay.classList.remove('animate-bounce');
                endGame('⏰ Waktu habis!');
            }
        }, 1000);
    }

    function endGame(reason = '⏹ Game selesai') {
        if (isFinished) return;
        isFinished = true;
        isStarted = false;
        isEnding = false;

        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        inputArea.disabled = true;
        startBtn.innerHTML = '<span>🔄</span> Main Lagi';
        startBtn.className = 'px-6 py-3 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-emerald-500/30 flex items-center gap-2';

        setStatus('finished', '✅ ' + reason);

        updateStats();

        // Siapkan data hasil
        const typed = inputArea.value;
        const correct = countCorrect(typed);
        const wrong = typed.length - correct;
        const elapsedSeconds = duration - timeLeft;
        const elapsedMinutes = Math.max(0.01, elapsedSeconds / 60);
        const grossWpm = Math.round((typed.length / 5) / elapsedMinutes);
        const totalTyped = correct + wrong;
        const accuracy = originalText.length > 0 ? Math.round((totalTyped / originalText.length) * 100) : 0;

        const resultData = {
            wpm: grossWpm,
            accuracy: accuracy,
            total_words: typed.trim() ? typed.trim().split(/\s+/).filter(w => w.length > 0).length : 0,
            correct_chars: correct,
            wrong_chars: wrong,
            backspace: backspaceCount,
            total_chars: typed.length,
            total_text_length: originalText.length,
            duration_used: elapsedSeconds,
            mode: 'practice',
            is_multiplayer: isMultiplayer,
            slow_keys: slowKeys,
            keyboard_map: allKeys,
            score: score,
            penalty: penalty,
            violations: violations,
        };

        // Kirim ke server
        fetch('{{ route("owner.game.games.submit-result") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                game_id: gameId,
                token: token,
                result: resultData,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.success) {
                showResultPopup(resultData);
            }
        })
        .catch(error => {
            console.error('Error saving result:', error);
            showResultPopup(resultData);
        });
    }

    function resetGame() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        isStarted = false;
        isFinished = false;
        isEnding = false;
        timeLeft = duration;
        startTimestamp = 0;
        totalKeystrokes = 0;
        correctKeystrokes = 0;
        wrongKeystrokes = 0;
        backspaceCount = 0;
        lastCharTime = 0;
        slowKeys = {};
        allKeys = {};
        score = 0;
        penalty = 0;
        violations = 0;

        inputArea.value = '';
        inputArea.disabled = true;

        timerDisplay.textContent = duration;
        timerDisplay.classList.remove('text-red-600', 'dark:text-red-400', 'animate-pulse', 'animate-bounce');
        grossWpmDisplay.textContent = '0';
        accuracyDisplay.textContent = '0%';
        progressDisplay.textContent = '0%';
        progressFill.style.width = '0%';
        scoreDisplay.textContent = '0';
        correctCharsDisplay.textContent = '0';
        wrongCharsDisplay.textContent = '0';
        backspaceDisplay.textContent = '0';
        totalCharsDisplay.textContent = '0';
        totalTextLengthDisplay.textContent = originalText.length;

        renderText('');

        startBtn.innerHTML = '<span>▶</span> Mulai';
        startBtn.className = 'px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/30 flex items-center gap-2';

        setStatus('idle', '⏸ Belum dimulai');
    }

    function setStatus(type, text) {
        const colors = {
            idle: 'bg-slate-400',
            running: 'bg-emerald-500',
            ending: 'bg-orange-500',
            finished: 'bg-indigo-500',
            error: 'bg-red-500'
        };
        statusDot.className = 'inline-block w-2 h-2 rounded-full ' + (colors[type] || 'bg-slate-400');
        statusText.textContent = text;
    }

    // ==============================================================
    // FUNGSI HANDLE SKIP (dipanggil oleh anti-cheat)
    // ==============================================================
    window.handleSkip = function() {
        if (isFinished || isEnding) return;
        if (isStarted) {
            // Skip soal: tidak dihitung
            setStatus('error', '⏭️ Soal dilewati karena pelanggaran.');
            penalty += GAME_CONFIG?.anti_cheat?.penalty || 5;
            violations++;
            // Hentikan game
            endGame('⏭️ Game dihentikan karena pelanggaran.');
        }
    };

    // ==============================================================
    // FUNGSI PAUSE / RESUME TIMER (dipanggil anti-cheat)
    // ==============================================================
    window._pauseTimers = function() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
        setStatus('error', '⏸️ Game dijeda.');
    };

    window._resumeTimers = function() {
        if (isStarted && !isFinished && !isEnding) {
            timerInterval = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = Math.max(0, timeLeft);
                if (timeLeft <= 0) {
                    if (!isEnding && !isFinished) {
                        startEndingProcess();
                    }
                }
            }, 1000);
            setStatus('running', '🟢 Sedang berjalan...');
        }
    };

    // ==============================================================
    // FUNGSI SHOW RESULT
    // ==============================================================
    function showResultPopup(data) {
        const existing = document.querySelector('.result-popup');
        if (existing) existing.remove();

        const popup = document.createElement('div');
        popup.className = 'result-popup fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn overflow-y-auto';
        popup.innerHTML = `
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-4xl w-full p-6 md:p-8 border border-slate-200 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">🏆</div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Hasil Permainan</h2>
                    <p class="text-sm text-slate-500">Game selesai! Berikut statistik Anda.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-500">WPM</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">${data.wpm}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-500">Akurasi</p>
                        <p class="text-2xl font-black text-amber-600 dark:text-amber-400">${data.accuracy}%</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-500">Skor</p>
                        <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">${data.score}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-500">Pelanggaran</p>
                        <p class="text-2xl font-black text-red-600">${data.violations}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-6 text-sm">
                    <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                        <span class="text-slate-500">✅ Benar</span>
                        <span class="font-bold text-emerald-600">${data.correct_chars}</span>
                    </div>
                    <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                        <span class="text-slate-500">❌ Salah</span>
                        <span class="font-bold text-red-600">${data.wrong_chars}</span>
                    </div>
                    <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                        <span class="text-slate-500">⌫ Backspace</span>
                        <span class="font-bold text-orange-600">${data.backspace}</span>
                    </div>
                    <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                        <span class="text-slate-500">📝 Total Kata</span>
                        <span class="font-bold text-slate-700">${data.total_words}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mt-4">
                    <button onclick="this.closest('.result-popup').remove(); document.getElementById('reset-btn').click();"
                            class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/20">
                        🔄 Main Lagi
                    </button>
                    <button onclick="this.closest('.result-popup').remove(); window.location.href='{{ route('owner.game.games.show', $game) }}'"
                            class="flex-1 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition">
                        ✕ Tutup
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(popup);

        popup.addEventListener('click', function(e) {
            if (e.target === this) this.remove();
        });
    }

    // ==============================================================
    // EVENT LISTENERS
    // ==============================================================

    inputArea.addEventListener('input', function() {
        if (!isStarted || isFinished || isEnding) return;

        const typed = this.value;
        const prevLen = parseInt(this.dataset.prevLen || '0');
        const len = typed.length;

        if (len < prevLen) {
            backspaceCount++;
            backspaceDisplay.textContent = backspaceCount;
        }
        this.dataset.prevLen = len;

        if (len > 0) {
            const lastChar = typed[len-1];
            totalKeystrokes++;
            const now = Date.now();
            if (lastCharTime > 0) {
                const diff = now - lastCharTime;
                if (diff > 300) {
                    slowKeys[lastChar] = (slowKeys[lastChar] || 0) + diff;
                }
            }
            lastCharTime = now;

            const key = lastChar.toLowerCase();
            if (key.match(/[a-z]/)) {
                allKeys[key] = (allKeys[key] || 0) + 1;
            }
        }

        renderText(typed);
        updateStats();

        // Cek selesai
        if (typed.length >= originalText.length && originalText.length > 0) {
            const correct = countCorrect(typed);
            if (correct === originalText.length) {
                if (!isEnding && !isFinished) {
                    setStatus('finished', '🎉 Selesai! Semua teks berhasil diketik.');
                    endGame('🎉 Selesai! Semua teks berhasil diketik.');
                }
            }
        }
    });

    startBtn.addEventListener('click', function() {
        if (this.textContent.includes('Mulai')) {
            startGame();
        } else if (this.textContent.includes('Main Lagi')) {
            resetGame();
            setTimeout(startGame, 300);
        } else {
            endGame('⏹ Dihentikan oleh pengguna');
        }
    });

    resetBtn.addEventListener('click', resetGame);

    copyBtn.addEventListener('click', function() {
        if (originalText) {
            navigator.clipboard.writeText(originalText)
                .then(() => {
                    const original = this.innerHTML;
                    this.innerHTML = '✅ Tersalin!';
                    setTimeout(() => { this.innerHTML = original; }, 2000);
                })
                .catch(() => {
                    const textarea = document.createElement('textarea');
                    textarea.value = originalText;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    textarea.remove();
                    const original = this.innerHTML;
                    this.innerHTML = '✅ Tersalin!';
                    setTimeout(() => { this.innerHTML = original; }, 2000);
                });
        }
    });

    // ==============================================================
    // MULTIPLAYER (simulasi)
    // ==============================================================
    if (isMultiplayer) {
        const container = document.getElementById('multiplayer-players');
        const dummyPlayers = [
            { id: 2, name: 'Budi', role: 'Anggota', wpm: 45, accuracy: 92 },
            { id: 3, name: 'Citra', role: 'Pengurus', wpm: 38, accuracy: 88 },
            { id: 4, name: 'Dewi', role: 'Anggota', wpm: 52, accuracy: 95 },
        ];
        dummyPlayers.forEach(p => {
            const div = document.createElement('div');
            div.className = 'player-item flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700';
            div.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-sm font-bold">${p.name[0]}</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">${p.name}</span>
                    <span class="text-xs text-slate-500">(${p.role})</span>
                </div>
                <div class="text-right">
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">${p.wpm} WPM</span>
                    <span class="text-sm text-slate-500 ml-2">${p.accuracy}%</span>
                </div>
            `;
            container.appendChild(div);
        });
    }

    // ==============================================================
    // INISIALISASI
    // ==============================================================
    totalTextLengthDisplay.textContent = originalText.length;
    resetGame();

    // Expose finishGame untuk anti-cheat
    window.finishGame = function() {
        if (!isFinished) {
            endGame('⛔ Game dihentikan oleh sistem keamanan.');
        }
    };

    console.log('⌨️ Typing Game Loaded');
});
</script>
@endpush
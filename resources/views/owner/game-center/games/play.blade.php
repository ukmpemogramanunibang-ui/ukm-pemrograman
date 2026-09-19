{{-- resources/views/game/play.blade.php --}}
@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-slate-50 dark:from-slate-950 dark:via-indigo-950/10 dark:to-slate-950 py-6">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">

        {{-- HEADER GAME --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl p-4 md:p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-3xl shadow-lg shadow-indigo-500/20">
                        {{ $game->icon ?? '⌨️' }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 dark:text-white">{{ $game->name }}</h1>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                                {{ ucfirst($game->game_type) }}
                            </span>
                            @if($game->difficulty)
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full capitalize">
                                {{ $game->difficulty }}
                            </span>
                            @endif
                            @php
                                $isMultiplayer = $gameData['settings']['multiplayer']['enabled'] ?? false;
                            @endphp
                            @if($isMultiplayer)
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full">
                                <i class="fas fa-users mr-1"></i> Multiplayer
                            </span>
                            @else
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-full">
                                <i class="fas fa-user mr-1"></i> Single Player
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Pemain</p>
                        <p class="font-bold text-sm text-slate-800 dark:text-white">{{ auth()->user()->name ?? 'Guest' }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->level->name ?? 'Anggota' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                    </div>
                    <a href="{{ route('owner.game.games.show', $game) }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition text-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- KONTEN GAME UTAMA --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">

            @if($game->game_type === 'typing')
                {{-- TYPING GAME LENGKAP --}}
                <div id="typing-game" class="p-4 md:p-8">

                    {{-- STATISTIK UTAMA --}}
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-500">⏱️ Waktu</p>
                            <p id="timer-display" class="text-2xl font-black text-indigo-600 dark:text-indigo-400">60</p>
                            <p class="text-xs text-slate-500">detik</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-500">📊 Progress</p>
                            <p id="progress-display" class="text-2xl font-black text-purple-600 dark:text-purple-400">0%</p>
                            <p class="text-xs text-slate-500">teks selesai</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-500">✅ Akurasi</p>
                            <p id="accuracy-display" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">100%</p>
                            <p class="text-xs text-slate-500">dari total teks</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-500">🚀 WPM</p>
                            <p id="wpm-display" class="text-2xl font-black text-cyan-600 dark:text-cyan-400">0</p>
                            <p class="text-xs text-slate-500">kata/menit (benar)</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-500">⌨️ Karakter</p>
                            <p id="chars-display" class="text-2xl font-black text-slate-700 dark:text-slate-300">0/0</p>
                            <p class="text-xs text-slate-500">benar / total</p>
                        </div>
                    </div>

                    {{-- STATISTIK DETAIL --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-6 text-sm">
                        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                            <span class="text-slate-500 dark:text-slate-400">⌫ Backspace</span>
                            <span id="backspace-count" class="font-bold text-orange-600 dark:text-orange-400">0</span>
                        </div>
                        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                            <span class="text-slate-500 dark:text-slate-400">❌ Salah</span>
                            <span id="wrong-chars" class="font-bold text-red-600 dark:text-red-400">0</span>
                        </div>
                        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                            <span class="text-slate-500 dark:text-slate-400">⏸️ Jeda</span>
                            <span id="pause-count" class="font-bold text-amber-600 dark:text-amber-400">0</span>
                        </div>
                        <div class="flex justify-between items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/40 rounded-lg">
                            <span class="text-slate-500 dark:text-slate-400">📝 Kata</span>
                            <span id="word-count" class="font-bold text-slate-700 dark:text-slate-300">0</span>
                        </div>
                    </div>

                    {{-- TEKS SUMBER --}}
                    <div id="text-display-wrapper" class="relative">
                        <div id="text-display"
                             class="bg-slate-100 dark:bg-slate-800 rounded-xl p-4 md:p-6 font-mono text-base md:text-lg leading-relaxed min-h-[140px] select-none whitespace-pre-wrap break-words border border-slate-200 dark:border-slate-700">
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
                                ▶ Mulai
                            </button>
                            <button id="reset-btn"
                                    class="px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition flex items-center gap-2">
                                ⟳ Reset
                            </button>
                            <button id="copy-text-btn"
                                    class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold transition flex items-center gap-2 text-sm">
                                📋 Salin Teks
                            </button>
                        </div>
                    </div>

                    {{-- STATUS --}}
                    <div id="game-status" class="mt-4 text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                        <span id="status-dot" class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
                        <span id="status-text">Belum dimulai</span>
                    </div>
                </div>

                @push('scripts')
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    'use strict';

                    // ==============================================================
                    // KONFIGURASI
                    // ==============================================================
                    const gameId = {{ $game->id }};
                    const token = '{{ $token }}';
                    const userId = {{ auth()->id() ?? 0 }};
                    const duration = {{ $gameData['settings']['duration'] ?? 60 }};

                    // ==============================================================
                    // DOM ELEMENTS
                    // ==============================================================
                    const textDisplay = document.getElementById('text-display');
                    const inputArea = document.getElementById('typing-input');
                    const startBtn = document.getElementById('start-btn');
                    const resetBtn = document.getElementById('reset-btn');
                    const copyBtn = document.getElementById('copy-text-btn');

                    const timerDisplay = document.getElementById('timer-display');
                    const progressDisplay = document.getElementById('progress-display');
                    const accuracyDisplay = document.getElementById('accuracy-display');
                    const wpmDisplay = document.getElementById('wpm-display');
                    const charsDisplay = document.getElementById('chars-display');
                    const backspaceDisplay = document.getElementById('backspace-count');
                    const wrongCharsDisplay = document.getElementById('wrong-chars');
                    const pauseCountDisplay = document.getElementById('pause-count');
                    const wordCountDisplay = document.getElementById('word-count');
                    const statusDot = document.getElementById('status-dot');
                    const statusText = document.getElementById('status-text');
                    const progressFill = document.getElementById('text-progress-fill');

                    // ==============================================================
                    // STATE
                    // ==============================================================
                    let originalText = `{{ addslashes($gameData['settings']['custom_text'] ?? 'Ini adalah contoh teks untuk latihan mengetik. Cobalah untuk mengetik dengan cepat dan akurat. Semakin cepat dan akurat, semakin tinggi skor Anda.') }}`;
                    const totalChars = originalText.length;

                    let timeLeft = duration;
                    let timerInterval = null;
                    let isStarted = false;
                    let isFinished = false;
                    let startTimestamp = 0;
                    let lastCharTime = 0;
                    let totalPauseDuration = 0;
                    let pauseStart = 0;

                    let backspaceCount = 0;
                    let correctChars = 0;
                    let wrongChars = 0;
                    let typedChars = 0;
                    let totalKeystrokes = 0;
                    let wordCount = 0;
                    let slowKeys = {};
                    let allKeys = {};

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

                        const activeSpan = textDisplay.querySelector('.text-emerald-500, .text-red-500');
                        if (activeSpan) {
                            activeSpan.scrollIntoView?.({ block: 'nearest', behavior: 'smooth' });
                        }
                    }

                    function escapeHtml(str) {
                        const div = document.createElement('div');
                        div.textContent = str;
                        return div.innerHTML;
                    }

                    // ==============================================================
                    // FUNGSI HITUNG STATISTIK - AKURASI BERDASARKAN TOTAL TEKS
                    // ==============================================================
                    function updateStats() {
                        const typed = inputArea.value;
                        const len = typed.length;
                        const total = originalText.length;

                        // Hitung karakter benar dari yang sudah diketik
                        let correct = 0;
                        for (let i = 0; i < Math.min(len, total); i++) {
                            if (typed[i] === originalText[i]) correct++;
                        }
                        correctChars = correct;
                        wrongChars = len - correct;
                        typedChars = len;

                        // Progress = berapa persen teks yang sudah diketik (termasuk salah)
                        const progressPct = total > 0 ? Math.min(100, Math.round((len / total) * 100)) : 0;
                        progressDisplay.textContent = progressPct + '%';
                        progressFill.style.width = progressPct + '%';

                        // Akurasi = (karakter benar / total karakter dalam teks sumber) × 100%
                        // Ini mengukur seberapa akurat dari keseluruhan teks
                        const accuracyPct = total > 0 ? Math.min(100, Math.round((correct / total) * 100)) : 100;
                        accuracyDisplay.textContent = accuracyPct + '%';

                        // WPM = (karakter benar / 5) / (waktu efektif mengetik dalam menit)
                        // Waktu efektif = waktu total - jeda (pause)
                        const elapsedSeconds = (duration - timeLeft);
                        const effectiveSeconds = Math.max(0.1, elapsedSeconds - (totalPauseDuration / 1000));
                        const effectiveMinutes = effectiveSeconds / 60;
                        const wpm = effectiveMinutes > 0 ? Math.round((correct / 5) / effectiveMinutes) : 0;
                        wpmDisplay.textContent = wpm;

                        // Karakter
                        charsDisplay.textContent = correct + '/' + total;

                        // Word count (kata yang sudah diketik dengan benar)
                        const words = typed.trim() ? typed.trim().split(/\s+/).filter(w => w.length > 0).length : 0;
                        wordCount = words;
                        wordCountDisplay.textContent = wordCount;

                        // Timer
                        timerDisplay.textContent = Math.max(0, timeLeft);
                    }

                    // ==============================================================
                    // FUNGSI GAME
                    // ==============================================================
                    function startGame() {
                        if (isStarted || isFinished) return;
                        if (!originalText || originalText.length === 0) {
                            alert('❌ Teks kosong! Silakan reset dan coba lagi.');
                            return;
                        }

                        isStarted = true;
                        startTimestamp = Date.now();
                        lastCharTime = startTimestamp;
                        pauseStart = 0;
                        totalPauseDuration = 0;

                        inputArea.disabled = false;
                        inputArea.value = '';
                        inputArea.focus();

                        startBtn.innerHTML = '⏹ Selesai';
                        startBtn.className = 'px-6 py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-red-500/30 flex items-center gap-2';

                        setStatus('running', '🟢 Sedang berjalan...');

                        if (timerInterval) clearInterval(timerInterval);
                        timerInterval = setInterval(() => {
                            timeLeft--;
                            timerDisplay.textContent = Math.max(0, timeLeft);

                            // Deteksi jeda (jika tidak ada input dalam 2 detik)
                            const now = Date.now();
                            if (lastCharTime > 0 && now - lastCharTime > 2000) {
                                if (pauseStart === 0) {
                                    pauseStart = now;
                                }
                            } else if (pauseStart > 0) {
                                totalPauseDuration += (now - pauseStart);
                                pauseStart = 0;
                                pauseCountDisplay.textContent = Math.round(totalPauseDuration / 1000) + 's';
                            }

                            if (timeLeft <= 0) {
                                endGame('⏰ Waktu habis!');
                            }
                        }, 100);

                        updateStats();
                    }

                    function endGame(reason = '⏹ Game selesai') {
                        if (isFinished) return;
                        isFinished = true;
                        isStarted = false;

                        if (timerInterval) {
                            clearInterval(timerInterval);
                            timerInterval = null;
                        }

                        inputArea.disabled = true;
                        startBtn.innerHTML = '🔄 Main Lagi';
                        startBtn.className = 'px-6 py-3 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-emerald-500/30 flex items-center gap-2';

                        setStatus('finished', '✅ ' + reason);

                        // Update final stats
                        updateStats();

                        // Hitung hasil
                        const typed = inputArea.value;
                        const correct = correctChars;
                        const wrong = wrongChars;
                        const total = originalText.length;
                        const elapsedSeconds = duration - timeLeft;
                        const effectiveSeconds = Math.max(0.1, elapsedSeconds - (totalPauseDuration / 1000));
                        const effectiveMinutes = effectiveSeconds / 60;
                        const wpm = effectiveMinutes > 0 ? Math.round((correct / 5) / effectiveMinutes) : 0;
                        const accuracy = total > 0 ? Math.round((correct / total) * 100) : 100;

                        // Hitung konsistensi
                        const rhythm = calculateRhythm();

                        const resultData = {
                            wpm: wpm,
                            accuracy: accuracy,
                            total_words: wordCount,
                            correct_chars: correct,
                            wrong_chars: wrong,
                            total_chars: total,
                            typed_chars: typed.length,
                            backspace: backspaceCount,
                            duration_used: elapsedSeconds,
                            pause_duration: Math.round(totalPauseDuration / 1000),
                            slow_keys: slowKeys,
                            keyboard_map: allKeys,
                            missed_words: getMissedWords(typed),
                            rhythm: rhythm,
                            game_id: gameId,
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
                            console.error('Error:', error);
                            showResultPopup(resultData);
                        });
                    }

                    function calculateRhythm() {
                        const totalSlow = Object.keys(slowKeys).length;
                        const totalKeys = totalKeystrokes || 1;
                        const consistency = Math.max(0, 100 - Math.round((totalSlow / totalKeys) * 100));
                        const label = consistency >= 80 ? 'Sangat Rata' : (consistency >= 60 ? 'Cukup Rata' : 'Kurang Rata');

                        const sortedSlow = Object.entries(slowKeys)
                            .sort((a, b) => b[1] - a[1])
                            .slice(0, 10)
                            .map(([key, delay]) => ({ key, delay }));

                        return {
                            consistency: consistency,
                            consistency_label: label,
                            slow_keys: sortedSlow,
                            total_keys: totalKeys,
                            total_slow: totalSlow,
                        };
                    }

                    function getMissedWords(typed) {
                        const typedWords = typed.split(/\s+/).filter(w => w.length > 0);
                        const originalWords = originalText.split(/\s+/).filter(w => w.length > 0);
                        const missed = [];
                        for (let i = 0; i < originalWords.length; i++) {
                            if (i >= typedWords.length || typedWords[i] !== originalWords[i]) {
                                missed.push(originalWords[i]);
                            }
                        }
                        return missed.slice(0, 15);
                    }

                    function resetGame() {
                        if (timerInterval) {
                            clearInterval(timerInterval);
                            timerInterval = null;
                        }

                        isStarted = false;
                        isFinished = false;
                        timeLeft = duration;
                        startTimestamp = 0;
                        lastCharTime = 0;
                        totalPauseDuration = 0;
                        pauseStart = 0;
                        backspaceCount = 0;
                        correctChars = 0;
                        wrongChars = 0;
                        typedChars = 0;
                        totalKeystrokes = 0;
                        wordCount = 0;
                        slowKeys = {};
                        allKeys = {};

                        inputArea.value = '';
                        inputArea.disabled = true;
                        inputArea.dataset.prevLen = '0';

                        timerDisplay.textContent = duration;
                        progressDisplay.textContent = '0%';
                        accuracyDisplay.textContent = '100%';
                        wpmDisplay.textContent = '0';
                        charsDisplay.textContent = '0/' + totalChars;
                        backspaceDisplay.textContent = '0';
                        wrongCharsDisplay.textContent = '0';
                        pauseCountDisplay.textContent = '0s';
                        wordCountDisplay.textContent = '0';
                        progressFill.style.width = '0%';

                        renderText('');

                        startBtn.innerHTML = '▶ Mulai';
                        startBtn.className = 'px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl font-bold transition shadow-md shadow-indigo-500/30 flex items-center gap-2';

                        setStatus('idle', '⏸ Belum dimulai');
                    }

                    function setStatus(type, text) {
                        const colors = {
                            idle: 'bg-slate-400',
                            running: 'bg-emerald-500',
                            finished: 'bg-indigo-500',
                            error: 'bg-red-500'
                        };
                        statusDot.className = 'inline-block w-2 h-2 rounded-full ' + (colors[type] || 'bg-slate-400');
                        statusText.textContent = text;
                    }

                    function showResultPopup(data) {
                        const existing = document.querySelector('.result-popup');
                        if (existing) existing.remove();

                        const popup = document.createElement('div');
                        popup.className = 'result-popup fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn';
                        popup.innerHTML = `
                            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full p-6 md:p-8 border border-slate-200 dark:border-slate-700">
                                <div class="text-center mb-6">
                                    <div class="text-5xl mb-3">🏆</div>
                                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Hasil Permainan</h2>
                                    <p class="text-sm text-slate-500">Game selesai! Berikut statistik Anda.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                                        <p class="text-xs text-slate-500">WPM</p>
                                        <p class="text-2xl font-black text-cyan-600 dark:text-cyan-400">${data.wpm}</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                                        <p class="text-xs text-slate-500">Akurasi</p>
                                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">${data.accuracy}%</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                                        <p class="text-xs text-slate-500">Benar</p>
                                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">${data.correct_chars}</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                                        <p class="text-xs text-slate-500">Salah</p>
                                        <p class="text-2xl font-black text-red-600 dark:text-red-400">${data.wrong_chars}</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                                        <p class="text-xs text-slate-500">⌫ Backspace</p>
                                        <p class="text-2xl font-black text-orange-600 dark:text-orange-400">${data.backspace}</p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                                        <p class="text-xs text-slate-500">Konsistensi</p>
                                        <p class="text-2xl font-black text-purple-600 dark:text-purple-400">${data.rhythm?.consistency || 0}%</p>
                                    </div>
                                </div>
                                <div class="mt-6 flex gap-3">
                                    <button onclick="this.closest('.result-popup').remove(); document.getElementById('reset-btn').click();"
                                            class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
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
                        if (!isStarted || isFinished) return;

                        const typed = this.value;
                        const prevLen = parseInt(this.dataset.prevLen || '0');
                        const len = typed.length;

                        // Deteksi backspace
                        if (len < prevLen) {
                            backspaceCount++;
                            backspaceDisplay.textContent = backspaceCount;
                        }
                        this.dataset.prevLen = len;

                        // Update waktu terakhir mengetik
                        const now = Date.now();
                        if (lastCharTime > 0 && now - lastCharTime > 2000 && pauseStart === 0) {
                            pauseStart = now;
                        } else if (pauseStart > 0) {
                            totalPauseDuration += (now - pauseStart);
                            pauseStart = 0;
                            pauseCountDisplay.textContent = Math.round(totalPauseDuration / 1000) + 's';
                        }
                        lastCharTime = now;

                        // Update slow keys
                        if (len > 0) {
                            const lastChar = typed[len-1];
                            totalKeystrokes++;
                            const key = lastChar.toLowerCase();
                            if (key.match(/[a-z]/)) {
                                allKeys[key] = (allKeys[key] || 0) + 1;
                            }

                            // Deteksi jeda antar karakter > 300ms
                            if (len > 1 && prevLen < len) {
                                const diff = now - lastCharTime;
                                if (diff > 300 && diff < 5000) {
                                    slowKeys[lastChar] = (slowKeys[lastChar] || 0) + diff;
                                }
                            }
                        }

                        renderText(typed);
                        updateStats();

                        // Cek selesai
                        if (typed.length >= originalText.length && originalText.length > 0) {
                            const correct = (() => {
                                let c = 0;
                                for (let i = 0; i < originalText.length; i++) {
                                    if (typed[i] === originalText[i]) c++;
                                }
                                return c;
                            })();
                            if (correct === originalText.length) {
                                endGame('🎉 Selesai! Semua teks berhasil diketik.');
                            }
                        }
                    });

                    inputArea.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            if (!isStarted && !isFinished) {
                                e.preventDefault();
                                startGame();
                            } else if (isStarted && !isFinished) {
                                e.preventDefault();
                                endGame('⏹ Dihentikan oleh pengguna');
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
                    // INISIALISASI
                    // ==============================================================
                    resetGame();

                    console.log('🚀 Typing Game Loaded (Akurat)');
                });
                </script>

                <style>
                @keyframes fadeIn {
                    from { opacity: 0; transform: scale(0.95); }
                    to { opacity: 1; transform: scale(1); }
                }
                .animate-fadeIn {
                    animation: fadeIn 0.2s ease-out;
                }
                #text-display {
                    font-size: 1.125rem;
                    line-height: 2rem;
                    letter-spacing: 0.5px;
                }
                #text-display span {
                    transition: all 0.1s ease;
                    border-radius: 2px;
                    padding: 0 1px;
                }
                @media (max-width: 640px) {
                    #text-display {
                        font-size: 0.95rem;
                        line-height: 1.8rem;
                        padding: 0.75rem;
                    }
                }
                </style>
                @endpush

            @elseif(in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding']))
                {{-- QUIZ / TEBAK / LOGIC / CODING --}}
                <div class="p-4 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            @if($game->game_type === 'quiz') ❓ Quiz
                            @elseif($game->game_type === 'tebak') 🎯 Tebak
                            @elseif($game->game_type === 'logic') 🧠 Logic
                            @elseif($game->game_type === 'coding') 💻 Coding
                            @endif
                        </h2>
                        <div class="flex items-center gap-4 text-sm">
                            <span id="score-display" class="font-bold text-indigo-600 dark:text-indigo-400">Skor: 0</span>
                            <span id="progress-text" class="text-slate-500">1 / {{ count($gameData['questions'] ?? []) }}</span>
                        </div>
                    </div>

                    @if(!empty($gameData['questions']) && count($gameData['questions']) > 0)
                        @php $questions = $gameData['questions']; $total = count($questions); @endphp
                        <div id="question-container">
                            <div id="question-box" class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 min-h-[200px]">
                            </div>
                            <div class="flex justify-between mt-6">
                                <button id="prev-btn" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition disabled:opacity-50 font-bold" disabled>
                                    ◀ Sebelumnya
                                </button>
                                <button id="next-btn" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition font-bold">
                                    Selanjutnya ▶
                                </button>
                            </div>
                            <button id="submit-quiz-btn" class="mt-6 w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-md shadow-emerald-500/20">
                                ✅ Kumpulkan Jawaban
                            </button>
                        </div>

                        @push('scripts')
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const questions = @json($questions);
                            let currentIndex = 0;
                            const total = questions.length;
                            const box = document.getElementById('question-box');
                            const prevBtn = document.getElementById('prev-btn');
                            const nextBtn = document.getElementById('next-btn');
                            const progressText = document.getElementById('progress-text');
                            const scoreDisplay = document.getElementById('score-display');
                            let answers = [];

                            function renderQuestion(index) {
                                const q = questions[index];
                                if (!q) return;
                                let html = `<div class="mb-4">
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">${q.question || 'Pertanyaan'}</h3>
                                </div>`;

                                if (q.options && typeof q.options === 'object' && !Array.isArray(q.options)) {
                                    html += `<div class="space-y-2">`;
                                    for (let [key, value] of Object.entries(q.options)) {
                                        html += `<label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition">
                                            <input type="radio" name="answer" value="${key}" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-slate-700 dark:text-slate-300">${value}</span>
                                        </label>`;
                                    }
                                    html += `</div>`;
                                } else if (q.options && Array.isArray(q.options)) {
                                    html += `<div class="space-y-2">`;
                                    q.options.forEach(opt => {
                                        html += `<label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition">
                                            <input type="radio" name="answer" value="${opt}" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-slate-700 dark:text-slate-300">${opt}</span>
                                        </label>`;
                                    });
                                    html += `</div>`;
                                } else if (q.type === 'essay' || q.type === 'coding') {
                                    html += `<textarea rows="4" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition" placeholder="Tulis jawaban Anda di sini..."></textarea>`;
                                } else {
                                    html += `<input type="text" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition" placeholder="Masukkan jawaban...">`;
                                }

                                box.innerHTML = html;
                                progressText.textContent = `${index+1} / ${total}`;
                                prevBtn.disabled = index === 0;
                                nextBtn.textContent = index === total - 1 ? 'Selesai' : 'Selanjutnya';

                                if (answers[index]) {
                                    const radio = document.querySelector(`input[name="answer"][value="${answers[index]}"]`);
                                    if (radio) radio.checked = true;
                                    const textarea = document.querySelector('textarea');
                                    if (textarea) textarea.value = answers[index];
                                    const input = document.querySelector('input[type="text"]');
                                    if (input) input.value = answers[index];
                                }
                            }

                            function getCurrentAnswer() {
                                const radio = document.querySelector('input[name="answer"]:checked');
                                if (radio) return radio.value;
                                const textarea = document.querySelector('textarea');
                                if (textarea) return textarea.value;
                                const input = document.querySelector('input[type="text"]');
                                if (input) return input.value;
                                return null;
                            }

                            prevBtn.addEventListener('click', function() {
                                if (currentIndex > 0) {
                                    answers[currentIndex] = getCurrentAnswer();
                                    currentIndex--;
                                    renderQuestion(currentIndex);
                                }
                            });

                            nextBtn.addEventListener('click', function() {
                                answers[currentIndex] = getCurrentAnswer();
                                if (currentIndex < total - 1) {
                                    currentIndex++;
                                    renderQuestion(currentIndex);
                                } else {
                                    submitQuiz();
                                }
                            });

                            function submitQuiz() {
                                answers[currentIndex] = getCurrentAnswer();
                                fetch('{{ route("owner.game.games.submit-quiz") }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        game_id: {{ $game->id }},
                                        token: '{{ $token }}',
                                        answers: answers,
                                        questions: questions
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        alert('✅ Jawaban berhasil dikirim!');
                                    }
                                })
                                .catch(error => alert('❌ Terjadi kesalahan: ' + error.message));
                            }

                            document.getElementById('submit-quiz-btn').addEventListener('click', submitQuiz);
                            renderQuestion(0);
                        });
                        </script>
                        @endpush
                    @else
                        <div class="text-center py-12 text-slate-500">
                            <p class="text-4xl mb-4">📭</p>
                            <p>Belum ada soal untuk game ini.</p>
                        </div>
                    @endif
                </div>

            @elseif($game->game_type === 'reaction')
                {{-- REACTION GAME --}}
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

            @else
                {{-- Custom / Unknown --}}
                <div class="p-4 md:p-8 text-center py-16">
                    <div class="text-6xl mb-4">✨</div>
                    <h2 class="text-2xl font-bold text-slate-700 dark:text-slate-300">Game Custom</h2>
                    <p class="text-slate-500 mt-2">Pengaturan khusus untuk game ini akan muncul di sini.</p>
                    <pre class="mt-4 text-left text-xs bg-slate-100 dark:bg-slate-800 p-4 rounded-xl overflow-auto max-h-60">{{ json_encode($gameData, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif

        </div>

        {{-- FOOTER --}}
        <div class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
            <p>Game ID: {{ $game->id }} · Token: <span class="font-mono text-indigo-500">{{ $token }}</span> · Sesi: {{ now()->format('d M Y H:i') }}</p>
            <p class="mt-1">© {{ date('Y') }} UNICODE Game Center. All rights reserved.</p>
        </div>

    </div>
</div>
@endsection
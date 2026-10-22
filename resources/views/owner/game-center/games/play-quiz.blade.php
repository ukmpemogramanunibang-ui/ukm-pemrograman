@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 p-4 md:p-8">
    <div class="container mx-auto max-w-6xl">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-brain text-indigo-500"></i>
                    {{ $game->name ?? 'Quiz' }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400" id="game-info">
                    <i class="fas fa-tag mr-1"></i> 
                    {{ ucfirst($gameConfig['game_subtype'] ?? ($game->game_subtype ?? 'multiple_choice')) }}
                    @if(isset($gameConfig['timer']['mode']) && $gameConfig['timer']['mode'] === 'per_question')
                        · <i class="far fa-clock"></i> {{ $gameConfig['timer']['per_question'] ?? 30 }} detik/soal
                    @elseif(isset($gameConfig['timer']['mode']) && $gameConfig['timer']['mode'] === 'unlimited')
                        · <i class="far fa-hourglass"></i> Tanpa batas waktu
                    @elseif(isset($gameConfig['timer']['total']))
                        · <i class="far fa-hourglass"></i> {{ floor($gameConfig['timer']['total'] / 60) }} menit {{ $gameConfig['timer']['total'] % 60 }} detik
                    @endif
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    <i class="fas fa-user mr-1"></i> {{ $userName ?? 'Pemain' }} 
                    <span class="mx-1">·</span>
                    <i class="fas fa-user-tag mr-1"></i> {{ $role ?? 'Guest' }}
                </p>
            </div>
            <a href="{{ route('owner.game.games.index') }}" 
   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl shadow hover:shadow-md transition border border-slate-200 dark:border-slate-700">
    <i class="fas fa-arrow-left mr-2"></i> Keluar
</a>
        </div>

        <!-- Main Quiz Container -->
        <div id="quiz-app" class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 md:p-8 transition-all">
            
            <!-- Progress & Timer -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">
                        <i class="fas fa-list-ol mr-1"></i> Soal <span id="current-number">1</span> / <span id="total-number">0</span>
                    </span>
                    <div class="h-2 w-32 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div id="progress-bar" class="h-full bg-indigo-600 transition-all duration-500 ease-out" style="width: 0%"></div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span id="connection-status" class="hidden text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        <i class="fas fa-circle text-[8px] animate-pulse mr-1"></i> Terhubung
                    </span>
                    <span id="timer-display" class="font-mono text-lg font-bold text-amber-600 dark:text-amber-400">
                        <i class="fas fa-clock mr-1"></i> <span id="timer-text">--:--</span>
                    </span>
                    <button id="restart-btn" class="text-sm text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition" title="Mulai ulang quiz">
                        <i class="fas fa-redo-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Anti-Cheat Warning -->
            <div id="anti-cheat-warning" class="hidden mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl text-sm text-yellow-700 dark:text-yellow-300">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span id="anti-cheat-message">Jangan berpindah tab atau keluar dari halaman ini!</span>
            </div>

            <!-- Skill Panel (tampil jika skill aktif) -->
            <div id="skill-panel" class="hidden mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
                        <i class="fas fa-bolt mr-2"></i> Skill Saya
                    </h3>
                    <span class="text-xs text-indigo-500 dark:text-indigo-400">Poin: <span id="skill-points">0</span></span>
                </div>
                <div id="skill-list" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Contoh skill (akan diisi JS) -->
                </div>
            </div>

            <!-- Question Area -->
            <div id="question-container" class="mb-8 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 min-h-[140px] transition">
                <div class="flex items-start justify-between">
                    <p class="text-lg md:text-xl font-semibold text-slate-800 dark:text-white leading-relaxed" id="question-text">
                        Pertanyaan akan muncul di sini.
                    </p>
                    <span class="text-xs font-medium text-slate-400 dark:text-slate-500 ml-4" id="question-type"></span>
                </div>
                @if(isset($game->thumbnail) && $game->thumbnail)
                    <img src="{{ asset('storage/'.$game->thumbnail) }}" alt="Gambar game" class="mt-4 max-h-40 rounded-lg shadow-md mx-auto">
                @endif
                <img id="question-image" class="hidden mt-4 max-h-60 rounded-lg shadow-md mx-auto" alt="Gambar soal" />
            </div>

            <!-- Options Area -->
            <div id="options-container" class="space-y-3 mb-8">
                <!-- Dinamis oleh JavaScript -->
            </div>

            <!-- Navigation Buttons -->
            <div class="flex flex-wrap justify-between items-center gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button id="prev-btn" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-medium hover:bg-slate-300 dark:hover:bg-slate-600 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" disabled>
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </button>
                <button id="next-btn" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Status Info -->
            <div id="status-bar" class="mt-4 text-sm text-center text-slate-500 dark:text-slate-400">
                <span id="status-text">Jawab setiap soal sebelum melanjutkan.</span>
            </div>
        </div>

        <!-- Result Section -->
        <div id="result-section" class="hidden bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 md:p-8 mt-6 transition-all">
            <h2 class="text-3xl font-extrabold text-center text-slate-800 dark:text-white mb-6">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i> Hasil Quiz
            </h2>
            
            <!-- Info Pemain -->
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 mb-6 border border-slate-200 dark:border-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Nama Pemain</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white" id="result-player-name">{{ $userName ?? 'Pemain' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Role</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white" id="result-player-role">{{ $role ?? 'Guest' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">ID Pengguna</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white" id="result-player-id">{{ $userId ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistik Utama -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl">
                    <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400" id="score-final">0</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Poin</p>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl">
                    <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400" id="correct-final">0</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Benar</p>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl">
                    <p class="text-3xl font-black text-red-600 dark:text-red-400" id="wrong-final">0</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Salah</p>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl">
                    <p class="text-3xl font-black text-amber-600 dark:text-amber-400" id="time-final">0</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Waktu (detik)</p>
                </div>
            </div>

            <!-- Performance Bars -->
            <div class="mt-6 space-y-3">
                <div>
                    <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                        <span>Kecepatan</span>
                        <span id="speed-percent">0%</span>
                    </div>
                    <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div id="speed-bar" class="h-full bg-indigo-500 transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                        <span>Akurasi</span>
                        <span id="accuracy-percent">0%</span>
                    </div>
                    <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div id="accuracy-bar" class="h-full bg-emerald-500 transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                        <span>Konsistensi</span>
                        <span id="consistency-percent">0%</span>
                    </div>
                    <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div id="consistency-bar" class="h-full bg-amber-500 transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
            </div>

            <!-- Pencapaian -->
            <div class="mt-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3"><i class="fas fa-medal mr-2"></i>Pencapaian</h3>
                <div id="achievements-list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Dinamis -->
                </div>
            </div>

            <!-- Detail Jawaban -->
            <div class="mt-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3">Rincian Jawaban</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
                        <thead class="text-xs uppercase bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Soal</th>
                                <th class="px-4 py-3">Jawaban Anda</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody id="answer-detail-body">
                            <!-- Dinamis -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-8">
                <button onclick="window.location.reload()" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition">
                    <i class="fas fa-redo mr-2"></i> Main Lagi
                </button>
                <a href="{{ route('owner.game.games.index') }}" class="ml-3 px-8 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">
    <i class="fas fa-home mr-2"></i> Kembali
</a>
            </div>
        </div>

        {{-- Debug info (hanya muncul jika data tidak ditemukan) --}}
        <div id="debug-info" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl hidden">
            <p class="font-bold text-red-700 dark:text-red-300"><i class="fas fa-bug mr-2"></i> Debug: Data tidak ditemukan</p>
            <pre id="debug-output" class="mt-2 text-xs text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 p-3 rounded-lg overflow-auto max-h-40"></pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // 1. KUMPULKAN DATA DARI BERBAGAI SUMBER
    // ============================================================
    let rawQuestions = [];
    let rawConfig = {};

    try {
        const gameData = @json($gameData ?? []);
        if (gameData && Array.isArray(gameData.questions) && gameData.questions.length > 0) {
            rawQuestions = gameData.questions;
        }
        if (gameData && gameData.config) {
            rawConfig = gameData.config;
        }
    } catch (e) {}

    if (rawQuestions.length === 0) {
        try {
            const directQuestions = @json($questions ?? []);
            if (Array.isArray(directQuestions) && directQuestions.length > 0) {
                rawQuestions = directQuestions;
            }
        } catch (e) {}
    }

    if (rawQuestions.length === 0) {
        try {
            const gameObj = @json($game ?? []);
            if (gameObj && gameObj.questions) {
                rawQuestions = typeof gameObj.questions === 'string' ? JSON.parse(gameObj.questions) : gameObj.questions;
            }
            if (gameObj && gameObj.quiz_config) {
                rawConfig = typeof gameObj.quiz_config === 'string' ? JSON.parse(gameObj.quiz_config) : gameObj.quiz_config;
            }
        } catch (e) {}
    }

    if (Object.keys(rawConfig).length === 0) {
        try {
            const config = @json($gameConfig ?? []);
            if (config && typeof config === 'object') {
                rawConfig = config;
            }
        } catch (e) {}
    }

    rawQuestions = rawQuestions.map((q, idx) => ({
        id: q.id || idx,
        question: q.question || q.text || 'Pertanyaan tidak tersedia',
        type: q.type || 'multiple_choice',
        options: Array.isArray(q.options) ? q.options : [],
        answer: q.answer || null,
        explanation: q.explanation || '',
        weight: q.weight || 10,
        difficulty: q.difficulty || 'medium',
        image: q.image || null,
    }));

    const debugInfo = document.getElementById('debug-info');
    const debugOutput = document.getElementById('debug-output');
    if (rawQuestions.length === 0) {
        debugInfo.classList.remove('hidden');
        debugOutput.textContent = JSON.stringify({
            'gameData': @json($gameData ?? 'tidak ada'),
            'gameConfig': @json($gameConfig ?? 'tidak ada'),
            'game': @json($game ?? 'tidak ada'),
            'questions': @json($questions ?? 'tidak ada'),
            'rawQuestions': rawQuestions,
            'rawConfig': rawConfig,
            'tip': 'Periksa apakah controller mengirimkan $gameData[\'questions\'] atau $questions dengan benar.'
        }, null, 2);
    }

    // ============================================================
    // 2. KONFIGURASI
    // ============================================================
    const config = {
        timer: {
            mode: rawConfig.timer?.mode || 'total',
            total: rawConfig.timer?.total || 600,
            per_question: rawConfig.timer?.per_question || 30,
            timeout_action: rawConfig.timer?.timeout_action || 'skip',
            enabled: rawConfig.timer?.enabled !== false,
        },
        points: {
            correct: rawConfig.points?.correct ?? 10,
            wrong: rawConfig.points?.wrong ?? 0,
            empty: rawConfig.points?.empty ?? 0,
            bonus_complete: rawConfig.points?.bonus_complete ?? 20,
            bonus_streak: rawConfig.points?.bonus_streak ?? 5,
            max_points: rawConfig.points?.max_points ?? 1000,
        },
        anti_cheat: rawConfig.anti_cheat || {},
        game_subtype: rawConfig.game_subtype || 'multiple_choice',
        skills: {
            enabled: rawConfig.skills?.enabled === true && 
                     (rawConfig.game_subtype || 'multiple_choice') === 'multiple_choice' && 
                     rawConfig.points?.enabled === true,
            definitions: rawConfig.skills?.definitions || []
        },
        idempotency_key: '{{ $attemptToken ?? '' }}'
    };

    // ============================================================
    // 3. STATE
    // ============================================================
    const state = {
        questions: rawQuestions,
        currentIndex: 0,
        answers: {},
        score: 0,
        correct: 0,
        wrong: 0,
        isFinished: false,
        isAnswered: false,
        startTime: null,
        timerInterval: null,
        timeLeft: 0,
        violations: 0,
        lastAnswerTimestamps: {},
        connectionLost: false,
        submissionToken: null,
        skillPoints: rawConfig.points?.initial_points || 0
    };

    // DOM Elements
    const el = {
        questionText: document.getElementById('question-text'),
        questionType: document.getElementById('question-type'),
        questionImage: document.getElementById('question-image'),
        optionsContainer: document.getElementById('options-container'),
        currentNumber: document.getElementById('current-number'),
        totalNumber: document.getElementById('total-number'),
        progressBar: document.getElementById('progress-bar'),
        timerText: document.getElementById('timer-text'),
        timerDisplay: document.getElementById('timer-display'),
        prevBtn: document.getElementById('prev-btn'),
        nextBtn: document.getElementById('next-btn'),
        statusText: document.getElementById('status-text'),
        restartBtn: document.getElementById('restart-btn'),
        quizApp: document.getElementById('quiz-app'),
        resultSection: document.getElementById('result-section'),
        antiCheatWarning: document.getElementById('anti-cheat-warning'),
        antiCheatMessage: document.getElementById('anti-cheat-message'),
        connectionStatus: document.getElementById('connection-status'),
        skillPanel: document.getElementById('skill-panel'),
        skillList: document.getElementById('skill-list'),
        skillPoints: document.getElementById('skill-points'),
        scoreFinal: document.getElementById('score-final'),
        correctFinal: document.getElementById('correct-final'),
        wrongFinal: document.getElementById('wrong-final'),
        timeFinal: document.getElementById('time-final'),
        speedPercent: document.getElementById('speed-percent'),
        speedBar: document.getElementById('speed-bar'),
        accuracyPercent: document.getElementById('accuracy-percent'),
        accuracyBar: document.getElementById('accuracy-bar'),
        consistencyPercent: document.getElementById('consistency-percent'),
        consistencyBar: document.getElementById('consistency-bar'),
        achievementsList: document.getElementById('achievements-list'),
        answerDetailBody: document.getElementById('answer-detail-body'),
    };

    // ============================================================
    // 4. UTILITAS
    // ============================================================
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    const formatTime = (seconds) => {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    };

    // ============================================================
    // 5. TIMER ENGINE
    // ============================================================
    const TimerEngine = {
        reset() {
            clearInterval(state.timerInterval);
            if (!config.timer.enabled || config.timer.mode === 'unlimited') {
                el.timerText.textContent = '∞';
                el.timerDisplay.classList.remove('text-red-500', 'text-amber-600', 'dark:text-amber-400');
                return;
            }
            if (config.timer.mode === 'per_question') {
                state.timeLeft = config.timer.per_question || 30;
            } else {
                if (!state.startTime) {
                    state.startTime = Date.now();
                }
                const elapsed = Math.floor((Date.now() - state.startTime) / 1000);
                state.timeLeft = Math.max(0, (config.timer.total || 600) - elapsed);
            }
            this.update();
            state.timerInterval = setInterval(() => {
                state.timeLeft--;
                this.update();
                if (state.timeLeft <= 0) {
                    clearInterval(state.timerInterval);
                    this.handleTimeout();
                }
            }, 1000);
        },
        update() {
            if (config.timer.mode === 'unlimited') {
                el.timerText.textContent = '∞';
                return;
            }
            el.timerText.textContent = formatTime(state.timeLeft);
            if (state.timeLeft < 10) {
                el.timerDisplay.classList.add('text-red-500');
                el.timerDisplay.classList.remove('text-amber-600', 'dark:text-amber-400');
            } else {
                el.timerDisplay.classList.remove('text-red-500');
                el.timerDisplay.classList.add('text-amber-600', 'dark:text-amber-400');
            }
        },
        handleTimeout() {
            if (state.isFinished) return;
            if (config.timer.timeout_action === 'finish') {
                QuizEngine.finish();
            } else {
                if (state.currentIndex < state.questions.length - 1) {
                    state.currentIndex++;
                    renderQuestion();
                } else {
                    QuizEngine.finish();
                }
            }
        }
    };

    // ============================================================
    // 6. SKILL ENGINE (UI Dasar)
    // ============================================================
    const SkillEngine = {
        init() {
            if (!config.skills.enabled) {
                el.skillPanel.classList.add('hidden');
                return;
            }
            el.skillPanel.classList.remove('hidden');
            this.renderSkills();
            this.updatePoints();
        },
        renderSkills() {
            const skillDefinitions = config.skills.definitions;
            if (!skillDefinitions.length) {
                el.skillList.innerHTML = '<p class="text-sm text-slate-500 dark:text-slate-400 col-span-3">Tidak ada skill yang tersedia.</p>';
                return;
            }
            el.skillList.innerHTML = '';
            skillDefinitions.forEach(skill => {
                const btn = document.createElement('button');
                btn.className = 'p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition text-left';
                btn.dataset.skillCode = skill.code;
                btn.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-800 dark:text-white">${skill.name}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">x${skill.quantity || 0}</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">${skill.description}</p>
                    <div class="mt-2 text-xs font-semibold ${skill.type === 'attack' ? 'text-red-500 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'}">
                        ${skill.type === 'attack' ? '⚔' : '🛡'} Harga: ${skill.price} poin
                    </div>
                `;
                btn.addEventListener('click', () => {
                    this.useSkill(skill);
                });
                el.skillList.appendChild(btn);
            });
        },
        updatePoints() {
            el.skillPoints.textContent = state.skillPoints;
        },
        useSkill(skill) {
            if (state.skillPoints < skill.price) {
                alert('Poin tidak cukup!');
                return;
            }
            if (skill.type === 'attack') {
                this.selectTarget(skill);
            } else if (skill.type === 'defense') {
                // Gunakan langsung, kirim ke server
                console.log(`Skill ${skill.code} digunakan (defense)`, { target: 'self' });
                // Di sini Anda akan melakukan fetch ke backend
            }
        },
        selectTarget(skill) {
            // Tampilkan modal pilih target (dummy)
            const players = ['Andi', 'Budi', 'Citra']; // contoh
            const target = prompt('Pilih target serangan: \n' + players.map((p, i) => `${i+1}. ${p}`).join('\n'));
            if (target) {
                const idx = parseInt(target) - 1;
                if (idx >= 0 && idx < players.length) {
                    console.log(`Skill ${skill.code} digunakan (attack)`, { target: players[idx] });
                    // Kirim ke server
                }
            }
        }
    };

    // ============================================================
    // 7. RENDER QUESTION
    // ============================================================
    function renderQuestion() {
        if (state.isFinished || state.questions.length === 0) {
            if (state.questions.length === 0) {
                el.questionText.textContent = '⚠️ Belum ada soal untuk quiz ini.';
                el.optionsContainer.innerHTML = `
                    <div class="p-6 text-center bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                        <p class="text-amber-700 dark:text-amber-300 font-bold">
                            <i class="fas fa-exclamation-triangle mr-2"></i> 
                            Tidak ada soal yang ditemukan.
                        </p>
                        <p class="text-sm text-amber-600 dark:text-amber-400 mt-2">
                            Pastikan Anda telah menambahkan soal di halaman edit game.
                        </p>
                        <a href="{{ route('owner.game.games.edit', $game) }}" 
                           class="mt-3 inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition">
                            <i class="fas fa-edit mr-1"></i> Edit Game
                        </a>
                    </div>
                `;
                el.nextBtn.disabled = true;
                el.prevBtn.disabled = true;
                el.statusText.textContent = 'Tidak ada soal.';
                el.totalNumber.textContent = '0';
                el.progressBar.style.width = '0%';
            }
            return;
        }

        const q = state.questions[state.currentIndex];
        if (!q) return;

        el.currentNumber.textContent = state.currentIndex + 1;
        el.totalNumber.textContent = state.questions.length;
        el.progressBar.style.width = ((state.currentIndex + 1) / state.questions.length * 100) + '%';

        el.questionText.textContent = q.question || 'Pertanyaan tidak tersedia';
        el.questionType.textContent = (q.type === 'multiple_choice' || q.type === 'true_false' || config.game_subtype === 'multiple_choice') ? 'Pilihan Ganda' : 'Esai';

        if (q.image) {
            el.questionImage.src = q.image;
            el.questionImage.classList.remove('hidden');
        } else {
            el.questionImage.src = '';
            el.questionImage.classList.add('hidden');
        }

        el.optionsContainer.innerHTML = '';
        const isMultipleChoice = (q.type === 'multiple_choice' || q.type === 'true_false' || config.game_subtype === 'multiple_choice');

        if (isMultipleChoice) {
            let options = [];
            if (Array.isArray(q.options)) {
                options = q.options;
            } else if (typeof q.options === 'object' && q.options !== null) {
                options = Object.values(q.options);
            } else {
                options = [
                    { label: 'A', text: 'Pilihan A', correct: false },
                    { label: 'B', text: 'Pilihan B', correct: false },
                    { label: 'C', text: 'Pilihan C', correct: false },
                    { label: 'D', text: 'Pilihan D', correct: false }
                ];
            }

            if (options.length > 0 && typeof options[0] === 'string') {
                options = options.map((text, idx) => ({
                    label: String.fromCharCode(65 + idx),
                    text: text,
                    correct: false
                }));
            }

            options.forEach((opt, idx) => {
                const btn = document.createElement('button');
                const isSelected = state.answers[state.currentIndex] !== undefined && state.answers[state.currentIndex] === idx;
                btn.className = `w-full p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 
                                 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 
                                 transition text-left flex items-center gap-3 
                                 ${isSelected ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30' : ''}`;
                // Kunci: teks opsi harus memiliki warna yang sesuai dark mode
                btn.innerHTML = `<span class="font-bold text-indigo-600 dark:text-indigo-400">${opt.label}.</span> <span class="text-slate-800 dark:text-slate-200">${opt.text || '(kosong)'}</span>`;
                btn.dataset.index = idx;
                btn.addEventListener('click', function(e) {
                    if (state.isFinished) return;
                    QuizEngine.selectAnswer(this, idx);
                });
                el.optionsContainer.appendChild(btn);
            });

            if (state.answers[state.currentIndex] !== undefined) {
                state.isAnswered = true;
                el.statusText.textContent = '✅ Jawaban sudah dipilih.';
            } else {
                state.isAnswered = false;
                el.statusText.textContent = 'Pilih salah satu jawaban.';
            }
        } else {
            const textarea = document.createElement('textarea');
            textarea.rows = 5;
            textarea.className = 'w-full p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition bg-transparent text-slate-800 dark:text-white';
            textarea.placeholder = 'Tulis jawaban essay Anda di sini...';
            textarea.value = state.answers[state.currentIndex] || '';
            textarea.addEventListener('input', function(e) {
                if (state.isFinished) return;
                state.answers[state.currentIndex] = e.target.value.trim();
                state.isAnswered = (state.answers[state.currentIndex].length > 0);
                el.statusText.textContent = state.isAnswered ? '✅ Jawaban tersimpan.' : 'Ketik jawaban Anda.';
                debouncedAnswerSave();
            });
            el.optionsContainer.appendChild(textarea);
            state.isAnswered = (state.answers[state.currentIndex] && state.answers[state.currentIndex].trim().length > 0);
            el.statusText.textContent = state.isAnswered ? '✅ Jawaban tersimpan.' : 'Ketik jawaban Anda.';
        }

        el.prevBtn.disabled = (state.currentIndex === 0);
        el.nextBtn.innerHTML = (state.currentIndex === state.questions.length - 1) ? 'Selesai <i class="fas fa-check"></i>' : 'Selanjutnya <i class="fas fa-chevron-right"></i>';

        if (config.timer.mode === 'per_question') {
            TimerEngine.reset();
        }
    }

    // ============================================================
    // 8. ANTI-CHEAT
    // ============================================================
    const AntiCheat = {
        level: config.anti_cheat.level || 'basic',
        violations: [],
        violationLimit: config.anti_cheat.violation_limit || 3,
        penaltySeconds: config.anti_cheat.penalty || 5,
        handleViolation(type, message) {
            if (config.anti_cheat.enabled === false) return;
            state.violations++;
            this.violations.push({ type, message, timestamp: Date.now() });
            el.antiCheatWarning.classList.remove('hidden');
            el.antiCheatMessage.textContent = message + ' (Pelanggaran ke-' + state.violations + ')';

            if (state.violations >= this.violationLimit) {
                alert('Anda telah melanggar aturan lebih dari ' + this.violationLimit + ' kali. Quiz dihentikan!');
                QuizEngine.finish(true);
            } else {
                if (config.timer.enabled && config.timer.mode !== 'unlimited') {
                    state.timeLeft = Math.max(0, state.timeLeft - this.penaltySeconds);
                    TimerEngine.update();
                }
            }
        }
    };

    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !state.isFinished) {
            if (config.anti_cheat.detect_tab_switch) {
                AntiCheat.handleViolation('tab_switch', 'Jangan berpindah tab!');
            }
        }
    });

    window.addEventListener('blur', function() {
        if (!state.isFinished && config.anti_cheat.detect_blur) {
            if (AntiCheat.level !== 'basic') {
                AntiCheat.handleViolation('blur', 'Jangan klik di luar halaman!');
            }
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth < 100 && !state.isFinished && config.anti_cheat.detect_minimize) {
            AntiCheat.handleViolation('minimize', 'Jangan minimize jendela!');
        }
    });

    if (config.anti_cheat.disable_copy_paste) {
        document.addEventListener('copy', function(e) { e.preventDefault(); });
        document.addEventListener('paste', function(e) { e.preventDefault(); });
    }

    if (config.anti_cheat.block_context_menu) {
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
    }

    // ============================================================
    // 9. QUIZ ENGINE
    // ============================================================
    const QuizEngine = {
        init() {
            if (state.questions.length === 0) {
                renderQuestion();
                return;
            }
            state.startTime = Date.now();
            renderQuestion();
            el.statusText.textContent = 'Selamat mengerjakan!';
            if (config.timer.mode === 'total') {
                TimerEngine.reset();
            } else {
                TimerEngine.update();
            }
            this.initConnection();
            this.debouncedAnswerSave = debounce(() => this.saveAnswerToServer(), 1000);
            state.submissionToken = this.generateToken();
            SkillEngine.init();
        },

        initConnection() {
            window.addEventListener('online', () => {
                el.connectionStatus.classList.remove('hidden');
                el.connectionStatus.innerHTML = '<i class="fas fa-circle text-[8px] text-emerald-500 mr-1"></i> Terhubung';
                state.connectionLost = false;
                this.saveAllPendingAnswers();
            });
            window.addEventListener('offline', () => {
                el.connectionStatus.classList.remove('hidden');
                el.connectionStatus.innerHTML = '<i class="fas fa-circle text-[8px] text-red-500 mr-1"></i> Koneksi terputus';
                state.connectionLost = true;
            });
        },

        selectAnswer(btn, idx) {
            if (state.isFinished) return;
            el.optionsContainer.querySelectorAll('button').forEach(b => {
                b.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
            });
            btn.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/30');
            state.answers[state.currentIndex] = idx;
            state.isAnswered = true;
            el.statusText.textContent = '✅ Jawaban dipilih.';
            this.saveAnswerToServer();
        },

        next() {
            if (state.isFinished) return;
            const q = state.questions[state.currentIndex];
            const isMultipleChoice = (q.type === 'multiple_choice' || q.type === 'true_false' || config.game_subtype === 'multiple_choice');
            if (isMultipleChoice && !state.isAnswered) {
                alert('Jawab dulu soal ini sebelum melanjutkan.');
                return;
            }
            if (state.currentIndex < state.questions.length - 1) {
                state.currentIndex++;
                renderQuestion();
            } else {
                this.finish();
            }
        },

        previous() {
            if (state.currentIndex > 0) {
                state.currentIndex--;
                renderQuestion();
            }
        },

        finish(force = false) {
            if (state.isFinished) return;
            state.isFinished = true;
            clearInterval(state.timerInterval);

            let totalCorrect = 0, totalWrong = 0, totalEmpty = 0, totalScore = 0;
            let totalTime = Math.floor((Date.now() - state.startTime) / 1000);
            if (config.timer.mode === 'unlimited') {
                totalTime = 0;
            }

            state.questions.forEach((q, idx) => {
                const userAns = state.answers[idx];
                const isMC = (q.type === 'multiple_choice' || q.type === 'true_false' || config.game_subtype === 'multiple_choice');

                if (isMC) {
                    let correctAns = -1;
                    if (Array.isArray(q.options)) {
                        const optIndex = q.options.findIndex(o => o.correct === true);
                        if (optIndex !== -1) correctAns = optIndex;
                    }
                    if (userAns === undefined || userAns === null) {
                        totalEmpty++;
                    } else if (userAns === correctAns) {
                        totalCorrect++;
                        totalScore += (config.points.correct ?? 10);
                    } else {
                        totalWrong++;
                    }
                } else {
                    if (userAns && userAns.trim().length > 0) {
                        if (q.answer) {
                            if (userAns.trim().toLowerCase() === q.answer.trim().toLowerCase()) {
                                totalCorrect++;
                                totalScore += (config.points.correct ?? 10);
                            } else {
                                totalWrong++;
                            }
                        } else {
                            totalWrong++;
                        }
                    } else {
                        totalEmpty++;
                    }
                }
            });

            if (totalCorrect === state.questions.length && state.questions.length > 0) {
                totalScore += (config.points.bonus_complete ?? 20);
            }

            let streak = 0, bestStreak = 0;
            state.questions.forEach((q, idx) => {
                const isMC = (q.type === 'multiple_choice' || q.type === 'true_false' || config.game_subtype === 'multiple_choice');
                let correctAns = -1;
                if (isMC && Array.isArray(q.options)) {
                    const optIndex = q.options.findIndex(o => o.correct === true);
                    if (optIndex !== -1) correctAns = optIndex;
                }
                if (isMC && state.answers[idx] === correctAns) {
                    streak++;
                    bestStreak = Math.max(bestStreak, streak);
                } else {
                    streak = 0;
                }
            });
            if (bestStreak >= 5) {
                totalScore += (config.points.bonus_streak ?? 5) * Math.floor(bestStreak / 5);
            }

            state.score = totalScore;
            state.correct = totalCorrect;
            state.wrong = totalWrong;

            el.quizApp.classList.add('hidden');
            el.resultSection.classList.remove('hidden');

            el.scoreFinal.textContent = state.score;
            el.correctFinal.textContent = state.correct;
            el.wrongFinal.textContent = state.wrong;
            el.timeFinal.textContent = totalTime;

            const totalQuestions = state.questions.length;
            const accuracy = totalQuestions > 0 ? (state.correct / totalQuestions) * 100 : 0;
            let speedScore = 0;
            if (config.timer.mode !== 'unlimited') {
                const avgTime = totalTime / totalQuestions;
                speedScore = Math.max(0, 100 - ((avgTime - 5) * 2));
                speedScore = Math.min(100, speedScore);
            } else {
                speedScore = 50;
            }
            const consistency = accuracy >= 80 ? 90 : accuracy >= 60 ? 70 : 50;

            el.speedPercent.textContent = Math.round(speedScore) + '%';
            el.speedBar.style.width = speedScore + '%';
            el.accuracyPercent.textContent = Math.round(accuracy) + '%';
            el.accuracyBar.style.width = accuracy + '%';
            el.consistencyPercent.textContent = Math.round(consistency) + '%';
            el.consistencyBar.style.width = consistency + '%';

            this.renderAchievements(accuracy, speedScore, bestStreak);
            this.renderAnswerDetails();
            this.submitResult(totalTime, force);
        },

        renderAchievements(accuracy, speed, streak) {
            el.achievementsList.innerHTML = '';
            const achievements = [];
            if (accuracy >= 80) {
                achievements.push({ icon: 'fa-star', color: 'text-yellow-500', title: 'Sharp Mind', desc: 'Akurasi tinggi' });
            }
            if (speed >= 80) {
                achievements.push({ icon: 'fa-bolt', color: 'text-indigo-500', title: 'Fast Thinker', desc: 'Cepat menjawab' });
            }
            if (streak >= 5) {
                achievements.push({ icon: 'fa-fire', color: 'text-orange-500', title: 'Perfect Streak', desc: '5 jawaban benar beruntun' });
            }
            if (achievements.length === 0) {
                achievements.push({ icon: 'fa-hand-paper', color: 'text-slate-400', title: 'Penyelesaian', desc: 'Quiz selesai' });
            }
            achievements.forEach((a) => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 bg-slate-100 dark:bg-slate-800 p-3 rounded-xl';
                div.innerHTML = `
                    <i class="fas ${a.icon} ${a.color} text-2xl"></i>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white">${a.title}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">${a.desc}</p>
                    </div>
                `;
                el.achievementsList.appendChild(div);
            });
        },

        renderAnswerDetails() {
            el.answerDetailBody.innerHTML = '';
            state.questions.forEach((q, idx) => {
                const userAns = state.answers[idx];
                const isMC = (q.type === 'multiple_choice' || q.type === 'true_false' || config.game_subtype === 'multiple_choice');
                let userAnswerText = 'Tidak dijawab';
                let status = 'Kosong';
                let statusClass = 'text-slate-500 dark:text-slate-400';

                if (isMC) {
                    let correctAns = -1;
                    if (Array.isArray(q.options)) {
                        const optIndex = q.options.findIndex(o => o.correct === true);
                        if (optIndex !== -1) correctAns = optIndex;
                    }
                    if (userAns !== undefined && userAns !== null) {
                        userAnswerText = q.options[userAns]?.text || 'Pilihan ' + (userAns + 1);
                        if (userAns === correctAns) {
                            status = 'Benar';
                            statusClass = 'text-emerald-600 dark:text-emerald-400';
                        } else {
                            status = 'Salah';
                            statusClass = 'text-red-600 dark:text-red-400';
                        }
                    }
                } else {
                    if (userAns && userAns.trim().length > 0) {
                        userAnswerText = userAns;
                        status = 'Terisi';
                        statusClass = 'text-emerald-600 dark:text-emerald-400';
                    }
                }

                const tr = document.createElement('tr');
                tr.className = 'border-b border-slate-200 dark:border-slate-700';
                tr.innerHTML = `
                    <td class="px-4 py-3">${idx + 1}</td>
                    <td class="px-4 py-3">${q.question}</td>
                    <td class="px-4 py-3">${userAnswerText}</td>
                    <td class="px-4 py-3 font-bold ${statusClass}">${status}</td>
                `;
                el.answerDetailBody.appendChild(tr);
            });
        },

        saveAnswerToServer() {
            console.log('Jawaban disimpan ke server:', state.answers[state.currentIndex]);
        },

        saveAllPendingAnswers() {
            Object.keys(state.answers).forEach(index => {
                console.log('Kirim ulang jawaban untuk soal', index, state.answers[index]);
            });
        },

        submitResult(totalTime, force = false) {
            console.log('Hasil dikirim ke server:', {
                score: state.score,
                correct: state.correct,
                wrong: state.wrong,
                totalTime: totalTime,
                force: force,
                submission_token: state.submissionToken
            });
        },

        generateToken() {
            return Math.random().toString(36).substr(2, 10) + Date.now().toString(36);
        }
    };

    // ============================================================
    // 10. EVENT LISTENERS
    // ============================================================
    el.nextBtn.addEventListener('click', () => QuizEngine.next());
    el.prevBtn.addEventListener('click', () => QuizEngine.previous());
    el.restartBtn.addEventListener('click', function() {
        if (confirm('Mulai ulang quiz? Semua jawaban akan direset.')) {
            window.location.reload();
        }
    });

    document.addEventListener('keydown', function(e) {
        const tag = e.target.tagName;
        if (tag === 'TEXTAREA' || tag === 'INPUT') return;
        if (e.key === 'ArrowRight' && !el.nextBtn.disabled) {
            el.nextBtn.click();
        } else if (e.key === 'ArrowLeft' && !el.prevBtn.disabled) {
            el.prevBtn.click();
        }
    });

    // ============================================================
    // 11. INISIALISASI
    // ============================================================
    QuizEngine.init();

    console.log(`✅ Quiz Engine v2 loaded: ${state.questions.length} soal, timer mode ${config.timer.mode}, total ${config.timer.total} detik, skill ${config.skills.enabled}`);
});
</script>

<style>
    :root {
        --question-bg: #f8fafc;
        --border-color: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
    }
    .dark {
        --question-bg: #1e293b;
        --border-color: #334155;
        --text-primary: #f1f5f9;
        --text-secondary: #94a3b8;
    }
    #question-container {
        background-color: var(--question-bg);
        border-color: var(--border-color);
    }
    #question-text, #question-type, #status-text {
        color: var(--text-primary);
    }
    #options-container button {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    #options-container button:hover {
        border-color: #818cf8;
        background-color: rgba(99, 102, 241, 0.05);
    }
    .dark #options-container button:hover {
        background-color: rgba(99, 102, 241, 0.1);
    }
    .option-selected {
        border-color: #4f46e5 !important;
        background-color: #eef2ff !important;
    }
    .dark .option-selected {
        background-color: rgba(99, 102, 241, 0.2) !important;
    }
    #progress-bar {
        transition: width 0.5s ease-out;
    }
    #timer-display {
        min-width: 90px;
        text-align: center;
    }
    #anti-cheat-warning {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.8; }
        100% { opacity: 1; }
    }
    #result-section {
        animation: fadeInUp 0.6s ease;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .performance-bar {
        height: 6px;
        border-radius: 9999px;
        background-color: #e2e8f0;
    }
    .dark .performance-bar {
        background-color: #334155;
    }
    .performance-bar div {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.5s ease;
    }
</style>
@endsection
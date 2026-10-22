{{-- ============================================================ --}}
{{-- QUIZ / TEBAK / LOGIC / CODING - PARTIAL                   --}}
{{-- ============================================================ --}}
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
                {{-- Akan diisi JavaScript --}}
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
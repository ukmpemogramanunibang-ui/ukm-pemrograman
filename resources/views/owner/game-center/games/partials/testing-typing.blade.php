<div>
    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
        <span class="text-2xl">⌨️</span> Typing Test
    </h2>

    @if(!empty($gameData['settings']))
        <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-xl mb-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-500">Mode:</span>
                    <span class="font-bold">{{ $gameData['settings']['mode'] ?? 'practice' }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Durasi:</span>
                    <span class="font-bold">{{ $gameData['settings']['duration'] ?? 60 }}s</span>
                </div>
                <div>
                    <span class="text-slate-500">Bahasa:</span>
                    <span class="font-bold">{{ $gameData['settings']['language'] ?? 'id' }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Multiplayer:</span>
                    <span class="font-bold">{{ isset($gameData['settings']['multiplayer']['enabled']) && $gameData['settings']['multiplayer']['enabled'] ? '✅' : '❌' }}</span>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl">
            <p class="font-mono text-lg leading-relaxed" id="text-display">
                {{ $gameData['settings']['custom_text'] ?? 'Contoh teks untuk typing test...' }}
            </p>
            <textarea class="w-full mt-4 p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition" 
                      rows="3" placeholder="Ketik di sini..." id="typing-input"></textarea>
            <div class="flex flex-wrap gap-3 mt-4">
                <button class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition" id="start-typing">
                    <i class="fas fa-play mr-2"></i> Mulai
                </button>
                <button class="px-6 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition" id="reset-typing">
                    <i class="fas fa-redo mr-2"></i> Reset
                </button>
                <span class="text-sm text-slate-500 flex items-center ml-auto" id="typing-timer">⏱️ 0s</span>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startBtn = document.getElementById('start-typing');
                const resetBtn = document.getElementById('reset-typing');
                const input = document.getElementById('typing-input');
                const timer = document.getElementById('typing-timer');
                let startTime = null;
                let timerInterval = null;

                startBtn.addEventListener('click', function() {
                    if (startTime) return;
                    startTime = Date.now();
                    input.focus();
                    timerInterval = setInterval(function() {
                        const elapsed = Math.floor((Date.now() - startTime) / 1000);
                        timer.textContent = '⏱️ ' + elapsed + 's';
                    }, 1000);
                });

                resetBtn.addEventListener('click', function() {
                    clearInterval(timerInterval);
                    startTime = null;
                    timer.textContent = '⏱️ 0s';
                    input.value = '';
                    input.focus();
                });
            });
        </script>
        @endpush
    @else
        <div class="text-center py-8 text-slate-500">
            <p>Belum ada pengaturan untuk game typing ini.</p>
        </div>
    @endif
</div>
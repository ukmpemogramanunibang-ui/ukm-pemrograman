<div>
    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
        <span class="text-2xl">📝</span> Quiz Mode
    </h2>

    @if(!empty($gameData['settings']))
        <div class="p-4 bg-slate-100 dark:bg-slate-800 rounded-xl mb-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-500">Total Soal:</span>
                    <span class="font-bold">{{ $gameData['settings']['total_questions'] ?? 0 }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Skoring:</span>
                    <span class="font-bold">{{ $gameData['settings']['scoring_mode'] ?? 'accuracy' }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Waktu per soal:</span>
                    <span class="font-bold">{{ $gameData['settings']['time_per_question'] ?? 30 }}s</span>
                </div>
                <div>
                    <span class="text-slate-500">Acak soal:</span>
                    <span class="font-bold">{{ isset($gameData['settings']['shuffle_questions']) && $gameData['settings']['shuffle_questions'] ? '✅' : '❌' }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($gameData['questions']) && count($gameData['questions']) > 0)
        <form id="quiz-form" class="space-y-6">
            @foreach($gameData['questions'] as $index => $q)
                <div class="p-5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <p class="font-bold text-slate-800 dark:text-white mb-3">
                        <span class="text-indigo-500">#{{ $index+1 }}</span> 
                        {{ $q['question'] ?? 'Pertanyaan' }}
                    </p>

                    @if(isset($q['options']) && is_array($q['options']) && count($q['options']) > 0)
                        <div class="space-y-2">
                            @foreach($q['options'] as $optKey => $optValue)
                                <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                    <input type="radio" name="q{{ $index }}" value="{{ $optKey }}" class="w-4 h-4 text-indigo-600">
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $optValue }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 italic">[Soal tipe essay atau belum memiliki opsi]</p>
                        <textarea class="w-full mt-2 p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition" 
                                  rows="2" placeholder="Tulis jawaban..."></textarea>
                    @endif

                    @if(isset($q['correct_answer']))
                        <div class="mt-2 text-xs text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-check-circle mr-1"></i> Jawaban benar: {{ $q['correct_answer'] }}
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="flex gap-3 mt-6">
                <button type="button" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition" id="submit-quiz">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban
                </button>
                <button type="reset" class="px-6 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition">
                    <i class="fas fa-undo mr-2"></i> Reset
                </button>
            </div>
        </form>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('submit-quiz').addEventListener('click', function() {
                    const form = document.getElementById('quiz-form');
                    const formData = new FormData(form);
                    const results = [];
                    for (let [key, value] of formData.entries()) {
                        results.push(key + ': ' + value);
                    }
                    alert('Jawaban Anda:\n' + results.join('\n') + '\n\n(Fungsi submit masih simulasi)');
                });
            });
        </script>
        @endpush
    @else
        <div class="text-center py-8 text-slate-500">
            <p>Belum ada soal untuk game ini.</p>
            <p class="text-sm mt-1">Tambahkan soal melalui halaman edit game.</p>
        </div>
    @endif
</div>
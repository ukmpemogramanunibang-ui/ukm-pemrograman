<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach(['total_questions' => 'Jumlah Soal', 'total_points' => 'Total Poin', 'scoring_mode' => 'Mode Penilaian', 'time_per_question' => 'Waktu per Soal (detik)'] as $key => $label)
        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="font-bold text-slate-800 dark:text-white">{{ $settings[$key] ?? '-' }}</p>
        </div>
    @endforeach
    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
        <p class="text-xs text-slate-500 dark:text-slate-400">Acak Soal</p>
        <p class="font-bold text-slate-800 dark:text-white">{{ isset($settings['shuffle_questions']) && $settings['shuffle_questions'] ? '✅ Ya' : '❌ Tidak' }}</p>
    </div>
    <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
        <p class="text-xs text-slate-500 dark:text-slate-400">Tampilkan Jawaban</p>
        <p class="font-bold text-slate-800 dark:text-white">{{ isset($settings['show_correct_answer']) && $settings['show_correct_answer'] ? '✅ Ya' : '❌ Tidak' }}</p>
    </div>
</div>

@if(!empty($questions))
    <div class="mt-6">
        <h4 class="font-bold text-slate-800 dark:text-white mb-3">📝 Daftar Soal ({{ count($questions) }})</h4>
        <div class="space-y-2 max-h-60 overflow-y-auto">
            @foreach($questions as $index => $q)
                <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-3 text-sm flex justify-between items-start">
                    <div>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">#{{ $index+1 }}</span>
                        <span class="text-slate-700 dark:text-slate-300">{{ Str::limit($q['question'] ?? 'Tidak ada pertanyaan', 100) }}</span>
                    </div>
                    <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-full">
                        {{ $q['type'] ?? 'multiple_choice' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
@endif
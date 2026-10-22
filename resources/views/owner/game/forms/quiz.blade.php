@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <i class="fas fa-pencil-alt text-indigo-500"></i> Buat Game Quiz
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Wizard 10 tahap untuk membuat game quiz interaktif, kompetitif, dan siap uji coba.
                </p>
            </div>
            <a href="{{ route('owner.game.games.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- WIZARD STEPS (10 langkah) --}}
        <div id="wizard-steps" class="flex items-center justify-between gap-2 p-4 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-x-auto">
            @php
                $steps = [
                    ['icon' => 'fa-question-circle', 'label' => 'Jenis'],
                    ['icon' => 'fa-info-circle', 'label' => 'Informasi'],
                    ['icon' => 'fa-coins', 'label' => 'Poin'],
                    ['icon' => 'fa-clock', 'label' => 'Waktu'],
                    ['icon' => 'fa-shield-alt', 'label' => 'Keamanan'],
                    ['icon' => 'fa-database', 'label' => 'Bank Soal'],
                    ['icon' => 'fa-trophy', 'label' => 'Skill'],
                    ['icon' => 'fa-chart-bar', 'label' => 'Hasil'],
                    ['icon' => 'fa-eye', 'label' => 'Preview'],
                    ['icon' => 'fa-check-circle', 'label' => 'Simpan'],
                ];
            @endphp
            @foreach ($steps as $idx => $step)
                <div class="step-item flex items-center gap-2 text-sm font-bold text-slate-400 dark:text-slate-500" data-step="{{ $idx }}">
                    <span class="step-number w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300">
                        {{ $idx + 1 }}
                    </span>
                    <span class="step-label hidden sm:inline">{{ $step['label'] }}</span>
                    @if ($idx < count($steps) - 1)
                        <div class="hidden md:block w-8 h-0.5 bg-slate-200 dark:bg-slate-700 step-connector"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- FORM UTAMA --}}
        <form id="quiz-form" action="{{ route('owner.game.games.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            @csrf
            <input type="hidden" name="type" value="native">
            <input type="hidden" name="game_type" value="quiz">
            <input type="hidden" name="quiz_config" id="quiz-config-json" value="">
            <input type="hidden" name="test_passed" id="test-passed-input" value="0">

            {{-- STEP 0: JENIS QUIZ --}}
            <div id="step-0" class="step-content p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">📌 Pilih Jenis Quiz</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Pilih salah satu jenis quiz yang ingin Anda buat.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $types = [
                            'multiple_choice' => ['icon' => '📌', 'title' => 'Pilihan Ganda', 'desc' => 'Soal dengan pilihan A, B, C, D, dst.', 'features' => ['✅ Mendukung Skill', '✅ Sistem Poin', '✅ Timer per soal / total', '✅ Anti-Cheat lengkap']],
                            'essay' => ['icon' => '📄', 'title' => 'Essay', 'desc' => 'Soal dengan jawaban berupa teks panjang.', 'features' => ['❌ Tidak mendukung Skill', '✅ Sistem Poin', '✅ Timer per soal / total', '✅ Anti-Cheat lengkap']],
                            'mixed' => ['icon' => '📌📄', 'title' => 'Pilihan Ganda + Essay', 'desc' => 'Kombinasi soal pilihan ganda dan essay.', 'features' => ['❌ Tidak mendukung Skill', '✅ Sistem Poin', '✅ Timer per soal / total', '✅ Anti-Cheat lengkap']],
                        ];
                    @endphp
                    @foreach ($types as $key => $type)
                        <div class="quiz-type-card border-2 border-slate-200 dark:border-slate-700 rounded-2xl p-6 hover:border-indigo-500 transition cursor-pointer" data-type="{{ $key }}">
                            <div class="text-5xl mb-3">{{ $type['icon'] }}</div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $type['title'] }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $type['desc'] }}</p>
                            <ul class="mt-3 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                                @foreach ($type['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="select-quiz-type mt-4 w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Pilih</button>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="quiz_type" id="selected-quiz-type" value="">
            </div>

            {{-- STEP 1: INFORMASI GAME --}}
            <div id="step-1" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">📋 Informasi Game</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Isi informasi dasar tentang game quiz Anda.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Game <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="game-name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" placeholder="Contoh: Battle Quiz Pemrograman Dasar">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Instruksi Permainan</label>
                        <textarea name="instructions" rows="2" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" placeholder="Jelaskan aturan main...">{{ old('instructions') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" placeholder="Deskripsikan game ini...">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                        <input type="text" name="category" value="{{ old('category', 'Pemrograman') }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mode Permainan</label>
                        <select name="game_mode" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="rank">🏆 Rank</option>
                            <option value="tournament">🏅 Tournament</option>
                            <option value="battle">⚔️ Battle</option>
                            <option value="vs">🆚 VS</option>
                            <option value="practice">📖 Latihan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Developer</label>
                        <input type="text" name="developer" value="{{ old('developer', 'UNICODE') }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Status</label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="draft" selected>📝 Draft</option>
                            <option value="testing">🧪 Testing</option>
                            <option value="published">🚀 Published</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Cover Game (maks 4 file)</label>
                        <div class="flex flex-wrap items-center gap-4">
                            <input type="file" name="cover_images[]" id="cover-input" multiple accept=".jpg,.jpeg,.png,.webp,.gif" class="hidden">
                            <button type="button" onclick="document.getElementById('cover-input').click()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-md flex items-center gap-2">
                                <i class="fas fa-cloud-upload-alt"></i> Pilih Gambar
                            </button>
                            <span id="cover-count" class="text-sm text-slate-500 dark:text-slate-400">Belum ada file dipilih</span>
                        </div>
                        <div id="cover-preview" class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3"></div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Format: jpg, png, jpeg, webp, gif | Maks 2MB per file | 4 file maksimal</p>
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 2: POIN --}}
            <div id="step-2" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">💰 Sistem Poin</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Atur sistem poin untuk game quiz Anda.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Gunakan Sistem Poin?</label>
                        <div class="flex gap-6">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                                <input type="radio" name="use_points" value="1" checked onchange="togglePoints(this.value)"> <span class="font-medium">Ya</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                                <input type="radio" name="use_points" value="0" onchange="togglePoints(this.value)"> <span class="font-medium">Tidak</span>
                            </label>
                        </div>
                    </div>

                    <div id="points-config" class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">✅ Poin benar</label>
                            <input type="number" name="point_correct" value="{{ old('point_correct', 10) }}" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">❌ Poin salah</label>
                            <input type="number" name="point_wrong" value="{{ old('point_wrong', 0) }}" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">⬜ Poin kosong</label>
                            <input type="number" name="point_empty" value="{{ old('point_empty', 0) }}" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">🎉 Bonus selesai</label>
                            <input type="number" name="bonus_complete" value="{{ old('bonus_complete', 20) }}" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">🔥 Bonus streak</label>
                            <input type="number" name="bonus_streak" value="{{ old('bonus_streak', 5) }}" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">📈 Poin maksimum</label>
                            <input type="number" name="max_points" value="{{ old('max_points', 1000) }}" min="1" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Poin berdasarkan</label>
                        <select name="point_based_on" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="correct_wrong">Benar / Salah</option>
                            <option value="difficulty">Kesulitan soal</option>
                            <option value="speed">Kecepatan</option>
                            <option value="combination">Kombinasi (benar + kecepatan)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 3: WAKTU --}}
            <div id="step-3" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">⏱ Durasi & Waktu</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Atur batas waktu untuk game quiz.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mode Waktu</label>
                        <select name="time_mode" id="time_mode" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="unlimited">♾️ Tanpa batas waktu</option>
                            <option value="total" selected>⏱️ Batas waktu seluruh game</option>
                            <option value="per_question">⏳ Batas waktu setiap soal</option>
                        </select>
                    </div>

                    <div id="time-total">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Durasi total (menit)</label>
                        <input type="number" name="time_total" value="{{ old('time_total', 10) }}" min="1" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>

                    <div id="time-per-question">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Durasi per soal (detik)</label>
                        <input type="number" name="time_per_question" value="{{ old('time_per_question', 15) }}" min="5" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Saat waktu habis</label>
                        <select name="timeout_action" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="skip">⬜ Soal dianggap kosong (skip)</option>
                            <option value="auto_next">⏩ Otomatis lanjut ke soal berikutnya</option>
                            <option value="finish">🏁 Game langsung selesai</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 4: KEAMANAN --}}
            <div id="step-4" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">🛡️ Keamanan & Anti-Cheat</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Atur sistem keamanan untuk mencegah kecurangan.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_detect_tab" value="1" checked> Deteksi pindah tab
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_detect_blur" value="1" checked> Deteksi kehilangan fokus
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_detect_minimize" value="1"> Deteksi minimize browser
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_detect_disconnect" value="1" checked> Deteksi putus internet
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_random_question" value="1" checked> Randomisasi soal
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_random_options" value="1" checked> Randomisasi pilihan jawaban
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_disable_copy_paste" value="1" checked> Nonaktifkan copy-paste
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                            <input type="checkbox" name="anti_cheat_block_context_menu" value="1" checked> Blokir klik kanan
                        </label>
                    </div>

                    <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-700 pt-4 mt-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Saat pemain keluar dari halaman</label>
                        <select name="on_leave_action" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="skip">⏭️ Skip soal aktif (permanen)</option>
                            <option value="pause">⏸️ Pause game</option>
                            <option value="nothing">⏹️ Tidak melakukan apa-apa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Batas pelanggaran</label>
                        <input type="number" name="cheat_violation_limit" value="{{ old('cheat_violation_limit', 3) }}" min="1" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Hukuman per pelanggaran (poin)</label>
                        <input type="number" name="cheat_penalty" value="{{ old('cheat_penalty', 5) }}" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 5: BANK SOAL --}}
            <div id="step-5" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">📝 Bank Soal</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Buat dan kelola soal quiz Anda dengan target jumlah tetap.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">🎯 Target Jumlah Soal <span class="text-red-500">*</span></label>
                        <input type="number" id="target-questions" value="{{ old('total_questions', 10) }}" min="1" max="100" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        <input type="hidden" name="total_questions" id="total-questions-hidden" value="{{ old('total_questions', 10) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">📊 Progress</label>
                        <div class="relative pt-1">
                            <div class="flex items-center justify-between">
                                <span id="progress-label" class="text-sm font-bold text-indigo-600 dark:text-indigo-400">0 / 10</span>
                                <span id="progress-percent" class="text-sm font-bold text-slate-600 dark:text-slate-400">0%</span>
                            </div>
                            <div class="overflow-hidden h-3 text-xs flex rounded-full bg-slate-200 dark:bg-slate-700">
                                <div id="progress-bar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-emerald-500 transition-all duration-500"></div>
                            </div>
                            <p id="progress-status" class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">⚠️ Masih kurang 10 soal</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 mb-6">
                    <button type="button" id="mode-manual-btn" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold transition shadow-md hover:bg-indigo-700 flex items-center gap-2">
                        <i class="fas fa-pencil-alt"></i> Mode Manual
                    </button>
                    <button type="button" id="mode-parser-btn" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition hover:bg-slate-300 dark:hover:bg-slate-600 flex items-center gap-2">
                        <i class="fas fa-file-import"></i> Auto Parser
                    </button>
                </div>

                <div id="manual-container" class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Soal dibuat: <span id="created-count">0</span></span>
                        <button type="button" id="add-question-btn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Tambah Soal
                        </button>
                    </div>
                    <div id="questions-list" class="space-y-4 sortable-questions"></div>
                    <input type="hidden" name="questions" id="questions-json" value="{{ old('questions') }}">
                </div>

                <div id="parser-container" class="hidden">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">📋 Tempel Teks Soal</label>
                        <textarea id="parser-input" rows="8" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition font-mono text-sm" placeholder="Contoh format:
1. Apa ibu kota Indonesia?
A. Bandung
B. Jakarta
C. Surabaya
D. Medan
Jawaban: B

2. Apa kepanjangan CPU?
A. Central Processing Unit
B. Computer Program Unit
C. Central Program Utility
D. Core Processing Utility
Jawaban: A"></textarea>
                        <div class="mt-3 flex gap-3">
                            <button type="button" id="parser-analyze-btn" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center gap-2">
                                <i class="fas fa-search"></i> Analisis Teks
                            </button>
                            <button type="button" id="parser-import-btn" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition flex items-center gap-2 disabled:opacity-50" disabled>
                                <i class="fas fa-upload"></i> Masukkan ke Editor
                            </button>
                            <button type="button" id="parser-clear-btn" class="px-6 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition hover:bg-slate-300 dark:hover:bg-slate-600">
                                Bersihkan
                            </button>
                        </div>
                        <div id="parser-result" class="mt-4 hidden"></div>
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" id="step-5-next" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 6: SKILL --}}
            <div id="step-6" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">⚡ Skill & Gameplay</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Atur sistem skill dan fitur gameplay tambahan.</p>

                <div id="skill-section" class="space-y-6">
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl">
                        <p class="text-amber-700 dark:text-amber-300 font-bold">
                            <i class="fas fa-info-circle mr-2"></i> Skill hanya tersedia untuk <strong>Quiz Pilihan Ganda</strong> dengan <strong>sistem poin aktif</strong>.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Aktifkan Skill?</label>
                        <div class="flex gap-6">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                                <input type="radio" name="skill_enabled" value="1" checked onchange="toggleSkillConfig()"> Ya
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                                <input type="radio" name="skill_enabled" value="0" onchange="toggleSkillConfig()"> Tidak
                            </label>
                        </div>
                    </div>
                    <div id="skill-mode-select">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Pilih sistem skill</label>
                        <select name="skill_mode" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="all">✨ Semua skill</option>
                            <option value="selected">🎯 Pilih skill sendiri</option>
                        </select>
                    </div>

                    <div id="skill-list" class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        @php
                            $skills = [
                                'bomb' => '💣 Lempar Bom',
                                'shield' => '🛡️ Perisai',
                                'double_points' => '⚡ Ganda Poin',
                                'fifty_fifty' => '✂️ 50/50',
                                'slowmo' => '🐌 Slowmo',
                                'extra_time' => '⏱️ Tambah Waktu',
                                'poison' => '☠️ Poison',
                                'dark_question' => '🌑 Gelapkan Soal',
                                'hard_attack' => '⚔️ Serang Sulit',
                                'streak' => '🔥 Benar Berurutan'
                            ];
                        @endphp
                        @foreach ($skills as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="skill_list[]" value="{{ $key }}" checked> {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                        <h4 class="font-bold text-slate-800 dark:text-white mb-2">💰 Biaya Skill (poin)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Bom</label>
                                <input type="number" name="skill_cost_bomb" value="50" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Perisai</label>
                                <input type="number" name="skill_cost_shield" value="30" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Ganda Poin</label>
                                <input type="number" name="skill_cost_double" value="100" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">50/50</label>
                                <input type="number" name="skill_cost_fifty" value="75" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Slowmo</label>
                                <input type="number" name="skill_cost_slowmo" value="60" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Tambah Waktu</label>
                                <input type="number" name="skill_cost_extratime" value="50" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Poison</label>
                                <input type="number" name="skill_cost_poison" value="80" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Gelapkan</label>
                                <input type="number" name="skill_cost_dark" value="60" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                            <div>
                                <label class="text-sm text-slate-600 dark:text-slate-400">Serang Sulit</label>
                                <input type="number" name="skill_cost_hard" value="120" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 7: HASIL --}}
            <div id="step-7" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">🏆 Hasil & Ranking</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Atur tampilan hasil akhir dan sistem pemenang.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Tampilkan di Hasil Akhir</h3>
                    </div>
                    <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_total_points" value="1" checked> Total poin
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_correct_count" value="1" checked> Jumlah benar
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_wrong_count" value="1" checked> Jumlah salah
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_empty_count" value="1" checked> Jumlah kosong
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_accuracy" value="1" checked> Akurasi
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_total_time" value="1"> Waktu bermain
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_ranking" value="1" checked> Ranking
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_streak" value="1"> Streak tertinggi
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_skills_used" value="1"> Skill digunakan
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                            <input type="checkbox" name="show_violations" value="1"> Pelanggaran
                        </label>
                    </div>

                    <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-700 pt-4 mt-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tampilkan pembahasan</label>
                        <select name="show_explanation" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="after_each">📖 Setelah setiap soal</option>
                            <option value="after_finish" selected>📖 Setelah selesai</option>
                            <option value="never">🚫 Tidak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Sistem Pemenang</label>
                        <select name="winner_criteria" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="points">🏆 Poin tertinggi</option>
                            <option value="accuracy">🎯 Akurasi tertinggi</option>
                            <option value="speed">⚡ Waktu tercepat</option>
                            <option value="combination">⭐ Kombinasi poin + akurasi + waktu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Passing score (%)</label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', 60) }}" min="0" max="100" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 8: PREVIEW & UJI COBA --}}
            <div id="step-8" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">🎮 Preview & Uji Coba</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Lihat tampilan game dan uji coba sebelum menyimpan.</p>

                {{-- Card Preview --}}
                <div id="preview-card" class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 rounded-3xl border border-indigo-200 dark:border-indigo-800 p-6 mb-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <span class="text-4xl" id="preview-icon">🎮</span>
                                <h3 class="text-2xl font-black text-slate-800 dark:text-white" id="preview-name">-</h3>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300" id="preview-type">-</span>
                                <span class="px-3 py-1 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300" id="preview-mode">-</span>
                                <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300" id="preview-questions">0 Soal</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-3" id="preview-desc">-</p>
                            <div class="grid grid-cols-3 gap-3 mt-4">
                                <div class="bg-white dark:bg-slate-800 rounded-xl p-3 text-center shadow-sm">
                                    <p class="text-xs text-slate-500">🏆 Poin</p>
                                    <p class="font-bold text-indigo-600 dark:text-indigo-400" id="preview-points">-</p>
                                </div>
                                <div class="bg-white dark:bg-slate-800 rounded-xl p-3 text-center shadow-sm">
                                    <p class="text-xs text-slate-500">⏱ Waktu</p>
                                    <p class="font-bold text-amber-600 dark:text-amber-400" id="preview-timer">-</p>
                                </div>
                                <div class="bg-white dark:bg-slate-800 rounded-xl p-3 text-center shadow-sm">
                                    <p class="text-xs text-slate-500">⚡ Skill</p>
                                    <p class="font-bold text-emerald-600 dark:text-emerald-400" id="preview-skills">-</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center gap-3">
                            <button type="button" id="open-simulator-btn" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/30 transition flex items-center gap-3 text-lg">
                                <i class="fas fa-play-circle"></i> 🧪 Uji Coba Game
                            </button>
                            <span id="test-status" class="text-sm text-slate-500 dark:text-slate-400">
                                ⚠️ Belum diuji coba
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Daftar Soal Accordion --}}
                <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                    <h4 class="font-bold text-slate-800 dark:text-white mb-3">📋 Daftar Soal (<span id="preview-question-count">0</span>)</h4>
                    <div id="question-accordion" class="space-y-2 max-h-80 overflow-y-auto pr-2"></div>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <button type="button" id="step-8-next" class="step-next px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
                </div>
            </div>

            {{-- STEP 9: SPONSOR & SIMPAN --}}
            <div id="step-9" class="step-content hidden p-6 md:p-8">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-2">💼 Sponsor & Simpan</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Tambahkan sponsor untuk game Anda (opsional), lalu simpan.</p>

                {{-- Pilihan sponsor --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Apakah Anda ingin menambahkan sponsor?</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                            <input type="radio" name="sponsor_choice" value="1" onchange="toggleSponsorForm(true)"> Ya
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300">
                            <input type="radio" name="sponsor_choice" value="0" checked onchange="toggleSponsorForm(false)"> Tidak
                        </label>
                    </div>
                </div>

                {{-- Form Sponsor --}}
                <div id="sponsor-form" class="hidden border-t border-slate-200 dark:border-slate-700 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Nama Sponsor <span class="text-red-500">*</span></label>
                            <input type="text" name="sponsor_name" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Website Sponsor <span class="text-red-500">*</span></label>
                            <input type="url" name="sponsor_website" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" placeholder="https://contoh.com">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tagline</label>
                            <input type="text" name="sponsor_tagline" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Alamat</label>
                            <input type="text" name="sponsor_address" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Deskripsi Sponsor</label>
                            <textarea name="sponsor_description" rows="2" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Status uji coba --}}
                <div id="final-test-status" class="mt-4 p-4 rounded-2xl border">
                    <p class="font-bold flex items-center gap-2">
                        <span id="final-test-icon" class="text-amber-500">⚠️</span>
                        <span id="final-test-text" class="text-slate-700 dark:text-slate-300">Belum diuji coba</span>
                    </p>
                </div>

                <div class="flex justify-between mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" class="step-back px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">← Kembali</button>
                    <div class="flex gap-3">
                        <button type="button" id="final-test-btn" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition flex items-center gap-2">
                            <i class="fas fa-flask"></i> Uji Coba Lagi
                        </button>
                        <button type="submit" name="action" value="save" id="save-game-btn" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-save"></i> 💾 SIMPAN GAME
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- SIMULATOR MODAL --}}
<div id="simulator-modal" class="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-sm flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-flask text-amber-500"></i> 🧪 UJI COBA GAME
            </h3>
            <button id="close-simulator" class="text-slate-500 hover:text-slate-700 dark:hover:text-white text-2xl">&times;</button>
        </div>
        <div id="simulator-body" class="flex-1 overflow-y-auto p-6"></div>
        <div id="simulator-footer" class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
            <button id="simulator-close-btn" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition">Tutup</button>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ================================================================
    // VARIABLES & STATE
    // ================================================================
    let currentStep = 0;
    const totalSteps = 10; // 0-9
    const form = document.getElementById('quiz-form');
    let questions = [];
    let targetQuestions = parseInt(document.getElementById('target-questions').value) || 10;
    let testPassed = false;
    let coverFiles = [];

    // ================================================================
    // WIZARD NAVIGATION
    // ================================================================
    function goToStep(step) {
        if (step < 0) step = 0;
        if (step >= totalSteps) step = totalSteps - 1;
        currentStep = step;

        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        const target = document.getElementById('step-' + step);
        if (target) target.classList.remove('hidden');

        document.querySelectorAll('.step-item').forEach((el, idx) => {
            const num = el.querySelector('.step-number');
            if (idx <= step) {
                el.classList.remove('text-slate-400', 'dark:text-slate-500');
                el.classList.add('text-indigo-600', 'dark:text-indigo-400');
                num.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
                num.classList.add('bg-indigo-600', 'text-white');
            } else {
                el.classList.remove('text-indigo-600', 'dark:text-indigo-400');
                el.classList.add('text-slate-400', 'dark:text-slate-500');
                num.classList.remove('bg-indigo-600', 'text-white');
                num.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-300');
            }
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });

        if (step === 8) updatePreview();
        if (step === 9) updateFinalStatus();
        if (step === 5) validateBankSoal();
    }

    document.querySelectorAll('.step-next').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentStep === 0 && !document.getElementById('selected-quiz-type').value) {
                alert('Silakan pilih jenis quiz terlebih dahulu.');
                return;
            }
            if (currentStep === 5) {
                const validation = validateBankSoal();
                if (!validation.valid) {
                    alert(validation.message);
                    return;
                }
            }
            if (currentStep === 8 && !testPassed) {
                if (!confirm('Anda belum menguji coba game. Lanjutkan tanpa uji coba?')) return;
            }
            if (currentStep < totalSteps - 1) {
                goToStep(currentStep + 1);
            }
        });
    });

    document.querySelectorAll('.step-back').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentStep > 0) goToStep(currentStep - 1);
        });
    });

    // ================================================================
    // JENIS QUIZ
    // ================================================================
    document.querySelectorAll('.quiz-type-card').forEach(card => {
        card.addEventListener('click', function() {
            const type = this.dataset.type;
            document.querySelectorAll('.quiz-type-card').forEach(c => c.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20'));
            this.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
            document.getElementById('selected-quiz-type').value = type;
            goToStep(1);
        });
        card.querySelector('.select-quiz-type').addEventListener('click', function(e) {
            e.stopPropagation();
            card.click();
        });
    });

    // ================================================================
    // POIN TOGGLE
    // ================================================================
    window.togglePoints = function(val) {
        const config = document.getElementById('points-config');
        if (val === '1') config.style.display = 'grid';
        else config.style.display = 'none';
    };
    setTimeout(() => {
        const checked = document.querySelector('input[name="use_points"]:checked');
        if (checked) togglePoints(checked.value);
    }, 200);

    // ================================================================
    // TIME MODE
    // ================================================================
    document.getElementById('time_mode').addEventListener('change', function() {
        const total = document.getElementById('time-total');
        const per = document.getElementById('time-per-question');
        if (this.value === 'total') { total.style.display = 'block'; per.style.display = 'none'; }
        else if (this.value === 'per_question') { total.style.display = 'none'; per.style.display = 'block'; }
        else { total.style.display = 'none'; per.style.display = 'none'; }
    });
    document.getElementById('time_mode').dispatchEvent(new Event('change'));

    // ================================================================
    // SKILL CONFIG
    // ================================================================
    window.toggleSkillConfig = function() {
        const enabled = document.querySelector('input[name="skill_enabled"][value="1"]:checked');
        document.getElementById('skill-mode-select').style.display = enabled ? 'block' : 'none';
        document.getElementById('skill-list').style.display = enabled ? 'grid' : 'none';
    };
    setTimeout(toggleSkillConfig, 300);

    // ================================================================
    // COVER IMAGE
    // ================================================================
    const coverInput = document.getElementById('cover-input');
    const coverPreview = document.getElementById('cover-preview');
    const coverCount = document.getElementById('cover-count');

    coverInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        if (coverFiles.length + files.length > 4) {
            alert('⚠️ Maksimal 4 file cover!');
            this.value = '';
            return;
        }
        coverFiles = coverFiles.concat(files);
        renderCoverPreview();
        updateCoverCount();
        this.value = '';
    });

    function renderCoverPreview() {
        coverPreview.innerHTML = '';
        coverFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover" alt="Cover ${index+1}">
                    <button type="button" onclick="removeCover(${index})" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold opacity-0 group-hover:opacity-100 transition">✕</button>
                    <span class="absolute bottom-1 left-2 text-xs text-white bg-black/60 px-2 py-0.5 rounded truncate max-w-[90%]">${file.name}</span>
                `;
                coverPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeCover = function(index) {
        coverFiles.splice(index, 1);
        renderCoverPreview();
        updateCoverCount();
        updateCoverInput();
    };

    function updateCoverCount() {
        coverCount.textContent = coverFiles.length + ' file dipilih (maks 4)';
        if (coverFiles.length === 0) coverCount.textContent = 'Belum ada file dipilih';
    }

    function updateCoverInput() {
        const dataTransfer = new DataTransfer();
        coverFiles.forEach(file => dataTransfer.items.add(file));
        coverInput.files = dataTransfer.files;
    }

    // ================================================================
    // BANK SOAL - CORE
    // ================================================================
    const questionsList = document.getElementById('questions-list');
    const addBtn = document.getElementById('add-question-btn');
    const jsonInput = document.getElementById('questions-json');
    const totalHidden = document.getElementById('total-questions-hidden');
    const progressBar = document.getElementById('progress-bar');
    const progressLabel = document.getElementById('progress-label');
    const progressPercent = document.getElementById('progress-percent');
    const progressStatus = document.getElementById('progress-status');
    const createdCount = document.getElementById('created-count');

    try {
        const old = jsonInput.value;
        if (old) {
            const parsed = JSON.parse(old);
            if (Array.isArray(parsed)) questions = parsed;
        }
    } catch (e) {}

    if (questions.length === 0) {
        questions = [createEmptyQuestion()];
    }

    function createEmptyQuestion() {
        return {
            id: Date.now() + Math.random() * 1000,
            question: '',
            type: 'multiple_choice',
            options: [
                { id: Date.now() + 1, label: 'A', text: '', correct: false },
                { id: Date.now() + 2, label: 'B', text: '', correct: false },
                { id: Date.now() + 3, label: 'C', text: '', correct: false },
                { id: Date.now() + 4, label: 'D', text: '', correct: false }
            ],
            weight: 10,
            hint: '',
            difficulty: 'medium',
            explanation: '',
            image: null
        };
    }

    function renderQuestions() {
        questionsList.innerHTML = '';
        questions.forEach((q, index) => {
            const card = document.createElement('div');
            card.className = 'question-card p-4 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800/50 hover:shadow-md transition relative';
            card.dataset.index = index;

            const header = document.createElement('div');
            header.className = 'flex justify-between items-center mb-3 cursor-grab';
            header.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-grip-vertical text-slate-400 dark:text-slate-500"></i>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400">Soal #${index + 1}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">${q.type === 'multiple_choice' ? 'PG' : 'Essay'}</span>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="move-up-btn text-slate-400 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition" data-index="${index}"><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="move-down-btn text-slate-400 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition" data-index="${index}"><i class="fas fa-arrow-down"></i></button>
                    <button type="button" class="remove-question-btn text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-500 transition" data-index="${index}"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            card.appendChild(header);

            const body = document.createElement('div');
            body.className = 'space-y-3';
            body.innerHTML = `
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Pertanyaan <span class="text-red-500">*</span></label>
                    <input type="text" class="question-text w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" value="${escapeHtml(q.question)}" placeholder="Masukkan pertanyaan...">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tipe Soal</label>
                        <select class="question-type w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="multiple_choice" ${q.type === 'multiple_choice' ? 'selected' : ''}>Pilihan Ganda</option>
                            <option value="essay" ${q.type === 'essay' ? 'selected' : ''}>Essay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Bobot (poin)</label>
                        <input type="number" class="question-weight w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" value="${q.weight || 10}" min="1">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Kesulitan</label>
                        <select class="question-difficulty w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <option value="easy" ${q.difficulty === 'easy' ? 'selected' : ''}>🟢 Mudah</option>
                            <option value="medium" ${q.difficulty === 'medium' ? 'selected' : ''}>🟡 Sedang</option>
                            <option value="hard" ${q.difficulty === 'hard' ? 'selected' : ''}>🔴 Sulit</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Hint (opsional)</label>
                    <input type="text" class="question-hint w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" value="${escapeHtml(q.hint || '')}" placeholder="Petunjuk untuk soal ini...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Pembahasan (opsional)</label>
                    <input type="text" class="question-explanation w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" value="${escapeHtml(q.explanation || '')}" placeholder="Penjelasan jawaban...">
                </div>
                <div class="options-container" data-index="${index}">
                    ${renderOptions(q, index)}
                </div>
                <button type="button" class="add-option-btn text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-bold transition" data-index="${index}">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Pilihan
                </button>
            `;
            card.appendChild(body);
            questionsList.appendChild(card);
        });

        attachQuestionEvents();
        updateProgress();
        updateJson();
    }

    function renderOptions(q, qIndex) {
        let html = `<div class="space-y-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Pilihan Jawaban</label>`;
        q.options.forEach((opt, oIdx) => {
            html += `
                <div class="flex items-center gap-3 option-row">
                    <span class="font-mono font-bold text-slate-600 dark:text-slate-400 w-6">${opt.label}</span>
                    <input type="text" class="option-text flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition" value="${escapeHtml(opt.text)}" placeholder="Teks pilihan...">
                    <label class="flex items-center gap-1 text-sm text-slate-700 dark:text-slate-300">
                        <input type="radio" name="correct_${qIndex}" value="${oIdx}" ${opt.correct ? 'checked' : ''} class="option-correct w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                        <span>Benar</span>
                    </label>
                    <button type="button" class="remove-option-btn text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-500 transition ${q.options.length <= 2 ? 'hidden' : ''}" data-qindex="${qIndex}" data-oindex="${oIdx}">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            `;
        });
        html += `</div>`;
        return html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function attachQuestionEvents() {
        document.querySelectorAll('.remove-question-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (questions.length <= 1) {
                    alert('Minimal harus ada 1 soal.');
                    return;
                }
                if (confirm('Hapus soal #' + (idx+1) + '?')) {
                    questions.splice(idx, 1);
                    renderQuestions();
                }
            });
        });

        document.querySelectorAll('.move-up-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (idx > 0) {
                    [questions[idx], questions[idx-1]] = [questions[idx-1], questions[idx]];
                    renderQuestions();
                }
            });
        });
        document.querySelectorAll('.move-down-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (idx < questions.length - 1) {
                    [questions[idx], questions[idx+1]] = [questions[idx+1], questions[idx]];
                    renderQuestions();
                }
            });
        });

        document.querySelectorAll('.add-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                const q = questions[idx];
                if (q.options.length >= 6) {
                    alert('Maksimal 6 pilihan.');
                    return;
                }
                const nextLabel = String.fromCharCode(65 + q.options.length);
                q.options.push({ id: Date.now() + Math.random()*1000, label: nextLabel, text: '', correct: false });
                renderQuestions();
            });
        });

        document.querySelectorAll('.remove-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const qIdx = parseInt(this.dataset.qindex);
                const oIdx = parseInt(this.dataset.oindex);
                const q = questions[qIdx];
                if (q.options.length <= 2) {
                    alert('Minimal harus ada 2 pilihan.');
                    return;
                }
                q.options.splice(oIdx, 1);
                q.options.forEach((opt, i) => opt.label = String.fromCharCode(65 + i));
                renderQuestions();
            });
        });

        questionsList.addEventListener('input', function(e) {
            const target = e.target;
            if (target.classList.contains('question-text') || target.classList.contains('question-weight') ||
                target.classList.contains('question-hint') || target.classList.contains('question-explanation') ||
                target.classList.contains('option-text')) {
                updateQuestionsFromDOM();
            }
        });

        questionsList.addEventListener('change', function(e) {
            const target = e.target;
            if (target.classList.contains('question-type') || target.classList.contains('question-difficulty') ||
                target.classList.contains('option-correct')) {
                updateQuestionsFromDOM();
                updateProgress();
            }
        });
    }

    function updateQuestionsFromDOM() {
        const cards = document.querySelectorAll('.question-card');
        cards.forEach((card, idx) => {
            const q = questions[idx];
            if (!q) return;
            q.question = card.querySelector('.question-text').value;
            q.type = card.querySelector('.question-type').value;
            q.weight = parseInt(card.querySelector('.question-weight').value) || 10;
            q.difficulty = card.querySelector('.question-difficulty').value;
            q.hint = card.querySelector('.question-hint').value;
            q.explanation = card.querySelector('.question-explanation').value;

            const optionRows = card.querySelectorAll('.option-row');
            const newOptions = [];
            optionRows.forEach((row, oIdx) => {
                const text = row.querySelector('.option-text').value;
                const correct = row.querySelector('.option-correct').checked;
                const label = q.options[oIdx] ? q.options[oIdx].label : String.fromCharCode(65 + oIdx);
                newOptions.push({ id: q.options[oIdx]?.id || Date.now()+Math.random()*1000, label, text, correct });
            });
            q.options = newOptions;
        });
        updateJson();
        updateProgress();
    }

    function updateJson() {
        jsonInput.value = JSON.stringify(questions);
        totalHidden.value = targetQuestions;
    }

    function updateProgress() {
        const created = questions.length;
        const target = targetQuestions;
        const pct = target > 0 ? Math.min(100, Math.round((created / target) * 100)) : 0;
        progressBar.style.width = pct + '%';
        progressLabel.textContent = created + ' / ' + target;
        progressPercent.textContent = pct + '%';
        createdCount.textContent = created;

        let statusText, statusColor;
        if (created === target && target > 0) {
            statusText = '✅ Jumlah soal sesuai target!';
            statusColor = 'text-emerald-600 dark:text-emerald-400';
        } else if (created > target) {
            statusText = '❌ Kelebihan ' + (created - target) + ' soal. Hapus beberapa soal.';
            statusColor = 'text-red-600 dark:text-red-400';
        } else {
            statusText = '⚠️ Masih kurang ' + (target - created) + ' soal.';
            statusColor = 'text-amber-600 dark:text-amber-400';
        }
        progressStatus.textContent = statusText;
        progressStatus.className = 'mt-1 text-xs font-semibold ' + statusColor;

        const nextBtn = document.getElementById('step-5-next');
        if (created === target && target > 0) {
            nextBtn.disabled = false;
            nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            nextBtn.disabled = true;
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function validateBankSoal() {
        const created = questions.length;
        const target = targetQuestions;
        if (created !== target) {
            return { valid: false, message: `Jumlah soal harus tepat ${target} (saat ini ${created}).` };
        }
        for (let i = 0; i < questions.length; i++) {
            const q = questions[i];
            if (!q.question.trim()) {
                return { valid: false, message: `Soal #${i+1}: Pertanyaan kosong.` };
            }
            if (q.type === 'multiple_choice') {
                const hasCorrect = q.options.some(o => o.correct);
                if (!hasCorrect) {
                    return { valid: false, message: `Soal #${i+1}: Belum memilih jawaban benar.` };
                }
                const emptyOpts = q.options.some(o => !o.text.trim());
                if (emptyOpts) {
                    return { valid: false, message: `Soal #${i+1}: Ada pilihan yang kosong.` };
                }
                if (q.options.length < 2) {
                    return { valid: false, message: `Soal #${i+1}: Minimal 2 pilihan.` };
                }
            }
        }
        return { valid: true, message: '✅ Semua soal valid!' };
    }

    // ================================================================
    // AUTO PARSER
    // ================================================================
    const modeManualBtn = document.getElementById('mode-manual-btn');
    const modeParserBtn = document.getElementById('mode-parser-btn');
    const manualContainer = document.getElementById('manual-container');
    const parserContainer = document.getElementById('parser-container');
    const parserInput = document.getElementById('parser-input');
    const parserAnalyzeBtn = document.getElementById('parser-analyze-btn');
    const parserImportBtn = document.getElementById('parser-import-btn');
    const parserClearBtn = document.getElementById('parser-clear-btn');
    const parserResult = document.getElementById('parser-result');

    let parsedQuestions = [];

    modeManualBtn.addEventListener('click', function() {
        manualContainer.classList.remove('hidden');
        parserContainer.classList.add('hidden');
        this.classList.add('bg-indigo-600', 'text-white');
        this.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
        modeParserBtn.classList.remove('bg-indigo-600', 'text-white');
        modeParserBtn.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
        parserResult.classList.add('hidden');
    });

    modeParserBtn.addEventListener('click', function() {
        manualContainer.classList.add('hidden');
        parserContainer.classList.remove('hidden');
        this.classList.add('bg-indigo-600', 'text-white');
        this.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
        modeManualBtn.classList.remove('bg-indigo-600', 'text-white');
        modeManualBtn.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
    });

    parserAnalyzeBtn.addEventListener('click', function() {
        const text = parserInput.value.trim();
        if (!text) {
            alert('Masukkan teks soal terlebih dahulu.');
            return;
        }

        const result = parseQuestions(text);
        parsedQuestions = result.questions;
        parserResult.classList.remove('hidden');
        parserResult.innerHTML = `
            <div class="mt-4 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                <h4 class="font-bold text-slate-800 dark:text-white mb-2">📊 Hasil Analisis</h4>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="text-slate-500 dark:text-slate-400">Soal terdeteksi</span> <span class="font-bold text-indigo-600 dark:text-indigo-400">${result.questions.length}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-400">Pilihan terdeteksi</span> <span class="font-bold text-indigo-600 dark:text-indigo-400">${result.totalOptions}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-400">Kunci jawaban ditemukan</span> <span class="font-bold text-emerald-600 dark:text-emerald-400">${result.foundAnswers}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-400">Soal bermasalah</span> <span class="font-bold text-amber-600 dark:text-amber-400">${result.problems}</span></div>
                </div>
                ${result.errors.length > 0 ? `
                    <div class="mt-2 p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <p class="text-red-600 dark:text-red-400 text-sm font-bold">⚠️ Soal bermasalah:</p>
                        <ul class="list-disc list-inside text-sm text-red-500 dark:text-red-300">
                            ${result.errors.map(e => `<li>${e}</li>`).join('')}
                        </ul>
                    </div>
                ` : `
                    <div class="mt-2 p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                        <p class="text-emerald-600 dark:text-emerald-400 text-sm font-bold">✅ Semua soal terdeteksi dengan baik.</p>
                    </div>
                `}
                ${result.questions.length > 0 ? `<button type="button" id="parser-import-execute" class="mt-3 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition">📥 Masukkan ${result.questions.length} soal ke editor</button>` : ''}
            </div>
        `;

        const importExecute = document.getElementById('parser-import-execute');
        if (importExecute) {
            importExecute.addEventListener('click', function() {
                if (parsedQuestions.length === 0) {
                    alert('Tidak ada soal yang bisa diimpor.');
                    return;
                }
                if (questions.length > 0) {
                    if (!confirm('Akan mengganti semua soal yang ada dengan ' + parsedQuestions.length + ' soal baru. Lanjutkan?')) return;
                }
                questions = parsedQuestions.map(q => ({
                    id: Date.now() + Math.random() * 1000,
                    question: q.question || '',
                    type: q.type || 'multiple_choice',
                    options: (q.options || []).map((opt, idx) => ({
                        id: Date.now() + idx + Math.random() * 1000,
                        label: String.fromCharCode(65 + idx),
                        text: opt.text || '',
                        correct: opt.correct || false
                    })),
                    weight: q.weight || 10,
                    hint: q.hint || '',
                    difficulty: q.difficulty || 'medium',
                    explanation: q.explanation || '',
                    image: null
                }));
                targetQuestions = questions.length;
                document.getElementById('target-questions').value = targetQuestions;
                totalHidden.value = targetQuestions;
                renderQuestions();
                modeManualBtn.click();
                parserResult.innerHTML = `<div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <p class="text-emerald-700 dark:text-emerald-300 font-bold">✅ ${questions.length} soal berhasil diimpor!</p>
                </div>`;
            });
        }
    });

    parserClearBtn.addEventListener('click', function() {
        parserInput.value = '';
        parserResult.classList.add('hidden');
        parsedQuestions = [];
    });

    function parseQuestions(text) {
        const lines = text.split('\n').map(l => l.trim()).filter(l => l);
        const questions = [];
        let currentQuestion = null;
        let currentOptions = [];
        let errors = [];
        let totalOptions = 0;
        let foundAnswers = 0;
        let problems = 0;

        const optionRegex = /^([A-F])[\.\)]\s*(.*)/;
        const answerRegex = /^(jawaban|kunci|answer)\s*[:=]\s*([A-F])/i;

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];

            const questionMatch = line.match(/^(\d+)[\.\)]\s*(.*)/);
            if (questionMatch) {
                if (currentQuestion) {
                    finishQuestion();
                }
                currentQuestion = {
                    question: questionMatch[2].trim(),
                    type: 'multiple_choice',
                    options: [],
                    weight: 10,
                    hint: '',
                    difficulty: 'medium',
                    explanation: ''
                };
                currentOptions = [];
                continue;
            }

            const optMatch = line.match(optionRegex);
            if (optMatch && currentQuestion) {
                const label = optMatch[1];
                const text = optMatch[2].trim();
                currentOptions.push({ label, text, correct: false });
                totalOptions++;
                continue;
            }

            const ansMatch = line.match(answerRegex);
            if (ansMatch && currentQuestion) {
                const correctLabel = ansMatch[2].toUpperCase();
                const found = currentOptions.find(o => o.label === correctLabel);
                if (found) {
                    found.correct = true;
                    foundAnswers++;
                } else {
                    errors.push(`Soal #${questions.length+1}: Jawaban "${correctLabel}" tidak ditemukan di pilihan.`);
                    problems++;
                }
                continue;
            }

            if (currentQuestion && !line.match(/^[A-F][\.\)]/) && !line.match(/^(jawaban|kunci|answer)/i)) {
                currentQuestion.question += ' ' + line;
            }
        }

        if (currentQuestion) {
            finishQuestion();
        }

        function finishQuestion() {
            if (currentQuestion) {
                if (currentOptions.length === 0) {
                    currentQuestion.type = 'essay';
                } else {
                    currentQuestion.type = 'multiple_choice';
                    currentQuestion.options = currentOptions;
                    const hasCorrect = currentOptions.some(o => o.correct);
                    if (!hasCorrect) {
                        errors.push(`Soal #${questions.length+1}: Tidak ada jawaban benar yang dipilih.`);
                        problems++;
                        if (currentOptions.length > 0) currentOptions[0].correct = true;
                    }
                }
                questions.push(currentQuestion);
                currentQuestion = null;
                currentOptions = [];
            }
        }

        if (questions.length === 0 && text.length > 0) {
            const parts = text.split(/\n\s*\n/);
            if (parts.length > 1) {
                parts.forEach((p, idx) => {
                    const lines2 = p.split('\n').filter(l => l.trim());
                    const qText = lines2.join(' ');
                    questions.push({
                        question: qText,
                        type: 'essay',
                        options: [],
                        weight: 10,
                        hint: '',
                        difficulty: 'medium',
                        explanation: ''
                    });
                });
            } else {
                questions.push({
                    question: text,
                    type: 'essay',
                    options: [],
                    weight: 10,
                    hint: '',
                    difficulty: 'medium',
                    explanation: ''
                });
            }
            problems += questions.length;
        }

        return {
            questions: questions,
            totalOptions: totalOptions,
            foundAnswers: foundAnswers,
            problems: problems,
            errors: errors
        };
    }

    // ================================================================
    // ADD QUESTION
    // ================================================================
    addBtn.addEventListener('click', function() {
        if (questions.length >= 100) {
            alert('Maksimal 100 soal.');
            return;
        }
        if (questions.length >= targetQuestions) {
            if (!confirm('Jumlah soal sudah mencapai target ' + targetQuestions + '. Lanjutkan menambah?')) return;
        }
        questions.push(createEmptyQuestion());
        renderQuestions();
        const lastCard = questionsList.lastElementChild;
        if (lastCard) lastCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // ================================================================
    // DRAG & DROP
    // ================================================================
    if (typeof Sortable !== 'undefined') {
        const sortable = new Sortable(document.getElementById('questions-list'), {
            handle: '.cursor-grab',
            animation: 150,
            onEnd: function(evt) {
                const oldIndex = evt.oldIndex;
                const newIndex = evt.newIndex;
                if (oldIndex !== newIndex) {
                    const moved = questions.splice(oldIndex, 1)[0];
                    questions.splice(newIndex, 0, moved);
                    renderQuestions();
                }
            }
        });
    }

    // ================================================================
    // PREVIEW (STEP 8)
    // ================================================================
    function updatePreview() {
        const name = document.getElementById('game-name').value;
        const type = document.getElementById('selected-quiz-type').value;
        const typeLabels = { multiple_choice: 'Pilihan Ganda', essay: 'Essay', mixed: 'PG + Essay' };
        const mode = document.querySelector('[name="game_mode"]').value;
        const modeLabels = { rank: '🏆 Rank', tournament: '🏅 Tournament', battle: '⚔️ Battle', vs: '🆚 VS', practice: '📖 Latihan' };
        const desc = document.querySelector('[name="description"]').value || 'Tidak ada deskripsi';

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-type').textContent = typeLabels[type] || type;
        document.getElementById('preview-mode').textContent = modeLabels[mode] || mode;
        document.getElementById('preview-desc').textContent = desc;
        document.getElementById('preview-questions').textContent = questions.length + ' Soal';
        document.getElementById('preview-question-count').textContent = questions.length;

        const usePoints = document.querySelector('input[name="use_points"]:checked');
        document.getElementById('preview-points').textContent = (usePoints && usePoints.value === '1') ? 'Aktif' : 'Tidak aktif';

        const timeMode = document.getElementById('time_mode').value;
        const timerLabels = { unlimited: '♾️', total: (document.querySelector('[name="time_total"]')?.value || 10) + ' menit', per_question: (document.querySelector('[name="time_per_question"]')?.value || 15) + ' detik/soal' };
        document.getElementById('preview-timer').textContent = timerLabels[timeMode] || '-';

        const skillEnabled = document.querySelector('input[name="skill_enabled"][value="1"]:checked');
        document.getElementById('preview-skills').textContent = skillEnabled ? '✅ Aktif' : '❌ Tidak aktif';

        // Daftar soal accordion
        const container = document.getElementById('question-accordion');
        container.innerHTML = '';
        questions.forEach((q, idx) => {
            const div = document.createElement('div');
            div.className = 'border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden';
            const header = document.createElement('div');
            header.className = 'flex justify-between items-center p-3 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition';
            header.innerHTML = `
                <span class="font-bold text-slate-700 dark:text-slate-300">Soal #${idx+1}</span>
                <span class="text-xs px-2 py-1 rounded-full ${q.question.trim() ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'}">
                    ${q.question.trim() ? '✅ Valid' : '❌ Kosong'}
                </span>
            `;
            const body = document.createElement('div');
            body.className = 'p-3 hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700';
            body.innerHTML = `
                <p class="font-medium text-slate-800 dark:text-white">${q.question || '(Pertanyaan kosong)'}</p>
                ${q.type === 'multiple_choice' ? `
                    <ul class="mt-2 space-y-1 text-sm">
                        ${q.options.map(o => `<li class="${o.correct ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-400'}">${o.label}. ${o.text || '(kosong)'} ${o.correct ? '✅' : ''}</li>`).join('')}
                    </ul>
                ` : `<p class="text-sm text-slate-500 dark:text-slate-400">Jenis: Essay</p>`}
            `;
            div.appendChild(header);
            div.appendChild(body);
            header.addEventListener('click', function() {
                body.classList.toggle('hidden');
            });
            container.appendChild(div);
        });
    }

    // ================================================================
    // SIMULATOR
    // ================================================================
    const modal = document.getElementById('simulator-modal');
    const body = document.getElementById('simulator-body');
    const footer = document.getElementById('simulator-footer');

    let simState = {
        current: 0,
        answers: [],
        started: false,
        finished: false,
        score: 0,
        correct: 0,
        wrong: 0,
        empty: 0,
        timerInterval: null,
        timeLeft: 0,
    };

    document.getElementById('open-simulator-btn').addEventListener('click', function() {
        if (questions.length === 0) {
            alert('Tidak ada soal untuk diuji coba.');
            return;
        }
        const invalid = questions.some(q => !q.question.trim() || (q.type === 'multiple_choice' && !q.options.some(o => o.correct)));
        if (invalid) {
            alert('Ada soal yang belum lengkap. Perbaiki di Bank Soal.');
            return;
        }
        startSimulator();
    });

    document.getElementById('close-simulator').addEventListener('click', closeSimulator);
    document.getElementById('simulator-close-btn').addEventListener('click', closeSimulator);
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeSimulator();
    });

    function startSimulator() {
        modal.classList.remove('hidden');
        simState.current = 0;
        simState.answers = [];
        simState.started = false;
        simState.finished = false;
        simState.score = 0;
        simState.correct = 0;
        simState.wrong = 0;
        simState.empty = 0;
        if (simState.timerInterval) clearInterval(simState.timerInterval);

        renderSimQuestion();
        document.getElementById('simulator-footer').innerHTML = `
            <button id="simulator-prev" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition disabled:opacity-50" disabled>← Sebelumnya</button>
            <button id="simulator-next" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">Selanjutnya →</button>
            <button id="simulator-finish" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition hidden">✅ Selesai</button>
        `;
        document.getElementById('simulator-prev').addEventListener('click', () => { if (simState.current > 0) { simState.current--; renderSimQuestion(); } });
        document.getElementById('simulator-next').addEventListener('click', () => {
            saveCurrentAnswer();
            if (simState.current < questions.length - 1) {
                simState.current++;
                renderSimQuestion();
            } else {
                finishSimulator();
            }
        });
        document.getElementById('simulator-finish').addEventListener('click', finishSimulator);
    }

    function renderSimQuestion() {
        const q = questions[simState.current];
        if (!q) return;
        const total = questions.length;
        const progress = `${simState.current+1} / ${total}`;
        const html = `
            <div class="mb-4 flex justify-between items-center">
                <span class="font-bold text-indigo-600 dark:text-indigo-400">Soal ${progress}</span>
                <span class="text-sm text-slate-500 dark:text-slate-400">⏱ <span id="sim-timer">--</span></span>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl mb-4">
                <p class="text-lg font-bold text-slate-800 dark:text-white">${q.question || '(Pertanyaan kosong)'}</p>
            </div>
            <div class="space-y-3" id="sim-options">
                ${q.type === 'multiple_choice' ? q.options.map((o, idx) => `
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition">
                        <input type="radio" name="sim-answer" value="${idx}" ${simState.answers[simState.current] === idx ? 'checked' : ''}>
                        <span>${o.label}. ${o.text}</span>
                    </label>
                `).join('') : `
                    <textarea rows="3" id="sim-essay-input" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition" placeholder="Tulis jawaban essay...">${simState.answers[simState.current] || ''}</textarea>
                `}
            </div>
            <div class="mt-4 text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-2 rounded-xl">
                🧪 Mode Uji Coba - bukan pertandingan asli
            </div>
        `;
        body.innerHTML = html;

        const prevBtn = document.getElementById('simulator-prev');
        const nextBtn = document.getElementById('simulator-next');
        const finishBtn = document.getElementById('simulator-finish');
        if (prevBtn) prevBtn.disabled = simState.current === 0;
        if (simState.current === questions.length - 1) {
            if (nextBtn) { nextBtn.classList.add('hidden'); }
            if (finishBtn) { finishBtn.classList.remove('hidden'); }
        } else {
            if (nextBtn) { nextBtn.classList.remove('hidden'); }
            if (finishBtn) { finishBtn.classList.add('hidden'); }
        }

        if (!simState.started) {
            simState.started = true;
            const totalSeconds = 30 * questions.length;
            simState.timeLeft = Math.min(totalSeconds, 300);
            if (simState.timerInterval) clearInterval(simState.timerInterval);
            simState.timerInterval = setInterval(() => {
                simState.timeLeft--;
                const timerEl = document.getElementById('sim-timer');
                if (timerEl) {
                    const mins = Math.floor(simState.timeLeft / 60);
                    const secs = simState.timeLeft % 60;
                    timerEl.textContent = `${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
                }
                if (simState.timeLeft <= 0) {
                    clearInterval(simState.timerInterval);
                    alert('⏰ Waktu uji coba habis!');
                    finishSimulator();
                }
            }, 1000);
        }

        if (q.type === 'multiple_choice' && simState.answers[simState.current] !== undefined) {
            const radio = document.querySelector(`input[name="sim-answer"][value="${simState.answers[simState.current]}"]`);
            if (radio) radio.checked = true;
        }
        if (q.type === 'essay') {
            const ta = document.getElementById('sim-essay-input');
            if (ta && simState.answers[simState.current] !== undefined) ta.value = simState.answers[simState.current];
        }
    }

    function saveCurrentAnswer() {
        const q = questions[simState.current];
        if (q.type === 'multiple_choice') {
            const selected = document.querySelector('input[name="sim-answer"]:checked');
            simState.answers[simState.current] = selected ? parseInt(selected.value) : null;
        } else {
            const ta = document.getElementById('sim-essay-input');
            simState.answers[simState.current] = ta ? ta.value : '';
        }
    }

    function finishSimulator() {
        if (simState.finished) return;
        saveCurrentAnswer();
        simState.finished = true;
        if (simState.timerInterval) clearInterval(simState.timerInterval);

        let correct = 0;
        let wrong = 0;
        let empty = 0;
        let score = 0;
        questions.forEach((q, idx) => {
            const ans = simState.answers[idx];
            if (q.type === 'multiple_choice') {
                if (ans === null || ans === undefined) { empty++; }
                else if (q.options[ans] && q.options[ans].correct) { correct++; score += 10; }
                else { wrong++; }
            } else {
                if (ans && ans.trim().length > 0) { correct++; score += 5; }
                else { empty++; }
            }
        });
        simState.correct = correct;
        simState.wrong = wrong;
        simState.empty = empty;
        simState.score = score;

        const total = questions.length;
        const accuracy = total > 0 ? Math.round((correct / total) * 100) : 0;

        body.innerHTML = `
            <div class="text-center py-6">
                <div class="text-6xl mb-4">✅</div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Uji Coba Selesai!</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Game dapat dimainkan dengan normal.</p>
            </div>
            <div class="grid grid-cols-2 gap-4 max-w-md mx-auto">
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center">
                    <p class="text-xs text-slate-500">Soal</p>
                    <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">${total}</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center">
                    <p class="text-xs text-slate-500">Benar</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">${correct}</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center">
                    <p class="text-xs text-slate-500">Salah</p>
                    <p class="text-2xl font-black text-red-600 dark:text-red-400">${wrong}</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-center">
                    <p class="text-xs text-slate-500">Akurasi</p>
                    <p class="text-2xl font-black text-cyan-600 dark:text-cyan-400">${accuracy}%</p>
                </div>
            </div>
            <div class="mt-4 text-center text-sm text-slate-500 dark:text-slate-400">
                Poin: ${score} | Kosong: ${empty}
            </div>
        `;
        document.getElementById('simulator-footer').innerHTML = `
            <button id="sim-retry" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition">🔄 Uji Coba Lagi</button>
            <button id="sim-close-result" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">✅ Selesai & Simpan Hasil</button>
        `;
        document.getElementById('sim-retry').addEventListener('click', startSimulator);
        document.getElementById('sim-close-result').addEventListener('click', function() {
            testPassed = true;
            document.getElementById('test-passed-input').value = '1';
            document.getElementById('test-status').textContent = '✅ Uji coba berhasil!';
            document.getElementById('test-status').className = 'text-sm text-emerald-600 dark:text-emerald-400';
            closeSimulator();
            updateFinalStatus();
            document.getElementById('save-game-btn').disabled = false;
            showToast('✅ Game berhasil diuji coba! Konfigurasi siap dilanjutkan.');
        });
    }

    function closeSimulator() {
        modal.classList.add('hidden');
        if (simState.timerInterval) clearInterval(simState.timerInterval);
    }

    // ================================================================
    // SPONSOR & FINAL STATUS
    // ================================================================
    window.toggleSponsorForm = function(enabled) {
        const form = document.getElementById('sponsor-form');
        const input = document.getElementById('sponsor-enabled-input');
        if (enabled) {
            form.classList.remove('hidden');
            input.value = '1';
        } else {
            form.classList.add('hidden');
            input.value = '0';
        }
    };

    function updateFinalStatus() {
        const icon = document.getElementById('final-test-icon');
        const text = document.getElementById('final-test-text');
        const container = document.getElementById('final-test-status');
        if (testPassed) {
            icon.textContent = '✅';
            icon.className = 'text-emerald-500';
            text.textContent = 'Uji coba berhasil! Game siap disimpan.';
            container.className = 'p-4 rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20';
        } else {
            icon.textContent = '⚠️';
            icon.className = 'text-amber-500';
            text.textContent = 'Belum diuji coba. Lakukan uji coba terlebih dahulu.';
            container.className = 'p-4 rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20';
        }
        document.getElementById('save-game-btn').disabled = !testPassed;
    }

    // Toast
    function showToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 z-[99999] bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl font-bold text-sm max-w-md animate-fadeIn';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // ================================================================
    // SUBMIT HANDLER
    // ================================================================
    form.addEventListener('submit', function(e) {
        updateQuestionsFromDOM();
        updateJson();
        if (!testPassed) {
            e.preventDefault();
            alert('⚠️ Anda harus menguji coba game terlebih dahulu sebelum menyimpan.');
            goToStep(8);
            return false;
        }
        if (questions.length !== targetQuestions) {
            e.preventDefault();
            alert('Jumlah soal harus tepat ' + targetQuestions + ' (saat ini ' + questions.length + ').');
            goToStep(5);
            return false;
        }
        for (let i = 0; i < questions.length; i++) {
            const q = questions[i];
            if (!q.question.trim()) {
                e.preventDefault();
                alert('Soal #' + (i+1) + ' masih kosong pertanyaannya.');
                goToStep(5);
                return false;
            }
            if (q.type === 'multiple_choice') {
                const hasCorrect = q.options.some(o => o.correct);
                if (!hasCorrect) {
                    e.preventDefault();
                    alert('Soal #' + (i+1) + ' belum memiliki jawaban benar.');
                    goToStep(5);
                    return false;
                }
                const emptyOpts = q.options.some(o => !o.text.trim());
                if (emptyOpts) {
                    e.preventDefault();
                    alert('Soal #' + (i+1) + ' masih ada pilihan yang kosong.');
                    goToStep(5);
                    return false;
                }
                if (q.options.length < 2) {
                    e.preventDefault();
                    alert('Soal #' + (i+1) + ' minimal 2 pilihan.');
                    goToStep(5);
                    return false;
                }
            }
        }
        console.log('✅ Data game siap dikirim:', {
            name: document.getElementById('game-name').value,
            total_questions: targetQuestions,
            questions: questions
        });
    });

    // ================================================================
    // KEAMANAN
    // ================================================================
    document.addEventListener('contextmenu', function(e) { e.preventDefault(); return false; });
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey && (e.key === 's' || e.key === 'S' || e.key === 'u' || e.key === 'U')) ||
            (e.key === 'F2' || e.key === 'F12')) {
            e.preventDefault();
            alert('⛔ Fitur ini dinonaktifkan untuk keamanan.');
            return false;
        }
    });

    // ================================================================
    // INISIALISASI
    // ================================================================
    renderQuestions();
    goToStep(0);
    updateFinalStatus();

    document.getElementById('target-questions').addEventListener('change', function() {
        targetQuestions = parseInt(this.value) || 10;
        totalHidden.value = targetQuestions;
        updateProgress();
    });

    console.log('🚀 Quiz Wizard 10 Tahap siap!');
});
</script>

<style>
    .quiz-type-card { transition: all 0.3s ease; }
    .quiz-type-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(99, 102, 241, 0.15); }
    .step-content.hidden { display: none; }
    .step-item .step-number { transition: all 0.3s ease; }
    .step-item .step-connector { transition: background 0.3s ease; }
    .step-item.active .step-connector { background: #6366f1; }
    .question-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .sortable-ghost { opacity: 0.4; background: #e0e7ff; }
    .option-row { transition: background 0.2s; }
    .option-row:hover { background: rgba(99, 102, 241, 0.05); border-radius: 0.75rem; }
    #progress-bar { background-size: 200% 100%; animation: shimmer 2s infinite linear; }
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    input::placeholder, textarea::placeholder { color: #94a3b8; }
    .dark input::placeholder, .dark textarea::placeholder { color: #64748b; }
    .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #simulator-body { max-height: 70vh; }
</style>
@endsection
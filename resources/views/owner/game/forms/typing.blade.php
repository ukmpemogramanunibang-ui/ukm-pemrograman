@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-6">
    <div class="container mx-auto max-w-6xl">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <span>Game Center</span>
                    <span>/</span>
                    <span>Typing</span>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                    Buat Game Typing 10 Jari
                </h1>
                <p class="text-slate-500 mt-1">
                    Buat game typing yang bisa dimainkan sendiri maupun secara realtime.
                </p>
            </div>
            <a href="{{ route('owner.game.games.create-form') }}"
               class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                ← Kembali
            </a>
        </div>

        {{-- INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow border border-slate-200 dark:border-slate-800">
                <div class="text-sm text-slate-500">Mode</div>
                <div class="font-bold text-lg mt-1">Solo + Multiplayer</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow border border-slate-200 dark:border-slate-800">
                <div class="text-sm text-slate-500">Pemain</div>
                <div class="font-bold text-lg mt-1">Hingga 100 pemain</div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 shadow border border-slate-200 dark:border-slate-800">
                <div class="text-sm text-slate-500">Realtime</div>
                <div class="font-bold text-lg mt-1">WebSocket</div>
            </div>
        </div>

        {{-- FORM --}}
        <form action="{{ route('owner.game.games.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">

            @csrf

            <input type="hidden" name="type" value="native">
            <input type="hidden" name="game_type" value="typing">

            <div class="p-6 space-y-8">

                {{-- INFORMASI DASAR --}}
                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Informasi Game</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Nama Game <span class="text-red-500">*</span></label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Typing Master 10 Jari"
                                   class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-3 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                                   required>
                            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Deskripsi</label>
                            <input type="text"
                                   name="description"
                                   value="{{ old('description') }}"
                                   placeholder="Latihan typing 10 jari realtime"
                                   class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-3 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>
                </section>

                {{-- PENGATURAN GAME --}}
                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Pengaturan Game</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- DURASI CUSTOM --}}
                        <div>
                            <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Durasi (detik) <span class="text-red-500">*</span></label>
                            <input type="number"
                                   name="time_limit"
                                   value="{{ old('time_limit', 60) }}"
                                   min="10"
                                   max="600"
                                   class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-3 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                                   required>
                            @error('time_limit')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Maksimal Pemain</label>
                            <select name="max_players"
                                    class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-3 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="1" {{ old('max_players') == 1 ? 'selected' : '' }}>1 Pemain (Solo)</option>
                                <option value="2" {{ old('max_players') == 2 ? 'selected' : '' }}>2 Pemain</option>
                                <option value="4" {{ old('max_players') == 4 ? 'selected' : '' }}>4 Pemain</option>
                                <option value="10" {{ old('max_players') == 10 ? 'selected' : '' }}>10 Pemain</option>
                                <option value="25" {{ old('max_players') == 25 ? 'selected' : '' }}>25 Pemain</option>
                                <option value="50" {{ old('max_players') == 50 ? 'selected' : '' }}>50 Pemain</option>
                                <option value="100" {{ old('max_players') == 100 ? 'selected' : '' }}>100 Pemain</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Kesulitan</label>
                            <select name="difficulty"
                                    class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-3 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Mudah</option>
                                <option value="normal" {{ old('difficulty') == 'normal' || old('difficulty') == '' ? 'selected' : '' }}>Normal</option>
                                <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Sulit</option>
                                <option value="expert" {{ old('difficulty') == 'expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- MODE --}}
                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Mode Permainan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="cursor-pointer flex items-center gap-2 text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="allow_solo" value="1" class="w-4 h-4 text-indigo-600" {{ old('allow_solo', '1') ? 'checked' : '' }}>
                            Solo
                        </label>
                        <label class="cursor-pointer flex items-center gap-2 text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="allow_race" value="1" class="w-4 h-4 text-indigo-600" {{ old('allow_race', '1') ? 'checked' : '' }}>
                            Multiplayer Race
                        </label>
                        <label class="cursor-pointer flex items-center gap-2 text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="allow_private_room" value="1" class="w-4 h-4 text-indigo-600" {{ old('allow_private_room', '1') ? 'checked' : '' }}>
                            Private Room
                        </label>
                    </div>
                </section>

                {{-- MATERI TYPING --}}
                <section>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Materi Typing</h2>
                        {{-- Generate Teks --}}
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <div>
                                <label class="text-slate-600 dark:text-slate-400 mr-1">Bahasa:</label>
                                <select id="generateLang" class="border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-1.5 dark:bg-slate-800 text-slate-900 dark:text-white">
                                    <option value="id">Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-slate-600 dark:text-slate-400 mr-1">Paragraf:</label>
                                <select id="generateParagraphs" class="border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-1.5 dark:bg-slate-800 text-slate-900 dark:text-white">
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <button type="button" id="generateTextBtn"
                                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold transition shadow-md shadow-emerald-500/20">
                                ⚡ Generate Teks
                            </button>
                        </div>
                    </div>

                    <label class="block font-semibold mb-2 text-slate-700 dark:text-slate-300">Kata-kata / Kalimat <span class="text-red-500">*</span></label>
                    <textarea name="words"
                              id="wordsTextarea"
                              rows="6"
                              placeholder="programming, laravel, php, javascript, database, websocket, framework"
                              class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-4 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition"
                              required>{{ old('words') }}</textarea>
                    <p class="text-sm text-slate-500 mt-2">Pisahkan kata dengan koma. Minimal 1 kata. Gunakan tombol Generate untuk membuat teks otomatis.</p>
                    @error('words')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </section>

                {{-- COVER --}}
                <section>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Cover Game</h2>
                    <input type="file"
                           name="cover_images[]"
                           multiple
                           accept=".jpg,.png,.jpeg,.webp,.gif"
                           class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-3 dark:bg-slate-800 text-slate-700 dark:text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition">
                    <p class="text-sm text-slate-500 mt-2">Maksimal 4 gambar. Format: JPG, PNG, JPEG, WEBP, GIF. Maks 2MB per file.</p>
                    @error('cover_images.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </section>

                {{-- PREVIEW --}}
                <section>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-slate-800 dark:text-white mb-4">Preview Konfigurasi</h3>
                        <div id="gamePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl shadow text-center">
                                <span class="block text-slate-500">Mode</span>
                                <span class="font-semibold text-slate-800 dark:text-white" id="preview-mode">Solo + Race</span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl shadow text-center">
                                <span class="block text-slate-500">Waktu</span>
                                <span class="font-semibold text-slate-800 dark:text-white" id="preview-time">60 detik</span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl shadow text-center">
                                <span class="block text-slate-500">Pemain</span>
                                <span class="font-semibold text-slate-800 dark:text-white" id="preview-players">1</span>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl shadow text-center">
                                <span class="block text-slate-500">Kesulitan</span>
                                <span class="font-semibold text-slate-800 dark:text-white" id="preview-difficulty">Normal</span>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-slate-200 dark:border-slate-700 p-6 flex flex-col md:flex-row justify-between gap-3">
                <button type="button"
                        id="btnTesting"
                        class="px-6 py-3 rounded-xl border border-amber-500 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition font-semibold flex items-center gap-2">
                    🧪 Testing Game
                </button>
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                    🎮 Simpan Game
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ============================================================ --}}
{{-- TESTING MODAL --}}
{{-- ============================================================ --}}
<div id="testingModal"
     class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm items-center justify-center p-4"
     style="display: none;">

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden border border-slate-200 dark:border-slate-700">

        {{-- Header Modal --}}
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">🧪 Testing Game</h2>
                <p class="text-sm text-slate-500">Pastikan game dapat dimainkan sebelum disimpan.</p>
            </div>
            <button type="button" id="closeTesting" class="text-3xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition">×</button>
        </div>

        {{-- Body Modal --}}
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">

            {{-- STATISTIK --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl text-center border border-slate-200 dark:border-slate-700">
                    <div class="text-xs text-slate-500">WPM Kasar</div>
                    <div id="testGrossWpm" class="text-xl font-bold text-cyan-600 dark:text-cyan-400">0</div>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl text-center border border-slate-200 dark:border-slate-700">
                    <div class="text-xs text-slate-500">WPM Bersih</div>
                    <div id="testNetWpm" class="text-xl font-bold text-emerald-600 dark:text-emerald-400">0</div>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl text-center border border-slate-200 dark:border-slate-700">
                    <div class="text-xs text-slate-500">Akurasi</div>
                    <div id="testAccuracy" class="text-xl font-bold text-amber-600 dark:text-amber-400">100%</div>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl text-center border border-slate-200 dark:border-slate-700">
                    <div class="text-xs text-slate-500">Backspace</div>
                    <div id="testBackspace" class="text-xl font-bold text-orange-600 dark:text-orange-400">0</div>
                </div>
                <div class="bg-slate-100 dark:bg-slate-800 p-4 rounded-xl text-center border border-slate-200 dark:border-slate-700">
                    <div class="text-xs text-slate-500">Durasi</div>
                    <div id="testDuration" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">0s</div>
                </div>
            </div>

            {{-- TEKS --}}
            <div id="typingText"
                 class="bg-slate-100 dark:bg-slate-800 rounded-xl p-5 font-mono text-base md:text-lg leading-relaxed min-h-[120px] mb-4 border border-slate-200 dark:border-slate-700 whitespace-pre-wrap break-words">
                Masukkan kata terlebih dahulu...
            </div>

            {{-- PROGRESS BAR --}}
            <div class="h-1 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden mb-4">
                <div id="testProgressFill" class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>

            {{-- INPUT --}}
            <textarea id="testingInput"
                      disabled
                      rows="4"
                      placeholder="Tekan tombol mulai kemudian ketik di sini..."
                      class="w-full border border-slate-300 dark:border-slate-700 rounded-xl p-4 dark:bg-slate-800 text-slate-900 dark:text-white font-mono resize-none focus:ring-2 focus:ring-indigo-500 transition"></textarea>

            {{-- KONTROL --}}
            <div class="flex flex-wrap justify-between items-center mt-4 gap-3">
                <div id="testingStatus" class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                    <span id="testStatusDot" class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>Belum dimulai</span>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="resetTesting" class="px-5 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 transition font-semibold">
                        ⟳ Reset
                    </button>
                    <button type="button" id="startTesting" class="px-5 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition font-semibold shadow-md shadow-indigo-500/30">
                        ▶ Mulai Testing
                    </button>
                </div>
            </div>

            {{-- VALIDASI --}}
            <div id="validationResult" class="mt-4 hidden p-4 rounded-xl border text-sm"></div>

        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT --}}
{{-- ============================================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ==============================================================
    // PREVIEW KONFIGURASI
    // ==============================================================
    const timeInput = document.querySelector('[name="time_limit"]');
    const playerSelect = document.querySelector('[name="max_players"]');
    const difficultySelect = document.querySelector('[name="difficulty"]');
    const allowSolo = document.querySelector('[name="allow_solo"]');
    const allowRace = document.querySelector('[name="allow_race"]');

    function updatePreview() {
        document.getElementById('preview-time').textContent = (timeInput.value || 60) + ' detik';
        document.getElementById('preview-players').textContent = playerSelect.value;
        document.getElementById('preview-difficulty').textContent = difficultySelect.options[difficultySelect.selectedIndex]?.text || 'Normal';

        let mode = '';
        if (allowSolo.checked) mode += 'Solo';
        if (allowRace.checked) mode += (mode ? ' + ' : '') + 'Race';
        if (!mode) mode = 'None';
        document.getElementById('preview-mode').textContent = mode;
    }

    [timeInput, playerSelect, difficultySelect, allowSolo, allowRace].forEach(el => {
        el.addEventListener('change', updatePreview);
        el.addEventListener('input', updatePreview);
    });
    updatePreview();

    // ==============================================================
    // GENERATE TEKS OTOMATIS
    // ==============================================================
    const wordsTextarea = document.getElementById('wordsTextarea');
    const generateBtn = document.getElementById('generateTextBtn');
    const langSelect = document.getElementById('generateLang');
    const paragraphSelect = document.getElementById('generateParagraphs');

    // Daftar kata per bahasa
    const wordLists = {
        id: [
            'pemrograman', 'laravel', 'php', 'javascript', 'basis data', 'framework', 'website',
            'aplikasi', 'server', 'klien', 'antarmuka', 'pengguna', 'pengkodean', 'debugging',
            'kompilasi', 'deployment', 'testing', 'production', 'development', 'analisis',
            'desain', 'arsitektur', 'komponen', 'modul', 'library', 'dependensi', 'autentikasi',
            'otorisasi', 'session', 'cookie', 'api', 'rest', 'graphql', 'websocket', 'redis',
            'queue', 'event', 'listener', 'middleware', 'controller', 'model', 'view',
            'migration', 'seeder', 'factory', 'route', 'blade', 'vue', 'react', 'angular',
            'node', 'python', 'ruby', 'java', 'csharp', 'golang', 'rust', 'swift', 'kotlin',
            'android', 'ios', 'flutter', 'react native', 'docker', 'kubernetes', 'aws',
            'azure', 'gcp', 'linux', 'nginx', 'apache', 'mysql', 'postgresql', 'mongodb',
            'elasticsearch', 'memcached', 'varnish', 'cdn', 'ssl', 'dns', 'load balancer',
            'microservice', 'monolithic', 'serverless', 'ci/cd', 'git', 'github', 'gitlab',
            'bitbucket', 'agile', 'scrum', 'kanban', 'devops', 'sre', 'monitoring', 'logging',
            'tracing', 'metrics', 'alerting', 'dashboard'
        ],
        en: [
            'programming', 'laravel', 'php', 'javascript', 'database', 'framework', 'website',
            'application', 'server', 'client', 'interface', 'user', 'coding', 'debugging',
            'compilation', 'deployment', 'testing', 'production', 'development', 'analysis',
            'design', 'architecture', 'component', 'module', 'library', 'dependency',
            'authentication', 'authorization', 'session', 'cookie', 'api', 'rest', 'graphql',
            'websocket', 'redis', 'queue', 'event', 'listener', 'middleware', 'controller',
            'model', 'view', 'migration', 'seeder', 'factory', 'route', 'blade', 'vue',
            'react', 'angular', 'node', 'python', 'ruby', 'java', 'csharp', 'golang', 'rust',
            'swift', 'kotlin', 'android', 'ios', 'flutter', 'react native', 'docker',
            'kubernetes', 'aws', 'azure', 'gcp', 'linux', 'nginx', 'apache', 'mysql',
            'postgresql', 'mongodb', 'elasticsearch', 'memcached', 'varnish', 'cdn', 'ssl',
            'dns', 'load balancer', 'microservice', 'monolithic', 'serverless', 'ci/cd',
            'git', 'github', 'gitlab', 'bitbucket', 'agile', 'scrum', 'kanban', 'devops',
            'sre', 'monitoring', 'logging', 'tracing', 'metrics', 'alerting', 'dashboard'
        ]
    };

    function generateTypingText(lang, paragraphs) {
        const words = wordLists[lang] || wordLists.en;
        const shuffled = [...words];
        // Acak kata
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }

        const totalWords = Math.min(shuffled.length, 20 + Math.floor(Math.random() * 30));
        const selected = shuffled.slice(0, totalWords);

        // Buat paragraf
        const wordsPerParagraph = Math.ceil(selected.length / paragraphs);
        let result = '';
        for (let p = 0; p < paragraphs; p++) {
            const start = p * wordsPerParagraph;
            const end = Math.min(start + wordsPerParagraph, selected.length);
            const slice = selected.slice(start, end);
            if (slice.length === 0) break;
            // Kapitalisasi huruf pertama
            let sentence = slice.join(' ');
            if (sentence.length > 0) {
                sentence = sentence.charAt(0).toUpperCase() + sentence.slice(1);
            }
            result += sentence + '. ';
        }
        return result.trim();
    }

    generateBtn.addEventListener('click', function() {
        const lang = langSelect.value;
        const paragraphs = parseInt(paragraphSelect.value) || 2;
        const text = generateTypingText(lang, paragraphs);
        wordsTextarea.value = text;
        // Trigger change agar preview update jika perlu
        wordsTextarea.dispatchEvent(new Event('input'));
    });

    // ==============================================================
    // TESTING MODAL
    // ==============================================================
    const modal = document.getElementById('testingModal');
    const btnTesting = document.getElementById('btnTesting');
    const closeTesting = document.getElementById('closeTesting');
    const startTesting = document.getElementById('startTesting');
    const resetTesting = document.getElementById('resetTesting');
    const input = document.getElementById('testingInput');
    const typingText = document.getElementById('typingText');
    const progressFill = document.getElementById('testProgressFill');

    const grossWpm = document.getElementById('testGrossWpm');
    const netWpm = document.getElementById('testNetWpm');
    const accuracy = document.getElementById('testAccuracy');
    const backspace = document.getElementById('testBackspace');
    const duration = document.getElementById('testDuration');
    const statusDot = document.getElementById('testStatusDot');
    const statusText = document.querySelector('#testingStatus span:last-child');
    const validationResult = document.getElementById('validationResult');

    let targetText = '';
    let startTime = null;
    let timer = null;
    let backspaceCount = 0;
    let isTesting = false;
    let isFinished = false;
    let timeElapsed = 0;

    // Ambil kata dari textarea form
    function getWordsFromForm() {
        const wordsInput = document.querySelector('[name="words"]');
        if (!wordsInput) return [];
        return wordsInput.value.split(',').map(x => x.trim()).filter(Boolean);
    }

    function generateText() {
        const words = getWordsFromForm();
        if (!words.length) {
            targetText = '';
            typingText.textContent = '⚠️ Masukkan kata terlebih dahulu di form!';
            return false;
        }
        const generated = [];
        for (let i = 0; i < 50; i++) {
            const random = words[Math.floor(Math.random() * words.length)];
            generated.push(random);
        }
        targetText = generated.join(' ');
        typingText.textContent = targetText;
        return true;
    }

    function renderTypingHighlight(typed) {
        if (!targetText) return;
        let html = '';
        for (let i = 0; i < targetText.length; i++) {
            const char = targetText[i];
            if (i < typed.length) {
                const isCorrect = typed[i] === char;
                html += isCorrect
                    ? `<span class="text-emerald-500 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-0.5 rounded">${char}</span>`
                    : `<span class="text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-950/30 px-0.5 rounded line-through">${char}</span>`;
            } else {
                html += `<span class="text-slate-400 dark:text-slate-500">${char}</span>`;
            }
        }
        typingText.innerHTML = html;
    }

    function updateStats() {
        if (!startTime) return;
        const elapsed = (Date.now() - startTime) / 1000;
        timeElapsed = elapsed;
        duration.textContent = Math.floor(elapsed) + 's';

        const typed = input.value;
        const len = typed.length;
        const total = targetText.length;

        // Progress
        const pct = total > 0 ? Math.min(100, Math.round((len / total) * 100)) : 0;
        progressFill.style.width = pct + '%';

        // Correct / Wrong
        let correct = 0;
        for (let i = 0; i < Math.min(len, total); i++) {
            if (typed[i] === targetText[i]) correct++;
        }
        const wrong = len - correct;
        const minutes = Math.max(0.01, elapsed / 60);

        const gross = Math.round((len / 5) / minutes);
        const net = Math.max(0, Math.round(((correct - wrong) / 5) / minutes));
        const acc = len > 0 ? Math.round((correct / len) * 100) : 100;

        grossWpm.textContent = gross;
        netWpm.textContent = net;
        accuracy.textContent = acc + '%';

        renderTypingHighlight(typed);

        // Cek selesai
        if (len >= total && total > 0) {
            const allCorrect = (() => {
                for (let i = 0; i < total; i++) {
                    if (typed[i] !== targetText[i]) return false;
                }
                return true;
            })();
            if (allCorrect) {
                finishTest('✅ Selesai! Semua teks berhasil diketik.');
            }
        }
    }

    function finishTest(message) {
        if (isFinished) return;
        isFinished = true;
        isTesting = false;
        clearInterval(timer);
        timer = null;
        input.disabled = true;
        statusDot.className = 'inline-block w-2 h-2 rounded-full bg-emerald-500';
        statusText.textContent = message;

        // Validasi
        const typed = input.value;
        const correct = (() => {
            let c = 0;
            for (let i = 0; i < Math.min(typed.length, targetText.length); i++) {
                if (typed[i] === targetText[i]) c++;
            }
            return c;
        })();
        const wrong = typed.length - correct;
        const minutes = Math.max(0.01, timeElapsed / 60);
        const gross = Math.round((typed.length / 5) / minutes);
        const net = Math.max(0, Math.round(((correct - wrong) / 5) / minutes));
        const acc = typed.length > 0 ? Math.round((correct / typed.length) * 100) : 100;

        let valid = true;
        let errors = [];
        if (!targetText || targetText.length === 0) { valid = false; errors.push('Teks kosong'); }
        if (typed.length === 0) { valid = false; errors.push('Tidak ada karakter yang diketik'); }
        if (correct === 0 && typed.length > 0) { valid = false; errors.push('Tidak ada karakter yang benar'); }

        let statusHtml = '';
        if (valid) {
            statusHtml = `
                <div class="flex items-start gap-3">
                    <span class="text-2xl">✅</span>
                    <div>
                        <p class="font-bold text-emerald-600 dark:text-emerald-400">GAME VALID</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Game dapat dimainkan dengan baik.</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2 text-xs">
                            <span>WPM: ${net}</span>
                            <span>Akurasi: ${acc}%</span>
                            <span>Benar: ${correct}</span>
                            <span>Salah: ${wrong}</span>
                        </div>
                    </div>
                </div>
            `;
            validationResult.className = 'mt-4 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/20 text-sm';
        } else {
            statusHtml = `
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🔴</span>
                    <div>
                        <p class="font-bold text-red-600 dark:text-red-400">GAME TIDAK VALID</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Masalah ditemukan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 mt-1">
                            ${errors.map(e => `<li>${e}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
            validationResult.className = 'mt-4 p-4 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/20 text-sm';
        }
        validationResult.innerHTML = statusHtml;
        validationResult.classList.remove('hidden');

        startTesting.textContent = '🔄 Test Ulang';
    }

    function resetTest() {
        clearInterval(timer);
        timer = null;
        isTesting = false;
        isFinished = false;
        startTime = null;
        backspaceCount = 0;
        timeElapsed = 0;
        input.value = '';
        input.disabled = true;
        input.dataset.prevLen = '0';
        backspace.textContent = '0';
        grossWpm.textContent = '0';
        netWpm.textContent = '0';
        accuracy.textContent = '100%';
        duration.textContent = '0s';
        progressFill.style.width = '0%';
        statusDot.className = 'inline-block w-2 h-2 rounded-full bg-slate-400';
        statusText.textContent = 'Belum dimulai';
        validationResult.classList.add('hidden');
        startTesting.textContent = '▶ Mulai Testing';

        if (targetText) {
            renderTypingHighlight('');
        } else {
            typingText.innerHTML = 'Masukkan kata terlebih dahulu...';
        }
    }

    // Event untuk modal
    btnTesting.addEventListener('click', function() {
        const hasWords = getWordsFromForm().length > 0;
        if (!hasWords) {
            alert('⚠️ Isi minimal 1 kata di form terlebih dahulu!');
            return;
        }
        const ok = generateText();
        if (!ok) {
            alert('⚠️ Gagal generate teks. Pastikan kata diisi dengan benar.');
            return;
        }
        resetTest();
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        setTimeout(() => input.focus(), 100);
    });

    closeTesting.addEventListener('click', function() {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        clearInterval(timer);
        timer = null;
        resetTest();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
            clearInterval(timer);
            timer = null;
            resetTest();
        }
    });

    startTesting.addEventListener('click', function() {
        if (this.textContent.includes('Test Ulang')) {
            resetTest();
            setTimeout(() => startTesting.click(), 300);
            return;
        }
        if (isTesting) return;
        if (!targetText || targetText.length === 0) {
            generateText();
            if (!targetText) return;
        }
        isTesting = true;
        isFinished = false;
        startTime = Date.now();
        backspaceCount = 0;
        input.value = '';
        input.disabled = false;
        input.focus();
        input.dataset.prevLen = '0';
        statusDot.className = 'inline-block w-2 h-2 rounded-full bg-emerald-500';
        statusText.textContent = 'Sedang testing...';
        startTesting.textContent = '⏹ Selesai';
        validationResult.classList.add('hidden');

        clearInterval(timer);
        timer = setInterval(updateStats, 200);
        updateStats();
    });

    resetTesting.addEventListener('click', function() {
        resetTest();
        if (targetText) renderTypingHighlight('');
    });

    input.addEventListener('input', function() {
        if (!isTesting || isFinished) return;
        const prevLen = parseInt(this.dataset.prevLen || '0');
        const len = this.value.length;
        if (len < prevLen) {
            backspaceCount++;
            backspace.textContent = backspaceCount;
        }
        this.dataset.prevLen = len;
        updateStats();
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey && isTesting && !isFinished) {
            e.preventDefault();
            startTesting.click();
        }
    });

    // Simpan referensi ke generateText untuk digunakan ulang
    window._typingTestGenerate = generateText;

    console.log('🧪 Testing Game Loaded');
});
</script>
@endpush

@endsection
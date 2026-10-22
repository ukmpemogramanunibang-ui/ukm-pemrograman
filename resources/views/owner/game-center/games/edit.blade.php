@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <span>{{ $game->icon ?? '🎮' }}</span>
                    Edit Game: {{ $game->name }}
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Ubah konfigurasi game yang terintegrasi dengan Game Center UNICODE.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('owner.game.games.index') }}"
                   class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('owner.game.games.show', $game) }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fas fa-eye"></i> Detail
                </a>
                <a href="{{ route('owner.game.games.diagnostic', $game) }}"
                   class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fas fa-flask"></i> Diagnostic
                </a>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl">
                <p class="text-emerald-700 dark:text-emerald-300 font-bold">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <p class="text-red-700 dark:text-red-300 font-bold"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</p>
                @if(session('error_details'))
                    <pre class="mt-2 text-xs text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 p-3 rounded-lg overflow-auto max-h-40">{{ session('error_details') }}</pre>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <h4 class="text-red-700 dark:text-red-300 font-bold mb-2">⚠️ Terjadi kesalahan validasi:</h4>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- FORM UTAMA --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
            <form action="{{ route('owner.game.games.update', $game) }}" method="POST" class="space-y-6" id="game-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="source_type" value="{{ $game->source_type }}">
                <input type="hidden" name="game_type" id="game_type_hidden" value="{{ $game->game_type ?? 'typing' }}">

                {{-- ============================================================ --}}
                {{-- TAB NAVIGASI --}}
                {{-- ============================================================ --}}
                <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-700 pb-4">
                    <button type="button" class="tab-btn px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-sm" data-tab="tab-identitas">
                        <i class="fas fa-id-card mr-1"></i> Identitas
                    </button>
                    @if($game->source_type === 'native')
                        <button type="button" class="tab-btn px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition" data-tab="tab-konfigurasi">
                            <i class="fas fa-cog mr-1"></i> Konfigurasi
                        </button>
                        <button type="button" class="tab-btn px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition" data-tab="tab-proctoring">
                            <i class="fas fa-shield-alt mr-1"></i> Proctoring
                        </button>
                        @if(in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding']))
                        <button type="button" class="tab-btn px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition" data-tab="tab-soal">
                            <i class="fas fa-list mr-1"></i> Soal
                        </button>
                        @endif
                        <button type="button" class="tab-btn px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition" data-tab="tab-gambar">
                            <i class="fas fa-images mr-1"></i> Gambar
                        </button>
                    @endif
                </div>

                {{-- ============================================================ --}}
                {{-- TAB 1: IDENTITAS GAME --}}
                {{-- ============================================================ --}}
                <div id="tab-identitas" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Game <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $game->name) }}" required
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Slug <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" value="{{ old('slug', $game->slug) }}" required
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Contoh: quiz-unicorn, typing-arena</p>
                            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Icon (Emoji)</label>
                            <input type="text" name="icon" value="{{ old('icon', $game->icon ?? '🎮') }}" maxlength="10"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            @error('icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Banner (URL)</label>
                            <input type="text" name="banner" value="{{ old('banner', $game->banner) }}"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            @error('banner') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Developer</label>
                            <input type="text" name="developer" value="{{ old('developer', $game->developer ?? 'UNICODE') }}"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            @error('developer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Version</label>
                            <input type="text" name="version" value="{{ old('version', $game->version ?? '1.0.0') }}"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            @error('version') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="draft" {{ old('status', $game->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="testing" {{ old('status', $game->status) == 'testing' ? 'selected' : '' }}>Testing</option>
                                <option value="published" {{ old('status', $game->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="maintenance" {{ old('status', $game->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="archived" {{ old('status', $game->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                            <textarea name="description" rows="4"
                                      class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">{{ old('description', $game->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- TAB 2: KONFIGURASI GAME (KHUSUS NATIVE) --}}
                {{-- ============================================================ --}}
                @if($game->source_type === 'native')
                <div id="tab-konfigurasi" class="tab-content hidden">
                    <div class="space-y-6">

                        {{-- Informasi tipe game --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">
                                    @if($game->game_type === 'typing') ⌨️
                                    @elseif($game->game_type === 'quiz') ❓
                                    @elseif($game->game_type === 'coding') 💻
                                    @elseif($game->game_type === 'logic') 🧠
                                    @elseif($game->game_type === 'tebak') 🎯
                                    @elseif($game->game_type === 'reaction') ⚡
                                    @elseif($game->game_type === 'custom') ✨
                                    @endif
                                </span>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-white">Tipe Game: {{ ucfirst($game->game_type) }}</p>
                                    <p class="text-xs text-slate-500">Jenis game tidak dapat diubah setelah dibuat.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Difficulty --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tingkat Kesulitan</label>
                            <select name="difficulty" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="beginner" {{ old('difficulty', $game->difficulty) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty', $game->difficulty) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty', $game->difficulty) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="expert" {{ old('difficulty', $game->difficulty) == 'expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                            @error('difficulty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Max Attempts --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Maksimal Percobaan</label>
                            <input type="number" name="max_attempts" value="{{ old('max_attempts', $game->max_attempts ?? 1) }}" min="0" max="10"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">0 = tidak terbatas</p>
                            @error('max_attempts') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ============================================================ --}}
                        {{-- KONFIGURASI SPESIFIK PER TIPE GAME --}}
                        {{-- ============================================================ --}}

                        @if($game->game_type === 'typing')
                        {{-- TYPING ENGINE --}}
                        <div class="border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl p-6 bg-emerald-50/30 dark:bg-emerald-900/10">
                            <h4 class="text-xl font-bold text-emerald-700 dark:text-emerald-300 mb-4 flex items-center gap-2">
                                <span>⌨️</span> Typing Engine
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mode <span class="text-red-500">*</span></label>
                                    <select name="typing_settings[mode]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="practice" {{ old('typing_settings.mode', $game->typing_settings['mode'] ?? 'practice') == 'practice' ? 'selected' : '' }}>Practice</option>
                                        <option value="time_trial" {{ old('typing_settings.mode', $game->typing_settings['mode'] ?? '') == 'time_trial' ? 'selected' : '' }}>Time Trial</option>
                                        <option value="ranked" {{ old('typing_settings.mode', $game->typing_settings['mode'] ?? '') == 'ranked' ? 'selected' : '' }}>Ranked</option>
                                        <option value="challenge" {{ old('typing_settings.mode', $game->typing_settings['mode'] ?? '') == 'challenge' ? 'selected' : '' }}>Challenge</option>
                                        <option value="multiplayer" {{ old('typing_settings.mode', $game->typing_settings['mode'] ?? '') == 'multiplayer' ? 'selected' : '' }}>Multiplayer</option>
                                    </select>
                                    @error('typing_settings.mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Durasi (detik)</label>
                                    <input type="number" name="typing_settings[duration]" value="{{ old('typing_settings.duration', $game->typing_settings['duration'] ?? 60) }}" min="10" max="3600"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('typing_settings.duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Bahasa</label>
                                    <select name="typing_settings[language]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="id" {{ old('typing_settings.language', $game->typing_settings['language'] ?? 'id') == 'id' ? 'selected' : '' }}>Indonesia</option>
                                        <option value="en" {{ old('typing_settings.language', $game->typing_settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="mixed" {{ old('typing_settings.language', $game->typing_settings['language'] ?? '') == 'mixed' ? 'selected' : '' }}>Campuran</option>
                                    </select>
                                    @error('typing_settings.language') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Sumber Teks <span class="text-red-500">*</span></label>
                                    <select name="typing_settings[text_source]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="system" {{ old('typing_settings.text_source', $game->typing_settings['text_source'] ?? 'system') == 'system' ? 'selected' : '' }}>📚 Koleksi Sistem</option>
                                        <option value="custom" {{ old('typing_settings.text_source', $game->typing_settings['text_source'] ?? '') == 'custom' ? 'selected' : '' }}>✏️ Teks Saya</option>
                                        <option value="random" {{ old('typing_settings.text_source', $game->typing_settings['text_source'] ?? '') == 'random' ? 'selected' : '' }}>🎲 Generate Acak</option>
                                        <option value="theme" {{ old('typing_settings.text_source', $game->typing_settings['text_source'] ?? '') == 'theme' ? 'selected' : '' }}>📖 Berdasarkan Tema</option>
                                    </select>
                                    @error('typing_settings.text_source') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Teks Kustom (jika sumber = custom)</label>
                                    <textarea name="typing_settings[custom_text]" rows="4"
                                              class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-emerald-500 transition">{{ old('typing_settings.custom_text', $game->typing_settings['custom_text'] ?? '') }}</textarea>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Minimal 10 karakter, maksimal 5000 karakter.</p>
                                    @error('typing_settings.custom_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Panjang Teks</label>
                                    <select name="typing_settings[text_length]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="short" {{ old('typing_settings.text_length', $game->typing_settings['text_length'] ?? '') == 'short' ? 'selected' : '' }}>Pendek</option>
                                        <option value="medium" {{ old('typing_settings.text_length', $game->typing_settings['text_length'] ?? 'medium') == 'medium' ? 'selected' : '' }}>Sedang</option>
                                        <option value="long" {{ old('typing_settings.text_length', $game->typing_settings['text_length'] ?? '') == 'long' ? 'selected' : '' }}>Panjang</option>
                                        <option value="custom" {{ old('typing_settings.text_length', $game->typing_settings['text_length'] ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Jumlah Paragraf</label>
                                    <input type="number" name="typing_settings[paragraphs]" value="{{ old('typing_settings.paragraphs', $game->typing_settings['paragraphs'] ?? 1) }}" min="1" max="10"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('typing_settings.paragraphs') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tema (jika sumber = theme)</label>
                                    <select name="typing_settings[theme]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="programming" {{ old('typing_settings.theme', $game->typing_settings['theme'] ?? '') == 'programming' ? 'selected' : '' }}>💻 Programming</option>
                                        <option value="technology" {{ old('typing_settings.theme', $game->typing_settings['theme'] ?? '') == 'technology' ? 'selected' : '' }}>📱 Teknologi</option>
                                        <option value="education" {{ old('typing_settings.theme', $game->typing_settings['theme'] ?? '') == 'education' ? 'selected' : '' }}>📚 Pendidikan</option>
                                        <option value="motivation" {{ old('typing_settings.theme', $game->typing_settings['theme'] ?? '') == 'motivation' ? 'selected' : '' }}>💪 Motivasi</option>
                                        <option value="organization" {{ old('typing_settings.theme', $game->typing_settings['theme'] ?? '') == 'organization' ? 'selected' : '' }}>🏛️ Organisasi</option>
                                        <option value="general" {{ old('typing_settings.theme', $game->typing_settings['theme'] ?? '') == 'general' ? 'selected' : '' }}>📖 Umum</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @elseif($game->game_type === 'quiz')
                        {{-- QUIZ SETTINGS --}}
                        <div class="border-2 border-indigo-200 dark:border-indigo-800 rounded-2xl p-6 bg-indigo-50/30 dark:bg-indigo-900/10">
                            <h4 class="text-xl font-bold text-indigo-700 dark:text-indigo-300 mb-4 flex items-center gap-2">
                                <span>❓</span> Pengaturan Quiz
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Jumlah Soal <span class="text-red-500">*</span></label>
                                    <input type="number" name="quiz_settings[total_questions]" value="{{ old('quiz_settings.total_questions', $game->quiz_settings['total_questions'] ?? 10) }}" min="1" max="200"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('quiz_settings.total_questions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Total Poin <span class="text-red-500">*</span></label>
                                    <input type="number" name="quiz_settings[total_points]" value="{{ old('quiz_settings.total_points', $game->quiz_settings['total_points'] ?? 100) }}" min="1"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('quiz_settings.total_points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mode Penilaian <span class="text-red-500">*</span></label>
                                    <select name="quiz_settings[scoring_mode]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="accuracy" {{ old('quiz_settings.scoring_mode', $game->quiz_settings['scoring_mode'] ?? 'accuracy') == 'accuracy' ? 'selected' : '' }}>Akurasi</option>
                                        <option value="speed" {{ old('quiz_settings.scoring_mode', $game->quiz_settings['scoring_mode'] ?? '') == 'speed' ? 'selected' : '' }}>Kecepatan</option>
                                        <option value="streak" {{ old('quiz_settings.scoring_mode', $game->quiz_settings['scoring_mode'] ?? '') == 'streak' ? 'selected' : '' }}>Streak</option>
                                        <option value="combination" {{ old('quiz_settings.scoring_mode', $game->quiz_settings['scoring_mode'] ?? '') == 'combination' ? 'selected' : '' }}>Kombinasi</option>
                                    </select>
                                    @error('quiz_settings.scoring_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Waktu per Soal (detik)</label>
                                    <input type="number" name="quiz_settings[time_per_question]" value="{{ old('quiz_settings.time_per_question', $game->quiz_settings['time_per_question'] ?? 30) }}" min="5" max="300"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('quiz_settings.time_per_question') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Acak Soal</label>
                                    <select name="quiz_settings[shuffle_questions]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="1" {{ old('quiz_settings.shuffle_questions', $game->quiz_settings['shuffle_questions'] ?? 1) == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('quiz_settings.shuffle_questions', $game->quiz_settings['shuffle_questions'] ?? '') == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tampilkan Jawaban Benar</label>
                                    <select name="quiz_settings[show_correct_answer]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="1" {{ old('quiz_settings.show_correct_answer', $game->quiz_settings['show_correct_answer'] ?? 1) == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('quiz_settings.show_correct_answer', $game->quiz_settings['show_correct_answer'] ?? '') == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @elseif(in_array($game->game_type, ['tebak', 'logic', 'coding', 'custom', 'reaction']))
                        {{-- GENERIC SETTINGS --}}
                        <div class="border-2 border-purple-200 dark:border-purple-800 rounded-2xl p-6 bg-purple-50/30 dark:bg-purple-900/10">
                            <h4 class="text-xl font-bold text-purple-700 dark:text-purple-300 mb-4 flex items-center gap-2">
                                <span>⚙️</span> Pengaturan {{ ucfirst($game->game_type) }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach(($game->game_settings ?? []) as $key => $value)
                                    @if(!is_array($value))
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                        @if(is_bool($value))
                                            <select name="game_settings[{{ $key }}]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                                <option value="1" {{ $value ? 'selected' : '' }}>Ya</option>
                                                <option value="0" {{ !$value ? 'selected' : '' }}>Tidak</option>
                                            </select>
                                        @elseif(is_numeric($value))
                                            <input type="number" name="game_settings[{{ $key }}]" value="{{ old("game_settings.$key", $value) }}"
                                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        @else
                                            <input type="text" name="game_settings[{{ $key }}]" value="{{ old("game_settings.$key", $value) }}"
                                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        @endif
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-4 p-4 bg-slate-100 dark:bg-slate-800 rounded-xl">
                                <p class="text-xs text-slate-500">Data konfigurasi lainnya dapat dilihat di database.</p>
                            </div>
                        </div>
                        @endif

                    </div>{{-- end space-y-6 --}}
                </div>{{-- end tab-konfigurasi --}}

                {{-- ============================================================ --}}
                {{-- TAB 3: PROCTORING --}}
                {{-- ============================================================ --}}
                <div id="tab-proctoring" class="tab-content hidden">
                    @if($game->game_type === 'quiz')
                        <div class="border-2 border-red-200 dark:border-red-800 rounded-2xl p-6 bg-red-50/30 dark:bg-red-900/10">
                            <h4 class="text-xl font-bold text-red-700 dark:text-red-300 mb-4 flex items-center gap-2">
                                <span>🛡️</span> Pengaturan Proctoring
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Deteksi Tab Switch</label>
                                    <select name="proctoring_settings[allow_tab_switch]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="0" {{ old('proctoring_settings.allow_tab_switch', $game->proctoring_settings['allow_tab_switch'] ?? 0) == 0 ? 'selected' : '' }}>Aktif (Tidak boleh keluar)</option>
                                        <option value="1" {{ old('proctoring_settings.allow_tab_switch', $game->proctoring_settings['allow_tab_switch'] ?? '') == 1 ? 'selected' : '' }}>Nonaktif (Boleh keluar)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Maksimal Pelanggaran <span class="text-red-500">*</span></label>
                                    <input type="number" name="proctoring_settings[max_violations]" value="{{ old('proctoring_settings.max_violations', $game->proctoring_settings['max_violations'] ?? 4) }}" min="1" max="10"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('proctoring_settings.max_violations') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Penalti per Pelanggaran</label>
                                    <input type="number" name="proctoring_settings[penalty_per_violation]" value="{{ old('proctoring_settings.penalty_per_violation', $game->proctoring_settings['penalty_per_violation'] ?? 5) }}" min="0" max="100"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('proctoring_settings.penalty_per_violation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Eskalasi Penalti</label>
                                    <select name="proctoring_settings[escalate_penalty]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="0" {{ old('proctoring_settings.escalate_penalty', $game->proctoring_settings['escalate_penalty'] ?? 0) == 0 ? 'selected' : '' }}>Tetap</option>
                                        <option value="1" {{ old('proctoring_settings.escalate_penalty', $game->proctoring_settings['escalate_penalty'] ?? '') == 1 ? 'selected' : '' }}>Meningkat (+5)</option>
                                        <option value="2" {{ old('proctoring_settings.escalate_penalty', $game->proctoring_settings['escalate_penalty'] ?? '') == 2 ? 'selected' : '' }}>Double (x2)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Auto-Submit Saat Maksimal Pelanggaran</label>
                                    <select name="proctoring_settings[auto_submit_on_violation]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="1" {{ old('proctoring_settings.auto_submit_on_violation', $game->proctoring_settings['auto_submit_on_violation'] ?? 1) == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('proctoring_settings.auto_submit_on_violation', $game->proctoring_settings['auto_submit_on_violation'] ?? '') == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Kunci Akses Setelah Selesai</label>
                                    <select name="proctoring_settings[lock_on_completion]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="1" {{ old('proctoring_settings.lock_on_completion', $game->proctoring_settings['lock_on_completion'] ?? 1) == 1 ? 'selected' : '' }}>Ya</option>
                                        <option value="0" {{ old('proctoring_settings.lock_on_completion', $game->proctoring_settings['lock_on_completion'] ?? '') == 0 ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Izinkan Masuk Kembali</label>
                                    <select name="proctoring_settings[allow_reentry]" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                        <option value="0" {{ old('proctoring_settings.allow_reentry', $game->proctoring_settings['allow_reentry'] ?? 0) == 0 ? 'selected' : '' }}>Tidak</option>
                                        <option value="1" {{ old('proctoring_settings.allow_reentry', $game->proctoring_settings['allow_reentry'] ?? '') == 1 ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Pesan Notifikasi Pelanggaran</label>
                                    <input type="text" name="proctoring_settings[violation_message]" value="{{ old('proctoring_settings.violation_message', $game->proctoring_settings['violation_message'] ?? '⚠️ Anda terdeteksi keluar dari halaman quiz! Poin akan dikurangi.') }}"
                                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3">
                                    @error('proctoring_settings.violation_message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                            <i class="fas fa-shield-alt text-5xl mb-4 block"></i>
                            <p class="text-lg font-bold">Proctoring hanya tersedia untuk game tipe Quiz.</p>
                            <p class="text-sm">Game ini bertipe <strong>{{ ucfirst($game->game_type) }}</strong>.</p>
                        </div>
                    @endif
                </div>

                {{-- ============================================================ --}}
                {{-- TAB 4: SOAL --}}
                {{-- ============================================================ --}}
                @if(in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding']))
                <div id="tab-soal" class="tab-content hidden">
                    <div class="border-2 border-purple-200 dark:border-purple-800 rounded-2xl p-6 bg-purple-50/30 dark:bg-purple-900/10">
                        <h4 class="text-xl font-bold text-purple-700 dark:text-purple-300 mb-4 flex items-center gap-2">
                            <span>📝</span> Manajemen Soal
                        </h4>
                        @php
                            $questions = $game->quiz_questions ?? $game->game_questions ?? [];
                            $count = is_array($questions) ? count($questions) : 0;
                        @endphp

                        @if($count > 0)
                            <div class="bg-white dark:bg-slate-800 rounded-xl p-4 mb-4">
                                <p class="text-slate-700 dark:text-slate-300">Total Soal: <strong>{{ $count }}</strong></p>
                                <div class="max-h-80 overflow-y-auto mt-3 space-y-2">
                                    @foreach(array_slice($questions, 0, 10) as $index => $q)
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 text-sm border border-slate-200 dark:border-slate-700">
                                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">#{{ $index+1 }}</span>
                                            <span class="text-slate-800 dark:text-slate-200">{{ Str::limit($q['question'] ?? $q['pertanyaan'] ?? 'Tidak ada pertanyaan', 100) }}</span>
                                            @if(isset($q['type']))
                                                <span class="text-xs text-slate-500 ml-2">({{ $q['type'] }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($count > 10)
                                        <p class="text-xs text-slate-500 text-center">... dan {{ $count - 10 }} soal lainnya</p>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Untuk mengubah atau menambah soal, silakan buat game baru atau akses langsung database.
                                </p>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-slate-500">Belum ada soal untuk game ini.</p>
                                <p class="text-sm text-slate-400 mt-2">Tambahkan soal saat membuat game baru.</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- ============================================================ --}}
                {{-- TAB 5: GAMBAR --}}
                {{-- ============================================================ --}}
                <div id="tab-gambar" class="tab-content hidden">
                    <div class="border-2 border-teal-200 dark:border-teal-800 rounded-2xl p-6 bg-teal-50/30 dark:bg-teal-900/10">
                        <h4 class="text-xl font-bold text-teal-700 dark:text-teal-300 mb-4 flex items-center gap-2">
                            <span>🖼️</span> Galeri Gambar
                        </h4>

                        @if($game->game_images && count($game->game_images) > 0)
                            <div class="flex flex-wrap gap-4 mb-6">
                                @foreach($game->game_images as $img)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $img) }}"
                                             class="w-32 h-32 object-cover rounded-xl border-2 border-slate-200 dark:border-slate-700 group-hover:border-indigo-500 transition"
                                             alt="Game image">
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-xl flex items-center justify-center">
                                            <a href="{{ asset('storage/' . $img) }}" target="_blank" class="text-white text-sm font-bold">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Upload gambar baru untuk mengganti gambar lama.</p>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @for($i = 1; $i <= 3; $i++)
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Gambar {{ $i }}</label>
                                <input type="file" name="game_images[]" accept="image/*"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-600 file:text-white file:font-bold hover:file:bg-indigo-700">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Max 2MB, format JPG/PNG/WEBP</p>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
                @endif{{-- end if native --}}

                {{-- ============================================================ --}}
                {{-- FITUR DUPAYUNG --}}
                {{-- ============================================================ --}}
                @if($game->source_type === 'native')
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 border-t border-slate-200 dark:border-slate-700 pt-6">
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="support_rating" value="1" {{ old('support_rating', $game->support_rating) ? 'checked' : '' }}>
                        <span>⭐ Rating & Ranking</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="support_matchmaking" value="1" {{ old('support_matchmaking', $game->support_matchmaking) ? 'checked' : '' }}>
                        <span>⚡ Matchmaking</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="support_challenge" value="1" {{ old('support_challenge', $game->support_challenge) ? 'checked' : '' }}>
                        <span>🏆 Challenge</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="show_in_leaderboard" value="1" {{ old('show_in_leaderboard', $game->show_in_leaderboard) ? 'checked' : '' }}>
                        <span>📊 Leaderboard</span>
                    </label>
                </div>
                @endif

                {{-- ============================================================ --}}
                {{-- TOMBOL SUBMIT --}}
                {{-- ============================================================ --}}
                <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Update Game
                    </button>
                    <button type="submit" name="action" value="save_and_test" class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition flex items-center gap-2">
                        <i class="fas fa-play"></i> Update & Uji Coba
                    </button>
                    <a href="{{ route('owner.game.games.index') }}" class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Navigation
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Reset semua tab
            tabBtns.forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white');
                b.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
            });
            tabContents.forEach(c => c.classList.add('hidden'));

            // Aktifkan tab yang diklik
            this.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-300');
            this.classList.add('bg-indigo-600', 'text-white');

            const target = document.getElementById(this.dataset.tab);
            if (target) target.classList.remove('hidden');
        });
    });

    // Auto open first tab jika ada error
    @if($errors->any())
        // Cari tab pertama yang memiliki error
        const firstErrorField = document.querySelector('.is-invalid, [name].border-red-500');
        if (firstErrorField) {
            const tabId = firstErrorField.closest('.tab-content')?.id;
            if (tabId) {
                document.querySelector(`.tab-btn[data-tab="${tabId}"]`)?.click();
            }
        }
    @endif

    // Preview gambar
    document.querySelectorAll('input[type="file"][name="game_images[]"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = this.parentElement.querySelector('.preview-image');
                reader.onload = function(e) {
                    if (!preview) {
                        const img = document.createElement('img');
                        img.className = 'preview-image mt-2 rounded-lg max-h-32 object-cover';
                        img.src = e.target.result;
                        input.parentElement.appendChild(img);
                    } else {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    console.log('✅ Game Edit Form loaded');
});
</script>
@endsection
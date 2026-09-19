@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- ============================================================ --}}
        {{-- HEADER --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <span>🧪</span> Testing Game
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Pilih game dari daftar untuk melakukan testing. Sistem akan menampilkan game secara interaktif beserta detail soal dan jawaban.
                </p>
            </div>
            <a href="{{ route('owner.game.games.index') }}" 
               class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ============================================================ --}}
        {{-- FORM PILIH GAME --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <form method="GET" action="{{ route('owner.game.games.testing') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Pilih Game <span class="text-red-500">*</span></label>
                    <select name="game_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">-- Pilih Game --</option>
                        @foreach($games as $g)
                            <option value="{{ $g->id }}" {{ request('game_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->name }} ({{ ucfirst($g->game_type ?? 'unknown') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                        <i class="fas fa-flask"></i> Test Game
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- NOTIFIKASI ERROR / SUKSES --}}
        {{-- ============================================================ --}}
        @if(session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <p class="text-red-700 dark:text-red-300 font-bold"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</p>
                @if(session('error_details'))
                    <pre class="mt-2 text-xs text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 p-3 rounded-lg overflow-auto max-h-40">{{ session('error_details') }}</pre>
                @endif
            </div>
        @endif

        @if(isset($error) && $error)
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <p class="text-red-700 dark:text-red-300 font-bold"><i class="fas fa-exclamation-circle mr-2"></i> Gagal memuat game!</p>
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $error }}</p>
                <div class="mt-3 p-3 bg-red-100 dark:bg-red-900/30 rounded-lg text-xs text-red-700 dark:text-red-300">
                    <p><strong>💡 Solusi:</strong></p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Pastikan game memiliki soal atau pengaturan yang valid.</li>
                        <li>Periksa kembali konfigurasi game di halaman edit.</li>
                        <li>Jika game adalah Quiz/Tebak/Logic/Coding, pastikan ada soal yang ditambahkan.</li>
                        <li>Jika game adalah Typing, pastikan teks pengaturan sudah diisi.</li>
                    </ul>
                </div>
            </div>
        @endif

        @if(isset($success) && $success && $game)
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl">
                <p class="text-emerald-700 dark:text-emerald-300 font-bold"><i class="fas fa-check-circle mr-2"></i> Game berhasil dimuat! Silahkan mainkan di bawah.</p>
                <div class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                    <span class="font-semibold">Game:</span> {{ $game->name }} &nbsp;|&nbsp; 
                    <span class="font-semibold">Tipe:</span> {{ ucfirst($game->game_type) }} &nbsp;|&nbsp;
                    <span class="font-semibold">Status:</span> {{ ucfirst($game->status) }}
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- AREA GAME (jika ada dan sukses) --}}
        {{-- ============================================================ --}}
        @if(isset($success) && $success && $game)
            <div id="game-area" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span>{{ $game->icon ?? '🎮' }}</span> {{ $game->name }}
                        <span class="text-xs bg-indigo-500/10 text-indigo-500 px-2 py-0.5 rounded-full ml-2">TESTING MODE</span>
                    </h2>
                    <span class="text-sm text-slate-500">👤 Testing sebagai Owner</span>
                </div>

                {{-- Game Interaktif --}}
                @if($game->source_type === 'external')
                    <div class="text-center py-12">
                        <i class="fas fa-external-link-alt text-4xl mb-3 text-slate-400"></i>
                        <p class="text-slate-600 dark:text-slate-400 mb-4">Game eksternal tidak bisa di-test di sini.</p>
                        <a href="{{ $game->url }}" target="_blank" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
                            <i class="fas fa-external-link-alt mr-2"></i> Buka Game
                        </a>
                    </div>
                @else
                    @if($game->game_type === 'typing' && isset($gameData['settings']))
                        @include('owner.game-center.games.partials.play-typing', ['settings' => $gameData['settings']])
                    @elseif($game->game_type === 'quiz' && isset($gameData['questions']) && count($gameData['questions']) > 0)
                        @include('owner.game-center.games.partials.play-quiz', [
                            'settings' => $gameData['settings'] ?? [],
                            'questions' => $gameData['questions']
                        ])
                    @elseif(in_array($game->game_type, ['tebak', 'logic', 'coding', 'custom', 'reaction']) && isset($gameData['questions']) && count($gameData['questions']) > 0)
                        @include('owner.game-center.games.partials.play-generic', [
                            'settings' => $gameData['settings'] ?? [],
                            'questions' => $gameData['questions']
                        ])
                    @else
                        <div class="text-center py-12 text-amber-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-3 block"></i>
                            <p class="font-bold">⚠️ Game tidak dapat ditampilkan!</p>
                            <p class="text-sm text-slate-500 mt-2">Pastikan game memiliki pengaturan atau soal yang valid.</p>
                        </div>
                    @endif
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- DETAIL SOAL & JAWABAN (untuk game yang memiliki soal) --}}
            {{-- ============================================================ --}}
            @if(in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding']) && isset($gameData['questions']) && count($gameData['questions']) > 0)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <span>📋</span> Detail Soal & Jawaban ({{ count($gameData['questions']) }} soal)
                    </h3>
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0">
                                <tr class="text-left text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                                    <th class="px-4 py-2">#</th>
                                    <th class="px-4 py-2">Pertanyaan</th>
                                    <th class="px-4 py-2">Jawaban Benar</th>
                                    <th class="px-4 py-2">Tipe</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gameData['questions'] as $index => $q)
                                    <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                        <td class="px-4 py-2 font-bold text-slate-700 dark:text-slate-300">{{ $index+1 }}</td>
                                        <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ Str::limit($q['question'] ?? '-', 60) }}</td>
                                        <td class="px-4 py-2">
                                            @if(isset($q['type']) && $q['type'] === 'multiple_choice')
                                                @if(isset($q['options'][$q['correct']]))
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $q['options'][$q['correct']] }}</span>
                                                    <span class="text-xs text-slate-400">({{ $q['correct'] }})</span>
                                                @else
                                                    <span class="text-red-500">Tidak tersedia</span>
                                                @endif
                                            @elseif(isset($q['type']) && $q['type'] === 'essay')
                                                <span class="text-emerald-600 dark:text-emerald-400">{{ $q['essay_answer'] ?? '-' }}</span>
                                            @elseif(isset($q['answer']))
                                                <span class="text-emerald-600 dark:text-emerald-400">{{ $q['answer'] }}</span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-500">
                                                {{ ucfirst($q['type'] ?? 'multiple_choice') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if(isset($q['answer']) || isset($q['correct']) || isset($q['essay_answer']))
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500">✅ Valid</span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-500/10 text-red-500">⚠️ Tidak lengkap</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-xs text-slate-500">
                        <p><strong>Catatan:</strong> Untuk soal pilihan ganda, jawaban benar ditandai dengan warna hijau. Pastikan semua soal memiliki jawaban yang valid.</p>
                    </div>
                </div>
            @endif

        @endif

    </div>
</div>
@endsection
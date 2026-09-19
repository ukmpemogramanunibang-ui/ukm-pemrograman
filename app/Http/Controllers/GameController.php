<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameSession;
use App\Models\GameSponsor;
use App\Models\GameSponsorMedia;
use App\Models\User;
use App\Models\UserGameStat;
use App\Models\Leaderboard;
use App\Models\LeaderboardEntry;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    /**
     * Menampilkan daftar game dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = Game::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }
        if ($request->filled('game_type')) {
            $query->where('game_type', $request->game_type);
        }

        $games = $query->paginate(12)->withQueryString();

        // Statistik
        $totalGames     = Game::count();
        $totalNative    = Game::where('source_type', 'native')->count();
        $totalExternal  = Game::where('source_type', 'external')->count();
        $totalPublished = Game::where('status', 'published')->count();

        $gameConfig = []; // untuk view

        return view('owner.game-center.games.index', compact(
            'games', 'totalGames', 'totalNative', 'totalExternal', 'totalPublished', 'gameConfig'
        ));
    }

    // ============================================================
    // PUBLISH / UNPUBLISH
    // ============================================================
    public function publish(Game $game)
    {
        $game->update(['status' => 'published']);
        return redirect()->route('owner.game.games.index')
            ->with('success', "Game \"{$game->name}\" berhasil dipublikasikan.");
    }

    public function unpublish(Game $game)
    {
        $game->update(['status' => 'draft']);
        return redirect()->route('owner.game.games.index')
            ->with('success', "Game \"{$game->name}\" berhasil di-unpublish.");
    }

    // ============================================================
    // SETTINGS & QR
    // ============================================================
    public function settings(Game $game)
    {
        return view('owner.game-center.games.settings', compact('game'));
    }

    public function generateQr(Game $game)
    {
        $qrService = app(\App\Services\QrCodeService::class);
        $qrPath = $qrService->generateForGame($game);
        return view('owner.game-center.games.qr', compact('game', 'qrPath'));
    }

    // ============================================================
    // CREATE & STORE (dengan transaksi)
    // ============================================================
    public function create()
    {
        return view('owner.game-center.games.create');
    }

    public function createForm(Request $request)
    {
        $type = $request->query('type');
        $gameType = $request->query('game_type');

        if ($type === 'external') {
            return view('owner.game.forms.external', compact('type', 'gameType'));
        }

        if ($gameType === 'quiz') {
            return view('owner.game.forms.quiz', compact('type', 'gameType'));
        }

        $viewMap = [
            'typing'        => 'owner.game.forms.typing',
            'coding'        => 'owner.game.forms.coding',
            'logic'         => 'owner.game.forms.logic',
            'tebak'         => 'owner.game.forms.tebak',
            'reaction'      => 'owner.game.forms.reaction',
            'custom'        => 'owner.game.forms.custom',
            'puzzle'        => 'owner.game.forms.puzzle',
            'geometry_dash' => 'owner.game.forms.geometry_dash',
        ];

        $view = $viewMap[$gameType] ?? 'owner.game.forms.default';
        return view($view, compact('type', 'gameType'));
    }

    public function store(Request $request)
{
    // Tentukan tipe sumber dan tipe game
    $sourceType = $request->input('type', 'native');
    $gameType = $request->input('game_type');

    // Jika external, set game_type menjadi 'external' jika kosong
    if ($sourceType === 'external' && empty($gameType)) {
        $gameType = 'external';
    }

    // 1. Validasi dasar
    $rules = [
        'name'          => 'required|string|max:255',
        'description'   => 'nullable|string',
        'cover_images'  => 'nullable|array|max:4',
        'cover_images.*'=> 'image|mimes:jpg,png,jpeg,webp,gif|max:2048',
        'instructions'  => 'nullable|string',
        'category'      => 'nullable|string|max:100',
        'game_mode'     => 'nullable|in:rank,tournament,battle,vs,practice',
        'developer'     => 'nullable|string|max:255',
        'status'        => 'nullable|in:draft,testing,published,maintenance,archived',
    ];

    // 2. Jika tipe external, tambahkan validasi URL
    if ($sourceType === 'external') {
        $rules['url'] = 'required|url|max:255';
        $rules['integration_level'] = 'required|in:link,embedded,api';
        $rules['api_key'] = 'nullable|string|max:255';
    }

    // 3. Aturan tambahan per tipe game (hanya untuk native)
    $extraRules = [
        'typing' => [
            'words'       => 'required|string',
            'time_limit'  => 'nullable|numeric|min:10',
            'max_players' => 'nullable|integer|min:1',
            'difficulty'  => 'nullable|string|in:beginner,normal,hard,expert',
        ],
        'quiz'   => [
            'questions'       => 'required|json',
            'quiz_config'     => 'nullable|json',
            'quiz_type'       => 'required|in:multiple_choice,essay,mixed',
            'time_mode'       => 'nullable|in:unlimited,total,per_question',
            'time_total'      => 'nullable|integer|min:1',
            'time_per_question' => 'nullable|integer|min:1',
            'use_points'      => 'nullable|boolean',
            'point_correct'   => 'nullable|integer|min:0',
            'point_wrong'     => 'nullable|integer|min:0',
            'point_empty'     => 'nullable|integer|min:0',
            'bonus_complete'  => 'nullable|integer|min:0',
            'bonus_streak'    => 'nullable|integer|min:0',
            'max_points'      => 'nullable|integer|min:1',
            'passing_score'   => 'nullable|integer|min:0|max:100',
        ],
        'coding' => ['code_snippet' => 'nullable|string'],
        'logic'  => ['logic_question' => 'required|string'],
        'tebak'  => ['answer' => 'required|string'],
        'reaction' => ['duration' => 'required|numeric|min:1'],
        'custom' => ['custom_field' => 'nullable|string'],
        'puzzle' => ['puzzle_data' => 'required|string'],
        'geometry_dash' => ['level_data' => 'required|string'],
    ];

    if ($sourceType === 'native' && isset($extraRules[$gameType])) {
        $rules = array_merge($rules, $extraRules[$gameType]);
    }

    // 4. Sponsor
    if ($request->boolean('sponsor_enabled')) {
        $rules = array_merge($rules, [
            'sponsor_name'    => 'required|string|max:255',
            'sponsor_website' => 'required|url|max:255',
            'sponsor_tagline' => 'nullable|string|max:255',
            'sponsor_address' => 'nullable|string|max:255',
            'sponsor_description' => 'nullable|string',
            'sponsor_media'   => 'nullable|array|max:7',
            'sponsor_media.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4|max:10240',
        ]);
    }

    $validated = $request->validate($rules);

    DB::beginTransaction();

    try {
        // Buat game
        $game = new Game();
        $game->name = $validated['name'];
        $game->description = $validated['description'] ?? null;
        $game->instructions = $validated['instructions'] ?? null;
        $game->category = $validated['category'] ?? null;
        $game->game_mode = $validated['game_mode'] ?? 'practice';
        $game->source_type = $sourceType;
        $game->game_type = $gameType; // sudah dipastikan tidak null
        $game->status = $validated['status'] ?? 'draft';
        $game->developer = $validated['developer'] ?? 'UNICODE';
        $game->version = '1.0.0';

        // Slug unik
        $slug = Str::slug($validated['name']);
        $count = Game::where('slug', 'LIKE', $slug . '%')->count();
        $game->slug = $count ? $slug . '-' . ($count + 1) : $slug;

        // === Upload cover images ===
        if ($request->hasFile('cover_images')) {
            // Pastikan folder menggunakan game_type yang benar (untuk external tetap 'external')
            $folderType = ($sourceType === 'external') ? 'external' : $gameType;
            $paths = $this->uploadCoverImages($request->file('cover_images'), $folderType);
            $game->game_images = $paths;
            $game->cover_images = $paths;
            Log::info('Cover images uploaded: ' . json_encode($paths));
        } else {
            Log::info('No cover images uploaded for game: ' . $validated['name']);
        }

        // === Simpan data external jika tipe external ===
        if ($sourceType === 'external') {
            $game->url = $validated['url'];
            $game->integration_level = $validated['integration_level'] ?? 'link';
            if (!empty($validated['api_key'])) {
                $game->api_key = $validated['api_key'];
            }
        }

        // Simpan game (termasuk cover dan field external)
        $game->save();

        // === Simpan pengaturan spesifik game (hanya untuk native) ===
        if ($sourceType === 'native') {
            $this->saveGameSettings($game, $request, $gameType);
        }

        // === Simpan sponsor jika diaktifkan ===
        if ($request->boolean('sponsor_enabled')) {
            $this->saveSponsor($game, $validated, $request);
        }

        // === Jalankan diagnostic untuk native ===
        if ($sourceType === 'native') {
            $this->diagnostic($game, true);
        }

        DB::commit();

        return redirect()
            ->route('owner.game.games.index')
            ->with('success', "Game \"{$game->name}\" berhasil dibuat!");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal menyimpan game: ' . $e->getMessage(), [
            'request' => $request->except(['_token', 'questions', 'quiz_config']),
            'trace'   => $e->getTraceAsString()
        ]);

        return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan game: ' . $e->getMessage()]);
    }
}

    /**
     * Upload cover images dengan validasi dan error handling.
     */
    private function uploadCoverImages($files, $gameType)
    {
        $paths = [];
        $folder = 'games/' . ($gameType ?? 'external') . '/covers';

        foreach ($files as $file) {
            if (!$file->isValid()) {
                throw new \Exception("File {$file->getClientOriginalName()} gagal diunggah (error: {$file->getErrorMessage()}).");
            }

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $filename, 'public');

            if (!$path) {
                throw new \Exception("Gagal menyimpan file {$file->getClientOriginalName()}.");
            }

            $paths[] = $path;
            Log::info('Cover image terupload: ' . $path);
        }

        return $paths;
    }

    /**
     * Simpan pengaturan game berdasarkan tipe (hanya untuk native).
     */
    private function saveGameSettings(Game $game, Request $request, $gameType)
    {
        if ($gameType === 'typing') {
            $game->typing_settings = [
                'custom_text' => $request->input('words'),
                'duration'    => (int) $request->input('time_limit', 60),
                'max_players' => (int) $request->input('max_players', 1),
                'difficulty'  => $request->input('difficulty', 'normal'),
                'allow_solo'  => $request->boolean('allow_solo'),
                'allow_race'  => $request->boolean('allow_race'),
                'allow_private_room' => $request->boolean('allow_private_room'),
            ];
        } elseif (in_array($gameType, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
            $settings = $request->except([
                '_token', 'name', 'description', 'cover_images', 'type', 'game_type',
                'questions', 'quiz_config', 'quiz_type', 'sponsor_enabled', 'sponsor_name',
                'sponsor_website', 'sponsor_tagline', 'sponsor_address', 'sponsor_description',
                'sponsor_media', 'sponsor_media_data', 'instructions', 'category', 'game_mode',
                'developer', 'status', 'total_questions', 'test_passed'
            ]);

            if ($gameType === 'quiz') {
                $quizConfig = $request->input('quiz_config');
                if ($quizConfig) {
                    $quizConfigArray = json_decode($quizConfig, true);
                    if (is_array($quizConfigArray)) {
                        $settings = array_merge($settings, $quizConfigArray);
                    }
                }
                // Set default untuk quiz
                $settings['quiz_type'] = $request->input('quiz_type', 'multiple_choice');
                $settings['time_mode'] = $request->input('time_mode', 'total');
                $settings['time_total'] = (int) $request->input('time_total', 10) * 60; // dalam detik
                $settings['time_per_question'] = (int) $request->input('time_per_question', 15);
                $settings['use_points'] = $request->boolean('use_points', true);
                $settings['point_correct'] = (int) $request->input('point_correct', 10);
                $settings['point_wrong'] = (int) $request->input('point_wrong', 0);
                $settings['point_empty'] = (int) $request->input('point_empty', 0);
                $settings['bonus_complete'] = (int) $request->input('bonus_complete', 20);
                $settings['bonus_streak'] = (int) $request->input('bonus_streak', 5);
                $settings['max_points'] = (int) $request->input('max_points', 1000);
                $settings['passing_score'] = (int) $request->input('passing_score', 60);

                // Simpan soal
                if ($request->has('questions')) {
                    $questions = json_decode($request->input('questions'), true);
                    if (is_array($questions)) {
                        $game->quiz_questions = $questions;
                    }
                }
            }

            $game->game_settings = $settings;
        } else {
            // Game lain: simpan semua input yang tidak termasuk ke dalam game_settings
            $settings = $request->except([
                '_token', 'name', 'description', 'cover_images', 'type', 'game_type',
                'sponsor_enabled', 'sponsor_name', 'sponsor_website', 'sponsor_tagline',
                'sponsor_address', 'sponsor_description', 'sponsor_media', 'sponsor_media_data',
                'instructions', 'category', 'game_mode', 'developer', 'status', 'total_questions',
                'test_passed'
            ]);
            $game->game_settings = $settings;
        }

        $game->save();
    }

    /**
     * Simpan sponsor dan media.
     */
    private function saveSponsor(Game $game, $validated, Request $request)
    {
        $sponsor = GameSponsor::create([
            'game_id'      => $game->id,
            'enabled'      => true,
            'name'         => $validated['sponsor_name'],
            'address'      => $validated['sponsor_address'] ?? null,
            'website_url'  => $validated['sponsor_website'],
            'tagline'      => $validated['sponsor_tagline'] ?? null,
            'description'  => $validated['sponsor_description'] ?? null,
        ]);

        if ($request->hasFile('sponsor_media')) {
            $sort = 0;
            foreach ($request->file('sponsor_media') as $file) {
                $path = $file->store('sponsors/' . $game->id, 'public');
                GameSponsorMedia::create([
                    'game_sponsor_id' => $sponsor->id,
                    'type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'sort_order' => $sort++,
                    'is_clickable' => true,
                ]);
            }
        }
    }

    // ============================================================
    // SHOW, EDIT, UPDATE (dengan transaksi)
    // ============================================================
    public function show(Game $game)
    {
        $game->load('sponsors.media');
        return view('owner.game-center.games.show', compact('game'));
    }

    public function edit(Game $game)
    {
        return view('owner.game-center.games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:games,slug,' . $game->id,
            'icon' => 'nullable|string|max:50',
            'banner' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'developer' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:20',
            'status' => 'required|in:draft,testing,published,maintenance,archived',
        ];

        // Jika game external, tambahkan validasi URL
        if ($game->source_type === 'external') {
            $rules['url'] = 'nullable|url|max:255';
            $rules['integration_level'] = 'nullable|in:link,embedded,api';
            $rules['api_key'] = 'nullable|string|max:255';
        }

        // Jika game native, tambahkan aturan khusus
        if ($game->source_type === 'native') {
            $rules['difficulty'] = 'nullable|in:beginner,intermediate,advanced,expert';
            $rules['support_rating'] = 'nullable|boolean';
            $rules['support_matchmaking'] = 'nullable|boolean';
            $rules['support_challenge'] = 'nullable|boolean';
            $rules['show_in_leaderboard'] = 'nullable|boolean';
            $rules['max_attempts'] = 'nullable|integer|min:0|max:10';
            $rules['cover_images'] = 'nullable|array|max:4';
            $rules['cover_images.*'] = 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $data = [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'icon' => $validated['icon'] ?? $game->icon,
                'banner' => $validated['banner'] ?? null,
                'description' => $validated['description'] ?? null,
                'developer' => $validated['developer'] ?? 'UNICODE',
                'version' => $validated['version'] ?? '1.0.0',
                'status' => $validated['status'],
            ];

            // Update field external jika ada
            if ($game->source_type === 'external') {
                if (!empty($validated['url'])) {
                    $data['url'] = $validated['url'];
                }
                if (!empty($validated['integration_level'])) {
                    $data['integration_level'] = $validated['integration_level'];
                }
                if (!empty($validated['api_key'])) {
                    $data['api_key'] = $validated['api_key'];
                }
            }

            if ($game->source_type === 'native') {
                $data['difficulty'] = $validated['difficulty'] ?? 'intermediate';
                $data['support_rating'] = $request->boolean('support_rating', true);
                $data['support_matchmaking'] = $request->boolean('support_matchmaking', false);
                $data['support_challenge'] = $request->boolean('support_challenge', true);
                $data['show_in_leaderboard'] = $request->boolean('show_in_leaderboard', true);
                $data['max_attempts'] = $validated['max_attempts'] ?? 1;

                // Upload cover baru
                if ($request->hasFile('cover_images')) {
                    // Hapus cover lama
                    if ($game->game_images) {
                        foreach ($game->game_images as $img) {
                            if (Storage::disk('public')->exists($img)) {
                                Storage::disk('public')->delete($img);
                            }
                        }
                    }

                    $paths = $this->uploadCoverImages($request->file('cover_images'), $game->game_type ?? 'native');
                    $data['game_images'] = $paths;
                    $data['cover_images'] = $paths;
                }

                // Update pengaturan tambahan
                foreach (['game_settings', 'typing_settings', 'quiz_settings', 'proctoring_settings'] as $key) {
                    if ($request->has($key) && is_array($request->$key)) {
                        $current = $game->$key ?? [];
                        $data[$key] = array_merge($current, $request->$key);
                    }
                }
            }

            $game->update($data);

            if ($game->source_type === 'native') {
                $this->diagnostic($game, true);
            }

            DB::commit();

            return redirect()->route('owner.game.games.index')
                ->with('success', "Game \"{$game->name}\" berhasil diperbarui!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update game: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui game: ' . $e->getMessage()]);
        }
    }

    // ============================================================
    // DESTROY, TRASH, RESTORE, FORCE DESTROY
    // ============================================================
    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('owner.game.games.index')
            ->with('success', "Game \"{$game->name}\" dipindahkan ke tempat sampah.");
    }

    public function trash()
    {
        $games = Game::onlyTrashed()->paginate(12);
        $totalTrashed = Game::onlyTrashed()->count();
        return view('owner.game-center.games.trash', compact('games', 'totalTrashed'));
    }

    public function restore($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->restore();
        return redirect()->route('owner.game.games.trash')
            ->with('success', "Game \"{$game->name}\" berhasil dipulihkan.");
    }

    public function forceDestroy($id)
    {
        $game = Game::withTrashed()->findOrFail($id);

        // Hapus file cover
        if ($game->game_images) {
            foreach ($game->game_images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }
        if ($game->cover_images) {
            foreach ($game->cover_images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        // Hapus sponsor dan media
        if ($game->sponsors) {
            foreach ($game->sponsors as $sponsor) {
                if ($sponsor->media) {
                    foreach ($sponsor->media as $media) {
                        if (Storage::disk('public')->exists($media->file_path)) {
                            Storage::disk('public')->delete($media->file_path);
                        }
                        $media->delete();
                    }
                }
                $sponsor->delete();
            }
        }

        $game->forceDelete();
        return redirect()->route('owner.game.games.trash')
            ->with('success', "Game \"{$game->name}\" dihapus permanen.");
    }

    // ============================================================
    // PLAY GAMEPLAY
    // ============================================================
    private function normalizeQuestions($questions)
    {
        if (is_array($questions)) {
            return $questions;
        }
        if (is_string($questions) && !empty($questions)) {
            $decoded = json_decode($questions, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function playGameplay($game, $type = null, $token = null)
    {
        try {
            $gameModel = Game::findOrFail($game);
            $user = auth()->user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $expectedToken = md5($gameModel->id . $user->id . 'secret');
            if ($token && $token !== $expectedToken) {
                abort(403, 'Token tidak valid.');
            }
            $token = $token ?? $expectedToken;

            // Jika game external, redirect langsung ke URL
            if ($gameModel->source_type === 'external') {
                if (empty($gameModel->url)) {
                    return redirect()->route('owner.game.games.show', $gameModel)
                        ->with('error', 'Game ini tidak memiliki URL yang valid.');
                }
                return redirect($gameModel->url);
            }

            $gameType = $type ?? $gameModel->game_type;
            $gameSubtype = $gameModel->game_settings['quiz_type'] ?? 'multiple_choice';

            $settings = [];
            $questions = [];

            if ($gameType === 'typing') {
                $settings = $gameModel->typing_settings ?? [];
                if (empty($settings)) {
                    $settings = [
                        'custom_text' => 'Ini adalah teks default untuk latihan mengetik. Silakan isi pengaturan game dengan teks yang lebih relevan.',
                        'duration' => 60,
                        'max_players' => 1,
                        'difficulty' => 'normal',
                        'allow_solo' => true,
                        'allow_race' => false,
                        'allow_private_room' => false,
                    ];
                    session()->flash('warning', 'Game ini belum memiliki pengaturan typing. Menggunakan teks default sementara.');
                }
            } elseif (in_array($gameType, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                $settings = $gameModel->game_settings ?? [];
                $rawQuestions = $gameModel->quiz_questions ?? $gameModel->game_questions ?? [];
                $questions = $this->normalizeQuestions($rawQuestions);
                if (empty($questions) && isset($settings['questions'])) {
                    $questions = $this->normalizeQuestions($settings['questions']);
                }
            } else {
                $settings = $gameModel->game_settings ?? [];
            }

            // Validasi kesiapan game
            $hasError = false;
            $errorMessage = '';
            if (in_array($gameType, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                if (empty($questions)) {
                    $hasError = true;
                    $errorMessage = 'Game ini belum memiliki soal. Tambahkan soal terlebih dahulu.';
                }
            } elseif ($gameType === 'typing') {
                if (empty($settings)) {
                    $hasError = true;
                    $errorMessage = 'Game Typing belum memiliki pengaturan teks.';
                }
            } elseif ($gameType === 'geometry_dash') {
                if (empty($settings)) {
                    $hasError = true;
                    $errorMessage = 'Game Geometry Dash belum memiliki pengaturan level.';
                }
            } elseif (in_array($gameType, ['reaction', 'custom'])) {
                if (empty($settings)) {
                    $hasError = true;
                    $errorMessage = 'Pengaturan game belum lengkap.';
                }
            }

            if ($hasError) {
                return redirect()->route('owner.game.games.show', $gameModel)
                    ->with('error', $errorMessage);
            }

            // Konfigurasi game untuk frontend
            $gameConfig = [
                'game_id' => $gameModel->id,
                'token' => $token,
                'user_id' => $user->id,
                'game_type' => $gameType,
                'game_subtype' => $gameSubtype,
                'anti_cheat' => [
                    'detect_tab_switch' => $gameModel->anti_cheat_detect_tab ?? true,
                    'detect_blur' => $gameModel->anti_cheat_detect_blur ?? true,
                    'detect_disconnect' => $gameModel->anti_cheat_detect_disconnect ?? true,
                    'disable_copy_paste' => $gameModel->anti_cheat_disable_copy_paste ?? true,
                    'block_context_menu' => $gameModel->anti_cheat_block_context_menu ?? true,
                    'on_leave_action' => $gameModel->on_leave_action ?? 'skip',
                    'violation_limit' => $gameModel->cheat_violation_limit ?? 3,
                    'penalty' => $gameModel->cheat_penalty ?? 5,
                ],
                'points' => [
                    'use_points' => $gameModel->use_points ?? true,
                    'correct' => $gameModel->point_correct ?? 10,
                    'wrong' => $gameModel->point_wrong ?? 0,
                    'empty' => $gameModel->point_empty ?? 0,
                    'bonus_complete' => $gameModel->bonus_complete ?? 20,
                    'bonus_streak' => $gameModel->bonus_streak ?? 5,
                    'max_points' => $gameModel->max_points ?? 1000,
                ],
                'timer' => [
                    'mode' => $gameModel->time_mode ?? 'total',
                    'total' => ($gameModel->time_total ?? 10) * 60,
                    'per_question' => $gameModel->time_per_question ?? 15,
                    'timeout_action' => $gameModel->timeout_action ?? 'skip',
                ],
            ];

            $gameData = [
                'settings'  => $settings,
                'questions' => $questions,
                'config'    => $gameConfig,
            ];

            $viewMap = [
                'typing' => 'owner.game-center.games.play-typing',
                'quiz' => 'owner.game-center.games.play-quiz',
                'puzzle' => 'owner.game-center.games.play-puzzle',
                'geometry_dash' => 'owner.game-center.games.play-geometry-dash',
                'coding' => 'owner.game-center.games.play-coding',
                'logic' => 'owner.game-center.games.play-logic',
                'tebak' => 'owner.game-center.games.play-tebak',
                'reaction' => 'owner.game-center.games.play-reaction',
                'custom' => 'owner.game-center.games.play-custom',
            ];

            $view = $viewMap[$gameType] ?? 'owner.game-center.games.play-generic';

            return view($view, [
                'game'       => $gameModel,
                'gameData'   => $gameData,
                'questions'  => $questions,
                'settings'   => $settings,
                'gameConfig' => $gameConfig,
                'user'       => $user->id,
                'userId'     => $user->id,
                'userName'   => $user->name ?? 'Pemain',
                'role'       => $user->level->name ?? 'Guest',
                'token'      => $token,
            ]);

        } catch (\Exception $e) {
            Log::error('Gameplay error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('owner.game.games.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // PLAY TEST (internal)
    // ============================================================
    public function playTest(Request $request, $game = null, $user = null, $role = null, $token = null)
    {
        try {
            if ($game && is_numeric($game)) {
                $gameModel = Game::findOrFail($game);
            } elseif ($request->has('game')) {
                $gameModel = Game::findOrFail($request->game);
            } else {
                return redirect()->route('owner.game.games.index')
                    ->with('error', 'Game tidak ditemukan.');
            }

            $userId = $user ?? auth()->id();
            $userRole = $role ?? (auth()->user()->level->name ?? 'guest');
            $expectedToken = md5($gameModel->id . $userId . 'secret');
            $token = $token ?? $expectedToken;

            if ($gameModel->source_type === 'external') {
                if (empty($gameModel->url)) {
                    return redirect()->route('owner.game.games.show', $gameModel)
                        ->with('error', 'Game ini tidak memiliki URL yang valid.');
                }
                return redirect($gameModel->url);
            }

            if (empty($gameModel->game_type)) {
                return redirect()->route('owner.game.games.show', $gameModel)
                    ->with('error', 'Game ini belum memiliki tipe yang ditentukan.')
                    ->with('error_details', 'Field game_type kosong.');
            }

            $settings = [];
            $questions = [];

            if ($gameModel->game_type === 'typing') {
                $settings = $gameModel->typing_settings ?? [];
            } elseif (in_array($gameModel->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                $settings = $gameModel->game_settings ?? [];
                $rawQuestions = $gameModel->quiz_questions ?? $gameModel->game_questions ?? [];
                $questions = $this->normalizeQuestions($rawQuestions);
                if (empty($questions) && isset($settings['questions'])) {
                    $questions = $this->normalizeQuestions($settings['questions']);
                }
            } else {
                $settings = $gameModel->game_settings ?? [];
            }

            $gameConfig = [
                'game_id' => $gameModel->id,
                'token' => $token,
                'user_id' => $userId,
                'game_type' => $gameModel->game_type,
                'game_subtype' => $settings['quiz_type'] ?? 'multiple_choice',
                'anti_cheat' => [],
                'points' => [
                    'correct' => $settings['point_correct'] ?? 10,
                    'wrong' => $settings['point_wrong'] ?? 0,
                    'empty' => $settings['point_empty'] ?? 0,
                ],
                'timer' => [
                    'mode' => $settings['time_mode'] ?? 'total',
                    'total' => ($settings['time_total'] ?? 10) * 60,
                    'per_question' => $settings['time_per_question'] ?? 15,
                ],
            ];

            $gameData = [
                'settings'  => $settings,
                'questions' => $questions,
                'config'    => $gameConfig,
            ];

            return view('game.play', [
                'game'     => $gameModel,
                'gameData' => $gameData,
                'gameConfig' => $gameConfig,
                'user'     => $userId,
                'role'     => $userRole,
                'token'    => $token,
            ]);

        } catch (\Exception $e) {
            Log::error('PlayTest error: ' . $e->getMessage());
            return redirect()->route('owner.game.games.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // TESTING WITH TOKEN
    // ============================================================
    public function testingWithToken($gameId, $token)
    {
        try {
            $game = Game::findOrFail($gameId);
            $expectedToken = md5($game->id . auth()->id() . 'secret');
            if ($token !== $expectedToken) {
                abort(403, 'Token tidak valid.');
            }

            if ($game->source_type === 'external') {
                if (empty($game->url)) {
                    return redirect()->route('owner.game.games.show', $game)
                        ->with('error', 'Game ini tidak memiliki URL yang valid.');
                }
                return redirect($game->url);
            }

            $settings = [];
            $questions = [];

            if ($game->game_type === 'typing') {
                $settings = $game->typing_settings ?? [];
            } elseif (in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                $settings = $game->game_settings ?? [];
                $rawQuestions = $game->quiz_questions ?? $game->game_questions ?? [];
                $questions = $this->normalizeQuestions($rawQuestions);
                if (empty($questions) && isset($settings['questions'])) {
                    $questions = $this->normalizeQuestions($settings['questions']);
                }
            } else {
                $settings = $game->game_settings ?? [];
            }

            $gameConfig = [
                'game_id' => $game->id,
                'token' => $token,
                'user_id' => auth()->id(),
                'game_type' => $game->game_type,
                'game_subtype' => $settings['quiz_type'] ?? 'multiple_choice',
                'anti_cheat' => [],
                'points' => [],
                'timer' => [],
            ];

            $gameData = [
                'settings'  => $settings,
                'questions' => $questions,
                'config'    => $gameConfig,
            ];

            // Validasi
            if (in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                if (empty($gameData['questions'])) {
                    return redirect()->route('owner.game.games.show', $game)
                        ->with('error', 'Game ini belum memiliki soal.');
                }
            } elseif ($game->game_type === 'typing') {
                if (empty($gameData['settings'])) {
                    return redirect()->route('owner.game.games.show', $game)
                        ->with('error', 'Game Typing belum memiliki pengaturan teks.');
                }
            } elseif ($game->game_type === 'geometry_dash') {
                if (empty($gameData['settings'])) {
                    return redirect()->route('owner.game.games.show', $game)
                        ->with('error', 'Game Geometry Dash belum memiliki pengaturan level.');
                }
            }

            return view('game.play', [
                'game'     => $game,
                'gameData' => $gameData,
                'gameConfig' => $gameConfig,
                'token'    => $token,
            ]);

        } catch (\Exception $e) {
            Log::error('TestingWithToken error: ' . $e->getMessage());
            return redirect()->route('owner.game.games.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // TESTING PAGE (owner)
    // ============================================================
    public function testing(Request $request)
    {
        $gameId = $request->query('game_id');
        $game = null;
        $gameData = null;
        $gameConfig = [];
        $error = null;
        $success = false;

        if ($gameId) {
            try {
                $game = Game::findOrFail($gameId);
                $settings = [];
                $questions = [];

                if ($game->game_type === 'typing') {
                    $settings = $game->typing_settings ?? [];
                } elseif (in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                    $settings = $game->game_settings ?? [];
                    $questions = $game->quiz_questions ?? $game->game_questions ?? [];
                } else {
                    $settings = $game->game_settings ?? [];
                }

                $gameConfig = [
                    'game_id' => $game->id,
                    'token' => '',
                    'user_id' => auth()->id(),
                    'game_type' => $game->game_type,
                    'game_subtype' => $settings['quiz_type'] ?? 'multiple_choice',
                    'anti_cheat' => [],
                    'points' => [],
                    'timer' => [],
                ];

                $gameData = [
                    'settings'  => $settings,
                    'questions' => $questions,
                    'config'    => $gameConfig,
                ];

                // Validasi sederhana
                if (in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
                    if (empty($gameData['questions'])) {
                        $error = 'Game ini tidak memiliki soal. Tambahkan soal terlebih dahulu.';
                        $game = null;
                    } else {
                        $success = true;
                    }
                } elseif ($game->game_type === 'typing') {
                    if (empty($gameData['settings'])) {
                        $error = 'Game Typing belum memiliki pengaturan teks.';
                        $game = null;
                    } else {
                        $success = true;
                    }
                } elseif ($game->game_type === 'geometry_dash') {
                    if (empty($gameData['settings'])) {
                        $error = 'Game Geometry Dash belum memiliki pengaturan level.';
                        $game = null;
                    } else {
                        $success = true;
                    }
                } elseif (in_array($game->game_type, ['reaction', 'custom'])) {
                    if (empty($gameData['settings'])) {
                        $error = 'Pengaturan game belum lengkap.';
                        $game = null;
                    } else {
                        $success = true;
                    }
                } else {
                    $success = true;
                }
            } catch (\Exception $e) {
                $error = 'Terjadi kesalahan: ' . $e->getMessage();
                $game = null;
            }
        }

        $games = Game::orderBy('name')->get();
        return view('owner.game-center.games.testing', compact('games', 'game', 'gameData', 'error', 'success', 'gameConfig'));
    }

    public function loadTest(Request $request)
    {
        return redirect()->route('owner.game.games.testing', ['game_id' => $request->game_id]);
    }

    // ============================================================
    // SUBMIT RESULT (Typing, Reaction, dll)
    // ============================================================
    public function submitResult(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'token'   => 'required|string',
            'result'  => 'required|array',
        ]);

        $game = Game::findOrFail($validated['game_id']);
        $user = auth()->user();

        $expected = md5($game->id . $user->id . 'secret');
        if ($validated['token'] !== $expected) {
            return response()->json(['error' => 'Token tidak valid'], 403);
        }

        $result = $validated['result'];
        $score = $result['score'] ?? $result['wpm'] ?? 0;

        $session = GameSession::create([
            'player_id'   => $user->id,
            'game_id'     => $game->id,
            'score'       => $score,
            'metrics'     => $result,
            'mode'        => $result['mode'] ?? 'practice',
            'result'      => 'completed',
            'started_at'  => now()->subSeconds($result['duration_used'] ?? 60),
            'ended_at'    => now(),
        ]);

        $this->updateLeaderboard($game->id, $user->id, $score, $result);
        $this->updateUserGameStats($user->id, $game->id, $score, $result);

        return response()->json([
            'success'    => true,
            'session_id' => $session->id,
            'redirect'   => route('owner.game.games.result', ['game' => $game->id, 'session' => $session->id]),
        ]);
    }

    // ============================================================
    // SUBMIT QUIZ
    // ============================================================
    public function submitQuiz(Request $request)
    {
        $validated = $request->validate([
            'game_id'   => 'required|exists:games,id',
            'token'     => 'required|string',
            'answers'   => 'required|array',
            'questions' => 'required|array',
        ]);

        $game = Game::findOrFail($validated['game_id']);
        $user = auth()->user();

        $expected = md5($game->id . $user->id . 'secret');
        if ($validated['token'] !== $expected) {
            return response()->json(['error' => 'Token tidak valid'], 403);
        }

        $questions = $validated['questions'];
        if (is_string($questions)) {
            $questions = json_decode($questions, true);
            if (!is_array($questions)) {
                $questions = [];
            }
        }

        $score = 0;
        $correct = 0;
        $total = count($questions);

        foreach ($questions as $idx => $q) {
            $userAnswer = $validated['answers'][$idx] ?? '';
            $correctAnswer = $q['answer'] ?? $q['correct'] ?? '';
            if (strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer))) {
                $correct++;
                $score += 10;
            }
        }

        $session = GameSession::create([
            'player_id' => $user->id,
            'game_id'   => $game->id,
            'score'     => $score,
            'metrics'   => ['correct' => $correct, 'total' => $total, 'answers' => $validated['answers']],
            'mode'      => 'quiz',
            'result'    => 'completed',
            'started_at'=> now(),
            'ended_at'  => now(),
        ]);

        return response()->json([
            'success'    => true,
            'score'      => $score,
            'correct'    => $correct,
            'total'      => $total,
            'redirect'   => route('owner.game.games.result', ['game' => $game->id, 'session' => $session->id]),
        ]);
    }

    // ============================================================
    // SHOW RESULT
    // ============================================================
    public function showResult($gameId, $sessionId)
    {
        $game = Game::findOrFail($gameId);
        $session = GameSession::with('player')->findOrFail($sessionId);

        if ($session->player_id !== auth()->id()) {
            abort(403);
        }

        $metrics = $session->metrics ?? [];
        $leaderboard = GameSession::where('game_id', $gameId)
            ->whereNotNull('ended_at')
            ->with('player')
            ->orderBy('score', 'desc')
            ->limit(20)
            ->get();

        $rank = $leaderboard->where('score', '>', $session->score)->count() + 1;
        $totalPlayers = GameSession::where('game_id', $gameId)->whereNotNull('ended_at')->count();
        $rhythmData = $this->calculateRhythm($metrics);

        return view('game.result', compact(
            'game', 'session', 'metrics', 'leaderboard', 'rank', 'totalPlayers', 'rhythmData'
        ));
    }

    // ============================================================
    // LEADERBOARD HELPERS
    // ============================================================
    private function updateLeaderboard($gameId, $playerId, $score, $result)
    {
        $periods = ['global', 'weekly', 'monthly'];
        foreach ($periods as $period) {
            $this->updateOrCreateLeaderboard($gameId, $playerId, $score, $period, $result);
        }
    }

    private function updateOrCreateLeaderboard($gameId, $playerId, $score, $period, $result)
    {
        $leaderboard = Leaderboard::firstOrCreate(
            ['game_id' => $gameId, 'period' => $period],
            ['calculated_at' => now()]
        );

        $entry = LeaderboardEntry::where('leaderboard_id', $leaderboard->id)
            ->where('player_id', $playerId)
            ->first();

        if ($entry) {
            if ($score > $entry->score) {
                $entry->update(['score' => $score]);
            }
        } else {
            LeaderboardEntry::create([
                'leaderboard_id' => $leaderboard->id,
                'player_id' => $playerId,
                'rank' => 0,
                'score' => $score,
            ]);
        }

        $this->rerankLeaderboard($leaderboard);
    }

    private function rerankLeaderboard($leaderboard)
    {
        $entries = LeaderboardEntry::where('leaderboard_id', $leaderboard->id)
            ->orderBy('score', 'desc')
            ->get();

        $rank = 1;
        foreach ($entries as $entry) {
            $entry->update(['rank' => $rank++]);
        }
    }

    // ============================================================
    // USER GAME STATS
    // ============================================================
    private function updateUserGameStats($userId, $gameId, $score, $result)
    {
        $stats = UserGameStat::firstOrCreate(
            ['user_id' => $userId, 'game_id' => $gameId],
            ['total_plays' => 0, 'total_score' => 0, 'best_score' => 0, 'avg_score' => 0]
        );

        $stats->total_plays += 1;
        $stats->total_score += $score;
        $stats->best_score = max($stats->best_score, $score);
        $stats->avg_score = round($stats->total_score / $stats->total_plays, 2);
        $stats->last_metrics = $result;
        $stats->last_played_at = now();
        $stats->save();

        $game = Game::find($gameId);
        if ($game) {
            $game->increment('player_count');
        }
    }

    // ============================================================
    // RHYTHM CALCULATION (untuk typing)
    // ============================================================
    private function calculateRhythm($metrics)
    {
        $slowKeys = $metrics['slow_keys'] ?? [];
        $keyboardMap = $metrics['keyboard_map'] ?? [];

        $totalKeys = array_sum($keyboardMap);
        $totalSlow = count($slowKeys);

        $consistency = 100;
        if ($totalKeys > 0 && $totalSlow > 0) {
            $consistency = max(0, 100 - round(($totalSlow / $totalKeys) * 100));
        }

        $consistencyLabel = $consistency >= 80 ? 'Sangat Rata' : ($consistency >= 60 ? 'Cukup Rata' : 'Kurang Rata');

        $slowKeysSorted = collect($slowKeys)
            ->map(function ($delay, $key) {
                return ['key' => $key, 'delay' => $delay];
            })
            ->sortByDesc('delay')
            ->take(10)
            ->values()
            ->toArray();

        $latePairs = $this->findLatePairs($slowKeysSorted);

        return [
            'consistency' => $consistency,
            'consistency_label' => $consistencyLabel,
            'slow_keys' => $slowKeysSorted,
            'late_pairs' => $latePairs,
            'total_keys' => $totalKeys,
            'total_slow' => $totalSlow,
        ];
    }

    private function findLatePairs($slowKeys)
    {
        $pairs = [];
        $keys = array_column($slowKeys, 'key');
        for ($i = 0; $i < count($keys) - 1; $i++) {
            if (abs(ord($keys[$i]) - ord($keys[$i+1])) <= 2) {
                $pairs[] = [
                    'pair' => $keys[$i] . $keys[$i+1],
                    'delay' => $slowKeys[$i]['delay'] + $slowKeys[$i+1]['delay']
                ];
            }
        }
        return collect($pairs)->sortByDesc('delay')->take(5)->values()->toArray();
    }

    // ============================================================
    // DASHBOARD
    // ============================================================
    public function dashboard()
    {
        $totalGames     = Game::count();
        $totalNative    = Game::where('source_type', 'native')->count();
        $totalExternal  = Game::where('source_type', 'external')->count();
        $totalPublished = Game::where('status', 'published')->count();

        $totalPlayers  = User::count();
        $activePlayers = User::where('is_active', true)->count();

        $totalMatches   = GameSession::count();
        $finishedMatches = GameSession::whereNotNull('ended_at')->count();
        $matchesToday   = GameSession::whereDate('created_at', today())->count();

        $totalChallenges = 0;
        $activeChallenges = 0;
        if (class_exists(Challenge::class)) {
            try {
                $totalChallenges = Challenge::count();
                if (Schema::hasColumn('challenges', 'status')) {
                    $activeChallenges = Challenge::where('status', 'active')->count();
                }
            } catch (\Exception $e) {}
        }

        $totalCompetitions = 0;
        $activeCompetitions = 0;
        if (class_exists(Competition::class)) {
            try {
                $totalCompetitions = Competition::count();
                if (Schema::hasColumn('competitions', 'status')) {
                    $activeCompetitions = Competition::where('status', 'active')->count();
                }
            } catch (\Exception $e) {}
        }

        $totalReward = 0;
        if (class_exists(Reward::class)) {
            try {
                $totalReward = Reward::sum('amount') ?? 0;
            } catch (\Exception $e) {}
        }
        $totalRewardFormatted = 'Rp ' . number_format($totalReward, 0, ',', '.');

        $matchmakingStatus = 'Aktif';
        $matchmakingColor  = 'emerald';
        $serverStatus      = 'Online';
        $serverColor       = 'cyan';

        $averageWinRate = 68.5;
        $averageWinRateFormatted = $averageWinRate . '%';
        $topPlayerName  = 'John Doe';
        $topPlayerRating = '2450';
        $topPlayerRank  = 'Grandmaster';

        $popularGames = Game::where('status', 'published')
            ->orderBy('player_count', 'desc')
            ->limit(4)
            ->get(['id', 'name', 'icon', 'status', 'player_count']);
        foreach ($popularGames as $game) {
            $game->sessions_count = GameSession::where('game_id', $game->id)->count();
        }

        $recentActivities = collect([
            (object) ['player' => 'Andi', 'action' => 'menyelesaikan challenge "Typing Speed"', 'time' => '2 menit lalu'],
            (object) ['player' => 'Budi', 'action' => 'bergabung ke kompetisi "Coding Battle"', 'time' => '5 menit lalu'],
            (object) ['player' => 'Citra', 'action' => 'mencapai rank Gold', 'time' => '10 menit lalu'],
            (object) ['player' => 'Dewi', 'action' => 'memulai game "Quiz Unicorn"', 'time' => '15 menit lalu'],
        ]);

        return view('owner.game-center.dashboard', compact(
            'totalGames', 'totalNative', 'totalExternal', 'totalPublished',
            'totalPlayers', 'activePlayers', 'totalMatches', 'finishedMatches', 'matchesToday',
            'totalChallenges', 'activeChallenges', 'totalCompetitions', 'activeCompetitions',
            'totalRewardFormatted', 'matchmakingStatus', 'matchmakingColor', 'serverStatus',
            'serverColor', 'averageWinRate', 'averageWinRateFormatted', 'topPlayerName',
            'topPlayerRating', 'topPlayerRank', 'popularGames', 'recentActivities'
        ));
    }

    // ============================================================
    // DIAGNOSTIC
    // ============================================================
    public function diagnosticPreview(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'game_type' => 'required',
        ]);

        $gameType = $request->input('game_type');
        $hasSettings = false;
        $hasQuestions = false;
        $errors = [];
        $recommendations = [];

        if ($gameType === 'typing') {
            $settings = $request->input('typing_settings');
            $hasSettings = !empty($settings);
            if (!$hasSettings) $errors[] = 'Pengaturan typing belum diisi.';
            $hasQuestions = true;
        } elseif (in_array($gameType, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
            $settings = $request->input($gameType.'_settings');
            $hasSettings = !empty($settings);
            if (!$hasSettings) $errors[] = 'Pengaturan '.$gameType.' belum diisi.';
            $questions = $request->input($gameType.'_questions');
            $hasQuestions = !empty($questions);
            if (!$hasQuestions) $errors[] = 'Belum ada soal untuk game ini.';
        } elseif ($gameType === 'geometry_dash') {
            $settings = $request->input('geometry_dash_settings');
            $hasSettings = !empty($settings);
            if (!$hasSettings) $errors[] = 'Pengaturan Geometry Dash belum diisi.';
            $hasQuestions = true;
        } else {
            $hasSettings = true;
            $hasQuestions = true;
        }

        $status = 'passed';
        if (!empty($errors)) {
            if ($hasSettings && $hasQuestions) {
                $status = 'warning';
                $recommendations = $errors;
            } else {
                $status = 'failed';
            }
        }

        return response()->json([
            'status' => $status,
            'errors' => $errors,
            'recommendations' => $recommendations,
        ]);
    }

    public function diagnostic(Game $game, $silent = false)
    {
        if ($game->source_type !== 'native') {
            return $silent ? null : redirect()->route('owner.game.games.index')
                ->with('error', 'Diagnostic hanya untuk game native.');
        }

        $checks = [];
        $errors = [];
        $warnings = [];
        $suggestions = [];

        // Cek 1: Game type
        if (empty($game->game_type)) {
            $errors[] = 'Game type belum diisi.';
            $checks[] = ['name' => 'Game Type', 'status' => 'failed', 'message' => 'Game type kosong'];
        } else {
            $checks[] = ['name' => 'Game Type', 'status' => 'passed', 'message' => 'Game type: ' . $game->game_type];
        }

        // Cek 2: Settings
        if ($game->game_type === 'typing') {
            if (empty($game->typing_settings)) {
                $errors[] = 'Typing settings belum diisi.';
                $checks[] = ['name' => 'Typing Settings', 'status' => 'failed'];
            } else {
                $checks[] = ['name' => 'Typing Settings', 'status' => 'passed'];
            }
        } elseif (in_array($game->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle'])) {
            if (empty($game->game_settings)) {
                $errors[] = 'Game settings belum diisi.';
                $checks[] = ['name' => 'Game Settings', 'status' => 'failed'];
            } else {
                $checks[] = ['name' => 'Game Settings', 'status' => 'passed'];
            }
            if (empty($game->quiz_questions)) {
                $errors[] = 'Soal belum ada.';
                $checks[] = ['name' => 'Questions', 'status' => 'failed'];
            } else {
                $checks[] = ['name' => 'Questions', 'status' => 'passed', 'message' => count($game->quiz_questions) . ' soal'];
            }
        } elseif ($game->game_type === 'geometry_dash') {
            if (empty($game->game_settings)) {
                $errors[] = 'Geometry Dash settings belum diisi.';
                $checks[] = ['name' => 'Geometry Dash Settings', 'status' => 'failed'];
            } else {
                $checks[] = ['name' => 'Geometry Dash Settings', 'status' => 'passed'];
            }
        }

        // Cek 3: Status
        if ($game->status === 'draft') {
            $warnings[] = 'Game masih draft.';
            $checks[] = ['name' => 'Status', 'status' => 'warning', 'message' => 'Draft'];
        } else {
            $checks[] = ['name' => 'Status', 'status' => 'passed', 'message' => $game->status];
        }

        // Cek 4: Cover
        if (empty($game->cover_images)) {
            $warnings[] = 'Belum ada cover image.';
            $checks[] = ['name' => 'Cover', 'status' => 'warning'];
        } else {
            $checks[] = ['name' => 'Cover', 'status' => 'passed', 'message' => count($game->cover_images) . ' gambar'];
        }

        // Tentukan status keseluruhan
        $overallStatus = 'passed';
        if (!empty($errors)) {
            $overallStatus = 'failed';
        } elseif (!empty($warnings)) {
            $overallStatus = 'warning';
        }

        $game->diagnostic_status = $overallStatus;
        $game->diagnostic_last_run = now();
        $game->save();

        $rawData = [
            'game' => $game->toArray(),
            'checks' => $checks,
            'errors' => $errors,
            'warnings' => $warnings,
            'suggestions' => $suggestions,
            'summary' => [
                'total_checks' => count($checks),
                'passed' => collect($checks)->where('status', 'passed')->count(),
                'warnings' => collect($checks)->where('status', 'warning')->count(),
                'failed' => collect($checks)->where('status', 'failed')->count(),
            ],
            'overall_status' => $overallStatus,
        ];

        if ($silent) {
            return $rawData;
        }

        return view('owner.game-center.games.diagnostic', compact(
            'game', 'checks', 'errors', 'warnings', 'suggestions', 'summary', 'overallStatus', 'rawData'
        ));
    }

    public function fixDiagnostic(Game $game)
    {
        $fixed = 0;
        $messages = [];

        if ($game->game_type === 'typing' && empty($game->typing_settings)) {
            $game->typing_settings = [
                'custom_text' => 'Mengetik adalah keterampilan penting di era digital. Latihan mengetik 10 jari akan membantu Anda mengetik lebih cepat dan akurat. Teruslah berlatih setiap hari untuk hasil terbaik.',
                'duration' => 60,
                'max_players' => 1,
                'difficulty' => 'normal',
                'allow_solo' => true,
                'allow_race' => false,
                'allow_private_room' => false,
            ];
            $fixed++;
            $messages[] = '✅ Typing settings diisi default.';
        }

        if ($game->game_type === 'typing' && !empty($game->typing_settings) && empty($game->typing_settings['custom_text'])) {
            $game->typing_settings['custom_text'] = 'Ini adalah teks default untuk latihan mengetik. Silakan isi pengaturan game dengan teks yang lebih relevan.';
            $fixed++;
            $messages[] = '✅ custom_text diisi default.';
        }

        if ($game->game_type === 'typing' && !empty($game->typing_settings) && (!isset($game->typing_settings['duration']) || $game->typing_settings['duration'] < 10)) {
            $game->typing_settings['duration'] = 60;
            $fixed++;
            $messages[] = '✅ Durasi diset ke 60 detik.';
        }

        if ($game->game_type === 'typing' && !empty($game->typing_settings) && (!isset($game->typing_settings['max_players']) || $game->typing_settings['max_players'] < 1)) {
            $game->typing_settings['max_players'] = 1;
            $fixed++;
            $messages[] = '✅ max_players diset ke 1.';
        }

        // Fix quiz tanpa soal
        if ($game->game_type === 'quiz' && empty($game->quiz_questions)) {
            $game->quiz_questions = [
                [
                    'question' => 'Apa ibu kota Indonesia?',
                    'type' => 'multiple_choice',
                    'options' => [
                        ['label' => 'A', 'text' => 'Bandung', 'correct' => false],
                        ['label' => 'B', 'text' => 'Jakarta', 'correct' => true],
                        ['label' => 'C', 'text' => 'Surabaya', 'correct' => false],
                        ['label' => 'D', 'text' => 'Medan', 'correct' => false],
                    ],
                    'weight' => 10,
                    'difficulty' => 'easy',
                ]
            ];
            $fixed++;
            $messages[] = '✅ Ditambahkan soal default untuk quiz.';
        }

        $game->save();

        if ($fixed === 0) {
            return redirect()->route('owner.game.games.diagnostic', $game)
                ->with('warning', 'Tidak ada masalah yang bisa diperbaiki otomatis.');
        }

        return redirect()->route('owner.game.games.diagnostic', $game)
            ->with('success', 'Perbaikan otomatis berhasil! (' . $fixed . ' perubahan)')
            ->with('messages', $messages);
    }
}
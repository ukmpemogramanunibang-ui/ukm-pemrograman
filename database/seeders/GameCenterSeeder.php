<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Player;
use App\Models\GameMatch;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\GameSetting;
use Illuminate\Support\Facades\Hash;

class GameCenterSeeder extends Seeder
{
    public function run()
    {
        // ============================================================
        // 1. GAMES – gunakan firstOrCreate agar tidak duplikat
        // ============================================================
        $games = [
            ['name' => 'Typing Arena', 'slug' => 'typing-arena', 'icon' => '⌨️', 'status' => 'published', 'version' => '2.4.1'],
            ['name' => 'Code Sprint', 'slug' => 'code-sprint', 'icon' => '💻', 'status' => 'published', 'version' => '1.8.0'],
            ['name' => 'Debug Master', 'slug' => 'debug-master', 'icon' => '🐞', 'status' => 'published', 'version' => '1.4.5'],
            ['name' => 'Logic Battle', 'slug' => 'logic-battle', 'icon' => '🧠', 'status' => 'maintenance', 'version' => '3.1.2'],
        ];
        foreach ($games as $g) {
            Game::firstOrCreate(['slug' => $g['slug']], $g);
        }

        // ============================================================
        // 2. PLAYERS – gunakan firstOrCreate berdasarkan email
        // ============================================================
        $players = [
            ['name' => 'Muhammad Ibni', 'nim' => '12345', 'email' => 'ibni@example.com', 'password' => Hash::make('password'), 'rank' => 'Diamond', 'rating' => 2984, 'xp' => 182000, 'win_rate' => 92, 'wins' => 150, 'losses' => 12],
            ['name' => 'Rizky Pratama', 'nim' => '12346', 'email' => 'rizky@example.com', 'password' => Hash::make('password'), 'rank' => 'Platinum', 'rating' => 2841, 'xp' => 167000, 'win_rate' => 89, 'wins' => 130, 'losses' => 16],
            ['name' => 'Alya Putri', 'nim' => '12347', 'email' => 'alya@example.com', 'password' => Hash::make('password'), 'rank' => 'Platinum', 'rating' => 2745, 'xp' => 158000, 'win_rate' => 86, 'wins' => 118, 'losses' => 19],
            ['name' => 'Fajar Ramadhan', 'nim' => '12348', 'email' => 'fajar@example.com', 'password' => Hash::make('password'), 'rank' => 'Gold', 'rating' => 2612, 'xp' => 149000, 'win_rate' => 82, 'wins' => 105, 'losses' => 23],
        ];
        foreach ($players as $p) {
            Player::firstOrCreate(['email' => $p['email']], $p);
        }

        // ============================================================
        // 3. GAME MATCHES – hapus semua data lama, lalu buat baru
        //    (agar tidak menumpuk setiap kali seeder dijalankan)
        // ============================================================
        GameMatch::truncate();
        $matchStatuses = ['finished', 'finished', 'finished', 'active', 'waiting'];
        for ($i = 0; $i < 10; $i++) {
            GameMatch::create([
                'game_id'     => rand(1, 4),
                'player1_id'  => rand(1, 4),
                'player2_id'  => rand(1, 4) == 1 ? null : rand(1, 4),
                'status'      => $matchStatuses[array_rand($matchStatuses)],
                'winner_id'   => rand(1, 4),
                'score_player1' => rand(50, 100),
                'score_player2' => rand(50, 100),
                'started_at'  => now()->subMinutes(rand(10, 120)),
                'ended_at'    => now()->subMinutes(rand(1, 10)),
                'created_at'  => now()->subDays(rand(0, 3)),
            ]);
        }

        // ============================================================
        // 4. CHALLENGES – firstOrCreate berdasarkan title + game_id
        // ============================================================
        Challenge::firstOrCreate(
            ['title' => '10 Finger Speed Rush', 'game_id' => 1],
            [
                'description'      => 'Latihan mengetik 10 jari dengan kecepatan tinggi',
                'difficulty'       => 'expert',
                'type'             => 'daily',
                'duration_minutes' => 5,
                'xp_reward'        => 500,
                'is_active'        => true,
                'starts_at'        => now(),
                'ends_at'          => now()->addDay(),
            ]
        );

        // ============================================================
        // 5. COMPETITIONS – firstOrCreate berdasarkan name
        // ============================================================
        Competition::firstOrCreate(
            ['name' => 'UNICODE Typing Championship'],
            [
                'description'          => 'Kejuaraan mengetik tingkat universitas',
                'game_id'              => 1,
                'status'               => 'live',
                'max_participants'     => 32,
                'current_participants' => 32,
                'start_date'           => now(),
                'end_date'             => now()->addDays(3),
                'prize_pool'           => 5000000,
            ]
        );

        // ============================================================
        // 6. SETTINGS – firstOrCreate berdasarkan key
        // ============================================================
        $settings = [
            ['key' => 'game_center_name', 'value' => 'UNICODE Game Center', 'group' => 'general', 'description' => 'Nama Game Center'],
            ['key' => 'max_matchmaking', 'value' => '100', 'group' => 'matchmaking'],
            ['key' => 'default_match_duration', 'value' => '10', 'group' => 'matchmaking'],
            ['key' => 'xp_per_win', 'value' => '100', 'group' => 'economy'],
            ['key' => 'xp_per_challenge', 'value' => '50', 'group' => 'economy'],
            ['key' => 'coin_per_win', 'value' => '25', 'group' => 'economy'],
            ['key' => 'streak_bonus', 'value' => '10', 'group' => 'economy'],
        ];
        foreach ($settings as $s) {
            GameSetting::firstOrCreate(['key' => $s['key']], $s);
        }

        $this->command->info('✅ Game Center seeded successfully! (No duplicates)');
    }
}
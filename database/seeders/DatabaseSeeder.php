<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cache permission agar role & permission selalu sinkron
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Jalankan seluruh seeder sesuai urutan dependency
        $this->call([
    PermissionSeeder::class,
    RoleSeeder::class,
    LevelStatusSeeder::class,   // ganti dari LevelSeeder dan StatusSeeder
    UniversitySeeder::class,
    MajorSeeder::class,
    OrganizationalPeriodSeeder::class,
    JabatanSeeder::class,
    UserSeeder::class,
    AttendancePenaltyRuleSeeder::class,
    GameCenterSeeder::class,
]);
$games = [
        ['name' => 'Typing Arena', 'slug' => 'typing-arena', 'icon' => '⌨️', 'status' => 'published', 'version' => '2.4.1'],
        ['name' => 'Code Sprint', 'slug' => 'code-sprint', 'icon' => '💻', 'status' => 'published', 'version' => '1.8.0'],
        // ...
    ];
    foreach ($games as $g) {
        Game::create($g);
    }

    // Buat pemain contoh
    $players = [
        ['name' => 'Muhammad Ibni', 'nim' => '12345', 'email' => 'ibni@example.com', 'password' => bcrypt('password'), 'rank' => 'Diamond', 'rating' => 2984, 'xp' => 182000, 'win_rate' => 92],
        // ...
    ];
    foreach ($players as $p) {
        Player::create($p);
    }

    // Buat challenge
    Challenge::create([
        'game_id' => 1,
        'title' => '10 Finger Speed Rush',
        'description' => 'Latihan mengetik 10 jari',
        'difficulty' => 'expert',
        'type' => 'daily',
        'duration_minutes' => 5,
        'xp_reward' => 500,
    ]);

    // Buat kompetisi
    Competition::create([
        'name' => 'UNICODE Typing Championship',
        'description' => 'Kejuaraan mengetik tingkat universitas',
        'game_id' => 1,
        'status' => 'live',
        'max_participants' => 32,
        'current_participants' => 32,
        'start_date' => now(),
        'end_date' => now()->addDays(3),
        'prize_pool' => 5000000,
    ]);
            // UserQrSeeder::class,  // sudah ditangani di UserSeeder
    // DemoSeeder::class,     // belum diperlukan
       
    }
}
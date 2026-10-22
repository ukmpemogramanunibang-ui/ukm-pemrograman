<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 *  DatabaseSeeder — Orchestrator Utama
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *  File ini HANYA memanggil seeder lain dalam urutan dependency yang benar.
 *  TIDAK BOLEH berisi logika seeding langsung (create/insert/update).
 *
 *  ─── Aturan Emas ────────────────────────────────────────────────────────
 *
 *  1. Setiap seeder HARUS idempoten (pakai updateOrCreate / firstOrCreate).
 *  2. Setiap seeder HARUS menangani dependency-nya sendiri (jangan asumsi).
 *  3. Urutan LEVEL 1 → 2 → 3 → 4 TIDAK BOLEH diacak.
 *  4. Untuk domain baru (game center, finance, LMS), buat seeder terpisah.
 *
 *  ─── Kalau Butuh Seeder Game/Player/Challenge/Competition ───────────────
 *
 *  Buat file baru: php artisan make:seeder GameCenterSeeder
 *
 *  Lalu di dalam GameCenterSeeder::run() gunakan pola:
 *
 *      use App\Models\Game;
 *      use App\Models\Player;
 *      use App\Models\Challenge;
 *      use App\Models\Competition;
 *
 *      Game::updateOrCreate(
 *          ['slug' => 'typing-arena'],
 *          ['name' => 'Typing Arena', 'icon' => '⌨️', 'status' => 'published']
 *      );
 *
 *      // dst — WAJIB pakai updateOrCreate, JANGAN create()
 *      //      WAJIB resolve FK via slug, JANGAN hardcode id
 *
 *  Setelah itu daftarkan di $this->call() di bawah, SETELAH LEVEL 4.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Reset cache permission agar role & permission selalu sinkron ───
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Jalankan seluruh seeder sesuai urutan dependency ───
        $this->call([
            // ══ LEVEL 1: master data dasar (tidak bergantung apa pun) ══
            LevelStatusSeeder::class,      // → tabel: levels, statuses
            StatusSeeder::class,           // → tabel: statuses (detail)
            PermissionSeeder::class,       // → tabel: permissions (Spatie)
            RoleSeeder::class,             // → tabel: roles (Spatie)
            UniversitySeeder::class,       // → tabel: universities
            MajorSeeder::class,            // → tabel: majors
            PositionSeeder::class,         // → tabel: positions

            // ══ LEVEL 2: organisasi (butuh level/status) ══
            OrganizationsSeeder::class,    // → tabel: organizations

            // ══ LEVEL 3: user (butuh level/status/org) ══
            UserSeeder::class,             // → tabel: users
            JabatanSeeder::class,          // → tabel: jabatans, jabatan_user

            // ══ LEVEL 4: data operasional (butuh user & org) ══
            OrganizationalPeriodSeeder::class,  // → tabel: organizational_periods
            UserQrCodeSeeder::class,            // → tabel: user_qr_codes

            // ══ LEVEL 5: domain seeder (aktifkan jika perlu) ══
            // GameCenterSeeder::class,
            // FinanceSeeder::class,
            // LmsSeeder::class,
        ]);
    }
}
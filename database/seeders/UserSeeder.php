<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🛡️ Guard: command bisa null saat dipanggil dari test / db:seed programatik
        $canLog = $this->command !== null;

        // ═══════════════════════════════════════════════════════════════
        // 1. Validasi dependensi (level & status)
        // ═══════════════════════════════════════════════════════════════
        $requiredLevels = ['owner', 'superadmin', 'ketua', 'pengurus', 'anggota', 'guest'];

        $levelMap = Level::whereIn('name', $requiredLevels)
            ->get()
            ->keyBy('name');

        $missingLevels = array_diff($requiredLevels, $levelMap->keys()->all());
        if (! empty($missingLevels)) {
            throw new \RuntimeException(
                'Level tidak lengkap. Missing: ' . implode(', ', $missingLevels)
                . '. Jalankan LevelStatusSeeder terlebih dahulu.'
            );
        }

        $statusAktif = Status::where('name', 'active')->first();
        if (! $statusAktif) {
            throw new \RuntimeException(
                'Status "active" tidak ditemukan. Jalankan StatusSeeder terlebih dahulu.'
            );
        }

        // ═══════════════════════════════════════════════════════════════
        // 2. Definisi data user
        // ═══════════════════════════════════════════════════════════════
        $defaultPassword = 'password';
        $ownerPinHash    = Hash::make('123456');

        $users = [
            [
                'email'         => 'owner@ukm.ac.id',
                'nim'           => '0000000001',
                'name'          => 'Owner UKM',
                'level'         => 'owner',
                'pin'           => $ownerPinHash,
                'member_number' => 'UKM-OWNER-0001',
            ],
            [
                'email'         => 'superadmin@unicode.test',
                'nim'           => '0000000002',
                'name'          => 'Superadmin',
                'level'         => 'superadmin',
                'member_number' => 'UKM-SUPER-0001',
            ],
            [
                'email'         => 'ketua@unicode.test',
                'nim'           => '0000000003',
                'name'          => 'Ketua UKM',
                'level'         => 'ketua',
                'member_number' => 'UKM-KETUA-0001',
            ],
            [
                'email'         => 'bendahara@unicode.test',
                'nim'           => '0000000004',
                'name'          => 'Bendahara UKM',
                'level'         => 'pengurus',
                'member_number' => 'UKM-PENG-0001',
            ],
            [
                'email'         => 'bima@unicode.test',
                'nim'           => '0000000005',
                'name'          => 'Bima',
                'level'         => 'anggota',
                'member_number' => 'UKM-ANG-0001',
            ],
            [
                'email'         => 'nisa@unicode.test',
                'nim'           => '0000000006',
                'name'          => 'Nisa',
                'level'         => 'anggota',
                'member_number' => 'UKM-ANG-0002',
            ],
            [
                'email'         => 'umum@unicode.test',
                'nim'           => '0000000007',
                'name'          => 'Anggota Umum',
                'level'         => 'anggota',
                'member_number' => 'UKM-UMUM-0001',
            ],
            [
                'email'         => 'pengunjung@unicode.test',
                'nim'           => null,
                'name'          => 'Pengunjung',
                'level'         => 'guest',
                'member_number' => 'UKM-GST-0001',
                'account_type'  => 'guest',
            ],
        ];

        // ═══════════════════════════════════════════════════════════════
        // 3. Loop: buat user baru, preservasi yang sudah ada
        // ═══════════════════════════════════════════════════════════════
        $created   = 0;
        $preserved = 0;
        $skipped   = 0;

        foreach ($users as $data) {
            $level = $levelMap->get($data['level']);

            if (! $level) {
                $skipped++;
                if ($canLog) {
                    $this->command->warn(
                        "⚠️  Level '{$data['level']}' tidak ditemukan, skip: {$data['email']}"
                    );
                }
                continue;
            }

            $existing = User::where('email', $data['email'])->first();

            if ($existing) {
                $preserved++;
                if ($canLog) {
                    $this->command->line("⏭️  Preserved (existing): {$data['email']}");
                }
                continue;
            }

            User::create([
                'user_uuid'         => (string) Str::uuid(),
                'email'             => $data['email'],           // ✅ WAJIB: untuk idempotensi
                'email_verified_at' => now(),                    // ✅ WAJIB: konsisten dgn Laravel
                'nim'               => $data['nim'],
                'name'              => $data['name'],
                'password'          => Hash::make($defaultPassword),
                'pin'               => $data['pin'] ?? null,
                'level_id'          => $level->id,
                'status_id'         => $statusAktif->id,
                'member_number'     => $data['member_number'],
                'account_type'      => $data['account_type'] ?? 'official',
                'is_verified'       => true,
                'verified_at'       => now(),
                'is_active'         => true,
            ]);

            $created++;
            if ($canLog) {
                $this->command->info("✅ Created: {$data['email']}");
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // 4. Ringkasan (hanya jika ada command)
        // ═══════════════════════════════════════════════════════════════
        if ($canLog) {
            $this->command->newLine();
            $this->command->info(
                "📊 Ringkasan: {$created} dibuat, {$preserved} dipertahankan, {$skipped} di-skip."
            );
            $this->command->newLine();
            $this->command->line('📧 Kredensial default (password: "password"):');
            $this->command->line('   Owner      : owner@ukm.ac.id        (PIN: 123456)');
            $this->command->line('   Superadmin : superadmin@unicode.test');
            $this->command->line('   Ketua      : ketua@unicode.test');
            $this->command->line('   Bendahara  : bendahara@unicode.test');
            $this->command->line('   Bima       : bima@unicode.test');
            $this->command->line('   Nisa       : nisa@unicode.test');
            $this->command->line('   Umum       : umum@unicode.test');
            $this->command->line('   Pengunjung : pengunjung@unicode.test');
        }
    }
}
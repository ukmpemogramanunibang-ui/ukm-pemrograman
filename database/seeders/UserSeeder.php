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
        // Ambil level berdasarkan ID agar lebih stabil
        $levelMap = Level::whereIn('name', ['owner', 'superadmin', 'ketua', 'pengurus', 'anggota', 'guest'])
            ->get()
            ->keyBy('name');

        if ($levelMap->count() < 6) {
            throw new \Exception('Level tidak lengkap. Jalankan LevelStatusSeeder terlebih dahulu.');
        }

        $statusAktif = Status::where('name', 'active')->first();
        if (!$statusAktif) {
            throw new \Exception('Status "active" tidak ditemukan.');
        }

        $defaultPassword = 'password';
        $ownerPin = '123456';

        $users = [
            [
                'email' => 'owner@ukm.ac.id',
                'nim' => '0000000001',
                'name' => 'Owner UKM',
                'level' => 'owner',
                'pin' => Hash::make($ownerPin),
                'member_number' => 'UKM-OWNER-0001',
            ],
            [
                'email' => 'superadmin@unicode.test',
                'nim' => '0000000002',
                'name' => 'Superadmin',
                'level' => 'superadmin',
                'member_number' => 'UKM-SUPER-0001',
            ],
            [
                'email' => 'ketua@unicode.test',
                'nim' => '0000000003',
                'name' => 'Ketua UKM',
                'level' => 'ketua',
                'member_number' => 'UKM-KETUA-0001',
            ],
            [
                'email' => 'bendahara@unicode.test',
                'nim' => '0000000004',
                'name' => 'Bendahara UKM',
                'level' => 'pengurus',
                'member_number' => 'UKM-PENG-0001',
            ],
            [
                'email' => 'bima@unicode.test',
                'nim' => '0000000005',
                'name' => 'Bima',
                'level' => 'anggota',
                'member_number' => 'UKM-ANG-0001',
            ],
            [
                'email' => 'nisa@unicode.test',
                'nim' => '0000000006',
                'name' => 'Nisa',
                'level' => 'anggota',
                'member_number' => 'UKM-ANG-0002',
            ],
            [
                'email' => 'umum@unicode.test',
                'nim' => '0000000007',
                'name' => 'Anggota Umum',
                'level' => 'anggota',
                'member_number' => 'UKM-UMUM-0001',
            ],
            [
                'email' => 'pengunjung@unicode.test',
                'nim' => null,
                'name' => 'Pengunjung',
                'level' => 'guest',
                'member_number' => 'UKM-GST-0001',
                'account_type' => 'guest',
            ],
        ];

        foreach ($users as $data) {
    $levelId = $levelMap[$data['level']]->id ?? null;
    if (!$levelId) continue;

    $existing = User::where('email', $data['email'])->first();

    if (!$existing) {
        // Buat baru
        User::create([
            'user_uuid' => (string) Str::uuid(),
            'nim' => $data['nim'],
            'name' => $data['name'],
            'password' => Hash::make($defaultPassword),
            'pin' => $data['pin'] ?? null,
            'level_id' => $levelId,
            'status_id' => $statusAktif->id,
            'member_number' => $data['member_number'],
            'account_type' => $data['account_type'] ?? 'official',
            'is_verified' => true,
            'verified_at' => now(),
            'is_active' => true,
        ]);
        $this->command->info("✅ Created system user: {$data['email']}");
    } else {
        // Hanya update jika ada field yang wajib (misal level_id berubah)
        // JANGAN reset password, pin, atau data lain yang sudah diubah user.
        $this->command->line("⏭️ Existing user preserved: {$data['email']}");
    }
}

        $this->command->info('✅ User testing berhasil dibuat.');
        $this->command->line('📧 Owner: owner@ukm.ac.id / password (PIN: 123456)');
        $this->command->line('📧 Superadmin: superadmin@unicode.test / password');
        $this->command->line('📧 Ketua: ketua@unicode.test / password');
        $this->command->line('📧 Bendahara: bendahara@unicode.test / password');
        $this->command->line('📧 Bima: bima@unicode.test / password');
        $this->command->line('📧 Nisa: nisa@unicode.test / password');
        $this->command->line('📧 Anggota Umum: umum@unicode.test / password');
        $this->command->line('📧 Pengunjung: pengunjung@unicode.test / password');
    }
}
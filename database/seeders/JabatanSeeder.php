<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use Illuminate\Support\Str;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            // Level 2: Ketua
            ['code' => 'KETUA', 'name' => 'Ketua', 'level' => 2, 'is_management' => true, 'can_login' => true, 'can_vote' => true, 'can_manage_members' => true, 'can_manage_events' => true],
            // Level 3: Pengurus Inti
            ['code' => 'WAKIL', 'name' => 'Wakil', 'level' => 3, 'is_management' => true, 'can_login' => true, 'can_vote' => true],
            ['code' => 'BENDAHARA', 'name' => 'Bendahara', 'level' => 3, 'is_management' => true, 'can_login' => true],
            ['code' => 'SEKRETARIS', 'name' => 'Sekretaris', 'level' => 3, 'is_management' => true, 'can_login' => true],
            // Level 4: PJ/Assisten
            ['code' => 'ASBENDA', 'name' => 'Assisten Bendahara', 'level' => 4],
            ['code' => 'ASSEKRE', 'name' => 'Assisten Sekretaris', 'level' => 4],
            ['code' => 'PJ_PENGEMBANG', 'name' => 'PJ Pengembang', 'level' => 4],
            ['code' => 'PJ_PDD', 'name' => 'PJ PDD', 'level' => 4],
            ['code' => 'ASPJ_PENGEMBANG', 'name' => 'Assisten PJ Pengembang', 'level' => 4],
            ['code' => 'ASPJ_PDD', 'name' => 'Assisten PDD', 'level' => 4],
        ];

        // PJ dan Assisten Kelompok 1-12 (level 4)
        for ($i = 1; $i <= 12; $i++) {
            $jabatans[] = ['code' => "PJ_KEL{$i}", 'name' => "PJ Kelompok {$i}", 'level' => 4];
            $jabatans[] = ['code' => "ASPJ_KEL{$i}", 'name' => "Assisten PJ Kelompok {$i}", 'level' => 4];
        }

        // Level 5: Anggota
        $jabatans[] = ['code' => 'ANGGOTA_TIM', 'name' => 'Anggota Kelompok + Tim Pengembang/PDD', 'level' => 5];
        $jabatans[] = ['code' => 'ANGGOTA_KEL', 'name' => 'Anggota Kelompok', 'level' => 5];
        // Level 6: Anggota Umum
        $jabatans[] = ['code' => 'ANGGOTA_UMUM', 'name' => 'Anggota Umum', 'level' => 6];
        // Level 7: Pengunjung
        $jabatans[] = ['code' => 'PENGUNJUNG', 'name' => 'Pengunjung', 'level' => 7, 'can_login' => true]; // pengunjung bisa login

        foreach ($jabatans as $j) {
            Jabatan::firstOrCreate(
                ['name' => $j['name']],
                [
                    'jabatan_uuid' => (string) Str::uuid(),
                    'code'         => $j['code'] ?? null,
                    'description'  => $j['description'] ?? null,
                    'level'        => $j['level'],
                    'sort_order'   => $j['sort_order'] ?? 0,
                    'is_management'=> $j['is_management'] ?? false,
                    'is_active'    => $j['is_active'] ?? true,
                    'can_login'    => $j['can_login'] ?? true,
                    'can_vote'     => $j['can_vote'] ?? false,
                    'can_manage_members' => $j['can_manage_members'] ?? false,
                    'can_manage_events'  => $j['can_manage_events'] ?? false,
                ]
            );
        }
    }
}
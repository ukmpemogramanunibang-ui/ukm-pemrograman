<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelStatusSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 8 Level lengkap (1–8)
        $levels = [
            ['id' => 1, 'name' => 'owner',        'display_name' => 'Owner'],
            ['id' => 2, 'name' => 'superadmin',   'display_name' => 'Superadmin'],
            ['id' => 3, 'name' => 'ketua',        'display_name' => 'Ketua UKM'],
            ['id' => 4, 'name' => 'pengurus',     'display_name' => 'Pengurus Inti'],
            ['id' => 5, 'name' => 'anggota',      'display_name' => 'Anggota UKM'],
            ['id' => 6, 'name' => 'anggota_umum', 'display_name' => 'Anggota Umum'],
            ['id' => 7, 'name' => 'pj',           'display_name' => 'PJ / Asisten'],
            ['id' => 8, 'name' => 'guest',        'display_name' => 'Pengunjung'],
        ];

        foreach ($levels as $level) {
            DB::table('levels')->updateOrInsert(
                ['id' => $level['id']],
                ['name' => $level['name'], 'display_name' => $level['display_name']]
            );
        }

        $statuses = [
            ['name' => 'pending',   'display_name' => 'Pending'],
            ['name' => 'active',    'display_name' => 'Aktif'],
            ['name' => 'suspended', 'display_name' => 'Ditangguhkan'],
            ['name' => 'blocked',   'display_name' => 'Diblokir'],
            ['name' => 'expired',   'display_name' => 'Kadaluarsa'],
            ['name' => 'deleted',   'display_name' => 'Dihapus'],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['name' => $status['name']],
                ['display_name' => $status['display_name']]
            );
        }
    }
}
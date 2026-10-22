<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Aktif', 'slug' => 'aktif'],
            ['name' => 'Nonaktif', 'slug' => 'nonaktif'],
            ['name' => 'Pending', 'slug' => 'pending'],
            ['name' => 'Cuti', 'slug' => 'cuti'],
            ['name' => 'Lulus', 'slug' => 'lulus'],
            ['name' => 'Alumni', 'slug' => 'alumni'],
            ['name' => 'Blacklist', 'slug' => 'blacklist'],
            ['name' => 'Suspend', 'slug' => 'suspend'],
            ['name' => 'Deleted', 'slug' => 'deleted'],
            ['name' => 'Menunggu Verifikasi', 'slug' => 'menunggu-verifikasi'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['name' => $status['name']],
                ['slug' => $status['slug']]
            );
        }
    }
}
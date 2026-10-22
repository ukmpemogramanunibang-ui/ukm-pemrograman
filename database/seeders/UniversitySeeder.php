<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;
use Illuminate\Support\Str;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        University::firstOrCreate(
            ['name' => 'Universitas Contoh'],
            [
                'university_uuid' => (string) Str::uuid(),
                'short_name'      => 'UNCON',
                'city'            => 'Jakarta',
                'province'        => 'DKI Jakarta',
                'is_active'       => true,
            ]
        );
    }
}
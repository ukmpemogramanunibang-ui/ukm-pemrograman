<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Major;
use Illuminate\Support\Str;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        Major::firstOrCreate(
            ['name' => 'Teknik Informatika'],
            [
                'major_uuid' => (string) Str::uuid(),
                'university_id' => 1, // asumsikan universitas pertama
            ]
        );
    }
}
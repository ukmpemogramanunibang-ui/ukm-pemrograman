<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationalPeriod;
use Illuminate\Support\Str;

class OrganizationalPeriodSeeder extends Seeder
{
    public function run(): void
    {
        OrganizationalPeriod::firstOrCreate(
            ['name' => '2025/2026'],
            [
                'organizational_period_uuid' => (string) Str::uuid(),
                'start_date'                 => '2025-09-01',
                'end_date'                   => '2026-08-31',
                'is_active'                  => true,
            ]
        );
    }
}
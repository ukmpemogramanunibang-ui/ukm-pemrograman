<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserQrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $owner = \App\Models\User::where('email', 'owner@ukm.ac.id')->first();
    if ($owner) {
        \App\Models\UserQrCode::firstOrCreate(
            ['user_id' => $owner->id],
            [
                'qr_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'token' => \Illuminate\Support\Str::random(80),
                'generated_at' => now(),
                'is_active' => true,
            ]
        );
    }
}
}

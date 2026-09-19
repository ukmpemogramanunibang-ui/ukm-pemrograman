<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\IdentityVaultService;
use App\Models\IdentityVault\IdentityRegistry;

class IdentityVaultSeeder extends Seeder
{
    public function run(IdentityVaultService $vault)
    {
        $users = User::all();
        foreach ($users as $user) {
            // Cek apakah sudah ada identity
            $existing = IdentityRegistry::where('user_uuid', $user->user_uuid)->first();
            if (!$existing) {
                $identity = $vault->createIdentity($user);
                $this->command->info("Created identity for {$user->name}");
            } else {
                $this->command->info("Identity already exists for {$user->name}");
            }
        }
    }
}
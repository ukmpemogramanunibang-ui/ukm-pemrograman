<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;

class SetRoleLabel extends Command
{
    protected $signature = 'org:set-role-label
                            {user_id : ID user}
                            {organization : slug atau ID organisasi}
                            {label : Label spesifik, misal "Ketua BEM"}';

    protected $description = 'Set label jabatan spesifik user di organisasi tertentu';

    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $orgArg = $this->argument('organization');
        $label  = $this->argument('label');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User ID {$userId} tidak ditemukan.");
            return self::FAILURE;
        }

        $org = is_numeric($orgArg)
            ? Organization::find($orgArg)
            : Organization::where('slug', $orgArg)->first();

        if (!$org) {
            $this->error("Organisasi '{$orgArg}' tidak ditemukan.");
            return self::FAILURE;
        }

        $membership = $user->organizationMemberships()
            ->where('organization_id', $org->id)
            ->first();

        if (!$membership) {
            $this->error("User {$user->name} bukan anggota {$org->name}.");
            return self::FAILURE;
        }

        $old = $membership->position_label ?? '(kosong)';
        $membership->update(['position_label' => $label]);

        $this->info("✅ Label diperbarui:");
        $this->line("   User : {$user->name} (ID {$userId})");
        $this->line("   Org  : {$org->name}");
        $this->line("   Dari : {$old}");
        $this->line("   Ke   : {$label}");

        return self::SUCCESS;
    }
}
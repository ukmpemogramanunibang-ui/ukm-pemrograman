<?php
namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillUnicodeMembershipsSeeder extends Seeder
{
    public function run(): void
    {
        $unicode = Organization::where('slug', 'ukm-pemrograman-unicode')->firstOrFail();

        // Mapping level_id -> access_role
        $roleMap = [
            3 => 'leader',    // Ketua UKM
            4 => 'officer',   // Pengurus
            5 => 'member',    // Anggota
            6 => 'member',    // Anggota Umum (opsional: ikut UNICODE?)
            7 => 'pj',        // PJ
        ];

        $now = now();
        $batch = [];

        User::query()
            ->whereNull('deleted_at')
            ->whereIn('level_id', array_keys($roleMap))
            ->chunkById(500, function ($users) use (&$batch, $unicode, $roleMap, $now) {
                foreach ($users as $u) {
                    $batch[] = [
                        'organization_id'   => $unicode->id,
                        'user_id'           => $u->id,
                        'access_role'       => $roleMap[$u->level_id] ?? 'member',
                        'membership_status' => $u->is_active ? 'active' : 'inactive',
                        'joined_at'         => $u->created_at?->toDateString() ?? $now->toDateString(),
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
                DB::table('organization_memberships')->insertOrIgnore($batch);
                $batch = [];
            });

        $this->command->info("Backfill selesai. Total membership: " . DB::table('organization_memberships')->count());
    }
}
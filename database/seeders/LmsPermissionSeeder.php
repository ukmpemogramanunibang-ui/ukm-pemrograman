<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LmsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Jika kamu sudah punya tabel permissions, tinggal daftarkan.
        // Di sini hanya placeholder — sesuaikan dengan sistem kamu.
        $permissions = [
            'lms.view', 'lms.create_group', 'lms.update_group', 'lms.delete_group',
            'lms.create_task', 'lms.update_task', 'lms.delete_task',
            'lms.publish', 'lms.view_all', 'lms.assign_role', 'lms.assign_individual',
            'lms.review_submission', 'lms.delete_other_group', 'lms.view_audit',
        ];

        // DB::table('permissions')->insertOrIgnore(
        //     collect($permissions)->map(fn ($p) => ['name' => $p, 'created_at' => now(), 'updated_at' => now()])->all()
        // );
    }
}
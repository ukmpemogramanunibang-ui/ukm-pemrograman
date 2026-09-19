<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'owner',
            'superadmin',
            'ketua',
            'pengurus inti',
            'pj/assisten',
            'anggota kelompok',
            'anggota umum',
            'pengunjung',
        ];

        // Buat semua role (hanya satu kali)
        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }
  $roles = ['owner', 'superadmin', 'ketua', 'pengurus', 'anggota', 'guest'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
        // Ambil role owner yang sudah pasti ada
        $owner = Role::where('name', 'owner')->where('guard_name', 'web')->first();

        // Berikan SEMUA permission ke owner
        if ($owner) {
            $owner->syncPermissions(Permission::all());
        }
    }
}
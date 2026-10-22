<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage users',
            'manage members',
            'manage points',
            'approve points',
            'manage attendance',
            'manage events',
            'manage rooms',
            'manage gallery',
            'manage articles',
            'manage announcements',
            'manage permissions',
            'manage roles',
            'manage settings',
            'scan qr',
            'login qr',
            'approve members',
            'manage teams',
            'manage groups',
            'manage committees',
            'manage finances',
            'manage inventory',
            'manage documents',
            'manage reports',
            'view dashboard',
            'view analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',   // wajib diisi agar sinkron dengan migration Spatie
            ]);
        }
    }
}
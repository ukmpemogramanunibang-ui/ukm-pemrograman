<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Level;
use App\Models\Status;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permission dasar
        Permission::create(['name' => 'login-manual']);
        Permission::create(['name' => 'login-qr']);
        Permission::create(['name' => 'view-dashboard']);

        // Buat role
        $superadmin = Role::create(['name' => 'superadmin']);
        $ketua      = Role::create(['name' => 'ketua']);
        $pengurus   = Role::create(['name' => 'pengurus']);
        $anggota    = Role::create(['name' => 'anggota']);
        $guest      = Role::create(['name' => 'guest']);

        // Assign permission ke role
        $superadmin->givePermissionTo(Permission::all());
        $ketua->givePermissionTo(['login-manual', 'login-qr', 'view-dashboard']);
        $anggota->givePermissionTo(['login-manual', 'login-qr', 'view-dashboard']);
        $guest->givePermissionTo(['login-manual', 'view-dashboard']);

        // Ambil level superadmin dan status active
        $levelSuperadmin = Level::where('name', 'superadmin')->first();
        $statusActive    = Status::where('name', 'active')->first();

        // Buat user Superadmin awal
        $user = User::create([
            'user_uuid'     => (string) Str::uuid(),
            'nim'           => '0000000001',
            'name'          => 'Superadmin',
            'username'      => 'superadmin',
            'email'         => 'superadmin@unicode.test',
            'password'      => bcrypt('password'),
            'level_id'      => $levelSuperadmin->id,
            'status_id'     => $statusActive->id,
            'account_type'  => 'official',
            'is_active'     => true,
            'is_verified'   => true,
        ]);

        $user->assignRole('superadmin');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\University;
use App\Models\Major;
use App\Models\Jabatan;
use App\Models\Level;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixRelasiDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Universitas Default
        $universities = [
            'Universitas Bhakti Asih' => 'UNIBA',
            'Universitas Contoh' => 'UNCON',
            'Universitas Bina Nusantara' => 'BINUS',
        ];

        foreach ($universities as $name => $short) {
            University::firstOrCreate(
                ['name' => $name],
                [
                    'university_uuid' => Str::uuid(),
                    'short_name' => $short,
                    'is_active' => true,
                ]
            );
        }

        // 2. Jurusan Default (semua terhubung ke universitas pertama)
        $majors = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Manajemen',
            'Kebidanan',
            'Keperawatan',
            'Hukum',
            'Pelayaran',
        ];
        $firstUniv = University::first();
        foreach ($majors as $majorName) {
            Major::firstOrCreate(
                ['name' => $majorName, 'university_id' => $firstUniv->id],
                ['major_uuid' => Str::uuid(), 'is_active' => true]
            );
        }

        // 3. Jabatan Default
        $jabatans = [
            ['code' => 'KETUA', 'name' => 'Ketua', 'level' => 2],
            ['code' => 'WAKIL', 'name' => 'Wakil', 'level' => 3],
            ['code' => 'BENDAHARA', 'name' => 'Bendahara', 'level' => 3],
            ['code' => 'SEKRETARIS', 'name' => 'Sekretaris', 'level' => 3],
            ['code' => 'PJ_PENGEMBANG', 'name' => 'PJ Pengembang', 'level' => 4],
            ['code' => 'PJ_PDD', 'name' => 'PJ PDD', 'level' => 4],
            ['code' => 'PENGURUS', 'name' => 'Pengurus', 'level' => 4],
            ['code' => 'ANGGOTA', 'name' => 'Anggota', 'level' => 5],
            ['code' => 'ANGGOTA_UMUM', 'name' => 'Anggota Umum', 'level' => 5],
            ['code' => 'PENGUNJUNG', 'name' => 'Pengunjung', 'level' => 6],
        ];
        foreach ($jabatans as $jab) {
            Jabatan::firstOrCreate(
                ['code' => $jab['code']],
                [
                    'jabatan_uuid' => Str::uuid(),
                    'name' => $jab['name'],
                    'level' => $jab['level'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Pastikan Level & Status Ada
        $levels = [
            1 => 'owner',
            2 => 'superadmin',
            3 => 'ketua',
            4 => 'pengurus',
            5 => 'anggota',
            6 => 'guest',
        ];
        foreach ($levels as $id => $name) {
            Level::firstOrCreate(['id' => $id], ['name' => $name]);
        }
        $statuses = [
            1 => 'pending',
            2 => 'active',
            3 => 'suspended',
            4 => 'blocked',
            5 => 'expired',
            6 => 'deleted',
        ];
        foreach ($statuses as $id => $name) {
            Status::firstOrCreate(['id' => $id], ['name' => $name]);
        }

        // 5. Setup Role & Permission (Spatie)
        $this->setupRolesAndPermissions();

        // 6. Hubungkan User ke Relasi
        $users = User::all();

        foreach ($users as $user) {
            // Universitas & Jurusan
            if (!$user->university_id) {
                $user->university_id = University::first()->id;
            }
            if (!$user->major_id) {
                $user->major_id = Major::first()->id;
            }

            // Level & Status
            if (!$user->level_id) {
                $user->level_id = 4; // default pengurus
            }
            if (!$user->status_id) {
                $user->status_id = 2; // aktif
            }

            $user->save();

            // Hubungkan ke Jabatan
            $levelToJabatan = [
                1 => 'KETUA',
                2 => 'PENGURUS',
                3 => 'KETUA',
                4 => 'PENGURUS',
                5 => 'ANGGOTA',
                6 => 'PENGUNJUNG',
            ];
            $kodeJabatan = $levelToJabatan[$user->level_id] ?? 'PENGURUS';
            $jabatan = Jabatan::where('code', $kodeJabatan)->first();

            if ($jabatan) {
                $exists = DB::table('jabatan_user')
                    ->where('user_id', $user->id)
                    ->where('jabatan_id', $jabatan->id)
                    ->exists();

                if (!$exists) {
                    DB::table('jabatan_user')->insert([
                        'jabatan_user_uuid' => Str::uuid(),
                        'user_id' => $user->id,
                        'jabatan_id' => $jabatan->id,
                        'is_primary' => true,
                        'status' => 'active',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Hubungkan ke Role (Spatie)
            $roleName = $levels[$user->level_id] ?? 'pengurus';
            $role = Role::findOrCreate($roleName, 'web');
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }
    }

    private function setupRolesAndPermissions(): void
    {
        // Daftar permission yang akan diberikan ke Owner
        $permissions = [
            'manage users', 'manage members', 'manage points', 'approve points',
            'manage attendance', 'manage events', 'manage rooms', 'manage gallery',
            'manage articles', 'manage announcements', 'manage permissions',
            'manage roles', 'manage settings', 'scan qr', 'login qr',
            'approve members', 'manage teams', 'manage groups', 'manage committees',
            'manage finances', 'manage inventory', 'manage documents',
            'manage reports', 'view dashboard', 'view analytics',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // Buat role Owner dengan semua permission
        $ownerRole = Role::findOrCreate('owner', 'web');
        $ownerRole->syncPermissions($permissions);

        // Role lain bisa diberi permission sesuai kebutuhan
        // (contoh: ketua hanya beberapa permission)
        $ketuaRole = Role::findOrCreate('ketua', 'web');
        $ketuaRole->syncPermissions(['view dashboard', 'manage members', 'scan qr', 'login qr', 'approve members']);

        $pengurusRole = Role::findOrCreate('pengurus', 'web');
        $pengurusRole->syncPermissions(['view dashboard', 'scan qr', 'login qr']);
    }
}
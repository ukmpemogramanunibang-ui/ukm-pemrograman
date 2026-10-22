<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\University;
use App\Models\Major;
use App\Models\Jabatan;
use App\Models\JabatanUser;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class FillUserInfoToRelationalTables extends Seeder
{
    public function run(): void
    {
        // Ambil semua user untuk referensi
        $users = User::all()->keyBy('id');

        // 1. Update tabel jabatan_user berdasarkan user_id
        DB::table('jabatan_user')->get()->each(function ($row) use ($users) {
            $user = $users->get($row->user_id);
            if ($user) {
                DB::table('jabatan_user')
                    ->where('id', $row->id)
                    ->update([
                        'name_user' => $user->name,
                        'nim_user' => $user->nim,
                    ]);
            }
        });

        // 2. Update tabel model_has_roles berdasarkan model_id (asumsi model_type = User)
        DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->get()
            ->each(function ($row) use ($users) {
                $user = $users->get($row->model_id);
                if ($user) {
                    DB::table('model_has_roles')
                        ->where('role_id', $row->role_id)
                        ->where('model_id', $row->model_id)
                        ->where('model_type', 'App\\Models\\User')
                        ->update([
                            'name_user' => $user->name,
                            'nim_user' => $user->nim,
                        ]);
                }
            });

        // 3. Untuk tabel universities, majors, jabatans – isi dengan data user pertama (contoh) atau biarkan NULL
        // Jika ingin mengisi, gunakan user pertama sebagai referensi
        $firstUser = User::first();
        if ($firstUser) {
            DB::table('universities')->update([
                'name_user' => $firstUser->name,
                'nim_user' => $firstUser->nim,
            ]);
            DB::table('majors')->update([
                'name_user' => $firstUser->name,
                'nim_user' => $firstUser->nim,
            ]);
            DB::table('jabatans')->update([
                'name_user' => $firstUser->name,
                'nim_user' => $firstUser->nim,
            ]);
        }

        // 4. Untuk tabel roles dan role_has_permissions – tidak ada user_id langsung, isi NULL
        // Biarkan NULL (default) karena tidak ada relasi user yang jelas
    }
}
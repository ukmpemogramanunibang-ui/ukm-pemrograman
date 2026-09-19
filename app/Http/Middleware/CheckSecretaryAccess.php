<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSecretaryAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Anda harus login terlebih dahulu.');
        }

        // 1. Owner dan Ketua selalu diizinkan
        if (in_array($user->level_id, [1, 3])) {
            return $next($request);
        }

        // 2. Pengurus (level_id 4) harus memiliki jabatan Sekretaris atau Asisten Sekretaris
        if ($user->level_id == 4) {
            $sekretarisJabatan = ['Sekretaris', 'Asisten Sekretaris']; // sesuaikan dengan nama di tabel jabatan
            $hasSecretaryRole = $user->jabatans()
                ->whereIn('nama_jabatan', $sekretarisJabatan) // pastikan kolom nama sesuai
                ->wherePivot('status', 'active')
                ->exists();

            if ($hasSecretaryRole) {
                return $next($request);
            }
        }

        // Jika tidak lolos, tampilkan 403
        abort(403, 'Akses ditolak. Hanya Owner, Ketua, dan Sekretaris yang diizinkan.');
    }
}
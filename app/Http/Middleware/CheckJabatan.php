<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckJabatan
 * ------------------------------------------------------------
 * Memastikan user yang login memiliki salah satu jabatan (slug)
 * yang diizinkan. Dipakai untuk route dinamis seperti:
 *   - /pengurus/bendahara/*   → jabatan:bendahara,asisten-bendahara
 *   - /pengurus/pdd/*         → jabatan:pdd,pj-pdd
 *   - /pengurus/pengembang/*  → jabatan:pengembang,pj-pengembang
 *
 * Aman terhadap:
 *   - relasi jabatans belum ada
 *   - user null (guest)
 *   - query error → silent fallback to deny
 */
class CheckJabatan
{
    public function handle(Request $request, Closure $next, string ...$slugs): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // Bersihkan slug: trim + lowercase + buang empty
        $slugs = array_values(array_filter(array_map(
            fn ($s) => strtolower(trim((string) $s)),
            $slugs
        )));

        if (empty($slugs)) {
            // Kalau tidak dikasih parameter, izinkan lewat saja
            return $next($request);
        }

        $hasJabatan = false;

        try {
            // Cek via relasi jabatans (many-to-many) — utama
            if (method_exists($user, 'jabatans')) {
                $hasJabatan = $user->jabatans()
                    ->whereIn('slug', $slugs)
                    ->wherePivot('status', 'active')
                    ->exists();
            }

            // Fallback 1: cek via kolom/atribut jika ada
            if (! $hasJabatan && isset($user->jabatan_slug)) {
                $hasJabatan = in_array(strtolower($user->jabatan_slug), $slugs, true);
            }

            // Fallback 2: cek pivot table manual (kalau relasi tidak didefinisikan)
            if (! $hasJabatan) {
                $hasJabatan = \DB::table('jabatan_user')
                    ->join('jabatans', 'jabatans.id', '=', 'jabatan_user.jabatan_id')
                    ->where('jabatan_user.user_id', $user->id)
                    ->whereIn('jabatans.slug', $slugs)
                    ->where('jabatan_user.status', 'active')
                    ->exists();
            }
        } catch (\Throwable $e) {
            // Fail-safe: log & deny
            \Log::warning('CheckJabatan query failed', [
                'user_id' => $user->id ?? null,
                'slugs'   => $slugs,
                'error'   => $e->getMessage(),
            ]);
            $hasJabatan = false;
        }

        if (! $hasJabatan) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses jabatan ini.',
                    'required' => $slugs,
                ], 403);
            }
            abort(403, 'Anda tidak memiliki hak akses jabatan ini.');
        }

        return $next($request);
    }
}
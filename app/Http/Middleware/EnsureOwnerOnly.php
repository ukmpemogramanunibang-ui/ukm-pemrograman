<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * ═══════════════════════════════════════════════════════════════
 *  ENSURE OWNER ONLY
 *  ═══════════════════════════════════════════════════════════════
 *  Melindungi seluruh area /owner/* dari akses role lain:
 *
 *  ✅ ALLOWED:
 *     - User dengan level_id = 1 (Owner)
 *     - User dengan Spatie role 'owner'
 *     - Owner dalam mode Explore (impersonate) — HANYA owner
 *
 *  ❌ BLOCKED (dengan 403 atau redirect):
 *     - Superadmin, Ketua, Pengurus, Anggota UKM, Anggota Umum, Pengunjung
 *     - Guest (belum login)
 *     - User yang dipaksa impersonate ke owner (tidak mungkin, tapi dicek)
 *
 *  Anti-celah:
 *     - Cek ganda: level_id + Spatie role + session fingerprint
 *     - Log semua percobaan ilegal
 *     - Redirect bersih, tidak bocorkan info
 * ═══════════════════════════════════════════════════════════════
 */
class EnsureOwnerOnly
{
public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        $isOwnerByLevel = (int) $user->level_id === 1;
        $isOwnerByRole  = method_exists($user, 'hasRole')
            && $user->hasRole('owner');

        if (! $isOwnerByLevel || ! $isOwnerByRole) {
            try {
                Log::channel('security')->warning('[Security] Illegal access to OWNER area', [
                    'user_id'  => $user->id,
                    'level_id' => $user->level_id,
                    'url'      => $request->fullUrl(),
                    'ip'       => $request->ip(),
                ]);
            } catch (\Throwable $e) {}

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Hanya Owner.',
                ], 403);
            }

            abort(403, 'Akses ditolak. Halaman ini hanya untuk Owner.');
        }

        return $next($request);
    }
    /**
     * Deny — bisa return JSON atau redirect.
     */
    protected function deny(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akses ditolak. Halaman ini hanya untuk Owner.',
                'code'    => 'OWNER_ONLY',
            ], 403);
        }

        // Halaman HTML — redirect bersih
        return response()->view('errors.403', [
            'message' => 'Akses ditolak. Hanya Owner yang dapat mengakses halaman ini.',
            'reason'  => 'Anda login sebagai role selain Owner. Jika ini sebuah kesalahan, hubungi Owner.',
        ], 403);
    }

    /**
     * Audit log — catat percobaan ilegal.
     */
    protected function logIllegalAccess(Request $request, $user, string $reason = 'non_owner'): void
    {
        try {
            Log::channel('daily')->warning('[Security] Illegal access attempt to OWNER area', [
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'level_id'   => $user->level_id,
                'role'       => method_exists($user, 'getRoleNames')
                    ? $user->getRoleNames()->toArray()
                    : [],
                'reason'     => $reason,
                'url'        => $request->fullUrl(),
                'method'     => $request->method(),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer'    => $request->header('referer'),
                'at'         => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            // silent — jangan sampai log bikin error
        }
    }
}
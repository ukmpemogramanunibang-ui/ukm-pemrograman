<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLmsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        if (!$user->is_active) {
            abort(403, 'Akun Anda tidak aktif.');
        }

        if (in_array($user->status_id ?? 0, [/* nonaktif, suspend, blacklist, deleted */])) {
            abort(403, 'Akun Anda tidak memiliki akses ke LMS.');
        }

        // Role yang diizinkan
        $allowedLevels = [1, 2, 3, 4, 5]; // Owner..Anggota UKM
        if (!in_array($user->level_id, $allowedLevels)) {
            abort(403, 'Role Anda tidak memiliki akses LMS.');
        }

        return $next($request);
    }
}
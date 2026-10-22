<?php

namespace App\Http\Middleware;

use App\Models\Lms\LmsItem;
use App\Models\Lms\LmsItemUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memastikan user adalah penerima sah (recipient)
 * dari sebuah tugas/material sebelum bisa mengakses endpoint
 * seperti /tugas/{item}/kumpulkan, /tugas/{item}/submit, dll.
 *
 * Lapisan 3 dari 3 lapis keamanan (Policy + Middleware + Auth).
 */
class VerifiedRecipient
{
    public function handle(Request $request, Closure $next, string $param = 'item'): Response
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        // Ambil item dari route parameter (default: 'item')
        $item = $request->route($param);

        if (!$item instanceof LmsItem) {
            // Bisa jadi id numeric; resolve manual
            $id = is_object($item) ? $item->id : $item;
            $item = LmsItem::find($id);
        }

        if (!$item) {
            abort(404, 'Tugas tidak ditemukan.');
        }

        // 1. Cek apakah item sudah dipublikasikan
        if (!in_array($item->status, ['published', 'draft'])) {
            abort(404, 'Tugas ini sudah tidak tersedia.');
        }

        // 2. Cek apakah user adalah recipient
        $assignment = LmsItemUser::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$assignment) {
            // Owner & Superadmin boleh bypass (untuk audit / monitoring)
            if (in_array($user->level_id, [1, 2])) {
                return $next($request);
            }

            // Ketua/pengurus yang merupakan creator juga boleh bypass
            if (in_array($user->level_id, [3, 4]) && $item->created_by === $user->id) {
                return $next($request);
            }

            return $request->expectsJson()
                ? response()->json([
                    'message' => 'Anda tidak memiliki akses ke tugas ini.',
                ], 403)
                : abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        // 3. Simpan assignment di request untuk dipakai controller
        $request->attributes->set('lms_assignment', $assignment);
        $request->attributes->set('lms_item', $item);

        // 4. Cek deadline (khusus task)
        if ($item->type === 'task' && $item->deadline_at) {
            $isLate = now()->gt($item->deadline_at);

            // Kalau late & submission sudah pernah, tolak
            if ($isLate && $assignment->submitted_at) {
                // Biarkan lewat kalau memang untuk melihat / revisi
                // Tapi cek apakah task allow_resubmit
                if (!$item->allow_resubmit) {
                    return $request->expectsJson()
                        ? response()->json([
                            'message' => 'Deadline sudah lewat dan resubmit tidak diizinkan.',
                        ], 403)
                        : abort(403, 'Deadline sudah lewat.');
                }
            }
        }

        // 5. Cek status item (cancelled / archived)
        if (in_array($item->status, ['archived', 'deleted'])) {
            abort(410, 'Tugas ini sudah diarsipkan.');
        }

        return $next($request);
    }
}
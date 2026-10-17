<?php

namespace App\Http\Controllers\Owner\Attendance;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Services\Attendance\AttendanceEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Endpoint untuk absensi user (manual/NFC/QR).
 * Semua endpoint dilindungi throttle + server-authoritative.
 */
class AttendanceApiController extends Controller
{
    public function __construct(protected AttendanceEngine $engine) {}

    public function checkIn(Request $request, AttendanceSession $session)
    {
        // Rate limiting: max 10 request/menit/user
        $key = 'attendance:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'code' => 'rate_limited',
                'message' => 'Terlalu banyak permintaan. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $method = $request->input('method', 'manual');
        if (!in_array($method, ['manual', 'nfc', 'qr'])) {
            return response()->json(['success' => false, 'message' => 'Metode tidak valid.'], 422);
        }

        $result = $this->engine->process(Auth::user(), $session, $method, [
            'ip'     => $request->ip(),
            'device' => $request->header('X-Device-Id'),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function monitorData(AttendanceSession $session)
    {
        // delegate ke monitor()
        return app(AttendanceController::class)->monitor($session);
    }
}
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NfcController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\Api\AnnouncementController;

/*
|--------------------------------------------------------------------------
| API Routes — UNICODE Connect
|--------------------------------------------------------------------------
|
| Struktur:
|   1. Sanctum (user default)
|   2. Public API v1 (health, login, resolve NFC)
|   3. Protected API v1 (auth:sanctum)
|   4. Legacy endpoints (backward compatible)
|
*/

/* ============================================================
 | 1. SANCTUM DEFAULT
 * ============================================================ */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->name('api.user');

/* ============================================================
 | 2. PUBLIC API v1
 * ============================================================ */
Route::prefix('v1')->name('api.v1.')->group(function () {

    /* ---------- Health Check ---------- */
    Route::middleware('throttle:api-ping')
        ->get('/ping', fn () => response()->json([
            'status'    => 'ok',
            'service'   => 'UNICODE Connect API',
            'version'   => 'v1',
            'server_time' => now()->toIso8601String(),
        ]))->name('ping');

    /* ---------- Auth ---------- */
    Route::prefix('auth')->name('auth.')->group(function () {

        // Login NIM + password (device mobile)
        Route::middleware('throttle:api-login')
            ->post('/login', [AuthController::class, 'login'])
            ->name('login');

        // Refresh token
        Route::middleware('auth:sanctum')
            ->post('/refresh', [AuthController::class, 'refresh'])
            ->name('refresh');

        // Logout
        Route::middleware('auth:sanctum')
            ->post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        // Cek status device & session
        Route::middleware('auth:sanctum')
            ->get('/me', [AuthController::class, 'me'])
            ->name('me');
    });

    /* ---------- NFC Public (untuk Flutter tap) ---------- */
    Route::prefix('nfc')->name('nfc.')->group(function () {

        // Health check (longgar — dipanggil tiap buka app)
        Route::middleware('throttle:api-nfc-ping')
            ->get('/ping', [NfcController::class, 'ping'])
            ->name('ping');

        // Resolve NFC tag → identitas user
        Route::middleware('throttle:api-nfc-resolve')
            ->post('/resolve', [NfcController::class, 'resolve'])
            ->name('resolve');
    });

    /* ---------- QR Public ---------- */
    Route::prefix('qr')->name('qr.')->group(function () {

        // Validate QR token
        Route::middleware('throttle:api-qr')
            ->post('/validate', [QrController::class, 'validate'])
            ->name('validate');

        // Scan QR untuk absensi (butuh auth)
        Route::middleware(['auth:sanctum', 'throttle:api-qr'])
            ->post('/scan', [QrController::class, 'scan'])
            ->name('scan');
    });
});

/* ============================================================
 | 3. PROTECTED API v1 (auth:sanctum)
 * ============================================================ */
Route::prefix('v1')
    ->name('api.v1.')
    ->middleware(['auth:sanctum', 'throttle:api-auth'])
    ->group(function () {

    /* ---------- Profile ---------- */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])
            ->middleware('throttle:api-upload')->name('avatar');
        Route::post('/device-token', [ProfileController::class, 'registerDevice'])
            ->name('device-token'); // untuk FCM
    });

    /* ---------- Notifications ---------- */
    Route::prefix('notifications')->name('notifications.')->group(function () {

        // List + filter
        Route::get('/', [NotificationController::class, 'index'])
            ->middleware('throttle:api-notif-poll')->name('index');

        // Polling (long-poll friendly)
        Route::get('/poll', [NotificationController::class, 'poll'])
            ->middleware('throttle:api-notif-poll')->name('poll');

        // Unread count only (untuk badge)
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
            ->middleware('throttle:api-notif-poll')->name('unread-count');

        // Actions — batch friendly
        Route::post('/read-all', [NotificationController::class, 'readAll'])
            ->name('read-all');
        Route::delete('/clear-read', [NotificationController::class, 'destroyAll'])
            ->name('clear-read');

        // Per-notification (UUID)
        $uuid = '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}';

        Route::prefix('{id}')->where(['id' => $uuid])->group(function () {
            Route::get('/', [NotificationController::class, 'show'])->name('show');
            Route::post('/read', [NotificationController::class, 'read'])->name('read');
            Route::post('/seen', [NotificationController::class, 'seen'])->name('seen');
            Route::post('/actioned', [NotificationController::class, 'actioned'])->name('actioned');
            Route::post('/star', [NotificationController::class, 'toggleStar'])->name('star');
            Route::post('/archive', [NotificationController::class, 'archive'])->name('archive');
            Route::post('/unarchive', [NotificationController::class, 'unarchive'])->name('unarchive');
            Route::delete('/', [NotificationController::class, 'destroy'])->name('destroy');
        });
    });

    /* ---------- Attendance ---------- */
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/today', [AttendanceController::class, 'today'])->name('today');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::post('/clock-in', [AttendanceController::class, 'clockIn'])
            ->middleware('throttle:api-attendance')->name('clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])
            ->middleware('throttle:api-attendance')->name('clock-out');
        Route::post('/permission', [AttendanceController::class, 'requestPermission'])
            ->middleware('throttle:api-attendance')->name('permission');
    });

    /* ---------- Announcements ---------- */
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/{id}', [AnnouncementController::class, 'show'])
            ->whereNumber('id')->name('show');
        Route::post('/{id}/read', [AnnouncementController::class, 'markRead'])
            ->whereNumber('id')->name('read');
    });

    /* ---------- NFC Personal ---------- */
    Route::prefix('nfc')->name('nfc.')->group(function () {
        Route::get('/my-card', [NfcController::class, 'myCard'])->name('my-card');
        Route::get('/history', [NfcController::class, 'history'])->name('history');
    });
});

/* ============================================================
 | 4. LEGACY — Backward Compatible
 |    (owner web polling — tetap dipakai header notification)
 * ============================================================ */
Route::middleware(['auth:sanctum', 'throttle:api-legacy'])
    ->prefix('owner/notifications')
    ->name('api.owner.notifications.')
    ->group(function () {

    Route::get('/poll', [NotificationController::class, 'poll'])->name('poll');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');

    $uuid = '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}';
    Route::post('/{id}/read', [NotificationController::class, 'read'])
        ->where('id', $uuid)->name('read');
    Route::post('/{id}/star', [NotificationController::class, 'toggleStar'])
        ->where('id', $uuid)->name('star');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])
        ->where('id', $uuid)->name('destroy');
    Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
});

/* ============================================================
 | 5. FALLBACK — 404 JSON untuk semua endpoint /api/*
 * ============================================================ */
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint API tidak ditemukan.',
        'code'    => 'API_ROUTE_NOT_FOUND',
    ], 404);
});
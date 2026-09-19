<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AnggotaController;
use App\Models\User;
use App\Models\Event;
use App\Models\AuditLog;
use App\Models\UserQrCode;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AccountCheckController;
use App\Services\QrCodeService;
use App\Services\Attendance\AttendanceClockService;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NfcLoginController;
use App\Http\Controllers\Auth\GoogleDriveController;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\RoomAccessController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\QrValidationController;

use App\Http\Controllers\GameController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GameDiagnosticController;

use App\Http\Controllers\Ketua\QRCodeController;
use App\Http\Controllers\KetuaDashboardController;

use App\Http\Controllers\Owner\ContactsController;
use App\Http\Controllers\Owner\QrCleanupController;
use App\Http\Controllers\Owner\NotificationController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\Owner\RestoreController;
use App\Http\Controllers\Owner\ScheduleController;
use App\Http\Controllers\Owner\KelompokController;
use App\Http\Controllers\Owner\DivisiController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\ExploreAccountController;
use App\Http\Controllers\Owner\PromoteDemoteController;
use App\Http\Controllers\Owner\SecretaryController;
use App\Http\Controllers\Owner\ChatController;
use App\Http\Controllers\Owner\IdentityVaultController;
use App\Http\Controllers\Owner\SystemHealthController;

use App\Http\Controllers\Owner\AttendanceController;
use App\Http\Controllers\Owner\AttendanceAuditController;
use App\Http\Controllers\Owner\AttendanceCalendarController;
use App\Http\Controllers\Owner\AttendancePenaltyController;
use App\Http\Controllers\Owner\AttendancePermissionController;

use App\Http\Controllers\Owner\Management\MemberController;
use App\Http\Controllers\Owner\Management\NfcController;
use App\Http\Controllers\Owner\Management\QrRecoveryController;
use App\Http\Controllers\Owner\Management\TestingController;

use App\Http\Controllers\Owner\Financial\DashboardController as FinancialDashboardController;
use App\Http\Controllers\Owner\Financial\GenerationController;
use App\Http\Controllers\Owner\Financial\FeePlanController;
use App\Http\Controllers\Owner\Financial\InvoiceController;
use App\Http\Controllers\Owner\Financial\PaymentController;
use App\Http\Controllers\Owner\Financial\PenaltyController;
use App\Http\Controllers\Owner\Financial\PointController;
use App\Http\Controllers\Owner\Financial\CampaignController;
use App\Http\Controllers\Owner\Financial\CalendarController;
use App\Http\Controllers\Owner\Financial\AcademicPeriodController;
use App\Http\Controllers\Owner\Financial\ReportController;
use App\Http\Controllers\Owner\Financial\ReactivationController;

use App\Http\Controllers\Secretary\IncomingMailController;
use App\Http\Controllers\Secretary\OutgoingMailController;
use App\Http\Controllers\Secretary\MinutesController;
use App\Http\Controllers\Secretary\ArchiveController;

use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Anggota\ProfilController;
use App\Http\Controllers\Anggota\PoinController;
use App\Http\Controllers\Anggota\PresensiController;
use App\Http\Controllers\Anggota\EventController as AnggotaEventController;
use App\Http\Controllers\Anggota\SertifikatController;
use App\Http\Controllers\Anggota\PortofolioController;
use App\Http\Controllers\Anggota\PengaturanController;

use App\Http\Middleware\CheckSecretaryAccess;
use App\Http\Middleware\OwnerExplorer;

/*
|--------------------------------------------------------------------------
| WEB ROUTES – UKM Pemrograman UNICODE
|--------------------------------------------------------------------------
|
| Struktur:
|   1. Public
|   2. Auth status (heartbeat)
|   3. Auth (guest)
|   4. Password reset
|   5. Authenticated (auth)
|       5.1 Dashboard
|       5.2 Owner
|       5.3 Superadmin
|       5.4 Ketua
|       5.5 Pengurus
|       5.6 Anggota
|   6. Profile & Logout
|
*/

/* ============================================================
 | 1. PUBLIC
 * ============================================================ */

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::view('/about', 'about')->name('about');
Route::view('/dokumentasi', 'dokumentasi')->name('dokumentasi');
Route::view('/prestasi', 'prestasi')->name('prestasi');
Route::view('/portfolio', 'portfolio')->name('portfolio');
Route::get('/member', [AnggotaController::class, 'index'])->name('member');

Route::get('/kontak', [ContactController::class, 'index'])->name('kontak');
Route::post('/kontak', [ContactController::class, 'store'])->name('kontak.store');
Route::view('/ppt-interaktif', 'interaksi-ppt')->name('ppt.interaktif');
Route::view('/interaksi-ppt', 'interaksi-ppt')->name('interaksi.ppt');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
/* ============================================================
 | PUBLIC ACCOUNT CHECK – CEK AKUN UNICODE
 * ============================================================ */

Route::prefix('cek-akun')->name('account.check')->group(function () {

    Route::get('/', [AccountCheckController::class, 'index'])
        ->name('.index');

    Route::get('/manual', [AccountCheckController::class, 'manual'])
        ->name('.manual');

    Route::get('/qr', [AccountCheckController::class, 'qr'])
        ->name('.qr');

    Route::get('/nfc', [AccountCheckController::class, 'nfc'])
        ->name('.nfc');

    // Resolver pusat dengan multi-layer rate limiter
    Route::post('/resolve', [AccountCheckController::class, 'resolve'])
        ->middleware('throttle:account-check')
        ->name('.resolve');
});
/* ============================================================
 | PUBLIC ACCOUNT CHECK – CEK AKUN UNICODE
 * ============================================================ */


// Room publik
Route::prefix('room_publik')->name('room.public.')->group(function () {
    Route::get('/', [RoomAccessController::class, 'publicIndex'])->name('index');
    Route::get('/realtime/{token}', [RoomAccessController::class, 'publicRealtime'])->name('realtime');
    Route::post('/nim', [RoomAccessController::class, 'publicNimLookup'])
        ->middleware('throttle:10,1')
        ->name('nim');
});

Route::get('/room/scan/{token}', function ($token) {
    $room = \App\Models\Room::where('room_token', $token)->first();
    if (!$room) abort(404, 'QR Code tidak valid');
    return redirect()->route('room.public.index', ['room_token' => $room->room_token]);
})->name('room.scan');

// Test email (development only)
Route::get('/test-email', function () {
    Mail::raw('Tes email dari Laravel + Mailtrap', function ($message) {
        $message->to('ukmpemogramanunibang@gmail.com')->subject('Tes Mailtrap');
    });
    return 'Email berhasil dikirim.';
})->middleware('debug');

/* ============================================================
 | 2. AUTH STATUS (heartbeat)
 |    Guest → 401, tidak redirect looping.
 * ============================================================ */

Route::get('/check-auth-status', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'authenticated' => false,
            'redirect'      => null,
        ], 401);
    }

    return response()->json([
        'authenticated' => true,
        'redirect'      => $user->getDashboardRoute(),
    ]);
})
    ->middleware('throttle:30,1')
    ->name('auth.status');

/* ============================================================
 | 3. AUTH (GUEST)
 * ============================================================ */

Route::middleware('guest')->group(function () {

    // Pilih metode login
    Route::get('/login', [LoginController::class, 'showLoginOptions'])->name('login');

    // Login manual (NIM)
    Route::get('/login/manual', [LoginController::class, 'showNimForm'])->name('login.manual.form');
    Route::post('/login/manual', [LoginController::class, 'manualLogin'])->name('login.manual');

    Route::match(['get', 'post'], '/login/check-nim-json', [LoginController::class, 'checkNimJson'])
        ->middleware('throttle:30,1')
        ->name('login.check-nim-json');

    // Login QR
    Route::get('/login/qr', [LoginController::class, 'showQrForm'])->name('login.qr.form');
    Route::post('/login/qr-scan', [LoginController::class, 'qrScan'])
        ->middleware('throttle:30,1')
        ->name('login.qr-scan');
    Route::post('/login/qr-authenticate', [LoginController::class, 'qrAuthenticate'])
        ->middleware('throttle:10,1')
        ->name('login.qr-authenticate');

    // Login NFC
    Route::get('/login/nfc', [NfcLoginController::class, 'showForm'])->name('nfc.login.form');
    Route::post('/login/nfc/scan', [NfcLoginController::class, 'scan'])
        ->middleware('throttle:10,1')
        ->name('nfc.scan');
    Route::post('/login/nfc/authenticate', [NfcLoginController::class, 'authenticate'])
        ->middleware('throttle:10,1')
        ->name('nfc.authenticate');

    // Guest login/register
    Route::get('/guest/login', [LoginController::class, 'showGuestLogin'])->name('guest.login');
    Route::post('/guest/login', [LoginController::class, 'guestLogin'])
        ->middleware('throttle:10,1');

    Route::get('/guest/register', [LoginController::class, 'showGuestRegister'])->name('guest.register');
    Route::post('/guest/register', [LoginController::class, 'guestRegister'])
        ->middleware('throttle:5,1');
});

/* ============================================================
 | 4. PASSWORD RESET
 * ============================================================ */

Route::controller(LoginController::class)->group(function () {
    Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'resetPassword')
        ->middleware('throttle:5,1')
        ->name('password.update');
});

/* ============================================================
 | 5. AUTHENTICATED (auth)
 * ============================================================ */

Route::middleware('auth')->group(function () {

    /* ---------- 5.1 Dashboard & Fallback ---------- */

    Route::get('/dashboard', function () {
        return redirect(auth()->user()->getDashboardRoute());
    })->name('dashboard');

    // Google Drive
    Route::prefix('auth/google')->name('google.')->group(function () {
        Route::get('/', [GoogleDriveController::class, 'redirect'])->name('redirect');
        Route::get('/callback', [GoogleDriveController::class, 'callback'])->name('callback');
        Route::get('/revoke', [GoogleDriveController::class, 'revoke'])->name('revoke');
    });

    /* ============================================================
     | 5.2 OWNER
     * ============================================================ */

    Route::prefix('owner')->name('owner.')->group(function () {

        /* ---------- Dashboard ---------- */

        Route::get('/dashboard', function () {
            if (auth()->user()->level_id != 1) {
                return redirect(auth()->user()->getDashboardRoute());
            }

            $stats = [
                'total_users'        => User::count(),
                'total_pengurus'     => User::where('level_id', 4)->count(),
                'total_anggota'      => User::where('level_id', 5)->where('name', '!=', 'Anggota Umum')->count(),
                'total_pengunjung'   => User::where('level_id', 6)->orWhere('account_type', 'guest')->count(),
                'total_anggota_umum' => User::where('level_id', 5)->where('name', 'Anggota Umum')->count(),
                'total_blocked'      => User::where('is_active', false)->orWhereNotNull('locked_until')->count(),
                'total_owner'        => User::where('level_id', 1)->count(),
                'total_superadmin'   => User::where('level_id', 2)->count(),
            ];

            $activities = collect();

            User::orderByDesc('created_at')->take(5)->get()->each(function ($u) use ($activities) {
                $activities->push((object) [
                    'user'   => $u->name,
                    'action' => 'bergabung sebagai ' . ($u->level->name ?? 'user'),
                    'time'   => $u->created_at->diffForHumans(),
                    'color'  => 'green',
                ]);
            });

            if (class_exists(AuditLog::class)) {
                AuditLog::with('user')
                    ->where(fn ($q) => $q->where('action', 'login')->orWhere('description', 'like', '%login%'))
                    ->latest()
                    ->take(5)
                    ->get()
                    ->each(function ($log) use ($activities) {
                        if (!$log->user) return;
                        $activities->push((object) [
                            'user'   => $log->user->name,
                            'action' => 'melakukan login',
                            'time'   => $log->created_at->diffForHumans(),
                            'color'  => 'blue',
                        ]);
                    });
            }

            $activities = $activities->take(5)->values();

            return view('dashboard.owner', compact('stats', 'activities'));
        })->name('dashboard');

        /* ---------- System ---------- */

        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/time', fn () => response()->json(
                app(AttendanceClockService::class)->syncPayload()
            ))->name('time');

            Route::get('/health', [SystemHealthController::class, 'index'])->name('health');
            Route::post('/mail-test', [SystemHealthController::class, 'sendTestMail'])->name('mail-test');

            Route::get('/config', fn () => view('owner.system.config'))->name('config');
            Route::get('/backup', fn () => view('owner.system.backup'))->name('backup');
        });

        /* ---------- Promote / Demote ---------- */

        Route::prefix('promote-demote')->name('promote-demote.')->group(function () {
            Route::get('/', [PromoteDemoteController::class, 'index'])->name('index');
            Route::get('/data', [PromoteDemoteController::class, 'getData'])->name('data');
            Route::get('/jabatans', [PromoteDemoteController::class, 'getJabatans'])->name('jabatans');
            Route::post('/update', [PromoteDemoteController::class, 'update'])->name('update');
        });
        // Alias lama
        Route::get('/promote-demote', [PromoteDemoteController::class, 'index'])->name('promote-demote');
        Route::get('/promote-demote/data', [PromoteDemoteController::class, 'getData'])->name('promote-demote.data');
        Route::get('/promote-demote/jabatans', [PromoteDemoteController::class, 'getJabatans'])->name('promote-demote.jabatans');
        Route::post('/promote-demote/update', [PromoteDemoteController::class, 'update'])->name('promote-demote.update');

        /* ---------- Explore Account ---------- */

        Route::prefix('settings/explore')->name('settings.explore.')->group(function () {
            Route::get('/', [ExploreAccountController::class, 'index'])->name('index');
            Route::post('/search', [ExploreAccountController::class, 'search'])->name('search');
            Route::get('/verify', [ExploreAccountController::class, 'showVerifyForm'])->name('verify');
            Route::post('/verify', [ExploreAccountController::class, 'verify'])->name('verify.post');
            Route::get('/{user}', [ExploreAccountController::class, 'explore'])
                ->whereNumber('user')
                ->middleware(OwnerExplorer::class)
                ->name('account');
            Route::post('/exit', [ExploreAccountController::class, 'exitExplorer'])->name('exit');
        });

        /* ---------- Restore / Backup ---------- */

        Route::prefix('restore')->name('restore.')->group(function () {
            Route::get('/', [RestoreController::class, 'index'])->name('index');
            Route::post('/run', [RestoreController::class, 'restore'])->name('run');
            Route::post('/backup', [RestoreController::class, 'createBackup'])->name('backup');
            Route::get('/files', [RestoreController::class, 'listFiles'])->name('files');
            Route::get('/download/{filename}', [RestoreController::class, 'download'])
                ->where('filename', '.*\.json$')
                ->name('download');
        });

        /* ---------- Notifications ---------- */

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/archived', [NotificationController::class, 'archived'])->name('archived');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
                ->middleware('throttle:60,1')
                ->name('unread-count');
            Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
            Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
            Route::get('/{notification}', [NotificationController::class, 'show'])
                ->whereNumber('notification')
                ->name('show');
            Route::post('/{notification}/read', [NotificationController::class, 'read'])
                ->whereNumber('notification')
                ->name('read');
            Route::post('/{notification}/archive', [NotificationController::class, 'archive'])
                ->whereNumber('notification')
                ->name('archive');
            Route::post('/{notification}/unarchive', [NotificationController::class, 'unarchive'])
                ->whereNumber('notification')
                ->name('unarchive');
            Route::post('/{notification}/toggle-star', [NotificationController::class, 'toggleStar'])
                ->whereNumber('notification')
                ->name('toggle-star');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])
                ->whereNumber('notification')
                ->name('destroy');

            Route::get('/chat', [ChatController::class, 'index'])->name('chat');
        });

        /* ---------- Bookings ---------- */

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/filter', [BookingController::class, 'filter'])->name('filter');
            Route::get('/create', [BookingController::class, 'create'])->name('create');
            Route::post('/store', [BookingController::class, 'store'])->name('store');
            Route::get('/{booking}', [BookingController::class, 'show'])
                ->whereNumber('booking')
                ->name('show');
            Route::post('/{booking}/approve', [BookingController::class, 'approve'])
                ->whereNumber('booking')->name('approve');
            Route::post('/{booking}/reject', [BookingController::class, 'reject'])
                ->whereNumber('booking')->name('reject');
            Route::post('/{booking}/override', [BookingController::class, 'overrideConflict'])
                ->whereNumber('booking')->name('override');
            Route::post('/{booking}/update-time', [BookingController::class, 'updateTime'])
                ->whereNumber('booking')->name('update-time');
            Route::post('/{booking}/move-room', [BookingController::class, 'moveRoom'])
                ->whereNumber('booking')->name('move-room');
            Route::post('/{booking}/checkin', [BookingController::class, 'checkin'])
                ->whereNumber('booking')->name('checkin');
            Route::post('/{booking}/checkout', [BookingController::class, 'checkout'])
                ->whereNumber('booking')->name('checkout');
        });

        /* ---------- Identity Vault ---------- */

        Route::prefix('identity-vault')->name('identity-vault.')->group(function () {
            Route::get('/', [IdentityVaultController::class, 'index'])->name('index');
            Route::post('/backup', [IdentityVaultController::class, 'backup'])->name('backup');
            Route::post('/recover', [IdentityVaultController::class, 'recover'])->name('recover');
            Route::post('/recover-all', [IdentityVaultController::class, 'recoverAll'])->name('recover-all');
            Route::get('/test-recovery', [IdentityVaultController::class, 'testRecovery'])->name('test');
            Route::get('/audit/{uuid?}', [IdentityVaultController::class, 'audit'])->name('audit');
            Route::get('/{uuid}', [IdentityVaultController::class, 'show'])->name('show');
        });

        /* ---------- QR Cleanup ---------- */

        Route::get('/qr-cleanup', [QrCleanupController::class, 'index'])->name('qr-cleanup');
        Route::delete('/qr-cleanup/destroy', [QrCleanupController::class, 'destroy'])->name('qr-cleanup.destroy');

        /* ---------- Divisi ---------- */

        Route::prefix('divisi')->name('divisi.')->group(function () {
            Route::get('/', [DivisiController::class, 'index'])->name('index');
            Route::get('/custom', [DivisiController::class, 'custom'])->name('custom');
            Route::get('/akses', [DivisiController::class, 'akses'])->name('akses');
            Route::get('/anggota/{divisionId?}', [DivisiController::class, 'anggota'])
                ->whereNumber('divisionId')->name('anggota');
            Route::get('/pengumuman', [DivisiController::class, 'pengumuman'])->name('pengumuman');
            Route::get('/pesan', [DivisiController::class, 'pesan'])->name('pesan');
            Route::get('/invite', [DivisiController::class, 'invite'])->name('invite');

            Route::post('/store', [DivisiController::class, 'store'])->name('store');
            Route::put('/{division}', [DivisiController::class, 'update'])
                ->whereNumber('division')->name('update');
            Route::delete('/{division}', [DivisiController::class, 'destroy'])
                ->whereNumber('division')->name('destroy');

            Route::post('/{division}/add-member', [DivisiController::class, 'addMember'])
                ->whereNumber('division')->name('add-member');
            Route::delete('/{division}/remove-member/{user}', [DivisiController::class, 'removeMember'])
                ->whereNumber('division')->whereNumber('user')->name('remove-member');
            Route::put('/{division}/update-member/{member}', [DivisiController::class, 'updateMember'])
                ->whereNumber('division')->whereNumber('member')->name('update-member');
            Route::post('/{division}/move-member/{member}', [DivisiController::class, 'moveMember'])
                ->whereNumber('division')->whereNumber('member')->name('move-member');
            Route::post('/{division}/permissions', [DivisiController::class, 'updatePermissions'])
                ->whereNumber('division')->name('update-permissions');
            Route::post('/announcement/store', [DivisiController::class, 'storeAnnouncement'])->name('announcement.store');
            Route::get('/{division}/members-count', [DivisiController::class, 'membersCount'])
                ->whereNumber('division')->name('members-count');
        });

        /* ---------- Kelompok ---------- */

        Route::prefix('kelompok')->name('kelompok.')->group(function () {
            Route::get('/', [KelompokController::class, 'index'])->name('index');
            Route::get('/custom', [KelompokController::class, 'custom'])->name('custom');
            Route::get('/akses', [KelompokController::class, 'akses'])->name('akses');
            Route::get('/anggota/{groupId?}', [KelompokController::class, 'anggota'])
                ->whereNumber('groupId')->name('anggota');
            Route::get('/pengumuman', [KelompokController::class, 'pengumuman'])->name('pengumuman');
            Route::get('/pesan', [KelompokController::class, 'pesan'])->name('pesan');
            Route::get('/invite', [KelompokController::class, 'invite'])->name('invite');
            Route::post('/store', [KelompokController::class, 'store'])->name('store');
            Route::put('/{group}', [KelompokController::class, 'update'])
                ->whereNumber('group')->name('update');
            Route::delete('/{group}', [KelompokController::class, 'destroy'])
                ->whereNumber('group')->name('destroy');
            Route::post('/{group}/add-member', [KelompokController::class, 'addMember'])
                ->whereNumber('group')->name('add-member');
            Route::delete('/{group}/remove-member/{user}', [KelompokController::class, 'removeMember'])
                ->whereNumber('group')->whereNumber('user')->name('remove-member');
            Route::put('/{group}/update-member/{member}', [KelompokController::class, 'updateMember'])
                ->whereNumber('group')->whereNumber('member')->name('update-member');
            Route::post('/{group}/move-member/{member}', [KelompokController::class, 'moveMember'])
                ->whereNumber('group')->whereNumber('member')->name('move-member');
            Route::post('/{group}/permissions', [KelompokController::class, 'updatePermissions'])
                ->whereNumber('group')->name('update-permissions');
            Route::post('/announcement/store', [KelompokController::class, 'storeAnnouncement'])->name('announcement.store');
            Route::get('/{group}/members-count', [KelompokController::class, 'membersCount'])
                ->whereNumber('group')->name('members-count');
        });

        /* ---------- Rooms (RoomController resource + kustom) ---------- */

        Route::resource('rooms', RoomController::class)
            ->names('rooms')
            ->whereNumber('room');

        Route::prefix('rooms')->name('rooms.')->group(function () {
            // Booking & kalender
            Route::get('/calendar', [RoomController::class, 'calendar'])->name('calendar');
            Route::get('/requests', [RoomController::class, 'requests'])->name('requests');
            Route::get('/check', [RoomController::class, 'check'])->name('check');
            Route::get('/check-conflicts', [RoomController::class, 'checkConflicts'])->name('check-conflicts');
            Route::post('/bulk-action', [RoomController::class, 'bulkAction'])->name('bulk-action');
            Route::post('/requests/{booking}/approve', [RoomController::class, 'approveBooking'])
                ->whereNumber('booking')->name('requests.approve');
            Route::post('/requests/{booking}/reject', [RoomController::class, 'rejectBooking'])
                ->whereNumber('booking')->name('requests.reject');
            Route::get('/export/{format}', [RoomController::class, 'export'])
                ->whereIn('format', ['csv', 'xlsx', 'pdf'])->name('export');

            // Maintenance
            Route::get('/maintenance', [RoomController::class, 'maintenanceIndex'])->name('maintenance.index');
            Route::get('/{room}/maintenance', [RoomController::class, 'maintenance'])
                ->whereNumber('room')->name('maintenance');

            // Facilities
            Route::get('/{room}/facilities', [RoomController::class, 'facilitiesIndex'])
                ->whereNumber('room')->name('facilities');

            // Schedules
            Route::get('/{room}/schedules', [RoomController::class, 'schedulesIndex'])
                ->whereNumber('room')->name('schedules');

            // Booking
            Route::get('/{room}/booking/create', [RoomController::class, 'bookingCreate'])
                ->whereNumber('room')->name('booking.create');
            Route::post('/{room}/booking', [RoomController::class, 'bookingStore'])
                ->whereNumber('room')->name('booking.store');
            Route::post('/{room}/book', [RoomController::class, 'storeBooking'])
                ->whereNumber('room')->name('book');

            // QR
            Route::post('/{room}/generate-qr', [RoomController::class, 'generateQr'])
                ->whereNumber('room')->name('generate-qr');
            Route::get('/{room}/download-qr/{format}', [RoomController::class, 'downloadQr'])
                ->whereNumber('room')
                ->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'svg'])
                ->name('download-qr');

            // History
            Route::get('/{room}/history', [RoomController::class, 'history'])
                ->whereNumber('room')->name('history');

            // QR testing
            Route::get('/qr-testing', [RoomController::class, 'testingIndex'])->name('testing');
            Route::post('/qr-testing/scan', [RoomController::class, 'testingScan'])->name('testing.scan');
            Route::post('/qr-testing/manual', [RoomController::class, 'testingManual'])->name('testing.manual');

            // Public/private
            Route::get('/public', [RoomController::class, 'publicIndex'])->name('public.index');
            Route::get('/public/history', [RoomController::class, 'publicHistory'])->name('public.history');
            Route::post('/public/{room}/toggle', [RoomController::class, 'togglePublic'])
                ->whereNumber('room')->name('public.toggle');
            Route::get('/public/export', [RoomController::class, 'publicExport'])->name('public.export');
            Route::post('/public/bulk', [RoomController::class, 'bulkPublicAction'])->name('public.bulk');

            Route::get('/room_private', [RoomAccessController::class, 'privateIndex'])->name('private');
            Route::get('/room_private/{room}', [RoomAccessController::class, 'privateShow'])
                ->whereNumber('room')->name('private.show');
        });
        /* ---------- Schedules ---------- */

        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/', [ScheduleController::class, 'index'])->name('index');
            Route::get('/upload', [ScheduleController::class, 'uploadForm'])->name('upload');
            Route::post('/upload', [ScheduleController::class, 'uploadStore'])->name('upload.store');
            Route::get('/public', [ScheduleController::class, 'publicIndex'])->name('public');
            Route::get('/history', [ScheduleController::class, 'history'])->name('history');
            Route::get('/{schedule}', [ScheduleController::class, 'show'])
                ->whereNumber('schedule')->name('show');
            Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])
                ->whereNumber('schedule')->name('edit');
            Route::put('/{schedule}', [ScheduleController::class, 'update'])
                ->whereNumber('schedule')->name('update');
            Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])
                ->whereNumber('schedule')->name('destroy');
        });

        /* ---------- Contacts ---------- */

        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [ContactsController::class, 'index'])->name('index');
            Route::get('/owner', [ContactsController::class, 'byRole'])->name('owner');
            Route::get('/superadmin', [ContactsController::class, 'byRole'])->name('superadmin');
            Route::get('/ketua', [ContactsController::class, 'byRole'])->name('ketua');
            Route::get('/pengurus', [ContactsController::class, 'byRole'])->name('pengurus');
            Route::get('/anggota', [ContactsController::class, 'byRole'])->name('anggota');
            Route::get('/anggota-umum', [ContactsController::class, 'byRole'])->name('anggota-umum');
            Route::get('/pengunjung', [ContactsController::class, 'byRole'])->name('pengunjung');
            Route::get('/active', [ContactsController::class, 'byStatus'])->name('active');
            Route::get('/offline', [ContactsController::class, 'byStatus'])->name('offline');
            Route::get('/frozen', [ContactsController::class, 'byStatus'])->name('frozen');
            Route::get('/blocked', [ContactsController::class, 'byStatus'])->name('blocked');
            Route::get('/deactivated', [ContactsController::class, 'byStatus'])->name('deactivated');
        });

        // Detail user (UUID)
        Route::get('/user/{uuid}', function ($uuid) {
            $user = User::with(['level', 'status', 'jabatans'])->where('user_uuid', $uuid)->firstOrFail();
            return view('owner.contacts.user', compact('user'));
        })->name('user');

        /* ---------- Chats ---------- */

        Route::prefix('chats')->name('chats.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/private/{uuid?}', [ChatController::class, 'private'])->name('private');
            Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
            Route::post('/poll', [ChatController::class, 'getNewMessages'])
                ->middleware('throttle:60,1')
                ->name('poll');
            Route::get('/search-users', [ChatController::class, 'searchUsers'])->name('search');
        });

        /* ---------- Groups / Messages / Security ---------- */

        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/', fn () => view('owner.groups.index'))->name('index');
            Route::get('/create', fn () => view('owner.groups.create'))->name('create');
        });

        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/inbox', fn () => view('owner.messages.inbox'))->name('inbox');
            Route::get('/outbox', fn () => view('owner.messages.outbox'))->name('outbox');
            Route::get('/rejected', fn () => view('owner.messages.rejected'))->name('rejected');
        });

        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/blocked-words', fn () => view('owner.security.blocked-words'))->name('blocked-words');
            Route::get('/chat-logs', fn () => view('owner.security.chat-logs'))->name('chat-logs');
        });

        /* ---------- Management ---------- */

        Route::prefix('management')->name('management.')->group(function () {

            /* Dashboard */
            Route::get('/', function () {
                $totalUsers      = User::count();
                $usersWithQr     = UserQrCode::distinct('user_id')->count('user_id');
                $usersWithoutQr  = $totalUsers - $usersWithQr;
                $qrBlocked       = UserQrCode::where('is_active', 0)->count();
                $usersBlocked    = User::where('is_active', false)->count();
                $roles           = \App\Models\Level::all();
                $totalMembers    = User::where('level_id', 5)->where('name', '!=', 'Anggota Umum')->count();
                $totalOfficials  = User::where('level_id', 4)->count();
                $totalPengunjung = User::where('level_id', 6)->orWhere('account_type', 'guest')->count();
                $totalBlocked    = User::where('is_active', false)->orWhereNotNull('locked_until')->count();
                $totalOwner      = User::where('level_id', 1)->count();
                $totalSuperadmin = User::where('level_id', 2)->count();
                $totalKetua      = User::where('level_id', 3)->count();
                $totalActive     = User::where('is_active', true)->count();
                $totalVerified   = User::where('is_verified', true)->count();
                $totalQR         = UserQrCode::whereNull('deleted_at')->count();
                $qrActive        = UserQrCode::whereNull('deleted_at')
                    ->whereHas('user', fn ($q) => $q->where('is_active', true))
                    ->count();
                $totalEvents     = Event::where('status', 'active')->count();

                $stats = [
                    ['label' => 'Total Anggota',      'value' => $totalMembers,   'icon' => 'fa-users',      'color' => 'blue',   'trend' => '+12%',         'trendType' => 'up'],
                    ['label' => 'Total Pengurus',     'value' => $totalOfficials, 'icon' => 'fa-user-tie',   'color' => 'green',  'trend' => '+3',           'trendType' => 'up'],
                    ['label' => 'Menunggu Approval',  'value' => 7,               'icon' => 'fa-clock',      'color' => 'yellow', 'trend' => '7 perlu review', 'trendType' => 'neutral'],
                    ['label' => 'QR Aktif',           'value' => $qrActive,       'icon' => 'fa-qrcode',     'color' => 'cyan',   'trend' => ($qrActive > 0 ? round(($qrActive / max($totalQR, 1)) * 100) . '% aktif' : '0%'), 'trendType' => ($qrActive > 0 ? 'up' : 'neutral')],
                ];

                $extraStats = [
                    ['label' => 'Owner',         'value' => $totalOwner,      'icon' => 'fa-crown',      'color' => 'amber'],
                    ['label' => 'Superadmin',    'value' => $totalSuperadmin, 'icon' => 'fa-shield-alt', 'color' => 'red'],
                    ['label' => 'Ketua',         'value' => $totalKetua,      'icon' => 'fa-users-cog',  'color' => 'purple'],
                    ['label' => 'Pengunjung',    'value' => $totalPengunjung, 'icon' => 'fa-globe',      'color' => 'teal'],
                    ['label' => 'Akun Diblokir', 'value' => $totalBlocked,    'icon' => 'fa-ban',        'color' => 'red'],
                ];

                $quickActions = [
                    ['label' => 'Tambah Anggota', 'route' => 'owner.management.members.create',        'icon' => 'fa-user-plus',   'color' => 'blue'],
                    ['label' => 'Data Anggota',   'route' => 'owner.management.members.index',         'icon' => 'fa-users',       'color' => 'indigo'],
                    ['label' => 'QR Code',        'route' => 'owner.management.qr.index',              'icon' => 'fa-qrcode',      'color' => 'cyan'],
                    ['label' => 'Kelompok',       'route' => 'owner.management.groups.index',          'icon' => 'fa-layer-group', 'color' => 'purple'],
                    ['label' => 'Data Pengurus',  'route' => 'owner.officials.index',                  'icon' => 'fa-user-tie',    'color' => 'green'],
                    ['label' => 'Struktur',       'route' => 'owner.officials.structure.index',        'icon' => 'fa-sitemap',     'color' => 'amber'],
                    ['label' => 'Jabatan',        'route' => 'owner.officials.positions.index',        'icon' => 'fa-briefcase',   'color' => 'pink'],
                    ['label' => 'Audit',          'route' => 'owner.officials.audit.index',            'icon' => 'fa-history',     'color' => 'gray'],
                ];

                $activities = AuditLog::with('user')
                    ->latest()
                    ->take(6)
                    ->get()
                    ->map(function ($log) {
                        $colors = ['login' => 'blue', 'logout' => 'gray', 'register' => 'green', 'create' => 'cyan', 'update' => 'amber', 'delete' => 'red'];
                        $icons  = ['login' => 'fa-sign-in-alt', 'logout' => 'fa-sign-out-alt', 'register' => 'fa-user-plus'];
                        return (object) [
                            'user'   => $log->user->name ?? 'Sistem',
                            'action' => $log->description ?? $log->action,
                            'time'   => $log->created_at->diffForHumans(),
                            'color'  => $colors[$log->action] ?? 'gray',
                            'icon'   => $icons[$log->action] ?? 'fa-circle',
                        ];
                    });

                $allUsers = User::with(['level', 'status', 'university', 'major'])->paginate(15);

                return view('owner.management.dashboard', compact(
                    'stats', 'extraStats', 'quickActions', 'activities', 'allUsers',
                    'totalActive', 'totalVerified', 'totalQR', 'qrActive', 'totalEvents'
                ));
            })->name('dashboard');

            /* ---------- Members ---------- */

            Route::prefix('members')->name('members.')->group(function () {
                Route::get('/', function () {
                    $members = User::where('level_id', 5)
                        ->where('name', '!=', 'Anggota Umum')
                        ->with('level')
                        ->paginate(15);
                    return view('owner.management.members', compact('members'));
                })->name('index');

                Route::get('/create', [MemberController::class, 'create'])->name('create');
                Route::post('/', [MemberController::class, 'store'])->name('store');
                Route::get('/check', [MemberController::class, 'checkUnique'])->name('check');
                Route::get('/history', fn () => view('owner.management.members.history'))->name('history');

                // Import
                Route::prefix('import')->name('import.')->group(function () {
                    Route::get('/', [MemberController::class, 'importForm'])->name('form');
                    Route::post('/', [MemberController::class, 'importProcess'])->name('process');
                    Route::post('/preview', [MemberController::class, 'importPreview'])->name('preview');
                    Route::post('/store', [MemberController::class, 'importStore'])->name('store');
                    Route::post('/update-card', [MemberController::class, 'importUpdateCard'])->name('update-card');
                    Route::post('/delete-card', [MemberController::class, 'importDeleteCard'])->name('delete-card');
                    Route::get('/get-card', [MemberController::class, 'importGetCard'])->name('get-card');
                    Route::get('/success', [MemberController::class, 'importSuccess'])->name('success');
                });

                Route::post('/sync', [MemberController::class, 'syncMembers'])->name('sync');

                // CRUD dinamis
                Route::get('/{id}', function ($id) {
                    $member = User::findOrFail($id);
                    return view('owner.management.members.show', compact('member'));
                })->whereNumber('id')->name('show');

                Route::get('/{id}/edit', function ($id) {
                    $member = User::findOrFail($id);
                    return view('owner.management.members.edit', compact('member'));
                })->whereNumber('id')->name('edit');

                Route::delete('/{id}', function ($id) {
                    User::findOrFail($id)->delete();
                    return redirect()->route('owner.management.members.index')->with('success', 'Anggota berhasil dihapus.');
                })->whereNumber('id')->name('destroy');
            });

            // Users detail
            Route::get('/users/{id}', function ($id) {
                $user = User::with(['level', 'status', 'university', 'major'])->findOrFail($id);
                return view('owner.management.users.show', compact('user'));
            })->whereNumber('id')->name('users.show');

            /* ---------- Officials (management) ---------- */

            Route::prefix('officials')->name('officials.')->group(function () {
                Route::get('/', function () {
                    $officials = User::where('level_id', 4)->paginate(15);
                    return view('owner.management.officials', compact('officials'));
                })->name('index');
                Route::get('/create', fn () => view('owner.management.officials.create'))->name('create');
                Route::get('/{id}', function ($id) {
                    $official = User::findOrFail($id);
                    return view('owner.management.officials.show', compact('official'));
                })->whereNumber('id')->name('show');
                Route::get('/{id}/edit', function ($id) {
                    $official = User::findOrFail($id);
                    return view('owner.management.officials.edit', compact('official'));
                })->whereNumber('id')->name('edit');
                Route::delete('/{id}', function ($id) {
                    User::findOrFail($id)->delete();
                    return redirect()->route('owner.management.officials.index')->with('success', 'Pengurus berhasil dihapus.');
                })->whereNumber('id')->name('destroy');
            });

            /* ---------- NFC ---------- */

            Route::prefix('nfc')->name('nfc.')->group(function () {

    // ---- Halaman utama ----
    Route::get('/',                 [NfcController::class, 'index'])->name('index');
    Route::get('/generate',         [NfcController::class, 'generate'])->name('generate');
    Route::post('/generate',        [NfcController::class, 'store'])->name('generate.store');
    Route::get('/history',          [NfcController::class, 'history'])->name('history');
Route::post('/{nfc}/assign-identifier', [NfcController::class, 'assignIdentifier'])
    ->name('assign-identifier');
    Route::post('/bulk-assign-identifier', [NfcController::class, 'bulkAssignIdentifier'])
    ->name('bulk-assign-identifier');

Route::get('/stats', [NfcController::class, 'stats'])
    ->name('stats');
    // ---- Aksi per-credential (pakai route model binding nfc_uuid) ----
    Route::post('/{nfc}/revoke',    [NfcController::class, 'revoke'])->name('revoke');
    Route::post('/{nfc}/move',      [NfcController::class, 'move'])->name('move');
Route::post('/bulk-revoke', [NfcController::class, 'bulkRevoke'])->name('bulk-revoke');
    // ---- Download file ----
    Route::get('/download/{user_id}/{format}', [NfcController::class, 'download'])
        ->whereNumber('user_id')
        ->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'pdf'])
        ->name('download');

    // ---- Hapus (soft delete) ----
    Route::delete('/{id}', [NfcController::class, 'destroy'])
        ->whereNumber('id')
        ->name('destroy');

    // ---- Testing & upload (helper) ----
    Route::get('/testing',          [NfcController::class, 'testing'])->name('testing');
    Route::post('/testing/validate',[NfcController::class, 'validate'])
        ->middleware('throttle:20,1')->name('testing.validate');
    Route::post('/upload',          [NfcController::class, 'uploadFile'])->name('upload');
    Route::post('/upload-image',    [NfcController::class, 'uploadImage'])->name('upload-image');
});

            /* ---------- Testing ---------- */

            Route::prefix('testing')->name('testing.')->group(function () {
                Route::get('/', fn () => view('owner.management.testing.index'))->name('index');
                Route::get('/qr', fn () => view('owner.management.testing.qr'))->name('qr');
                Route::post('/qr/validate', [TestingController::class, 'validateQr'])->name('qr.validate');
                Route::get('/nim', fn () => view('owner.management.testing.nim'))->name('nim');
                Route::post('/nim/find', [TestingController::class, 'findUserByNim'])->name('nim.find');
                Route::get('/identity', fn () => view('owner.management.testing.identity'))->name('identity');
                Route::post('/identity/test', [TestingController::class, 'identityTest'])->name('identity.test');
                Route::get('/validate', fn () => view('owner.management.testing.validate'))->name('validate');
                Route::post('/validate/deep', [TestingController::class, 'deepValidate'])->name('validate.deep');
                Route::post('/upload-qr', [TestingController::class, 'uploadQr'])->name('upload-qr');
            });

            /* ---------- QR ---------- */

            Route::prefix('qr')->name('qr.')->group(function () {

                Route::get('/', function () {
                    $totalUsers     = User::count();
                    $usersWithQr    = UserQrCode::distinct('user_id')->count('user_id');
                    $usersWithoutQr = $totalUsers - $usersWithQr;
                    $qrBlocked      = UserQrCode::where('is_active', 0)->count();
                    $usersBlocked   = User::where('is_active', false)->count();
                    $roles          = \App\Models\Level::all();

                    $query = User::whereHas('qrCodes', fn ($q) => $q->whereNull('deleted_at'))->with('level');
                    if (request()->filled('role')) {
                        $query->where('level_id', request('role'));
                    }
                    $qrUsers = $query->paginate(15);

                    return view('owner.management.qr.index', compact(
                        'qrUsers', 'usersWithoutQr', 'usersWithQr', 'qrBlocked', 'usersBlocked', 'roles'
                    ));
                })->name('index');

                Route::get('/generate', function () {
                    $users = User::with('level')->get();
                    $roles = \App\Models\Level::all();
                    return view('owner.management.qr.generate', compact('users', 'roles'));
                })->name('generate');

                Route::post('/generate', function (Request $request) {
                    $request->validate([
                        'user_ids'   => 'required|array|min:1',
                        'user_ids.*' => 'exists:users,id',
                        'expired_at' => 'nullable|date',
                        'replace'    => 'nullable|boolean',
                        'format'     => 'nullable|string|in:png,jpg,jpeg,webp',
                    ]);

                    $replace   = $request->boolean('replace', false);
                    $expiredAt = $request->expired_at ? \Carbon\Carbon::parse($request->expired_at) : null;
                    $format    = $request->input('format', 'png');
                    $success   = 0;
                    $skipped   = 0;
                    $qrService = app(QrCodeService::class);

                    foreach ($request->input('user_ids') as $userId) {
                        $user = User::find($userId);
                        if (!$user) continue;
                        $qr = $qrService->generateForUser($user, [
                            'replace'    => $replace,
                            'expired_at' => $expiredAt,
                            'format'     => $format,
                        ]);
                        $qr ? $success++ : $skipped++;
                    }

                    $msg = "✅ Berhasil generate $success QR Code format $format.";
                    if ($skipped > 0) $msg .= " ⚠️ $skipped user dilewati.";

                    return redirect()->route('owner.management.qr.index')->with('success', $msg);
                })->name('generate.store');

                Route::post('/repair/{qr_id}', function ($qr_id, Request $request) {
                    $qr = UserQrCode::findOrFail($qr_id);
                    if ($qr->is_revoked || !$qr->is_active) {
                        return back()->with('error', '❌ QR tidak dapat diperbaiki (dicabut / tidak aktif).');
                    }
                    $format = $request->input('format', 'png');
                    $repaired = app(QrCodeService::class)->repairQr($qr, $format);
                    if ($repaired) {
                        \Log::info('QR repaired', ['qr_uuid' => $qr->qr_uuid, 'user_id' => $qr->user_id, 'by' => auth()->id()]);
                        return back()->with('success', '✅ QR berhasil diperbaiki.');
                    }
                    return back()->with('error', '❌ Gagal memperbaiki QR.');
                })->whereNumber('qr_id')->name('qr.repair');

                Route::get('/massal', function () {
                    $users = User::paginate(15);
                    $roles = \App\Models\Level::all();
                    return view('owner.management.qr.massal', compact('users', 'roles'));
                })->name('massal');

                Route::post('/massal', function (Request $request) {
                    $userIds = $request->input('user_ids', []);
                    if (empty($userIds)) return back()->with('error', 'Pilih minimal satu user.');

                    $count = 0;
                    $qrService = app(QrCodeService::class);
                    foreach ($userIds as $userId) {
                        $user = User::find($userId);
                        if (!$user) continue;
                        if ($user->qrCodes()->where('is_active', true)->exists()) continue;
                        if ($qrService->generateForUser($user, ['replace' => false])) $count++;
                    }
                    return redirect()->route('owner.management.qr.index')->with('success', "Berhasil generate $count QR.");
                })->name('massal.store');

                Route::get('/konversi', fn () => view('owner.management.qr.konversi'))->name('konversi');
                Route::post('/konversi', function () {
                    $users = User::whereNotNull('qr_token')->whereDoesntHave('qrCodes')->get();
                    $count = 0;
                    $qrService = app(QrCodeService::class);
                    foreach ($users as $user) {
                        $qr = $qrService->generateForUser($user, ['replace' => false]);
                        if ($qr) { $qr->token = $user->qr_token; $qr->save(); $count++; }
                    }
                    return redirect()->route('owner.management.qr.index')->with('success', "Konversi selesai. $count QR dipindahkan.");
                })->name('konversi.process');

                Route::get('/test', fn () => view('owner.management.qr.test'))->name('test');
                Route::post('/test', function (Request $request) {
                    $qr = UserQrCode::where('token', $request->input('token'))->first();
                    if ($qr && $qr->is_active) {
                        return response()->json([
                            'valid' => true,
                            'user'  => $qr->user ? [
                                'name'  => $qr->user->name,
                                'nim'   => $qr->user->nim,
                                'level' => $qr->user->level->name ?? 'N/A',
                            ] : null,
                        ]);
                    }
                    return response()->json(['valid' => false, 'message' => 'QR tidak valid.']);
                })->middleware('throttle:30,1')->name('test.validate');

                Route::get('/print', function () {
                    $users = User::with('qrCodes')->get();
                    return view('owner.management.qr.print', compact('users'));
                })->name('print');
                Route::post('/print', function (Request $request) {
                    $user = User::find($request->input('user_id'));
                    if (!$user) return back()->with('error', 'User tidak ditemukan.');
                    return back()->with('success', 'Cetak kartu untuk ' . $user->name . ' sedang diproses.');
                })->name('print.process');

                Route::get('/download', [QrController::class, 'index'])->name('download');
                Route::get('/download-qr/{qr_id}/{format}', [QrController::class, 'download'])
    ->whereNumber('qr_id')
    ->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'])
    ->name('download.get');

                Route::get('/deactivate', function () {
                    $users = User::all();
                    return view('owner.management.qr.deactivate', compact('users'));
                })->name('deactivate');
                Route::post('/deactivate', function (Request $request) {
                    $user = User::find($request->input('user_id'));
                    if (!$user) return back()->with('error', 'User tidak ditemukan.');

                    $qr = $user->qrCodes()->where('is_active', true)->first();
                    if (!$qr) return back()->with('error', 'User tidak memiliki QR aktif.');

                    $qr->update([
                        'is_active'  => false,
                        'is_revoked' => true,
                        'revoked_at' => now(),
                        'revoked_by' => auth()->id(),
                        'notes'      => $request->input('reason', 'Tidak ada alasan'),
                    ]);
                    return redirect()->route('owner.management.qr.index')->with('success', 'QR dinonaktifkan.');
                })->name('deactivate.process');

                Route::get('/detail/{id}', function ($id) {
                    $user = User::with('qrCodes')->findOrFail($id);
                    return view('owner.management.qr.detail', compact('user'));
                })->whereNumber('id')->name('detail');

                Route::post('/revoke/{id}', function ($id) {
                    $user = User::findOrFail($id);
                    $qr = $user->qrCodes()->where('is_active', true)->first();
                    if (!$qr) return back()->with('error', 'User tidak memiliki QR aktif.');
                    $qr->update([
                        'is_active'  => false,
                        'is_revoked' => true,
                        'revoked_at' => now(),
                        'revoked_by' => auth()->id(),
                    ]);
                    return redirect()->route('owner.management.qr.index')->with('success', 'QR dicabut permanen.');
                })->whereNumber('id')->name('revoke');

                // Recovery
                Route::get('/recovery', [QrRecoveryController::class, 'index'])->name('recovery');
                Route::post('/recovery/preview', [QrRecoveryController::class, 'preview'])->name('recovery.preview');
                Route::post('/recovery/submit', [QrRecoveryController::class, 'submit'])->name('recovery.submit');

                // Cleanup & delete selected
               Route::delete('/cleanup-revoked', [QrController::class, 'cleanupRevoked'])->name('cleanup-revoked');
                Route::delete('/delete-selected', [QrController::class, 'deleteSelected'])->name('delete-selected');

                /* ---------- ROTATE & HISTORY QR (BARU) ---------- */
                Route::post('/rotate/{user_id}', [QrController::class, 'rotate'])
                    ->whereNumber('user_id')
                    ->name('rotate');

                Route::get('/history/{user_id}', [QrController::class, 'history'])
                    ->whereNumber('user_id')
                    ->name('history');
            });

            /* ---------- Groups / Divisions / Appointments / Violations / Archive ---------- */

            Route::prefix('groups')->name('groups.')->group(function () {
                Route::get('/', fn () => view('owner.management.groups.index'))->name('index');
                Route::get('/history', fn () => view('owner.management.groups.history'))->name('history');
                Route::get('/move/{id}', fn ($id) => view('owner.management.groups.move', compact('id')))
                    ->whereNumber('id')->name('move');
                Route::get('/{id}', fn ($id) => view('owner.management.groups.show', compact('id')))
                    ->whereNumber('id')->name('show');
            });

            Route::prefix('divisions')->name('divisions.')->group(function () {
                Route::get('/', fn () => view('owner.management.divisions.index'))->name('index');
                Route::get('/history', fn () => view('owner.management.divisions.history'))->name('history');
                Route::get('/move/{id}', fn ($id) => view('owner.management.divisions.move', compact('id')))
                    ->whereNumber('id')->name('move');
                Route::get('/{id}', fn ($id) => view('owner.management.divisions.show', compact('id')))
                    ->whereNumber('id')->name('show');
            });

            Route::prefix('appointments')->name('appointments.')->group(function () {
                Route::get('/', fn () => view('owner.management.appointments.index'))->name('index');
                Route::get('/create', fn () => view('owner.management.appointments.create'))->name('create');
                Route::get('/approval', fn () => view('owner.management.appointments.approval'))->name('approval');
                Route::get('/history', fn () => view('owner.management.appointments.history'))->name('history');
            });

            Route::prefix('violations')->name('violations.')->group(function () {
                Route::get('/', fn () => view('owner.management.violations.index'))->name('index');
                Route::get('/warning', fn () => view('owner.management.violations.warning'))->name('warning');
                Route::get('/light', fn () => view('owner.management.violations.light'))->name('light');
                Route::get('/medium', fn () => view('owner.management.violations.medium'))->name('medium');
                Route::get('/heavy', fn () => view('owner.management.violations.heavy'))->name('heavy');
                Route::get('/suspend', fn () => view('owner.management.violations.suspend'))->name('suspend');
                Route::get('/block', fn () => view('owner.management.violations.block'))->name('block');
            });

            Route::prefix('archive')->name('archive.')->group(function () {
                Route::get('/graduated', fn () => view('owner.management.archive.graduated'))->name('graduated');
                Route::get('/left', fn () => view('owner.management.archive.left'))->name('left');
                Route::get('/history', fn () => view('owner.management.archive.history'))->name('history');
            });
        }); // end management

        /* ---------- Officials (owner) ---------- */

        Route::prefix('officials')->name('officials.')->group(function () {

            // Dashboard & index
            Route::get('/', function () {
                $officials = User::whereIn('level_id', [3, 4])
                    ->with('level', 'jabatans', 'university', 'major')
                    ->orderBy('level_id')
                    ->paginate(15);
                return view('owner.officials.index', compact('officials'));
            })->name('dashboard');

            Route::get('/index', function () {
                $officials = User::whereIn('level_id', [3, 4])
                    ->with('level', 'jabatans', 'university', 'major')
                    ->orderBy('level_id')
                    ->paginate(15);
                return view('owner.officials.index', compact('officials'));
            })->name('index');

            // Create / Store / Update / Destroy
            Route::get('/create', function () {
                $universities = \App\Models\University::all();
                $majors       = \App\Models\Major::all();
                $jabatans     = \App\Models\Jabatan::all();
                return view('owner.officials.create', compact('universities', 'majors', 'jabatans'));
            })->name('create');

            Route::post('/', function (Request $request) {
                $request->validate([
                    'name'          => 'required|string|max:255',
                    'nim'           => 'required|string|max:20|unique:users,nim',
                    'email'         => 'required|email|unique:users,email',
                    'phone'         => 'required|string|max:20',
                    'universitas'   => 'required|string|max:255',
                    'program_studi' => 'required|string|max:255',
                    'level_id'      => 'required|in:3,4',
                    'jabatan_id'    => 'nullable|exists:jabatans,id',
                    'username'      => 'nullable|string|max:100|unique:users,username',
                    'password'      => 'required|string|min:8|confirmed',
                    'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'is_active'     => 'boolean',
                    'is_verified'   => 'boolean',
                ]);

                $university = \App\Models\University::firstOrCreate(['name' => $request->universitas]);
                $major = \App\Models\Major::firstOrCreate([
                    'name'          => $request->program_studi,
                    'university_id' => $university->id,
                ]);

                $avatarPath = $request->hasFile('avatar')
                    ? $request->file('avatar')->store('avatars', 'public')
                    : null;

                $prefix = $request->level_id == 3 ? 'UKM-KETUA-' : 'UKM-PENG-';
                $count  = User::where('level_id', $request->level_id)->count() + 1;
                $memberNumber = $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);

                $user = User::create([
                    'user_uuid'     => (string) \Illuminate\Support\Str::uuid(),
                    'nim'           => $request->nim,
                    'name'          => $request->name,
                    'username'      => $request->username ?? strtolower($request->nim),
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'university_id' => $university->id,
                    'major_id'      => $major->id,
                    'level_id'      => $request->level_id,
                    'status_id'     => 2,
                    'password'      => bcrypt($request->password),
                    'account_type'  => 'official',
                    'ukm_id'        => 1,
                    'is_active'     => $request->boolean('is_active', true),
                    'is_verified'   => $request->boolean('is_verified', false),
                    'verified_at'   => $request->boolean('is_verified') ? now() : null,
                    'avatar_url'    => $avatarPath,
                    'member_number' => $memberNumber,
                ]);

                if ($request->jabatan_id) {
                    \DB::table('jabatan_user')->insert([
                        'user_id'    => $user->id,
                        'jabatan_id' => $request->jabatan_id,
                        'is_primary' => true,
                        'status'     => 'active',
                        'start_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                return redirect()->route('owner.officials.index')
                    ->with('success', 'Ketua/Pengurus berhasil ditambahkan.');
            })->name('store');

            // Struktur & placeholder pages
            Route::get('/core', fn () => view('owner.officials.core'))->name('core');
            Route::get('/assistant-secretary', [SecretaryController::class, 'dashboard'])
                ->middleware(CheckSecretaryAccess::class)->name('assistant-secretary');
            Route::get('/pj-division', fn () => view('owner.officials.pj-division'))->name('pj-division');
            Route::get('/assistant-division', fn () => view('owner.officials.assistant-division'))->name('assistant-division');
            Route::get('/division-members', fn () => view('owner.officials.division-members'))->name('division-members');

            // Secretary dashboard (root)
            Route::get('/secretary', [SecretaryController::class, 'dashboard'])
                ->middleware(CheckSecretaryAccess::class)->name('secretary');

            /* ---------- Secretary (incoming/outgoing/minutes/archive) ---------- */

            Route::middleware(CheckSecretaryAccess::class)
                ->prefix('secretary')
                ->name('secretary.')
                ->group(function () {
                    Route::get('/dashboard', [SecretaryController::class, 'dashboard'])->name('dashboard');

                    // Incoming mails
                    Route::resource('incoming-mails', IncomingMailController::class)
                        ->names('incoming-mails');
                    Route::post('incoming-mails/{incomingMail}/forward', [IncomingMailController::class, 'forward'])
                        ->name('incoming-mails.forward');
                    Route::post('incoming-mails/{incomingMail}/archive', [IncomingMailController::class, 'archive'])
                        ->name('incoming-mails.archive');

                    // Outgoing mails
                    Route::resource('outgoing-mails', OutgoingMailController::class)
                        ->names('outgoing-mails');

                    // Minutes
                    Route::resource('minutes', MinutesController::class)->names('minutes');
                    Route::post('minutes/{minute}/archive', [MinutesController::class, 'archive'])->name('minutes.archive');
                    Route::post('minutes/{minute}/unarchive', [MinutesController::class, 'unarchive'])->name('minutes.unarchive');
                    Route::get('minutes/{minute}/pdf', [MinutesController::class, 'exportPdf'])->name('minutes.pdf');
                    Route::get('minutes/booking-data/{booking}', [MinutesController::class, 'getBookingData'])->name('minutes.booking-data');
                    Route::get('attachments/{attachment}/download', [MinutesController::class, 'downloadAttachment'])->name('minutes.download-attachment');
                    Route::delete('attachments/{attachment}', [MinutesController::class, 'deleteAttachment'])->name('minutes.delete-attachment');

                    // Archive
                    Route::resource('archives', ArchiveController::class)->names('archives');

                    // Room QR download
                    Route::get('rooms/{room}/download-qr/{format}', [RoomController::class, 'downloadQr'])
                        ->whereNumber('room')
                        ->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'svg'])
                        ->name('rooms.download-qr');
                });

            // Dynamic routes (paling bawah)
            Route::get('/{id}/edit', function ($id) {
                $official = User::findOrFail($id);
                return view('owner.officials.edit', compact('official'));
            })->whereNumber('id')->name('edit');

            Route::put('/{id}', function (Request $request, $id) {
                return redirect()->route('owner.officials.index')->with('success', 'Pengurus berhasil diperbarui (placeholder).');
            })->whereNumber('id')->name('update');

            Route::delete('/{id}', function ($id) {
                User::findOrFail($id)->delete();
                return redirect()->route('owner.officials.index')->with('success', 'Pengurus berhasil dihapus.');
            })->whereNumber('id')->name('destroy');

            /* ---------- Structure ---------- */

            Route::prefix('structure')->name('structure.')->group(function () {
                Route::get('/', fn () => view('owner.officials.structure.index'))->name('index');
                Route::get('/owner', fn () => view('owner.officials.structure.owner'))->name('owner');
                Route::get('/superadmin', fn () => view('owner.officials.structure.superadmin'))->name('superadmin');
                Route::get('/ketua', fn () => view('owner.officials.structure.ketua'))->name('ketua');
                Route::get('/pengurus-inti', fn () => view('owner.officials.structure.pengurus-inti'))->name('pengurus-inti');
                Route::get('/sekretaris', fn () => view('owner.officials.structure.sekretaris'))->name('sekretaris');
                Route::get('/asisten-sekretaris', fn () => view('owner.officials.structure.asisten-sekretaris'))->name('asisten-sekretaris');
                Route::get('/pj-divisi', fn () => view('owner.officials.structure.pj-divisi'))->name('pj-divisi');
                Route::get('/asisten-divisi', fn () => view('owner.officials.structure.asisten-divisi'))->name('asisten-divisi');
                Route::get('/anggota-divisi', fn () => view('owner.officials.structure.anggota-divisi'))->name('anggota-divisi');
            });

            /* ---------- QR Pengurus ---------- */

            Route::prefix('qr')->name('qr.')->group(function () {
                Route::get('/', function () {
                    $officials = User::whereIn('level_id', [3, 4])
                        ->with('level')->orderBy('level_id')->paginate(15);
                    return view('owner.officials.index', compact('officials'));
                })->name('dashboard');

                Route::get('/index', function () {
                    $officials = User::whereIn('level_id', [3, 4])
                        ->with('level')->orderBy('level_id')->paginate(15);
                    return view('owner.officials.index', compact('officials'));
                })->name('index');

                Route::get('/generate', function () {
                    $officials = User::where('level_id', 4)->get();
                    return view('owner.officials.qr.generate', compact('officials'));
                })->name('generate');
                Route::post('/generate', fn () => redirect()
                    ->route('owner.officials.qr.index')
                    ->with('success', 'QR berhasil digenerate (placeholder).'))->name('generate.store');

                Route::get('/massal', function () {
                    $officials = User::where('level_id', 4)->paginate(15);
                    return view('owner.officials.qr.massal', compact('officials'));
                })->name('massal');
                Route::post('/massal', fn () => redirect()
                    ->route('owner.officials.qr.index')
                    ->with('success', 'QR massal berhasil dibuat (placeholder).'))->name('massal.store');

                Route::get('/testing', fn () => view('owner.officials.qr.testing'))->name('testing');
                Route::post('/testing', fn () => response()->json(['valid' => true]))->name('testing.validate');

                Route::get('/print-card', function () {
                    $officials = User::where('level_id', 4)->get();
                    return view('owner.officials.qr.print-card', compact('officials'));
                })->name('print-card');
                Route::post('/print-card', fn () => back()->with('success', 'Print card sedang diproses (placeholder).'))->name('print-card.process');

                Route::get('/detail/{id}', function ($id) {
                    $user = User::with('qrCodes')->findOrFail($id);
                    return view('owner.officials.qr.detail', compact('user'));
                })->whereNumber('id')->name('detail');

                Route::post('/revoke/{id}', fn ($id) => back()->with('success', 'QR berhasil dicabut (placeholder).'))
                    ->whereNumber('id')->name('revoke');
            });

            /* ---------- Divisions (officials) ---------- */

            Route::prefix('divisions')->name('divisions.')->group(function () {
                Route::get('/pengembang', fn () => view('owner.officials.divisions.pengembang'))->name('pengembang');
                Route::get('/pdd', fn () => view('owner.officials.divisions.pdd'))->name('pdd');
                Route::get('/{id}', fn ($id) => view('owner.officials.divisions.show', ['id' => $id]))
                    ->whereNumber('id')->name('show');
            });

            /* ---------- Positions ---------- */

            Route::prefix('positions')->name('positions.')->group(function () {
                Route::get('/', fn () => view('owner.officials.positions.index'))->name('index');
                Route::get('/appoint', fn () => view('owner.officials.positions.appoint'))->name('appoint');
                Route::get('/demote', fn () => view('owner.officials.positions.demote'))->name('demote');
                Route::get('/transfer', fn () => view('owner.officials.positions.transfer'))->name('transfer');
                Route::get('/terminate', fn () => view('owner.officials.positions.terminate'))->name('terminate');
                Route::get('/history', fn () => view('owner.officials.positions.history'))->name('history');
            });

            /* ---------- Audit (officials) ---------- */

            Route::prefix('audit')->name('audit.')->group(function () {
                Route::get('/', fn () => view('owner.officials.audit.index'))->name('index');
                Route::get('/changes', fn () => view('owner.officials.audit.changes'))->name('changes');
                Route::get('/appointments', fn () => view('owner.officials.audit.appointments'))->name('appointments');
                Route::get('/demotions', fn () => view('owner.officials.audit.demotions'))->name('demotions');
                Route::get('/transfers', fn () => view('owner.officials.audit.transfers'))->name('transfers');
                Route::get('/terminations', fn () => view('owner.officials.audit.terminations'))->name('terminations');
            });
        }); // end officials

        /* ---------- Attendance ---------- */

        Route::prefix('attendance')->name('attendance.')->group(function () {

            /* Audit */
            Route::prefix('audit')->name('audit.')->group(function () {
                Route::get('/', [AttendanceAuditController::class, 'index'])->name('index');
                Route::get('/export', [AttendanceAuditController::class, 'export'])->name('export');
                Route::get('/verify', [AttendanceAuditController::class, 'verify'])->name('verify');
                Route::post('/alerts/{alert}/ack', [AttendanceAuditController::class, 'acknowledgeAlert'])->name('alerts.ack');
                Route::get('/{log}', [AttendanceAuditController::class, 'show'])->name('show');
            });

            /* Penalties */
            Route::prefix('penalties')->name('penalties.')->group(function () {
                Route::get('/', [AttendancePenaltyController::class, 'index'])->name('index');
                Route::get('/export', [AttendancePenaltyController::class, 'export'])->name('export');
                Route::post('/bulk', [AttendancePenaltyController::class, 'bulk'])->name('bulk');
                Route::post('/reconcile', [AttendancePenaltyController::class, 'reconcile'])->name('reconcile');
                Route::post('/disputes/{dispute}/review', [AttendancePenaltyController::class, 'disputeReview'])
                    ->whereNumber('dispute')->name('dispute.review');

                Route::prefix('{penalty}')->whereNumber('penalty')->group(function () {
                    Route::get('/', [AttendancePenaltyController::class, 'show'])->name('show');
                    Route::post('/approve', [AttendancePenaltyController::class, 'approve'])->name('approve');
                    Route::post('/reject', [AttendancePenaltyController::class, 'reject'])->name('reject');
                    Route::post('/cancel', [AttendancePenaltyController::class, 'cancel'])->name('cancel');
                    Route::post('/waive', [AttendancePenaltyController::class, 'waive'])->name('waive');
                    Route::post('/reverse', [AttendancePenaltyController::class, 'reverse'])->name('reverse');
                    Route::post('/pay', [AttendancePenaltyController::class, 'pay'])->name('pay');
                    Route::post('/dispute', [AttendancePenaltyController::class, 'disputeStore'])->name('dispute.store');
                });
            });

            /* Static */
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/create', [AttendanceController::class, 'create'])->name('create');
            Route::post('/', [AttendanceController::class, 'store'])->name('store');
            Route::get('/history', [AttendanceController::class, 'history'])->name('history');
            Route::get('/recap', [AttendanceController::class, 'recap'])->name('recap');
            Route::get('/recap/past', [AttendanceController::class, 'recapPast'])->name('recap.past');
            Route::get('/points', [AttendanceController::class, 'points'])->name('points');
            Route::get('/calendar', [AttendanceCalendarController::class, 'index'])->name('calendar');
            Route::get('/calendar/data', [AttendanceCalendarController::class, 'data'])
                ->middleware('throttle:30,1')->name('calendar.data');

            /* Permissions */
            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/', [AttendancePermissionController::class, 'index'])->name('index');
                Route::post('/{permission}/approve', [AttendancePermissionController::class, 'approve'])
                    ->whereNumber('permission')->name('approve');
                Route::post('/{permission}/reject', [AttendancePermissionController::class, 'reject'])
                    ->whereNumber('permission')->name('reject');
            });

            /* Dynamic {session} – HARUS PALING BAWAH */
            Route::get('/{session}', [AttendanceController::class, 'show'])->whereNumber('session')->name('show');
            Route::put('/{session}', [AttendanceController::class, 'update'])->whereNumber('session')->name('update');
            Route::post('/{session}/open', [AttendanceController::class, 'openNow'])->whereNumber('session')->name('open-now');
            Route::post('/{session}/close', [AttendanceController::class, 'close'])->whereNumber('session')->name('close');
            Route::post('/{session}/extend', [AttendanceController::class, 'extend'])->whereNumber('session')->name('extend');
            Route::post('/{session}/cancel', [AttendanceController::class, 'cancel'])->whereNumber('session')->name('cancel');
            Route::get('/{session}/manual', [AttendanceController::class, 'manualForm'])->whereNumber('session')->name('manual.form');
            Route::post('/{session}/manual', [AttendanceController::class, 'manualStore'])->whereNumber('session')->name('manual.store');
            Route::get('/{session}/monitor', [AttendanceController::class, 'monitor'])->whereNumber('session')->name('monitor');
        }); // end attendance

        /* ---------- Financial ---------- */

        Route::prefix('financial')->name('financial.')->group(function () {

            Route::get('/dashboard', [FinancialDashboardController::class, 'index'])->name('dashboard');

            /* Generations */
            Route::get('generations/eligible-roles', [GenerationController::class, 'eligibleRoles'])
                ->name('generations.eligible-roles');

            Route::resource('generations', GenerationController::class)
                ->names('generations')
                ->parameters(['generations' => 'generation'])
                ->whereNumber('generation');

            Route::prefix('generations/{generation}')->name('generations.')
                ->whereNumber('generation')
                ->group(function () {
                    Route::get('/search-users', [GenerationController::class, 'searchUsers'])
                        ->middleware('throttle:30,1')->name('search-users');
                    Route::get('/target', [GenerationController::class, 'targetForm'])->name('target');
                    Route::post('/target-preview', [GenerationController::class, 'targetPreview'])->name('target-preview');
                    Route::post('/target-apply', [GenerationController::class, 'targetApply'])->name('target-apply');
                });

            /* Fee Plans */
            Route::resource('fee-plans', FeePlanController::class)
                ->names('fee-plans')
                ->parameters(['fee-plans' => 'feePlan'])
                ->whereNumber('feePlan');
            Route::get('fee-plans/{feePlan}/duplicate', [FeePlanController::class, 'duplicate'])
                ->whereNumber('feePlan')->name('fee-plans.duplicate');

            /* Invoices */
            Route::prefix('invoices')->name('invoices.')->group(function () {
                Route::get('/', [InvoiceController::class, 'index'])->name('index');
                Route::get('/generate', [InvoiceController::class, 'generate'])->name('generate');
                Route::get('/check-tagihan', [InvoiceController::class, 'checkTagihan'])->name('check-tagihan');
                Route::get('/check-pengumuman', [InvoiceController::class, 'checkPengumuman'])->name('check-pengumuman');
                Route::get('/user-tagihan/{user}', [InvoiceController::class, 'userTagihan'])
                    ->whereNumber('user')->name('user-tagihan');
                Route::get('/user-pengumuman/{user}', [InvoiceController::class, 'userPengumuman'])
                    ->whereNumber('user')->name('user-pengumuman');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])
                    ->whereNumber('invoice')->name('show');
            });

            /* Payments */
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index');
                Route::get('/{payment}', [PaymentController::class, 'show'])
                    ->whereNumber('payment')->name('show');
            });

            /* Penalty Rules */
            Route::resource('penalty-rules', PenaltyController::class)
                ->names('penalty-rules')
                ->parameters(['penalty-rules' => 'penaltyRule'])
                ->whereNumber('penaltyRule');

            /* Points */
            Route::prefix('points')->name('points.')->group(function () {
                Route::get('/', [PointController::class, 'index'])->name('index');
                Route::get('/leaderboard', [PointController::class, 'leaderboard'])->name('leaderboard');
            });

            /* Campaigns */
            Route::resource('campaigns', CampaignController::class)
                ->names('campaigns')
                ->parameters(['campaigns' => 'campaign'])
                ->whereNumber('campaign');

            /* Holidays & Academic Periods */
            Route::resource('holidays', CalendarController::class)
                ->names('holidays')
                ->parameters(['holidays' => 'holiday'])
                ->whereNumber('holiday');

            Route::resource('academic-periods', AcademicPeriodController::class)
                ->names('academic-periods')
                ->parameters(['academic-periods' => 'academicPeriod'])
                ->whereNumber('academicPeriod');

            /* Reports */
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [ReportController::class, 'index'])->name('index');
                Route::get('/export', [ReportController::class, 'export'])->name('export');
            });

            /* Reactivations */
            Route::prefix('reactivations')->name('reactivations.')->group(function () {
                Route::get('/', [ReactivationController::class, 'index'])->name('index');
                Route::post('/{id}/approve', [ReactivationController::class, 'approve'])
                    ->whereNumber('id')->name('approve');
                Route::post('/{id}/reject', [ReactivationController::class, 'reject'])
                    ->whereNumber('id')->name('reject');
            });
        }); // end financial

        /* ---------- Game Center ---------- */

        Route::prefix('game')->name('game.')->group(function () {

            Route::get('/dashboard', [GameController::class, 'dashboard'])->name('dashboard');

            Route::prefix('games')->name('games.')->group(function () {
                Route::get('/', [GameController::class, 'index'])->name('index');
                Route::get('/trash', [GameController::class, 'trash'])->name('trash');
                Route::patch('/restore/{id}', [GameController::class, 'restore'])->whereNumber('id')->name('restore');
                Route::delete('/force/{id}', [GameController::class, 'forceDestroy'])->whereNumber('id')->name('force');

                Route::get('/create', [GameController::class, 'create'])->name('create');
                Route::get('/create-form', [GameController::class, 'createForm'])->name('create-form');
                Route::post('/', [GameController::class, 'store'])->name('store');

                Route::post('/submit-result', [GameController::class, 'submitResult'])->name('submit-result');
                Route::post('/submit-quiz', [GameController::class, 'submitQuiz'])->name('submit-quiz');
                Route::post('/diagnostic-preview', [GameController::class, 'diagnosticPreview'])->name('diagnostic.preview');

                Route::get('/{game}', [GameController::class, 'show'])->whereNumber('game')->name('show');
                Route::get('/{game}/edit', [GameController::class, 'edit'])->whereNumber('game')->name('edit');
                Route::put('/{game}', [GameController::class, 'update'])->whereNumber('game')->name('update');
                Route::delete('/{game}', [GameController::class, 'destroy'])->whereNumber('game')->name('destroy');

                Route::get('/{game}/gameplay/{type}/{token?}', [GameController::class, 'playGameplay'])
                    ->whereNumber('game')->name('gameplay');
                Route::get('/{game}/testing/{token}', [GameController::class, 'testingWithToken'])
                    ->whereNumber('game')->name('testing.token');
                Route::get('/{game}/result/{session}', [GameController::class, 'showResult'])
                    ->whereNumber('game')->name('result');
                Route::get('/{game}/diagnostic', [GameController::class, 'diagnostic'])
                    ->whereNumber('game')->name('diagnostic');
                Route::post('/{game}/fix-diagnostic', [GameController::class, 'fixDiagnostic'])
                    ->whereNumber('game')->name('fix-diagnostic');
                Route::patch('/{game}/publish', [GameController::class, 'publish'])
                    ->whereNumber('game')->name('publish');
                Route::patch('/{game}/unpublish', [GameController::class, 'unpublish'])
                    ->whereNumber('game')->name('unpublish');
                Route::get('/{game}/settings', [GameController::class, 'settings'])
                    ->whereNumber('game')->name('settings');
                Route::get('/{game}/qr', [GameController::class, 'generateQr'])
                    ->whereNumber('game')->name('qr');
            });

            /* Leaderboard */
            Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
            Route::post('/leaderboard/rebuild', [LeaderboardController::class, 'rebuild'])->name('leaderboard.rebuild');
            Route::get('/games/{game}/leaderboard', [LeaderboardController::class, 'getLeaderboard'])
                ->whereNumber('game')->name('games.leaderboard');

            /* Players */
            Route::prefix('players')->name('players.')->group(function () {
                Route::get('/', [PlayerController::class, 'index'])->name('index');
                Route::get('/{player}', [PlayerController::class, 'show'])->whereNumber('player')->name('show');
                Route::get('/{player}/edit', [PlayerController::class, 'edit'])->whereNumber('player')->name('edit');
                Route::put('/{player}', [PlayerController::class, 'update'])->whereNumber('player')->name('update');
                Route::delete('/{player}', [PlayerController::class, 'destroy'])->whereNumber('player')->name('destroy');
            });

            /* Challenges */
            Route::prefix('challenges')->name('challenges.')->group(function () {
                Route::get('/', [ChallengeController::class, 'index'])->name('index');
                Route::get('/create', [ChallengeController::class, 'create'])->name('create');
                Route::post('/', [ChallengeController::class, 'store'])->name('store');
                Route::get('/{challenge}', [ChallengeController::class, 'show'])->whereNumber('challenge')->name('show');
                Route::get('/{challenge}/edit', [ChallengeController::class, 'edit'])->whereNumber('challenge')->name('edit');
                Route::put('/{challenge}', [ChallengeController::class, 'update'])->whereNumber('challenge')->name('update');
                Route::delete('/{challenge}', [ChallengeController::class, 'destroy'])->whereNumber('challenge')->name('destroy');
                Route::post('/{challenge}/toggle', [ChallengeController::class, 'toggle'])->whereNumber('challenge')->name('toggle');
            });

            /* Competitions */
            Route::prefix('competitions')->name('competitions.')->group(function () {
                Route::get('/', [CompetitionController::class, 'index'])->name('index');
                Route::get('/create', [CompetitionController::class, 'create'])->name('create');
                Route::post('/', [CompetitionController::class, 'store'])->name('store');
                Route::get('/{competition}', [CompetitionController::class, 'show'])->whereNumber('competition')->name('show');
                Route::get('/{competition}/edit', [CompetitionController::class, 'edit'])->whereNumber('competition')->name('edit');
                Route::put('/{competition}', [CompetitionController::class, 'update'])->whereNumber('competition')->name('update');
                Route::delete('/{competition}', [CompetitionController::class, 'destroy'])->whereNumber('competition')->name('destroy');
                Route::post('/{competition}/participants', [CompetitionController::class, 'addParticipants'])->whereNumber('competition')->name('participants.add');
                Route::post('/{competition}/bracket/generate', [CompetitionController::class, 'generateBracket'])->whereNumber('competition')->name('bracket.generate');
                Route::get('/{competition}/match/{match}', [CompetitionController::class, 'viewMatch'])->whereNumber('competition')->whereNumber('match')->name('match');
                Route::post('/{competition}/match/{match}/start', [CompetitionController::class, 'startMatch'])->whereNumber('competition')->whereNumber('match')->name('match.start');
                Route::post('/{competition}/match/{match}/score', [CompetitionController::class, 'updateMatchScore'])->whereNumber('competition')->whereNumber('match')->name('match.score');
                Route::get('/{competition}/live', [CompetitionController::class, 'live'])->whereNumber('competition')->name('live');
                Route::get('/{competition}/live/data', [CompetitionController::class, 'getLiveData'])->whereNumber('competition')->name('live.data');
            });

            /* Testing */
            Route::get('/testing', [GameController::class, 'testing'])->name('testing');
            Route::post('/testing/load', [GameController::class, 'loadTest'])->name('testing.load');

            /* Settings */
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        }); // end game

        /* ---------- Approvals / Audit / Leaderboard / Announcements / Reports / Data / Violations / Settings ---------- */

        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', fn () => view('owner.approvals.index'))->name('index');
            Route::get('/pending', fn () => view('owner.approvals.pending'))->name('pending');
            Route::get('/history', fn () => view('owner.approvals.history'))->name('history');
            Route::get('/{id}', fn ($id) => view('owner.approvals.show', compact('id')))->name('show');
        });

        Route::prefix('audit')->name('audit.')->group(function () {
            Route::get('/', fn () => view('owner.audit.index'))->name('index');
            Route::get('/activity', fn () => view('owner.audit.activity'))->name('activity');
            Route::get('/data-changes', fn () => view('owner.audit.data-changes'))->name('data-changes');
            Route::get('/role-changes', fn () => view('owner.audit.role-changes'))->name('role-changes');
            Route::get('/security', fn () => view('owner.audit.security'))->name('security');
        });

        Route::get('/leaderboard', fn () => view('owner.leaderboard'))->name('leaderboard');
        Route::get('/assign-tasks', fn () => view('owner.assign-tasks'))->name('assign-tasks');

        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/all', fn () => view('owner.announcements.all'))->name('all');
            Route::get('/officials', fn () => view('owner.announcements.officials'))->name('officials');
            Route::get('/members', fn () => view('owner.announcements.members'))->name('members');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', fn () => view('owner.reports.index'))->name('index');
            Route::get('/analysis', fn () => view('owner.reports.analysis'))->name('analysis');
        });

        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/profile', fn () => view('owner.data.profile'))->name('profile');
            Route::get('/qr', fn () => view('owner.data.qr'))->name('qr');
            Route::get('/testing', fn () => view('owner.data.testing'))->name('testing');
        });

        Route::prefix('violations')->name('violations.')->group(function () {
            Route::get('/light', fn () => view('owner.violations.light'))->name('light');
            Route::get('/medium', fn () => view('owner.violations.medium'))->name('medium');
            Route::get('/heavy', fn () => view('owner.violations.heavy'))->name('heavy');
            Route::get('/block', fn () => view('owner.violations.block'))->name('block');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', fn () => view('owner.settings.general'))->name('index');
            Route::get('/profile', fn () => view('owner.settings.profile'))->name('profile');
            Route::get('/general', fn () => view('owner.settings.general'))->name('general');
            Route::get('/explore', fn () => view('owner.settings.explore'))->name('explore');
            Route::get('/audit', fn () => view('owner.settings.audit'))->name('audit');
        });
    }); // end owner

    /* ============================================================
     | 5.3 SUPERADMIN
     * ============================================================ */

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/all', fn () => view('superadmin.announcements.all'))->name('all');
            Route::get('/officials', fn () => view('superadmin.announcements.officials'))->name('officials');
            Route::get('/members', fn () => view('superadmin.announcements.members'))->name('members');
        });

        Route::prefix('management')->name('management.')->group(function () {
            Route::get('/members', fn () => view('superadmin.management.members'))->name('members');
            Route::get('/officials', fn () => view('superadmin.management.officials'))->name('officials');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/analysis', fn () => view('superadmin.reports.analysis'))->name('analysis');
        });

        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/all', fn () => view('superadmin.contacts.all'))->name('all');
            Route::get('/officials', fn () => view('superadmin.contacts.officials'))->name('officials');
            Route::get('/members', fn () => view('superadmin.contacts.members'))->name('members');
        });

        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/profile', fn () => view('superadmin.data.profile'))->name('profile');
            Route::get('/qr', fn () => view('superadmin.data.qr'))->name('qr');
            Route::get('/testing', fn () => view('superadmin.data.testing'))->name('testing');
        });

        Route::prefix('violations')->name('violations.')->group(function () {
            Route::get('/light', fn () => view('superadmin.violations.light'))->name('light');
            Route::get('/medium', fn () => view('superadmin.violations.medium'))->name('medium');
            Route::get('/heavy', fn () => view('superadmin.violations.heavy'))->name('heavy');
            Route::get('/block', fn () => view('superadmin.violations.block'))->name('block');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', fn () => view('superadmin.settings.general'))->name('index');
            Route::get('/profile', fn () => view('superadmin.settings.profile'))->name('profile');
            Route::get('/general', fn () => view('superadmin.settings.general'))->name('general');
            Route::get('/explore', fn () => view('superadmin.settings.explore'))->name('explore');
            Route::get('/audit', fn () => view('superadmin.settings.audit'))->name('audit');
        });

        Route::get('/notifications', fn () => view('superadmin.notifications'))->name('notifications');
    });

    /* ============================================================
     | 5.4 KETUA
     * ============================================================ */

    Route::prefix('ketua')->name('ketua.')->middleware('role:ketua')->group(function () {
        // Tambahkan route ketua di sini (placeholder)
    });

    /* ============================================================
     | 5.5 PENGURUS
     * ============================================================ */

    Route::prefix('pengurus')->name('pengurus.')->middleware('role:pengurus')->group(function () {
        Route::get('/tugas', fn () => view('pengurus.tugas'))->name('tugas');
        Route::get('/kelompok', fn () => view('pengurus.kelompok'))->name('kelompok');
        Route::get('/pengumuman', fn () => view('pengurus.pengumuman'))->name('pengumuman');
        Route::get('/kontak', fn () => view('pengurus.kontak'))->name('kontak');
        Route::get('/anggota', fn () => view('pengurus.anggota'))->name('anggota');
        Route::get('/data-diri', fn () => view('pengurus.data-diri'))->name('data-diri');
        Route::get('/pengaturan', fn () => view('pengurus.pengaturan'))->name('pengaturan');
        Route::get('/peringatan', fn () => view('pengurus.peringatan'))->name('peringatan');
        Route::get('/notifications', fn () => view('pengurus.notifications'))->name('notifications');
    });

    /* ============================================================
     | 5.6 ANGGOTA
     * ============================================================ */

    Route::middleware(['verified', 'role:anggota', 'no-cache'])
        ->prefix('anggota')
        ->name('anggota.')
        ->group(function () {
            Route::get('/dashboard', [AnggotaDashboardController::class, 'index'])->name('dashboard');
            Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
            Route::get('/poin', [PoinController::class, 'index'])->name('poin');
            Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi');
            Route::get('/event', [AnggotaEventController::class, 'index'])->name('event');
            Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat');
            Route::get('/portofolio', [PortofolioController::class, 'index'])->name('portofolio');
            Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
            Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        });

    /* ============================================================
     | 6. PROFILE & LOGOUT (semua user login)
     * ============================================================ */

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('/love/{user}', [ProfileController::class, 'toggleLove'])
            ->whereNumber('user')->name('love');
        Route::post('/follow/{user}', [ProfileController::class, 'toggleFollow'])
            ->whereNumber('user')->name('follow');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

/* ============================================================
 | 7. PUBLIC PROFILE (di luar auth)
 * ============================================================ */

Route::get('/profile/{user}', [ProfileController::class, 'showPublic'])
    ->whereNumber('user')
    ->name('profile.public');
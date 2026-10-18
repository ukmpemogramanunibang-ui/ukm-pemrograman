<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/* ============================================================
 | CONTROLLERS — Public
 * ============================================================ */
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AccountCheckController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\Ketua\AnnouncementController;

/* ============================================================
 | CONTROLLERS — Room / QR
 * ============================================================ */
use App\Http\Controllers\RoomAccessController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\QrValidationController;

/* ============================================================
 | CONTROLLERS — Game Center
 * ============================================================ */
use App\Http\Controllers\GameController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GameDiagnosticController;

/* ============================================================
 | CONTROLLERS — Auth
 * ============================================================ */
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NfcLoginController;
use App\Http\Controllers\Auth\GoogleDriveController;
use App\Http\Controllers\Auth\PasswordRecoveryController;

/* ============================================================
 | CONTROLLERS — Ketua
 * ============================================================ */
use App\Http\Controllers\Ketua\QRCodeController;
use App\Http\Controllers\KetuaDashboardController;

/* ============================================================
 | CONTROLLERS — Owner (Core)
 * ============================================================ */
use App\Http\Controllers\Owner\ContactsController;
use App\Http\Controllers\Owner\QrCleanupController;
use App\Http\Controllers\Owner\NotificationController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\Owner\RestoreController;
use App\Http\Controllers\Owner\ScheduleController;
use App\Http\Controllers\Owner\KelompokController;
use App\Http\Controllers\Owner\DivisiController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\PromoteDemoteController;
use App\Http\Controllers\Owner\SecretaryController;
use App\Http\Controllers\Owner\ChatController;
use App\Http\Controllers\Owner\IdentityVaultController;
use App\Http\Controllers\Owner\SystemHealthController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Dashboard\ItemVisibilityController;
/* ============================================================
 | CONTROLLERS — Owner (Attendance)
 * ============================================================ */
use App\Http\Controllers\Owner\AttendanceController;
use App\Http\Controllers\Owner\AttendanceAuditController;
use App\Http\Controllers\Owner\AttendanceCalendarController;
use App\Http\Controllers\Owner\AttendancePenaltyController;
use App\Http\Controllers\Owner\AttendancePermissionController;

/* ============================================================
 | CONTROLLERS — Owner (Management)
 * ============================================================ */
use App\Http\Controllers\Owner\Management\MemberController;
use App\Http\Controllers\Owner\Management\NfcController;
use App\Http\Controllers\Owner\Management\QrRecoveryController;
use App\Http\Controllers\Owner\Management\TestingController;

/* ============================================================
 | CONTROLLERS — Owner (Financial)
 * ============================================================ */
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

/* ============================================================
 | CONTROLLERS — Owner (LMS)
 * ============================================================ */
use App\Http\Controllers\Owner\Lms\DashboardController  as LmsDashboardController;
use App\Http\Controllers\Owner\Lms\GroupController      as LmsGroupController;
use App\Http\Controllers\Owner\Lms\ItemController       as LmsItemController;
use App\Http\Controllers\Owner\Lms\SubmissionController as LmsSubmissionController;
use App\Http\Controllers\Owner\Lms\MonitoringController as LmsMonitoringController;
use App\Http\Controllers\Owner\Lms\AuditController      as LmsAuditController;
use App\Http\Controllers\Owner\Lms\SettingsController   as LmsSettingsController;

/* ============================================================
 | CONTROLLERS — Secretary
 * ============================================================ */
use App\Http\Controllers\Secretary\IncomingMailController;
use App\Http\Controllers\Secretary\OutgoingMailController;
use App\Http\Controllers\Secretary\MinutesController;
use App\Http\Controllers\Secretary\ArchiveController;

/* ============================================================
 | CONTROLLERS — Anggota
 * ============================================================ */
use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Anggota\ProfilController;
use App\Http\Controllers\Anggota\PoinController;
use App\Http\Controllers\Anggota\PresensiController;
use App\Http\Controllers\Anggota\EventController as AnggotaEventController;
use App\Http\Controllers\Anggota\SertifikatController;
use App\Http\Controllers\Anggota\PortofolioController;
use App\Http\Controllers\Anggota\PengaturanController;

/* ============================================================
 | CONTROLLERS — Keputusan (Public)
 * ============================================================ */
use App\Http\Controllers\Keputusan\KeputusanController;
use App\Http\Controllers\Keputusan\RodaController;
use App\Http\Controllers\Keputusan\PollingController;
use App\Http\Controllers\Keputusan\SpinController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\UkmController;
/* ============================================================
 | SERVICES & MODELS
 * ============================================================ */
use App\Services\QrCodeService;
use App\Services\Attendance\AttendanceClockService;
use App\Models\User;
use App\Models\Event;
use App\Models\AuditLog;
use App\Models\UserQrCode;

/* ============================================================
 | MIDDLEWARE
 * ============================================================ */
use App\Http\Middleware\EnsureLmsAccess;
use App\Http\Middleware\CheckSecretaryAccess;
use App\Http\Middleware\OwnerExplorer;

/* ============================================================
 | HELPER CONSTANT — Regex UUID
 * ============================================================ */
if (! defined('UUID_REGEX')) {
    define('UUID_REGEX', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');
}


/* ═══════════════════════════════════════════════════════════
 | 1. PUBLIC
 * ═══════════════════════════════════════════════════════════ */

Route::get('/',                [LandingController::class, 'index'])->name('landing');
Route::view('/about',          'about')->name('about');
Route::view('/dokumentasi',    'dokumentasi')->name('dokumentasi');
Route::view('/prestasi',       'prestasi')->name('prestasi');
Route::view('/portfolio',      'portfolio')->name('portfolio');
Route::view('/ppt-interaktif', 'interaksi-ppt')->name('ppt.interaktif');
Route::view('/interaksi-ppt',  'interaksi-ppt')->name('interaksi.ppt');

Route::get('/member', [AnggotaController::class, 'index'])->name('member');

Route::get('/explore', function () {
    if (auth()->check()) {
        return redirect()->to(auth()->user()->getDashboardRoute());
    }
    return app(ExploreController::class)->index();
})->name('explore');

/* ---------- Keputusan (Public) ---------- */
Route::prefix('keputusan')->name('keputusan.')->group(function () {
    Route::get('/', [KeputusanController::class, 'index'])->name('index');

    Route::prefix('roda')->name('roda.')->group(function () {
        Route::get('/buat',  [RodaController::class, 'create'])->name('create');
        Route::post('/buat', [RodaController::class, 'store'])->name('store');

        Route::get('/{token}/state', [SpinController::class, 'state'])
            ->where('token', '[A-Za-z0-9]+')->middleware('throttle:120,1')->name('state');
        Route::post('/{token}/spin', [SpinController::class, 'store'])
            ->where('token', '[A-Za-z0-9]+')->middleware('throttle:10,1')->name('spin');
        Route::post('/{token}/complete/{spin}', [SpinController::class, 'complete'])
            ->where('token', '[A-Za-z0-9]+')->whereNumber('spin')->name('complete');

        Route::get('/{token}', [RodaController::class, 'show'])
            ->where('token', '[A-Za-z0-9]+')->name('show');
    });

    Route::prefix('poll')->name('poll.')->group(function () {
        Route::get('/buat',  [PollingController::class, 'create'])->name('create');
        Route::post('/buat', [PollingController::class, 'store'])->name('store');
        Route::get('/{token}', [PollingController::class, 'show'])
            ->where('token', '[A-Za-z0-9]+')->name('show');
        Route::post('/{token}/vote', [PollingController::class, 'vote'])
            ->middleware('throttle:30,1')->name('vote');
    });
});

Route::prefix('member/keputusan')->name('member.keputusan.')->group(function () {
    Route::get('/', [KeputusanController::class, 'dashboard'])->name('dashboard');
});

/* ---------- Kontak ---------- */
Route::get('/kontak',  [ContactController::class, 'index'])->name('kontak');
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:contact')->name('kontak.store');
Route::prefix('organisasi')->name('organisasi.')->group(function () {
    Route::get('/',  [OrganisasiController::class, 'index'])->name('index');

    // Setiap unit punya route & view sendiri
    Route::get('/bem',              [OrganisasiController::class, 'bem'])->name('bem');
    Route::get('/hima-informatika', [OrganisasiController::class, 'himaInformatika'])->name('hima-informatika');
    Route::get('/hima-kebidanan',   [OrganisasiController::class, 'himaKebidanan'])->name('hima-kebidanan');
    Route::get('/hima-keperawatan', [OrganisasiController::class, 'himaKeperawatan'])->name('hima-keperawatan');
    Route::get('/hima-manajemen',   [OrganisasiController::class, 'himaManajemen'])->name('hima-manajemen');
    Route::get('/hima-hukum',       [OrganisasiController::class, 'himaHukum'])->name('hima-hukum');
    Route::get('/hima-ars',         [OrganisasiController::class, 'himaArs'])->name('hima-ars');
});

/* ═══════════════ UKM ═══════════════ */
Route::prefix('ukm')->name('ukm.')->group(function () {
    Route::get('/',  [UkmController::class, 'index'])->name('index');

    Route::get('/pemrograman',   [UkmController::class, 'pemrograman'])->name('pemrograman');
    Route::get('/multimedia',    [UkmController::class, 'multimedia'])->name('multimedia');
    Route::get('/nova',          [UkmController::class, 'nova'])->name('nova');
    Route::get('/badminton',     [UkmController::class, 'badminton'])->name('badminton');
    Route::get('/basket',        [UkmController::class, 'basket'])->name('basket');
    Route::get('/teater-teras',  [UkmController::class, 'teaterTeras'])->name('teater-teras');
    Route::get('/aksara-tarana', [UkmController::class, 'aksaraTarana'])->name('aksara-tarana');
    Route::get('/savora-padus',  [UkmController::class, 'savoraPadus'])->name('savora-padus');
    Route::get('/futsal',        [UkmController::class, 'futsal'])->name('futsal');
});
/* ---------- Room Publik ---------- */
Route::prefix('room_publik')->name('room.public.')->group(function () {
    Route::get('/',              [RoomAccessController::class, 'publicIndex'])->name('index');
    Route::post('/check-availability', [RoomAccessController::class, 'publicCheckAvailability'])
        ->middleware('throttle:20,1')->name('check');
    Route::post('/nim', [RoomAccessController::class, 'publicNimLookup'])
        ->middleware('throttle:10,1')->name('nim');

    Route::get('/booking/{code}', [RoomAccessController::class, 'publicBookingStatus'])
        ->where('code', '[A-Za-z0-9\-]+')->name('booking.status');
    Route::get('/realtime/{token}', [RoomAccessController::class, 'publicRealtime'])
        ->where('token', '[A-Za-z0-9\-]+')->name('realtime');

    Route::get('/{room}',          [RoomAccessController::class, 'publicShow'])
        ->whereNumber('room')->name('show');
    Route::get('/{room}/booking',  [RoomAccessController::class, 'publicBookingForm'])
        ->whereNumber('room')->name('booking.form');
    Route::post('/{room}/booking', [RoomAccessController::class, 'publicBookingStore'])
        ->whereNumber('room')->middleware('throttle:10,1')->name('booking.store');
});

Route::get('/room/scan/{token}', function (string $token) {
    $room = \App\Models\Room::where('room_token', $token)->first();
    if (! $room) abort(404, 'QR Code tidak valid');
    return redirect()->route('room.public.index', ['room_token' => $room->room_token]);
})->where('token', '[A-Za-z0-9\-]+')->name('room.scan');

/* ---------- Cek Akun ---------- */
Route::prefix('cek-akun')->name('account.check.')->group(function () {
    Route::get('/',       [AccountCheckController::class, 'index'])->name('index');
    Route::get('/manual', [AccountCheckController::class, 'manual'])->name('manual');
    Route::get('/qr',     [AccountCheckController::class, 'qr'])->name('qr');
    Route::get('/nfc',    [AccountCheckController::class, 'nfc'])->name('nfc');
    Route::post('/resolve', [AccountCheckController::class, 'resolve'])
        ->middleware('throttle:account-check')->name('resolve');
});

/* ---------- Dev Only ---------- */
if (app()->environment('local', 'development')) {
    Route::get('/test-email', function () {
        Mail::raw('Tes email dari Laravel + Mailtrap', function ($message) {
            $message->to('ukmpemogramanunibang@gmail.com')->subject('Tes Mailtrap');
        });
        return 'Email berhasil dikirim.';
    })->name('dev.test-email');
}


/* ═══════════════════════════════════════════════════════════
 | 2. AUTH STATUS (Heartbeat)
 * ═══════════════════════════════════════════════════════════ */

Route::get('/check-auth-status', function (Request $request) {
    $user = $request->user();
    if (! $user) return response()->json(['authenticated' => false, 'redirect' => null], 401);
    return response()->json(['authenticated' => true, 'redirect' => $user->getDashboardRoute()]);
})->middleware('throttle:30,1')->name('auth.status');


/* ═══════════════════════════════════════════════════════════
 | 3. AUTH (GUEST)
 * ═══════════════════════════════════════════════════════════ */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginOptions'])->name('login');
    Route::get('/login/manual',  [LoginController::class, 'showNimForm'])->name('login.manual.form');
    Route::post('/login/manual', [LoginController::class, 'manualLogin'])->name('login.manual');
    Route::match(['get', 'post'], '/login/check-nim-json', [LoginController::class, 'checkNimJson'])
        ->middleware('throttle:30,1')->name('login.check-nim-json');

    Route::get('/login/qr',             [LoginController::class, 'showQrForm'])->name('login.qr.form');
    Route::post('/login/qr-scan',       [LoginController::class, 'qrScan'])
        ->middleware('throttle:30,1')->name('login.qr-scan');
    Route::post('/login/qr-authenticate', [LoginController::class, 'qrAuthenticate'])
        ->middleware('throttle:10,1')->name('login.qr-authenticate');

    Route::get('/login/nfc',             [NfcLoginController::class, 'showForm'])->name('nfc.login.form');
    Route::post('/login/nfc/scan',       [NfcLoginController::class, 'scan'])
        ->middleware('throttle:10,1')->name('nfc.scan');
    Route::post('/login/nfc/authenticate', [NfcLoginController::class, 'authenticate'])
        ->middleware('throttle:10,1')->name('nfc.authenticate');

    Route::get('/guest/login',     [LoginController::class, 'showGuestLogin'])->name('guest.login');
    Route::post('/guest/login',    [LoginController::class, 'guestLogin'])
        ->middleware('throttle:10,1');
    Route::get('/guest/register',  [LoginController::class, 'showGuestRegister'])->name('guest.register');
    Route::post('/guest/register', [LoginController::class, 'guestRegister'])
        ->middleware('throttle:5,1');
});


/* ═══════════════════════════════════════════════════════════
 | 4. PASSWORD RESET — OTP
 * ═══════════════════════════════════════════════════════════ */
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password',  [PasswordRecoveryController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [PasswordRecoveryController::class, 'requestOtp'])
        ->middleware('throttle:10,1')->name('password.email');
    Route::get('/forgot-password/otp',  [PasswordRecoveryController::class, 'showOtp'])->name('password.otp');
    Route::post('/forgot-password/otp', [PasswordRecoveryController::class, 'verifyOtp'])
        ->middleware('throttle:10,1')->name('password.otp.verify');
    Route::get('/forgot-password/reset', [PasswordRecoveryController::class, 'showReset'])->name('password.reset.custom');
    Route::post('/forgot-password/reset', [PasswordRecoveryController::class, 'resetPassword'])
        ->middleware('throttle:5,1')->name('password.update.custom');
});


/* ═══════════════════════════════════════════════════════════
 | EMERGENCY EXITS
 * ═══════════════════════════════════════════════════════════ */
Route::match(['get', 'post'], '/panic-exit', function () {
    try { Auth::logout(); } catch (\Throwable $e) {}
    session()->flush();
    session()->regenerate();
    cookie()->queue(cookie()->forget(config('session.cookie')));
    return redirect('/login')->with('status', 'Sesi telah dibersihkan.');
})->name('panic.exit');

Route::match(['get', 'post'], '/exit-impersonation', function () {
    $ownerId = session('impersonator_id');
    session()->forget([
        'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
        'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
        'target_user_id', 'role_slug', 'impersonator_id',
    ]);
    if ($ownerId && $owner = \App\Models\User::find($ownerId)) {
        Auth::login($owner);
        return redirect()->route('owner.dashboard')->with('success', 'Kembali ke akun Owner.');
    }
    return redirect('/login');
})->name('exit.impersonation');


/* ═══════════════════════════════════════════════════════════
 | 5. AUTHENTICATED
 * ═══════════════════════════════════════════════════════════ */
Route::middleware('auth')->group(function () {

    /* ---------- 5.1 Dashboard & Google Drive ---------- */
    Route::get('/dashboard', fn () => redirect(auth()->user()->getDashboardRoute()))->name('dashboard');

    Route::prefix('auth/google')->name('google.')->group(function () {
        Route::get('/',         [GoogleDriveController::class, 'redirect'])->name('redirect');
        Route::get('/callback', [GoogleDriveController::class, 'callback'])->name('callback');
        Route::get('/revoke',   [GoogleDriveController::class, 'revoke'])->name('revoke');
    });
    Route::post('/switch-organization/{organization}', function (\App\Models\Organization $organization) {
    $ok = \App\Services\OrganizationContext::authorize(auth()->user(), $organization->id);
    abort_unless($ok, 403);
    \App\Services\OrganizationContext::set($organization->id);
    return back()->with('success', "Beralih ke {$organization->name}");
})->name('organization.switch');

Route::get('/my-organizations', function () {
    return view('organization.selector', [
        'organizations' => auth()->user()->activeOrganizations()->get(),
    ]);
})->name('organization.selector');
        /* ════════════════════════════════════════════════════════
     | 5.1b DASHBOARD ITEM VISIBILITY
     | Personal hide/show per user untuk card dashboard.
     | Berlaku untuk SEMUA role (owner, superadmin, dst).
     * ════════════════════════════════════════════════════════ */
    Route::prefix('dashboard/items')->name('dashboard.items.')->group(function () {
        Route::post('/{item}/toggle',       [ItemVisibilityController::class, 'toggle'])->name('toggle');
        Route::post('/{item}/visibility',   [ItemVisibilityController::class, 'set'])->name('set');
        Route::post('/bulk-visibility',     [ItemVisibilityController::class, 'bulk'])->name('bulk');
        Route::delete('/reset-visibility',  [ItemVisibilityController::class, 'reset'])->name('reset');
    });


    /* ════════════════════════════════════════════════════════
     | 5.2 OWNER  — FULL
     * ════════════════════════════════════════════════════════ */
Route::prefix('owner')->name('owner.')
    ->middleware(['auth', 'owner.only'])   // ⬅️ WAJIB
    ->group(function () {

        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        /* ---------- System ---------- */
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/time', fn () => response()->json(app(AttendanceClockService::class)->syncPayload()))->name('time');
            Route::get('/health', [SystemHealthController::class, 'index'])->name('health');
            Route::post('/mail-test', [SystemHealthController::class, 'sendTestMail'])->name('mail-test');
            Route::get('/config', fn () => view('owner.system.config'))->name('config');
            Route::get('/backup', fn () => view('owner.system.backup'))->name('backup');
        });

        /* ---------- Promote / Demote ---------- */
        Route::prefix('promote-demote')->name('promote-demote.')->group(function () {
            Route::get('/',          [PromoteDemoteController::class, 'index'])->name('index');
            Route::get('/data',      [PromoteDemoteController::class, 'getData'])->name('data');
            Route::get('/jabatans',  [PromoteDemoteController::class, 'getJabatans'])->name('jabatans');
            Route::post('/update',   [PromoteDemoteController::class, 'update'])->name('update');
        });

        /* ---------- Explore (Owner Impersonation) ---------- */
        Route::prefix('settings/explore')->name('settings.explore.')->group(function () {
            $ctrl = \App\Http\Controllers\Owner\Settings\ExploreController::class;
            Route::get('/',                    [$ctrl, 'index'])->name('index');
            Route::post('/folders',            [$ctrl, 'storeFolder'])->name('folders.store');
            Route::put('/folders/{folder}',    [$ctrl, 'updateFolder'])->name('folders.update');
            Route::delete('/folders/{folder}', [$ctrl, 'destroyFolder'])->name('folders.destroy');
            Route::post('/folders/{folder}/cards', [$ctrl, 'storeCard'])->name('cards.store');
            Route::delete('/cards/{card}',         [$ctrl, 'destroyCard'])->name('cards.destroy');
            Route::get('/verify',          [$ctrl, 'verify'])->name('verify');
            Route::post('/verify',         [$ctrl, 'verifyAuthorize'])->name('verify.authorize');
            Route::get('/radar', [$ctrl, 'radar'])->name('radar');
            Route::get('/account/{user}',  [$ctrl, 'account'])->whereNumber('user')->name('account');
            Route::post('/exit',           [$ctrl, 'exitExplore'])->name('exit');
        });

        /* ---------- Impersonation Gateway ---------- */
        Route::group([
            'middleware' => ['impersonate.require', 'impersonate.isolate'],
            'prefix'     => '{roleSlug}/{impersonationToken}',
            'as'         => 'impersonate.',
            'where'      => [
                'roleSlug'           => 'anggota\-umum|anggota\-ukm|pengurus|ketua|superadmin|pengunjung',
                'impersonationToken' => '[A-Za-z0-9]{48}',
            ],
        ], function () {
            $ctrl = \App\Http\Controllers\Owner\ImpersonationDashboardController::class;
            Route::get('/dashboard',     [$ctrl, 'dashboard'])->name('dashboard');
            Route::get('/profile',       [$ctrl, 'profile'])->name('profile');
            Route::get('/poin',          [$ctrl, 'poin'])->name('poin');
            Route::get('/presensi',      [$ctrl, 'presensi'])->name('presensi');
            Route::get('/event',         [$ctrl, 'event'])->name('event');
            Route::get('/sertifikat',    [$ctrl, 'sertifikat'])->name('sertifikat');
            Route::get('/portofolio',    [$ctrl, 'portofolio'])->name('portofolio');
            Route::get('/prestasi',      [$ctrl, 'prestasi'])->name('prestasi');
            Route::get('/tugas',         [$ctrl, 'tugas'])->name('tugas');
            Route::get('/kelompok',      [$ctrl, 'kelompok'])->name('kelompok');
            Route::get('/pengumuman',    [$ctrl, 'pengumuman'])->name('pengumuman');
            Route::get('/kontak',        [$ctrl, 'kontak'])->name('kontak');
            Route::get('/anggota',       [$ctrl, 'anggota'])->name('anggota');
            Route::get('/chatting',      [$ctrl, 'chatting'])->name('chatting');
            Route::get('/divisi',        [$ctrl, 'divisi'])->name('divisi');
            Route::get('/jadwal',        [$ctrl, 'jadwal'])->name('jadwal');
            Route::get('/ruangan',       [$ctrl, 'ruangan'])->name('ruangan');
            Route::get('/misi',          [$ctrl, 'misi'])->name('misi');
            Route::get('/leaderpoint',   [$ctrl, 'leaderpoint'])->name('leaderpoint');
            Route::get('/game',          [$ctrl, 'game'])->name('game');
            Route::get('/uangkas',       [$ctrl, 'uangkas'])->name('uangkas');
            Route::get('/sanksi',        [$ctrl, 'sanksi'])->name('sanksi');
            Route::get('/settings',      [$ctrl, 'settings'])->name('settings');
            Route::get('/notifications', [$ctrl, 'notifications'])->name('notifications');
        });

        /* ---------- Notifications ---------- */
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/archived', [NotificationController::class, 'archived'])->name('archived');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::get('/poll', [NotificationController::class, 'poll'])->name('poll');
            Route::get('/dropdown', [NotificationController::class, 'dropdown'])->name('dropdown');
            Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
            Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');

            Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
            Route::post('/{notification}/read', [NotificationController::class, 'read'])->name('read');
            Route::post('/{notification}/seen', [NotificationController::class, 'seen'])->name('seen');
            Route::post('/{notification}/actioned', [NotificationController::class, 'actioned'])->name('actioned');
            Route::post('/{notification}/archive', [NotificationController::class, 'archive'])->name('archive');
            Route::post('/{notification}/unarchive', [NotificationController::class, 'unarchive'])->name('unarchive');
            Route::post('/{notification}/toggle-star', [NotificationController::class, 'toggleStar'])->name('toggle-star');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        });

        /* ---------- Bookings ---------- */
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/',         [BookingController::class, 'index'])->name('index');
            Route::get('/filter',   [BookingController::class, 'filter'])->name('filter');
            Route::get('/create',   [BookingController::class, 'create'])->name('create');
            Route::post('/store',   [BookingController::class, 'store'])->name('store');
            Route::get('/{booking}', [BookingController::class, 'show'])->whereNumber('booking')->name('show');
            Route::post('/{booking}/approve', [BookingController::class, 'approve'])->whereNumber('booking')->name('approve');
            Route::post('/{booking}/reject', [BookingController::class, 'reject'])->whereNumber('booking')->name('reject');
            Route::post('/{booking}/override', [BookingController::class, 'overrideConflict'])->whereNumber('booking')->name('override');
            Route::post('/{booking}/update-time', [BookingController::class, 'updateTime'])->whereNumber('booking')->name('update-time');
            Route::post('/{booking}/move-room', [BookingController::class, 'moveRoom'])->whereNumber('booking')->name('move-room');
            Route::post('/{booking}/checkin', [BookingController::class, 'checkin'])->whereNumber('booking')->name('checkin');
            Route::post('/{booking}/checkout', [BookingController::class, 'checkout'])->whereNumber('booking')->name('checkout');
        });

        /* ---------- Identity Vault ---------- */
        Route::prefix('identity-vault')->name('identity-vault.')->group(function () {
            Route::get('/',                [IdentityVaultController::class, 'index'])->name('index');
            Route::post('/backup',         [IdentityVaultController::class, 'backup'])->name('backup');
            Route::post('/recover',        [IdentityVaultController::class, 'recover'])->name('recover');
            Route::post('/recover-all',    [IdentityVaultController::class, 'recoverAll'])->name('recover-all');
            Route::get('/test-recovery',   [IdentityVaultController::class, 'testRecovery'])->name('test');
            Route::get('/audit/{uuid?}',   [IdentityVaultController::class, 'audit'])->where('uuid', UUID_REGEX . '?')->name('audit');
            Route::get('/{uuid}',          [IdentityVaultController::class, 'show'])->where('uuid', UUID_REGEX)->name('show');
        });

        /* ---------- QR Cleanup ---------- */
        Route::get('/qr-cleanup',          [QrCleanupController::class, 'index'])->name('qr-cleanup');
        Route::delete('/qr-cleanup/destroy', [QrCleanupController::class, 'destroy'])->name('qr-cleanup.destroy');

        /* ---------- Divisi ---------- */
        Route::prefix('divisi')->name('divisi.')->group(function () {
            Route::get('/',              [DivisiController::class, 'index'])->name('index');
            Route::get('/custom',        [DivisiController::class, 'custom'])->name('custom');
            Route::get('/akses',         [DivisiController::class, 'akses'])->name('akses');
            Route::get('/anggota/{divisionId?}', [DivisiController::class, 'anggota'])->whereNumber('divisionId')->name('anggota');
            Route::get('/pengumuman',    [DivisiController::class, 'pengumuman'])->name('pengumuman');
            Route::get('/pesan',         [DivisiController::class, 'pesan'])->name('pesan');
            Route::get('/invite',        [DivisiController::class, 'invite'])->name('invite');

            Route::post('/store',        [DivisiController::class, 'store'])->name('store');
            Route::put('/{division}',    [DivisiController::class, 'update'])->whereNumber('division')->name('update');
            Route::delete('/{division}', [DivisiController::class, 'destroy'])->whereNumber('division')->name('destroy');

            Route::post('/{division}/add-member', [DivisiController::class, 'addMember'])->whereNumber('division')->name('add-member');
            Route::delete('/{division}/remove-member/{user}', [DivisiController::class, 'removeMember'])->whereNumber('division')->whereNumber('user')->name('remove-member');
            Route::put('/{division}/update-member/{member}', [DivisiController::class, 'updateMember'])->whereNumber('division')->whereNumber('member')->name('update-member');
            Route::post('/{division}/move-member/{member}', [DivisiController::class, 'moveMember'])->whereNumber('division')->whereNumber('member')->name('move-member');
            Route::post('/{division}/permissions', [DivisiController::class, 'updatePermissions'])->whereNumber('division')->name('update-permissions');
            Route::post('/announcement/store', [DivisiController::class, 'storeAnnouncement'])->name('announcement.store');
            Route::get('/{division}/members-count', [DivisiController::class, 'membersCount'])->whereNumber('division')->name('members-count');
        });

        /* ---------- LMS (Tugas & Materi) ---------- */
        Route::middleware([EnsureLmsAccess::class])->prefix('lms')->name('lms.')->group(function () {

            Route::get('/',          [LmsDashboardController::class, 'index'])->name('dashboard');
            Route::get('/all',       [LmsItemController::class, 'all'])->name('all');
            Route::get('/tasks',     [LmsItemController::class, 'tasks'])->name('tasks');
            Route::get('/materials', [LmsItemController::class, 'materials'])->name('materials');

            Route::prefix('groups')->name('groups.')->group(function () {
                Route::get('/',       [LmsGroupController::class, 'index'])->name('index');
                Route::get('/create', [LmsGroupController::class, 'create'])->name('create');
                Route::post('/',      [LmsGroupController::class, 'store'])->name('store');

                Route::prefix('{group}')->whereNumber('group')->group(function () {
                    Route::get('/items/create', [LmsItemController::class, 'create'])->name('items.create');
                    Route::post('/items', [LmsItemController::class, 'store'])->name('items.store');

                    Route::get('/',         [LmsGroupController::class, 'show'])->name('show');
                    Route::get('/edit',     [LmsGroupController::class, 'edit'])->name('edit');
                    Route::put('/',         [LmsGroupController::class, 'update'])->name('update');
                    Route::delete('/',      [LmsGroupController::class, 'destroy'])->name('destroy');
                    Route::post('/restore', [LmsGroupController::class, 'restore'])->name('restore');
                });
            });

            Route::prefix('items')->name('items.')->group(function () {
                Route::get('/create',       [LmsItemController::class, 'create'])->name('create');
                Route::post('/',            [LmsItemController::class, 'store'])->name('store');
                Route::get('/search-users', [LmsItemController::class, 'searchUsers'])->middleware('throttle:60,1')->name('search-users');

                Route::get('/{item}',      [LmsItemController::class, 'show'])->whereNumber('item')->name('show');
                Route::get('/{item}/edit', [LmsItemController::class, 'edit'])->whereNumber('item')->name('edit');
                Route::put('/{item}',      [LmsItemController::class, 'update'])->whereNumber('item')->name('update');
                Route::delete('/{item}',   [LmsItemController::class, 'destroy'])->whereNumber('item')->name('destroy');

                Route::get('/{item}/recipients', [LmsMonitoringController::class, 'recipients'])->whereNumber('item')->name('recipients');
                Route::post('/{item}/remind', [LmsMonitoringController::class, 'remind'])->whereNumber('item')->name('remind');
                Route::post('/{item}/submissions', [LmsSubmissionController::class, 'store'])->whereNumber('item')->name('submissions.store');
                Route::get('/{item}/submit', [LmsSubmissionController::class, 'create'])->whereNumber('item')->name('submissions.create');
                Route::post('/{item}/viewed',  [LmsSubmissionController::class, 'markViewed'])->whereNumber('item')->name('viewed');
                Route::post('/{item}/started', [LmsSubmissionController::class, 'markStarted'])->whereNumber('item')->name('started');
            });

            Route::prefix('submissions')->name('submissions.')->group(function () {
                Route::get('/',             [LmsSubmissionController::class, 'index'])->name('index');
                Route::get('/pending',      [LmsSubmissionController::class, 'pending'])->name('pending');
                Route::get('/late',         [LmsSubmissionController::class, 'late'])->name('late');
                Route::get('/{submission}', [LmsSubmissionController::class, 'show'])->whereNumber('submission')->name('show');
                Route::post('/{submission}/review', [LmsSubmissionController::class, 'review'])->whereNumber('submission')->name('review');
            });

            Route::get('/my-created',     [LmsItemController::class, 'myCreated'])->name('my-created');
            Route::get('/my-submissions', [LmsSubmissionController::class, 'mySubmissions'])->name('my-submissions');
            Route::get('/monitoring', [LmsMonitoringController::class, 'index'])->name('monitoring');
            Route::get('/activities', [LmsAuditController::class, 'activities'])->name('activities');
            Route::get('/audit',      [LmsAuditController::class, 'index'])->name('audit');
            Route::get('/archived',   [LmsItemController::class, 'archived'])->name('archived');
            Route::get('/notifications', [LmsSettingsController::class, 'notifications'])->name('notifications');
            Route::get('/settings',      [LmsSettingsController::class, 'index'])->name('settings');
            Route::put('/settings',      [LmsSettingsController::class, 'update'])->name('settings.update');
        });

        /* ---------- Kelompok ---------- */
        Route::prefix('kelompok')->name('kelompok.')->group(function () {
            Route::get('/',       [KelompokController::class, 'index'])->name('index');
            Route::get('/custom', [KelompokController::class, 'custom'])->name('custom');
            Route::get('/akses',  [KelompokController::class, 'akses'])->name('akses');
            Route::get('/invite', [KelompokController::class, 'invite'])->name('invite');
            Route::get('/pengumuman', [KelompokController::class, 'pengumuman'])->name('pengumuman');
            Route::get('/pesan',      [KelompokController::class, 'pesan'])->name('pesan');
            Route::get('/anggota/{groupId?}', [KelompokController::class, 'anggota'])->whereNumber('groupId')->name('anggota');
            Route::get('/members-counts', [KelompokController::class, 'membersCounts'])->name('members-counts');

            Route::post('/store', [KelompokController::class, 'store'])->name('store');
            Route::post('/pengumuman/preview', [KelompokController::class, 'previewAnnouncement'])->name('announcement.preview');
            Route::post('/pengumuman/store', [KelompokController::class, 'storeAnnouncement'])->name('announcement.store');
            Route::delete('/pengumuman/{announcement}', [KelompokController::class, 'destroyAnnouncement'])->whereNumber('announcement')->name('announcement.destroy');

            Route::put('/{group}',    [KelompokController::class, 'update'])->whereNumber('group')->name('update');
            Route::delete('/{group}', [KelompokController::class, 'destroy'])->whereNumber('group')->name('destroy');

            Route::post('/{group}/add-member', [KelompokController::class, 'addMember'])->whereNumber('group')->name('add-member');
            Route::post('/{group}/add-members-bulk', [KelompokController::class, 'addMembersBulk'])->whereNumber('group')->name('add-members-bulk');
            Route::delete('/{group}/remove-member/{user}', [KelompokController::class, 'removeMember'])->whereNumber('group')->whereNumber('user')->name('remove-member');
            Route::put('/{group}/update-member/{member}', [KelompokController::class, 'updateMember'])->whereNumber('group')->whereNumber('member')->name('update-member');
            Route::post('/{group}/move-member/{member}', [KelompokController::class, 'moveMember'])->whereNumber('group')->whereNumber('member')->name('move-member');
            Route::post('/{group}/permissions', [KelompokController::class, 'updatePermissions'])->whereNumber('group')->name('update-permissions');
            Route::get('/{group}/members-count', [KelompokController::class, 'membersCount'])->whereNumber('group')->name('members-count');
        });

        /* ---------- Rooms ---------- */
        Route::prefix('rooms')->name('rooms.')->group(function () {
            /* Static */
            Route::get('/calendar',         [RoomController::class, 'calendar'])->name('calendar');
            Route::get('/requests',         [RoomController::class, 'requests'])->name('requests');
            Route::get('/check',            [RoomController::class, 'check'])->name('check');
            Route::get('/check-conflicts',  [RoomController::class, 'checkConflicts'])->name('check-conflicts');
            Route::post('/bulk-action',     [RoomController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/maintenance',      [RoomController::class, 'maintenanceIndex'])->name('maintenance.index');
            Route::get('/qr-testing',       [RoomController::class, 'testingIndex'])->name('testing');
            Route::post('/qr-testing/scan', [RoomController::class, 'testingScan'])->name('testing.scan');
            Route::post('/qr-testing/manual', [RoomController::class, 'testingManual'])->name('testing.manual');
            Route::get('/public',           [RoomController::class, 'publicIndex'])->name('public.index');
            Route::get('/public/history',   [RoomController::class, 'publicHistory'])->name('public.history');
            Route::get('/public/export',    [RoomController::class, 'publicExport'])->name('public.export');
            Route::post('/public/bulk',     [RoomController::class, 'bulkPublicAction'])->name('public.bulk');
            Route::get('/room_private',     [RoomAccessController::class, 'privateIndex'])->name('private');
            Route::get('/export/{format}',  [RoomController::class, 'export'])->whereIn('format', ['csv', 'xlsx', 'pdf'])->name('export');

            /* CRUD */
            Route::get('/',         [RoomController::class, 'index'])->name('index');
            Route::get('/create',   [RoomController::class, 'create'])->name('create');
            Route::post('/',        [RoomController::class, 'store'])->name('store');
            Route::get('/{room}/documents/{filename}', [RoomController::class, 'downloadDocument'])
                ->whereNumber('room')->where('filename', '.*')->name('documents.download');

            /* Dynamic */
            Route::get('/{room}',         [RoomController::class, 'show'])->whereNumber('room')->name('show');
            Route::get('/{room}/edit',    [RoomController::class, 'edit'])->whereNumber('room')->name('edit');
            Route::put('/{room}',         [RoomController::class, 'update'])->whereNumber('room')->name('update');
            Route::patch('/{room}',       [RoomController::class, 'update'])->whereNumber('room');
            Route::delete('/{room}',      [RoomController::class, 'destroy'])->whereNumber('room')->name('destroy');

            Route::post('/requests/{booking}/approve', [RoomController::class, 'approveBooking'])->whereNumber('booking')->name('requests.approve');
            Route::post('/requests/{booking}/reject',  [RoomController::class, 'rejectBooking'])->whereNumber('booking')->name('requests.reject');

            Route::get('/{room}/maintenance',  [RoomController::class, 'maintenance'])->whereNumber('room')->name('maintenance');
            Route::get('/{room}/facilities',   [RoomController::class, 'facilitiesIndex'])->whereNumber('room')->name('facilities');
            Route::get('/{room}/schedules',    [RoomController::class, 'schedulesIndex'])->whereNumber('room')->name('schedules');
            Route::get('/{room}/history',      [RoomController::class, 'history'])->whereNumber('room')->name('history');
            Route::get('/{room}/booking/create',  [RoomController::class, 'bookingCreate'])->whereNumber('room')->name('booking.create');
            Route::post('/{room}/booking',        [RoomController::class, 'bookingStore'])->whereNumber('room')->name('booking.store');
            Route::post('/{room}/book',           [RoomController::class, 'storeBooking'])->whereNumber('room')->name('book');
            Route::post('/{room}/generate-qr',    [RoomController::class, 'generateQr'])->whereNumber('room')->name('generate-qr');
            Route::get('/{room}/download-qr/{format}', [RoomController::class, 'downloadQr'])
                ->whereNumber('room')->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'svg'])->name('download-qr');
            Route::post('/public/{room}/toggle',  [RoomController::class, 'togglePublic'])->whereNumber('room')->name('public.toggle');
            Route::get('/room_private/{room}',    [RoomAccessController::class, 'privateShow'])->whereNumber('room')->name('private.show');
        });

        /* ---------- Schedules ---------- */
        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/',         [ScheduleController::class, 'index'])->name('index');
            Route::get('/upload',   [ScheduleController::class, 'uploadForm'])->name('upload');
            Route::post('/upload',  [ScheduleController::class, 'uploadStore'])->name('upload.store');
            Route::get('/public',   [ScheduleController::class, 'publicIndex'])->name('public');
            Route::get('/history',  [ScheduleController::class, 'history'])->name('history');
            Route::get('/{schedule}',      [ScheduleController::class, 'show'])->whereNumber('schedule')->name('show');
            Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->whereNumber('schedule')->name('edit');
            Route::put('/{schedule}',      [ScheduleController::class, 'update'])->whereNumber('schedule')->name('update');
            Route::delete('/{schedule}',   [ScheduleController::class, 'destroy'])->whereNumber('schedule')->name('destroy');
        });

        /* ---------- Contacts ---------- */
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/',              [ContactsController::class, 'index'])->name('index');
            Route::get('/owner',         [ContactsController::class, 'byRole'])->name('owner');
            Route::get('/superadmin',    [ContactsController::class, 'byRole'])->name('superadmin');
            Route::get('/ketua',         [ContactsController::class, 'byRole'])->name('ketua');
            Route::get('/pengurus',      [ContactsController::class, 'byRole'])->name('pengurus');
            Route::get('/anggota',       [ContactsController::class, 'byRole'])->name('anggota');
            Route::get('/anggota-umum',  [ContactsController::class, 'byRole'])->name('anggota-umum');
            Route::get('/pengunjung',    [ContactsController::class, 'byRole'])->name('pengunjung');
            Route::get('/active',        [ContactsController::class, 'byStatus'])->name('active');
            Route::get('/offline',       [ContactsController::class, 'byStatus'])->name('offline');
            Route::get('/frozen',        [ContactsController::class, 'byStatus'])->name('frozen');
            Route::get('/blocked',       [ContactsController::class, 'byStatus'])->name('blocked');
            Route::get('/deactivated',   [ContactsController::class, 'byStatus'])->name('deactivated');
        });

        Route::get('/user/{uuid}', function (string $uuid) {
            $user = User::with(['level', 'status', 'jabatans'])->where('user_uuid', $uuid)->firstOrFail();
            return view('owner.contacts.user', compact('user'));
        })->where('uuid', UUID_REGEX)->name('user');

        /* ---------- Chats ---------- */
        Route::prefix('chats')->name('chats.')->group(function () {
            Route::get('/',                 [ChatController::class, 'index'])->name('index');
            Route::get('/private/{uuid?}',  [ChatController::class, 'private'])->where('uuid', UUID_REGEX . '?')->name('private');
            Route::post('/send',            [ChatController::class, 'sendMessage'])->name('send');
            Route::post('/poll',            [ChatController::class, 'getNewMessages'])->middleware('throttle:60,1')->name('poll');
            Route::get('/search-users',     [ChatController::class, 'searchUsers'])->name('search');
        });

        /* ---------- Placeholder pages ---------- */
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/',       fn () => view('owner.groups.index'))->name('index');
            Route::get('/create', fn () => view('owner.groups.create'))->name('create');
        });
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/inbox',    fn () => view('owner.messages.inbox'))->name('inbox');
            Route::get('/outbox',   fn () => view('owner.messages.outbox'))->name('outbox');
            Route::get('/rejected', fn () => view('owner.messages.rejected'))->name('rejected');
        });
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/blocked-words', fn () => view('owner.security.blocked-words'))->name('blocked-words');
            Route::get('/chat-logs',     fn () => view('owner.security.chat-logs'))->name('chat-logs');
        });

        /* ---------- Management ---------- */
        Route::prefix('management')->name('management.')->group(function () {

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
                $qrActive        = UserQrCode::whereNull('deleted_at')->whereHas('user', fn ($q) => $q->where('is_active', true))->count();
                $totalEvents     = Event::where('status', 'active')->count();

                $stats = [
                    ['label' => 'Total Anggota',     'value' => $totalMembers,   'icon' => 'fa-users',    'color' => 'blue',   'trend' => '+12%',           'trendType' => 'up'],
                    ['label' => 'Total Pengurus',    'value' => $totalOfficials, 'icon' => 'fa-user-tie', 'color' => 'green',  'trend' => '+3',             'trendType' => 'up'],
                    ['label' => 'Menunggu Approval', 'value' => 7,               'icon' => 'fa-clock',    'color' => 'yellow', 'trend' => '7 perlu review', 'trendType' => 'neutral'],
                    ['label' => 'QR Aktif',          'value' => $qrActive,       'icon' => 'fa-qrcode',   'color' => 'cyan',   'trend' => ($qrActive > 0 ? round(($qrActive / max($totalQR, 1)) * 100) . '% aktif' : '0%'), 'trendType' => ($qrActive > 0 ? 'up' : 'neutral')],
                ];
                $extraStats = [
                    ['label' => 'Owner',         'value' => $totalOwner,      'icon' => 'fa-crown',      'color' => 'amber'],
                    ['label' => 'Superadmin',    'value' => $totalSuperadmin, 'icon' => 'fa-shield-alt', 'color' => 'red'],
                    ['label' => 'Ketua',         'value' => $totalKetua,      'icon' => 'fa-users-cog',  'color' => 'purple'],
                    ['label' => 'Pengunjung',    'value' => $totalPengunjung, 'icon' => 'fa-globe',      'color' => 'teal'],
                    ['label' => 'Akun Diblokir', 'value' => $totalBlocked,    'icon' => 'fa-ban',        'color' => 'red'],
                ];
                $quickActions = [
                    ['label' => 'Tambah Anggota', 'route' => 'owner.management.members.create', 'icon' => 'fa-user-plus',   'color' => 'blue'],
                    ['label' => 'Data Anggota',   'route' => 'owner.management.members.index',  'icon' => 'fa-users',       'color' => 'indigo'],
                    ['label' => 'QR Code',        'route' => 'owner.management.qr.index',       'icon' => 'fa-qrcode',      'color' => 'cyan'],
                    ['label' => 'Kelompok',       'route' => 'owner.management.groups.index',   'icon' => 'fa-layer-group', 'color' => 'purple'],
                    ['label' => 'Data Pengurus',  'route' => 'owner.officials.index',           'icon' => 'fa-user-tie',    'color' => 'green'],
                    ['label' => 'Struktur',       'route' => 'owner.officials.structure.index', 'icon' => 'fa-sitemap',     'color' => 'amber'],
                    ['label' => 'Jabatan',        'route' => 'owner.officials.positions.index', 'icon' => 'fa-briefcase',   'color' => 'pink'],
                    ['label' => 'Audit',          'route' => 'owner.officials.audit.index',     'icon' => 'fa-history',     'color' => 'gray'],
                ];
                $activities = AuditLog::with('user')->latest()->take(6)->get()->map(function ($log) {
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

            Route::prefix('members')->name('members.')->group(function () {
                Route::get('/', function () {
                    $members = User::where('level_id', 5)->where('name', '!=', 'Anggota Umum')->with('level')->paginate(15);
                    return view('owner.management.members', compact('members'));
                })->name('index');
                Route::get('/create',  [MemberController::class, 'create'])->name('create');
                Route::post('/',       [MemberController::class, 'store'])->name('store');
                Route::get('/check',   [MemberController::class, 'checkUnique'])->name('check');
                Route::get('/history', fn () => view('owner.management.members.history'))->name('history');

                Route::prefix('import')->name('import.')->group(function () {
                    Route::get('/',              [MemberController::class, 'importForm'])->name('form');
                    Route::post('/',             [MemberController::class, 'importProcess'])->name('process');
                    Route::post('/preview',      [MemberController::class, 'importPreview'])->name('preview');
                    Route::post('/store',        [MemberController::class, 'importStore'])->name('store');
                    Route::post('/update-card',  [MemberController::class, 'importUpdateCard'])->name('update-card');
                    Route::post('/delete-card',  [MemberController::class, 'importDeleteCard'])->name('delete-card');
                    Route::get('/get-card',      [MemberController::class, 'importGetCard'])->name('get-card');
                    Route::get('/success',       [MemberController::class, 'importSuccess'])->name('success');
                });
                Route::post('/sync', [MemberController::class, 'syncMembers'])->name('sync');
                Route::get('/{id}', fn (int $id) => view('owner.management.members.show', ['member' => User::findOrFail($id)]))->whereNumber('id')->name('show');
                Route::get('/{id}/edit', fn (int $id) => view('owner.management.members.edit', ['member' => User::findOrFail($id)]))->whereNumber('id')->name('edit');
                Route::delete('/{id}', function (int $id) {
                    User::findOrFail($id)->delete();
                    return redirect()->route('owner.management.members.index')->with('success', 'Anggota berhasil dihapus.');
                })->whereNumber('id')->name('destroy');
            });

            Route::get('/users/{id}', function (int $id) {
                $user = User::with(['level', 'status', 'university', 'major'])->findOrFail($id);
                return view('owner.management.users.show', compact('user'));
            })->whereNumber('id')->name('users.show');

            Route::prefix('officials')->name('officials.')->group(function () {
                Route::get('/', fn () => view('owner.management.officials', ['officials' => User::where('level_id', 4)->paginate(15)]))->name('index');
                Route::get('/create', fn () => view('owner.management.officials.create'))->name('create');

            });

            Route::prefix('nfc')->name('nfc.')->group(function () {
                Route::get('/',             [NfcController::class, 'index'])->name('index');
                Route::get('/generate',     [NfcController::class, 'generate'])->name('generate');
                Route::post('/generate',    [NfcController::class, 'store'])->name('generate.store');
                Route::get('/history',      [NfcController::class, 'history'])->name('history');
                Route::get('/stats',        [NfcController::class, 'stats'])->name('stats');
                Route::post('/bulk-assign-identifier', [NfcController::class, 'bulkAssignIdentifier'])->name('bulk-assign-identifier');
                Route::post('/bulk-revoke', [NfcController::class, 'bulkRevoke'])->name('bulk-revoke');
                Route::get('/testing',      [NfcController::class, 'testing'])->name('testing');
                Route::post('/testing/validate', [NfcController::class, 'validateNfc'])->middleware('throttle:20,1')->name('testing.validate');
                Route::post('/upload',       [NfcController::class, 'uploadFile'])->name('upload');
                Route::post('/upload-image', [NfcController::class, 'uploadImage'])->name('upload-image');
                Route::get('/download/{user_id}/{format}', [NfcController::class, 'download'])
                    ->whereNumber('user_id')->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'pdf'])->name('download');
                Route::post('/{nfc}/assign-identifier', [NfcController::class, 'assignIdentifier'])->where('nfc', UUID_REGEX)->name('assign-identifier');
                Route::post('/{nfc}/revoke', [NfcController::class, 'revoke'])->where('nfc', UUID_REGEX)->name('revoke');
                Route::post('/{nfc}/move', [NfcController::class, 'move'])->where('nfc', UUID_REGEX)->name('move');
                Route::delete('/{id}', [NfcController::class, 'destroy'])->whereNumber('id')->name('destroy');
            });

            Route::prefix('testing')->name('testing.')->group(function () {
                Route::get('/',              fn () => view('owner.management.testing.index'))->name('index');
                Route::get('/qr',            fn () => view('owner.management.testing.qr'))->name('qr');
                Route::post('/qr/validate',  [TestingController::class, 'validateQr'])->name('qr.validate');
                Route::get('/nim',           fn () => view('owner.management.testing.nim'))->name('nim');
                Route::post('/nim/find',     [TestingController::class, 'findUserByNim'])->name('nim.find');
                Route::get('/identity',      fn () => view('owner.management.testing.identity'))->name('identity');
                Route::post('/identity/test', [TestingController::class, 'identityTest'])->name('identity.test');
                Route::get('/validate',      fn () => view('owner.management.testing.validate'))->name('validate');
                Route::post('/validate/deep', [TestingController::class, 'deepValidate'])->name('validate.deep');
                Route::post('/upload-qr',    [TestingController::class, 'uploadQr'])->name('upload-qr');
            });

            Route::prefix('qr')->name('qr.')->group(function () {
                Route::get('/', function () {
                    $totalUsers     = User::count();
                    $usersWithQr    = UserQrCode::distinct('user_id')->count('user_id');
                    $usersWithoutQr = $totalUsers - $usersWithQr;
                    $qrBlocked      = UserQrCode::where('is_active', 0)->count();
                    $usersBlocked   = User::where('is_active', false)->count();
                    $roles          = \App\Models\Level::all();
                    $query = User::whereHas('qrCodes', fn ($q) => $q->whereNull('deleted_at'))->with('level');
                    if (request()->filled('role')) $query->where('level_id', request('role'));
                    $qrUsers = $query->paginate(15);
                    return view('owner.management.qr.index', compact('qrUsers', 'usersWithoutQr', 'usersWithQr', 'qrBlocked', 'usersBlocked', 'roles'));
                })->name('index');

                Route::get('/generate', fn () => view('owner.management.qr.generate', ['users' => User::with('level')->get(), 'roles' => \App\Models\Level::all()]))->name('generate');
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
                    $success = $skipped = 0;
                    $qrService = app(QrCodeService::class);
                    foreach ($request->input('user_ids') as $userId) {
                        $user = User::find($userId);
                        if (! $user) continue;
                        $qr = $qrService->generateForUser($user, ['replace' => $replace, 'expired_at' => $expiredAt, 'format' => $format]);
                        $qr ? $success++ : $skipped++;
                    }
                    $msg = "✅ Berhasil generate $success QR Code format $format.";
                    if ($skipped > 0) $msg .= " ⚠️ $skipped user dilewati.";
                    return redirect()->route('owner.management.qr.index')->with('success', $msg);
                })->name('generate.store');

                Route::post('/repair/{qr_id}', function (int $qr_id, Request $request) {
                    $qr = UserQrCode::findOrFail($qr_id);
                    if ($qr->is_revoked || ! $qr->is_active) return back()->with('error', '❌ QR tidak dapat diperbaiki.');
                    $repaired = app(QrCodeService::class)->repairQr($qr, $request->input('format', 'png'));
                    return $repaired ? back()->with('success', '✅ QR berhasil diperbaiki.') : back()->with('error', '❌ Gagal memperbaiki QR.');
                })->whereNumber('qr_id')->name('repair');

                Route::get('/massal', fn () => view('owner.management.qr.massal', ['users' => User::paginate(15), 'roles' => \App\Models\Level::all()]))->name('massal');
                Route::post('/massal', function (Request $request) {
                    $userIds = $request->input('user_ids', []);
                    if (empty($userIds)) return back()->with('error', 'Pilih minimal satu user.');
                    $count = 0;
                    $qrService = app(QrCodeService::class);
                    foreach ($userIds as $userId) {
                        $user = User::find($userId);
                        if (! $user || $user->qrCodes()->where('is_active', true)->exists()) continue;
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
                        return response()->json(['valid' => true, 'user' => $qr->user ? ['name' => $qr->user->name, 'nim' => $qr->user->nim, 'level' => $qr->user->level->name ?? 'N/A'] : null]);
                    }
                    return response()->json(['valid' => false, 'message' => 'QR tidak valid.']);
                })->middleware('throttle:30,1')->name('test.validate');

                Route::get('/print', fn () => view('owner.management.qr.print', ['users' => User::with('qrCodes')->get()]))->name('print');
                Route::post('/print', function (Request $request) {
                    $user = User::find($request->input('user_id'));
                    if (! $user) return back()->with('error', 'User tidak ditemukan.');
                    return back()->with('success', 'Cetak kartu untuk ' . $user->name . ' sedang diproses.');
                })->name('print.process');

                Route::get('/download', [QrController::class, 'index'])->name('download');
                Route::get('/download-qr/{qr_id}/{format}', [QrController::class, 'download'])
                    ->whereNumber('qr_id')->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'])->name('download.get');

                Route::get('/deactivate', fn () => view('owner.management.qr.deactivate', ['users' => User::all()]))->name('deactivate');
                Route::post('/deactivate', function (Request $request) {
                    $user = User::find($request->input('user_id'));
                    if (! $user) return back()->with('error', 'User tidak ditemukan.');
                    $qr = $user->qrCodes()->where('is_active', true)->first();
                    if (! $qr) return back()->with('error', 'User tidak memiliki QR aktif.');
                    $qr->update(['is_active' => false, 'is_revoked' => true, 'revoked_at' => now(), 'revoked_by' => auth()->id(), 'notes' => $request->input('reason', 'Tidak ada alasan')]);
                    return redirect()->route('owner.management.qr.index')->with('success', 'QR dinonaktifkan.');
                })->name('deactivate.process');

                Route::get('/detail/{id}', fn (int $id) => view('owner.management.qr.detail', ['user' => User::with('qrCodes')->findOrFail($id)]))->whereNumber('id')->name('detail');
                Route::post('/revoke/{id}', function (int $id) {
                    $user = User::findOrFail($id);
                    $qr = $user->qrCodes()->where('is_active', true)->first();
                    if (! $qr) return back()->with('error', 'User tidak memiliki QR aktif.');
                    $qr->update(['is_active' => false, 'is_revoked' => true, 'revoked_at' => now(), 'revoked_by' => auth()->id()]);
                    return redirect()->route('owner.management.qr.index')->with('success', 'QR dicabut permanen.');
                })->whereNumber('id')->name('revoke');

                Route::get('/recovery',          [QrRecoveryController::class, 'index'])->name('recovery');
                Route::post('/recovery/preview', [QrRecoveryController::class, 'preview'])->name('recovery.preview');
                Route::post('/recovery/submit',  [QrRecoveryController::class, 'submit'])->name('recovery.submit');

                Route::delete('/cleanup-revoked', [QrController::class, 'cleanupRevoked'])->name('cleanup-revoked');
                Route::delete('/delete-selected', [QrController::class, 'deleteSelected'])->name('delete-selected');
                Route::post('/rotate/{user_id}', [QrController::class, 'rotate'])->whereNumber('user_id')->name('rotate');
                Route::get('/history/{user_id}', [QrController::class, 'history'])->whereNumber('user_id')->name('history');
            });

            Route::prefix('groups')->name('groups.')->group(function () {
                Route::get('/',          fn () => view('owner.management.groups.index'))->name('index');
                Route::get('/history',   fn () => view('owner.management.groups.history'))->name('history');
                Route::get('/move/{id}', fn (int $id) => view('owner.management.groups.move', compact('id')))->whereNumber('id')->name('move');
                Route::get('/{id}',      fn (int $id) => view('owner.management.groups.show', compact('id')))->whereNumber('id')->name('show');
            });

            Route::prefix('divisions')->name('divisions.')->group(function () {
                Route::get('/',          fn () => view('owner.management.divisions.index'))->name('index');
                Route::get('/history',   fn () => view('owner.management.divisions.history'))->name('history');
                Route::get('/move/{id}', fn (int $id) => view('owner.management.divisions.move', compact('id')))->whereNumber('id')->name('move');
                Route::get('/{id}',      fn (int $id) => view('owner.management.divisions.show', compact('id')))->whereNumber('id')->name('show');
            });

            Route::prefix('appointments')->name('appointments.')->group(function () {
                Route::get('/',          fn () => view('owner.management.appointments.index'))->name('index');
                Route::get('/create',    fn () => view('owner.management.appointments.create'))->name('create');
                Route::get('/approval',  fn () => view('owner.management.appointments.approval'))->name('approval');
                Route::get('/history',   fn () => view('owner.management.appointments.history'))->name('history');
            });

            Route::prefix('violations')->name('violations.')->group(function () {
                Route::get('/',        fn () => view('owner.management.violations.index'))->name('index');
                Route::get('/warning', fn () => view('owner.management.violations.warning'))->name('warning');
                Route::get('/light',   fn () => view('owner.management.violations.light'))->name('light');
                Route::get('/medium',  fn () => view('owner.management.violations.medium'))->name('medium');
                Route::get('/heavy',   fn () => view('owner.management.violations.heavy'))->name('heavy');
                Route::get('/suspend', fn () => view('owner.management.violations.suspend'))->name('suspend');
                Route::get('/block',   fn () => view('owner.management.violations.block'))->name('block');
            });

           /* ---------- Archive (Alumni / Keluar / History) ---------- */
Route::prefix('archive')->name('archive.')->group(function () {

    $page = function (string $statusNames, string $view, string $title) {
        return function () use ($statusNames, $view, $title) {
            try {
                $names = array_map('trim', explode(',', $statusNames));

                $base = User::query()
                    ->whereHas('status', fn ($q) => $q->whereIn('name', $names));

                $total    = (clone $base)->count();
                $thisYear = (clone $base)
                    ->whereYear('updated_at', now()->year)
                    ->count();

                $members = (clone $base)
                    ->with(['level', 'status', 'jabatans', 'university', 'major'])
                    ->orderByDesc('updated_at')
                    ->paginate(20);

                $stats = [
                    'total'     => $total,
                    'this_year' => $thisYear,
                    'pria'      => (clone $base)->where('gender', 'L')->count(),
                    'wanita'    => (clone $base)->where('gender', 'P')->count(),
                ];
            } catch (\Throwable $e) {
                $members = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
                $stats   = ['total' => 0, 'this_year' => 0, 'pria' => 0, 'wanita' => 0];
            }

            return view($view, compact('members', 'stats', 'title'));
        };
    };

    Route::get('/graduated', $page('alumni,graduated,lulus',  'owner.management.archive.graduated', 'Alumni / Lulus'))
        ->name('graduated');

    Route::get('/left',      $page('left,keluar,resigned,dropped_out', 'owner.management.archive.left', 'Keluar / Berhenti'))
        ->name('left');

    Route::get('/history',   $page('alumni,graduated,lulus,left,keluar,resigned,dropped_out', 'owner.management.archive.history', 'Riwayat Arsip'))
        ->name('history');
});
        });

        /* ---------- Officials (Owner) ---------- */
        Route::prefix('officials')->name('officials.')->group(function () {
            Route::get('/', function () {
                $officials = User::whereIn('level_id', [3, 4])->with('level', 'jabatans', 'university', 'major')->orderBy('level_id')->paginate(15);
                return view('owner.officials.index', compact('officials'));
            })->name('dashboard');

Route::get('/index', function (\Illuminate\Http\Request $request) {
    $query = User::whereIn('level_id', [3, 4])
        ->with(['level', 'jabatans', 'university', 'major']);

    if ($q = $request->input('q')) {
        $query->where(function ($w) use ($q) {
            $w->where('name', 'like', "%{$q}%")
              ->orWhere('nim', 'like', "%{$q}%")
              ->orWhere('email', 'like', "%{$q}%");
        });
    }

    if ($level = $request->input('level')) {
        if (in_array($level, ['3', '4'])) {
            $query->where('level_id', (int) $level);
        }
    }

    $officials = $query->orderBy('level_id')->paginate(15);

    return view('owner.officials.index', compact('officials'));
})->name('index');

            Route::get('/create', function () {
                return view('owner.officials.create', [
                    'universities' => \App\Models\University::all(),
                    'majors'       => \App\Models\Major::all(),
                    'jabatans'     => \App\Models\Jabatan::all(),
                ]);
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
                $major = \App\Models\Major::firstOrCreate(['name' => $request->program_studi, 'university_id' => $university->id]);
                $avatarPath = $request->hasFile('avatar') ? $request->file('avatar')->store('avatars', 'public') : null;
                $prefix = $request->level_id == 3 ? 'UKM-KETUA-' : 'UKM-PENG-';
                $count  = User::where('level_id', $request->level_id)->count() + 1;

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
                    'member_number' => $prefix . str_pad($count, 4, '0', STR_PAD_LEFT),
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
                return redirect()->route('owner.officials.index')->with('success', 'Ketua/Pengurus berhasil ditambahkan.');
            })->name('store');

            Route::get('/core',                fn () => view('owner.officials.core'))->name('core');
            Route::get('/assistant-secretary', [SecretaryController::class, 'dashboard'])->middleware(CheckSecretaryAccess::class)->name('assistant-secretary');
            Route::get('/pj-division',         fn () => view('owner.officials.pj-division'))->name('pj-division');
            Route::get('/assistant-division',  fn () => view('owner.officials.assistant-division'))->name('assistant-division');
            Route::get('/division-members',    fn () => view('owner.officials.division-members'))->name('division-members');
            Route::get('/secretary',           [SecretaryController::class, 'dashboard'])->middleware(CheckSecretaryAccess::class)->name('secretary');

            Route::middleware(CheckSecretaryAccess::class)->prefix('secretary')->name('secretary.')->group(function () {
                Route::get('/dashboard', [SecretaryController::class, 'dashboard'])->name('dashboard');
                Route::resource('incoming-mails', IncomingMailController::class)->names('incoming-mails');
                Route::post('incoming-mails/{incomingMail}/forward', [IncomingMailController::class, 'forward'])->name('incoming-mails.forward');
                Route::post('incoming-mails/{incomingMail}/archive', [IncomingMailController::class, 'archive'])->name('incoming-mails.archive');
                Route::resource('outgoing-mails', OutgoingMailController::class)->names('outgoing-mails');
                Route::resource('minutes', MinutesController::class)->names('minutes');
                Route::post('minutes/{minute}/archive',   [MinutesController::class, 'archive'])->name('minutes.archive');
                Route::post('minutes/{minute}/unarchive', [MinutesController::class, 'unarchive'])->name('minutes.unarchive');
                Route::get('minutes/{minute}/pdf',        [MinutesController::class, 'exportPdf'])->name('minutes.pdf');
                Route::get('minutes/booking-data/{booking}', [MinutesController::class, 'getBookingData'])->name('minutes.booking-data');
                Route::get('attachments/{attachment}/download', [MinutesController::class, 'downloadAttachment'])->name('minutes.download-attachment');
                Route::delete('attachments/{attachment}',       [MinutesController::class, 'deleteAttachment'])->name('minutes.delete-attachment');
                Route::resource('archives', ArchiveController::class)->names('archives');
                Route::get('rooms/{room}/download-qr/{format}', [RoomController::class, 'downloadQr'])
                    ->whereNumber('room')->whereIn('format', ['png', 'jpg', 'jpeg', 'webp', 'svg'])->name('rooms.download-qr');
            });

            Route::prefix('structure')->name('structure.')->group(function () {
                Route::get('/',                 fn () => view('owner.officials.structure.index'))->name('index');
                Route::get('/owner',            fn () => view('owner.officials.structure.owner'))->name('owner');
                Route::get('/superadmin',       fn () => view('owner.officials.structure.superadmin'))->name('superadmin');
                Route::get('/ketua',            fn () => view('owner.officials.structure.ketua'))->name('ketua');
                Route::get('/pengurus-inti',    fn () => view('owner.officials.structure.pengurus-inti'))->name('pengurus-inti');
                Route::get('/sekretaris',       fn () => view('owner.officials.structure.sekretaris'))->name('sekretaris');
                Route::get('/asisten-sekretaris', fn () => view('owner.officials.structure.asisten-sekretaris'))->name('asisten-sekretaris');
                Route::get('/pj-divisi',        fn () => view('owner.officials.structure.pj-divisi'))->name('pj-divisi');
                Route::get('/asisten-divisi',   fn () => view('owner.officials.structure.asisten-divisi'))->name('asisten-divisi');
                Route::get('/anggota-divisi',   fn () => view('owner.officials.structure.anggota-divisi'))->name('anggota-divisi');
            });

            Route::prefix('qr')->name('qr.')->group(function () {
                Route::get('/', function () {
                    return view('owner.officials.index', ['officials' => User::whereIn('level_id', [3, 4])->with('level')->orderBy('level_id')->paginate(15)]);
                })->name('dashboard');
                Route::get('/index', function () {
                    return view('owner.officials.index', ['officials' => User::whereIn('level_id', [3, 4])->with('level')->orderBy('level_id')->paginate(15)]);
                })->name('index');
                Route::get('/generate',  fn () => view('owner.officials.qr.generate', ['officials' => User::where('level_id', 4)->get()]))->name('generate');
                Route::post('/generate', fn () => redirect()->route('owner.officials.qr.index')->with('success', 'QR berhasil digenerate.'))->name('generate.store');
                Route::get('/massal',    fn () => view('owner.officials.qr.massal', ['officials' => User::where('level_id', 4)->paginate(15)]))->name('massal');
                Route::post('/massal',   fn () => redirect()->route('owner.officials.qr.index')->with('success', 'QR massal berhasil dibuat.'))->name('massal.store');
                Route::get('/testing',   fn () => view('owner.officials.qr.testing'))->name('testing');
                Route::post('/testing',  fn () => response()->json(['valid' => true]))->name('testing.validate');
                Route::get('/print-card',  fn () => view('owner.officials.qr.print-card', ['officials' => User::where('level_id', 4)->get()]))->name('print-card');
                Route::post('/print-card', fn () => back()->with('success', 'Print card sedang diproses.'))->name('print-card.process');
                Route::get('/detail/{id}', fn (int $id) => view('owner.officials.qr.detail', ['user' => User::with('qrCodes')->findOrFail($id)]))->whereNumber('id')->name('detail');
                Route::post('/revoke/{id}', fn (int $id) => back()->with('success', 'QR berhasil dicabut.'))->whereNumber('id')->name('revoke');
            });

            Route::prefix('divisions')->name('divisions.')->group(function () {
                Route::get('/pengembang', fn () => view('owner.officials.divisions.pengembang'))->name('pengembang');
                Route::get('/pdd',        fn () => view('owner.officials.divisions.pdd'))->name('pdd');
                Route::get('/{id}',       fn (int $id) => view('owner.officials.divisions.show', ['id' => $id]))->whereNumber('id')->name('show');
            });

            Route::prefix('positions')->name('positions.')->group(function () {
                Route::get('/',         fn () => view('owner.officials.positions.index'))->name('index');
                Route::get('/appoint',  fn () => view('owner.officials.positions.appoint'))->name('appoint');
                Route::get('/demote',   fn () => view('owner.officials.positions.demote'))->name('demote');
                Route::get('/transfer', fn () => view('owner.officials.positions.transfer'))->name('transfer');
                Route::get('/terminate', fn () => view('owner.officials.positions.terminate'))->name('terminate');
                Route::get('/history',  fn () => view('owner.officials.positions.history'))->name('history');
            });

Route::prefix('audit')->name('audit.')->group(function () {
    Route::get('/',              fn () => view('owner.officials.audit.index'))->name('index');
    Route::get('/changes',       fn () => view('owner.officials.audit.changes'))->name('changes');
    Route::get('/appointments',  fn () => view('owner.officials.audit.appointments'))->name('appointments');
    Route::get('/demotions',     fn () => view('owner.officials.audit.demotions'))->name('demotions');
    Route::get('/transfers',     fn () => view('owner.officials.audit.transfers'))->name('transfers');
    Route::get('/terminations',  fn () => view('owner.officials.audit.terminations'))->name('terminations');
});

// ⬇️⬇️⬇️ TAMBAHKAN DI SINI ⬇️⬇️⬇️
Route::get('/{id}', function (int $id) {
    try {
        $official = User::with(['level', 'jabatans', 'university', 'major', 'status'])
            ->findOrFail($id);
    } catch (\Throwable $e) {
        abort(404, 'Pengurus tidak ditemukan.');
    }
    return view('owner.officials.show', compact('official'));
})->whereNumber('id')->name('show');
// ⬆️⬆️⬆️ TAMBAHKAN DI SINI ⬆️⬆️⬆️

Route::get('/{id}/edit', fn (int $id) => view('owner.officials.edit', ['official' => User::findOrFail($id)]))->whereNumber('id')->name('edit');
Route::put('/{id}', fn (Request $request, int $id) => redirect()->route('owner.officials.index')->with('success', 'Pengurus berhasil diperbarui.'))->whereNumber('id')->name('update');
Route::delete('/{id}', function (int $id) {
    User::findOrFail($id)->delete();
    return redirect()->route('owner.officials.index')->with('success', 'Pengurus berhasil dihapus.');
})->whereNumber('id')->name('destroy');
        });

        /* ---------- Attendance ---------- */
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::prefix('audit')->name('audit.')->group(function () {
                Route::get('/',     [AttendanceAuditController::class, 'index'])->name('index');
                Route::get('/export', [AttendanceAuditController::class, 'export'])->name('export');
                Route::get('/verify', [AttendanceAuditController::class, 'verify'])->name('verify');
                Route::post('/alerts/{alert}/ack', [AttendanceAuditController::class, 'acknowledgeAlert'])->whereNumber('alert')->name('alerts.ack');
                Route::get('/{log}', [AttendanceAuditController::class, 'show'])->whereNumber('log')->name('show');
            });

            Route::prefix('penalties')->name('penalties.')->group(function () {
                Route::get('/',     [AttendancePenaltyController::class, 'index'])->name('index');
                Route::get('/export', [AttendancePenaltyController::class, 'export'])->name('export');
                Route::post('/bulk', [AttendancePenaltyController::class, 'bulk'])->name('bulk');
                Route::post('/reconcile', [AttendancePenaltyController::class, 'reconcile'])->name('reconcile');
                Route::post('/disputes/{dispute}/review', [AttendancePenaltyController::class, 'disputeReview'])->whereNumber('dispute')->name('dispute.review');
                Route::prefix('{penalty}')->whereNumber('penalty')->group(function () {
                    Route::get('/',         [AttendancePenaltyController::class, 'show'])->name('show');
                    Route::post('/approve', [AttendancePenaltyController::class, 'approve'])->name('approve');
                    Route::post('/reject',  [AttendancePenaltyController::class, 'reject'])->name('reject');
                    Route::post('/cancel',  [AttendancePenaltyController::class, 'cancel'])->name('cancel');
                    Route::post('/waive',   [AttendancePenaltyController::class, 'waive'])->name('waive');
                    Route::post('/reverse', [AttendancePenaltyController::class, 'reverse'])->name('reverse');
                    Route::post('/pay',     [AttendancePenaltyController::class, 'pay'])->name('pay');
                    Route::post('/dispute', [AttendancePenaltyController::class, 'disputeStore'])->name('dispute.store');
                });
            });

            Route::get('/',            [AttendanceController::class, 'index'])->name('index');
            Route::get('/create',      [AttendanceController::class, 'create'])->name('create');
            Route::post('/',           [AttendanceController::class, 'store'])->name('store');
            Route::get('/history',     [AttendanceController::class, 'history'])->name('history');
            Route::get('/recap',       [AttendanceController::class, 'recap'])->name('recap');
            Route::get('/recap/past',  [AttendanceController::class, 'recapPast'])->name('recap.past');
            Route::get('/points',      [AttendanceController::class, 'points'])->name('points');
            Route::get('/calendar',    [AttendanceCalendarController::class, 'index'])->name('calendar');
            Route::get('/calendar/data', [AttendanceCalendarController::class, 'data'])->middleware('throttle:30,1')->name('calendar.data');

            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/', [AttendancePermissionController::class, 'index'])->name('index');
                Route::post('/{permission}/approve', [AttendancePermissionController::class, 'approve'])->whereNumber('permission')->name('approve');
                Route::post('/{permission}/reject', [AttendancePermissionController::class, 'reject'])->whereNumber('permission')->name('reject');
            });

            Route::get('/{session}',           [AttendanceController::class, 'show'])->whereNumber('session')->name('show');
            Route::put('/{session}',           [AttendanceController::class, 'update'])->whereNumber('session')->name('update');
            Route::post('/{session}/open',     [AttendanceController::class, 'openNow'])->whereNumber('session')->name('open-now');
            Route::post('/{session}/close',    [AttendanceController::class, 'close'])->whereNumber('session')->name('close');
            Route::post('/{session}/extend',   [AttendanceController::class, 'extend'])->whereNumber('session')->name('extend');
            Route::post('/{session}/cancel',   [AttendanceController::class, 'cancel'])->whereNumber('session')->name('cancel');
            Route::get('/{session}/manual',    [AttendanceController::class, 'manualForm'])->whereNumber('session')->name('manual.form');
            Route::post('/{session}/manual',   [AttendanceController::class, 'manualStore'])->whereNumber('session')->name('manual.store');
            Route::get('/{session}/monitor',   [AttendanceController::class, 'monitor'])->whereNumber('session')->name('monitor');
        });

        /* ---------- Financial ---------- */
        Route::prefix('financial')->name('financial.')->group(function () {
            Route::get('/dashboard', [FinancialDashboardController::class, 'index'])->name('dashboard');
            Route::get('generations/eligible-roles', [GenerationController::class, 'eligibleRoles'])->name('generations.eligible-roles');
            Route::resource('generations', GenerationController::class)->names('generations')->parameters(['generations' => 'generation'])->whereNumber('generation');
            Route::prefix('generations/{generation}')->name('generations.')->whereNumber('generation')->group(function () {
                Route::get('/search-users',   [GenerationController::class, 'searchUsers'])->middleware('throttle:30,1')->name('search-users');
                Route::get('/target',         [GenerationController::class, 'targetForm'])->name('target');
                Route::post('/target-preview', [GenerationController::class, 'targetPreview'])->name('target-preview');
                Route::post('/target-apply',  [GenerationController::class, 'targetApply'])->name('target-apply');
            });
            Route::resource('fee-plans', FeePlanController::class)->names('fee-plans')->parameters(['fee-plans' => 'feePlan'])->whereNumber('feePlan');
            Route::get('fee-plans/{feePlan}/duplicate', [FeePlanController::class, 'duplicate'])->whereNumber('feePlan')->name('fee-plans.duplicate');

            Route::prefix('invoices')->name('invoices.')->group(function () {
                Route::get('/',              [InvoiceController::class, 'index'])->name('index');
                Route::get('/generate',      [InvoiceController::class, 'generate'])->name('generate');
                Route::get('/check-tagihan', [InvoiceController::class, 'checkTagihan'])->name('check-tagihan');
                Route::get('/check-pengumuman', [InvoiceController::class, 'checkPengumuman'])->name('check-pengumuman');
                Route::get('/user-tagihan/{user}',    [InvoiceController::class, 'userTagihan'])->whereNumber('user')->name('user-tagihan');
                Route::get('/user-pengumuman/{user}', [InvoiceController::class, 'userPengumuman'])->whereNumber('user')->name('user-pengumuman');
                Route::get('/{invoice}', [InvoiceController::class, 'show'])->whereNumber('invoice')->name('show');
                 Route::get('/from-plan/{feePlan}/preview', [InvoiceController::class, 'generateFromPlan'])
        ->whereNumber('feePlan')->name('from-plan.preview');
    Route::post('/from-plan/{feePlan}', [InvoiceController::class, 'storeFromPlan'])
        ->whereNumber('feePlan')->name('from-plan.store');
            });
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/',         [PaymentController::class, 'index'])->name('index');
                Route::get('/{payment}', [PaymentController::class, 'show'])->whereNumber('payment')->name('show');
            });
            Route::resource('penalty-rules', PenaltyController::class)->names('penalty-rules')->parameters(['penalty-rules' => 'penaltyRule'])->whereNumber('penaltyRule');
            Route::prefix('points')->name('points.')->group(function () {
                Route::get('/',            [PointController::class, 'index'])->name('index');
                Route::get('/leaderboard', [PointController::class, 'leaderboard'])->name('leaderboard');
            });
            Route::resource('campaigns', CampaignController::class)->names('campaigns')->parameters(['campaigns' => 'campaign'])->whereNumber('campaign');
            Route::resource('holidays', CalendarController::class)->names('holidays')->parameters(['holidays' => 'holiday'])->whereNumber('holiday');
            Route::resource('academic-periods', AcademicPeriodController::class)->names('academic-periods')->parameters(['academic-periods' => 'academicPeriod'])->whereNumber('academicPeriod');
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/',       [ReportController::class, 'index'])->name('index');
                Route::get('/export', [ReportController::class, 'export'])->name('export');
            });
            Route::prefix('reactivations')->name('reactivations.')->group(function () {
                Route::get('/', [ReactivationController::class, 'index'])->name('index');
                Route::post('/{id}/approve', [ReactivationController::class, 'approve'])->whereNumber('id')->name('approve');
                Route::post('/{id}/reject',  [ReactivationController::class, 'reject'])->whereNumber('id')->name('reject');
            });

// Quick transitions
Route::post('campaigns/{campaign}/activate', [CampaignController::class, 'activate'])
    ->whereNumber('campaign')->name('campaigns.activate');
Route::post('campaigns/{campaign}/close', [CampaignController::class, 'close'])
    ->whereNumber('campaign')->name('campaigns.close');
Route::post('campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])
    ->whereNumber('campaign')->name('campaigns.cancel');
        });

        /* ---------- Game Center ---------- */
        Route::prefix('game')->name('game.')->group(function () {
            Route::get('/dashboard', [GameController::class, 'dashboard'])->name('dashboard');
            Route::prefix('games')->name('games.')->group(function () {
                Route::get('/',             [GameController::class, 'index'])->name('index');
                Route::get('/trash',        [GameController::class, 'trash'])->name('trash');
                Route::patch('/restore/{id}', [GameController::class, 'restore'])->whereNumber('id')->name('restore');
                Route::delete('/force/{id}', [GameController::class, 'forceDestroy'])->whereNumber('id')->name('force');
                Route::get('/create',      [GameController::class, 'create'])->name('create');
                Route::get('/create-form', [GameController::class, 'createForm'])->name('create-form');
                Route::post('/',           [GameController::class, 'store'])->name('store');
                Route::post('/submit-result', [GameController::class, 'submitResult'])->name('submit-result');
                Route::post('/submit-quiz',   [GameController::class, 'submitQuiz'])->name('submit-quiz');
                Route::post('/diagnostic-preview', [GameController::class, 'diagnosticPreview'])->name('diagnostic.preview');
                Route::get('/{game}',      [GameController::class, 'show'])->whereNumber('game')->name('show');
                Route::get('/{game}/edit', [GameController::class, 'edit'])->whereNumber('game')->name('edit');
                Route::put('/{game}',      [GameController::class, 'update'])->whereNumber('game')->name('update');
                Route::delete('/{game}',   [GameController::class, 'destroy'])->whereNumber('game')->name('destroy');
                Route::get('/{game}/gameplay/{type}/{token?}', [GameController::class, 'playGameplay'])->whereNumber('game')->name('gameplay');
                Route::get('/{game}/testing/{token}', [GameController::class, 'testingWithToken'])->whereNumber('game')->name('testing.token');
                Route::get('/{game}/result/{session}', [GameController::class, 'showResult'])->whereNumber('game')->name('result');
                Route::get('/{game}/diagnostic', [GameController::class, 'diagnostic'])->whereNumber('game')->name('diagnostic');
                Route::post('/{game}/fix-diagnostic', [GameController::class, 'fixDiagnostic'])->whereNumber('game')->name('fix-diagnostic');
                Route::patch('/{game}/publish',   [GameController::class, 'publish'])->whereNumber('game')->name('publish');
                Route::patch('/{game}/unpublish', [GameController::class, 'unpublish'])->whereNumber('game')->name('unpublish');
                Route::get('/{game}/settings', [GameController::class, 'settings'])->whereNumber('game')->name('settings');
                Route::get('/{game}/qr',       [GameController::class, 'generateQr'])->whereNumber('game')->name('qr');
            });
            Route::get('/leaderboard',         [LeaderboardController::class, 'index'])->name('leaderboard.index');
            Route::post('/leaderboard/rebuild', [LeaderboardController::class, 'rebuild'])->name('leaderboard.rebuild');
            Route::get('/games/{game}/leaderboard', [LeaderboardController::class, 'getLeaderboard'])->whereNumber('game')->name('games.leaderboard');
            Route::prefix('players')->name('players.')->group(function () {
                Route::get('/',              [PlayerController::class, 'index'])->name('index');
                Route::get('/{player}',      [PlayerController::class, 'show'])->whereNumber('player')->name('show');
                Route::get('/{player}/edit', [PlayerController::class, 'edit'])->whereNumber('player')->name('edit');
                Route::put('/{player}',      [PlayerController::class, 'update'])->whereNumber('player')->name('update');
                Route::delete('/{player}',   [PlayerController::class, 'destroy'])->whereNumber('player')->name('destroy');
            });
            Route::prefix('challenges')->name('challenges.')->group(function () {
                Route::get('/',               [ChallengeController::class, 'index'])->name('index');
                Route::get('/create',         [ChallengeController::class, 'create'])->name('create');
                Route::post('/',              [ChallengeController::class, 'store'])->name('store');
                Route::get('/{challenge}',    [ChallengeController::class, 'show'])->whereNumber('challenge')->name('show');
                Route::get('/{challenge}/edit', [ChallengeController::class, 'edit'])->whereNumber('challenge')->name('edit');
                Route::put('/{challenge}',    [ChallengeController::class, 'update'])->whereNumber('challenge')->name('update');
                Route::delete('/{challenge}', [ChallengeController::class, 'destroy'])->whereNumber('challenge')->name('destroy');
                Route::post('/{challenge}/toggle', [ChallengeController::class, 'toggle'])->whereNumber('challenge')->name('toggle');
            });
            Route::prefix('competitions')->name('competitions.')->group(function () {
                Route::get('/',                [CompetitionController::class, 'index'])->name('index');
                Route::get('/create',          [CompetitionController::class, 'create'])->name('create');
                Route::post('/',               [CompetitionController::class, 'store'])->name('store');
                Route::get('/{competition}',   [CompetitionController::class, 'show'])->whereNumber('competition')->name('show');
                Route::get('/{competition}/edit', [CompetitionController::class, 'edit'])->whereNumber('competition')->name('edit');
                Route::put('/{competition}',   [CompetitionController::class, 'update'])->whereNumber('competition')->name('update');
                Route::delete('/{competition}', [CompetitionController::class, 'destroy'])->whereNumber('competition')->name('destroy');
                Route::post('/{competition}/participants', [CompetitionController::class, 'addParticipants'])->whereNumber('competition')->name('participants.add');
                Route::post('/{competition}/bracket/generate', [CompetitionController::class, 'generateBracket'])->whereNumber('competition')->name('bracket.generate');
                Route::get('/{competition}/match/{match}', [CompetitionController::class, 'viewMatch'])->whereNumber('competition')->whereNumber('match')->name('match');
                Route::post('/{competition}/match/{match}/start', [CompetitionController::class, 'startMatch'])->whereNumber('competition')->whereNumber('match')->name('match.start');
                Route::post('/{competition}/match/{match}/score', [CompetitionController::class, 'updateMatchScore'])->whereNumber('competition')->whereNumber('match')->name('match.score');
                Route::get('/{competition}/live', [CompetitionController::class, 'live'])->whereNumber('competition')->name('live');
                Route::get('/{competition}/live/data', [CompetitionController::class, 'getLiveData'])->whereNumber('competition')->name('live.data');
            });
            Route::get('/testing',     [GameController::class, 'testing'])->name('testing');
            Route::post('/testing/load', [GameController::class, 'loadTest'])->name('testing.load');
            Route::get('/settings',    [SettingController::class, 'index'])->name('settings.index');
        });

        /* ---------- Approvals / Audit / Announcements / Reports / Settings ---------- */
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/',        fn () => view('owner.approvals.index'))->name('index');
            Route::get('/pending', fn () => view('owner.approvals.pending'))->name('pending');
            Route::get('/history', fn () => view('owner.approvals.history'))->name('history');
            Route::get('/{id}',    fn (int $id) => view('owner.approvals.show', compact('id')))->whereNumber('id')->name('show');
        });

        Route::prefix('audit')->name('audit.')->group(function () {
            Route::get('/',             fn () => view('owner.audit.index'))->name('index');
            Route::get('/activity',     fn () => view('owner.audit.activity'))->name('activity');
            Route::get('/data-changes', fn () => view('owner.audit.data-changes'))->name('data-changes');
            Route::get('/role-changes', fn () => view('owner.audit.role-changes'))->name('role-changes');
            Route::get('/security',     fn () => view('owner.audit.security'))->name('security');
        });

        Route::get('/leaderboard',  fn () => view('owner.leaderboard'))->name('leaderboard');
        Route::get('/assign-tasks', fn () => view('owner.assign-tasks'))->name('assign-tasks');

        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/all',       fn () => view('owner.announcements.all'))->name('all');
            Route::get('/officials', fn () => view('owner.announcements.officials'))->name('officials');
            Route::get('/members',   fn () => view('owner.announcements.members'))->name('members');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',         fn () => view('owner.reports.index'))->name('index');
            Route::get('/analysis', fn () => view('owner.reports.analysis'))->name('analysis');
        });

        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/profile', fn () => view('owner.data.profile'))->name('profile');
            Route::get('/qr',      fn () => view('owner.data.qr'))->name('qr');
            Route::get('/testing', fn () => view('owner.data.testing'))->name('testing');
        });

        Route::prefix('violations')->name('violations.')->group(function () {
            Route::get('/light',  fn () => view('owner.violations.light'))->name('light');
            Route::get('/medium', fn () => view('owner.violations.medium'))->name('medium');
            Route::get('/heavy',  fn () => view('owner.violations.heavy'))->name('heavy');
            Route::get('/block',  fn () => view('owner.violations.block'))->name('block');
        });

/* ---------- General Settings + Dashboard Builder ---------- */
Route::prefix('settings')->name('settings.')->group(function () {

    // 🔥 WAJIB — 3 route yang hilang
    Route::get('/',        fn () => redirect()->route('owner.settings.general'))->name('index');
    Route::get('/profile', [\App\Http\Controllers\Owner\GeneralSettingsController::class, 'profile'])->name('profile');
    Route::get('/audit',   [\App\Http\Controllers\Owner\GeneralSettingsController::class, 'audit'])->name('audit');

    // Hub (sudah ada)
    Route::get('/general',  [\App\Http\Controllers\Owner\GeneralSettingsController::class, 'index'])
        ->name('general');
    Route::get('/dashboard-history', [\App\Http\Controllers\Owner\GeneralSettingsController::class, 'history'])
        ->name('dashboard-history');

    // Custom Cards (sudah ada)
    Route::get('/dashboard-cards/create',  [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'createCard'])->name('dashboard-cards.create');
    Route::post('/dashboard-cards',        [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'storeCard'])->name('dashboard-cards.store');
    Route::get('/dashboard-cards/{item}/edit', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'editCard'])->whereNumber('item')->name('dashboard-cards.edit');
    Route::put('/dashboard-cards/{item}',  [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'updateCard'])->whereNumber('item')->name('dashboard-cards.update');
    Route::patch('/dashboard-cards/{item}/toggle', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'toggleCard'])->whereNumber('item')->name('dashboard-cards.toggle');
    Route::delete('/dashboard-cards/{item}', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'destroyCard'])->whereNumber('item')->name('dashboard-cards.destroy');

    // Shortcuts (sudah ada)
    Route::get('/dashboard-shortcuts/create', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'createShortcut'])->name('dashboard-shortcuts.create');
    Route::post('/dashboard-shortcuts',       [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'storeShortcut'])->name('dashboard-shortcuts.store');
    Route::get('/dashboard-shortcuts/{item}/edit', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'editShortcut'])->whereNumber('item')->name('dashboard-shortcuts.edit');
    Route::put('/dashboard-shortcuts/{item}', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'updateShortcut'])->whereNumber('item')->name('dashboard-shortcuts.update');
    Route::patch('/dashboard-shortcuts/{item}/toggle', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'toggleShortcut'])->whereNumber('item')->name('dashboard-shortcuts.toggle');
    Route::delete('/dashboard-shortcuts/{item}', [\App\Http\Controllers\Owner\DashboardBuilderController::class, 'destroyShortcut'])->whereNumber('item')->name('dashboard-shortcuts.destroy');
});

        Route::prefix('restore')->name('restore.')->group(function () {
            Route::get('/', [RestoreController::class, 'index'])->name('index');
            Route::post('/backup', [RestoreController::class, 'backup'])->name('backup');
            Route::post('/recover', [RestoreController::class, 'recover'])->name('recover');
        });
    }); // end owner


Route::prefix('superadmin')->name('superadmin.')
    ->middleware(['owner.explore.bypass', 'role:superadmin'])
    ->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Superadmin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/ringkasan', fn () => view('superadmin.dashboard', ['tab' => 'ringkasan']))->name('ringkasan');

        /* PENGUMUMAN */
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/all',       fn () => view('superadmin.announcements.all'))->name('all');
            Route::get('/create',    fn () => view('superadmin.announcements.all', ['create' => 1]))->name('create');
            Route::post('/',         fn () => back()->with('success', 'Pengumuman dikirim.'))->name('store');
            Route::get('/officials', fn () => view('superadmin.announcements.officials'))->name('officials');
            Route::get('/members',   fn () => view('superadmin.announcements.members'))->name('members');
            Route::get('/history',   fn () => view('superadmin.announcements.members', ['tab' => 'history']))->name('history');
        });

        /* LAPORAN */
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/analysis',    fn () => view('superadmin.reports.analysis'))->name('analysis');
            Route::get('/rooms',       fn () => view('superadmin.reports.analysis', ['type' => 'ruangan']))->name('rooms');
            Route::get('/bookings',    fn () => view('superadmin.reports.analysis', ['type' => 'booking']))->name('bookings');
            Route::get('/schedules',   fn () => view('superadmin.reports.analysis', ['type' => 'jadwal']))->name('schedules');
            Route::get('/permissions', fn () => view('superadmin.reports.analysis', ['type' => 'izin']))->name('permissions');
            Route::get('/export',      fn () => back()->with('success', 'Export sedang diproses.'))->name('export');
        });

        /* KONTAK */
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/all',       fn () => view('superadmin.contacts.all'))->name('all');
            Route::get('/officials', fn () => view('superadmin.contacts.officials'))->name('officials');
            Route::get('/members',   fn () => view('superadmin.contacts.members'))->name('members');
        });

        Route::get('/notifications', fn () => view('superadmin.notifications'))->name('notifications');
    });


    /* ════════════════════════════════════════════════════════
     | 5.4 KETUA  — SIMPLIFIED · REUSE OWNER PAGES
     * ════════════════════════════════════════════════════════ */
Route::prefix('ketua')->name('ketua.')

    ->middleware(['owner.explore.bypass', 'role:ketua'])
        ->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\KetuaDashboardController::class, 'index'])->name('dashboard');

Route::prefix('manajemen-anggota')->name('manajemen-anggota.')->group(function () {
    $ctrl = \App\Http\Controllers\Ketua\MemberManagementController::class;

    Route::get('/',               [$ctrl, 'index'])->name('index');
    Route::get('/data',           [$ctrl, 'data'])->name('data');
    Route::post('/bulk-action',   [$ctrl, 'bulkAction'])->name('bulk-action');
    Route::post('/manage-role',   [$ctrl, 'manageRole'])->name('manage-role');
    Route::get('/{id}',           [$ctrl, 'show'])->whereNumber('id')->name('show');
});
Route::prefix('lms')->name('lms.')->group(function () {    // Groups
    Route::get('/groups/create', [\App\Http\Controllers\Owner\Lms\GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups',       [\App\Http\Controllers\Owner\Lms\GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [\App\Http\Controllers\Owner\Lms\GroupController::class, 'show'])
        ->whereNumber('group')->name('groups.show');

    // Items
    Route::get('/items/create', [\App\Http\Controllers\Owner\Lms\ItemController::class, 'create'])->name('items.create');
    Route::post('/items',       [\App\Http\Controllers\Owner\Lms\ItemController::class, 'store'])->name('items.store');
    Route::get('/items/search-users', [\App\Http\Controllers\Owner\Lms\ItemController::class, 'searchUsers'])
        ->middleware('throttle:60,1')->name('items.search-users');
    Route::get('/items/{item}', [\App\Http\Controllers\Owner\Lms\ItemController::class, 'show'])
        ->whereNumber('item')->name('items.show');

    // Items within group
    Route::get('/groups/{group}/items/create', [\App\Http\Controllers\Owner\Lms\ItemController::class, 'create'])
        ->whereNumber('group')->name('groups.items.create');
    Route::post('/groups/{group}/items',       [\App\Http\Controllers\Owner\Lms\ItemController::class, 'store'])
        ->whereNumber('group')->name('groups.items.store');
});
/* ── Events (Ketua) ── */
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/create', function () {
        return view('ketua.events.create');
    })->name('create');
    Route::post('/', function () {
        return redirect()
            ->route('ketua.kegiatan', ['tab' => 'events'])
            ->with('success', 'Event berhasil dibuat.');
    })->name('store');
});
/* ══════════════════════════════════════════════════════════════
 | MANAJEMEN PENGURUS (KETUA)
 | Role: Ketua (3) + Pengurus (4)
 * ══════════════════════════════════════════════════════════════ */
Route::prefix('manajemen-pengurus')->name('manajemen-pengurus.')->group(function () {

    /* ═══ MAIN INDEX — pakai Controller (biar eligibleCandidates terkirim) ═══ */
    Route::get('/', [\App\Http\Controllers\Ketua\PengurusController::class, 'index'])
        ->name('index');

    /* ═══ STATIC ROUTES — HARUS SEBELUM /{id} ═══ */
    Route::get('/create', [\App\Http\Controllers\Ketua\PengurusController::class, 'create'])
        ->name('create');

    Route::post('/setup-pin', [\App\Http\Controllers\Ketua\PengurusController::class, 'setupPin'])
        ->name('setup-pin');

    Route::post('/bulk-action', [\App\Http\Controllers\Ketua\PengurusController::class, 'bulkAction'])
        ->name('bulk-action');

    Route::post('/manage-role', [\App\Http\Controllers\Ketua\PengurusController::class, 'manageRole'])
        ->name('manage-role');

    /* ═══ DYNAMIC — WILDCARD TERAKHIR ═══ */
    Route::get('/{id}', [\App\Http\Controllers\Ketua\PengurusController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});
/* ── Divisi (Ketua) ── */
Route::prefix('divisi')->name('divisi.')->group(function () {

    Route::get('/', function (\Illuminate\Http\Request $request) {
        $divisions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $stats = [
            'total'         => 0,
            'aktif'         => 0,
            'nonaktif'      => 0,
            'total_anggota' => 0,
        ];

        try {
            $model = null;
            if (class_exists(\App\Models\Division::class)) {
                $model = \App\Models\Division::class;
            } elseif (class_exists(\App\Models\Divisi::class)) {
                $model = \App\Models\Divisi::class;
            }

            if ($model) {
                $stats = [
                    'total'         => (int) $model::count(),
                    'aktif'         => (int) $model::where('status', 'aktif')->count(),
                    'nonaktif'      => (int) $model::where(function ($q) {
                        $q->where('status', '!=', 'aktif')->orWhereNull('status');
                    })->count(),
                    'total_anggota' => (int) \DB::table('division_members')
                        ->where('status', 'aktif')->count(),
                ];

                $query = $model::query();

                if ($q = $request->input('q')) {
                    $query->where(function ($w) use ($q) {
                        $w->where('name', 'like', "%{$q}%")
                          ->orWhere('code', 'like', "%{$q}%")
                          ->orWhere('description', 'like', "%{$q}%");
                    });
                }

                if ($status = $request->input('status')) {
                    if ($status === 'aktif') {
                        $query->where('status', 'aktif');
                    } elseif ($status === 'nonaktif') {
                        $query->where(function ($w) {
                            $w->where('status', '!=', 'aktif')->orWhereNull('status');
                        });
                    }
                }

                if (method_exists($model, 'members')) {
                    $query->withCount('members');
                }

                $divisions = $query->orderBy('name')->paginate(20)->withQueryString();
            }
        } catch (\Throwable $e) {
            \Log::error('[Ketua] divisi.index error', ['error' => $e->getMessage()]);
        }

        return view('ketua.divisi.index', compact('divisions', 'stats'));
    })->name('index');

    /* ── Custom (Create / Edit) ── */
    Route::get('/custom', function (\Illuminate\Http\Request $request) {
        $division = null;
        if ($request->filled('id')) {
            $model = class_exists(\App\Models\Division::class)
                ? \App\Models\Division::class
                : \App\Models\Divisi::class;
            $division = $model::find($request->input('id'));
        }

        $users = \App\Models\User::query()
            ->whereIn('level_id', [3, 4, 5])
            ->where('is_active', true)
            ->with('level:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'nim', 'level_id']);

        $userOptions = $users->map(fn ($u) => [
            'id'    => $u->id,
            'name'  => $u->name,
            'nim'   => $u->nim ?? '-',
            'level' => $u->level->name ?? 'Member',
        ])->values()->all();

        return view('ketua.divisi.custom', compact('division', 'users', 'userOptions'));
    })->name('custom');

    /* ── Store / Update / Delete ── */
    Route::post('/store', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:aktif,nonaktif',
            'max_members' => 'nullable|integer|min:1',
            'ketua_label' => 'nullable|string|max:100',
            'ketua_id'    => 'required|exists:users,id',
        ]);

        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;

        try {
            \DB::beginTransaction();

            $division = $model::create([
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->status,
                'max_members' => $request->max_members,
                'ketua_label' => $request->ketua_label ?? 'Ketua',
                'created_by'  => auth()->id(),
            ]);

            // Tambah ketua
            \DB::table('division_members')->insert([
                'division_id' => $division->id,
                'user_id'     => $request->ketua_id,
                'position'    => 'ketua',
                'status'      => 'aktif',
                'joined_at'   => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Tambah pengurus
            foreach ((array) $request->input('pengurus_ids', []) as $idx => $userId) {
                if (empty($userId)) continue;
                \DB::table('division_members')->insert([
                    'division_id' => $division->id,
                    'user_id'     => $userId,
                    'position'    => 'pengurus',
                    'status'      => 'aktif',
                    'notes'       => $request->input("pengurus_jabatan.{$idx}"),
                    'joined_at'   => now(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            \DB::commit();

            return redirect()
                ->route('ketua.divisi.show', $division->id)
                ->with('success', 'Divisi berhasil dibuat.');
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    })->name('store');

    Route::delete('/{division}', function (int $division) {
        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;
        $model::findOrFail($division)->delete();
        return redirect()->route('ketua.divisi.index')->with('success', 'Divisi dihapus.');
    })->whereNumber('division')->name('destroy');

    /* ── Anggota Divisi ── */
    Route::get('/anggota/{divisionId?}', function (\Illuminate\Http\Request $request, ?int $divisionId = null) {
        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;

        $divisions       = $model::orderBy('name')->get();
        $selectedId      = $divisionId ?? $request->input('division_id');
        $selectedDivision= $selectedId ? $model::find($selectedId) : null;

        $members = collect();
        if ($selectedDivision && method_exists($selectedDivision, 'members')) {
            $members = $selectedDivision->members()->with('user.level')->get();
        }

        return view('ketua.divisi.anggota', compact(
            'divisions', 'selectedDivision', 'selectedId', 'members'
        ));
    })->name('anggota');

    /* ── Hak Akses ── */
    Route::get('/akses', function (\Illuminate\Http\Request $request) {
        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;

        $divisions        = $model::orderBy('name')->get();
        $selectedDivisionId = $request->input('division_id', $divisions->first()->id ?? null);
        $selectedDivision = $selectedDivisionId ? $model::find($selectedDivisionId) : null;

        $members = collect();
        if ($selectedDivision && method_exists($selectedDivision, 'members')) {
            $members = $selectedDivision->members()->with('user')->get();
        }

        return view('ketua.divisi.akses', compact(
            'divisions', 'selectedDivisionId', 'selectedDivision', 'members'
        ));
    })->name('akses');

    Route::post('/{division}/permissions', function (\Illuminate\Http\Request $request, int $division) {
        $permissions = $request->input('permissions', []);
        foreach ($permissions as $memberId => $perms) {
            \DB::table('division_members')->where('id', $memberId)->update([
                'can_view'           => isset($perms['view']) ? 1 : 0,
                'can_announcement'   => isset($perms['announcement']) ? 1 : 0,
                'can_message'        => isset($perms['message']) ? 1 : 0,
                'can_view_members'   => isset($perms['view_members']) ? 1 : 0,
                'can_manage_members' => isset($perms['manage_members']) ? 1 : 0,
                'can_manage_content' => isset($perms['manage_content']) ? 1 : 0,
                'updated_at'         => now(),
            ]);
        }
        return back()->with('success', 'Hak akses berhasil diperbarui.');
    })->whereNumber('division')->name('update-permissions');

    /* ── Pesan ── */
    Route::get('/pesan', function () {
        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;
        $divisions = $model::orderBy('name')->get();
        return view('ketua.divisi.pesan', compact('divisions'));
    })->name('pesan');

    Route::post('/pesan/store', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'division_id' => 'required|integer',
            'subject'     => 'required|string|max:255',
            'message'     => 'required|string|max:5000',
        ]);
        return back()->with('success', 'Pesan berhasil dikirim.');
    })->name('pesan.store');

    /* ── Pengumuman ── */
    Route::get('/pengumuman', function () {
        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;
        $divisions = $model::orderBy('name')->get();
        return view('ketua.divisi.pengumuman', compact('divisions'));
    })->name('pengumuman');

    Route::post('/announcement/store', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string|max:10000',
        ]);
        return back()->with('success', 'Pengumuman berhasil dikirim.');
    })->name('announcement.store');

    /* ── Invite ── */
    Route::get('/invite', function (\Illuminate\Http\Request $request) {
        $model = class_exists(\App\Models\Division::class)
            ? \App\Models\Division::class
            : \App\Models\Divisi::class;

        $divisions      = $model::orderBy('name')->get();
        $selectedId     = $request->input('division_id');
        $selectedDivision = $selectedId ? $model::find($selectedId) : null;

        $users = \App\Models\User::query()
            ->whereIn('level_id', [3, 4, 5])
            ->where('is_active', true)
            ->with('level:id,name')
            ->orderBy('name')
            ->get();

        return view('ketua.divisi.invite', compact(
            'divisions', 'selectedDivision', 'users'
        ));
    })->name('invite');
/* ═══ ADD / REMOVE / MOVE MEMBER — HARUS DI ATAS WILDCARD /{id} ═══ */

/* Tambah anggota ke divisi */
Route::post('/{division}/add-member', function (\Illuminate\Http\Request $request, int $division) {
    $request->validate([
        'user_id'  => 'required|exists:users,id',
        'position' => 'required|in:ketua,pengurus,anggota',
    ]);

    $model = class_exists(\App\Models\Division::class)
        ? \App\Models\Division::class
        : \App\Models\Divisi::class;

    $div = $model::findOrFail($division);

    // Cek duplikat
    $exists = \DB::table('division_members')
        ->where('division_id', $division)
        ->where('user_id', $request->user_id)
        ->exists();

    if ($exists) {
        return back()->with('warning', 'User sudah menjadi anggota divisi ini.');
    }

    // Cek max members
    if (!empty($div->max_members)) {
        $current = \DB::table('division_members')
            ->where('division_id', $division)
            ->where('status', 'aktif')
            ->count();
        if ($current >= $div->max_members) {
            return back()->with('error', 'Divisi sudah penuh (max: ' . $div->max_members . ').');
        }
    }

    // Kalau posisi ketua → turunkan ketua lama ke pengurus
    if ($request->position === 'ketua') {
        \DB::table('division_members')
            ->where('division_id', $division)
            ->where('position', 'ketua')
            ->update(['position' => 'pengurus', 'updated_at' => now()]);
    }

    \DB::table('division_members')->insert([
        'division_id' => $division,
        'user_id'     => $request->user_id,
        'position'    => $request->position,
        'status'      => 'aktif',
        'joined_at'   => now(),
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    return back()->with('success', 'Anggota berhasil ditambahkan ke divisi.');
})->whereNumber('division')->name('add-member');

/* Hapus anggota dari divisi */
Route::delete('/{division}/remove-member/{user}', function (int $division, int $user) {
    \DB::table('division_members')
        ->where('division_id', $division)
        ->where('user_id', $user)
        ->delete();

    return back()->with('success', 'Anggota berhasil dikeluarkan dari divisi.');
})->whereNumber('division')->whereNumber('user')->name('remove-member');

/* Update jabatan anggota */
Route::put('/{division}/update-member/{member}', function (\Illuminate\Http\Request $request, int $division, int $member) {
    $request->validate([
        'position' => 'required|in:ketua,pengurus,anggota',
    ]);

    if ($request->position === 'ketua') {
        \DB::table('division_members')
            ->where('division_id', $division)
            ->where('position', 'ketua')
            ->where('id', '!=', $member)
            ->update(['position' => 'pengurus', 'updated_at' => now()]);
    }

    \DB::table('division_members')->where('id', $member)->update([
        'position'   => $request->position,
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Jabatan berhasil diperbarui.');
})->whereNumber('division')->whereNumber('member')->name('update-member');

/* Pindahkan anggota ke divisi lain */
Route::post('/{division}/move-member/{member}', function (\Illuminate\Http\Request $request, int $division, int $member) {
    $request->validate([
        'target_division_id' => 'required|integer|exists:divisions,id',
    ]);

    \DB::table('division_members')->where('id', $member)->update([
        'division_id' => $request->target_division_id,
        'updated_at'  => now(),
    ]);

    return back()->with('success', 'Anggota berhasil dipindahkan.');
})->whereNumber('division')->whereNumber('member')->name('move-member');

/* Update divisi (PUT) */
Route::put('/{division}', function (\Illuminate\Http\Request $request, int $division) {
    $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string|max:2000',
        'status'      => 'required|in:aktif,nonaktif',
        'max_members' => 'nullable|integer|min:1',
        'ketua_label' => 'nullable|string|max:100',
    ]);

    $model = class_exists(\App\Models\Division::class)
        ? \App\Models\Division::class
        : \App\Models\Divisi::class;

    $model::findOrFail($division)->update([
        'name'        => $request->name,
        'description' => $request->description,
        'status'      => $request->status,
        'max_members' => $request->max_members,
        'ketua_label' => $request->ketua_label ?? 'Ketua',
    ]);

    return redirect()
        ->route('ketua.divisi.show', $division)
        ->with('success', 'Divisi berhasil diperbarui.');
})->whereNumber('division')->name('update');

/* Counter anggota (AJAX) */
Route::get('/{division}/members-count', function (int $division) {
    $count = \DB::table('division_members')
        ->where('division_id', $division)
        ->where('status', 'aktif')
        ->count();
    return response()->json(['count' => $count]);
})->whereNumber('division')->name('members-count');

    /* ── SHOW (paling bawah — wildcard) ── */
    Route::get('/{id}', function (int $id) {
        $division = null;

        try {
            if (class_exists(\App\Models\Division::class)) {
                $division = \App\Models\Division::find($id);
            }
            if (! $division && class_exists(\App\Models\Divisi::class)) {
                $division = \App\Models\Divisi::find($id);
            }
            if ($division) {
                try { $division->loadMissing('members.user.level'); }
                catch (\Throwable $e) { try { $division->loadMissing('members'); } catch (\Throwable $e2) {} }
            }
        } catch (\Throwable $e) {
            \Log::error('[Ketua] divisi.show error', ['id' => $id, 'error' => $e->getMessage()]);
        }

        if (! $division) {
            return response()->view('ketua.divisi.not-found', ['divisionId' => $id], 404);
        }

        return view('ketua.divisi.show', compact('division'));
    })->whereNumber('id')->name('show');
});
/* ── Kelompok ── */
Route::prefix('kelompok')->name('kelompok.')->group(function () {
    Route::get('/', function () {
        $groups = collect();
        try {
            if (class_exists(\App\Models\Kelompok::class)) {
                $groups = \App\Models\Kelompok::withCount('members')->orderBy('name')->paginate(20);
            } elseif (class_exists(\App\Models\Group::class)) {
                $groups = \App\Models\Group::withCount('members')->orderBy('name')->paginate(20);
            } else {
                $groups = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            }
        } catch (\Throwable $e) {
            $groups = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }
        return view('ketua.kelompok.index', compact('groups'));
    })->name('index');

    Route::get('/{id}', function (int $id) {
        try {
            $group = class_exists(\App\Models\Kelompok::class)
                ? \App\Models\Kelompok::with('members.level')->findOrFail($id)
                : \App\Models\Group::with('members.level')->findOrFail($id);
        } catch (\Throwable $e) {
            abort(404);
        }
        return view('ketua.kelompok.show', compact('group'));
    })->whereNumber('id')->name('show');
});
Route::get('/kegiatan', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    $tab  = $request->input('tab', 'events'); // events | tasks | materials | groups

    /* ═══════════════════════════════════════════════════
       EVENTS
       ═══════════════════════════════════════════════════ */
    $events = collect();
    if (class_exists(\App\Models\Event::class)
        && \Illuminate\Support\Facades\Schema::hasTable('events')) {
        try {
            $events = \App\Models\Event::latest()->paginate(15, ['*'], 'events_page');
        } catch (\Throwable $e) {}
    }

    /* ═══════════════════════════════════════════════════
       LMS TASKS & MATERIALS (dari Owner)
       ═══════════════════════════════════════════════════ */
    $lmsStats = ['total' => 0, 'tasks' => 0, 'materials' => 0, 'groups' => 0];

    $lmsTasks     = collect();
    $lmsMaterials = collect();
    $lmsGroups    = collect();
    $myStats      = ['new' => 0, 'progress' => 0, 'done' => 0, 'late' => 0];

    if (class_exists(\App\Models\Lms\LmsItem::class)) {
        try {
            $lmsStats = [
                'total'     => \App\Models\Lms\LmsItem::count(),
                'tasks'     => \App\Models\Lms\LmsItem::where('type', 'task')->count(),
                'materials' => \App\Models\Lms\LmsItem::where('type', 'material')->count(),
                'groups'    => \App\Models\Lms\LmsGroup::where('status', 'active')->count(),
            ];

            $myStats = [
                'new' => \App\Models\Lms\LmsItemUser::where('user_id', $user->id)
                    ->where('status', 'new')->count(),
                'progress' => \App\Models\Lms\LmsItemUser::where('user_id', $user->id)
                    ->whereIn('status', ['viewed', 'in_progress'])->count(),
                'done' => \App\Models\Lms\LmsItemUser::where('user_id', $user->id)
                    ->whereIn('status', ['completed', 'submitted', 'pending_review'])->count(),
                'late' => \App\Models\Lms\LmsItemUser::where('user_id', $user->id)
                    ->where('status', 'late')->count(),
            ];

            // Tasks
            $lmsTasks = \App\Models\Lms\LmsItem::query()
                ->where('type', 'task')
                ->with([
                    'group:id,name',
                    'creator:id,name,avatar_url,level_id',
                    'creator.level:id,name',
                ])
                ->orderByDesc('created_at')
                ->paginate(12, ['*'], 'tasks_page');

            // Materials
            $lmsMaterials = \App\Models\Lms\LmsItem::query()
                ->where('type', 'material')
                ->with([
                    'group:id,name',
                    'creator:id,name,avatar_url,level_id',
                    'creator.level:id,name',
                ])
                ->orderByDesc('created_at')
                ->paginate(12, ['*'], 'materials_page');

            // Groups
            $lmsGroups = \App\Models\Lms\LmsGroup::query()
                ->where('status', 'active')
                ->with([
                    'creator:id,name,avatar_url,level_id',
                    'creator.level:id,name',
                ])
                ->withCount('items')
                ->orderByDesc('updated_at')
                ->limit(6)
                ->get();

        } catch (\Throwable $e) {
            \Log::warning('[Ketua Kegiatan] LMS gagal load', ['error' => $e->getMessage()]);
        }
    }

    return view('ketua.event-kegiatan', compact(
        'events',
        'tab',
        'lmsStats',
        'myStats',
        'lmsTasks',
        'lmsMaterials',
        'lmsGroups'
    ));
})->name('kegiatan');

        /* Presensi Overview */
        Route::get('/presensi', function () {
            $sessions = collect();
            if (class_exists(\App\Models\AttendanceSession::class)
                && \Illuminate\Support\Facades\Schema::hasTable('attendance_sessions')) {
                try { $sessions = \App\Models\AttendanceSession::latest()->paginate(15); } catch (\Throwable $e) {}
            }
            return view('ketua.presensi-qr', compact('sessions'));
        })->name('presensi');

        /* Keuangan Overview */
        // KETUA — Uangkas (enhanced)
Route::get('/keuangan', [\App\Http\Controllers\Ketua\UangkasController::class, 'index'])->name('keuangan');
Route::get('/uangkas',  [\App\Http\Controllers\Ketua\UangkasController::class, 'index'])->name('uangkas');

        /* LeaderPoint */
        Route::get('/poin-prestasi', function () {
            $topUsers = User::query()->where('is_active', true)->whereNull('deleted_at')
                ->withSum(['pointHistories as total_points' => fn ($q) => $q->where('status', 'approved')], 'points')
                ->orderByDesc('total_points')->limit(20)->get();
            return view('ketua.poin-prestasi', compact('topUsers'));
        })->name('poin-prestasi');

        /* Pengumuman */
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            $ctrl = AnnouncementController::class;
            Route::get('/',        [$ctrl, 'index'])->name('index');
            Route::get('/create',  [$ctrl, 'create'])->name('create');
            Route::post('/',       [$ctrl, 'store'])->name('store');
            Route::get('/{id}',    [$ctrl, 'show'])->whereNumber('id')->name('show');
            Route::delete('/{id}', [$ctrl, 'destroy'])->whereNumber('id')->name('destroy');
        });

        /* Placeholder helper */
        $page = function (string $view) {
            return function () use ($view) {
                if (view()->exists($view)) return view($view);
                return response(
                    '<!doctype html><html><head><title>Coming Soon</title>'
                    . '<script src="https://cdn.tailwindcss.com"></script></head>'
                    . '<body class="flex items-center justify-center min-h-screen bg-gray-50">'
                    . '<div class="text-center p-8 bg-white rounded-2xl shadow-sm border border-gray-200 max-w-md">'
                    . '<div class="text-5xl mb-4">🚧</div>'
                    . '<h1 class="text-xl font-bold text-gray-800 mb-2">Halaman Sedang Dikembangkan</h1>'
                    . '<p class="text-sm text-gray-500 mb-3">View: <code class="px-2 py-1 bg-gray-100 rounded text-xs">' . e($view) . '</code></p>'
                    . '<a href="' . url()->previous() . '" class="inline-block mt-2 px-4 py-2 text-sm rounded-lg bg-purple-600 text-white hover:bg-purple-700">← Kembali</a>'
                    . '</div></body></html>', 200);
            };
        };
/* ═══ Notification Center Ketua ═══ */
/* ═══ NOTIFICATION CENTER — KETUA ═══ */
Route::prefix('notifications')->name('notifications.')->group(function () {
    $ctrl = \App\Http\Controllers\Ketua\NotificationController::class;

    Route::get('/',                     [$ctrl, 'index'])->name('index');
    Route::get('/unread-count',         [$ctrl, 'unreadCount'])->name('unread-count');
    Route::get('/latest',               [$ctrl, 'latest'])->name('latest');
    Route::get('/poll',                 [$ctrl, 'poll'])->name('poll');

    // Aksi per-notifikasi
    Route::get('/{notification}',           [$ctrl, 'show'])->name('show');
    Route::post('/{notification}/read',     [$ctrl, 'markRead'])->name('read');
    Route::post('/{notification}/unread',   [$ctrl, 'markUnread'])->name('unread');
    Route::post('/{notification}/star',     [$ctrl, 'toggleStar'])->name('toggle-star');
    Route::post('/{notification}/archive',  [$ctrl, 'archive'])->name('archive');
    Route::post('/{notification}/unarchive',[$ctrl, 'unarchive'])->name('unarchive');
    Route::delete('/{notification}',        [$ctrl, 'destroy'])->name('destroy');

    // Bulk
    Route::post('/mark-all-read',   [$ctrl, 'markAllRead'])->name('mark-all-read');
    Route::delete('/clear-read',    [$ctrl, 'destroyAll'])->name('destroy-all');
});
/* ═══════════════════════════════════════════════════════════
 | MANAJEMEN QR — KETUA (view-only, reuse QrController)
 ═══════════════════════════════════════════════════════════ */
Route::prefix('management/qr')->name('management.qr.')->group(function () {
    Route::get('/',                 [\App\Http\Controllers\QrController::class, 'index'])->name('index');
    Route::get('/generate',         [\App\Http\Controllers\QrController::class, 'generate'])->name('generate');
    Route::get('/detail/{user}',    [\App\Http\Controllers\QrController::class, 'detail'])->name('detail');
    Route::post('/revoke/{user}',   [\App\Http\Controllers\QrController::class, 'revoke'])->name('revoke');
    Route::post('/rotate/{user}',   [\App\Http\Controllers\QrController::class, 'rotate'])->name('rotate');
    Route::get('/history/{user}',   [\App\Http\Controllers\QrController::class, 'history'])->name('history');
    Route::delete('/delete-selected',[\App\Http\Controllers\QrController::class, 'deleteSelected'])->name('delete-selected');
    Route::get('/download-qr/{qr}/{format}', [\App\Http\Controllers\QrController::class, 'download'])->name('download');
});
        Route::get('/approval',      $page('ketua.approval.dashboard'))->name('approval');
        Route::get('/laporan',       $page('ketua.laporan-analitik'))->name('laporan');
        Route::get('/pengaturan',    $page('ketua.pengaturan'))->name('pengaturan');

    });


/* Manajemen NFC — KETUA (view only, tanpa revoke/hapus) */
Route::prefix('management/nfc')->name('management.nfc.')->group(function () {
    Route::get('/detail/{user}', [\App\Http\Controllers\Owner\Management\NfcController::class, 'show'])
        ->whereNumber('user')->name('detail');
});

    /* ════════════════════════════════════════════════════════
     | 5.5 PENGURUS  — DINAMIS BERDASARKAN JABATAN/DIVISI
     * ════════════════════════════════════════════════════════ */
Route::prefix('pengurus')->name('pengurus.')
    ->middleware(['owner.explore.bypass', 'role:pengurus'])
        ->group(function () {

Route::get('/dashboard', function () {
    $user = auth()->user();
    $dashboardData = app(\App\Services\DashboardItemService::class)->resolveFor($user);
    return view('pengurus.dashboard', compact('user', 'dashboardData'));
})->name('dashboard');
        /* Misi */
        Route::get('/misi',         fn () => view('pengurus.tugas', ['mode' => 'misi']))->name('misi');
        Route::get('/misi/saya',    fn () => view('pengurus.tugas', ['mode' => 'misi', 'scope' => 'saya']))->name('misi.saya');
        Route::get('/misi/divisi',  fn () => view('pengurus.tugas', ['mode' => 'misi', 'scope' => 'divisi']))->name('misi.divisi');
        Route::get('/misi/selesai', fn () => view('pengurus.tugas', ['mode' => 'misi', 'scope' => 'selesai']))->name('misi.selesai');

        /* Tugas */
        Route::get('/tugas',        fn () => view('pengurus.tugas'))->name('tugas');
        Route::get('/tugas/divisi', fn () => view('pengurus.tugas', ['scope' => 'divisi']))->name('tugas.divisi');

        /* Event */
        Route::get('/event',           fn () => view('pengurus.pengumuman', ['tab' => 'event']))->name('event');
        Route::get('/event/saya',      fn () => view('pengurus.pengumuman', ['tab' => 'event', 'scope' => 'saya']))->name('event.saya');
        Route::get('/event/kehadiran', fn () => view('pengurus.pengumuman', ['tab' => 'kehadiran']))->name('event.kehadiran');
Route::prefix('keuangan')->name('keuangan.')->group(function () {
    Route::get('/',     [\App\Http\Controllers\Pengurus\UangkasController::class, 'index'])->name('index');
    Route::get('/uangkas', [\App\Http\Controllers\Pengurus\UangkasController::class, 'index'])->name('uangkas');
});
        /* Umum */
        Route::get('/pengumuman', fn () => view('pengurus.pengumuman'))->name('pengumuman');
        Route::get('/kontak',     fn () => view('pengurus.kontak'))->name('kontak');
        Route::get('/anggota',    fn () => view('pengurus.anggota'))->name('anggota');
        Route::get('/kelompok',   fn () => view('pengurus.kelompok'))->name('kelompok');
        Route::get('/presensi',   fn () => view('pengurus.data-diri', ['tab' => 'presensi']))->name('presensi');
        Route::get('/presensi/izin', fn () => view('pengurus.data-diri', ['tab' => 'izin']))->name('presensi.izin');
        Route::get('/data-diri',  fn () => view('pengurus.data-diri'))->name('data-diri');
        Route::get('/pengaturan', fn () => view('pengurus.pengaturan'))->name('pengaturan');
        Route::get('/peringatan', fn () => view('pengurus.peringatan'))->name('peringatan');
        Route::get('/notifications', fn () => view('pengurus.notifications'))->name('notifications');

        /* ---------- Menu khusus per jabatan ---------- */
        Route::middleware([CheckSecretaryAccess::class])
            ->prefix('secretary')->name('secretary.')->group(function () {
                Route::get('/dashboard',   fn () => app(SecretaryController::class)->dashboard())->name('dashboard');
                Route::get('/laporan',     fn () => view('owner.officials.secretary.minutes.index'))->name('laporan');
                Route::get('/dokumentasi', fn () => view('owner.officials.secretary.archives.index'))->name('dokumentasi');
            });

        Route::middleware(['jabatan:bendahara,asisten-bendahara'])
            ->prefix('bendahara')->name('bendahara.')->group(function () {
                Route::get('/iuran',      fn () => view('owner.financial.fee-plans.index'))->name('iuran');
                Route::get('/tagihan',    fn () => view('owner.financial.invoices.index'))->name('tagihan');
                Route::get('/verifikasi', fn () => view('owner.financial.payments.index'))->name('verifikasi');
                Route::get('/rekap',      fn () => view('owner.financial.reports.index'))->name('rekap');
            });

        Route::middleware(['jabatan:pdd,pj-pdd'])
            ->prefix('pdd')->name('pdd.')->group(function () {
                Route::get('/dokumentasi', fn () => view('owner.officials.divisions.pdd', ['tab' => 'dokumentasi']))->name('dokumentasi');
                Route::get('/publikasi',   fn () => view('owner.officials.divisions.pdd', ['tab' => 'publikasi']))->name('publikasi');
                Route::get('/portfolio',   fn () => view('owner.officials.divisions.pdd', ['tab' => 'portfolio']))->name('portfolio');
            });

        Route::middleware(['jabatan:pengembang,pj-pengembang'])
            ->prefix('pengembang')->name('pengembang.')->group(function () {
                Route::get('/tugas',      fn () => view('owner.officials.divisions.pengembang', ['tab' => 'tugas']))->name('tugas');
                Route::get('/misi',       fn () => view('owner.officials.divisions.pengembang', ['tab' => 'misi']))->name('misi');
                Route::get('/monitoring', fn () => view('owner.officials.divisions.pengembang', ['tab' => 'monitoring']))->name('monitoring');
            });
    });


    /* ════════════════════════════════════════════════════════
     | 5.6 ANGGOTA  — FINAL, SEMUA DI DALAM AUTH
     * ════════════════════════════════════════════════════════ */
    /* ════════════════════════════════════════════════════════
     | 5.6 ANGGOTA  — FINAL, SEMUA DI DALAM AUTH
     * ════════════════════════════════════════════════════════ */
Route::middleware(['owner.explore.bypass', 'verified', 'role:anggota', 'no-cache'])
        ->prefix('anggota-ukm')->name('anggota.')
        ->group(function () {

        Route::get('/dashboard',  [AnggotaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profil',     [ProfilController::class, 'index'])->name('profil');
        Route::get('/poin',       [PoinController::class, 'index'])->name('poin');
        Route::get('/presensi',   [PresensiController::class, 'index'])->name('presensi');
        Route::get('/event',      [AnggotaEventController::class, 'index'])->name('event');
        Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat');
        Route::get('/portofolio', [PortofolioController::class, 'index'])->name('portofolio');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
Route::get('/chatting',    fn () => view('anggota-ukm.chatting'))->name('chatting');
Route::get('/pengumuman',  fn () => view('anggota-ukm.pengumuman'))->name('pengumuman');
Route::get('/kelompok',    fn () => view('anggota-ukm.kelompok'))->name('kelompok');
Route::get('/divisi',      fn () => view('anggota-ukm.divisi'))->name('divisi');
Route::get('/jadwal',      fn () => view('anggota-ukm.jadwal'))->name('jadwal');
Route::get('/ruangan',     fn () => view('anggota-ukm.ruangan'))->name('ruangan');
Route::get('/misi',        fn () => view('anggota-ukm.misi'))->name('misi');
Route::get('/leaderpoint', fn () => view('anggota-ukm.leaderpoint'))->name('leaderpoint');
Route::get('/game',        fn () => view('anggota-ukm.game'))->name('game');
Route::get('/sanksi',      fn () => view('anggota-ukm.sanksi'))->name('sanksi');
Route::get('/uangkas', [\App\Http\Controllers\Anggota\UangkasController::class, 'index'])->name('uangkas');
    });
/* ═══════════════════════════════════════════════════════════
 | 5.6b ANGGOTA UMUM
 * ═══════════════════════════════════════════════════════════ */
Route::middleware(['owner.explore.bypass', 'verified', 'no-cache'])
    ->prefix('anggota-umum')->name('anggota-umum.')
    ->group(function () {

        Route::get('/dashboard', function () {
            // Reuse dashboard anggota-ukm atau buat sendiri
            if (view()->exists('anggota-umum.dashboard')) {
                return view('anggota-umum.dashboard');
            }
            return redirect()->route('anggota-umum.uangkas');
        })->name('dashboard');

        Route::get('/uangkas', [\App\Http\Controllers\Anggota\UangkasController::class, 'index'])
            ->name('uangkas');

        // Tambahkan halaman lain bila perlu
        Route::get('/profil', fn () => view('anggota-umum.profil'))->name('profil');
        Route::get('/pengaturan', fn () => view('anggota-umum.pengaturan'))->name('pengaturan');
    });
    /* ════════════════════════════════════════════════════════
     | 5.7 PENGUNJUNG  — Redirect helper (publik sudah cukup)
     * ════════════════════════════════════════════════════════ */
    Route::prefix('pengunjung')->name('pengunjung.')->group(function () {
        Route::get('/beranda', fn () => redirect()->route('landing'))->name('beranda');
        Route::get('/jelajah', fn () => redirect()->route('explore'))->name('jelajah');
        Route::get('/event',   fn () => redirect()->route('explore', ['tab' => 'event']))->name('event');
        Route::get('/ruangan', fn () => redirect()->route('room.public.index'))->name('ruangan');
        Route::get('/game',    fn () => redirect()->route('explore', ['tab' => 'game']))->name('game');
          Route::get('/dashboard', [\App\Http\Controllers\Pengunjung\DashboardController::class, 'index'])->name('dashboard');
    });


    /* ════════════════════════════════════════════════════════
     | 6. PROFILE & LOGOUT
     * ════════════════════════════════════════════════════════ */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',    [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',  [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('/love/{user}',   [ProfileController::class, 'toggleLove'])->whereNumber('user')->name('love');
        Route::post('/follow/{user}', [ProfileController::class, 'toggleFollow'])->whereNumber('user')->name('follow');
    });

    Route::match(['get', 'post'], '/logout', function () {
        $keysToForget = [
            'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
            'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
            'target_user_id', 'role_slug', 'impersonator_id',
            'impersonate_token', 'impersonation_token',
            'explore_token', 'explore_verified', 'explore_verified_at',
            'explore_role', 'impersonate_role',
        ];
        session()->forget($keysToForget);

        try { Auth::guard('web')->logout(); } catch (\Throwable $e) {}
        session()->invalidate();
        session()->regenerateToken();

        $cookiesToForget = array_filter([
            config('session.cookie'),
            'XSRF-TOKEN',
            'impersonation_token', 'impersonate_token',
            'explore_token', 'explore_verified',
            'remember_web_' . sha1(\Illuminate\Foundation\Application::class),
        ]);
        foreach ($cookiesToForget as $name) {
            \Cookie::queue(\Cookie::forget($name));
        }

        return redirect('/login')
            ->with('status', 'Anda telah keluar sepenuhnya.')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ]);
    })->name('logout');
});


/* ═══════════════════════════════════════════════════════════
 | EXPLORE RESET (di luar auth)
 * ═══════════════════════════════════════════════════════════ */
Route::get('/explore/reset', function () {
    foreach ([
        'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
        'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
        'target_user_id', 'role_slug', 'impersonator_id',
        'impersonate_token', 'impersonation_token',
        'explore_token', 'explore_verified', 'explore_verified_at',
    ] as $k) { session()->forget($k); }

    session()->invalidate();
    session()->regenerateToken();

    foreach (array_filter([
        config('session.cookie'), 'XSRF-TOKEN',
        'impersonation_token', 'explore_token', 'explore_verified',
    ]) as $name) {
        \Cookie::queue(\Cookie::forget($name));
    }
    return redirect('/login')->with('status', 'State explorer telah dibersihkan.');
})->name('explore.reset');


/* ═══════════════════════════════════════════════════════════
 | 7. PUBLIC PROFILE (di luar auth)
 * ═══════════════════════════════════════════════════════════ */
Route::get('/profile/{user}', [ProfileController::class, 'showPublic'])
    ->whereNumber('user')->name('profile.public');
    Route::get('/debug-explore', function () {
    return response()->json([
        'auth_id'          => auth()->id(),
        'auth_level'       => auth()->user()?->level_id,
        'explore_active'   => session('owner.explore.active'),
        'explore_owner_id' => session('owner.explore.owner_id'),
        'explore_user_id'  => session('owner.explore.user_id'),
        'explore_role'     => session('owner.explore.role_slug'),
        'explore_token'    => session('owner.explore.token') ? 'exists' : 'MISSING',
        'all_explore_keys' => array_filter(
            session()->all(),
            fn ($k) => str_starts_with($k, 'owner.explore'),
            ARRAY_FILTER_USE_KEY
        ),
    ]);
});
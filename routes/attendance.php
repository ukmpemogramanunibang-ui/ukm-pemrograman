<?php

use App\Http\Controllers\Owner\Attendance\AttendanceController;
use App\Http\Controllers\Owner\Attendance\AttendanceApiController;
use App\Http\Controllers\Owner\Attendance\AttendancePermissionController;

Route::prefix('owner/attendance')
    ->name('owner.attendance.')
    ->middleware(['auth'])
    ->group(function () {
        // Operasional
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [AttendanceController::class, 'create'])->name('create');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
        Route::get('/calendar', [AttendanceController::class, 'calendar'] ?? fn() => view('owner.attendance.calendar'))->name('calendar');
        Route::get('/history', [AttendanceController::class, 'history'] ?? fn() => view('owner.attendance.history'))->name('history');

        // Sesi
        Route::get('/{session}', [AttendanceController::class, 'show'])->name('show');
        Route::put('/{session}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{session}', [AttendanceController::class, 'destroy'] ?? fn() => back())->name('destroy');

        // Aksi sesi
        Route::post('/{session}/open-now', [AttendanceController::class, 'openNow'])->name('open-now');
        Route::post('/{session}/close', [AttendanceController::class, 'close'])->name('close');
        Route::post('/{session}/extend', [AttendanceController::class, 'extend'])->name('extend');
        Route::post('/{session}/cancel', [AttendanceController::class, 'cancel'])->name('cancel');

        // Monitor Live
        Route::get('/{session}/monitor', [AttendanceController::class, 'monitor'])->name('monitor');

        // Manual oleh pengurus
        Route::get('/{session}/manual', [AttendanceController::class, 'manualForm'])->name('manual.form');
        Route::post('/{session}/manual', [AttendanceController::class, 'manualStore'])->name('manual.store');

        // Izin
        Route::get('/permissions/all', [AttendancePermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [AttendancePermissionController::class, 'store'])->name('permissions.store');
        Route::post('/permissions/{permission}/approve', [AttendancePermissionController::class, 'approve'])->name('permissions.approve');
        Route::post('/permissions/{permission}/reject', [AttendancePermissionController::class, 'reject'])->name('permissions.reject');

        // Rekap
        Route::get('/recap', fn() => view('owner.attendance.recap'))->name('recap');
        Route::get('/recap/past', fn() => view('owner.attendance.historical'))->name('recap.past');
        Route::get('/points', fn() => view('owner.attendance.points'))->name('points');
        Route::get('/penalties', fn() => view('owner.attendance.penalties'))->name('penalties');
        Route::get('/audit', fn() => view('owner.attendance.audit'))->name('audit');
    });

// API endpoint (dipanggil via AJAX dari user)
Route::prefix('api/attendance')
    ->name('api.attendance.')
    ->middleware(['auth'])
    ->group(function () {
        Route::post('/{session}/check-in', [AttendanceApiController::class, 'checkIn'])->name('check-in');
        Route::get('/{session}/monitor-data', [AttendanceApiController::class, 'monitorData'])->name('monitor-data');
    });
<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\DashboardItemService;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(DashboardItemService $dashboardItems)
    {
        abort_unless(
            (int) (auth()->user()->level_id ?? 0) === 4,
            403,
            'Hanya Pengurus yang dapat mengakses halaman ini.'
        );

        $user = auth()->user();

        // Dashboard folder + shortcut
        $dashboardData = $dashboardItems->resolveFor($user);

        // Statistik
        $stats = [
            'total_tugas'    => 0,
            'total_misi'     => 0,
            'total_event'    => 0,
            'total_anggota'  => User::where('level_id', 5)->whereNull('deleted_at')->count(),
        ];

        // Aktivitas
        $activities = collect();
        if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
            $activities = AuditLog::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($log) => (object) [
                    'user'   => $log->user->name ?? 'Anda',
                    'action' => $log->description ?? $log->action,
                    'time'   => $log->created_at->diffForHumans(),
                    'color'  => 'indigo',
                ]);
        }

        return view('pengurus.dashboard', compact(
            'user',
            'stats',
            'activities',
            'dashboardData'
        ));
    }
}
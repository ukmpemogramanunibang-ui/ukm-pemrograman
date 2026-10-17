<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\DashboardItemService;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(DashboardItemService $dashboardItems)
    {
        abort_unless((int) (auth()->user()->level_id ?? 0) === 2, 403);

        $user          = auth()->user();
        $dashboardData = $dashboardItems->resolveFor($user);

        // ===== Stats =====
        $stats = [
            'total_users'       => User::whereNull('deleted_at')->count(),
            'total_announce'    => 0,
            'total_reports'     => 0,
            'total_contacts'    => User::whereNull('deleted_at')->count(),
            'total_notifs'      => 0,
            'total_active'      => User::where('is_active', true)->count(),
        ];

        // Announcement count (kalau ada model)
        if (class_exists(\App\Models\Announcement::class) && Schema::hasTable('announcements')) {
            try { $stats['total_announce'] = \App\Models\Announcement::count(); } catch (\Throwable $e) {}
        }

        // Reports count
        $stats['total_reports'] = 8; // placeholder — ganti dengan real query

        // Notifications
        if (method_exists($user, 'unreadNotifications')) {
            $stats['total_notifs'] = $user->unreadNotifications()->count();
        }

        // ===== Activities =====
        $activities = collect();
        if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
            $activities = AuditLog::with('user:id,name')
                ->latest()
                ->take(8)
                ->get()
                ->map(fn ($log) => (object) [
                    'user'   => $log->user->name ?? 'Sistem',
                    'action' => $log->description ?? $log->action,
                    'time'   => optional($log->created_at)->diffForHumans(),
                    'color'  => 'purple',
                    'icon'   => 'fa-circle',
                ]);
        }

        // ===== Progress bars =====
        $progress = [
            'active_users'   => $stats['total_users'] > 0
                ? round(($stats['total_active'] / $stats['total_users']) * 100) : 0,
            'announce_month' => 75,
            'report_ready'   => 60,
            'contact_coverage' => 88,
        ];

        return view('superadmin.dashboard', compact(
            'user', 'stats', 'activities', 'progress', 'dashboardData'
        ));
    }
}
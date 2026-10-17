<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginHistory;
use App\Models\Event;
use App\Models\PointHistory;
use App\Models\Kas;
use App\Services\DashboardItemService;
use App\Traits\HasDashboardItems;
use Illuminate\Support\Facades\Schema;

class KetuaDashboardController extends Controller
{
    use HasDashboardItems;

    public function index(DashboardItemService $dashboardItems)
    {
        // === Statistik ===
        $totalAnggota = User::where('level_id', 5)->whereNull('deleted_at')->count();
        $anggotaBaruBulanIni = User::whereMonth('created_at', now()->month)->count();

        // === Event Aktif ===
        $eventAktif = 0;
        if (class_exists(Event::class) && Schema::hasTable('events')) {
            try {
                $eventAktif = Schema::hasColumn('events', 'status')
                    ? Event::where('status', 'aktif')->count()
                    : Event::count();
            } catch (\Exception $e) {}
        }

        // === Total Poin ===
        $totalPoin = 0;
        if (class_exists(PointHistory::class) && Schema::hasTable('point_histories')) {
            try {
                if (Schema::hasColumn('point_histories', 'points')) {
                    $totalPoin = (int) PointHistory::where('status', 'approved')->sum('points');
                } elseif (Schema::hasColumn('point_histories', 'poin')) {
                    $totalPoin = (int) PointHistory::sum('poin');
                }
            } catch (\Exception $e) {}
        }

        // === Total Kas ===
        $totalKas = 0;
        if (class_exists(Kas::class) && Schema::hasTable('kas')) {
            try {
                if (Schema::hasColumn('kas', 'jumlah')) {
                    $totalKas = Kas::sum('jumlah');
                }
            } catch (\Exception $e) {}
        }

        // === Aktivitas Login ===
        $aktivitasLogin = collect();
        if (class_exists(LoginHistory::class) && Schema::hasTable('login_histories')) {
            try {
                $aktivitasLogin = LoginHistory::with('user')->latest()->limit(10)->get();
            } catch (\Exception $e) {}
        }

        $anggotaTerbaru = User::latest()->limit(5)->get();

        // 🔥 Dashboard Folder + Shortcut
        $dashboardData = $this->getDashboardData($dashboardItems);

        // 🔥 Format stats & activities untuk blade
        $stats = [
            'total_anggota'  => $totalAnggota,
            'total_pengurus' => User::where('level_id', 4)->whereNull('deleted_at')->count(),
            'total_event'    => $eventAktif,
            'total_poin'     => $totalPoin,
        ];

        $activities = $aktivitasLogin->map(fn ($log) => (object) [
            'user'   => $log->user->name ?? 'Sistem',
            'action' => 'login ke sistem',
            'time'   => $log->created_at->diffForHumans(),
            'color'  => 'purple',
        ]);

        return view('dashboard.ketua', compact(
            'stats',
            'activities',
            'dashboardData',
            'totalAnggota',
            'anggotaBaruBulanIni',
            'eventAktif',
            'totalPoin',
            'totalKas',
            'aktivitasLogin',
            'anggotaTerbaru'
        ));
    }
}
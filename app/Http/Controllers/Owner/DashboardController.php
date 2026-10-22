<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->level_id !== 1) {
            return redirect(auth()->user()->getDashboardRoute());
        }

        return view('dashboard.owner', [
            'stats'      => $this->getStats(),
            'activities' => $this->getActivities(),
        ]);
    }

    /**
     * Ambil statistik user dengan 1 query (lebih efisien).
     */
    private function getStats(): array
    {
        // ✅ 1 query agregat, bukan 9 query terpisah
        $raw = User::query()
            ->selectRaw("
                COUNT(*) as total_users,
                SUM(CASE WHEN level_id = 1 THEN 1 ELSE 0 END) as total_owner,
                SUM(CASE WHEN level_id = 2 THEN 1 ELSE 0 END) as total_superadmin,
                SUM(CASE WHEN level_id = 4 THEN 1 ELSE 0 END) as total_pengurus,
                SUM(CASE WHEN level_id = 5 AND name != 'Anggota Umum' THEN 1 ELSE 0 END) as total_anggota,
                SUM(CASE WHEN level_id = 5 AND name = 'Anggota Umum' THEN 1 ELSE 0 END) as total_anggota_umum,
                SUM(CASE WHEN level_id = 6 OR account_type = 'guest' THEN 1 ELSE 0 END) as total_pengunjung,
                SUM(CASE WHEN is_active = 0 OR locked_until IS NOT NULL THEN 1 ELSE 0 END) as total_blocked
            ")
            ->first();

        return [
            'total_users'        => (int) $raw->total_users,
            'total_pengurus'     => (int) $raw->total_pengurus,
            'total_anggota'      => (int) $raw->total_anggota,
            'total_pengunjung'   => (int) $raw->total_pengunjung,
            'total_anggota_umum' => (int) $raw->total_anggota_umum,
            'total_blocked'      => (int) $raw->total_blocked,
            'total_owner'        => (int) $raw->total_owner,
            'total_superadmin'   => (int) $raw->total_superadmin,
        ];
    }

    /**
     * Gabungkan aktivitas user baru + log login.
     */
    private function getActivities(): Collection
    {
        // ✅ eager load relasi level (kolom minimal)
        $newUsers = User::with('level:id,name')
            ->select(['id', 'name', 'level_id', 'created_at'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn (User $u) => (object) [
                'user'   => $u->name,
                'action' => 'bergabung sebagai ' . ($u->level->name ?? 'user'),
                'time'   => $u->created_at->diffForHumans(),
                'color'  => 'green',
            ]);

        $loginLogs = collect();
        if (class_exists(AuditLog::class)) {
            $loginLogs = AuditLog::with('user:id,name')   // ✅ eager load + kolom minimal
                ->where(fn ($q) => $q->where('action', 'login')
                                     ->orWhere('description', 'like', '%login%'))
                ->latest()
                ->take(5)
                ->get()
                ->filter(fn ($log) => $log->user)          // buang log tanpa user
                ->map(fn ($log) => (object) [
                    'user'   => $log->user->name,
                    'action' => 'melakukan login',
                    'time'   => $log->created_at->diffForHumans(),
                    'color'  => 'blue',
                ]);
        }

        return $newUsers->concat($loginLogs)->take(5)->values();
    }
}
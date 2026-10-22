<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // --- STATISTIK REAL DARI DATABASE ---

        // Total Anggota (level_id = 5)
        $totalAnggota = User::where('level_id', 5)->count();

        // Total Pengurus (level_id = 4)
        $totalPengurus = User::where('level_id', 4)->count();

        // Total Pengunjung / Guest (level_id = 6)
        $totalPengunjung = User::where('level_id', 6)->count();

        // Total Anggota Umum (level_id = 5 DAN is_active = true)
        // atau bisa juga user dengan level_id = 5 dan status_id = aktif
        $totalAnggotaUmum = User::where('level_id', 5)
            ->where('is_active', true)
            ->count();

        // Total Akun yang Diblokir (is_active = false)
        $totalDiblokir = User::where('is_active', false)->count();

        // Total User Keseluruhan
        $totalUser = User::count();

        // Total Owner (level_id = 1)
        $totalOwner = User::where('level_id', 1)->count();

        // Total Superadmin (level_id = 2)
        $totalSuperadmin = User::where('level_id', 2)->count();

        // Total Ketua (level_id = 3)
        $totalKetua = User::where('level_id', 3)->count();

        // Aktivitas terbaru (contoh: 5 user terakhir yang login)
        $aktivitasTerbaru = User::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->limit(5)
            ->get(['name', 'last_login_at', 'level_id']);

        // Data tambahan untuk grafik/analisis (opsional)
        $levelStats = User::select('level_id', DB::raw('count(*) as total'))
            ->groupBy('level_id')
            ->with('level')
            ->get();

        return view('dashboard.owner', compact(
            'totalAnggota',
            'totalPengurus',
            'totalPengunjung',
            'totalAnggotaUmum',
            'totalDiblokir',
            'totalUser',
            'totalOwner',
            'totalSuperadmin',
            'totalKetua',
            'aktivitasTerbaru',
            'levelStats'
        ));
    }
}
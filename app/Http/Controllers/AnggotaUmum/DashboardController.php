<?php

namespace App\Http\Controllers\AnggotaUmum;

use App\Http\Controllers\Controller;
use App\Services\DashboardItemService;

class DashboardController extends Controller
{
    public function index(DashboardItemService $dashboardItems)
    {
        $user = auth()->user();
        $dashboardData = $dashboardItems->resolveFor($user);

        // Stats (placeholder — sesuaikan dengan data real)
        $totalPoin       = 0;
        $totalEvent      = 0;
        $totalPresensi   = 0;
        $totalSertifikat = 0;

        return view('anggota-umum.dashboard', compact(
            'totalPoin', 'totalEvent', 'totalPresensi', 'totalSertifikat',
            'dashboardData'
        ));
    }
}
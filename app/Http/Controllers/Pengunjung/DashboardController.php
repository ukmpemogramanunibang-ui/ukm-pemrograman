<?php

namespace App\Http\Controllers\Pengunjung;

use App\Http\Controllers\Controller;
use App\Services\DashboardItemService;

class DashboardController extends Controller
{
    public function index(DashboardItemService $dashboardItems)
    {
        $user = auth()->user();
        $dashboardData = $dashboardItems->resolveFor($user);

        return view('pengunjung.dashboard', compact('dashboardData'));
    }
}
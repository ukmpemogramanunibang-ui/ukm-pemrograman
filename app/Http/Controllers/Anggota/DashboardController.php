<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PointHistory;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Presensi;        // <-- Tambahkan ini
use App\Models\Sertifikat;      // <-- Tambahkan ini
use App\Services\DashboardItemService;

class DashboardController extends Controller
{
   public function index()
{
    $user = Auth::user();
 // Nonaktifkan cache agar tidak bisa di-back setelah logout
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    $totalPoin = PointHistory::where('user_id', $user->id)->sum('points') ?: 0;

    $totalEvent = Event::whereHas('peserta', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })->count() ?: 0;

    // Gunakan try-catch untuk menghindari error jika tabel/model belum ada
    try {
        $totalPresensi = Presensi::where('user_id', $user->id)->count() ?: 0;
    } catch (\Exception $e) {
        $totalPresensi = 0;
    }

    try {
        $totalSertifikat = Sertifikat::where('user_id', $user->id)->count() ?: 0;
    } catch (\Exception $e) {
        $totalSertifikat = 0;
    }

    $allPoin = PointHistory::select('user_id', DB::raw('SUM(points) as total'))
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->pluck('user_id');
    $rank = $allPoin->search($user->id) + 1;

    $activities = PointHistory::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

    return view('anggota-ukm.dashboard', compact(
        'totalPoin',
        'totalEvent',
        'totalPresensi',
        'totalSertifikat',
        'rank',
        'activities'
    ));
}
}
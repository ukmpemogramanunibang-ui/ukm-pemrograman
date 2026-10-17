<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
   public function index()
{
    $user = auth()->user();
    if ($user->level == 0 || $user->level == 1) {
        return view('home.superadmin');
    } elseif ($user->level == 2) {
        return view('home.ketua');
    } elseif (in_array($user->level, [3,4,5])) {
        return view('home.anggota');
    } elseif ($user->level == 6) {
        return view('home.umum');
    } else {
        return view('home.pengunjung');
    }
    $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Ambil nilai level dari relasi level
        $levelValue = $user->level->value ?? 99;

        // Arahkan ke view home sesuai level
        switch ($levelValue) {
            case 0: // Owner
                return view('home.owner', compact('user'));
            case 1: // Superadmin
                return view('home.superadmin', compact('user'));
            case 2: // Ketua
                return view('home.ketua', compact('user'));
            case 3: // Pengurus Inti
            case 4: // PJ/Assisten
                return view('home.pengurus', compact('user'));
            case 5: // Anggota Kelompok
                return view('home.anggota', compact('user'));
            case 6: // Anggota Umum
                return view('home.anggota_umum', compact('user'));
            case 7: // Pengunjung
                return view('home.pengunjung', compact('user'));
            default:
                abort(403, 'Akses tidak diizinkan');
        }
}

}

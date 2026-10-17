<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginHistory;
use App\Models\Event;
use App\Models\PointHistory;
use App\Models\Kas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class KetuaDashboardController extends Controller
{
    public function index()
    {
        // === Statistik yang pasti aman ===
        $totalAnggota = User::count();
        $anggotaBaruBulanIni = User::whereMonth('created_at', now()->month)->count();

        // === Event Aktif (dengan pengecekan kolom) ===
        $eventAktif = 0;
        if (class_exists(Event::class) && Schema::hasTable('events')) {
            try {
                // Cek apakah kolom 'status' ada di tabel events
                if (Schema::hasColumn('events', 'status')) {
                    $eventAktif = Event::where('status', 'aktif')->count();
                } else {
                    // Jika kolom status tidak ada, hitung semua event atau beri 0
                    $eventAktif = Event::count(); // atau 0
                }
            } catch (\Exception $e) {
                $eventAktif = 0;
            }
        }

        // === Total Poin (jika tabel & kolom 'poin' ada) ===
        $totalPoin = 0;
        if (class_exists(PointHistory::class) && Schema::hasTable('point_histories')) {
            try {
                if (Schema::hasColumn('point_histories', 'poin')) {
                    $totalPoin = PointHistory::sum('poin');
                }
            } catch (\Exception $e) {
                $totalPoin = 0;
            }
        }

        // === Total Kas (jika tabel & kolom 'jumlah' ada) ===
        $totalKas = 0;
        if (class_exists(Kas::class) && Schema::hasTable('kas')) {
            try {
                if (Schema::hasColumn('kas', 'jumlah')) {
                    $totalKas = Kas::sum('jumlah');
                }
            } catch (\Exception $e) {
                $totalKas = 0;
            }
        }

        $persentaseKas = 12; // contoh statis

        // === Aktivitas Login (jika model & tabel ada) ===
        $aktivitasLogin = collect();
        if (class_exists(LoginHistory::class) && Schema::hasTable('login_histories')) {
            try {
                $aktivitasLogin = LoginHistory::with('user')
                                    ->latest()
                                    ->limit(10)
                                    ->get();
            } catch (\Exception $e) {
                $aktivitasLogin = collect();
            }
        }

        // === 5 Anggota Terbaru (pasti aman) ===
        $anggotaTerbaru = User::latest()->limit(5)->get();

        return view('dashboard.ketua', compact(
            'totalAnggota',
            'anggotaBaruBulanIni',
            'eventAktif',
            'totalPoin',
            'totalKas',
            'persentaseKas',
            'aktivitasLogin',
            'anggotaTerbaru'
        ));
    }
}
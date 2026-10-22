<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        try {
            $presensis = Presensi::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        } catch (\Exception $e) {
            $presensis = collect();
        }
        return view('anggota.presensi', compact('presensis'));
    }
}
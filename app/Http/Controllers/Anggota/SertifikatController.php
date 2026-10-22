<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Auth;

class SertifikatController extends Controller
{
    public function index()
    {
        try {
            $sertifikats = Sertifikat::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        } catch (\Exception $e) {
            $sertifikats = collect();
        }
        return view('anggota.sertifikat', compact('sertifikats'));
    }
}
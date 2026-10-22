<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use Illuminate\Support\Facades\Auth;

class PortofolioController extends Controller
{
    public function index()
    {
        try {
            $portofolios = Portofolio::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(6);
        } catch (\Exception $e) {
            $portofolios = collect();
        }
        return view('anggota.portofolio', compact('portofolios'));
    }
}
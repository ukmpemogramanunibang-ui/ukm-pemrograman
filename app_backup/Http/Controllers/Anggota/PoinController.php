<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PointHistory;
use Illuminate\Support\Facades\Auth;

class PoinController extends Controller
{
    public function index()
    {
        $totalPoin = PointHistory::where('user_id', Auth::id())->sum('points') ?: 0;
        $histories = PointHistory::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        return view('anggota.poin', compact('totalPoin', 'histories'));
    }
}
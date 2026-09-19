<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\PointAccount;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index()
    {
        $points = PointAccount::with('user')->orderBy('balance', 'desc')->paginate(15);
        return view('owner.financial.points.index', compact('points'));
    }

    public function leaderboard()
    {
        $leaderboard = PointAccount::with('user')
            ->where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->limit(100)
            ->get();
        return view('owner.financial.points.leaderboard', compact('leaderboard'));
    }
}
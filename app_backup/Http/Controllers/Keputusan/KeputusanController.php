<?php

namespace App\Http\Controllers\Keputusan;

use App\Http\Controllers\Controller;
use App\Models\DecisionPoll;
use App\Models\DecisionWheel;
use Illuminate\Http\Request;

class KeputusanController extends Controller
{
    /** Landing publik — pilih metode */
    public function index(Request $request)
    {
        if (auth()->check()) {
            return $this->dashboard();
        }

        return view('keputusan.landing');
    }

    /** Workspace pengguna login */
    public function dashboard()
    {
        $userId = auth()->id();

        $wheels = DecisionWheel::where('user_id', $userId)
            ->withCount('entries', 'spins')
            ->latest()->take(5)->get();

        $polls = DecisionPoll::where('user_id', $userId)
            ->withCount('options')
            ->latest()->take(5)->get();

        $stats = [
            'wheels'   => DecisionWheel::where('user_id', $userId)->count(),
            'polls'    => DecisionPoll::where('user_id', $userId)->count(),
            'decisions'=> DecisionWheel::where('user_id', $userId)->sum('id') + DecisionPoll::where('user_id', $userId)->sum('id'),
        ];

        return view('keputusan.dashboard', compact('wheels', 'polls', 'stats'));
    }
}
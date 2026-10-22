<?php

namespace App\Http\Controllers;

use App\Models\User;

class AnggotaController extends Controller
{
public function index()
{
    $members = User::query()
        ->whereIn('level_id', [2, 3, 4, 5, 6])
        ->where('is_active', true)
        ->whereNull('deleted_at')
        ->with(['level', 'major', 'university'])
        ->withSum(['pointHistories as approved_points' => function ($q) {
            $q->where('status', 'approved');
        }], 'points')
        ->orderByRaw('FIELD(level_id, 2, 3, 4, 5, 6)')
        ->orderBy('name')
        ->get();

    // HAPUS BARIS INI ↓
    // dd($members->count(), $members->take(3)->pluck('name'));

    return view('member', compact('members'));
}
}
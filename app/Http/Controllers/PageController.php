<?php

namespace App\Http\Controllers;

use App\Models\User;

class PageController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    // ← tambahkan di sini
    public function anggota()
    {
        $members = User::query()
            ->whereIn('level_id', [2, 3, 4, 5, 6])
            ->where('is_active', true)
            ->orderByRaw('FIELD(level_id, 2, 3, 4, 5, 6)')
            ->orderBy('name')
            ->get();

        return view('anggota.index', compact('members'));
    }
}
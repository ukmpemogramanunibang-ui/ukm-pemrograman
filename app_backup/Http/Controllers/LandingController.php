<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('owner')) return redirect()->route('owner.dashboard');
            if ($user->hasRole('superadmin')) return redirect()->route('superadmin.dashboard');
            if ($user->hasRole('ketua')) return redirect()->route('ketua.dashboard');
            if ($user->hasRole('pengurus')) return redirect()->route('pengurus.dashboard');
            if ($user->hasRole('anggota')) return redirect()->route('anggota.dashboard');
            return redirect()->route('dashboard');
        }
        return view('landing');
    }
}
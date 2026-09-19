<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showManualLoginForm()
    {
        return view('login-manual'); // ganti dengan nama view Anda
    }

    // nanti tambahkan method untuk proses login manual
}
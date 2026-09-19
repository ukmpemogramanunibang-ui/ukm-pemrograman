<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'login/check-nim-json',   // AJAX cek NIM
        'login/manual',           // AJAX login
        'logout',                 // jika nanti logout via AJAX
    ];
}
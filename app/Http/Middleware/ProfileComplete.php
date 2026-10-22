<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: sesuaikan logika kelengkapan profil
        return $next($request);
    }
}
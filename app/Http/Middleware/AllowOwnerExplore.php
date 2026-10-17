<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOwnerExplore
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId  = (int) (auth()->id() ?? 0);
        $isOwner = auth()->check() && (int) auth()->user()->level_id === 1;

        if ($isOwner
            && (bool) session('owner.explore.active', false)
            && $userId === (int) session('owner.explore.owner_id', 0)
            && session('owner.explore.role_slug')
            && session('owner.explore.token')) {
            $request->attributes->set('is_owner_exploring', true);
        }

        return $next($request);
    }
}
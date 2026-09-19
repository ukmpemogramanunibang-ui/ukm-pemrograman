<?php
namespace App\Http\Middleware;
use Closure;
class CacheHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (config('app.env') === 'production') {
            $response->headers->set('Cache-Control', 'public, max-age=3600');
        }
        return $response;
    }
}
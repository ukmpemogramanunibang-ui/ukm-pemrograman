<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // Coba dari session (kalau ada)
        if ($request->hasSession()) {
            $locale = $request->session()->get('locale');
        }

        // Fallback: dari user, header, atau config
        $locale ??= $request->user()?->locale
                 ?? $request->header('Accept-Language')
                 ?? config('app.locale');

        // Normalisasi (mis. "id-ID,id;q=0.9" → "id")
        $locale = substr($locale, 0, 2);

        App::setLocale($locale);

        return $next($request);
    }
}
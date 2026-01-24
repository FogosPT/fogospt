<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(Cookie::get('userLocale', config('app.locale')));

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LocaleMatch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->route('locale');
        $availableLocales = config('custom.availableLocales');

        if (!in_array($locale, $availableLocales)) {
            return redirect('/' . config('app.locale') . $request->getPathInfo());
        }

        App::setLocale($locale);
        
        Cookie::queue('userLocale', $locale, config('session.lifetime'));

        return $next($request);
    }
}

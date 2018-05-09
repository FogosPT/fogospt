<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class SetLanguage
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
		if (\Session::has('userLocale') && in_array(session('userLocale'), config('custom.availableLocales'))) {
			\App::setLocale(session('userLocale'));
		}
		else {
			\Session::put('userLocale', config('app.locale'));
			\App::setLocale(config('app.locale'));
		}
		return $next($request);
	}
}

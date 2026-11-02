<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	/**
	 * The application's global HTTP middleware stack.
	 *
	 * These middleware are run during every request to your application.
	 *
	 * @var array
	 */
	protected $middleware = [
		\App\Http\Middleware\TrustProxies::class,
		\App\Http\Middleware\SecurityHeaders::class,
		\Illuminate\Http\Middleware\HandleCors::class,
		\App\Http\Middleware\PreventRequestsDuringMaintenance::class,
		\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
		\App\Http\Middleware\TrimStrings::class,
		\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
	];

	/**
	 * The application's route middleware groups.
	 *
	 * @var array
	 */
	protected $middlewareGroups = [
		// Deliberately stateless: no session, cookies or CSRF, so every page
		// can be cached publicly by the CDN (a Set-Cookie would bypass it).
		// Locale comes from the URL prefix (LocaleMatch).
		'web' => [],
	];

	/**
	 * The application's route middleware aliases.
	 *
	 * @var array
	 */
	protected $middlewareAliases = [
		'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
		'signed'        => \Illuminate\Routing\Middleware\ValidateSignature::class,
		'throttle'      => \Illuminate\Routing\Middleware\ThrottleRequests::class,
		'locale.match'  => \App\Http\Middleware\LocaleMatch::class,
	];
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Baseline security headers for every response. Kept in the app (not only in
 * nginx) so they ship regardless of which proxy config is live.
 */
class SecurityHeaders
{
    // Pages meant to be embedded by third parties (the configurable map).
    private const EMBEDDABLE_ROUTES = ['mapCustom'];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff', false);
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);

        if (!$request->routeIs(...self::EMBEDDABLE_ROUTES)) {
            $headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        }

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000', false);
        }

        return $response;
    }
}

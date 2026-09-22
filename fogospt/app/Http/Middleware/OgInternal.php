<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Gate the /og/fogo/{id}/render Blade against public access. The page is a
// slim card designed for the headless renderer only — no site chrome, no
// nav — so it must never be indexed or hit by a real user. Only requests
// with a valid HMAC-SHA256(id, APP_KEY) in X-Og-Token pass. IP allow-lists
// would be spoofable through X-Forwarded-For because TrustProxies is '*'.
class OgInternal
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = (string) $request->route('id');

        $expected = hash_hmac('sha256', $id, (string) config('app.key'));
        $provided = (string) $request->header('X-Og-Token', '');

        if ($provided !== '' && hash_equals($expected, $provided)) {
            return $next($request);
        }

        abort(403);
    }
}

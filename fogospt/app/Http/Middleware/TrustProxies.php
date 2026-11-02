<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * '*' lets Laravel honour X-Forwarded-* from any proxy in front of the
     * container (nginx → CDN/Cloudflare). Without this the framework saw
     * the request as plain HTTP and produced http:// redirects on https://
     * pages, triggering Mixed Content blocks in the browser.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    // X-Forwarded-Host is deliberately not trusted: with $proxies = '*' any
    // client could set it and poison canonical/og:url/share links in pages
    // that the CDN caches publicly.
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}

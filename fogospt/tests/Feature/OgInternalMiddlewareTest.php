<?php

namespace Tests\Feature;

use Tests\TestCase;

// The slim /og/fogo/{id}/render Blade is the raw canvas the sidecar
// screenshots — no site chrome, no navigation. Any way for a public
// crawler to reach it would create a broken-looking result in the wild
// and, worse, make the page indexable. Middleware must reject requests
// that neither present a valid HMAC nor originate from the compose
// network.
class OgInternalMiddlewareTest extends TestCase
{
    /** @test */
    public function render_endpoint_rejects_public_requests_without_token(): void
    {
        // Simulate a public IP so the loopback / host-gateway allow-list
        // doesn't kick in. TrustProxies is TrustedIps=[] by default in tests.
        $this->get('/og/fogo/12345/render', [
            'X-Forwarded-For' => '203.0.113.10',
        ])->assertStatus(403);
    }

    /** @test */
    public function render_endpoint_rejects_requests_with_wrong_token(): void
    {
        $this->get('/og/fogo/12345/render', [
            'X-Og-Token'      => str_repeat('0', 64),
            'X-Forwarded-For' => '203.0.113.10',
        ])->assertStatus(403);
    }

    /** @test */
    public function render_endpoint_accepts_requests_with_valid_hmac_token(): void
    {
        $id = '12345';
        $token = hash_hmac('sha256', $id, (string) config('app.key'));

        // Upstream API call will fail in tests (no FPTS, no network), so the
        // controller aborts 404 for the unknown fire — but crucially it is
        // not 403, which is what we're proving here: the middleware let us
        // through when the token was correct.
        $response = $this->get("/og/fogo/{$id}/render", [
            'X-Og-Token'      => $token,
            'X-Forwarded-For' => '203.0.113.10',
        ]);

        $this->assertNotEquals(403, $response->status(), 'valid HMAC token must not 403');
    }
}

<?php

namespace Tests\Feature;

use App\Libs\LegacyApi;
use Tests\TestCase;

/**
 * Regression tests for the platform audit: upstream failures degrade
 * gracefully, inputs reaching third-party URLs are validated, and every
 * response carries the baseline security headers.
 */
class HardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Nothing listens on port 9 — every upstream call fails fast with a
        // ConnectException, which Guzzle 7 does not wrap in RequestException.
        config(['services.fogos.url' => 'http://127.0.0.1:9']);
    }

    /** @test */
    public function upstream_transport_errors_return_an_error_array_instead_of_throwing(): void
    {
        $this->assertSame(['error' => 'upstream_unavailable'], LegacyApi::getFire('123'));
        $this->assertSame(['error' => 'upstream_unavailable'], LegacyApi::getFires());
    }

    /**
     * @test
     *
     * @dataProvider cardsProvider
     */
    public function fire_cards_render_when_upstream_is_down(string $card): void
    {
        $this->get("/pt/views/{$card}/123")->assertOk();
    }

    public static function cardsProvider(): array
    {
        return [['risk'], ['status'], ['meteo'], ['extra'], ['shares']];
    }

    /**
     * @test
     *
     * @dataProvider pagesProvider
     */
    public function pages_render_when_upstream_is_down(string $path, int $status): void
    {
        $this->get($path)->assertStatus($status);
    }

    public static function pagesProvider(): array
    {
        return [
            ['/pt', 200], ['/pt/madeira', 200], ['/pt/outros', 200], ['/pt/mapa', 200],
            ['/pt/mapa/configurar', 200], ['/pt/tabela', 200], ['/pt/lista', 200],
            ['/pt/avisos', 200], ['/pt/madeira/avisos', 200], ['/pt/estatisticas', 200],
            ['/pt/informacoes', 200], ['/pt/parceiros', 200], ['/pt/notificacoes', 200],
            ['/pt/sobre', 200], ['/pt/api', 200], ['/pt/api-termos', 200], ['/pt/privacy-policy', 200],
            ['/en/sobre', 200], ['/es/sobre', 200],
            ['/pt/fogo/123', 200], ['/pt/fogo/123/detalhe', 404], ['/pt/madeira/fogo/123', 200],
        ];
    }

    /** @test */
    public function feeds_render_when_upstream_is_down(): void
    {
        $this->get('/rss/fires.xml')->assertOk();
        $this->get('/sitemap-fires.xml')->assertOk();
    }

    /** @test */
    public function fire_ids_with_query_metacharacters_are_rejected(): void
    {
        $this->get('/pt/fogo/1%26foo%3Dbar')->assertNotFound();
        $this->get('/pt/views/risk/1%26x%3D1')->assertNotFound();
    }

    /** @test */
    public function malformed_fcm_tokens_are_rejected_before_any_upstream_call(): void
    {
        $this->post('/notifications/subscribe', [
            'token' => 'REALTOKEN0123456789/rel/topics/anything?',
            'topic' => 'warnings',
        ])->assertStatus(400);

        $this->post('/notifications/subscribe', [
            'token' => ['array'],
            'topic' => 'warnings',
        ])->assertStatus(400);
    }

    /** @test */
    public function wms_proxy_rejects_oversized_tiles_and_unknown_layers(): void
    {
        $this->get('/v1/ipma-wms?REQUEST=GetMap&LAYERS=arome.2m.temperature.continent&WIDTH=4096&HEIGHT=256')
            ->assertStatus(400);
        $this->get('/v1/ipma-wms?REQUEST=GetMap&LAYERS=evil&WIDTH=256&HEIGHT=256')
            ->assertStatus(400);
    }

    /** @test */
    public function responses_carry_security_headers(): void
    {
        $this->get('/pt/sobre')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    /** @test */
    public function the_custom_map_stays_embeddable(): void
    {
        $this->get('/pt/mapa')->assertHeaderMissing('X-Frame-Options');
    }

    /** @test */
    public function share_links_do_not_reflect_the_raw_request_uri(): void
    {
        $html = $this->get('/pt/views/shares/123?"><script>alert(1)</script>')->getContent();

        $this->assertStringNotContainsString('<script>alert(1)', $html);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

// The OG card Blade compiles into raw HTML that the sidecar screenshots.
// A malformed template would silently produce a blank card in every share
// preview, so we render it with a synthetic fire payload and assert the
// pieces that matter show up.
class OgFireBladeTest extends TestCase
{
    /** @test */
    public function it_renders_the_status_location_meios_and_map_container(): void
    {
        $html = view('og.fire', [
            'fire' => [
                'status'   => 'Em Curso',
                'location' => 'Serra do Caramulo',
                'concelho' => 'Tondela',
                'distrito' => 'Viseu',
                'man'      => 42,
                'terrain'  => 12,
                'aerial'   => 3,
                'lat'      => 40.5,
                'lng'      => -8.2,
            ],
            'kml'     => null,
            'kmlVost' => null,
        ])->render();

        $this->assertStringContainsString('Em Curso', $html);
        $this->assertStringContainsString('Serra do Caramulo', $html);
        $this->assertStringContainsString('Tondela', $html);
        $this->assertStringContainsString('Viseu', $html);
        $this->assertStringContainsString('>42<', $html);
        $this->assertStringContainsString('>12<', $html);
        $this->assertStringContainsString('window.__ogReady', $html);
        // 1200x630 is the size the FB/X/WhatsApp crawlers expect for a
        // large image card. The viewport meta drives Chrome's viewport in
        // the sidecar too.
        $this->assertStringContainsString('width=1200', $html);
    }

    /** @test */
    public function it_surfaces_unconfirmed_meios_when_upstream_sends_minus_one(): void
    {
        // The upstream API uses -1 to mean "not yet confirmed" on very
        // new incidents. Rendering "-1 Operacionais" would be a lie.
        $html = view('og.fire', [
            'fire' => [
                'status'   => 'Despacho',
                'location' => 'Braga',
                'concelho' => 'Braga',
                'man'      => -1,
                'terrain'  => -1,
                'aerial'   => -1,
                'lat'      => 41.5,
                'lng'      => -8.4,
            ],
            'kml'     => null,
            'kmlVost' => null,
        ])->render();

        $this->assertStringContainsString('por confirmar', $html);
        $this->assertStringNotContainsString('>-1<', $html);
    }

    /** @test */
    public function it_survives_a_barebones_fire_payload_from_a_freshly_created_incident(): void
    {
        // Upstream can push a new fire before location/status are populated.
        // The Blade must render *something* rather than exploding — a blank
        // preview is worse than a placeholder card in every share.
        $html = view('og.fire', [
            'fire'    => ['id' => '999'],
            'kml'     => null,
            'kmlVost' => null,
        ])->render();

        $this->assertStringContainsString('Fogos.pt', $html);
        // Even on a bare payload, the ready flag script must ship — the
        // sidecar hangs on waitForFunction otherwise.
        $this->assertStringContainsString('window.__ogReady', $html);
    }
}

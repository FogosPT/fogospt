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
    public function it_renders_status_location_meios_bars_and_timeline(): void
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
                'statusHistory' => [
                    ['statusCode' => 3,  'label' => '14:03', 'status' => 'Despacho'],
                    ['statusCode' => 4,  'label' => '14:12', 'status' => 'Em Curso'],
                    ['statusCode' => 7,  'label' => '15:30', 'status' => 'Vigilância'],
                    ['statusCode' => 4,  'label' => '16:23', 'status' => 'Em Curso'],
                ],
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
        // The bar-fill widths are the visual meios chart; without them
        // the section is just numbers. Assert the man bar computed a
        // width so a future refactor cannot silently drop the chart.
        $this->assertMatchesRegularExpression('/bar-fill.*width:\s*100\.00%/', $html);
        // Timeline must show every event we passed in (order is
        // oldest-first so "Despacho" leads).
        $this->assertStringContainsString('Despacho', $html);
        $this->assertStringContainsString('Vigilância', $html);
        $this->assertStringContainsString('14:03', $html);
        $this->assertStringContainsString('16:23', $html);
        $this->assertStringContainsString('window.__ogReady', $html);
        $this->assertStringContainsString('width=1200', $html);
    }

    /** @test */
    public function it_shows_overflow_when_history_has_more_than_five_events(): void
    {
        $history = [];
        for ($i = 1; $i <= 8; $i++) {
            $history[] = ['statusCode' => 4, 'label' => "1{$i}:00", 'status' => 'Em Curso'];
        }

        $html = view('og.fire', [
            'fire' => ['status' => 'Em Curso', 'location' => 'X', 'statusHistory' => $history],
            'kml'     => null,
            'kmlVost' => null,
        ])->render();

        // 8 events, we show last 5, overflow reads "+3 anteriores"
        $this->assertStringContainsString('+3 anteriores', $html);
        $this->assertStringNotContainsString('11:00', $html); // trimmed
        $this->assertStringNotContainsString('12:00', $html); // trimmed
        $this->assertStringNotContainsString('13:00', $html); // trimmed
        $this->assertStringContainsString('14:00', $html); // kept
        $this->assertStringContainsString('18:00', $html); // kept
    }

    /** @test */
    public function it_shows_empty_state_when_history_is_missing(): void
    {
        $html = view('og.fire', [
            'fire' => ['status' => 'Despacho', 'location' => 'X'],
            'kml'     => null,
            'kmlVost' => null,
        ])->render();

        $this->assertStringContainsString('Sem histórico', $html);
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

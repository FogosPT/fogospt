<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The Madeira map page ships the shell of the SRPC, IP-RAM panel that
 * main2.js fills from the Fogos.pt API v2, and the legacy per-fire URLs keep
 * working (they fall back to the map: v1/madeira/fires no longer exists).
 */
class MadeiraPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Upstream down: the page must not depend on server-side data.
        config(['services.fogos.url' => 'http://127.0.0.1:9']);
    }

    #[Test]
    #[DataProvider('localesProvider')]
    public function madeira_page_renders_the_occurrences_panel(string $locale, string $title, string $source): void
    {
        $this->get("/{$locale}/madeira")
            ->assertOk()
            ->assertSee('id="madeira-occ-list"', false)
            ->assertSee('id="madeira-event"', false)
            ->assertSee($title)
            ->assertSee($source)
            ->assertSee('/js/main2.js', false);
    }

    public static function localesProvider(): array
    {
        return [
            ['pt', 'Ocorrências na Madeira', 'Fonte: SRPC, IP-RAM (via Fogos.pt).'],
            ['en', 'Occurrences in Madeira', 'Source: SRPC, IP-RAM (via Fogos.pt).'],
            ['es', 'Incidencias en Madeira', 'Fuente: SRPC, IP-RAM (vía Fogos.pt).'],
        ];
    }

    #[Test]
    public function legacy_fire_urls_fall_back_to_the_madeira_map(): void
    {
        $this->get('/pt/madeira/fogo/2024010012345')
            ->assertOk()
            ->assertSee('id="madeira-occ-list"', false);

        $this->get('/madeira/fogo/2024010012345')
            ->assertRedirect('/pt/madeira/fogo/2024010012345');
    }

    #[Test]
    public function the_map_script_reads_the_v2_madeira_endpoints(): void
    {
        $js = file_get_contents(public_path('js/main2.js'));

        $this->assertStringContainsString('/v2/madeira', $js);
        $this->assertStringNotContainsString('v1/madeira/fires', $js);
    }
}

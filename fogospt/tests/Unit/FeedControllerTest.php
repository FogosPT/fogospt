<?php

namespace Tests\Unit;

use App\Http\Controllers\FeedController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Exercises FeedController's pure XML builders directly (via reflection) so the
 * feeds can be asserted without booting the framework or hitting the upstream
 * API — the public entry points only add Cache + LegacyApi around these.
 */
class FeedControllerTest extends TestCase
{
    /** @test */
    public function fires_feed_is_well_formed_and_maps_fields(): void
    {
        $xml = $this->render('renderFiresFeed', [$this->sampleFires()]);

        $this->assertTrue((new \DOMDocument())->loadXML($xml), 'fires feed must be well-formed XML');

        // The entry with no id is skipped, so 2 of the 3 fires become items.
        $this->assertSame(2, substr_count($xml, '<item>'));

        $this->assertStringContainsString('<link>https://fogos.pt/pt/fogo/123</link>', $xml);
        $this->assertStringContainsString('<guid isPermaLink="true">https://fogos.pt/pt/fogo/123</guid>', $xml);
        $this->assertStringContainsString('<category>Em Curso</category>', $xml);
        $this->assertStringContainsString('<georss:point>37.1 -8</georss:point>', $xml);

        // Unsafe characters from upstream text must be entity-escaped.
        $this->assertStringContainsString('Faro &amp; &lt;Loulé&gt;', $xml);
        $this->assertStringNotContainsString('Faro & <Loulé>', $xml);

        // Resource counts agree in number (1 -> singular, otherwise plural).
        $this->assertStringContainsString('1 aéreo.', $xml);
        $this->assertStringContainsString('3 aéreos', $xml);

        // pubDate comes from the absolute dateTime.sec, rendered RFC-822 in UTC.
        $this->assertStringContainsString('<pubDate>' . gmdate(DATE_RSS, 1782957840) . '</pubDate>', $xml);
    }

    /** @test */
    public function fires_feed_marks_unconfirmed_when_upstream_sends_minus_one(): void
    {
        $fires = [[
            'id' => '789', 'status' => 'Despacho', 'location' => 'Braga',
            'man' => -1, 'terrain' => -1, 'aerial' => -1,
            'dateTime' => ['sec' => 1782957840],
        ]];

        $xml = $this->render('renderFiresFeed', [$fires]);

        $this->assertStringContainsString('Meios: por confirmar', $xml);
        $this->assertStringNotContainsString('-1 operacionais', $xml);
        $this->assertStringNotContainsString('-1 terrestres', $xml);
        $this->assertStringNotContainsString('-1 aéreos', $xml);
    }

    /** @test */
    public function warnings_feed_derives_time_from_objectid_and_escapes(): void
    {
        $xml = $this->render('renderWarningsFeed', [$this->sampleWarnings()]);

        $this->assertTrue((new \DOMDocument())->loadXML($xml), 'warnings feed must be well-formed XML');

        // The empty-text warning is skipped.
        $this->assertSame(1, substr_count($xml, '<item>'));
        $this->assertStringContainsString('<guid isPermaLink="false">69cbf128bcf49e554d28ed82</guid>', $xml);
        $this->assertStringContainsString('A1 &amp; B2 &lt;cortada&gt;', $xml);

        // The first 4 bytes of the ObjectId are the publish time.
        $this->assertStringContainsString('<pubDate>' . gmdate(DATE_RSS, hexdec('69cbf128')) . '</pubDate>', $xml);
    }

    private function sampleFires(): array
    {
        return [
            [
                'id' => '123', 'status' => 'Em Curso', 'location' => 'Faro & <Loulé>',
                'man' => 10, 'terrain' => 2, 'aerial' => 1, 'natureza' => 'Mato',
                'lat' => 37.1, 'lng' => -8.0, 'dateTime' => ['sec' => 1782957840],
            ],
            [
                'id' => '456', 'status' => 'Conclusão', 'location' => 'Porto',
                'man' => 0, 'terrain' => 0, 'aerial' => 3,
                'date' => '02-07-2026', 'hour' => '14:07',
            ],
            ['status' => 'no id here'],
        ];
    }

    private function sampleWarnings(): array
    {
        return [
            ['_id' => ['$id' => '69cbf128bcf49e554d28ed82'], 'text' => 'A1 & B2 <cortada>'],
            ['text' => '   '],
        ];
    }

    private function render(string $method, array $args): string
    {
        $m = new ReflectionMethod(FeedController::class, $method);
        $m->setAccessible(true);

        return $m->invokeArgs(new FeedController(), $args);
    }
}

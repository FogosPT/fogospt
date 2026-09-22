<?php

namespace Tests\Unit;

use App\Libs\OgRenderer;
use Tests\TestCase;

// The image URL is what plataformas sociais key their cache on. The hash
// bucketing rules encoded here are the invariant: they decide when FB and
// WhatsApp will refetch the card. If they drift, existing shares silently
// stop refreshing on status changes. Uses the Illuminate TestCase because
// cachePath() reaches through storage_path() into the app container.
class OgRendererTest extends TestCase
{
    /** @test */
    public function hash_is_stable_for_the_same_inputs_inside_a_15_minute_bucket(): void
    {
        $fire = ['status' => 'Em Curso', 'man' => 10, 'terrain' => 4, 'aerial' => 1];

        $a = OgRenderer::hash($fire);
        $b = OgRenderer::hash($fire);

        $this->assertSame($a, $b);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $a);
    }

    /** @test */
    public function hash_changes_when_status_changes(): void
    {
        $emCurso     = OgRenderer::hash(['status' => 'Em Curso',     'man' => 5, 'terrain' => 2, 'aerial' => 0]);
        $emResolucao = OgRenderer::hash(['status' => 'Em Resolução', 'man' => 5, 'terrain' => 2, 'aerial' => 0]);

        $this->assertNotSame($emCurso, $emResolucao);
    }

    /** @test */
    public function hash_changes_when_meios_change(): void
    {
        $base = ['status' => 'Em Curso', 'man' => 10, 'terrain' => 4, 'aerial' => 1];

        $this->assertNotSame(
            OgRenderer::hash($base),
            OgRenderer::hash(array_merge($base, ['man' => 12]))
        );
        $this->assertNotSame(
            OgRenderer::hash($base),
            OgRenderer::hash(array_merge($base, ['terrain' => 5]))
        );
        $this->assertNotSame(
            OgRenderer::hash($base),
            OgRenderer::hash(array_merge($base, ['aerial' => 2]))
        );
    }

    /** @test */
    public function cache_path_encodes_id_and_hash(): void
    {
        $path = OgRenderer::cachePath('12345', 'abcd1234');

        $this->assertStringEndsWith('fogo-12345-abcd1234.png', $path);
        $this->assertStringContainsString('/storage/app/og/', $path);
    }

    /** @test */
    public function hash_changes_when_status_history_grows(): void
    {
        // Re-entering the same top-level state (Vigilância → Em Curso →
        // Vigilância) leaves `status` and meios untouched but adds a
        // timeline entry. The card must refresh so the new event shows.
        $base = ['status' => 'Vigilância', 'man' => 5, 'terrain' => 2, 'aerial' => 0];

        $before = OgRenderer::hash($base + ['statusHistory' => [['statusCode' => 7]]]);
        $after  = OgRenderer::hash($base + ['statusHistory' => [
            ['statusCode' => 7],
            ['statusCode' => 4],
            ['statusCode' => 7],
        ]]);

        $this->assertNotSame($before, $after);
    }

    /** @test */
    public function missing_meios_default_to_zero_so_partial_payloads_still_hash(): void
    {
        // Upstream may omit fields on very-new incidents. We must not fatal.
        $only_status = OgRenderer::hash(['status' => 'Despacho']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $only_status);
    }
}

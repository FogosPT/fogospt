<?php

namespace Tests\Unit;

use App\Libs\OgRenderer;
use Tests\TestCase;

// The OG card cache gets one PNG per fire per 20-min bucket; prune() keeps
// it bounded. `test_` prefixes (no annotations) work on PHPUnit 10 and 12.
class OgPruneTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = storage_path('app/og');
        @mkdir($this->dir, 0775, true);
    }

    public function test_prune_removes_only_files_older_than_the_cutoff(): void
    {
        $old = $this->dir . '/fogo-1-aaaaaaaa.png';
        $oldTmp = $this->dir . '/fogo-1-aaaaaaaa.png.1234.tmp';
        $fresh = $this->dir . '/fogo-2-bbbbbbbb.png';
        $unrelated = $this->dir . '/keep-me.txt';

        foreach ([$old, $oldTmp, $fresh, $unrelated] as $f) {
            file_put_contents($f, 'x');
        }
        touch($old, time() - 2 * 86400);
        touch($oldTmp, time() - 2 * 86400);
        touch($unrelated, time() - 2 * 86400);

        $this->assertSame(2, OgRenderer::prune(86400));
        $this->assertFileDoesNotExist($old);
        $this->assertFileDoesNotExist($oldTmp);
        $this->assertFileExists($fresh);
        $this->assertFileExists($unrelated);

        @unlink($fresh);
        @unlink($unrelated);
    }

    public function test_artisan_command_reports_removed_files(): void
    {
        $old = $this->dir . '/fogo-3-cccccccc.png';
        file_put_contents($old, 'x');
        touch($old, time() - 3 * 3600);

        $this->artisan('og:prune', ['--hours' => 2])
            ->expectsOutput('Removed 1 cached card(s).')
            ->assertSuccessful();

        $this->assertFileDoesNotExist($old);
    }
}

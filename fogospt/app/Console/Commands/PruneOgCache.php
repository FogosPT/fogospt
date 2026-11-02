<?php

namespace App\Console\Commands;

use App\Libs\OgRenderer;
use Illuminate\Console\Command;

class PruneOgCache extends Command
{
    protected $signature = 'og:prune {--hours=24 : Delete cached share cards older than this}';

    protected $description = 'Delete old per-fire Open Graph PNGs from storage/app/og';

    public function handle(): int
    {
        $removed = OgRenderer::prune(max(1, (int) $this->option('hours')) * 3600);
        $this->info("Removed {$removed} cached card(s).");

        return self::SUCCESS;
    }
}

<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * Compiles every Blade view from scratch and lints the PHP. Catches new Blade
 * directives that collide with literal text (e.g. `@context` in JSON-LD,
 * added in Laravel 12) — stale compiled views would otherwise hide them.
 */
class ViewsCompileTest extends TestCase
{
    #[Test]
    public function every_blade_view_compiles_to_valid_php(): void
    {
        $compiler = app('blade.compiler');
        $broken = [];

        foreach (Finder::create()->in(resource_path('views'))->name('*.blade.php') as $file) {
            $tmp = tempnam(sys_get_temp_dir(), 'blade');
            file_put_contents($tmp, $compiler->compileString($file->getContents()));
            exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($tmp) . ' 2>&1', $out, $rc);
            unlink($tmp);
            if ($rc !== 0) {
                $broken[] = $file->getRelativePathname();
            }
            $out = [];
        }

        $this->assertSame([], $broken);
    }

    #[Test]
    public function json_ld_keeps_its_at_keywords(): void
    {
        $this->get('/pt/sobre')->assertSee('"@context": "https://schema.org"', false);
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

// Every user-facing key must exist in pt, en and es — a missing one renders
// the raw key ("pages.stats.motives.title") on the page.
class TranslationsParityTest extends TestCase
{
    private const LOCALES = ['pt', 'en', 'es'];
    private const FILES = ['elements', 'pages', 'js', 'includes', 'api-docs', 'api-terms'];

    public function test_all_locales_define_the_same_keys(): void
    {
        $missing = [];
        foreach (self::FILES as $file) {
            $keys = [];
            foreach (self::LOCALES as $locale) {
                $keys[$locale] = array_keys($this->flatten(require __DIR__ . "/../../resources/lang/{$locale}/{$file}.php"));
            }
            $all = array_unique(array_merge(...array_values($keys)));
            foreach (self::LOCALES as $locale) {
                foreach (array_diff($all, $keys[$locale]) as $key) {
                    $missing[] = "{$locale}: {$file}.{$key}";
                }
            }
        }

        $this->assertSame([], $missing);
    }

    // Lists (e.g. api-terms sections) are compared as a whole, not per index.
    private function flatten(array $data, string $prefix = ''): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
            if (is_array($value) && !array_is_list($value)) {
                $out += $this->flatten($value, $path);
            } else {
                $out[$path] = true;
            }
        }
        return $out;
    }
}

<?php

namespace App\Http\Controllers;

use App\Libs\LegacyApi;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    private const LOCALES = ['pt', 'en', 'es'];
    private const BASE_URL = 'https://fogos.pt';
    private const CACHE_TTL_SECONDS = 900;

    public function fires()
    {
        $xml = Cache::get('sitemap:fires');
        if (!is_string($xml)) {
            $fires = LegacyApi::getFires();
            $entries = $fires['data'] ?? [];
            $xml = $this->renderFiresSitemap(is_array($entries) ? $entries : []);
            // Don't pin an empty sitemap for 15 min because upstream blipped.
            Cache::put('sitemap:fires', $xml, isset($fires['error']) ? 30 : self::CACHE_TTL_SECONDS);
        }

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL_SECONDS . ', s-maxage=' . self::CACHE_TTL_SECONDS);
    }

    private function renderFiresSitemap(array $fires): string
    {
        $now = date('Y-m-d');

        $urls = '';
        foreach ($fires as $fire) {
            if (empty($fire['id'])) {
                continue;
            }
            $id = (string) $fire['id'];
            if (!preg_match('/^[0-9A-Za-z_-]+$/', $id)) {
                continue;
            }
            $lastmod = $this->resolveLastmod($fire) ?: $now;

            foreach (['fogo/' . $id, 'fogo/' . $id . '/detalhe'] as $path) {
                $urls .= $this->renderUrl($path, $lastmod, 'hourly', '0.70');
            }
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
{$urls}</urlset>

XML;
    }

    private function renderUrl(string $path, string $lastmod, string $changefreq, string $priority): string
    {
        $alternates = '';
        foreach (self::LOCALES as $locale) {
            $href = self::BASE_URL . '/' . $locale . '/' . $path;
            $alternates .= "        <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$href}\"/>\n";
        }
        $xDefault = self::BASE_URL . '/pt/' . $path;
        $alternates .= "        <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$xDefault}\"/>\n";

        return "    <url>\n"
            . "        <loc>" . self::BASE_URL . '/pt/' . $path . "</loc>\n"
            . $alternates
            . "        <lastmod>{$lastmod}</lastmod>\n"
            . "        <changefreq>{$changefreq}</changefreq>\n"
            . "        <priority>{$priority}</priority>\n"
            . "    </url>\n";
    }

    private function resolveLastmod(array $fire): ?string
    {
        if (empty($fire['date'])) {
            return null;
        }
        // Upstream serves dates as "DD-MM-YYYY"; sitemap.org requires W3C datetime.
        $parts = explode('-', $fire['date']);
        if (count($parts) !== 3) {
            return null;
        }
        [$d, $m, $y] = $parts;
        if (!ctype_digit($d) || !ctype_digit($m) || !ctype_digit($y)) {
            return null;
        }
        return sprintf('%04d-%02d-%02d', $y, $m, $d);
    }
}

<?php

namespace App\Http\Controllers;

use App\Libs\LegacyApi;
use Illuminate\Support\Facades\Cache;

/**
 * RSS 2.0 feeds for active fires and warnings.
 *
 * Mirrors SitemapController: fetch upstream data via LegacyApi, build the XML
 * as a string, cache the rendered document in Redis and serve it with explicit
 * Content-Type + Cache-Control headers. The feeds are locale-agnostic and live
 * at the site root so readers can subscribe with a single, stable URL.
 */
class FeedController extends Controller
{
    private const BASE_URL = 'https://fogos.pt';
    private const CACHE_TTL_SECONDS = 300;

    public function fires()
    {
        $xml = Cache::remember('feed:fires', self::CACHE_TTL_SECONDS, function () {
            $fires = LegacyApi::getFires();
            $entries = $fires['data'] ?? [];

            return $this->renderFiresFeed(is_array($entries) ? $entries : []);
        });

        return $this->xmlResponse($xml);
    }

    public function warnings()
    {
        $xml = Cache::remember('feed:warnings', self::CACHE_TTL_SECONDS, function () {
            $warnings = LegacyApi::getWarnings();
            $entries = $warnings['data'] ?? [];

            return $this->renderWarningsFeed(is_array($entries) ? $entries : []);
        });

        return $this->xmlResponse($xml);
    }

    private function xmlResponse(string $xml)
    {
        return response($xml, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL_SECONDS . ', s-maxage=' . self::CACHE_TTL_SECONDS);
    }

    private function renderFiresFeed(array $fires): string
    {
        $self = self::BASE_URL . '/rss/fires.xml';
        $items = '';

        foreach ($fires as $fire) {
            if (empty($fire['id'])) {
                continue;
            }
            $id       = (string) $fire['id'];
            $url      = self::BASE_URL . '/pt/fogo/' . $id;
            $status   = trim((string) ($fire['status'] ?? ''));
            $location = trim((string) ($fire['location'] ?? $id));
            $title    = $status !== '' ? $status . ' — ' . $location : $location;

            $men     = (int) ($fire['man'] ?? 0);
            $terrain = (int) ($fire['terrain'] ?? 0);
            $aerial  = (int) ($fire['aerial'] ?? 0);
            $nature  = trim((string) ($fire['natureza'] ?? ''));

            $descParts = [];
            if ($status !== '') {
                $descParts[] = 'Estado: ' . $status;
            }
            if ($nature !== '') {
                $descParts[] = 'Natureza: ' . $nature;
            }
            // Upstream uses -1 to flag counts that haven't been reported yet
            // (see main.js: `hasUnknownMeios = man == -1 || terrain == -1 ||
            // aerial == -1`). Treat the whole row as unconfirmed rather than
            // printing "-1 operacionais".
            if ($men < 0 || $terrain < 0 || $aerial < 0) {
                $descParts[] = 'Meios: por confirmar';
            } else {
                $descParts[] = 'Meios: ' . $this->meios($men, 'operacional', 'operacionais')
                    . ', ' . $this->meios($terrain, 'terrestre', 'terrestres')
                    . ', ' . $this->meios($aerial, 'aéreo', 'aéreos');
            }
            $description = implode('. ', $descParts) . '.';

            $geo = '';
            if (isset($fire['lat'], $fire['lng']) && is_numeric($fire['lat']) && is_numeric($fire['lng'])) {
                $geo = '            <georss:point>' . (float) $fire['lat'] . ' ' . (float) $fire['lng'] . "</georss:point>\n";
            }

            $items .= "        <item>\n"
                . '            <title>' . $this->esc($title) . "</title>\n"
                . '            <link>' . $this->esc($url) . "</link>\n"
                . '            <guid isPermaLink="true">' . $this->esc($url) . "</guid>\n"
                . '            <pubDate>' . gmdate(DATE_RSS, $this->fireTimestamp($fire)) . "</pubDate>\n"
                . ($status !== '' ? '            <category>' . $this->esc($status) . "</category>\n" : '')
                . '            <description>' . $this->esc($description) . "</description>\n"
                . $geo
                . "        </item>\n";
        }

        return $this->channel(
            'Fogos.pt — Incêndios ativos',
            self::BASE_URL . '/pt',
            'Incêndios rurais ativos em Portugal continental, atualizados em tempo real pelo Fogos.pt.',
            $self,
            $items
        );
    }

    private function renderWarningsFeed(array $warnings): string
    {
        $self = self::BASE_URL . '/rss/warnings.xml';
        $link = self::BASE_URL . '/pt/avisos';
        $items = '';

        foreach ($warnings as $warning) {
            $text = trim((string) ($warning['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $wid  = (string) ($warning['_id']['$id'] ?? '');
            $guid = $wid !== '' ? $wid : md5($text);

            $items .= "        <item>\n"
                . '            <title>' . $this->esc($text) . "</title>\n"
                . '            <link>' . $this->esc($link) . "</link>\n"
                . '            <guid isPermaLink="false">' . $this->esc($guid) . "</guid>\n"
                . '            <pubDate>' . gmdate(DATE_RSS, $this->warningTimestamp($wid)) . "</pubDate>\n"
                . '            <description>' . $this->esc($text) . "</description>\n"
                . "        </item>\n";
        }

        return $this->channel(
            'Fogos.pt — Avisos',
            $link,
            'Avisos e cortes de estrada relacionados com incêndios em Portugal, publicados pelo Fogos.pt.',
            $self,
            $items
        );
    }

    private function channel(string $title, string $link, string $description, string $self, string $items): string
    {
        $now = gmdate(DATE_RSS);
        $t = $this->esc($title);
        $l = $this->esc($link);
        $d = $this->esc($description);
        $s = $this->esc($self);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:georss="http://www.georss.org/georss">
    <channel>
        <title>{$t}</title>
        <link>{$l}</link>
        <description>{$d}</description>
        <language>pt-PT</language>
        <lastBuildDate>{$now}</lastBuildDate>
        <ttl>5</ttl>
        <atom:link href="{$s}" rel="self" type="application/rss+xml"/>
{$items}    </channel>
</rss>

XML;
    }

    /**
     * Upstream serves an absolute Unix timestamp in dateTime.sec; fall back to
     * parsing the "DD-MM-YYYY" + "HH:MM" pair, then to now.
     */
    private function fireTimestamp(array $fire): int
    {
        if (!empty($fire['dateTime']['sec']) && is_numeric($fire['dateTime']['sec'])) {
            return (int) $fire['dateTime']['sec'];
        }
        if (!empty($fire['date'])) {
            $parts = explode('-', $fire['date']);
            if (count($parts) === 3) {
                [$dd, $mm, $yy] = $parts;
                $hour = !empty($fire['hour']) ? $fire['hour'] : '00:00';
                $ts = strtotime(sprintf('%04d-%02d-%02d %s', $yy, $mm, $dd, $hour));
                if ($ts !== false) {
                    return $ts;
                }
            }
        }
        return time();
    }

    /**
     * A Mongo ObjectId encodes its creation time in the first 4 bytes, which
     * is exactly when the warning was published.
     */
    private function warningTimestamp(string $objectId): int
    {
        if (strlen($objectId) >= 8 && ctype_xdigit(substr($objectId, 0, 8))) {
            return (int) hexdec(substr($objectId, 0, 8));
        }
        return time();
    }

    /**
     * "1 aéreo" vs "2 aéreos" — pick the number's grammatical form.
     */
    private function meios(int $count, string $singular, string $plural): string
    {
        return $count . ' ' . ($count === 1 ? $singular : $plural);
    }

    /**
     * Strip characters illegal in XML 1.0, then entity-escape the rest.
     */
    private function esc(string $value): string
    {
        $clean = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $value);

        return htmlspecialchars($clean ?? $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

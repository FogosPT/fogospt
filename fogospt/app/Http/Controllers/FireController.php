<?php

namespace App\Http\Controllers;

use App\Libs\HelperFuncs;
use App\Libs\LegacyApi;
use App\Libs\OgRenderer;
use GuzzleHttp;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Jorenvh\Share\Share;


class FireController extends Controller
{
    public $fire;

    public function get($locale, $id)
    {
        if (!$id) {
            return view('index');
        }

        $this->setFireById($id);
        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFire($id);

        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        if (isset($status['data'])) {
            $this->fire['statusHistory'] = $status['data'];
        } else {
            $this->fire['statusHistory'] = false;
        }

        $hash = OgRenderer::hash($this->fire);
        $metadata = $this->generateMetadata($id, $hash);
        $shares = new Share();
        $s = $shares->page($metadata['url'])
            ->facebook()
            ->whatsapp();

        return view('index', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata));
    }

    public function getDetails($locale, $id)
    {
        if (!$id) {
            return view('index');
        }

        $this->setFireById($id);

        if ($this->fire === null) {
            abort(404);
        }

        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFire($id);

        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        if (isset($status['data'])) {
            $this->fire['statusHistory'] = $status['data'];
        } else {
            $this->fire['statusHistory'] = false;
        }

        $hash = OgRenderer::hash($this->fire);
        $metadata = $this->generateMetadata($id, $hash);
        $shares = new Share();
        $s = $shares->page($metadata['url'])
            ->facebook()
            ->whatsapp();

        if(isset($this->fire['kml'])){
            $kml = preg_replace( "/\r|\n/", "", $this->fire['kml'] );
        } else {
            $kml = null;
        }

        if(isset($this->fire['kmlVost'])){
            $kmlVost = preg_replace( "/\r|\n/", "", $this->fire['kmlVost'] );
        } else {
            $kmlVost = null;
        }

        return view('detail', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata, 'kml' => $kml, 'kmlVost' => $kmlVost));
    }

    // Public endpoint that FB/WhatsApp/X hit for og:image. Always answers
    // 200 with a PNG — even for missing / broken fires we fall back to the
    // legacy static card, because a 404 makes the crawler give up on the
    // preview entirely.
    public function getOgImage($id)
    {
        $this->setFireById($id);
        if ($this->fire === null) {
            return $this->serveStaticOg(60);
        }

        // Enrich with the same fields generateMetadata() would see so the
        // hash reflects everything meaningful.
        $risk   = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFire($id);
        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        $this->fire['statusHistory'] = isset($status['data']) ? $status['data'] : false;

        $hash = OgRenderer::hash($this->fire);

        $base = rtrim((string) config('services.og_renderer.internal_app_url', 'http://host.docker.internal:8093'), '/');
        $renderUrl = "{$base}/pt/og/fogo/{$id}/render?t={$hash}";

        $path = OgRenderer::render((string) $id, $hash, $renderUrl);

        if ($path === null) {
            // Sidecar failed — serve the fallback with a short TTL so we
            // can retry soon without poisoning the CDN with a stale error.
            return $this->serveStaticOg(60, 'MISS-FALLBACK');
        }

        return response()->file($path, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=300, s-maxage=900, stale-while-revalidate=86400',
            'X-Cache'       => is_file($path) && (time() - filemtime($path) > 5) ? 'HIT' : 'MISS',
        ]);
    }

    // Renders the slim 1200x630 Blade that the headless sidecar screenshots.
    // Gated by the `og.internal` middleware — never reachable from the web.
    public function renderOgHtml($locale, $id)
    {
        $this->setFireById($id);
        if ($this->fire === null) {
            abort(404);
        }

        $risk   = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFire($id);
        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        $this->fire['statusHistory'] = isset($status['data']) ? $status['data'] : false;

        $kml     = isset($this->fire['kml'])     ? preg_replace("/\r|\n/", '', $this->fire['kml'])     : null;
        $kmlVost = isset($this->fire['kmlVost']) ? preg_replace("/\r|\n/", '', $this->fire['kmlVost']) : null;

        return response()->view('og.fire', [
            'fire'    => $this->fire,
            'kml'     => $kml,
            'kmlVost' => $kmlVost,
        ])->header('Cache-Control', 'private, no-store');
    }

    private function serveStaticOg(int $maxAge, string $xCache = 'STATIC'): Response
    {
        $path = public_path('img/og-image.png');
        return response()->file($path, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => "public, max-age={$maxAge}, s-maxage={$maxAge}",
            'X-Cache'       => $xCache,
        ]);
    }

    public function getSharesCard($locale, $id)
    {
        $this->setFireById($id);

        if ($this->fire === null) {
            return view('elements.shares', ['shares' => '', 'fire' => [], 'metadata' => []]);
        }

        $metadata = $this->generateMetadata();

        $shares = new Share();
        $s = $shares->page($metadata['url'], $metadata['title'])
            ->facebook()
            ->twitter()
            ->whatsapp();

        $s = str_replace('views/shares', 'fogo', $s);

        return view('elements.shares', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata));
    }

    public function getGeneralCard($locale, $id)
    {
        $this->setFireById($id);

        if ($this->fire === null) {
            return view('elements.risk', ['fire' => []]);
        }

        $risk = LegacyApi::getRiskByFire($id);
        $this->fire['risk'] = $risk['data'][0]['hoje'] ?? null;

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCard($locale, $id)
    {
        $this->setFireById($id);

        if ($this->fire === null) {
            return view('elements.status', ['fire' => []]);
        }

        $status = LegacyApi::getStatusByFire($id);
        $this->fire['statusHistory'] = $status['data'] ?? [];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCard($locale, $id)
    {
        $this->setFireById($id);

        return view('elements.meteo', array('fire' => $this->fire ?? []));
    }

    public function getExtraCard($locale, $id)
    {
        $this->setFireById($id);

        if (!empty($this->fire['extra']) || !empty($this->fire['pco']) || !empty($this->fire['cos'])) {
            return view('elements.extra', array('fire' => $this->fire));
        } else {
            return \Response::json();
        }

    }


    public function getAll()
    {
        return \Response::json(LegacyApi::getFires());
    }

    public function getMadeira($locale, $id)
    {
        if (!$id) {
            return view('index-madeira');
        }

        $this->setMadeiraFireById($id);

        if ($this->fire === null) {
            return view('index-madeira');
        }

        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFireMadeira($id);
        $this->fire['risk'] = $risk['data'][0]['hoje'] ?? null;
        $this->fire['statusHistory'] = $status['data'] ?? false;

        return view('index-madeira', array('fire' => $this->fire, 'metadata' => $this->generateMetadata()));
    }

    public function getGeneralCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        if ($this->fire === null) {
            return view('elements.risk', ['fire' => []]);
        }

        $risk = LegacyApi::getRiskByFire($id);
        $this->fire['risk'] = $risk['data'][0]['hoje'] ?? null;

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        if ($this->fire === null) {
            return view('elements.status', ['fire' => []]);
        }

        $status = LegacyApi::getStatusByFireMadeira($id);
        $this->fire['statusHistory'] = $status['data'] ?? [];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        return view('elements.meteo', array('fire' => $this->fire ?? []));
    }

    public function getExtraCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        if (!empty($this->fire['extra'])) {
            return view('elements.extra', array('fire' => $this->fire));
        } else {
            return \Response::json();
        }

    }

    public function getAllMadeira()
    {
        return \Response::json(LegacyApi::getFires());
    }


    private function setFireById($id)
    {
        $fire = LegacyApi::getFire($id);

        if (isset($fire['data'])) {
            $this->fire = $fire['data'];
        } else {
            $this->fire = null;
        }
    }

    private function setMadeiraFireById($id)
    {
        $fire = LegacyApi::getMadeiraFire($id);

        if (isset($fire['data'])) {
            $this->fire = $fire['data'];
        } else {
            $this->fire = null;
        }

    }

    public function getLightnings()
    {
        $cacheKey = 'lightnings:dea:v2';
        $lockKey  = 'lightnings:dea:lock';
        $softTtl  = 300;    // 5 min — considered fresh
        $hardTtl  = 3600;   // 1 h  — usable as stale fallback

        $cached = Redis::get($cacheKey);
        $entry = $cached ? json_decode($cached, true) : null;
        $hasEntry = is_array($entry) && isset($entry['payload'], $entry['fetched_at']);
        $isFresh = $hasEntry && (time() - (int) $entry['fetched_at']) < $softTtl;

        if ($isFresh) {
            return response($entry['payload'], 200)
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'public, max-age=300')
                ->header('X-Cache', 'HIT');
        }

        // Single-flight: only one worker may probe IPMA at a time. Anyone else
        // serves the last stale payload (or empty) immediately — never block
        // on the upstream, which is what was starving PHP-FPM under load.
        $haveLock = Redis::set($lockKey, '1', 'EX', 15, 'NX');
        if (!$haveLock) {
            return $this->lightningsStaleOrEmpty($entry);
        }

        try {
            $payload = $this->fetchLightningsPayload();
        } finally {
            Redis::del($lockKey);
        }

        if ($payload === null) {
            return $this->lightningsStaleOrEmpty($entry);
        }

        Redis::set($cacheKey, json_encode(['payload' => $payload, 'fetched_at' => time()]), 'EX', $hardTtl);

        return response($payload, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=300')
            ->header('X-Cache', 'MISS');
    }

    private function lightningsStaleOrEmpty($entry)
    {
        if (is_array($entry) && isset($entry['payload'])) {
            return response($entry['payload'], 200)
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'public, max-age=60')
                ->header('X-Cache', 'STALE');
        }
        return response(json_encode(['data' => []]), 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=30')
            ->header('X-Cache', 'EMPTY');
    }

    /**
     * Scrape IPMA's obs.dea page for the live lightning GeoJSON embedded as
     * `var data = {...FeatureCollection...};`. Returns the normalised JSON
     * string the frontend expects, or null on any failure.
     *
     * Short 4s timeout because this runs inside a request-serving worker;
     * we'd rather return stale than tie up a slot.
     */
    private function fetchLightningsPayload()
    {
        try {
            $client = new GuzzleHttp\Client(['timeout' => 4, 'connect_timeout' => 2]);
            $resp = $client->request('GET', 'https://www.ipma.pt/pt/otempo/obs.dea/', [
                'http_errors' => false,
                'headers'     => ['User-Agent' => 'Mozilla/5.0 (fogos.pt lightnings proxy)'],
            ]);
        } catch (\Exception $e) {
            return null;
        }
        if ($resp->getStatusCode() !== 200) return null;

        $json = $this->extractBalancedJson((string) $resp->getBody(), 'var data = ');
        if ($json === null) return null;
        $geo = json_decode($json, true);
        if (!is_array($geo) || !isset($geo['features']) || !is_array($geo['features'])) return null;

        $items = [];
        foreach ($geo['features'] as $f) {
            $coords = $f['geometry']['coordinates'] ?? null;
            $props  = $f['properties']             ?? null;
            if (!is_array($coords) || count($coords) < 2 || !is_array($props)) continue;
            // IPMA serves the strike time as a naive ISO string (no Z / no
            // offset) but the values are UTC. Append Z so the frontend
            // doesn't misread it as local time.
            $time = $props['time'] ?? null;
            if (is_string($time) && $time !== '' && !preg_match('/[zZ]|[+\-]\d{2}:?\d{2}$/', $time)) {
                $time .= 'Z';
            }
            $items[] = [
                'timestamp' => $time,
                'payload'   => [
                    'latitude'  => (float) $coords[1],
                    'longitude' => (float) $coords[0],
                    'intensity' => isset($props['amplitude']) ? (float) $props['amplitude'] : null,
                    'icloud'    => !empty($props['icloud']),
                ],
            ];
        }

        return json_encode([
            'data'    => $items,
            'updated' => $geo['update_date'] ?? null,
        ]);
    }

    /**
     * Find a JSON object literal that follows $marker in $haystack, matching
     * braces while respecting string quoting. Returns the JSON text or null.
     * Needed because IPMA's inlined GeoJSON has nested objects, so a naive
     * regex would either stop at the first `}` or grab too much.
     */
    private function extractBalancedJson($haystack, $marker)
    {
        $start = strpos($haystack, $marker);
        if ($start === false) {
            return null;
        }
        $i = $start + strlen($marker);
        $len = strlen($haystack);
        while ($i < $len && ctype_space($haystack[$i])) {
            $i++;
        }
        if ($i >= $len || $haystack[$i] !== '{') {
            return null;
        }
        $objStart = $i;
        $depth = 0;
        $inStr = false;
        $esc = false;
        for (; $i < $len; $i++) {
            $c = $haystack[$i];
            if ($inStr) {
                if ($esc) {
                    $esc = false;
                } elseif ($c === '\\') {
                    $esc = true;
                } elseif ($c === '"') {
                    $inStr = false;
                }
                continue;
            }
            if ($c === '"') {
                $inStr = true;
            } elseif ($c === '{') {
                $depth++;
            } elseif ($c === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($haystack, $objStart, $i - $objStart + 1);
                }
            }
        }
        return null;
    }


}

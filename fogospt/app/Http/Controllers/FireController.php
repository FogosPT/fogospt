<?php

namespace App\Http\Controllers;

use App\Libs\HelperFuncs;
use App\Libs\LegacyApi;
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

        $metadata = $this->generateMetadata();
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

        $metadata = $this->generateMetadata();
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
        $cacheKey = 'lightnings:dea';
        if (env('APP_ENV') === 'production') {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                return response($cached, 200)
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'public, max-age=300')
                    ->header('X-Cache', 'HIT');
            }
        }

        $empty = json_encode(['data' => []]);

        // IPMA's old dea.json endpoint stopped being updated (always returns
        // {"data":[]}). The live feed is embedded in the obs.dea HTML page
        // as a `var data = {...FeatureCollection...};` assignment, so we
        // scrape the JSON object out and normalise it to the shape the
        // map JS already expects: {data: [{timestamp, payload:{...}}, ...]}.
        try {
            $client = new GuzzleHttp\Client(['timeout' => 10]);
            $resp = $client->request('GET', 'https://www.ipma.pt/pt/otempo/obs.dea/', [
                'http_errors' => false,
                'headers'     => ['User-Agent' => 'Mozilla/5.0 (fogos.pt lightnings proxy)'],
            ]);
        } catch (\Exception $e) {
            return response($empty, 200)->header('Content-Type', 'application/json');
        }

        if ($resp->getStatusCode() !== 200) {
            return response($empty, 200)->header('Content-Type', 'application/json');
        }

        $html = (string) $resp->getBody();
        $json = $this->extractBalancedJson($html, 'var data = ');
        if ($json === null) {
            return response($empty, 200)->header('Content-Type', 'application/json');
        }
        $geo = json_decode($json, true);
        if (!is_array($geo) || !isset($geo['features']) || !is_array($geo['features'])) {
            return response($empty, 200)->header('Content-Type', 'application/json');
        }

        $items = [];
        foreach ($geo['features'] as $f) {
            $coords = $f['geometry']['coordinates'] ?? null;
            $props  = $f['properties']             ?? null;
            if (!is_array($coords) || count($coords) < 2 || !is_array($props)) {
                continue;
            }
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

        $payload = json_encode([
            'data'    => $items,
            'updated' => $geo['update_date'] ?? null,
        ]);

        if (env('APP_ENV') === 'production') {
            Redis::set($cacheKey, $payload, 'EX', 300);
        }

        return response($payload, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=300')
            ->header('X-Cache', 'MISS');
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

<?php

namespace App\Http\Controllers;

use App\Libs\HotSpots;
use App\Libs\LegacyApi;
use Carbon\Carbon;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ApiController extends Controller
{
    public function getMobileContributors()
    {
        $contributors = LegacyApi::getMobileContributors();

        return \Response::json($contributors);
    }

    public function getModis()
    {
        return $this->cachedHotspots('modis', function () {
            return HotSpots::getModis();
        });
    }

    public function getVIIRS()
    {
        return $this->cachedHotspots('VIIRS', function () {
            return HotSpots::getVIIRS();
        });
    }

    /**
     * Shared Redis-backed cache for the NASA FIRMS hotspot feeds. The upstream
     * CSV at fogos.icnf.pt is slow and the response is identical for every
     * visitor, so we serve the cached JSON for 18 min and let the browser
     * reuse it for the same window via Cache-Control.
     */
    private function cachedHotspots($key, callable $fetch)
    {
        $cached = Redis::get($key);
        if ($cached) {
            return response($cached, 200)
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'public, max-age=1080')
                ->header('X-Cache', 'HIT');
        }

        $data = $fetch();
        $encoded = json_encode($data ?: new \stdClass());

        if (!empty($data)) {
            Redis::set($key, $encoded, 'EX', 1080);
        }

        return response($encoded, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=1080')
            ->header('X-Cache', 'MISS');
    }

    /**
     * Find the most recent AROME model run that the IPMA WMS will actually
     * render. The capabilities document advertises a default reference_time
     * but the server returns 404 unless we send the parameter explicitly,
     * and the newest run isn't always published yet — so we probe a tiny
     * GetMap and walk back through (00, 12 UTC) candidates until one
     * answers 200. Result cached in Redis for 30 min.
     */
    public function getIpmaReferenceTime()
    {
        $refTime = $this->resolveAromeReferenceTime();
        if ($refTime === null) {
            return response()->json(['error' => 'unavailable'], 503);
        }
        return response(json_encode(['reference_time' => $refTime]), 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=1800');
    }

    /**
     * Returns the most recent AROME model run that IPMA's WMS will actually
     * render, or null if none of the recent candidates respond. Caches the
     * answer in Redis for 30 min so callers don't reprobe every request.
     */
    private function resolveAromeReferenceTime()
    {
        $cacheKey = 'ipma:ref-time';
        if (env('APP_ENV') === 'production') {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                $decoded = json_decode($cached, true);
                if (isset($decoded['reference_time'])) {
                    return $decoded['reference_time'];
                }
            }
        }

        $now = Carbon::now('UTC');
        $runHour = $now->hour < 12 ? 0 : 12;
        $candidate = $now->copy()->setTime($runHour, 0, 0);

        $client = new GuzzleHttp\Client(['timeout' => 10]);
        $found = null;

        for ($i = 0; $i < 4; $i++) {
            $refTime = $candidate->format('Y-m-d\TH:i');
            $url = 'https://mf2.ipma.pt/services/?SERVICE=WMS&VERSION=1.3.0&REQUEST=GetMap'
                . '&LAYERS=arome.2m.temperature.continent&STYLES=&CRS=EPSG:3857'
                . '&BBOX=-1100000,4500000,-700000,5200000&WIDTH=32&HEIGHT=32'
                . '&FORMAT=image/png&TRANSPARENT=true&reference_time=' . urlencode($refTime);

            try {
                $resp = $client->request('HEAD', $url, ['http_errors' => false]);
                if ($resp->getStatusCode() === 200) {
                    $found = $refTime;
                    break;
                }
            } catch (\Exception $e) {
                // try next candidate
            }

            $candidate->subHours(12);
        }

        if ($found !== null && env('APP_ENV') === 'production') {
            Redis::set($cacheKey, json_encode(['reference_time' => $found]), 'EX', 1800);
        }

        return $found;
    }

    /**
     * Proxy for IPMA AROME u/v wind grid, served in the leaflet-velocity
     * format (array of two header/data objects with parameterNumber 2/3).
     *
     * IPMA publishes runs at 00 and 12 UTC; we walk back from the current
     * 12-hour boundary trying candidate (run, forecast_hour) pairs until
     * one returns 200, then inject the GRIB metadata leaflet-velocity
     * needs. Result cached in Redis for 30 min so we shield IPMA from
     * traffic and keep frontend latency low.
     */
    public function getIpmaWind()
    {
        $cacheKey = 'ipma:wind';

        if (env('APP_ENV') === 'production') {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                return response($cached, 200)
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'public, max-age=1800')
                    ->header('X-Cache', 'HIT');
            }
        }

        $now = Carbon::now('UTC');
        $runHour = $now->hour < 12 ? 0 : 12;
        $candidate = $now->copy()->setTime($runHour, 0, 0);

        $client = new GuzzleHttp\Client(['timeout' => 15]);
        $data = null;
        $usedRun = null;
        $usedForecastHour = null;

        for ($i = 0; $i < 4; $i++) {
            $forecastHour = $candidate->diffInHours($now);

            if ($forecastHour >= 1) {
                $url = sprintf(
                    'https://mf2.ipma.pt/services/data/arome/wind_pt2/%s/%d',
                    $candidate->format('Y-m-d\TH'),
                    $forecastHour
                );

                try {
                    $response = $client->request('GET', $url, ['http_errors' => false]);
                    if ($response->getStatusCode() === 200) {
                        $decoded = json_decode($response->getBody()->getContents(), true);
                        if (is_array($decoded) && count($decoded) === 2
                            && isset($decoded[0]['header'], $decoded[0]['data'])
                            && isset($decoded[1]['header'], $decoded[1]['data'])) {
                            $data = $decoded;
                            $usedRun = $candidate->format('Y-m-d\TH:i\Z');
                            $usedForecastHour = $forecastHour;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // try next candidate
                }
            }

            $candidate->subHours(12);
        }

        if ($data === null) {
            return response()->json(['error' => 'unavailable'], 503);
        }

        // Inject the GRIB metadata that leaflet-velocity uses to distinguish
        // the U and V wind components. IPMA returns them in this order but
        // doesn't tag the headers, so we do it here.
        $data[0]['header']['parameterCategory'] = 2;
        $data[0]['header']['parameterNumber']   = 2;
        $data[0]['header']['parameterUnit']     = 'm.s-1';
        $data[1]['header']['parameterCategory'] = 2;
        $data[1]['header']['parameterNumber']   = 3;
        $data[1]['header']['parameterUnit']     = 'm.s-1';
        $data[0]['header']['refTime']      = $usedRun;
        $data[0]['header']['forecastTime'] = $usedForecastHour;

        $encoded = json_encode($data);

        if (env('APP_ENV') === 'production') {
            Redis::set($cacheKey, $encoded, 'EX', 1800);
        }

        return response($encoded, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=1800')
            ->header('X-Cache', 'MISS');
    }

    /**
     * Time-series at a single (lat, lng) point for the IPMA charts shown on
     * fire detail pages. Mirrors MF2's GetFeatureInfo trick (one WMS call
     * with a comma-separated list of timestamps in the time= parameter
     * returns a time series). Two calls in series: AROME hourly + LSA-SAF/RCM
     * daily. Result normalised and cached in Redis for 1h.
     */
    public function getIpmaPoint($lat, $lng)
    {
        $lat = (float) $lat;
        $lng = (float) $lng;

        $region = $this->regionForPoint($lat, $lng);
        if ($region === null) {
            return response()->json(['error' => 'out_of_range'], 422);
        }

        $cacheKey = sprintf('ipma:point:%.3f:%.3f', $lat, $lng);
        if (env('APP_ENV') === 'production') {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                return response($cached, 200)
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'public, max-age=1800')
                    ->header('X-Cache', 'HIT');
            }
        }

        $refTime = $this->resolveAromeReferenceTime();
        if ($refTime === null) {
            return response()->json(['error' => 'reference_time_unavailable'], 503);
        }
        $refTimeIso = $refTime . ':00';

        // Build a 1° × 1° bbox centered on the point in EPSG:3857 for a 256×256
        // request — the centred pixel ends up at i=128, j=128.
        list($mx, $my) = $this->lngLatToWebMercator($lng, $lat);
        $halfSpanMx = 55600;  // ~0.5° in meters at PT latitudes (good enough)
        $bbox = sprintf('%.0f,%.0f,%.0f,%.0f',
            $mx - $halfSpanMx, $my - $halfSpanMx,
            $mx + $halfSpanMx, $my + $halfSpanMx);

        $aromeLayers = [
            'arome.2m.temperature.' . $region,
            'arome.2m.relative_humidity.' . $region,
            'arome.10m.windintensity.' . $region,
            'arome.10m.gustintensity.' . $region,
            'arome.10m.windbarbs.' . $region,
            'arome.2m.pressure.' . $region,
            'arome.0m.precipitation.' . $region,
        ];
        $satLayers = [
            'lsasaf.fwi.' . $region,
            'lsasaf.isi.' . $region,
            'lsasaf.bui.' . $region,
            'lsasaf.dc.' . $region,
            'lsasaf.dmc.' . $region,
            'lsasaf.ffmc.' . $region,
            'lsasaf.p2000.' . $region,
            'lsasaf.p2000a.' . $region,
            'ipma.rcm.' . $region,
        ];

        // 48 hourly timestamps (AROME) and 10 daily (LSA-SAF/RCM) starting at ref.
        $hourlyTimes = [];
        for ($h = 0; $h < 48; $h++) {
            $hourlyTimes[] = Carbon::parse($refTimeIso)->addHours($h)->format('Y-m-d\TH:i:s');
        }
        $dailyTimes = [];
        for ($d = 0; $d < 10; $d++) {
            $dailyTimes[] = Carbon::parse($refTimeIso)->addDays($d)->format('Y-m-d\TH:i:s');
        }

        $client = new GuzzleHttp\Client(['timeout' => 20]);

        $aromeRaw = $this->fetchGfi($client, $aromeLayers, $hourlyTimes, $refTimeIso, $bbox);
        $satRaw   = $this->fetchGfi($client, $satLayers,   $dailyTimes,  $refTimeIso, $bbox);

        if ($aromeRaw === null && $satRaw === null) {
            return response()->json(['error' => 'upstream_unavailable'], 503);
        }

        $hourly = $this->normaliseAromeSeries($aromeRaw, $region);
        $daily  = $this->normaliseSatSeries($satRaw, $region);

        $payload = [
            'lat'            => $lat,
            'lng'            => $lng,
            'region'         => $region,
            'reference_time' => $refTime,
            'hourly'         => $hourly,
            'daily'          => $daily,
        ];

        $encoded = json_encode($payload);

        if (env('APP_ENV') === 'production') {
            Redis::set($cacheKey, $encoded, 'EX', 3600);
        }

        return response($encoded, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=1800')
            ->header('X-Cache', 'MISS');
    }

    /**
     * Aggressively-cached proxy for IPMA WMS GetMap / GetLegendGraphic so the
     * browser never talks to mf2.ipma.pt directly. The cache key is derived
     * from the normalised whitelisted query string, which makes hits stable
     * across leaflet's param-ordering quirks. Tiles for a given reference_time
     * are immutable, so we send long max-age + immutable HTTP cache headers
     * and store the raw PNG in Redis (base64 — phpredis returns binary-safe
     * but base64 keeps it portable) for 24 h.
     */
    public function getIpmaWms(Request $request)
    {
        $allowed = [
            'SERVICE','VERSION','REQUEST','LAYERS','STYLES','CRS','SRS','BBOX',
            'WIDTH','HEIGHT','FORMAT','TRANSPARENT','REFERENCE_TIME','TIME',
            'LAYER','SLD_VERSION','STYLE','EXCEPTIONS',
        ];
        $allowedFlip = array_flip($allowed);

        $params = [];
        foreach ($request->query() as $k => $v) {
            $K = strtoupper($k);
            if (isset($allowedFlip[$K]) && is_scalar($v)) {
                $params[$K] = (string) $v;
            }
        }

        $req = isset($params['REQUEST']) ? $params['REQUEST'] : '';
        if (!in_array($req, ['GetMap', 'GetLegendGraphic'], true)) {
            return response()->json(['error' => 'unsupported_request'], 400);
        }
        if (isset($params['FORMAT']) && $params['FORMAT'] !== 'image/png') {
            return response()->json(['error' => 'unsupported_format'], 400);
        }
        if ($req === 'GetMap' && empty($params['LAYERS'])) {
            return response()->json(['error' => 'missing_layers'], 400);
        }
        if ($req === 'GetLegendGraphic' && empty($params['LAYER'])) {
            return response()->json(['error' => 'missing_layer'], 400);
        }

        // Stable, sorted query string for cache-key derivation. mf2 receives
        // the same shape (leaflet sends mixed case; this normalises).
        ksort($params);
        $canonical = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $cacheKey = 'ipma:wms:' . sha1($canonical);

        if (env('APP_ENV') === 'production') {
            $cached = Redis::get($cacheKey);
            if ($cached !== null && $cached !== false) {
                $bytes = base64_decode($cached, true);
                if ($bytes !== false) {
                    return response($bytes, 200)
                        ->header('Content-Type', 'image/png')
                        ->header('Cache-Control', 'public, max-age=86400, immutable')
                        ->header('X-Cache', 'HIT');
                }
            }
        }

        // mf2 lowercases its param names but accepts uppercase. Forward what
        // we whitelisted — drops anything unexpected.
        $upstreamUrl = 'https://mf2.ipma.pt/services/?' . $canonical;

        try {
            $client = new GuzzleHttp\Client(['timeout' => 10]);
            $resp = $client->request('GET', $upstreamUrl, ['http_errors' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'upstream_unavailable'], 502);
        }

        if ($resp->getStatusCode() !== 200) {
            return response()->json(['error' => 'upstream_status', 'status' => $resp->getStatusCode()], 502);
        }

        $contentType = $resp->getHeaderLine('Content-Type');
        if (stripos($contentType, 'image/png') === false) {
            // mf2 returns XML ServiceExceptionReport on errors — do not cache.
            return response()->json(['error' => 'upstream_non_image'], 502);
        }

        $bytes = (string) $resp->getBody();

        if (env('APP_ENV') === 'production') {
            Redis::set($cacheKey, base64_encode($bytes), 'EX', 86400);
        }

        return response($bytes, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400, immutable')
            ->header('X-Cache', 'MISS');
    }

    /**
     * Single-point, single-layer AROME forecast value for the cursor hover
     * indicator on the map. Hits the same mf2 GetFeatureInfo backend as the
     * fire detail panel but only for one layer and one timestamp — short
     * enough to be called on debounced mousemove.
     */
    public function getIpmaValue(Request $request)
    {
        $lat  = (float) $request->query('lat');
        $lng  = (float) $request->query('lng');
        $kind = (string) $request->query('kind', '');

        $kinds = [
            'temperature'   => ['base' => 'arome.2m.temperature',       'unit' => '°C'],
            'wind'          => ['base' => 'arome.10m.windintensity',    'unit' => 'km/h'],
            'windDirection' => ['base' => 'arome.10m.windbarbs',        'unit' => 'km/h'],
            'precipitation' => ['base' => 'arome.0m.precipitation',     'unit' => 'mm'],
            'humidity'      => ['base' => 'arome.2m.relative_humidity', 'unit' => '%'],
        ];
        if (!isset($kinds[$kind])) {
            return response()->json(['error' => 'invalid_kind'], 400);
        }

        $region = $this->regionForPoint($lat, $lng);
        if ($region === null) {
            return response()->json([
                'value'  => null,
                'unit'   => $kinds[$kind]['unit'],
                'kind'   => $kind,
                'region' => null,
            ])->header('Cache-Control', 'public, max-age=300');
        }

        // Round to ~5 km grid so adjacent hover positions hit the same cache.
        $cacheLat = round($lat / 0.05) * 0.05;
        $cacheLng = round($lng / 0.05) * 0.05;
        $cacheKey = sprintf('ipma:value:%s:%s:%.2f:%.2f', $kind, $region, $cacheLat, $cacheLng);
        if (env('APP_ENV') === 'production') {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                return response($cached, 200)
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'public, max-age=300')
                    ->header('X-Cache', 'HIT');
            }
        }

        $refTime = $this->resolveAromeReferenceTime();
        if ($refTime === null) {
            return response()->json(['error' => 'reference_time_unavailable'], 503);
        }
        $refTimeIso = $refTime . ':00';

        $layer = $kinds[$kind]['base'] . '.' . $region;

        list($mx, $my) = $this->lngLatToWebMercator($lng, $lat);
        $halfSpanMx = 5000;
        $bbox = sprintf('%.0f,%.0f,%.0f,%.0f',
            $mx - $halfSpanMx, $my - $halfSpanMx,
            $mx + $halfSpanMx, $my + $halfSpanMx);

        // AROME timestamps are UTC. The app timezone is Europe/Lisbon, so
        // Carbon::now() without an explicit zone would give us local time and
        // we'd query the wrong forecast hour (especially in DST).
        $nowIso = Carbon::now('UTC')->setMinute(0)->setSecond(0)->format('Y-m-d\TH:i:s');

        $url = 'https://mf2.ipma.pt/services/'
            . '?service=WMS&version=1.3.0&request=GetFeatureInfo'
            . '&srs=EPSG:3857&info_format=application/json'
            . '&reference_time=' . urlencode($refTimeIso)
            . '&time=' . urlencode($nowIso)
            . '&width=256&height=256'
            . '&bbox=' . urlencode($bbox)
            . '&i=128&j=128'
            . '&layers=' . urlencode($layer)
            . '&query_layers=' . urlencode($layer);

        try {
            $client = new GuzzleHttp\Client(['timeout' => 6]);
            $resp = $client->request('GET', $url, ['http_errors' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'upstream_unavailable'], 502);
        }
        if ($resp->getStatusCode() !== 200) {
            return response()->json(['error' => 'upstream_status', 'status' => $resp->getStatusCode()], 502);
        }

        $data = json_decode((string) $resp->getBody(), true);
        $rawValue = null;
        if (is_array($data) && !empty($data['data']) && is_array($data['data'])) {
            $row = reset($data['data']);
            if (is_array($row) && array_key_exists($layer, $row)) {
                $rawValue = $row[$layer];
            }
        }

        $value = null;
        if ($kind === 'windDirection' && is_array($rawValue) && count($rawValue) === 2
            && is_numeric($rawValue[0]) && is_numeric($rawValue[1])) {
            // mf2 returns [u, v] in m/s. Speed in km/h, meteorological
            // direction (FROM where the wind blows, 0°=N, 90°=E).
            $u = (float) $rawValue[0];
            $v = (float) $rawValue[1];
            $speedKmh = sqrt($u * $u + $v * $v) * 3.6;
            $dirDeg = atan2(-$u, -$v) * 180.0 / M_PI;
            if ($dirDeg < 0) $dirDeg += 360.0;
            $value = [
                'speed'     => round($speedKmh, 1),
                'direction' => (int) round($dirDeg),
            ];
        } elseif (is_numeric($rawValue)) {
            $v = (float) $rawValue;
            if ($kind === 'wind') {
                $value = round($v * 3.6, 1);
            } elseif ($kind === 'humidity') {
                $value = (int) round($v);
            } else {
                $value = round($v, 1);
            }
        }

        $payload = [
            'value'  => $value,
            'unit'   => $kinds[$kind]['unit'],
            'kind'   => $kind,
            'region' => $region,
        ];
        $encoded = json_encode($payload);

        if (env('APP_ENV') === 'production') {
            Redis::set($cacheKey, $encoded, 'EX', 600);
        }

        return response($encoded, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=300')
            ->header('X-Cache', 'MISS');
    }

    private function regionForPoint($lat, $lng)
    {
        if ($lng >= -12.8 && $lng <= -1.2 && $lat >= 34.0 && $lat <= 44.8) {
            return 'continent';
        }
        if ($lng >= -19.0 && $lng <= -14.8 && $lat >= 30.9 && $lat <= 34.8) {
            return 'madeira';
        }
        if ($lng >= -33.1 && $lng <= -24.1 && $lat >= 35.7 && $lat <= 40.9) {
            return 'azores';
        }
        return null;
    }

    private function lngLatToWebMercator($lng, $lat)
    {
        $earth = 6378137.0;
        $x = $lng * M_PI / 180.0 * $earth;
        $y = log(tan((90.0 + $lat) * M_PI / 360.0)) * $earth;
        return [$x, $y];
    }

    private function fetchGfi(GuzzleHttp\Client $client, array $layers, array $times, $refTimeIso, $bbox)
    {
        $url = 'https://mf2.ipma.pt/services/'
            . '?service=WMS&version=1.3.0&request=GetFeatureInfo'
            . '&srs=EPSG:3857&info_format=application/json'
            . '&reference_time=' . urlencode($refTimeIso)
            . '&time=' . urlencode(implode(',', $times))
            . '&width=256&height=256'
            . '&bbox=' . urlencode($bbox)
            . '&i=128&j=128'
            . '&query_layers=' . urlencode(implode(',', $layers));

        try {
            $resp = $client->request('GET', $url, ['http_errors' => false]);
            if ($resp->getStatusCode() !== 200) {
                return null;
            }
            $body = (string) $resp->getBody();
            return json_decode($body, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function normaliseAromeSeries($raw, $region)
    {
        if (!is_array($raw) || empty($raw['data']) || !is_array($raw['data'])) {
            return [];
        }

        $map = [
            'arome.2m.temperature.' . $region       => 'temperature',
            'arome.2m.relative_humidity.' . $region => 'humidity',
            'arome.10m.windintensity.' . $region    => 'wind',
            'arome.10m.gustintensity.' . $region    => 'gust',
            'arome.2m.pressure.' . $region          => 'pressure',
            'arome.0m.precipitation.' . $region     => 'precipitation',
        ];
        $barbsKey = 'arome.10m.windbarbs.' . $region;

        $rows = [];
        foreach ($raw['data'] as $row) {
            if (empty($row['datetime'])) continue;
            $out = ['datetime' => $row['datetime']];
            foreach ($map as $src => $dst) {
                $out[$dst] = isset($row[$src]) ? $row[$src] : null;
            }
            // The windbarbs layer returns [u, v] in m/s — east and north
            // wind components. We expose them so the frontend can derive
            // direction (and draw arrows on the wind chart).
            $barbs = isset($row[$barbsKey]) ? $row[$barbsKey] : null;
            if (is_array($barbs) && count($barbs) === 2 && is_numeric($barbs[0]) && is_numeric($barbs[1])) {
                $out['windU'] = (float) $barbs[0];
                $out['windV'] = (float) $barbs[1];
            } else {
                $out['windU'] = null;
                $out['windV'] = null;
            }
            $rows[] = $out;
        }

        usort($rows, function ($a, $b) {
            return strcmp($a['datetime'], $b['datetime']);
        });
        return $rows;
    }

    private function normaliseSatSeries($raw, $region)
    {
        if (!is_array($raw)) return [];

        $vars = ['fwi','isi','bui','dc','dmc','ffmc','p2000','p2000a','rcm'];
        $out = [];
        foreach ($vars as $v) {
            if (empty($raw[$v]) || !is_array($raw[$v])) {
                $out[$v] = [];
                continue;
            }
            // The IPMA value lives under a layer-name key; pick whichever key
            // is not "datetime" for resilience to renames.
            $rows = [];
            foreach ($raw[$v] as $row) {
                if (empty($row['datetime'])) continue;
                $value = null;
                foreach ($row as $k => $val) {
                    if ($k !== 'datetime') { $value = $val; break; }
                }
                $rows[] = ['datetime' => $row['datetime'], 'value' => $value];
            }
            usort($rows, function ($a, $b) {
                return strcmp($a['datetime'], $b['datetime']);
            });
            $out[$v] = $rows;
        }

        return $out;
    }

}

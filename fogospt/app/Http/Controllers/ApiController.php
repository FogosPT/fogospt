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

        if(env('APP_ENV') === 'production'){
            $exists = Redis::get('modis');
            if($exists){
                $response = json_decode($exists);
            } else {
                $response = HotSpots::getModis();


                if(!empty($response)){
                    Redis::set('modis', json_encode($response),'EX', 1080);
                }
            }
        } else {
            $response = HotSpots::getModis();
        }

        return \Response::json($response);
    }

    public function getVIIRS()
    {
        if(env('APP_ENV') === 'production'){
            $exists = Redis::get('VIIRS');
            if($exists){
                $response = json_decode($exists);
            } else {
                $response = HotSpots::getVIIRS();


                if(!empty($response)){
                    Redis::set('VIIRS', json_encode($response),'EX', 1080);
                }
            }
        } else {
            $response = HotSpots::getVIIRS();
        }

        return \Response::json($response);
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
        $cacheKey = 'ipma:ref-time';
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
                // try next
            }

            $candidate->subHours(12);
        }

        if ($found === null) {
            return response()->json(['error' => 'unavailable'], 503);
        }

        $payload = json_encode(['reference_time' => $found]);

        if (env('APP_ENV') === 'production') {
            Redis::set($cacheKey, $payload, 'EX', 1800);
        }

        return response($payload, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'public, max-age=1800')
            ->header('X-Cache', 'MISS');
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

}

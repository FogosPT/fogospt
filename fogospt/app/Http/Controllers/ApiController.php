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

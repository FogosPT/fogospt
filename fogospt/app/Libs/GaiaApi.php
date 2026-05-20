<?php

namespace App\Libs;

use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class GaiaApi
{
    private static function baseUrl()
    {
        return rtrim(env('GAIA_API_BASE', 'https://wildfires.gaia-project.cloud/api'), '/');
    }

    private static function getClient()
    {
        $apiKey = env('GAIA_API_KEY', '');

        $headers = [
            'Accept' => 'application/json',
        ];

        if ($apiKey !== '') {
            $headers['X-API-Key'] = $apiKey;
        }

        return new GuzzleHttp\Client([
            'headers' => $headers,
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);
    }

    private static function request($path, array $query = [], $cacheKey = null, $ttl = 300)
    {
        if (env('GAIA_API_KEY', '') === '') {
            Log::warning('GAIA_API_KEY not configured; returning empty payload', ['path' => $path]);
            return ['error' => 'gaia_api_key_missing'];
        }

        $useCache = $cacheKey !== null && env('APP_ENV') === 'production';

        if ($useCache) {
            $cached = Redis::get($cacheKey);
            if ($cached) {
                return json_decode($cached, true);
            }
        }

        $url = self::baseUrl() . $path;

        try {
            $response = self::getClient()->request('GET', $url, ['query' => $query]);
        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $result = json_decode($response->getBody()->getContents(), true);

        if ($useCache && $result !== null) {
            Redis::set($cacheKey, json_encode($result), 'EX', $ttl);
        }

        return $result;
    }

    public static function getEvents(array $filters = [])
    {
        $key = 'gaia:events:' . md5(json_encode($filters));
        $ttl = !empty($filters['is_active']) ? 300 : 1800;
        return self::request('/events', $filters, $key, $ttl);
    }

    public static function getEvent($id)
    {
        return self::request('/events/' . (int) $id, [], 'gaia:event:' . (int) $id, 600);
    }

    public static function getEventAcquisitions($id, array $filters = [])
    {
        $key = 'gaia:event:' . (int) $id . ':acq:' . md5(json_encode($filters));
        return self::request('/events/' . (int) $id . '/acquisitions', $filters, $key, 1800);
    }

    public static function getEventTimeline($id, array $filters = [])
    {
        $key = 'gaia:event:' . (int) $id . ':timeline:' . md5(json_encode($filters));
        return self::request('/events/' . (int) $id . '/timeline', $filters, $key, 600);
    }

    public static function getDetections(array $filters = [])
    {
        $key = 'gaia:detections:' . md5(json_encode($filters));
        return self::request('/detections', $filters, $key, 300);
    }

    public static function getDelineations(array $filters = [])
    {
        $key = 'gaia:delineations:' . md5(json_encode($filters));
        return self::request('/delineations', $filters, $key, 3600);
    }

    public static function getSources()
    {
        return self::request('/sources', [], 'gaia:sources', 86400);
    }
}

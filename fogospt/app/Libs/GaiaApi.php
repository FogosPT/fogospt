<?php

namespace App\Libs;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class GaiaApi
{
    private static function baseUrl()
    {
        return rtrim(config('services.gaia.base', 'https://wildfires.gaia-project.cloud/api'), '/');
    }

    private static function getClient()
    {
        $apiKey = (string) config('services.gaia.key', '');

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
        if ((string) config('services.gaia.key', '') === '') {
            Log::warning('GAIA_API_KEY not configured; returning empty payload', ['path' => $path]);
            return ['error' => 'gaia_api_key_missing'];
        }

        $useCache = $cacheKey !== null && app()->environment('production');

        if ($useCache) {
            $cached = Redis::get($cacheKey);
            $decoded = $cached ? json_decode($cached, true) : null;
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $url = self::baseUrl() . $path;

        try {
            $response = self::getClient()->request('GET', $url, ['query' => $query, 'http_errors' => false]);
        } catch (GuzzleException $e) {
            Log::warning('gaia api transport error', ['path' => $path, 'error' => $e->getMessage()]);
            return ['error' => 'upstream_unavailable'];
        }

        if ($response->getStatusCode() !== 200) {
            Log::warning('gaia api bad status', ['path' => $path, 'status' => $response->getStatusCode()]);
            return ['error' => 'upstream_status_' . $response->getStatusCode()];
        }

        $result = json_decode((string) $response->getBody(), true);
        if (!is_array($result)) {
            return ['error' => 'upstream_invalid_json'];
        }

        if ($useCache) {
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
}

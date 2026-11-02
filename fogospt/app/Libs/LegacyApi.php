<?php

namespace App\Libs;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Single integration point for source.fogos.pt.
 *
 * Every method returns either the decoded JSON array or ['error' => <code>].
 * Error codes are generic on purpose — upstream URLs/bodies are logged, never
 * returned, so they can't leak into responses or cached pages.
 */
class LegacyApi
{
    // Short in-app cache for per-fire lookups. One marker click fans out into
    // five /views/* card requests, each needing the same fire payload; without
    // this every click costs ~7 upstream round-trips.
    private const FIRE_TTL = 30;
    private const FIRE_EXTRA_TTL = 60;

    private static function getClient(): GuzzleHttp\Client
    {
        // Hard timeouts are critical: Guzzle's defaults are 0 (= wait forever).
        // Without them, a stalled source.fogos.pt pins every FPM worker that
        // touches LegacyApi, the pool drains, and every other request — even
        // routes that don't call the API at all, like `/` — queues behind the
        // hung workers, producing 12-17s TTFB on the homepage.
        return new GuzzleHttp\Client([
            'base_uri' => (string) config('services.fogos.url', 'https://source.fogos.pt'),
            'headers' => [
                'FPTS' => (string) config('services.fogos.fpts', ''),
            ],
            'connect_timeout' => 3,
            'timeout'         => 6,
            'http_errors'     => false,
        ]);
    }

    // Third-party hosts must never receive the FPTS token.
    private static function getThirdPartyClient(): GuzzleHttp\Client
    {
        return new GuzzleHttp\Client([
            'headers' => ['User-Agent' => 'fogos.pt'],
            'connect_timeout' => 3,
            'timeout'         => 6,
            'http_errors'     => false,
        ]);
    }

    private static function request(string $path, array $query = [], ?GuzzleHttp\Client $client = null): array
    {
        $client = $client ?? self::getClient();

        try {
            $response = $client->request('GET', $path, $query ? ['query' => $query] : []);
        } catch (GuzzleException $e) {
            // ConnectException (timeouts, DNS) is not a RequestException in
            // Guzzle 7 — catching the interface covers every transport failure.
            Log::warning('legacy api transport error', ['path' => $path, 'error' => $e->getMessage()]);
            return ['error' => 'upstream_unavailable'];
        }

        if ($response->getStatusCode() !== 200) {
            // 404 is the normal answer for finished/unknown fires (crawlers
            // follow old links) — not worth a warning on every hit.
            $level = $response->getStatusCode() === 404 ? 'debug' : 'warning';
            Log::{$level}('legacy api bad status', ['path' => $path, 'status' => $response->getStatusCode()]);
            return ['error' => 'upstream_status_' . $response->getStatusCode()];
        }

        $result = json_decode((string) $response->getBody(), true);
        if (!is_array($result)) {
            Log::warning('legacy api invalid json', ['path' => $path]);
            return ['error' => 'upstream_invalid_json'];
        }

        return $result;
    }

    // Cache successful responses only, so a transient upstream failure never
    // starves callers for the full TTL.
    private static function cached(string $key, int $ttl, callable $fetch): array
    {
        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached;
        }

        $result = $fetch();
        if (!isset($result['error'])) {
            Cache::put($key, $result, $ttl);
        }

        return $result;
    }

    public static function getFires(): array
    {
        return self::request('/new/fires');
    }

    public static function getWarnings(): array
    {
        return self::request('/v1/warnings');
    }

    public static function getWarningsMadeira(): array
    {
        return self::request('/v1/madeira/warnings');
    }

    public static function getNow(): array
    {
        return self::request('/v1/now');
    }

    public static function getFire($id): array
    {
        return self::cached('legacy:fire:' . $id, self::FIRE_TTL, fn () => self::request('/fires', ['id' => $id]));
    }

    public static function getMadeiraFire($id): array
    {
        return self::cached('legacy:madeira-fire:' . $id, self::FIRE_TTL, fn () => self::request('/madeira/fires', ['id' => $id]));
    }

    public static function getRiskByFire($id): array
    {
        return self::cached('legacy:fire-risk:' . $id, self::FIRE_EXTRA_TTL, fn () => self::request('/fires/danger', ['id' => $id]));
    }

    public static function getStatusByFire($id): array
    {
        return self::cached('legacy:fire-status:' . $id, self::FIRE_EXTRA_TTL, fn () => self::request('/fires/status', ['id' => $id]));
    }

    public static function getStatusByFireMadeira($id): array
    {
        return self::cached('legacy:madeira-fire-status:' . $id, self::FIRE_EXTRA_TTL, fn () => self::request('/madeira/fires/status', ['id' => $id]));
    }

    // Redis-cached wrappers used by the OG share-card pipeline. Every
    // crawler hit on /og/fogo/{id}.png needs the fire payload and its
    // status history; without a cache a burst of FB + WhatsApp + X hitting
    // the same fresh incident would fan out to N upstream calls. 10 min is
    // fine for social crawlers, which themselves cache the PNG for hours.
    // Failures are never cached, so a bad response one minute doesn't
    // starve callers for the full TTL.
    public static function getFireCached($id, $ttl = 600): array
    {
        return self::redisCached('og:fire:' . $id, $ttl, fn () => self::request('/fires', ['id' => $id]));
    }

    public static function getStatusByFireCached($id, $ttl = 600): array
    {
        return self::redisCached('og:fire-status:' . $id, $ttl, fn () => self::request('/fires/status', ['id' => $id]));
    }

    private static function redisCached(string $key, int $ttl, callable $fetch): array
    {
        $cached = Redis::get($key);
        if ($cached !== null) {
            $decoded = json_decode($cached, true);
            if (is_array($decoded)) return $decoded;
        }
        $result = $fetch();
        if (isset($result['data']) && !isset($result['error'])) {
            Redis::set($key, json_encode($result), 'EX', $ttl);
        }
        return $result;
    }

    public static function getMobileContributors(): array
    {
        return self::cached('mobile:contributors', 108000, function () {
            $client = self::getThirdPartyClient();
            $result = self::request('https://api.github.com/repos/FogosPT/fogosmobile/contributors', [], $client);
            if (isset($result['error'])) {
                return $result;
            }

            foreach ($result as &$r) {
                // Only follow URLs that point back at the GitHub users API —
                // the list comes from a third-party response.
                if (!is_array($r) || !is_string($r['url'] ?? null) || !str_starts_with($r['url'], 'https://api.github.com/users/')) {
                    continue;
                }
                $data = self::request($r['url'], [], $client);
                if (isset($data['error'])) {
                    return $data;
                }
                $r = array_merge($r, $data);
            }
            unset($r);

            return $result;
        });
    }
}

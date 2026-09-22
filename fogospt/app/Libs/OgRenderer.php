<?php

namespace App\Libs;

use GuzzleHttp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Thin client for the og-renderer sidecar. Encapsulates the HMAC token, the
// disk cache path, and the "call the sidecar and save the PNG" flow so the
// controller can stay a one-liner.
class OgRenderer
{
    // Keep the disk cache path shape stable so a small cronjob can vacuum
    // old files by mtime later. `$hash` is already narrowed to 8 chars by
    // the caller — that's enough entropy for a per-fire bucket.
    public static function cachePath(string $id, string $hash): string
    {
        return storage_path("app/og/fogo-{$id}-{$hash}.png");
    }

    // Force fresh scrape of FB/WhatsApp caches whenever the underlying
    // status/meios change. Bucket to 20 min so we don't churn the URL for
    // no reason — matches the CDN s-maxage on the PNG response.
    public static function hash(array $fire): string
    {
        $bucket = (int) floor(time() / 1200);
        $key = ($fire['status']  ?? '')
             . '|' . (int) ($fire['man']     ?? 0)
             . '|' . (int) ($fire['terrain'] ?? 0)
             . '|' . (int) ($fire['aerial']  ?? 0)
             . '|' . $bucket;
        return substr(md5($key), 0, 8);
    }

    // Symmetric token: OgInternal middleware recomputes the same HMAC on
    // the way in and compares. APP_KEY is the shared secret; if it rotates,
    // in-flight sidecar calls fail once, then recover on the next miss.
    public static function token(string $id): string
    {
        return hash_hmac('sha256', $id, (string) config('app.key'));
    }

    // Returns the absolute path to a PNG on disk (either cache hit or
    // freshly rendered), or null if the sidecar was unreachable / errored.
    // The caller is expected to fall back to the static PNG when null.
    public static function render(string $id, string $hash, string $renderUrl): ?string
    {
        $path = self::cachePath($id, $hash);

        if (is_file($path) && filesize($path) > 0) {
            return $path;
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $endpoint = rtrim((string) config('services.og_renderer.url', 'http://og-renderer:3000'), '/') . '/render';

        try {
            $client = new GuzzleHttp\Client([
                'connect_timeout' => 2,
                'timeout'         => 12,
            ]);
            $resp = $client->request('POST', $endpoint, [
                'http_errors' => false,
                'json' => [
                    'url'    => $renderUrl,
                    'width'  => 1200,
                    'height' => 630,
                    'token'  => self::token($id),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('og-renderer unreachable', ['id' => $id, 'err' => $e->getMessage()]);
            return null;
        }

        if ($resp->getStatusCode() !== 200) {
            Log::warning('og-renderer failed', [
                'id'     => $id,
                'status' => $resp->getStatusCode(),
                'body'   => (string) $resp->getBody(),
            ]);
            return null;
        }

        $bytes = (string) $resp->getBody();
        if ($bytes === '' || strncmp($bytes, "\x89PNG", 4) !== 0) {
            Log::warning('og-renderer returned non-PNG payload', ['id' => $id]);
            return null;
        }

        // Write to a temp file and rename atomically so a concurrent hit
        // never reads a half-written PNG.
        $tmp = $path . '.' . bin2hex(random_bytes(4)) . '.tmp';
        if (file_put_contents($tmp, $bytes) === false) {
            return null;
        }
        rename($tmp, $path);
        return $path;
    }
}

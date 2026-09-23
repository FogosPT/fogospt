<?php

namespace App\Libs;

use GuzzleHttp;
use Illuminate\Support\Facades\Log;

// Thin client for the og-renderer sidecar. The controller pre-renders the
// Blade to HTML and hands it here — the sidecar then does setContent +
// screenshot, no navigation, no callback into PHP. That decoupling is what
// keeps a crawler storm from starving the PHP-FPM pool.
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
    // statusHistory count catches the case where an incident re-enters
    // the same top-level status (e.g. Vigilância → Em Curso → Vigilância)
    // and the timeline in the card grew without the status changing.
    public static function hash(array $fire): string
    {
        $bucket = (int) floor(time() / 1200);
        $historyN = 0;
        if (isset($fire['statusHistory']) && is_array($fire['statusHistory'])) {
            $historyN = count($fire['statusHistory']);
        }
        $key = ($fire['status']  ?? '')
             . '|' . (int) ($fire['man']     ?? 0)
             . '|' . (int) ($fire['terrain'] ?? 0)
             . '|' . (int) ($fire['aerial']  ?? 0)
             . '|' . $historyN
             . '|' . $bucket;
        return substr(md5($key), 0, 8);
    }

    // Returns the absolute path to a PNG on disk (either cache hit or
    // freshly rendered), or null if the sidecar was unreachable / errored.
    // The caller is expected to fall back to the static PNG when null.
    public static function render(string $id, string $hash, string $html): ?string
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
            // Total request timeout must exceed the sidecar's own hard
            // render cap (15s, see HARD_RENDER_CAP_MS in server.js and
            // docker-compose.yml) — otherwise curl aborts mid-render and
            // we never see the sidecar's 500 body, poisoning the diagnosis.
            $client = new GuzzleHttp\Client([
                'connect_timeout' => 2,
                'timeout'         => 20,
            ]);
            $resp = $client->request('POST', $endpoint, [
                'http_errors' => false,
                'json' => [
                    'html'   => $html,
                    'width'  => 1200,
                    'height' => 630,
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

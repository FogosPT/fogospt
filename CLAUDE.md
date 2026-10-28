# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Fogos.pt is a Portuguese forest fire monitoring web application built with Laravel 10 (PHP 8.3, `php:8.3.36-fpm` in docker-compose). It aggregates real-time fire data from an external backend (`source.fogos.pt`) and displays it on an interactive map with statistics, warnings, and fire detail cards.

## Development Setup

### Requirements
- Docker Desktop
- Node.js 20+ (CSS is built with the `sass` CLI + autoprefixer — no webpack)
- PHP 8.3 and Composer 2 (or run them via Docker, e.g. `docker run --rm -v "$PWD:/app" -w /app php:8.3.36-fpm php vendor/bin/phpunit`)

### Starting the stack
```bash
docker-compose up          # PHP-FPM (127.0.0.1:9001, for the host nginx), Redis, og-renderer sidecar
```

### Installing dependencies
```bash
cd fogospt
composer install           # or: docker run --rm -it -v "$(pwd):/app" composer:2 install
npm install
```

### Frontend assets
```bash
npm run build      # Minified public/css/app.css (commit it — CI checks it's up to date)
npm run watch      # Rebuild on change
npm run lint:js    # Syntax-check every public/js script
npm run test:smoke # Playwright smoke test against SMOKE_URL (default http://127.0.0.1:8091)
```

### Running tests
```bash
cd fogospt
php vendor/bin/phpunit                        # All tests
php vendor/bin/phpunit --filter TestName      # Single test
php vendor/bin/phpunit tests/Unit/            # Unit suite only
php vendor/bin/phpunit tests/Feature/         # Feature suite only
```

### Deploy
`./scripts/deploy.sh` on the production host: pulls master, installs PHP deps without dev packages, rebuilds/recreates containers, warms the Laravel caches, restarts FPM and runs smoke checks. CI (`.github/workflows/ci.yml`) runs PHPUnit, `composer audit`, the CSS build, a JS syntax check and the Playwright smoke test, and pushes the production image to `ghcr.io/fogospt/fogospt` on master (see `docker-compose.image.yml`).

## Architecture

### Data flow
The app has **no local fire data and no database** — everything is fetched from the external API at `source.fogos.pt` via `app/Libs/LegacyApi.php`. All requests go through `LegacyApi::request()`, which sends the `FPTS` token (config `services.fogos.fpts`) only to source.fogos.pt and always returns an array — either decoded JSON or `['error' => <code>]`, never an exception. Per-fire lookups are cached for 30–60 s. The `web` middleware group is intentionally stateless (no session/cookies/CSRF) so pages can be cached publicly.

### Key components

- **`app/Libs/LegacyApi.php`** — Static HTTP client wrapping all calls to `source.fogos.pt` and external APIs. This is the single integration point for fire data.
- **`app/Http/Controllers/GenericController.php`** — Handles most page renders (home, warnings, stats, about, etc.)
- **`app/Http/Controllers/FireController.php`** — Fire detail pages and AJAX card endpoints (`/views/risk/{id}`, `/views/status/{id}`, etc.)
- **`app/Http/Controllers/ApiController.php`** — Public API endpoints under `/v1/` (MODIS, VIIRS, mobile contributors)
- **`app/Libs/HotSpots.php`** — Geospatial processing using PHPGeo
- **`app/Http/Middleware/SetLanguage.php`** — PT/EN language switching

### Routes pattern
- `/` `/madeira` — main map views (mainland and Madeira island)
- `/fogo/{id}` — fire detail page; `/fogo/{id}/detalhe` — detail sub-page
- `/views/{card}/{id}` — AJAX-loaded card fragments (risk, status, meteo, extra, twitter, shares)
- `/v1/modis`, `/v1/viirs`, `/v1/mobile-contributors` — public API endpoints
- Language switching via `/change-language/{lang}`

### Frontend
Hand-written jQuery/Leaflet scripts in `public/js/` (served as-is, cache-busted with `?v={{ filemtime(...) }}`). `npm run build` compiles `resources/assets/sass/app.scss` → `public/css/app.css` (committed, cache-busted with filemtime). Upstream/third-party text must be escaped before it reaches `.html()`/`innerHTML`/popups — use `escapeFireText()` (main*.js) or the module-local `esc()` helpers.

### Environment variables (key ones)
- `FPTS` — API token for `source.fogos.pt`
- `REDIS_HOST` / `CACHE_DRIVER=redis` — Redis connection
- `APP_ENV` — FCM topics get a `dev-` prefix outside `production`
- Never call `env()` outside `config/` — it returns null once `config:cache` runs

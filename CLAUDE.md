# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Fogos.pt is a Portuguese forest fire monitoring web application built with Laravel 7 (PHP). It aggregates real-time fire data from an external backend (`source.fogos.pt`) and displays it on an interactive map with statistics, warnings, and fire detail cards.

## Development Setup

### Requirements
- Docker Desktop
- Node.js 14 (npm 6) — use `nvm use 14` or `fnm use 14`
- Python 2.7 (for node-gyp) — use `pyenv shell 2.7`
- PHP 7.2+ and Composer 1 (or use Docker for Composer, see below)

### Starting the stack
```bash
docker-compose up          # Starts PHP-FPM (port 9001), Nginx (port 8093), Redis
```

### Installing dependencies
```bash
cd fogospt
composer install           # or: docker run --rm -it -v "$(pwd):/app" composer:1.10.19 install
npm install
```

### Frontend assets
```bash
npm run dev        # Development build
npm run watch      # Watch mode
npm run production # Minified production build
```

### Running tests
```bash
cd fogospt
php vendor/bin/phpunit                        # All tests
php vendor/bin/phpunit --filter TestName      # Single test
php vendor/bin/phpunit tests/Unit/            # Unit suite only
php vendor/bin/phpunit tests/Feature/         # Feature suite only
```

### Database migrations
```bash
php artisan migrate
php artisan db:seed
```

## Architecture

### Data flow
The app has **no local fire data** — everything is fetched from the external API at `source.fogos.pt` via `app/Libs/LegacyApi.php`. This class makes authenticated HTTP requests using a `FPTS` header token (env var). Weather data is fetched from OpenWeatherMap and cached in Redis for 3 hours.

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
Vue.js 2 components compiled via Laravel Mix (Webpack). Source in `resources/js/` and `resources/sass/`. Compiled output goes to `public/`.

### Environment variables (key ones)
- `FPTS` — API token for `source.fogos.pt`
- `OPENWEATHER_API` — OpenWeatherMap API key
- `MAPBOX_KEY` — Mapbox token for maps
- `REDIS_HOST` / `CACHE_DRIVER=redis` — Redis connection
- `APP_ENV` — Some features (Redis weather caching, mobile contributors) only run in `production`

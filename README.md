# Fogos.pt

```
///////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////,*/////////////////////////////////
////////////////////////////////////////////*  *///////////////////////////////
////////////////////////////////////////////*   ,//////////////////////////////
////////////////////////////////////////////*    ,/////////////////////////////
////////////////////////////////////////////.     *////////////////////////////
///////////////////////////////////////////.      .////////////////////////////
/////////////////////////////////////////*         ////////////////////////////
///////////////////////////////////////*           ////////////////////////////
////////////////////////////////////*.             ////////////////////////////
/////////////////////////////////,                .////////////////////////////
//////////////////////////////*                   *//////*/////////////////////
////////////////////////////*                    ./////. */////////////////////
//////////////////////////*.                     *///,   */////////////////////
/////////////////////////*                      .///     */////////////////////
////////////////////////*                       *//.     ./////////////////////
///////////////////////*                        */*       ,////////////////////
///////////////////////.                        */*        *///////////////////
//////////////////////*                         ,/*         ,//////////////////
///////////////////* **                          */.         ./////////////////
/////////////////*   /*                           ,*           ////////////////
////////////////.   ./*                                         *//////////////
//////////////*      //*                                         */////////////
/////////////,        ,,.                                         *////////////
////////////,                                                      *///////////
///////////,                                                       .///////////
//////////*                                                         *//////////
//////////,                           ,.                            ,//////////
//////////,                          */*                            .//////////
//////////*                         *////.                          .//////////
///////////                        ,//////*                         *//////////
///////////*                       //////////,                      ///////////
////////////*       ,             ,////////////*                   *///////////
//////////////,     ,*            *//////////////.                *////////////
////////////////.    */,          ////////////////,             .//////////////
//////////////////*   ,////*,     /////////////////.          .*///////////////
/////////////////////*  *//////.  *////////////////*        ,//////////////////
////////////////////////*,*//////,./////////////////.    ,*////////////////////
///////////////////////////////////*////////////////,,*////////////////////////
```

## Run & Go

### Requirements

1. [Docker](https://www.docker.com/products/docker-desktop/)
2. [Node.js](https://nodejs.org/) 20+ (only to rebuild the CSS)
3. PHP 8.3 and [Composer](https://getcomposer.org/) 2 — or use the Docker images below

### Commands

```bash
git clone https://github.com/FogosPT/fogospt.git
cd fogospt
cp .env.example .env                       # docker-compose vars (UID/GID, REDIS_PASSWORD)
cp fogospt/.env.example fogospt/.env       # Laravel app vars (FPTS, Firebase, ...)
docker compose up -d                       # PHP-FPM on 127.0.0.1:9001, Redis, og-renderer

cd fogospt
docker run --rm -v "$PWD:/app" -w /app composer:2 install
docker run --rm -v "$PWD:/app" -w /app php:8.3.36-fpm php artisan key:generate
npm ci && npm run build                    # only when changing resources/assets/sass
```

PHP-FPM has no web server in front of it in this compose file — point a local
nginx at `127.0.0.1:9001`, or for quick local work run
`docker run --rm -p 8091:8000 -v "$PWD:/app" -w /app php:8.3.36-fpm php artisan serve --host=0.0.0.0`.

### Tests

```bash
docker run --rm -v "$PWD:/app" -w /app php:8.3.36-fpm php vendor/bin/phpunit
npm run lint:js
SMOKE_URL=http://127.0.0.1:8091 npm run test:smoke   # needs `npx playwright install chromium`
```

## Deploying to production

On the production host, from the repo root:

```bash
./scripts/deploy.sh
```

It fast-forwards to `origin/master`, installs PHP dependencies without dev
packages, rebuilds/recreates the containers, clears and re-warms the Laravel
config/route/view caches, restarts PHP-FPM (fresh OPcache) and runs smoke
checks against the live site. Purge the CDN cache afterwards if HTML or CSS
changed.

CI pushes a production image to `ghcr.io/fogospt/fogospt` on every push to
master; `docker-compose.image.yml` runs that image instead of the bind mount.

### Cloudflare cache rule for HTML

The app sends `Cache-Control: public, s-maxage=…` on cacheable routes (`/{pt|en|es}`,
`/{locale}/sobre`, `/{locale}/lista`, `/{locale}/fogo/*`, etc.) but Cloudflare
ignores HTML by default and the dashboard still shows `cf-cache-status: DYNAMIC`.
Create a Cache Rule:

- **When**: `(http.host eq "fogos.pt") and (http.request.method eq "GET") and (starts_with(http.request.uri.path, "/pt") or starts_with(http.request.uri.path, "/en") or starts_with(http.request.uri.path, "/es"))`
- **Then**: *Eligible for cache* + *Edge TTL = Use cache-control header from origin*

Without this rule the `s-maxage` directive is wasted — every visit still hits
the origin.

## Slack

https://communityinviter.com/apps/fogospt/fogos-pt

## License

Copyright 2018

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

# Fogos.pt (Next.js)

Este repositório foi migrado para **Next.js (App Router)**, com foco em performance e SEO.

## Incluído nesta fase

- Estrutura base Next.js 16 + React 19
- Página inicial com mapa Leaflet e ocorrências ativas (equivalente funcional ao `main.js` legado)
- Rotas públicas principais migradas para `app/`
- Páginas `/lista` (cards) e `/tabela` (tabular) ligadas ao feed de ocorrências
- Painel `/estatisticas` implementado com dados equivalentes ao legado (`v1/now/data`, semana, 8h, distritos, área ardida e motivos)
- Página `/notificacoes` integrada com Firebase Messaging (subscrição por tópicos/distritos)
- **SEO por página** com `metadata`, Open Graph, Twitter Card e canonical
- `robots.ts` e `sitemap.ts`
- Integração de **estado do tempo** via API pública Open-Meteo
- Bloco de **procura no Twitter/X** com link de pesquisa e embed da conta `@fogospt`

## Requisitos

- Node.js 18+
- npm 9+

## Execução local

```bash
npm install
npm run dev
```

Abrir: `http://localhost:3000`

## Scripts

- `npm run dev` — ambiente de desenvolvimento
- `npm run lint` — linting do Next.js
- `npm run build` — build de produção
- `npm run start` — servidor da build de produção

## Código legado

O código Laravel/PHP original permanece na pasta `fogospt/` para referência e migração gradual de regras de negócio.


## Deploy em Cloudflare Workers

Este projeto está preparado para deploy em Workers com **Wrangler 4** usando OpenNext. O `wrangler deploy` executa o build automaticamente via `wrangler.toml`.

```bash
npm install
npx wrangler deploy
```

### Variáveis/segredos

Defina os segredos necessários no Worker (ex.: `FIREBASE_TOKEN`):

```bash
npx wrangler secret put FIREBASE_TOKEN
```
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
In that mode the image's `public/` is copied to `runtime/public` on every
`up`, so point the host nginx `root` there — otherwise it would keep serving
the JS/CSS of whatever is checked out on disk.

### Cloudflare cache rule for HTML

The app sends `Cache-Control: public, s-maxage=…` on cacheable routes (`/{pt|en|es}`,
`/{locale}/sobre`, `/{locale}/lista`, `/{locale}/fogo/*`, etc.) but Cloudflare
ignores HTML by default and the dashboard still shows `cf-cache-status: DYNAMIC`.
Create a Cache Rule:

- **When**: `(http.host eq "fogos.pt") and (http.request.method eq "GET") and (starts_with(http.request.uri.path, "/pt") or starts_with(http.request.uri.path, "/en") or starts_with(http.request.uri.path, "/es"))`
- **Then**: *Eligible for cache* + *Edge TTL = Use cache-control header from origin*

Without this rule the `s-maxage` directive is wasted — every visit still hits
the origin.


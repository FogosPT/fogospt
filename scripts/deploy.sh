#!/usr/bin/env bash
# Deploy Fogos.pt on the production host (bind-mount setup from
# docker-compose.yml). Run from the repo root:  ./scripts/deploy.sh
#
# Idempotent: safe to re-run. Stops on the first failing step.
set -euo pipefail

cd "$(dirname "$0")/.."
SITE="${SITE_URL:-https://fogos.pt}"
COMPOSE="docker compose"

step() { printf '\n\033[1;33m▶ %s\033[0m\n' "$*"; }

step "Pull master"
git fetch --quiet origin
git merge --ff-only origin/master
git log --oneline -1

step "PHP dependencies (no dev packages)"
docker run --rm -v "$PWD/fogospt:/app" -w /app \
    --user "$(id -u):$(id -g)" -e COMPOSER_HOME=/tmp/composer \
    composer:2 install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --no-progress

step "Containers (rebuilds og-renderer only if its Dockerfile changed)"
$COMPOSE build og-renderer
$COMPOSE up -d --remove-orphans

step "Laravel caches"
docker exec fogospt php artisan optimize:clear
docker exec fogospt php artisan config:cache
docker exec fogospt php artisan route:cache
docker exec fogospt php artisan view:cache

step "Reload PHP-FPM (fresh OPcache)"
$COMPOSE restart fogospt

step "Smoke checks against $SITE"
fail=0
check() {
    local path="$1" want="$2" got
    got=$(curl -s -o /dev/null -w '%{http_code}' "$SITE$path")
    if [ "$got" = "$want" ]; then echo "  ok   $got $path"; else echo "  FAIL $got (want $want) $path"; fail=1; fi
}
check /pt 200
check /pt/sobre 200
check /pt/estatisticas 200
check /rss/fires.xml 200
check /sitemap-fires.xml 200
check /css/app.css 200
check "/pt/fogo/1%26x%3D1" 404

# HTTP checks may be answered by the CDN, so check the sidecars directly.
# Health checks need a few intervals after a recreate: wait, but bounded.
wait_healthy() {
    local svc="$1" id status deadline=$((SECONDS + ${HEALTH_TIMEOUT:-120}))
    id=$($COMPOSE ps -aq "$svc")
    while :; do
        status=$( [ -n "$id" ] && docker inspect -f '{{if not .State.Running}}stopped{{else if .State.Health}}{{.State.Health.Status}}{{else}}no-healthcheck{{end}}' "$id" 2>/dev/null)
        status=${status:-missing}
        case "$status" in
            healthy) echo "  ok   $svc healthy"; return 0 ;;
            starting) [ $SECONDS -lt $deadline ] || break; sleep 3 ;;
            *) break ;;
        esac
    done
    echo "  FAIL $svc: $status"; fail=1
}
wait_healthy redispt
wait_healthy og-renderer

# /health doesn't launch Chrome — render a tiny card end to end.
render=$(docker exec og-renderer node -e "
fetch('http://127.0.0.1:3000/render', { method: 'POST', headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ html: '<html><body><script>requestAnimationFrame(()=>requestAnimationFrame(()=>window.__ogReady=true))</script></body></html>', width: 120, height: 63 }) })
  .then(async (r) => { const b = Buffer.from(await r.arrayBuffer()); console.log(r.status + ' ' + (b.subarray(1, 4).toString() === 'PNG' ? 'png' : 'not-png')); })
  .catch((e) => console.log('error ' + e.message));" 2>&1 | head -1)
if [ "$render" = "200 png" ]; then echo "  ok   og-renderer renders"; else echo "  FAIL og-renderer render: $render"; fail=1; fi
echo "  og-renderer pids: $(docker exec og-renderer cat /sys/fs/cgroup/pids.current 2>/dev/null || echo n/a)"

if [ "$fail" -ne 0 ]; then
    echo -e "\n\033[1;31mSmoke checks failed — check storage/logs.\033[0m"
    exit 1
fi
echo -e "\n\033[1;32mDeploy OK.\033[0m Purge the CDN cache if HTML/CSS changed."

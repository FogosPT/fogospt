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

for svc in og-renderer redispt; do
    status=$(docker inspect -f '{{if .State.Health}}{{.State.Health.Status}}{{else}}no-healthcheck{{end}}' "$($COMPOSE ps -q $svc)")
    echo "  $svc: $status"
done
echo "  og-renderer pids: $(docker exec og-renderer cat /sys/fs/cgroup/pids.current)"

if [ "$fail" -ne 0 ]; then
    echo -e "\n\033[1;31mSmoke checks failed — check storage/logs.\033[0m"
    exit 1
fi
echo -e "\n\033[1;32mDeploy OK.\033[0m Purge the CDN cache if HTML/CSS changed."

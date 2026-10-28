# Production image for the Fogos.pt web app (PHP-FPM + code + vendor).
# Built and pushed by CI to ghcr.io/fogospt/fogospt on every push to master.
# The default docker-compose.yml still bind-mounts ./fogospt; see
# docker-compose.image.yml to run this image instead.

# --- PHP dependencies (no dev packages) ------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY fogospt/composer.json fogospt/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
        --no-interaction --ignore-platform-reqs
COPY fogospt/ ./
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts

# --- CSS --------------------------------------------------------------------
FROM node:22-alpine AS css
WORKDIR /app
COPY fogospt/package.json fogospt/package-lock.json ./
RUN npm ci --ignore-scripts --no-audit --no-fund
COPY fogospt/resources/assets ./resources/assets
RUN mkdir -p public/css && npm run build

# --- Runtime ----------------------------------------------------------------
# Same pinned patch release as docker-compose.yml — never below 8.3.36.
FROM php:8.3.36-fpm
COPY assets/php/php.ini /usr/local/etc/php/conf.d/zz-fogospt.ini
COPY assets/php/www.conf /usr/local/etc/php-fpm.d/zz-fogospt.conf

WORKDIR /var/www/html
COPY --from=vendor --chown=www-data:www-data /app ./
COPY --from=css --chown=www-data:www-data /app/public/css/app.css ./public/css/app.css

RUN rm -rf tests node_modules \
    && mkdir -p storage/app/og storage/logs storage/framework/cache storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    # Compose may run this image as the host UID (see docker-compose.yml):
    # keep the writable dirs writable for whichever user that is.
    && chmod -R a+rwX storage bootstrap/cache \
    && su www-data -s /bin/sh -c 'php artisan package:discover --ansi'

USER www-data
EXPOSE 9000

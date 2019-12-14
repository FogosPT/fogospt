# syntax=docker/dockerfile:experimental

## composer stage
FROM php:fogospt AS composer-builder

WORKDIR /src

## ideally here we copy only the files required for composer to build
COPY fogospt/ /src/

RUN --mount=type=cache,id=composer,sharing=locked,target=/root/.composer \
    composer -v install --no-dev --no-interaction


FROM alpine AS composer-storage

RUN adduser -DHSu 100 nginx -s /sbin/nologin
WORKDIR /var/www

COPY --from=composer-builder --chown=nginx:0 /src/vendor /var/www/vendor
## ideally here we copy only the files required for nginx to serve
COPY --chown=nginx:0 fogospt/ /var/www/

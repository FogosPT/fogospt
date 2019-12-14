ARG GITHUB_SHA

FROM docker.pkg.github.com/fogospt/fogospt/composer:fogospt-${GITHUB_SHA} AS composer
from docker.pkg.github.com/fogospt/fogospt/node:fogospt-${GITHUB_SHA} AS node

FROM alpine AS fogospt-storage

RUN adduser -DHSu 100 nginx -s /sbin/nologin
WORKDIR /var/www

USER nginx

COPY --from=composer --chown=nginx:0 /var/www/vendor /var/www/vendor
COPY --from=node --chown=nginx:0 /var/www/node_modules /var/www/node_modules

## ideally here we copy only the files required for nginx to serve
COPY --chown=nginx:0 fogospt/ /var/www/

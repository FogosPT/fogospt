FROM alpine AS fogospt-storage

RUN adduser -DHSu 100 nginx -s /sbin/nologin
WORKDIR /var/www

USER nginx

COPY --from=composer:fogospt --chown=nginx:0 /var/www/vendor /var/www/vendor
COPY --from=node:fogospt --chown=nginx:0 /var/www/node_modules /var/www/node_modules

## ideally here we copy only the files required for nginx to serve
COPY --chown=nginx:0 fogospt/ /var/www/

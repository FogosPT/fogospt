# syntax=docker/dockerfile:experimental

FROM nginx:stable-alpine AS nginx

EXPOSE 443
WORKDIR /var/www
RUN chown nginx:nginx /var/www

RUN --mount=type=cache,id=apk,sharing=locked,target=/var/cache/apk ln -vs /var/cache/apk /etc/apk/cache && \
    apk add --update \
    ca-certificates \
    nginx-module-xslt \
    nginx-module-geoip \
    nginx-module-image-filter \
    nginx-mod-http-perl \
    nginx-module-njs \
    gettext

COPY general_nginx_security_headers.conf /etc/nginx/conf.d/general_nginx_security_headers.conf
COPY nginx.conf /etc/nginx/nginx.conf

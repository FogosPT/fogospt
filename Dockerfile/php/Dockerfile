# syntax=docker/dockerfile:experimental

FROM alpine:3.10 AS php73
# FROM codecasts/alpine-3.9:php-7.3 AS php73

EXPOSE 9000
CMD ["/usr/sbin/php-fpm7", "-F"]
RUN adduser -DHSu 100 nginx -s /sbin/nologin
RUN ln -sv /usr/sbin/php-fpm7 /usr/sbin/php-fpm
WORKDIR /var/www
COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY file.conf /etc/php7/conf.d/file.conf
COPY file.conf /etc/php7/php-fpm.d/file.conf

RUN --mount=type=cache,id=apk,sharing=locked,target=/var/cache/apk ln -vs /var/cache/apk /etc/apk/cache && \
    apk add --no-cache \
    ca-certificates \
    php7-fpm \
    php7-curl \
    php7-exif \
    php7-fileinfo \
    php7-gd \
    php7-intl \
    php7-json \
    php7-mcrypt \
    php7-mysqli \
    php7-pdo_mysql \
    php7-pdo_sqlite \
    php7-phar \
    php7-session \
    php7-tokenizer \
    php7-zip \
    php7-dom \
    php7-xmlwriter \
    php7-xml

FROM php73 AS php73-xdebug
# target xdebug

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN --mount=type=cache,id=apk,sharing=locked,target=/var/cache/apk \
    apk add --update \
    php7-xdebug

FROM php73 AS php73-target
## final target

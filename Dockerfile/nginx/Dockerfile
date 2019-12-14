FROM nginx:stable-alpine AS nginx

EXPOSE 443
WORKDIR /var/www
RUN chown nginx:nginx /var/www

COPY general_nginx_security_headers.conf /etc/nginx/conf.d/general_nginx_security_headers.conf
COPY nginx.conf /etc/nginx/nginx.conf

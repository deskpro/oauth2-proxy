FROM alpine:3.8

RUN apk --update add \
  nginx \
  php7 \
  php7-fpm \
  php7-json \
  php7-openssl \
  php7-sodium \
  php7-ctype \
  php7-zlib \
  supervisor

COPY www.conf /etc/php7/php-fpm.d/www.conf

COPY oauth2.conf /etc/nginx/conf.d/oauth2.conf
COPY nginx.conf /etc/nginx/nginx.conf

COPY upstream.php /var/www/public/upstream.php
COPY www.conf /etc/php7/php-fpm.d/www.conf

RUN mkdir -p /var/run/php-fpm

RUN mkdir -p /var/log/supervisor
COPY nginx-supervisor.ini /etc/supervisor.d/nginx-supervisor.ini

EXPOSE 80

CMD ["/usr/bin/supervisord"]

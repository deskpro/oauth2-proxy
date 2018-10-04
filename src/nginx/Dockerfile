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
  php7-curl \
  supervisor

COPY www.conf /etc/php7/php-fpm.d/www.conf

COPY oauth2.conf /etc/nginx/conf.d/oauth2.conf
COPY nginx.conf /etc/nginx/nginx.conf
COPY auth.deskpro.com.key /etc/nginx/pki/auth.deskpro.com.key
COPY auth.deskpro.com.crt /etc/nginx/pki/auth.deskpro.com.crt
COPY dhparam.pem /etc/nginx/pki/dhparam.pem

COPY www.conf /etc/php7/php-fpm.d/www.conf
RUN mkdir -p /var/run/php-fpm

RUN mkdir -p /var/log/supervisor
COPY nginx-supervisor.ini /etc/supervisor.d/nginx-supervisor.ini

COPY --from=dpsocialauth/upstream:php  /var/www /var/www/public/

EXPOSE 80

CMD ["/usr/bin/supervisord"]

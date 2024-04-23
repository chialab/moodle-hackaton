###
# Assemble Moodle directory
###
FROM --platform=$BUILDPLATFORM chialab/php:8.3-fpm-alpine AS moodle

COPY moodle/ /var/www/moodle/
WORKDIR /var/www/moodle
RUN composer install --no-dev --prefer-dist --no-interaction

###
# Install additional required PHP dependencies over the chialab/php image, and unload `event` extension
###
FROM chialab/php:8.3-fpm-alpine AS php-base

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && rm /usr/local/etc/php/conf.d/xx-php-ext-event.ini

ADD --link https://browscap.org/stream?q=Lite_PHP_BrowsCapINI /opt/browscap.ini

COPY config/php-conf.ini /usr/local/etc/php/conf.d/bedita.ini
COPY config/phpfpm-conf.ini /usr/local/etc/php-fpm.d/zzz-bedita.conf

###
# Caddy image
###
FROM caddy:2-alpine AS web

COPY config/Caddyfile /etc/caddy/
COPY --from=moodle /var/www/moodle /app/webroot


###
# Build app image
###
FROM php-base AS app

COPY --from=moodle --chown=www-data:www-data /var/www/moodle /app/webroot

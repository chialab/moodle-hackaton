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
FROM chialab/php:8.3-apache AS php-base

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && rm /usr/local/etc/php/conf.d/xx-php-ext-event.ini \
    && a2enmod deflate \
    && a2enmod headers \
    && a2enmod proxy_fcgi \
    && a2enmod proxy_http \
    && a2enmod remoteip \
    && a2enmod rewrite \
    && a2enmod socache_shmcb \
    && a2enmod ssl \
    && a2enmod substitute \
    && rm /etc/apache2/conf-enabled/* \
    && rm /etc/apache2/sites-enabled/*

ADD --link https://browscap.org/stream?q=Lite_PHP_BrowsCapINI /opt/browscap.ini
COPY config/php/ /usr/local/etc/php/

###
# Final image
###
FROM php-base

COPY config/apache/ /etc/apache2/
COPY --from=moodle --chown=www-data:www-data /var/www/moodle /var/www/moodle
COPY --chown=www-data:www-data config/config.php /var/www/moodle/
WORKDIR /var/www/moodle

# Data directory for moodle
RUN mkdir /var/www/moodledata \
    && chown -R www-data:www-data /var/www/moodledata

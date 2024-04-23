###
# Assemble Moodle directory
###
FROM --platform=$BUILDPLATFORM busybox:1 AS moodle

SHELL [ "/bin/ash", "-o", "pipefail", "-c" ]

COPY moodle/ /var/www/moodle/

###
# Install additional required PHP dependencies over the chialab/php image, and unload `event` extension
###
FROM chialab/php:8.3-apache AS php-base

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && rm /usr/local/etc/php/conf.d/xx-php-ext-event.ini \
    && install-php-extensions gmp \
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
# COPY --link config/php/ /usr/local/etc/php/

###
# Build final image
###
FROM php-base

LABEL org.label-schema.schema-version="1.0" \
    org.label-schema.name="iebook" \
    org.label-schema.description="All-in-one image for BEdita 3 IEBook instance" \
    org.label-schema.vcs-url="https://gitlab.com/iebook/infrastructure/ieb-kubernetes/" \
    org.label-schema.vendor="Team Aglebert"

# COPY --link config/apache/ /etc/apache2/

COPY --from=moodle --chown=www-data:www-data /var/www/moodle /var/www/moodle

WORKDIR /var/www/moodle
# hadolint ignore=DL3025
CMD apache2-foreground "${APACHE_ARGUMENTS}"

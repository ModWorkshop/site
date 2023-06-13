# #### Caddy Stage
# # Install Caddy
# FROM caddy:builder-alpine AS caddy-builder
# RUN xcaddy build
# ####

# #### PHP Stage
# FROM webdevops/php:8.1-alpine as build


FROM webdevops/php-nginx:8.1-alpine as build

# Configure ENV variables
# ENV FPM_MAX_REQUESTS=1000
# ENV FPM_PM_MAX_CHILDREN=25
# ENV FPM_PM_START_SERVERS=5
# ENV FPM_PM_MIN_SPARE_SERVERS=5
# ENV FPM_PM_MAX_SPARE_SERVERS=10
# ENV PHP_MAX_EXECUTION_TIME=30
# ENV FPM_PROCESS_IDLE_TIMEOUT=120
ENV WEB_DOCUMENT_ROOT=/app/public

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php81-pear php81-dev imagemagick-dev gcc musl-dev make vips \
    && pecl install apfd imagick \
    # && pecl install excimer \
    && apk del .build-deps

# Install dev dependencies
# RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev imagemagick-dev \
#     libtool libxml2-dev postgresql-dev sqlite-dev

# # Install production dependencies
# RUN apk add --no-cache bash curl freetype-dev g++ gcc git icu-dev icu-libs imagemagick  \
#     libc-dev libjpeg-turbo-dev libpng-dev libzip-dev make mysql-client oniguruma-dev \
#     postgresql-libs supervisor zlib-dev

# RUN pecl install apfd imagick swoole

# PHP ini configuration
RUN echo "ffi.enable = true" >> /opt/docker/etc/php/php.ini
RUN echo "extension=apfd" >> /opt/docker/etc/php/php.ini
# RUN echo "extension=excimer" >> /opt/docker/etc/php/php.ini
RUN echo "pm = static" >> /opt/docker/etc/php/fpm/pool.d/application.conf
RUN echo "pm = static" >> /opt/docker/etc/php/fpm/php-fpm.conf
# RUN echo "extension=swoole" >> /opt/docker/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /opt/docker/etc/php/php.ini
RUN echo "upload_max_filesize = 1G" >> /opt/docker/etc/php/php.ini

FROM build as prod
USER root
# Cron Job
# RUN echo "* * * * * cd /var/www/html && php artisan schedule:run" >>  /var/spool/cron/crontabs/nobody 

# Copy stuff
RUN mkdir -p /app/public
WORKDIR /app/public
COPY --chown=1000:1000 ./root /var/www/html

# Install composer packages
RUN composer install --no-interaction --no-dev
RUN php artisan route:cache

USER application:application

FROM build as dev

# Copy stuff
WORKDIR /var/www/html

RUN apk add nodejs

# Install composer packages
CMD composer install --no-interaction \
    && php artisan mws:install --auto \
    && php artisan serve
    # && php artisan octane:start --server=swoole --host=0.0.0.0 --watch

# Start things and set to nobody
EXPOSE 8000
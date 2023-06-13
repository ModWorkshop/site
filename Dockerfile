FROM webdevops/php-nginx:8.1-alpine as build

ENV PHP_MEMORY_LIMIT=2048M
ENV WEB_DOCUMENT_ROOT=/app/public

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php81-pear php81-dev imagemagick-dev gcc musl-dev make vips \
    && pecl install apfd imagick \
    # && pecl install excimer \
    && apk del .build-deps

# PHP ini configuration
RUN echo "ffi.enable = true" >> /opt/docker/etc/php/php.ini
RUN echo "extension=apfd" >> /opt/docker/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /opt/docker/etc/php/php.ini
RUN echo "upload_max_filesize = 1G" >> /opt/docker/etc/php/php.ini

FROM build as prod
# Cron Job
# RUN echo "* * * * * cd /var/www/html && php artisan schedule:run" >>  /var/spool/cron/crontabs/nobody 

# Copy stuff
WORKDIR /app
COPY --chown=application:www-data ./root /app

# Install composer packages
USER application
RUN composer install --no-interaction --no-dev --optimize-autoloader
RUN php artisan route:cache

FROM build as dev

# Copy stuff
WORKDIR /var/www/html

RUN apk add nodejs

# Install composer packages
CMD composer install --no-interaction \
    && php artisan mws:install --auto \
    && php artisan serve

# Start things and set to nobody
EXPOSE 8000
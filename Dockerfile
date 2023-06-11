# #### Caddy Stage
# # Install Caddy
# FROM caddy:builder-alpine AS caddy-builder
# RUN xcaddy build
# ####

# #### PHP Stage
FROM webdevops/php:8.1-alpine as build


# FROM webdevops/php-nginx:8.1-alpine as build

# Configure ENV variables
ENV FPM_MAX_REQUESTS=500
ENV FPM_PM_MAX_CHILDREN=16
ENV FPM_PM_START_SERVERS=16
ENV FPM_PM_MIN_SPARE_SERVERS=8
ENV FPM_PM_MAX_SPARE_SERVERS=16
ENV LOG_STDERR=/proc/self/fd/2
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php81-pear php81-dev imagemagick-dev gcc musl-dev make vips \
    && pecl install apfd imagick excimer \
    && apk del .build-deps
RUN apk add redis

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
RUN echo "extension=excimer" >> /opt/docker/etc/php/php.ini
# RUN echo "extension=swoole" >> /opt/docker/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /opt/docker/etc/php/php.ini
RUN echo "upload_max_filesize = 1G" >> /opt/docker/etc/php/php.ini

FROM build as prod
# Cron Job
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run" >>  /var/spool/cron/crontabs/nobody 

# Copy stuff
WORKDIR /var/www/html
# COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
# COPY ./conf.d/Caddyfile /etc/caddy/Caddyfile
# COPY ./conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
# COPY ./conf.d/Caddyfile /etc/caddy/Caddyfile
COPY --chown=nobody ./root /var/www/html
RUN mkdir /.config
RUN chown -R nobody.nobody /run /.config

# Install composer packages
RUN composer install --no-interaction --no-dev
RUN php artisan route:cache

# Start things and set to nobody
USER nobody
EXPOSE 8080
# CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

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
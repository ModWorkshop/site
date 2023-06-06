#### Caddy Stage
# Install Caddy
FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build
####

#### PHP Stage
FROM webdevops/php:8.1-alpine as build

# Configure ENV variables
ENV FPM_MAX_REQUESTS=1000
ENV LOG_STDERR=/proc/self/fd/2

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php81-pear php81-dev imagemagick-dev gcc musl-dev make vips \
    && pecl install apfd \
    && pecl install imagick \
    && apk del .build-deps
RUN apk add redis

RUN \
    apk add --no-cache linux-headers && \
    apk add --no-cache libstdc++ postgresql-dev libpq && \
    apk add --no-cache --virtual .build-deps curl-dev openssl-dev pcre-dev pcre2-dev zlib-dev && \
    docker-php-ext-install sockets && \
    docker-php-source extract && \
    mkdir /usr/src/php/ext/openswoole && \
    curl -sfL https://github.com/openswoole/swoole-src/archive/v22.0.0.tar.gz -o swoole.tar.gz && \
    tar xfz swoole.tar.gz --strip-components=1 -C /usr/src/php/ext/openswoole && \
    docker-php-ext-configure openswoole \
        --enable-http2   \
        --enable-mysqlnd \
        --enable-openssl \
        --enable-sockets --enable-hook-curl --with-postgres && \
    docker-php-ext-install -j$(nproc) --ini-name zzz-docker-php-ext-openswoole.ini openswoole && \
    rm -f swoole.tar.gz $HOME/.composer/*-old.phar && \
    docker-php-source delete && \
    apk del .build-deps

# PHP ini configuration
RUN echo "ffi.enable = true" >> /opt/docker/etc/php/php.ini
RUN echo "extension=apfd" >> /opt/docker/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /opt/docker/etc/php/php.ini
RUN echo "upload_max_filesize = 1G" >> /opt/docker/etc/php/php.ini

FROM build as prod
# Cron Job
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run" >>  /var/spool/cron/crontabs/nobody 

# Copy stuff
WORKDIR /var/www/html
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY ./conf.d/Caddyfile /etc/caddy/Caddyfile
COPY ./conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY ./conf.d/Caddyfile /etc/caddy/Caddyfile
COPY --chown=nobody ./root /var/www/html
RUN mkdir /.config
RUN chown -R nobody.nobody /run /.config

# Install composer packages
RUN composer install --no-interaction --no-dev
RUN php artisan config:cache && php artisan route:cache

# Start things and set to nobody
USER nobody
EXPOSE 8080
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

FROM build as dev

# Copy stuff
WORKDIR /var/www/html

RUN apk add nodejs

# Install composer packages
CMD composer install --no-interaction \
    && php artisan mws:install --auto \
    && php artisan octane:start --server=swoole --host=0.0.0.0 --watch

# Start things and set to nobody
EXPOSE 8000
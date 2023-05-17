#### Caddy Stage
# Install Caddy
FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build
####

#### PHP Stage
FROM webdevops/php:8.1-alpine

# Configure ENV variables
ENV FPM_MAX_REQUESTS=1000
ENV LOG_STDERR=/proc/self/fd/2

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php81-pear php81-dev imagemagick-dev gcc musl-dev make vips redis \
    && pecl install apfd \
    && pecl install imagick \
    && apk del .build-deps

# PHP ini configuration
RUN echo "ffi.enable = true" >> /opt/docker/etc/php/php.ini
RUN echo "extension=apfd" >> /opt/docker/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /opt/docker/etc/php/php.ini
RUN echo "upload_max_filesize = 1G" >> /opt/docker/etc/php/php.ini

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
RUN chown -R nobody.nobody /run /.config

# Install composer packages
RUN composer install --no-interaction --no-dev

# Start things and set to nobody
USER nobody
EXPOSE 8080
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]


# Install Caddy
FROM caddy:builder-alpine AS caddy-builder
RUN xcaddy build

# Install PHP
FROM webdevops/php:8.1-alpine

ENV FPM_MAX_REQUESTS=1000
ENV LOG_STDERR=/proc/self/fd/2

# pecl & apfd (always populate formdata) stuff
ENV PHPIZE_DEPS \
  php81-pear \
  php81-dev \
  imagemagick-dev \
  gcc \
  musl-dev \
  make
RUN set -eux \
    && apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} \
    && pecl install apfd \
    && pecl install imagick \
    && apk del .build-deps

# libvips
RUN apk add vips
RUN echo "ffi.enable = true" >> /opt/docker/etc/php/php.ini
RUN echo "extension=apfd" >> /opt/docker/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /opt/docker/etc/php/php.ini
RUN echo "upload_max_filesize = 1G" >> /opt/docker/etc/php/php.ini
# Cron Job
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run" >>  /var/spool/cron/crontabs/nobody 

# Setup document root
WORKDIR /var/www/html

# Configure caddy
COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY ./conf.d/Caddyfile /etc/caddy/Caddyfile

# Configure supervisord
COPY ./conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN mkdir /.config
RUN chown -R nobody.nobody /var/www/html /var/www/html /run /.config

RUN apk add redis

# Switch to use a non-root user from here on
USER nobody
# Add application
COPY --chown=nobody ./root /var/www/html

# Expose the port caddy is reachable on
EXPOSE 8080


RUN 

# Let supervisord start caddy & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Install composer packages
RUN composer install --no-interaction --no-dev
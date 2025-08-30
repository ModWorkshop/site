#syntax=docker/dockerfile:1
FROM dunglas/frankenphp:1.8.0-php8.3-bookworm AS build

RUN apt-get update && apt-get install supervisor -y --no-install-recommends

# # Using heredoc from dockerfile:1.4 (ref: https://www.docker.com/blog/introduction-to-heredocs-in-dockerfiles/)
# # PHP ini configuration
# # So php ini doesn't break
RUN install-php-extensions \
    apcu \
    ffi \
    redis \
    pdo_mysql \
    exif \
    pdo_pgsql \
    pcntl \
    zip \
    vips

RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

RUN <<EOF cat >> $PHP_INI_DIR/php.ini

ffi.enable=true
post_max_size=100M
upload_max_filesize=100M
memory_limit=512M
max_execution_time=150
disable_functions=phpinfo
opcache.enable=1
opcache.enable_cli=1
opcache.jit_buffer_size=250M
opcache.memory_consumption=1024
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.interned_strings_buffer=8
opcache.fast_shutdown=1
realpath_cache_size=4096K
realpath_cache_ttl=600
zend.max_allowed_stack_size=-1
EOF

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

FROM build AS prod
# FROM build as prod
COPY --chown=nobody . /app

#cron https://github.com/TrafeX/docker-php-nginx/issues/110#issuecomment-1466265928
COPY conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY conf.d/Caddyfile /etc/caddy/Caddyfile

RUN apt-get update && apt-get install cron -y --no-install-recommends \
    # && chown nobody:nogroup /usr/sbin/cron \
    # && setcap cap_setgid=ep /usr/sbin/cron \
    && mkdir /etc/crontabs \
    && echo '* * * * * /usr/local/bin/php /app/artisan schedule:run >> /app/storage/logs/laravel-cron.log 2>&1' >> /etc/crontabs/nobody \
    && crontab -u nobody /etc/crontabs/nobody \
    # && chown -R nobody /var/spool/cron/crontabs/nobody \
    # && chmod 0644 /var/spool/cron/crontabs/nobody \
    && chown -R nobody /app/entrypoint.sh \
    && chmod +x /app/entrypoint.sh

# Install composer packages & cache this layer
RUN composer install --no-interaction --no-dev --optimize-autoloader --apcu-autoloader --no-progress --ignore-platform-reqs --ignore-platform-req=php

RUN php artisan scribe:generate #TODO: find a better way to generate these so we can include auth ones too

RUN php artisan route:cache \
    && php artisan optimize \
    && php artisan storage:link

ENTRYPOINT ["/app/entrypoint.sh"]

FROM build AS dev

# For development, we'll use the host user's UID/GID
ARG USER_ID=1000
ARG GROUP_ID=1000

# Create a user with the same UID/GID as the host user
RUN groupadd -g ${GROUP_ID} devuser && \
    useradd -u ${USER_ID} -g ${GROUP_ID} -m -s /bin/bash devuser

RUN <<EOF cat >> $PHP_INI_DIR/php.ini
opcache.validate_timestamps=1
EOF

# Change ownership of the working directory
RUN chown -R devuser:devuser /app

# Switch to the created user
USER devuser

# Install composer packages
CMD ["/bin/sh", "-c", "composer install --no-interaction --ignore-platform-req=php && php artisan mws:install --auto && php artisan serve --host=0.0.0.0"]

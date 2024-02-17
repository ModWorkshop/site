#syntax=docker/dockerfile:1
FROM dunglas/frankenphp:1.1.0-php8.3-alpine as build

RUN apk add --no-cache \
  supervisor

# # Using heredoc from dockerfile:1.4 (ref: https://www.docker.com/blog/introduction-to-heredocs-in-dockerfiles/)
# # PHP ini configuration
# # So php ini doesn't break
RUN install-php-extensions \
    ffi \
    simplexml \
    tokenizer \
    fileinfo \
    redis \
    pdo_mysql \
    pdo \
    exif \
    pdo_pgsql \
    xmlwriter \
    pcntl \
    posix \
    vips \
    apfd

RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

RUN <<EOF cat >> $PHP_INI_DIR/php.ini

ffi.enable=true
post_max_size=1G
upload_max_filesize=1G
memory_limit=2G
max_execution_time=150
disable_functions=phpinfo
opcache.enable_cli=1
opcache.jit_buffer_size=250M
EOF

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

FROM build as prod
# FROM build as prod
COPY --chown=nobody ./root /app

#cron https://github.com/TrafeX/docker-php-nginx/issues/110#issuecomment-1466265928
COPY entrypoint.sh /scripts/entrypoint.sh
COPY conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY conf.d/Caddyfile /etc/caddy/Caddyfile

RUN apk add --no-cache dcron libcap \
    && chown nobody:nobody /usr/sbin/crond \
    && setcap cap_setgid=ep /usr/sbin/crond \
    && echo '* * * * * php /app/artisan schedule:run >> /app/storage/logs/laravel.log 2>&1' >> /etc/crontabs/nobody \
    && crontab -u nobody /etc/crontabs/nobody \
    && chown -R nobody /var/spool/cron/crontabs/nobody \
    && chmod 0644 /var/spool/cron/crontabs/nobody \
    && chown -R nobody /scripts/entrypoint.sh \
    && chmod +x /scripts/entrypoint.sh

# Install composer packages & cache this layer
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-progress --ignore-platform-reqs \
    && php artisan route:cache \
    && php artisan optimize \
    && php artisan storage:link

ENTRYPOINT ["/scripts/entrypoint.sh"]

FROM build as dev

# Install composer packages
CMD ["/bin/sh", "-c", "composer install --no-interaction && php artisan mws:install --auto && php artisan serve"]
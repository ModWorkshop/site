# syntax=docker/dockerfile:1
FROM trafex/php-nginx:3.3.0 as build
USER root

RUN apk --no-cache add \
  php82-ffi \
  php82-simplexml \
  php82-tokenizer \
  php82-fileinfo \
  php82-redis \
  php82-pdo_mysql \
  php82-pdo \
  php82-exif \
  php82-pdo_pgsql \
  php82-xmlwriter \
  php82-cli \
  php82-pcntl \
  php82-posix \
  vips

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php82-pear php82-dev gcc g++ libc-dev musl-dev make linux-headers autoconf git \
    && pecl82 install apfd \
    && pecl82 install swoole \
    && apk del --purge .build-deps \
    && rm -rf /var/cache/apk/*

# Using heredoc from dockerfile:1.4 (ref: https://www.docker.com/blog/introduction-to-heredocs-in-dockerfiles/)
# PHP ini configuration
# So php ini doesn't break
RUN <<EOF cat >> /etc/php82/conf.d/custom.ini

ffi.enable=true
extension=apfd
extension=swoole
post_max_size=1G
upload_max_filesize=1G
memory_limit=500M
max_execution_time=150
disable_functions=phpinfo
opcache.enable_cli=1
opcache.jit_buffer_size=250M
EOF

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy stuff
COPY --chown=nobody ./conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY --chown=nobody ./conf.d/www.conf /etc/php82/php-fpm.d/www.conf

FROM build as prod
COPY --chown=nobody ./root /var/www/html

#cron https://github.com/TrafeX/docker-php-nginx/issues/110#issuecomment-1466265928
COPY entrypoint.sh /scripts/entrypoint.sh
COPY conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN apk add --no-cache dcron libcap \
    && chown nobody:nobody /usr/sbin/crond \
    && setcap cap_setgid=ep /usr/sbin/crond \
    && echo '* * * * * php /var/www/html/artisan schedule:run >> /var/www/html/storage/logs/laravel.log 2>&1' >> /etc/crontabs/nobody \
    && crontab -u nobody /etc/crontabs/nobody \
    && chown -R nobody /var/spool/cron/crontabs/nobody \
    && chmod 0644 /var/spool/cron/crontabs/nobody \
    && chown -R nobody /scripts/entrypoint.sh \
    && chmod +x /scripts/entrypoint.sh

# Install composer packages & cache this layer
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-progress \
    && php artisan route:cache \
    && php artisan optimize \
    && php artisan storage:link

# Switch to use a non-root user from here on
USER nobody

ENTRYPOINT ["/scripts/entrypoint.sh"]

FROM build as dev

# Install composer packages
CMD ["/bin/sh", "-c", "composer install --no-interaction && php artisan mws:install --auto && php artisan serve"]

FROM trafex/php-nginx as build
USER root

RUN apk --no-cache add \
  php81-ffi \
  php81-simplexml \
  php81-tokenizer \
  php81-fileinfo \
  php81-redis \
  php81-pdo

# Install stuff
RUN set -eux \
    && apk add --no-cache --virtual .build-deps php81-pear php81-dev gcc musl-dev make vips \
    && pecl install apfd \
    && apk del .build-deps

# PHP ini configuration
# So php ini doesn't break
RUN echo "" >> /etc/php81/conf.d/custom.ini
RUN echo "ffi.enable=true" >> /etc/php81/conf.d/custom.ini
RUN echo "extension=apfd" >> /etc/php81/conf.d/custom.ini
RUN echo "post_max_size=1G" >> /etc/php81/conf.d/custom.ini
RUN echo "upload_max_filesize=1G" >> /etc/php81/conf.d/custom.ini
#FUCK YOU WHOEVER MADE THIS SHITTY FUCKING FUNCTION
RUN echo "disable_functions=phpinfo" >> /etc/php81/conf.d/custom.ini
FROM build as prod
# Cron Job TODO
# RUN echo "* * * * * cd /var/www/html && php artisan schedule:run" >>  /var/spool/cron/crontabs/nobody 

# Copy stuff
COPY --chown=nobody ./root /var/www/html
COPY --chown=nobody ./conf.d/default.conf /etc/nginx/conf.d/default.conf

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer
# Install composer packages
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-progress
RUN php artisan route:cache

# Switch to use a non-root user from here on
USER nobody

FROM build as dev
# Install composer packages
CMD composer install --no-interaction \
    && php artisan mws:install --auto \
    && php artisan serve
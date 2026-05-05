#!/bin/sh

# Cache stuff for performance

echo "Clear and cache config"
php artisan config:clear && php artisan config:cache

echo "Clear and cache route"
php artisan route:clear && php artisan route:cache

echo "Run supervisord"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

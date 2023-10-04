#!/bin/sh

# Cache stuff for performance

echo "Clear and cache config"
php artisan config:clear && php artisan config:cache

echo "Run crond + visord"
/usr/sbin/crond -b
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
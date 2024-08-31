#!/bin/sh

# Cache stuff for performance

echo "Clear and cache config"
php artisan config:clear && php artisan config:cache

echo "Run cron + visord"
/usr/sbin/cron -b
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
#!/bin/sh
/usr/sbin/crond -b
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

# Cache stuff for performance
php artisan config:clear && php artisan config:cache

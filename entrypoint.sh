#!/bin/sh
# Cache stuff for performance
php artisan config:clear && php artisan config:cache

/usr/sbin/crond -b
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

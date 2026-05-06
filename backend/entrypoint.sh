#!/bin/sh

echo "Running scribe generate"
php artisan scribe:generate

echo "Running artisan optimize"
php artisan route:clear
php artisan optimize

echo "Running storage link"
php artisan storage:link

echo "Run supervisord"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

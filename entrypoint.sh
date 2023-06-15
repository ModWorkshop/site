#!/bin/sh
/usr/sbin/crond -b
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
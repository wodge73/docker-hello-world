#!/bin/bash
rm /run/apache2/apache2.pid # in case the server had crashed and we want to restart the container, the pid file must be removed
chown www-data:www-data /app -R
source /etc/apache2/envvars
tail -F /var/log/apache2/* &
exec apache2 -D FOREGROUND

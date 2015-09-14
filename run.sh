#!/bin/bash
# in case the server had crashed and we want to restart the container, the pid file must be removed; we suppress the error message, if the pid file is not present:
rm /run/apache2/apache2.pid 2> /dev/null
chown www-data:www-data /app -R
source /etc/apache2/envvars
tail -F /var/log/apache2/* &
exec apache2 -D FOREGROUND

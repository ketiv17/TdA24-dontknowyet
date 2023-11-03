#!/bin/bash

# start nginx webserver
nginx
echo "nginx loaded!"

# start php-fpm (php addon for nginx)
/usr/bin/php-fpm --fpm-config /etc/php/php-fpm.conf
echo "php-fpm loaded!"

echo "done!"
# so the container doesn't quit
tail -f /dev/null
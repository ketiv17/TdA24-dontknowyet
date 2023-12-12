#!/bin/bash

# start nginx webserver
nginx
echo "nginx started!"

# start php-fpm (php addon for nginx)
/usr/bin/php-fpm --fpm-config /etc/php/php-fpm.conf
echo "php-fpm started!"


echo "done!"
# so the container doesn't quit (replace with bash for debugging)
#tail -f /dev/null
bash
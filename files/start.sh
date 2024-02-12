#!/bin/bash

# start nginx webserver
nginx;
echo "nginx started!";

# start php-fpm (php addon for nginx)
/usr/bin/php-fpm --fpm-config /etc/php/php-fpm.conf;
echo "php-fpm started!";

# start frontend
bun /srv/bun/build/index.js --port 3000 &
echo "bun started!";

echo "done!";
bash;
echo "not running interactivly";
# so the container doesn't quit
tail -f /dev/null;
#!/bin/bash

rm -rf /opt/dvsa/olcs-backend/data/cache/module-config-cache.application.config.cache.php

/opt/dvsa/olcs-backend/vendor/bin/doctrine-module orm:generate-proxies /tmp/Entity/Proxy

# Start PHP-FPM
/usr/local/sbin/php-fpm -F --nodaemonize &

# Start Nginx
nginx -g 'daemon off;'
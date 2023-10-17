#!/bin/bash

# Start PHP-FPM
/usr/local/sbin/php-fpm -F --nodaemonize &

# Start Nginx
nginx -g 'daemon off;'
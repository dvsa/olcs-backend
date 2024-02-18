FROM php:7.4.33-fpm-alpine3.16

LABEL maintainer="<vol-team>@dvsa.gov.uk"
LABEL description="Alpine base image with PHP 7.4, nginx & Supervisord"
LABEL Name="php:nginx-supervisord-fpm-7.4.33-alpine"
LABEL Version="0.1"

RUN apk add --no-cache supervisor nginx

# Supervisor config
COPY ./supervisord.conf /etc/supervisord.conf

# Override php-fpm configs
COPY ./php-fpm/php-fpm.d/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./php-fpm/php-fpm.d/php-fpm.ini /usr/local/etc/php/php.ini

# Override nginx configs
COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./nginx/conf.d/backend.conf /etc/nginx/conf.d/backend.conf

COPY ./supervisord.conf /etc/supervisord.conf

# Copy Script
COPY ./start.sh /start.sh

# Make script executable
RUN chmod +x /start.sh

EXPOSE 80

ENTRYPOINT ["/bin/sh"]

CMD ["/start.sh"]
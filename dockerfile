# Add metadata to the image
ARG DVSA_AWS_SHAREDCOREECR_ID 

FROM ${DVSA_AWS_SHAREDCOREECR_ID}.dkr.ecr.eu-west-1.amazonaws.com/php-base:7.4.0-alpine-fpm-8b0b625

LABEL maintainer="shaun.hare@dvsa.gov.uk"
LABEL description="PHP Alpine base image with dependency packages"
LABEL Name="vol-php-fpm:7.4.33-alpine-fpm"
LABEL Version="0.1"

# FROM registry.olcs.dev-dvsacloud.uk/k8s/php:7.4.22-fpm-alpine as intermediate


ADD backend.tar.gz /opt/dvsa/olcs-backend

RUN mkdir -p /opt/dvsa/olcs-backend /var/log/dvsa /tmp/Entity/Proxy && \
    touch /var/log/dvsa/backend.log


# RUN apk add g++ git icu-dev zlib-dev libzip-dev && docker-php-ext-install intl zip && \
#     curl -sS "https://getcomposer.org/installer" -x "${http_proxy}" -o composer-setup.php && \
#     php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('https://composer.github.io/installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
#     php composer-setup.php --version=1.10.6 && php -r "unlink('composer-setup.php');" && \
#     php composer.phar install --optimize-autoloader --no-interaction --no-dev

# FROM registry.olcs.dev-dvsacloud.uk/k8s/php-baseline:7.4.22-fpm-alpine
    
# Tweak redis extension settings
RUN echo 'session.save_handler = redis' >> /usr/local/etc/php/conf.d/50-docker-php-ext-redis.ini && \
    echo 'session.save_path = "tcp://redis-master"' >> /usr/local/etc/php/conf.d/50-docker-php-ext-redis.ini



# COPY --from=intermediate /build/config /opt/dvsa/olcs-backend/config
# COPY --from=intermediate /build/vendor /opt/dvsa/olcs-backend/vendor
# COPY --from=intermediate /build/module /opt/dvsa/olcs-backend/module
# COPY --from=intermediate /build/public /opt/dvsa/olcs-backend/public
# COPY --from=intermediate /build/data /opt/dvsa/olcs-backend/data
# COPY --from=intermediate /build/init_autoloader.php /opt/dvsa/olcs-backend/init_autoloader.php

RUN rm -f /opt/dvsa/olcs-backend/config/autoload/local* && \
    chown -R www-data:www-data /opt/dvsa /tmp/Entity /var/log/dvsa

USER www-data

CMD ["/usr/local/sbin/php-fpm", "-F"]




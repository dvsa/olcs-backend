
FROM 245185850403.dkr.ecr.eu-west-1.amazonaws.com/php-base:7.4.0-alpine-fpm-8b0b625

LABEL maintainer="shaun.hare@dvsa.gov.uk"
LABEL description="PHP Alpine base image with dependency packages"
LABEL Name="vol-php-fpm:7.4.33-alpine-fpm"
LABEL Version="0.1"

# Expose ports
EXPOSE 80


RUN apk -U upgrade && apk add --no-cache \
    curl \
    nginx 
    
    
#RUN rm /etc/nginx/conf.d/default.conf

COPY nginx/conf.d/backend.conf /etc/nginx/nginx.conf

# FROM registry.olcs.dev-dvsacloud.uk/k8s/php:7.4.22-fpm-alpine as intermediate

RUN mkdir -p /opt/dvsa/olcs-backend /var/log/dvsa /tmp/Entity/Proxy && \
    touch /var/log/dvsa/backend.log
    
ADD backend.tar.gz /opt/dvsa/olcs-backend

COPY start.sh /start.sh
RUN chmod +x /start.sh


# FROM registry.olcs.dev-dvsacloud.uk/k8s/php-baseline:7.4.22-fpm-alpine
    
# Tweak redis extension settings
#RUN echo 'session.save_handler = redis' >> /usr/local/etc/php/conf.d/50-docker-php-ext-redis.ini && \
    #echo 'session.save_path = "tcp://redis-master"' >> /usr/local/etc/php/conf.d/50-docker-php-ext-redis.ini

RUN rm -f /opt/dvsa/olcs-backend/config/autoload/local* && \
    chown -R www-data:www-data /opt/dvsa /tmp/Entity /var/log/dvsa

RUN /opt/dvsa/olcs-backend/vendor/bin/doctrine-module orm:generate-proxies /tmp/Entity/Proxy


#USER www-data

CMD ["/start.sh"]




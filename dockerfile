FROM ${AWS_ACCOUNT_ID_SHAREDCOREECR}.dkr.ecr.${AWS_REGION}.amazonaws.com/quarantine:7.4.33-fpm-alpine-5ef99cd
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

COPY php-fpm/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

# FROM registry.olcs.dev-dvsacloud.uk/k8s/php:7.4.22-fpm-alpine as intermediate

RUN mkdir -p /opt/dvsa/olcs-backend /var/log/dvsa /tmp/Entity/Proxy /tmp/EntityCli/Proxy && \
    touch /var/log/dvsa/backend.log

ADD backend.tar.gz /opt/dvsa/olcs-backend

COPY start.sh /start.sh
RUN chmod +x /start.sh


# FROM registry.olcs.dev-dvsacloud.uk/k8s/php-baseline:7.4.22-fpm-alpine

# Tweak redis extension settings
#RUN echo 'session.save_handler = redis' >> /usr/local/etc/php/conf.d/50-docker-php-ext-redis.ini && \
    #echo 'session.save_path = "tcp://redis-master"' >> /usr/local/etc/php/conf.d/50-docker-php-ext-redis.ini

RUN rm -f /opt/dvsa/olcs-backend/config/autoload/local* && \
    chown -R nginx:nginx /opt/dvsa /tmp/* /var/log/dvsa && \
    chmod u=rwx,g=rwx,o=r -R /opt/dvsa /tmp/* /var/log/dvsa

#USER nginx

CMD ["/start.sh"]

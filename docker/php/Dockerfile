# ./docker/php/Dockerfile
FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libmemcached11 libmemcachedutil2 build-essential libmemcached-dev libz-dev
RUN pecl install memcached
RUN echo extension=memcached.so >> /usr/local/etc/php/conf.d/memcached.ini

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends \
    && apt-get install -y vim cron unzip git curl libxml2-dev g++ make autoconf icu-devtools libzip-dev libhiredis-dev ghostscript libgs-dev \
    && curl -sL https://deb.nodesource.com/setup_14.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get install -y nodejs \
    && docker-php-source extract \
    && pecl install xdebug \
    && pecl install imagick \
    && docker-php-ext-install gd \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-source delete \
    && docker-php-ext-install pdo_mysql soap intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.17 \
    && rm -rf /tmp/*

WORKDIR /usr/src/reports

COPY . /usr/src/reports

RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = 1G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

RUN composer install

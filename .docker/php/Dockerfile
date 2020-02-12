FROM php:7.4-fpm-alpine

RUN echo 'http://dl-cdn.alpinelinux.org/alpine/edge/community' >> /etc/apk/repositories

RUN apk add --update --virtual .run-deps --no-cache \
    $PHPIZE_DEPS \
    bzip2-dev \
    gettext-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    libxslt-dev \
    openssl-dev \
    shadow \
    composer

RUN docker-php-ext-install \
    bcmath \
    bz2 \
    calendar \
    exif \
    gettext \
    gd \
    mysqli \
    pdo_mysql \
    phar \
    soap \
    xmlrpc \
    xsl \
    zip

RUN pecl install xdebug 2.7.2 \
    && docker-php-ext-enable xdebug

RUN apk add --no-cache \
    bash \
    git \
    openssh

# PHP configuration
RUN ln -s /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN mkdir /var/log/php
RUN chown www-data:www-data /var/log/php

# Allow www-data to self-update composer
RUN chown www-data:www-data /usr/bin/composer

# Init ssh config
RUN mkdir /home/www-data/.ssh
COPY .docker/php/ssh-config /home/www-data/.ssh/config
RUN chown -R www-data:www-data /home/www-data/.ssh \
    && chmod 0700 /home/www-data/.ssh \
    && chmod 0600 /home/www-data/.ssh/config

ARG PHP_UID=1000
ARG PHP_GID=1000
RUN usermod -u $PHP_UID www-data \
    && groupmod -g $PHP_GID www-data

ARG PACKAGIST_USER='user'
ARG PACKAGIST_TOKEN='p4ssw0rd'
USER www-data
RUN composer config --global --auth http-basic.repo.packagist.com $PACKAGIST_USER $PACKAGIST_TOKEN



WORKDIR /var/www

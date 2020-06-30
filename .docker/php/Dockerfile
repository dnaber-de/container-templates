FROM php:7.4-fpm-alpine

RUN echo 'http://dl-cdn.alpinelinux.org/alpine/edge/community' >> /etc/apk/repositories

RUN apk add --update --virtual .run-deps --no-cache \
    $PHPIZE_DEPS \
    bzip2-dev \
    gettext-dev \
    icu-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    libxslt-dev \
    openssl-dev \
    shadow \
    composer \
    mysql-client \
    npm

RUN docker-php-ext-install \
    bcmath \
    bz2 \
    calendar \
    exif \
    gettext \
    gd \
    intl \
    mysqli \
    pdo_mysql \
    phar \
    soap \
    xmlrpc \
    xsl \
    zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN pecl install apcu \
    && docker-php-ext-enable apcu

# Memcached extension
# if you don't need it you can remove the next two
# RUN commands completely
RUN apk add --update --no-cache \
    libmemcached-dev \
    libmemcached-libs \
    cyrus-sasl-dev \
    zlib
RUN pecl install memcached \
    && docker-php-ext-enable memcached

# Add bash, git, openssh and gnupg
RUN apk add --no-cache \
    bash \
    git \
    openssh \
    # required for phive
    gnupg \
    ncurses

# Install phive
# If you don't need it remove the following
# RUN command
RUN set -e \
    && wget -O /tmp/phive.phar https://phar.io/releases/phive.phar \
    && wget -O /tmp/phive.phar.asc https://phar.io/releases/phive.phar.asc \
    && for KEYSERVER in \
        pool.sks-keyservers.net \
        pgp.mit.edu \
        ha.pool.sks-keyservers.net \
    ; do \
        if gpg --keyserver $KEYSERVER --recv-keys 0x9D8A98B29B2D5D79 \
        ; then \
            break \
        ; fi \
    ; done \
    && gpg --verify /tmp/phive.phar.asc /tmp/phive.phar \
    && chmod +x /tmp/phive.phar \
    && mv /tmp/phive.phar /usr/local/bin/phive \
    && chown www-data:www-data /usr/local/bin/phive

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

# Add paths for executables to PATH (composer, phive)
ENV PATH="${PATH}:/var/www/bin:/var/www/vendor/bin:/var/www/tools"

WORKDIR /var/www

FROM php:8.2.35-fpm

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libssl-dev \
    zip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxml2-dev \
    libmcrypt-dev \
    libmemcached-dev \
    libmemcached-tools \
    zlib1g-dev \
    libpng-dev \
    libwebp-dev \
    pkg-config \
    libzstd-dev \
    libevent-dev \
    libicu-dev \
    autoconf \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

RUN docker-php-ext-install -j"$(nproc)" \
    pdo_mysql gd soap zip bcmath exif intl

#RUN pecl install mcrypt-1.0.9 \
#    && docker-php-ext-enable mcrypt

#RUN pecl install memcached-3.2.0 \
#    && docker-php-ext-enable memcached

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        curl \
        ca-certificates \
    && curl -fsSL --retry 3 \
        https://github.com/php-memcached-dev/php-memcached/archive/refs/tags/v3.2.0.tar.gz \
        -o /tmp/php-memcached.tar.gz \
    && mkdir -p /usr/src/php/ext/memcached \
    && tar -xzf /tmp/php-memcached.tar.gz \
        --strip-components=1 \
        -C /usr/src/php/ext/memcached \
    && docker-php-ext-install -j"$(nproc)" memcached \
    && php -m | grep -i '^memcached$' \
    && rm -f /tmp/php-memcached.tar.gz \
    && rm -rf /var/lib/apt/lists/*

# Создаем пользователя и группу
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Меняем владельца директории
RUN chown -R www:www /var/www

# Переключаемся на пользователя www
USER www

WORKDIR /var/www

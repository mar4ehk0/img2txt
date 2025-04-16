FROM php:8.3-fpm

ARG UID
ARG GID
ARG USER_NAME
ARG SRC_PATH

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libwebp-dev \
            libxpm-dev \
            libmcrypt-dev \
            libbz2-dev \
            libicu-dev \
            libzip-dev \
            libonig-dev; \
    rm -rf /var/lib/apt/lists/*; \
    # Install php extensions
    docker-php-ext-install pdo_pgsql; \
    docker-php-ext-install bcmath; \
    docker-php-ext-install bz2; \
    docker-php-ext-install intl; \
    docker-php-ext-install zip; \
    # Install the PHP gd library
    docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-webp \
            --with-xpm \
            --with-freetype; \
    docker-php-ext-install gd; \
    # Install XDebug \
    pecl install xdebug-3.3.2 && docker-php-ext-enable xdebug; \
    # Create user for container
    touch /run/php-fpm.pid \
    && groupadd -g ${UID} ${USER_NAME} \
    && useradd -u ${GID} -ms /bin/bash -g ${USER_NAME} ${USER_NAME} \
    && chown ${USER_NAME}:${USER_NAME} /run/php-fpm.pid

USER ${USER_NAME}

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


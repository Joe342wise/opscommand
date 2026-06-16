FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        git \
        unzip \
        postgresql-dev \
        libzip-dev \
        zip \
    && docker-php-ext-install pdo_pgsql pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]

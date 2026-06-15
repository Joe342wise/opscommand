FROM php:8.4-cli
RUN apt-get update -qq && apt-get install -y --no-install-recommends libpq-dev > /dev/null 2>&1 \
    && docker-php-ext-install pgsql pdo_pgsql > /dev/null 2>&1
WORKDIR /app

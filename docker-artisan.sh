#!/bin/bash
# Run artisan commands via Docker with PHP 8.4 + pgsql
docker run --rm --network host \
  -v /home/joewise/Work/Npontu/opscommand:/app \
  -w /app \
  php:8.4-cli sh -c "
    apt-get update -qq 2>/dev/null &&
    apt-get install -y --no-install-recommends libpq-dev > /dev/null 2>&1 &&
    docker-php-ext-install pgsql pdo_pgsql > /dev/null 2>&1 &&
    php artisan $*
  "

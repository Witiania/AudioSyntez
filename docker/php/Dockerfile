FROM php:8.2-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y libpq-dev nano libzip-dev unzip && \
    docker-php-ext-install pdo pdo_pgsql pgsql zip

WORKDIR /app

COPY . .

COPY ./docker/php/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

CMD php-fpm -F; /entrypoint.sh
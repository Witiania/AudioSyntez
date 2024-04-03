FROM php:8.2-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y libpq-dev nano libzip-dev unzip git

RUN docker-php-ext-install pdo pdo_pgsql pgsql zip

RUN pecl install xdebug && docker-php-ext-enable xdebug
ADD ./docker/php/config/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app

COPY . .

COPY ./docker/php/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

CMD php-fpm -F; /entrypoint.sh
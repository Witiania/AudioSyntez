FROM php:8.2-fpm

# Установка Docker CLI
RUN curl -fsSL https://get.docker.com -o get-docker.sh && \
    sh get-docker.sh && \
    rm get-docker.sh

# Установка Docker Compose
RUN curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose && \
    chmod +x /usr/local/bin/docker-compose

# Дополнительные инструкции для установки зависимостей PHP и других необходимых компонентов
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y libpq-dev nano libzip-dev unzip git && \
    docker-php-ext-install pdo pdo_pgsql pgsql zip

WORKDIR /app

COPY . .

COPY ./docker/php/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

CMD /entrypoint.sh

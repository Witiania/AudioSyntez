#!/bin/bash

composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --env=test

php-fpm -F
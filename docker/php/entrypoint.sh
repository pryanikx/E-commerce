#!/bin/bash

set -e

composer install --no-dev --optimize-autoloader

cp .env.example .env

sed -i "s/DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env
sed -i "s/OPEN_EXCHANGE_RATES_API_KEY=.*/OPEN_EXCHANGE_RATES_API_KEY=${OPEN_EXCHANGE_RATES_API_KEY}/" .env

php artisan migrate
composer dump-autoload

# php artisan db:seed

php artisan key:generate

chown -R www-data:www-data /var/www/html

php-fpm
#php artisan serve --host 0.0.0.0 --port 80

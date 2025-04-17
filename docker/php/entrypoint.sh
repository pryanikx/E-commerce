#!/bin/bash

set -e

if [ ! -d "vendor" ]; then
    composer install --no-dev --optimize-autoloader
fi

cp .env.example .env

sed -i "s/DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

php artisan migrate

php artisan key:generate

chown -R www-data:www-data /var/www/html

npm install
npm run build

php-fpm
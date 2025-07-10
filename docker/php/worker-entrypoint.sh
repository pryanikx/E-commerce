#!/bin/bash

set -e

echo "Starting Queue Worker initialization..."

cd /var/www/html

echo "Creating required directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/temp
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "Waiting for dependencies..."
sleep 30

echo "Installing composer dependencies..."
su -s /bin/bash www-data -c "composer install --no-dev --optimize-autoloader"

if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    chown www-data:www-data .env
fi

chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap

echo "Waiting for RabbitMQ..."
while ! nc -z rabbitmq 5672; do
    echo "RabbitMQ is not ready yet..."
    sleep 2
done
echo "RabbitMQ is ready!"

echo "Waiting for MySQL..."
while ! nc -z ${DB_HOST} 3306; do
    echo "MySQL is not ready yet..."
    sleep 2
done
echo "MySQL is ready!"

echo "Waiting for LocalStack..."
while ! nc -z localstack 4566; do
    echo "LocalStack is not ready yet..."
    sleep 2
done
echo "LocalStack is ready!"

sleep 10

echo "Starting queue worker as www-data user..."

exec su -s /bin/bash www-data -c "
    php artisan queue:work rabbitmq \
        --queue=catalog_export \
        --tries=3 \
        --timeout=600 \
        --memory=512 \
        --sleep=3 \
        --verbose
"
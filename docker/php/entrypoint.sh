#!/bin/bash

set -e

composer install --no-dev --optimize-autoloader

cp .env.example .env

# Экранируем специальные символы для sed
DB_HOST_ESCAPED=$(printf '%s\n' "$DB_HOST" | sed 's/[[\.*^$()+?{|]/\\&/g')
DB_DATABASE_ESCAPED=$(printf '%s\n' "$DB_DATABASE" | sed 's/[[\.*^$()+?{|]/\\&/g')
DB_USERNAME_ESCAPED=$(printf '%s\n' "$DB_USERNAME" | sed 's/[[\.*^$()+?{|]/\\&/g')
DB_PASSWORD_ESCAPED=$(printf '%s\n' "$DB_PASSWORD" | sed 's/[[\.*^$()+?{|]/\\&/g')

AWS_ACCESS_KEY_ID_ESCAPED=$(printf '%s\n' "$AWS_ACCESS_KEY_ID" | sed 's/[[\.*^$()+?{|]/\\&/g')
AWS_SECRET_ACCESS_KEY_ESCAPED=$(printf '%s\n' "$AWS_SECRET_ACCESS_KEY" | sed 's/[[\.*^$()+?{|]/\\&/g')
AWS_DEFAULT_REGION_ESCAPED=$(printf '%s\n' "$AWS_DEFAULT_REGION" | sed 's/[[\.*^$()+?{|]/\\&/g')
AWS_BUCKET_ESCAPED=$(printf '%s\n' "$AWS_BUCKET" | sed 's/[[\.*^$()+?{|]/\\&/g')
AWS_ENDPOINT_URL_ESCAPED=$(printf '%s\n' "$AWS_ENDPOINT_URL" | sed 's/[[\.*^$()+?{|]/\\&/g')
OPEN_EXCHANGE_RATES_API_KEY_ESCAPED=$(printf '%s\n' "$OPEN_EXCHANGE_RATES_API_KEY" | sed 's/[[\.*^$()+?{|]/\\&/g')

# Заменяем переменные в .env файле
sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST_ESCAPED}|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE_ESCAPED}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME_ESCAPED}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD_ESCAPED}|" .env

sed -i "s|AWS_ACCESS_KEY_ID=.*|AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID_ESCAPED}|" .env
sed -i "s|AWS_SECRET_ACCESS_KEY=.*|AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY_ESCAPED}|" .env
sed -i "s|AWS_DEFAULT_REGION=.*|AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION_ESCAPED}|" .env
sed -i "s|AWS_BUCKET=.*|AWS_BUCKET=${AWS_BUCKET_ESCAPED}|" .env
sed -i "s|AWS_ENDPOINT_URL=.*|AWS_ENDPOINT_URL=${AWS_ENDPOINT_URL_ESCAPED}|" .env
sed -i "s|OPEN_EXCHANGE_RATES_API_KEY=.*|OPEN_EXCHANGE_RATES_API_KEY=${OPEN_EXCHANGE_RATES_API_KEY_ESCAPED}|" .env

# Добавляем дополнительные переменные для RabbitMQ и очередей
cat >> .env << EOF

# Queue settings
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_VHOST=/
RABBITMQ_LOGIN=admin
RABBITMQ_PASSWORD=password
RABBITMQ_QUEUE=catalog_export

# Export settings
CATALOG_EXPORT_ADMIN_EMAIL=admin@example.com
CATALOG_EXPORT_FROM_EMAIL=noreply@example.com

# AWS settings for LocalStack
AWS_USE_PATH_STYLE_ENDPOINT=true
SES_KEY=${AWS_ACCESS_KEY_ID}
SES_SECRET=${AWS_SECRET_ACCESS_KEY}
SES_REGION=${AWS_DEFAULT_REGION}
SES_ENDPOINT=${AWS_ENDPOINT_URL}

# Mail settings
MAIL_MAILER=ses
EOF

php artisan migrate
composer dump-autoload

# php artisan db:seed

php artisan key:generate

# Create storage directory if it doesn't exist
mkdir -p /var/www/html/storage/app/public/products
mkdir -p /var/www/html/storage/app/temp

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Create storage link if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    ln -s /var/www/html/storage/app/public /var/www/html/public/storage
fi

chown -R www-data:www-data /var/www/html

# Start PHP-FPM
php-fpm
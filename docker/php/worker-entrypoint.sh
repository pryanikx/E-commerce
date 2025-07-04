#!/bin/bash

set -e

echo "Starting Queue Worker initialization..."

# Переходим в рабочую директорию
cd /var/www/html

# Создаем необходимые директории с правильными правами
echo "Creating required directories..."
mkdir -p bootstrap/cache
mkdir -p storage/app/temp
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Устанавливаем правильные права доступа
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Ожидаем готовности зависимостей
echo "Waiting for dependencies..."
sleep 30

# Устанавливаем зависимости от имени www-data
echo "Installing composer dependencies..."
su -s /bin/bash www-data -c "composer install --no-dev --optimize-autoloader"

# Создаем .env файл если его нет
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env

    # Настраиваем переменные окружения
    cat > .env << EOF
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=3306
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=rabbitmq
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=ses
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Laravel"

AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION}
AWS_BUCKET=${AWS_BUCKET}
AWS_ENDPOINT_URL=${AWS_ENDPOINT_URL}
AWS_USE_PATH_STYLE_ENDPOINT=true

SES_KEY=${AWS_ACCESS_KEY_ID}
SES_SECRET=${AWS_SECRET_ACCESS_KEY}
SES_REGION=${AWS_DEFAULT_REGION}
SES_ENDPOINT=${AWS_ENDPOINT_URL}

RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_VHOST=/
RABBITMQ_LOGIN=admin
RABBITMQ_PASSWORD=password
RABBITMQ_QUEUE=catalog_export

CATALOG_EXPORT_ADMIN_EMAIL=admin@example.com
CATALOG_EXPORT_FROM_EMAIL=noreply@example.com

OPEN_EXCHANGE_RATES_API_KEY=${OPEN_EXCHANGE_RATES_API_KEY}
EOF

    # Устанавливаем права на .env
    chown www-data:www-data .env
fi

# Финальная проверка прав
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap

# Ожидаем готовности RabbitMQ
echo "Waiting for RabbitMQ..."
while ! nc -z rabbitmq 5672; do
    echo "RabbitMQ is not ready yet..."
    sleep 2
done
echo "RabbitMQ is ready!"

# Ожидаем готовности MySQL
echo "Waiting for MySQL..."
while ! nc -z ${DB_HOST} 3306; do
    echo "MySQL is not ready yet..."
    sleep 2
done
echo "MySQL is ready!"

# Ожидаем готовности LocalStack
echo "Waiting for LocalStack..."
while ! nc -z localstack 4566; do
    echo "LocalStack is not ready yet..."
    sleep 2
done
echo "LocalStack is ready!"

# Даем дополнительное время для полной инициализации
sleep 10

echo "Starting queue worker as www-data user..."

# Запускаем queue worker от имени www-data
exec su -s /bin/bash www-data -c "
    php artisan queue:work rabbitmq \
        --queue=catalog_export \
        --tries=3 \
        --timeout=600 \
        --memory=512 \
        --sleep=3 \
        --verbose
"
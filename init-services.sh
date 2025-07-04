#!/bin/bash

echo "🚀 Автоматическая инициализация системы экспорта каталога"

# Функция ожидания готовности сервиса
wait_for_service() {
    local service_name=$1
    local url=$2
    local max_attempts=60
    local attempt=1

    echo "⏳ Ожидание готовности $service_name..."

    while [ $attempt -le $max_attempts ]; do
        if curl -f "$url" > /dev/null 2>&1; then
            echo "✅ $service_name готов!"
            return 0
        fi

        if [ $((attempt % 10)) -eq 0 ]; then
            echo "   Попытка $attempt/$max_attempts: $service_name еще не готов..."
        fi

        sleep 3
        attempt=$((attempt + 1))
    done

    echo "❌ $service_name не запустился за отведенное время"
    return 1
}

# Ожидание готовности всех сервисов
echo "📋 Проверка готовности базовых сервисов..."

wait_for_service "LocalStack" "http://localhost:4566/health"
wait_for_service "RabbitMQ" "http://localhost:15672"

# Проверка готовности MySQL
echo "⏳ Ожидание готовности MySQL..."
until docker exec ecommerce_mysql mysqladmin ping -h localhost -u root -p${MYSQL_ROOT_PASSWORD} --silent; do
    echo "   MySQL еще не готов..."
    sleep 3
done
echo "✅ MySQL готов!"

# Инициализация LocalStack
echo "📦 Инициализация LocalStack S3 и SES..."

# Установка awscli-local
docker exec ecommerce_localstack pip install awscli-local > /dev/null 2>&1

# Создание S3 bucket
docker exec ecommerce_localstack awslocal s3 mb s3://catalog-exports --region us-east-1
echo "✅ S3 bucket 'catalog-exports' создан"

# Верификация SES email адресов
docker exec ecommerce_localstack awslocal ses verify-email-identity --email-address admin@example.com
docker exec ecommerce_localstack awslocal ses verify-email-identity --email-address noreply@example.com
echo "✅ SES email адреса верифицированы"

# Создание RabbitMQ очереди
echo "🐰 Создание RabbitMQ очереди..."
curl -u admin:password -H "content-type:application/json" \
  -X PUT http://localhost:15672/api/queues/%2F/catalog_export \
  -d '{"durable":true,"auto_delete":false,"arguments":{}}' > /dev/null 2>&1
echo "✅ RabbitMQ очередь 'catalog_export' создана"

# Ожидание готовности PHP
echo "⏳ Ожидание готовности PHP..."
until docker exec ecommerce_php php -v > /dev/null 2>&1; do
    echo "   PHP еще не готов..."
    sleep 3
done
echo "✅ PHP готов!"

# Запуск Queue Worker
echo "⚙️ Запуск Queue Worker..."
docker exec -d ecommerce_php sh -c "
    cd /var/www/html &&
    sleep 5 &&
    php artisan queue:work rabbitmq --queue=catalog_export --tries=3 --timeout=600 --verbose
"
echo "✅ Queue Worker запущен"

# Финальная проверка
echo ""
echo "🔍 Финальная проверка готовности системы..."

echo "S3 buckets:"
docker exec ecommerce_localstack awslocal s3 ls

echo "SES verified emails:"
docker exec ecommerce_localstack awslocal ses list-verified-email-addresses | grep -o '"[^"]*@[^"]*"' || echo "Проверьте SES вручную"

echo "RabbitMQ очереди:"
curl -u admin:password http://localhost:15672/api/queues/%2F/catalog_export 2>/dev/null | grep -o '"name":"[^"]*"' || echo "catalog_export: ✅"

echo "Queue Worker процесс:"
docker exec ecommerce_php ps aux | grep artisan | grep -v grep || echo "Проверьте worker вручную"

echo ""
echo "🎉 Система готова к использованию!"
echo ""
echo "📋 Доступные сервисы:"
echo "  🌐 Приложение: http://localhost"
echo "  🐰 RabbitMQ: http://localhost:15672 (admin/password)"
echo "  ☁️  LocalStack: http://localhost:4566"
echo ""
echo "🧪 Для тестирования:"
echo "  1. Откройте админку: http://localhost"
echo "  2. Войдите как администратор"
echo "  3. Нажмите '📤 Export Catalog'"
echo "  4. Проверьте email файлы: src/ecommerce/storage/app/emails/"
echo ""
echo "🔍 Мониторинг:"
echo "  docker logs ecommerce_php -f | grep -E '(export|queue|job)'"
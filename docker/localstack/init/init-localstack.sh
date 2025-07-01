#!/bin/bash

echo "Starting LocalStack initialization..."

# Ожидание готовности LocalStack
sleep 15

# Устанавливаем awslocal если его нет
if ! command -v awslocal &> /dev/null; then
    echo "Installing awslocal..."
    pip install awscli-local
fi

# Функция для повторных попыток
retry_command() {
    local max_attempts=5
    local delay=3
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        echo "Attempt $attempt/$max_attempts: $*"
        if "$@"; then
            echo "Command succeeded on attempt $attempt"
            return 0
        else
            echo "Command failed on attempt $attempt"
            if [ $attempt -lt $max_attempts ]; then
                echo "Waiting $delay seconds before retry..."
                sleep $delay
            fi
            attempt=$((attempt + 1))
        fi
    done

    echo "Command failed after $max_attempts attempts"
    return 1
}

# Проверяем готовность LocalStack
echo "Checking LocalStack health..."
retry_command curl -f http://localhost:4566/health

# Создание S3 bucket для экспорта каталогов
echo "Creating S3 bucket..."
retry_command awslocal s3 mb s3://catalog-exports --region us-east-1

# Проверяем что bucket создался
echo "Listing S3 buckets..."
awslocal s3 ls

# Настройка CORS для S3 bucket
echo "Setting up CORS for S3 bucket..."
retry_command awslocal s3api put-bucket-cors --bucket catalog-exports --cors-configuration '{
  "CORSRules": [
    {
      "AllowedHeaders": ["*"],
      "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
      "AllowedOrigins": ["*"],
      "ExposeHeaders": ["ETag"]
    }
  ]
}'

# Верификация email адреса для SES
echo "Verifying email identity for SES..."
retry_command awslocal ses verify-email-identity --email-address admin@example.com

# Дополнительная верификация email для отправителя
echo "Verifying sender email identity for SES..."
retry_command awslocal ses verify-email-identity --email-address noreply@example.com

# Проверяем верифицированные email адреса
echo "Listing verified email addresses..."
awslocal ses list-verified-email-addresses

# Настройка отправителя для SES
echo "Setting up SES identity policy..."
retry_command awslocal ses put-identity-policy --identity admin@example.com --policy-name AllowSendingPolicy --policy '{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": "*",
      "Action": [
        "ses:SendEmail",
        "ses:SendRawEmail"
      ],
      "Resource": "*"
    }
  ]
}'

# Создаем тестовый конфигурационный набор
echo "Creating SES configuration set..."
awslocal ses create-configuration-set --configuration-set Name=default-config-set || true

echo "LocalStack initialization completed successfully!"
echo "✅ S3 bucket 'catalog-exports' created"
echo "✅ SES email 'admin@example.com' verified"
echo "✅ SES email 'noreply@example.com' verified"
echo "✅ SES policies configured"

# Финальная проверка готовности сервисов
echo "Final service health check..."
echo "S3 service status:"
awslocal s3 ls || echo "❌ S3 service check failed"

echo "SES service status:"
awslocal ses list-verified-email-addresses || echo "❌ SES service check failed"

echo "LocalStack initialization script completed!"
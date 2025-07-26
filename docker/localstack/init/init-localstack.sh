#!/bin/bash

echo "Starting LocalStack initialization..."

sleep 15

if ! command -v awslocal &> /dev/null; then
    echo "Installing awslocal..."
    pip install awscli-local
fi

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

echo "Checking LocalStack health..."
retry_command curl -f http://localhost:4566/health

echo "Creating S3 bucket..."
retry_command awslocal s3 mb s3://catalog-exports --region us-east-1

echo "Listing S3 buckets..."
awslocal s3 ls

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

echo "Verifying email identity for SES..."
retry_command awslocal ses verify-email-identity --email-address admin@example.com

echo "Verifying sender email identity for SES..."
retry_command awslocal ses verify-email-identity --email-address noreply@example.com

echo "Listing verified email addresses..."
awslocal ses list-verified-email-addresses

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

echo "Creating SES configuration set..."
awslocal ses create-configuration-set --configuration-set Name=default-config-set || true

echo "LocalStack initialization completed successfully!"
echo "S3 bucket 'catalog-exports' created"
echo "SES email 'admin@example.com' verified"
echo "SES email 'noreply@example.com' verified"
echo "SES policies configured"

echo "Final service health check..."
echo "S3 service status:"
awslocal s3 ls || echo "S3 service check failed"

echo "SES service status:"
awslocal ses list-verified-email-addresses || echo "SES service check failed"

echo "LocalStack initialization script completed!"
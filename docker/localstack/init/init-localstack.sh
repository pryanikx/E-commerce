#!/bin/bash

sleep 10

awslocal s3 mb s3://catalog-exports --region us-east-1

awslocal s3api put-bucket-cors --bucket catalog-exports --cors-configuration '{
  "CORSRules": [
    {
      "AllowedHeaders": ["*"],
      "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
      "AllowedOrigins": ["*"],
      "ExposeHeaders": ["ETag"]
    }
  ]
}'

awslocal ses verify-email-identity --email-address admin@example.com

awslocal ses put-identity-policy --identity admin@example.com --policy-name AllowSendingPolicy --policy '{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": "*",
      "Action": "ses:SendEmail",
      "Resource": "*"
    }
  ]
}'

echo "LocalStack initialization completed successfully!"
echo "S3 bucket 'catalog-exports' created"
echo "SES email 'admin@example.com' verified"
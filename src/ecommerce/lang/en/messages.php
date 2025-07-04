<?php
// lang/en/messages.php

return [
    'deleted' => 'Successfully deleted!',
    'empty_maintenances' => 'No maintenance found!',
    'empty_manufacturers' => 'No manufacturers found!',
    'empty_products' => 'No products found!',
    'empty_categories' => 'No categories found!',
    'catalog_dispatched' => 'Catalog export job dispatched to RabbitMQ!',
    'no_product' => 'Such product doesn\'t exist!',
    'logout' => 'Successfully logged out!',
    'rabbitmq_starting_consumer' => 'Starting RabbitMQ consumer for product catalog',
    's3_client_initializing' => 'Initializing S3 client',
    'ses_client_initializing' => 'Initializing SES client',
    'consumer_started' => 'Consumer started, waiting for messages',
    'rabbitmq_message_created' => 'Created RabbitMQ message',
    'published_to_rabbitmq' => 'Published message to RabbitMQ',
    'rabbitmq_message_received' => 'Received message from RabbitMQ',
    'rabbitmq_queue_declared' => 'Declared RabbitMQ queue',
    'message_processed' => 'Processed message successfully',
    's3_bucket_created' => 'S3 bucket created',
    'csv_generated' => 'CSV file generated',
    'csv_uploaded' => 'Uploaded to S3',
    'csv_deleted_locally' => 'Deleted local CSV file',
    'preparing_to_send_email' => 'Preparing to send email',
    'confirmation_mail_sent' => 'Confirmation email sent',
    'email_content' => 'Email content (fallback)',
    'starting_publishcatalogjob' => 'Starting PublishCatalogJob',
    'csv_headers_written' => 'CSV headers written',
    'products_retrieved' => 'Products retrieved',
    'export_started' => 'Starting catalog export',
    'export_completed' => 'Catalog export completed successfully',
    'export_failed' => 'Catalog export failed',
    'export_failed_permanently' => 'Catalog export job failed permanently',
    'csv_file_generated' => 'CSV file generated successfully',
    'file_uploaded_to_s3' => 'File uploaded to S3 successfully',
    'success_notification_sent' => 'Success notification sent',
    'failure_notification_sent' => 'Failure notification sent',
    'failure_notification_failed' => 'Failed to send failure notification',
    'temp_file_cleaned' => 'Temporary file cleaned up',
    'email_notification_saved' => 'Email notification saved',
    'error_email_saved' => '📧 Error email saved',
    'catalog_export_completed' => 'Catalog export completed',
    'user_unauthorized' => 'User unauthorized',
    'catalog_export_started' => 'Catalog export has been started. You will receive an email notification after completion.',
    'catalog_export_start_failed' => 'Failed to start catalog export',
    'export_processing_check_email' => 'Export is being processed. Check your email for notifications.',
    'export_status_fetch_failed' => 'Failed to fetch export status',
];

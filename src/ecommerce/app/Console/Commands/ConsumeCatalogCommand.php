<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Aws\Ses\SesClient;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class ConsumeCatalogCommand extends Command
{
    /**
     * @var string $signature
     */
    protected $signature = 'catalog:consume';

    /**
     * @var string $description
     */
    protected $description = 'Consume product catalog CSV from RabbitMQ and upload to S3';

    /**
     * @var S3Client $s3
     */
    protected S3Client $s3;

    /**
     * @var SesClient $ses
     */
    protected SesClient $ses;

    /**
     * @param LoggerInterface $logger
     * @param Mailer $mailer
     */
    public function __construct(private readonly LoggerInterface $logger, private readonly Mailer $mailer)
    {
        parent::__construct();
        $this->s3 = $this->initializeS3Client();
        $this->ses = $this->initializeSesClient();
    }

    public function handle(): void
    {
        $this->info('Starting RabbitMQ consumer for product catalog');
        $this->logger->info('Starting RabbitMQ consumer for product catalog');

        try {
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest'),
                env('RABBITMQ_VHOST', '/')
            );
            $this->logger->info('Connected to RabbitMQ');

            $channel = $connection->channel();
            $queue = env('RABBITMQ_MESSAGE_QUEUE', 'catalog_messages');
            $channel->queue_declare($queue, false, true, false, false);
            $this->logger->info('Declared RabbitMQ queue', ['queue' => $queue]);

            $callback = function (AMQPMessage $msg) use ($channel) {
                try {
                    $data = json_decode($msg->body, true);
                    if (!is_array($data) || !isset($data['csv_file_path'], $data['csv_file_name'])) {
                        $this->logger->error('Invalid RabbitMQ message format', ['message' => $msg->body]);
                        $channel->basic_ack($msg->delivery_info['delivery_tag']);
                        $this->warn('Skipped invalid message: ' . $msg->body);
                        return;
                    }

                    $csvFilePath = $data['csv_file_path'];
                    $csvFileName = $data['csv_file_name'];
                    $bucket = env('AWS_BUCKET', 'product-catalog');

                    $this->logger->info('Received message from RabbitMQ', ['file' => $csvFileName, 'path' => $csvFilePath]);

                    if (!file_exists($csvFilePath)) {
                        throw new \Exception('CSV file not found: ' . $csvFilePath);
                    }

                    if (!$this->s3->doesBucketExist($bucket)) {
                        $this->s3->createBucket(['Bucket' => $bucket]);
                        $this->s3->waitUntil('BucketExists', ['Bucket' => $bucket]);
                        $this->logger->info('S3 bucket created', ['bucket' => $bucket]);
                    }

                    $this->s3->putObject([
                        'Bucket' => $bucket,
                        'Key' => $csvFileName,
                        'Body' => fopen($csvFilePath, 'r'),
                        'ContentType' => 'text/csv',
                    ]);
                    $this->logger->info('Uploaded to S3', ['bucket' => $bucket, 'file' => $csvFileName]);

                    unlink($csvFilePath);
                    $this->logger->info('Deleted local CSV file', ['path' => $csvFilePath]);

                    try {
                        $adminEmail = env('ADMIN_EMAIL', 'test@example.com');
                        $fromEmail = env('MAIL_FROM_ADDRESS', 'test@example.com');
                        $emailContent = "The product catalog has been exported to S3: s3://$bucket/$csvFileName";
                        $this->logger->info('Preparing to send email', [
                            'to' => $adminEmail,
                            'from' => $fromEmail,
                            'mailer' => config('mail.mailer'),
                            'endpoint' => env('AWS_ENDPOINT_URL'),
                        ]);
                        $this->mailer->raw($emailContent, function ($message) use ($adminEmail, $fromEmail) {
                            $message->to($adminEmail)
                                ->subject('Product Catalog Export Completed')
                                ->from($fromEmail, env('MAIL_FROM_NAME', 'Ecommerce Admin'));
                        });
                        $this->logger->info('Confirmation email sent', ['email' => $adminEmail, 'content' => $emailContent]);
                        $this->info('Sent email to: ' . $adminEmail);
                    } catch (\Exception $e) {
                        $this->logger->warning('Failed to send confirmation email', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $this->warn('Failed to send email: ' . $e->getMessage());

                        $this->logger->info('Email content (fallback)', [
                            'to' => $adminEmail,
                            'subject' => 'Product Catalog Export Completed',
                            'from' => $fromEmail,
                            'content' => $emailContent,
                        ]);
                    }

                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
                    $this->info('Processed message successfully: ' . $csvFileName);
                } catch (\Exception $e) {
                    $this->logger->error('Failed to process RabbitMQ message', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'message' => $msg->body,
                    ]);
                    $channel->basic_nack($msg->delivery_info['delivery_tag']);
                    $this->error('Failed to process message: ' . $e->getMessage());
                }
            };

            $channel->basic_consume($queue, '', false, false, false, false, $callback);
            $this->logger->info('Consumer started, waiting for messages');
            $this->info('Consumer started, waiting for messages');

            while ($channel->is_consuming()) {
                $channel->wait();
            }

            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            $this->logger->error('RabbitMQ consumer failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('Consumer failed: ' . $e->getMessage());
        }
    }

    protected function initializeS3Client(): S3Client
    {
        $this->logger->info('Initializing S3 client');
        return new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'endpoint' => env('AWS_ENDPOINT_URL', 'http://localhost:4566'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID', 'test'),
                'secret' => env('AWS_SECRET_ACCESS_KEY', 'test'),
            ],
        ]);
    }

    protected function initializeSesClient(): SesClient
    {
        $this->logger->info('Initializing SES client');
        return new SesClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'endpoint' => env('AWS_ENDPOINT_URL', 'http://localhost:4566'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID', 'test'),
                'secret' => env('AWS_SECRET_ACCESS_KEY', 'test'),
            ],
            'debug' => env('APP_DEBUG', false) ? ['logfn' => function ($msg) { $this->logger->debug('SES Client: ' . $msg); }] : false,
        ]);
    }
}

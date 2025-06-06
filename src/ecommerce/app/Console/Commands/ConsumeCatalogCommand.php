<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Aws\Ses\SesClient;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class ConsumeCatalogCommand extends Command
{
    private const COMMAND_SIGNATURE = 'catalog:consume';
    private const RABBITMQ_DEFAULT_HOST = 'localhost';
    private const RABBITMQ_DEFAULT_PORT = 5672;
    private const RABBITMQ_DEFAULT_USER = 'guest';
    private const RABBITMQ_DEFAULT_PASSWORD = 'guest';
    private const RABBITMQ_DEFAULT_VHOST = '/';
    private const RABBITMQ_MESSAGE_QUEUE = 'catalog_messages';
    private const AWS_DEFAULT_REGION = 'us-east-1';
    private const AWS_DEFAULT_ENDPOINT = 'http://localhost:4566';
    private const AWS_DEFAULT_ACCESS_KEY = 'test';
    private const AWS_DEFAULT_SECRET_KEY = 'test';
    private const AWS_DEFAULT_BUCKET = 'product-catalog';
    private const DEFAULT_ADMIN_EMAIL = 'test@example.com';
    private const DEFAULT_FROM_EMAIL = 'test@example.com';
    private const DEFAULT_FROM_NAME = 'Ecommerce Admin';
    private const EMAIL_SUBJECT = 'Product Catalog Export Completed';
    private const CSV_CONTENT_TYPE = 'text/csv';

    /**
     * @var string $signature
     */
    protected $signature = self::COMMAND_SIGNATURE;

    /**
     * @var string $description
     */
    protected $description = 'Consume product catalog CSV from RabbitMQ, upload to S3, and send confirmation email';

    /**
     * @var SesClient $ses
     */
    private SesClient $ses;

    /**
     * @var S3Client $s3
     */
    private S3Client $s3;
    /**
     * @var string $rabbitmqHost
     */
    private string $rabbitmqHost;
    /**
     * @var int $rabbitmqPort
     */
    private int $rabbitmqPort;
    /**
     * @var string $rabbitmqUser
     */
    private string $rabbitmqUser;
    /**
     * @var string $rabbitmqPassword
     */
    private string $rabbitmqPassword;
    /**
     * @var string $rabbitmqVhost
     */
    private string $rabbitmqVhost;
    /**
     * @var string $rabbitmqQueue
     */
    private string $rabbitmqQueue;
    /**
     * @var string $awsRegion
     */
    private string $awsRegion;
    /**
     * @var string $awsEndpoint
     */
    private string $awsEndpoint;
    /**
     * @var string $awsAccessKey
     */
    private string $awsAccessKey;
    /**
     * @var string $awsSecretKey
     */
    private string $awsSecretKey;
    /**
     * @var string $awsBucket
     */
    private string $awsBucket;
    /**
     * @var string $adminEmail
     */
    private string $adminEmail;
    /**
     * @var string $fromEmail
     */
    private string $fromEmail;
    /**
     * @var string $fromName
     */
    private string $fromName;
    /**
     * @var bool $debug
     */
    private bool $debug;

    /**
     * @param LoggerInterface $logger
     * @param Mailer $mailer
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Mailer $mailer
    ) {
        parent::__construct();
        $this->initializeEnvVariables();
        $this->s3 = $this->initializeS3Client();
        $this->ses = $this->initializeSesClient();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->info(__('messages.rabbitmq_starting_consumer'));
        $this->logger->info(__('messages.rabbitmq_starting_consumer'));

        try {
            $connection = $this->connectToRabbitMQ();
            $channel = $this->createRabbitMQChannel($connection);
            $this->consumeMessages($channel);
            $this->closeConnection($channel, $connection);
        } catch (\Exception $e) {
            $this->logError(__('errors.rabbitmq_consumer_failed'), $e);
            $this->error(__('errors.rabbitmq_consumer_failed') . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    private function initializeEnvVariables(): void
    {
        $this->rabbitmqHost = env('RABBITMQ_HOST', self::RABBITMQ_DEFAULT_HOST);
        $this->rabbitmqPort = (int) env('RABBITMQ_PORT', self::RABBITMQ_DEFAULT_PORT);
        $this->rabbitmqUser = env('RABBITMQ_USER', self::RABBITMQ_DEFAULT_USER);
        $this->rabbitmqPassword = env('RABBITMQ_PASSWORD', self::RABBITMQ_DEFAULT_PASSWORD);
        $this->rabbitmqVhost = env('RABBITMQ_VHOST', self::RABBITMQ_DEFAULT_VHOST);
        $this->rabbitmqQueue = env('RABBITMQ_MESSAGE_QUEUE', self::RABBITMQ_MESSAGE_QUEUE);
        $this->awsRegion = env('AWS_DEFAULT_REGION', self::AWS_DEFAULT_REGION);
        $this->awsEndpoint = env('AWS_ENDPOINT_URL', self::AWS_DEFAULT_ENDPOINT);
        $this->awsAccessKey = env('AWS_ACCESS_KEY_ID', self::AWS_DEFAULT_ACCESS_KEY);
        $this->awsSecretKey = env('AWS_SECRET_ACCESS_KEY', self::AWS_DEFAULT_SECRET_KEY);
        $this->awsBucket = env('AWS_BUCKET', self::AWS_DEFAULT_BUCKET);
        $this->adminEmail = env('ADMIN_EMAIL', self::DEFAULT_ADMIN_EMAIL);
        $this->fromEmail = env('MAIL_FROM_ADDRESS', self::DEFAULT_FROM_EMAIL);
        $this->fromName = env('MAIL_FROM_NAME', self::DEFAULT_FROM_NAME);
        $this->debug = env('APP_DEBUG', false);
    }

    /**
     * @return S3Client
     */
    private function initializeS3Client(): S3Client
    {
        $this->logger->info(__('messages.s3_client_initializing'));
        return new S3Client([
            'version' => 'latest',
            'region' => $this->awsRegion,
            'endpoint' => $this->awsEndpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $this->awsAccessKey,
                'secret' => $this->awsSecretKey,
            ],
        ]);
    }

    /**
     * @return SesClient
     */
    private function initializeSesClient(): SesClient
    {
        $this->logger->info(__('messages.ses_client_initializing'));
        return new SesClient([
            'version' => 'latest',
            'region' => $this->awsRegion,
            'endpoint' => $this->awsEndpoint,
            'credentials' => [
                'key' => $this->awsAccessKey,
                'secret' => $this->awsSecretKey,
            ],
            'debug' => $this->debug ? ['logfn' => fn($msg) => $this->logger->debug('SES Client: ' . $msg)] : false,
        ]);
    }

    /**
     * @throws \Exception
     */
    private function connectToRabbitMQ(): AMQPStreamConnection
    {
        $connection = new AMQPStreamConnection(
            $this->rabbitmqHost,
            $this->rabbitmqPort,
            $this->rabbitmqUser,
            $this->rabbitmqPassword,
            $this->rabbitmqVhost
        );
        $this->logger->info('Connected to RabbitMQ');
        return $connection;
    }

    /**
     * @param AMQPStreamConnection $connection
     *
     * @return AMQPChannel
     */
    private function createRabbitMQChannel(AMQPStreamConnection $connection): AMQPChannel
    {
        $channel = $connection->channel();
        $channel->queue_declare($this->rabbitmqQueue, false, true, false, false);
        $this->logger->info(__('messages.rabbitmq_queue_declared'), ['queue' => $this->rabbitmqQueue]);
        return $channel;
    }

    /**
     * @param AMQPChannel $channel
     *
     * @return void
     */
    private function consumeMessages(AMQPChannel $channel): void
    {
        $callback = function (AMQPMessage $msg) use ($channel) {
            $this->processMessage($msg, $channel);
        };
        $channel->basic_consume($this->rabbitmqQueue, '', false, false, false, false, $callback);
        $this->logger->info(__('messages.consumer_started'));
        $this->info(__('messages.consumer_started'));

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * @param AMQPMessage $message
     * @param AMQPChannel $channel
     *
     * @return void
     */
    private function processMessage(AMQPMessage $message, AMQPChannel $channel): void
    {
        try {
            $data = $this->decodeMessage($message);
            if (!$data) {
                $channel->basic_ack($message->delivery_info['delivery_tag']);
                return;
            }

            $this->logger->info(__('messages.rabbitmq_message_received'), ['file' => $data['csv_file_name'], 'path' => $data['csv_file_path']]);

            $this->validateFile($data['csv_file_path']);
            $this->ensureS3Bucket();
            $this->uploadToS3($data['csv_file_path'], $data['csv_file_name']);
            $this->deleteLocalFile($data['csv_file_path']);
            $this->sendConfirmationEmail($data['csv_file_name']);
            $channel->basic_ack($message->delivery_info['delivery_tag']);
            $this->info(__('messages.message_processed') . $data['csv_file_name']);
        } catch (\Exception $e) {
            $this->logMessageError($e, $message->body);
            $channel->basic_nack($message->delivery_info['delivery_tag']);
            $this->error(__('errors.process_message_failed') . $e->getMessage());
        }
    }

    /**
     * @param AMQPMessage $message
     *
     * @return array|null
     */
    private function decodeMessage(AMQPMessage $message): ?array
    {
        $data = json_decode($message->body, true);
        if (!is_array($data) || !isset($data['csv_file_path'], $data['csv_file_name'])) {
            $this->logger->error(__('errors.invalid_message_format'), ['message' => $message->body]);
            $this->warn('Skipped invalid message: ' . $message->body);
            return null;
        }
        return $data;
    }

    /**
     * @param string $filePath
     *
     * @return void
     * @throws \Exception
     */
    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception(__('errors.csv_file_not_found') . $filePath);
        }
    }

    /**
     * @return void
     */
    private function ensureS3Bucket(): void
    {
        if (!$this->s3->doesBucketExist($this->awsBucket)) {
            $this->s3->createBucket(['Bucket' => $this->awsBucket]);
            $this->s3->waitUntil('BucketExists', ['Bucket' => $this->awsBucket]);
            $this->logger->info(__('messages.s3_bucket_created'), ['bucket' => $this->awsBucket]);
        }
    }

    /**
     * @param string $filePath
     * @param string $fileName
     *
     * @return void
     */
    private function uploadToS3(string $filePath, string $fileName): void
    {
        $this->s3->putObject([
            'Bucket' => $this->awsBucket,
            'Key' => $fileName,
            'Body' => fopen($filePath, 'r'),
            'ContentType' => self::CSV_CONTENT_TYPE,
        ]);
        $this->logger->info(__('messages.csv_uploaded'), ['bucket' => $this->awsBucket, 'file' => $fileName]);
    }

    /**
     * @param string $filePath
     *
     * @return void
     */
    private function deleteLocalFile(string $filePath): void
    {
        unlink($filePath);
        $this->logger->info(__('messages.csv_deleted_locally'), ['path' => $filePath]);
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    private function sendConfirmationEmail(string $fileName): void
    {
        try {
            $emailContent = "The product catalog {$fileName} has been exported to S3:{$this->awsBucket}/{$fileName}";
            $this->logger->info(__('messages.preparing_to_send_email'), [
                'to' => $this->adminEmail,
                'from' => $this->fromEmail,
                'mailer' => config('mail.mailer'),
                'endpoint' => $this->awsEndpoint,
            ]);
            $this->mailer->raw($emailContent, function ($message) {
                $message->to($this->adminEmail)
                    ->subject(self::EMAIL_SUBJECT)
                    ->from($this->fromEmail, $this->fromName);
            });
            $this->logger->info(__('messages.confirmation_mail_sent'), ['email' => $this->adminEmail, 'content' => $emailContent]);
            $this->info('Sent email to: ' . $this->adminEmail);
        } catch (\Exception $e) {
            $this->logger->warning(__('errors.confirmation_email_sending_failed'), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error(__('errors.email_sending_failed') . $e->getMessage());
            $this->logger->info(__('messages.email_content'), [
                'to' => $this->adminEmail,
                'subject' => self::EMAIL_SUBJECT,
                'from' => $this->fromEmail,
                'content' => $emailContent,
            ]);
        }
    }

    /**
     * @param \Exception $e
     * @param string $messageContent
     *
     * @return void
     */
    private function logMessageError(\Exception $e, string $messageContent): void
    {
        $this->logger->error(__('errors.rabbitmq_process_failed'), [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'message' => $messageContent,
        ]);
    }

    /**
     * @param string $message
     * @param \Exception $e
     *
     * @return void
     */
    private function logError(string $message, \Exception $e): void
    {
        $this->logger->error($message, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * @param AMQPChannel $channel
     * @param AMQPStreamConnection $connection
     *
     * @return void
     * @throws \Exception
     */
    private function closeConnection(AMQPChannel $channel, AMQPStreamConnection $connection): void
    {
        $channel->close();
        $connection->close();
    }
}

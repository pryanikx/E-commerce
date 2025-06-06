<?php

namespace App\Jobs;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 *
 */
class PublishCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     *
     */
    private const RABBITMQ_DEFAULT_HOST = 'localhost';
    private const RABBITMQ_DEFAULT_PORT = 5672;
    private const RABBITMQ_DEFAULT_USER = 'guest';
    private const RABBITMQ_DEFAULT_PASSWORD = 'guest';
    private const RABBITMQ_DEFAULT_VHOST = '/';
    private const RABBITMQ_MESSAGE_QUEUE = 'catalog_messages';
    private const RABBITMQ_JOB_QUEUE = 'default';
    private const CSV_PREFIX = 'product-catalog-';
    private const CSV_EXTENSION = '.csv';
    private const STORAGE_PATH = 'app/';
    private const CSV_HEADERS = [
        'ID', 'Name', 'Article', 'Description', 'Release Date', 'Category Name',
        'Manufacturer Name', 'Price', 'Image URL', 'Maintenances',
    ];
    private const MESSAGE_DELIVERY_MODE = AMQPMessage::DELIVERY_MODE_PERSISTENT;

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
     * @var string $rabbitmqJobQueue
     */
    private string $rabbitmqJobQueue;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        private readonly LoggerInterface $logger
    ) {
        $this->initializeEnvVariables();
        $this->onQueue($this->rabbitmqJobQueue);
    }

    /**
     * Handle publishing catalog
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->logger->info(__('messages.starting_publishcatalogjob'));

        try {
            $csvFileName = $this->generateCsvFileName();
            $csvFilePath = $this->getCsvFilePath($csvFileName);
            $this->ensureStorageDirectoryWritable($csvFilePath);
            $this->generateCsvFile($csvFilePath, $csvFileName);
            $this->publishToRabbitMQ($csvFilePath, $csvFileName);
        } catch (\Exception $e) {
            $this->logError(__('errors.publishcatalogjob_failed'), $e);
            throw $e;
        }
    }

    /**
     * Initializes environment variables
     *
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
        $this->rabbitmqJobQueue = env('RABBITMQ_JOB_QUEUE', self::RABBITMQ_JOB_QUEUE);
    }

    /**
     * Generates CSV filename
     *
     * @return string
     */
    private function generateCsvFileName(): string
    {
        return self::CSV_PREFIX . date('Y-m-d_H-i-s') . self::CSV_EXTENSION;
    }

    /**
     * Gets CSV filepath
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getCsvFilePath(string $fileName): string
    {
        return storage_path(self::STORAGE_PATH . $fileName);
    }

    /**
     * Checks whether the storage directory is writable
     *
     * @param string $filePath
     *
     * @return void
     * @throws \Exception
     */
    private function ensureStorageDirectoryWritable(string $filePath): void
    {
        $storageDir = dirname($filePath);
        if (!is_writable($storageDir)) {
            $this->logger->error(__('errors.directory_is_not_writeable'), ['dir' => $storageDir]);
            throw new \Exception(__('errors.directory_is_not_writeable') . $storageDir);
        }
    }

    /**
     * Generates CSV file
     *
     * @param string $filePath
     * @param string $fileName
     *
     * @return void
     * @throws \Exception
     */
    private function generateCsvFile(string $filePath, string $fileName): void
    {
        $file = $this->openCsvFile($filePath, $fileName);
        $this->writeCsvHeaders($file);
        $this->writeProductData($file);
        fclose($file);
        $this->logger->info(__('messages.csv_generated'), ['file' => $fileName, 'path' => $filePath]);
    }

    /**
     * Opens CSV file
     *
     * @param string $filePath
     * @param string $fileName
     *
     * @return mixed
     * @throws \Exception
     */
    private function openCsvFile(string $filePath, string $fileName): mixed
    {
        $file = fopen($filePath, 'w');
        if (!$file) {
            $this->logger->error(__('errors.csv_creation_failed'), ['path' => $filePath]);
            throw new \Exception(__('errors.csv_creation_failed') . $fileName);
        }
        return $file;
    }

    /**
     * Writes headers to CSV file
     *
     * @param $file
     *
     * @return void
     */
    private function writeCsvHeaders($file): void
    {
        fputcsv($file, self::CSV_HEADERS);
        $this->logger->info(__('messages.csv_headers_written'), ['headers' => self::CSV_HEADERS]);
    }

    /**
     * Writes catalog data to CSV
     *
     * @param $file
     *
     * @return void
     */
    private function writeProductData($file): void
    {
        $products = $this->productRepository->all()->load(['category', 'manufacturer', 'maintenances']);
        $this->logger->info(__('messages.products_retrieved'), ['count' => $products->count()]);

        if ($products->isEmpty()) {
            $this->logger->info(__('messages.no_product'));
            return;
        }

        foreach ($products as $product) {
            $row = $this->formatProductRow($product);
            fputcsv($file, $row);
        }
    }

    /**
     * Formats product row
     *
     * @param $product
     *
     * @return array
     */
    private function formatProductRow($product): array
    {
        $maintenances = $product->maintenances->map(function ($m) {
            return [
                'name' => $m->name,
                'price' => (float) $m->pivot->price,
            ];
        })->toArray();

        return [
            $product->id,
            $product->name,
            $product->article,
            $product->description ?? '',
            $product->release_date ? $product->release_date->toDateString() : '',
            $product->category ? $product->category->name : '',
            $product->manufacturer ? $product->manufacturer->name : '',
            (float) $product->price,
            $product->image_path ? asset($product->image_path) : '',
            json_encode($maintenances),
        ];
    }

    /**
     * Publishes a message to RabbitMQ
     *
     * @param string $filePath
     * @param string $fileName
     *
     * @return void
     * @throws \Exception
     */
    private function publishToRabbitMQ(string $filePath, string $fileName): void
    {
        $connection = $this->connectToRabbitMQ();
        $channel = $this->createRabbitMQChannel($connection);
        $message = $this->createRabbitMQMessage($filePath, $fileName);
        $this->publishMessage($channel, $message);
        $this->closeRabbitMQConnection($channel, $connection);
    }

    /**
     * Created a connection to RabbitMQ
     *
     * @return AMQPStreamConnection
     * @throws \Exception
     */
    private function connectToRabbitMQ(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            $this->rabbitmqHost,
            $this->rabbitmqPort,
            $this->rabbitmqUser,
            $this->rabbitmqPassword,
            $this->rabbitmqVhost
        );
    }

    /**
     * Creates RabbitMQ channel
     *
     * @param AMQPStreamConnection $connection
     *
     * @return AMQPChannel
     */
    private function createRabbitMQChannel(AMQPStreamConnection $connection): AMQPChannel
    {
        $channel = $connection->channel();
        $channel->queue_declare($this->rabbitmqQueue, false, true, false, false);
        return $channel;
    }

    /**
     * Creates RabbitMQ message
     *
     * @param string $filePath
     * @param string $fileName
     *
     * @return AMQPMessage
     * @throws \Exception
     */
    private function createRabbitMQMessage(string $filePath, string $fileName): AMQPMessage
    {
        $messageBody = json_encode([
            'csv_file_path' => $filePath,
            'csv_file_name' => $fileName,
        ]);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error(__('errors.encode_json_failed'), ['error' => json_last_error_msg()]);
            throw new \Exception(__('errors.encode_json_failed') . json_last_error_msg());
        }

        $this->logger->info(__('messages.rabbitmq_message_created'), ['message' => $messageBody]);
        return new AMQPMessage($messageBody, ['delivery_mode' => self::MESSAGE_DELIVERY_MODE]);
    }

    /**
     * Publishes message
     *
     * @param AMQPChannel $channel
     * @param AMQPMessage $message
     *
     * @return void
     */
    private function publishMessage(AMQPChannel $channel, AMQPMessage $message): void
    {
        $channel->basic_publish($message, '', $this->rabbitmqQueue);
        $this->logger->info(__('messages.published_to_rabbitmq'), ['queue' => $this->rabbitmqQueue]);
    }

    /**
     * Closes RabbitMQ connection
     *
     * @throws \Exception
     */
    private function closeRabbitMQConnection(AMQPChannel $channel, AMQPStreamConnection $connection): void
    {
        $channel->close();
        $connection->close();
    }

    /**
     * Helper method for logging errors
     *
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
}

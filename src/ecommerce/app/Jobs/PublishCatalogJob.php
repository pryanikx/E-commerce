<?php

namespace App\Jobs;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class PublishCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(protected ProductRepositoryInterface $productRepository, private readonly LoggerInterface $logger)
    {
        $this->onQueue(env('RABBITMQ_JOB_QUEUE', 'default'));
    }

    /**
     * Handle publish catalog
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->logger->info('Starting PublishCatalogJob');

        $csvFileName = 'product-catalog-' . date('Y-m-d_H-i-s') . '.csv';
        $csvFilePath = storage_path('app/' . $csvFileName);

        try {
            $storageDir = dirname($csvFilePath);
            if (!is_writable($storageDir)) {
                $this->logger->error('Storage directory is not writable', ['dir' => $storageDir]);
                throw new \Exception(__('errors.directory_is_not_writeable') . $storageDir);
            }

            $file = fopen($csvFilePath, 'w');
            if (!$file) {
                $this->logger->error('Failed to open CSV file for writing', ['path' => $csvFilePath]);
                throw new \Exception(__('errors.csv_creation_failed'). $csvFilePath);
            }

            $headers = [
                'ID', 'Name', 'Article', 'Description', 'Release Date', 'Category Name',
                'Manufacturer Name', 'Price', 'Image URL', 'Maintenances',
            ];
            fputcsv($file, $headers);
            $this->logger->info('CSV headers written', ['headers' => $headers]);

            $products = $this->productRepository->all()->load(['category', 'manufacturer', 'maintenances']);
            $this->logger->info('Retrieved products', ['count' => $products->count()]);

            if ($products->isEmpty()) {
                $this->logger->warning('No products found for export');
            }

            foreach ($products as $product) {
                $maintenances = $product->maintenances->map(fn ($m) => [
                    'name' => $m->name,
                    'price' => (float) $m->pivot->price,
                ])->toArray();
                $row = [
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
                fputcsv($file, $row);
            }
            fclose($file);
            $this->logger->info('CSV file generated', ['file' => $csvFileName, 'path' => $csvFilePath]);

            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest'),
                env('RABBITMQ_VHOST', '/')
            );
            $channel = $connection->channel();
            $queue = env('RABBITMQ_MESSAGE_QUEUE', 'catalog_messages');
            $channel->queue_declare($queue, false, true, false, false);

            $messageBody = json_encode([
                'csv_file_path' => $csvFilePath,
                'csv_file_name' => $csvFileName,
            ]);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Failed to encode JSON message', ['error' => json_last_error_msg()]);
                throw new \Exception(__('errors.encode_json_failed') . json_last_error_msg());
            }

            $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($message, '', $queue);
            $this->logger->info('Published message to RabbitMQ', ['queue' => $queue, 'message' => $messageBody]);

            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            $this->logger->error('PublishCatalogJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

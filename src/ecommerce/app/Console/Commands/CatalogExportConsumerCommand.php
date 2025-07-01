<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CatalogExportConsumerCommand extends Command
{
    private const DEFAULT_TIMEOUT = 600;
    private const DEFAULT_TRIES = 3;
    private const DEFAULT_MEMORY = 512;
    private const QUEUE_CONNECTION = 'rabbitmq';
    private const QUEUE_NAME = 'catalog_export';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:export-consumer
                            {--timeout=' . self::DEFAULT_TIMEOUT . ' : Timeout for each job in seconds}
                            {--tries=' . self::DEFAULT_TRIES . ' : Number of tries for failed jobs}
                            {--memory=' . self::DEFAULT_MEMORY . ' : Memory limit in MB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start consuming catalog export jobs from RabbitMQ queue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $config = $this->getConfiguration();

        $this->displayStartupInfo($config);

        try {
            $this->startQueueWorker($config);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error(__('commands.consumer_start_error', ['error' => $e->getMessage()]));

            return self::FAILURE;
        }
    }

    /**
     * Get configuration from command options
     *
     * @return array
     */
    private function getConfiguration(): array
    {
        return [
            'timeout' => (int) $this->option('timeout'),
            'tries' => (int) $this->option('tries'),
            'memory' => (int) $this->option('memory'),
        ];
    }

    /**
     * Display startup information
     *
     * @param array $config
     */
    private function displayStartupInfo(array $config): void
    {
        $this->info(__('commands.starting_catalog_export_consumer'));
        $this->info(__('commands.configuration') . ':');
        $this->info(__('commands.timeout_config', ['timeout' => $config['timeout']]));
        $this->info(__('commands.max_tries_config', ['tries' => $config['tries']]));
        $this->info(__('commands.memory_limit_config', ['memory' => $config['memory']]));
        $this->info(__('commands.queue_config', ['queue' => self::QUEUE_NAME]));
        $this->line('');
    }

    /**
     * Start the queue worker
     *
     * @param array $config
     */
    private function startQueueWorker(array $config): void
    {
        $this->call('queue:work', [
            'connection' => self::QUEUE_CONNECTION,
            '--queue' => self::QUEUE_NAME,
            '--timeout' => $config['timeout'],
            '--tries' => $config['tries'],
            '--memory' => $config['memory'],
            '--verbose' => true,
        ]);
    }
}

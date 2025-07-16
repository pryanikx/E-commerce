<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use App\Services\Email\EmailHtmlBuilder;
use App\Services\Email\EmailDirectoryManager;
use App\Services\Email\EmailFileLogger;
use App\Services\Email\EmailNotificationService;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register email-related services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(EmailHtmlBuilder::class, function () {
            return new EmailHtmlBuilder();
        });

        $this->app->singleton(EmailDirectoryManager::class, function ($app) {
            return new EmailDirectoryManager(
                $app->make(Filesystem::class)
            );
        });

        $this->app->singleton(EmailFileLogger::class, function ($app) {
            return new EmailFileLogger(
                $app->make(Filesystem::class),
                $app->make(EmailHtmlBuilder::class),
                $app->make(EmailDirectoryManager::class),
                $app->make(ClockInterface::class),
                storage_path(config('services.email_notification.email_log_directory', 'app/emails')),
                config('services.email_notification.email_file_prefix', 'email_'),
                config('services.email_notification.default_from_email', 'noreply@example.com'),
            );
        });

        $this->app->singleton(EmailNotificationService::class, function ($app) {
            return new EmailNotificationService(
                $app->make(LoggerInterface::class),
                $app->make(EmailFileLogger::class),
                $app->make(ClockInterface::class),
            );
        });
    }

    /**
     * Bootstrap email-related services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}

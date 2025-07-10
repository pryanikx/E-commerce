<?php

declare(strict_types=1);

namespace App\Services\Email;

use Illuminate\Contracts\Filesystem\Filesystem;

class EmailDirectoryManager
{
    private const DIRECTORY_PERMISSIONS = 0755;

    public function __construct(private Filesystem $filesystem) {}

    public function ensureDirectoryExists(string $path): void
    {
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->makeDirectory($path);
        }
    }
} 
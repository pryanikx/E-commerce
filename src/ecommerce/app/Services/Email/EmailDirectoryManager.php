<?php

declare(strict_types=1);

namespace App\Services\Email;

use Illuminate\Contracts\Filesystem\Filesystem;

class EmailDirectoryManager
{
    /**
     * @param Filesystem $filesystem
     */
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    /**
     * Ensure the directory from $path exists.
     *
     * @param string $path
     *
     * @return void
     */
    public function ensureDirectoryExists(string $path): void
    {
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->makeDirectory($path);
        }
    }
}

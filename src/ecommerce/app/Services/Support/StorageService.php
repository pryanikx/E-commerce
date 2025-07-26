<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Support\Facades\Storage;

class StorageService implements StorageServiceInterface
{
    /**
     * Generate a path for storage
     *
     * @param string $path
     * @return string
     */
    public function path(string $path): string
    {
        return Storage::path($path);
    }

    /**
     * Check if the directory exists
     *
     * @param string $directory
     * @return bool
     */
    public function exists(string $directory): bool
    {
        return Storage::exists($directory);
    }

    /**
     * Create a directory
     *
     * @param string $directory
     * @return bool
     */
    public function makeDirectory(string $directory): bool
    {
        return Storage::makeDirectory($directory);
    }
}

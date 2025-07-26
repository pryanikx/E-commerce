<?php

declare(strict_types=1);

namespace App\Services\Support;

interface StorageServiceInterface
{
    /**
     * Generate a path for storage
     *
     * @param string $path
     * @return string
     */
    public function path(string $path): string;

    /**
     * Check if the directory exists
     *
     * @param string $directory
     * @return bool
     */
    public function exists(string $directory): bool;

    /**
     * Create a directory
     *
     * @param string $directory
     * @return bool
     */
    public function makeDirectory(string $directory): bool;
}

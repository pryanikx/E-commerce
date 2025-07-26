<?php

declare(strict_types=1);

namespace App\Services\Support;

interface StorageUploaderInterface
{
    /**
     * Download a file from storage to upload
     *
     * @param string $filePath
     * @param string $exportId
     *
     * @return string|null
     */
    public function uploadCatalogExport(string $filePath, string $exportId): ?string;
}

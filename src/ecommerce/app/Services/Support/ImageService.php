<?php

declare(strict_types=1);

namespace App\Services\Support;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\UploadedFile;
use Psr\Log\LoggerInterface;

class ImageService
{
    private const STORAGE_DISK = 'public';
    private const STORAGE_PREFIX = '/storage';

    public function __construct(
        private FilesystemFactory $filesystem,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Store an uploaded file and return its path.
     *
     * @param mixed $image
     * @param string|null $oldPath
     *
     * @return string|null
     */
    public function handleImagePath(mixed $image, ?string $oldPath = null): ?string
    {
        if ($image instanceof UploadedFile && $image->isValid()) {
            try {
                $this->deleteOldImageIfExists($oldPath);
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, self::STORAGE_DISK);

                return $path ? self::STORAGE_PREFIX . $path : null;
            } catch (\Exception $e) {
                $this->logger->error(
                    __('errors.store_image_failed', ['error' => $e->getMessage()])
                );

                return $oldPath;
            }
        }

        return $oldPath;
    }

    /**
     * Check if the image path exists and return its URL or null.
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public function getImageUrlOrNull(?string $imagePath): ?string
    {
        if (
            $imagePath &&
            $imagePath !== '/' &&
            $this->filesystem
                ->disk(self::STORAGE_DISK)
                ->exists(str_replace(self::STORAGE_PREFIX, '', $imagePath))
        ) {
            return asset($imagePath);
        }

        return null;
    }

    /**
     * Delete old image if it exists and is not the fallback image.
     *
     * @param string|null $oldPath
     *
     * @return void
     */
    private function deleteOldImageIfExists(?string $oldPath): void
    {
        if (
            $oldPath &&
            $this->filesystem
                ->disk(self::STORAGE_DISK)
                ->exists(str_replace(self::STORAGE_PREFIX, '', $oldPath))
        ) {
            $this->filesystem
                ->disk(self::STORAGE_DISK)
                ->delete(str_replace(self::STORAGE_PREFIX, '', $oldPath));
        }
    }
}

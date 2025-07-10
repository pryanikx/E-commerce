<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductListDTO;
use App\DTO\Product\ProductShowDTO;
use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\DTO\Product\ProductDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Currency\CurrencyCalculatorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class ProductService
{
    private const CACHE_TTL_MINUTES = 30;
    private const STORAGE_DISK = 'public';

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CurrencyCalculatorService $currencyCalculator
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     * @param ClockInterface $clock
     * @param FilesystemFactory $filesystem
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyCalculatorService $currencyCalculator,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private ClockInterface $clock,
        private FilesystemFactory $filesystem,
    )
    {
    }

    /**
     * Get all products with pagination.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        $products = $this->productRepository->all();
        return [
            'data' => array_map(fn($product) => $this->makeProductListDTO($product)->toArray(), $products),
        ];
    }

    /**
     * Get one product by ID with caching.
     *
     * @param int $id
     *
     * @return array|null
     * @throws \Exception
     */
    public function getProduct(int $id): ?array
    {
        $cacheKey = $this->getProductCacheKey($id);

        return $this->cache->remember(
            $cacheKey,
            $this->clock->now()->add(new \DateInterval('PT' . self::CACHE_TTL_MINUTES . 'M')
            ),

            function () use ($id) {
                try {
                    $product = $this->productRepository->find($id);
                    $dto = $this->makeProductShowDTO($product);
                    $dtoArray = $dto->toArray();
                    $dtoArray['image_url'] = $this->getImageUrlOrNull($product->image_path);

                    return $dtoArray;
                } catch (ModelNotFoundException $e) {

                    return null;
                }
            });
    }

    /**
     * Create a new product.
     *
     * @param array $requestValidated
     *
     * @return ProductDTO
     */
    public function createProduct(array $requestValidated): ProductDTO
    {
        $dto = new ProductStoreDTO($requestValidated);
        $image_path = $this->handleImagePath($dto->image);
        $created_product = $this->productRepository->create([
            'name' => $dto->name,
            'article' => $dto->article,
            'description' => $dto->description,
            'release_date' => $dto->release_date,
            'price' => $dto->price,
            'image_path' => $image_path ?? null,
            'manufacturer_id' => $dto->manufacturer_id,
            'category_id' => $dto->category_id,
        ]);

        if (!empty($dto->maintenances)) {
            $this->productRepository->attachMaintenances($created_product->id, $dto->maintenances);
        }

        $this->cacheProduct($created_product->id);

        return $created_product;
    }

    /**
     * Update an existing product by ID.
     *
     * @param int $id
     * @param array $requestValidated
     * @return ProductDTO
     */
    public function updateProduct(int $id, array $requestValidated): ProductDTO
    {
        $product = $this->productRepository->find($id);
        $dto = new ProductUpdateDTO($requestValidated);
        $data = $this->prepareUpdateData($dto, $product);
        $this->productRepository->update($id, $data);

        if ($dto->maintenances !== null) {
            $this->productRepository->attachMaintenances($id, $dto->maintenances);
        }

        $this->refreshProductCache($id);

        return $this->productRepository->find($id);
    }

    /**
     * Delete an existing product by ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteProduct(int $id): bool
    {
        $this->cache->forget($this->getProductCacheKey($id));

        return $this->productRepository->delete($id);
    }

    /**
     * Prepare data for product update.
     *
     * @param ProductUpdateDTO $dto
     * @param ProductDTO $product
     *
     * @return array
     */
    private function prepareUpdateData(ProductUpdateDTO $dto, ProductDTO $product): array
    {
        return [
            'name' => $dto->name ?? $product->name,
            'article' => $dto->article ?? $product->article,
            'description' => $dto->description ?? $product->description,
            'release_date' => $dto->release_date ?? $product->release_date,
            'price' => $dto->price ?? $product->price,
            'manufacturer_id' => $dto->manufacturer_id ?? $product->manufacturer_id,
            'category_id' => $dto->category_id ?? $product->category_id,
            'image_path' => $dto->image !== null ?
                $this->handleImagePath($dto->image, $product->image_path) :
                $product->image_path,
        ];
    }

    /**
     * Store an uploaded file and return its path.
     *
     * @param UploadedFile|null $image
     * @param string|null $oldPath
     *
     * @return string|null
     */
    private function handleImagePath(?UploadedFile $image, ?string $oldPath = null): ?string
    {
        if ($image && $image->isValid()) {
            try {
                $this->deleteOldImageIfExists($oldPath);
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, self::STORAGE_DISK);

                return $path ? 'storage/' . $path : null;
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
            $this->filesystem->disk(self::STORAGE_DISK)->exists(str_replace('storage/', '', $oldPath))
        ) {
            $this->filesystem->disk(self::STORAGE_DISK)->delete(str_replace('storage/', '', $oldPath));
        }
    }

    /**
     * Check if the image path exists and return its URL or null.
     *
     * @param string|null $imagePath
     * @return string|null
     */
    private function getImageUrlOrNull(?string $imagePath): ?string
    {
        if (
            $imagePath &&
            $imagePath !== '/' &&
            $this->filesystem->disk(
                self::STORAGE_DISK)->exists(str_replace('storage/', '', $imagePath))
        ) {
            return asset($imagePath);
        }

        return null;
    }

    /**
     * Refresh product cache.
     *
     * @param int $id
     *
     * @return void
     */
    private function refreshProductCache(int $id): void
    {
        $this->cache->forget($this->getProductCacheKey($id));
        $this->cacheProduct($id);
    }

    /**
     * Save product in cache.
     *
     * @param int $id
     *
     * @return void
     */
    private function cacheProduct(int $id): void
    {
        try {
            $product = $this->productRepository->find($id);
            $dto = $this->makeProductShowDTO($product);
            $dtoArray = $dto->toArray();
            $dtoArray['image_url'] = $this->getImageUrlOrNull($product->image_path);

            $this->cache->put(
                $this->getProductCacheKey($id),
                $dtoArray,
                $this->clock->now()->add(new \DateInterval('PT' . self::CACHE_TTL_MINUTES . 'M'))
            );
        } catch (\Exception $e) {
            $this->logger->error(__('errors.cache_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Convert Product model to ProductShowDTO.
     *
     * @param ProductDTO $product
     *
     * @return ProductShowDTO
     */
    private function makeProductShowDTO(ProductDTO $product): ProductShowDTO
    {
        return new ProductShowDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->description,
            $product->release_date instanceof Carbon ?
                $product->release_date->toDateString() :
                (string)$product->release_date,
            $product->category_name,
            $product->manufacturer_name,
            $product->price ? $this->currencyCalculator->convert($product->price) : null,
            $this->getImageUrlOrNull($product->image_path),
            [],
        );
    }

    /**
     * Convert Product model to ProductListDTO.
     *
     * @param ProductDTO $product
     *
     * @return ProductListDTO
     */
    private function makeProductListDTO(ProductDTO $product): ProductListDTO
    {
        return new ProductListDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->manufacturer_name,
            $product->price ? $this->currencyCalculator->convert((float) $product->price) : null,
            $this->getImageUrlOrNull($product->image_path),
        );
    }

    /**
     * Get cache key for one product.
     *
     * @param int $id
     *
     * @return string
     */
    private function getProductCacheKey(int $id): string
    {
        return "product_{$id}";
    }
}

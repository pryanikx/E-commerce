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
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;
use Illuminate\Contracts\Cache\Repository as CacheInterface;

class ProductService
{
    private const FALLBACK_IMAGE_PATH = 'storage/products/fallback_image1.png';
    private const CACHE_TTL_MINUTES = 30;
    private const STORAGE_DISK = 'public';

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CurrencyCalculatorService $currencyCalculator
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CurrencyCalculatorService $currencyCalculator,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private \Psr\Clock\ClockInterface $clock,
    ) {}

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
     */
    public function getProduct(int $id): ?array
    {
        $cacheKey = $this->getProductCacheKey($id);
        return $this->cache->remember($cacheKey, $this->clock->now()->add(new \DateInterval('PT' . self::CACHE_TTL_MINUTES . 'M')),
            function () use ($id) {
                try {
                    $product = $this->productRepository->find($id);
                    $dto = $this->makeProductShowDTO($product);
                    $dtoArray = $dto->toArray();
                    $dtoArray['image_url'] = $this->getImageUrlWithFallback($product->image_path);
                    return $dtoArray;
                } catch (ModelNotFoundException $e) {
                    return null;
                }
            });
    }

    /**
     * Create a new product.
     *
     * @param array $request_validated
     * 
     * @return ProductDTO
     */
    public function createProduct(array $request_validated): ProductDTO
    {
        $dto = new ProductStoreDTO($request_validated);
        $image_path = $this->handleImagePath($dto->image);
        $created_product = $this->productRepository->create([
            'name' => $dto->name,
            'article' => $dto->article,
            'description' => $dto->description,
            'release_date' => $dto->release_date,
            'price' => $dto->price,
            'image_path' => $image_path ?? self::FALLBACK_IMAGE_PATH,
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
     * @param array $request_validated
     * @return ProductDTO
     */
    public function updateProduct(int $id, array $request_validated): ProductDTO
    {
        $product = $this->productRepository->find($id);
        $dto = new ProductUpdateDTO($request_validated);
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
            'image_path' => $dto->image !== null ? $this->handleImagePath($dto->image, $product->image_path) : $product->image_path,
        ];
    }

    /**
     * Store an uploaded file and return its path.
     *
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param string|null $oldPath
     * 
     * @return string
     */
    private function handleImagePath(?\Illuminate\Http\UploadedFile $image, ?string $oldPath = null): string
    {
        if ($image && $image->isValid()) {
            try {
                $this->deleteOldImageIfExists($oldPath);

                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $filename, self::STORAGE_DISK);

                return $path ? 'storage/' . $path : $this->getFallbackImagePath();
            } catch (\Exception $e) {
                $this->logger->error(
                    __('errors.store_image_failed', ['error' => $e->getMessage()])
                );
                return $oldPath ?? $this->getFallbackImagePath();
            }
        }

        return $oldPath ?? $this->getFallbackImagePath();
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
        if ($oldPath &&
            $oldPath !== $this->getFallbackImagePath() &&
            Storage::disk(self::STORAGE_DISK)->exists(str_replace('storage/', '', $oldPath))
        ) {
            Storage::disk(self::STORAGE_DISK)->delete(str_replace('storage/', '', $oldPath));
        }
    }

    /**
     * Get fallback image path.
     *
     * @return string
     */
    private function getFallbackImagePath(): string
    {
        return self::FALLBACK_IMAGE_PATH;
    }

    /**
     * Check if the image path exists and return its URL or the fallback image URL.
     *
     * @param string|null $imagePath
     * @return string
     */
    private function getImageUrlWithFallback(?string $imagePath): string
    {
        $fallbackUrl = asset($this->getFallbackImagePath());

        if ($imagePath &&
            $imagePath !== '/' &&
            Storage::disk(self::STORAGE_DISK)->exists(str_replace('storage/', '', $imagePath))
        ) {
            return asset($imagePath);
        }

        return $fallbackUrl;
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
            $dtoArray['image_url'] = $this->getImageUrlWithFallback($product->image_path);

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
     * @param \App\DTO\Product\ProductDTO $product
     * 
     * @return \App\DTO\Product\ProductShowDTO
     */
    private function makeProductShowDTO(ProductDTO $product): \App\DTO\Product\ProductShowDTO
    {
        return new \App\DTO\Product\ProductShowDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->description,
            $product->release_date instanceof \Illuminate\Support\Carbon ? $product->release_date->toDateString() : (string)$product->release_date,
            $product->category_name,
            $product->manufacturer_name,
            $product->price ? $this->currencyCalculator->convert((float) $product->price) : null,
            $product->image_path ? $this->getImageUrlWithFallback($product->image_path) : null,
            [], // maintenances — если нужно, добавить в DTO и mapToDTO
        );
    }

    /**
     * Convert Product model to ProductListDTO.
     *
     * @param \App\DTO\Product\ProductDTO $product
     * 
     * @return \App\DTO\Product\ProductListDTO
     */
    private function makeProductListDTO(ProductDTO $product): \App\DTO\Product\ProductListDTO
    {
        return new \App\DTO\Product\ProductListDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->manufacturer_name,
            $product->price ? $this->currencyCalculator->convert((float) $product->price) : null,
            $product->image_path ? $this->getImageUrlWithFallback($product->image_path) : null,
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

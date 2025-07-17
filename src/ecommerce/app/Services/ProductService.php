<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductDTO;
use App\DTO\Product\ProductListDTO;
use App\DTO\Product\ProductShowDTO;
use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Currency\CurrencyCalculatorService;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

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
    ) {
    }

    /**
     * Get statistics for products.
     *
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return $this->productRepository->getStats();
    }

    /**
     * Get all products.
     *
     * @return array<int, array<string, mixed>>|null
     */
    public function getAll(): ?array
    {
        $products = $this->productRepository->all();

        return array_map(fn ($product) => $this->makeProductListDTO($product)->toArray(), $products);
    }

    /**
     * Get one product by ID with caching.
     *
     * @param int $id
     *
     * @return array<string, mixed>|null
     * @throws \Exception
     */
    public function getProduct(int $id): ?array
    {
        $cacheKey = $this->getProductCacheKey($id);

        return $this->cache->remember(
            $cacheKey,
            $this->clock->now()->add(new \DateInterval('PT' . self::CACHE_TTL_MINUTES . 'M')),
            function () use ($id) {
                try {
                    $product = $this->productRepository->find($id);
                    $dto = $this->makeProductShowDTO($product);

                    return $dto->toArray();
                } catch (ModelNotFoundException $e) {
                    return null;
                }
            }
        );
    }

    /**
     * Create a new product.
     *
     * @param array<string, mixed> $requestValidated
     * @return ProductDTO
     */
    public function createProduct(array $requestValidated): ProductDTO
    {
        $dto = new ProductStoreDTO(
            name: $requestValidated['name'],
            article: $requestValidated['article'],
            description: $requestValidated['description'],
            releaseDate: $requestValidated['release_date'],
            price: $requestValidated['price'],
            image: $requestValidated['image'] ?? null,
            manufacturerId: $requestValidated['manufacturer_id'],
            categoryId: $requestValidated['category_id'],
            maintenances: $requestValidated['maintenance_ids'] ?? [],
        );

        $imagePath = $this->handleImagePath($dto->image);
        $processedMaintenances = $this->processMaintenances($dto->maintenances);

        $createdProduct = $this->productRepository->create([
            'name' => $dto->name,
            'article' => $dto->article,
            'description' => $dto->description,
            'release_date' => $dto->releaseDate,
            'price' => $dto->price,
            'image_path' => $imagePath,
            'manufacturer_id' => $dto->manufacturerId,
            'category_id' => $dto->categoryId,
        ]);

        if (!empty($processedMaintenances)) {
            $this->productRepository->attachMaintenances($createdProduct->id, $processedMaintenances);
        }

        $this->cacheProduct($createdProduct->id);
        return $createdProduct;
    }

    /**
     * Update an existing product by ID.
     *
     * @param int $id
     * @param array<string, mixed> $requestValidated
     * @return ProductDTO
     */
    public function updateProduct(int $id, array $requestValidated): ProductDTO
    {
        $product = $this->productRepository->find($id);

        $dto = new ProductUpdateDTO(
            name: $requestValidated['name'] ?? null,
            article: $requestValidated['article'] ?? null,
            description: $requestValidated['description'] ?? null,
            releaseDate: $requestValidated['release_date'] ?? null,
            price: $requestValidated['price'] ?? null,
            image: $requestValidated['image'] ?? null,
            manufacturerId: $requestValidated['manufacturer_id'] ?? null,
            categoryId: $requestValidated['category_id'] ?? null,
            maintenances: $requestValidated['maintenance_ids'] ?? null,
        );

        $updateData = [
            'name' => $dto->name ?? $product->name,
            'article' => $dto->article ?? $product->article,
            'description' => $dto->description ?? $product->description,
            'release_date' => $dto->releaseDate ?? $product->releaseDate,
            'price' => $dto->price ?? $product->price,
            'manufacturer_id' => $dto->manufacturerId ?? $product->manufacturerId,
            'category_id' => $dto->categoryId ?? $product->categoryId,
            'image_path' => $dto->image !== null ?
                $this->handleImagePath($dto->image, $product->imagePath) :
                $product->imagePath,
        ];

        $this->productRepository->update($id, $updateData);

        if ($dto->maintenances !== null) {
            $processedMaintenances = $this->processMaintenances($dto->maintenances);
            $this->productRepository->attachMaintenances($id, $processedMaintenances);
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
        $isDeleted = $this->productRepository->delete($id);

        $this->cache->forget($this->getProductCacheKey($id));

        return $isDeleted;
    }

    /**
     * Convert a Product model to ProductShowDTO.
     *
     * @param ProductDTO $product
     *
     * @return ProductShowDTO
     */
    private function makeProductShowDTO(ProductDTO $product): ProductShowDTO
    {
        $maintenances = [];

        if ($product->maintenances) {
            $maintenances = collect($product->maintenances)->map(function (array $maintenance) {
                return [
                    'name' => $maintenance['name'],
                    'prices' => $this->currencyCalculator->convert((float) $maintenance['pivot']['price']),
                ];
            })->toArray();
        }

        return new ProductShowDTO(
            $product->id,
            $product->name,
            $product->article,
            $product->description ?? '',
            $product->releaseDate ?? '',
            $product->categoryName ?? '',
            $product->manufacturerName ?? '',
            $product->price ? $this->currencyCalculator->convert($product->price) : null,
            $this->getImageUrlOrNull($product->imagePath),
            $maintenances,
        );
    }

    /**
     * Convert a Product model to ProductListDTO.
     *
     * @param ProductDTO $product
     *
     * @return ProductListDTO
     */
    private function makeProductListDTO(ProductDTO $product): ProductListDTO
    {
        return new ProductListDTO(
            id: $product->id,
            name: $product->name,
            article: $product->article,
            manufacturerName: $product->manufacturerName ?? '',
            prices: $product->price ? $this->currencyCalculator->convert((float) $product->price) : null,
            imageUrl: $this->getImageUrlOrNull($product->imagePath),
        );
    }

    /**
     * Store an uploaded file and return its path.
     *
     * @param mixed $image
     * @param string|null $oldPath
     *
     * @return string|null
     */
    private function handleImagePath(mixed $image, ?string $oldPath = null): ?string
    {
        if ($image instanceof UploadedFile && $image->isValid()) {
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
            $this->filesystem->disk(
                self::STORAGE_DISK
            )->exists(
                str_replace('storage/', '', $oldPath)
            )
        ) {
            $this->filesystem->disk(
                self::STORAGE_DISK
            )->delete(
                str_replace('storage/', '', $oldPath)
            );
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
                self::STORAGE_DISK
            )->exists(str_replace('storage/', '', $imagePath))
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
     * Get a cache key for one product.
     *
     * @param int $id
     *
     * @return string
     */
    private function getProductCacheKey(int $id): string
    {
        return "product_{$id}";
    }

    /**
     * Process maintenance IDs with proper typing.
     *
     * @param mixed $maintenanceIds
     * @return array<int, mixed>
     */
    private function processMaintenances(mixed $maintenanceIds): array
    {
        if (!is_array($maintenanceIds) || empty($maintenanceIds)) {
            return [];
        }

        $result = [];

        foreach ($maintenanceIds as $maintenance) {
            if (is_array($maintenance) && isset($maintenance['id'], $maintenance['price'])) {
                $id = (int) $maintenance['id'];
                $price = (float) $maintenance['price'];
                $result[$id] = ['price' => $price];
            }
        }

        return $result;
    }
}

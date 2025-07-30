<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductDTO;
use App\DTO\Product\ProductStatsDTO;
use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Exceptions\DeleteDataException;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Support\ImageService;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    private const CACHE_TTL = 300;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductTransformer $transformer
     * @param ImageService $imageService
     * @param CacheInterface $cache
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductTransformer $transformer,
        private ImageService $imageService,
        private CacheInterface $cache,
    ) {
    }

    /**
     * Get statistics for products.
     *
     * @return ProductStatsDTO
     */
    public function getStats(): ProductStatsDTO
    {
        return $this->productRepository->getStats();
    }

    /**
     * Get all products.
     *
     * @return ProductDTO[]|null
     */
    public function getAll(): ?array
    {
        $products = $this->productRepository->all();

        return $products ? $this->transformer->transformCollection($products) : null;
    }

    /**
     * Get one product by ID with caching.
     *
     * @param int $id
     *
     * @return ProductDTO|null
     * @throws \Exception
     */
    public function getProduct(int $id): ?ProductDTO
    {
        $cacheKey = $this->getProductCacheKey($id);

        return $this->cache->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($id) {
                try {
                    $product = $this->productRepository->find($id);

                    return $this->transformer->transform($product);
                } catch (ModelNotFoundException $e) {
                    return null;
                }
            }
        );
    }

    /**
     * Create a new product.
     *
     * @param ProductStoreDTO $dto
     *
     * @return ProductDTO
     */
    public function createProduct(ProductStoreDTO $dto): ProductDTO
    {
        $dto->image = $this->imageService->handleImagePath($dto->image);
        $dto->maintenances = $this->transformer
            ->formatMaintenancesForStorage($dto->maintenances);

        $createdProduct = $this->productRepository->create($dto);

        if (!empty($dto->maintenances)) {
            $this->productRepository->attachMaintenances($createdProduct->id, $dto->maintenances);
        }

        $transformedProduct = $this->transformer->transform($createdProduct);
        $this->cache->put($this->getProductCacheKey($createdProduct->id), $transformedProduct);

        return $transformedProduct;
    }

    /**
     * Update an existing product by ID.
     *
     * @param ProductUpdateDTO $dto
     *
     * @return ProductDTO
     */
    public function updateProduct(ProductUpdateDTO $dto): ProductDTO
    {
        $product = $this->productRepository->find($dto->id);

        $dto->name ??= $product->name;
        $dto->article ??= $product->article;
        $dto->description ??= $product->description;
        $dto->releaseDate ??= $product->releaseDate;
        $dto->price ??= $product->price;
        $dto->manufacturerId ??= $product->manufacturerId;
        $dto->categoryId ??= $product->categoryId;
        $dto->image = $dto->image !== null
            ? $this->imageService->handleImagePath($dto->image, $product->imagePath)
            : $product->imagePath;

        $this->productRepository->update($dto);

        if ($dto->maintenances !== null) {
            $processedMaintenances = $this->transformer->
            formatMaintenancesForStorage($dto->maintenances);

            $this->productRepository->attachMaintenances($dto->id, $processedMaintenances);
        }

        $updatedProduct = $this->productRepository->find($dto->id);
        $transformedProduct = $this->transformer->transform($updatedProduct);

        $this->cache->put($this->getProductCacheKey($dto->id), $transformedProduct);

        return $transformedProduct;
    }

    /**
     * Delete an existing product by ID.
     *
     * @param int $id
     *
     * @return void
     * @throws DeleteDataException
     */
    public function deleteProduct(int $id): void
    {
        if (!$this->productRepository->delete($id)) {
            throw new DeleteDataException(__('errors.deletion_failed', ['id' => $id]));
        }

        $this->cache->forget($this->getProductCacheKey($id));
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
}

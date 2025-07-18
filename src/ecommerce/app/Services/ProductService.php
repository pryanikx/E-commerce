<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Support\ImageService;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Log\LoggerInterface;

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
     * @return array<string, int>
     */
    public function getStats(): array
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
     * @param array<string, mixed> $requestValidated
     * @return ProductDTO
     */
    public function createProduct(array $requestValidated): ProductDTO
    {
        $imagePath = $this->imageService->handleImagePath($requestValidated['image']);
        $processedMaintenances = $this->transformer->
        processMaintenancesForSave($requestValidated['maintenances']);

        $createdProduct = $this->productRepository->create([
            'name' => $requestValidated['name'],
            'article' => $requestValidated['article'],
            'description' => $requestValidated['description'],
            'release_date' => $requestValidated['release_date'],
            'price' => $requestValidated['price'],
            'image_path' => $imagePath,
            'manufacturer_id' => $requestValidated['manufacturer_id'],
            'category_id' => $requestValidated['category_id'],
        ]);

        if (!empty($processedMaintenances)) {
            $this->productRepository->attachMaintenances($createdProduct->id, $processedMaintenances);
        }

        $transformedProduct = $this->transformer->transform($createdProduct);
        $this->cache->put($this->getProductCacheKey($createdProduct->id), $transformedProduct);

        return $transformedProduct;
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

        $updateData = [
            'name' => $requestValidated['name'] ?? $product->name,
            'article' => $requestValidated['article'] ?? $product->article,
            'description' => $requestValidated['description'] ?? $product->description,
            'release_date' => $requestValidated['release_date'] ?? $product->releaseDate,
            'price' => $requestValidated['price'] ?? $product->price,
            'manufacturer_id' => $requestValidated['manufacturer_id'] ?? $product->manufacturerId,
            'category_id' => $requestValidated['category_id'] ?? $product->categoryId,
            'image_path' => $requestValidated['image'] !== null ?
                $this->imageService->handleImagePath($requestValidated['image'], $product->imagePath) :
                $product->imagePath,
        ];

        $this->productRepository->update($id, $updateData);

        if ($requestValidated['maintenances'] !== null) {
            $processedMaintenances = $this->transformer->
            processMaintenancesForSave($requestValidated['maintenances']);

            $this->productRepository->attachMaintenances($id, $processedMaintenances);
        }

        $updatedProduct = $this->productRepository->find($id);
        $transformedProduct = $this->transformer->transform($updatedProduct);

        $this->cache->put($this->getProductCacheKey($id), $transformedProduct);

        return $transformedProduct;
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

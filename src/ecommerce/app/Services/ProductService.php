<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Product\ProductListDTO;
use App\DTO\Product\ProductShowDTO;
use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Currency\CurrencyCalculator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

/**
 *
 */
class ProductService
{
     private const CACHE_TTL_MINUTES = 30;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CurrencyCalculator $currencyCalculator
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected CurrencyCalculator $currencyCalculator,
    )
    {
    }

    /**
     * Get all products with caching.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        $products = $this->productRepository->all();

        return [
            'data' => $products->map(function ($product) {
                $dto = (new ProductListDTO($product, $this->currencyCalculator))->toArray();
                $dto['image_url'] = $this->getImageUrlWithFallback($product->image_path);
                return $dto;
            })->toArray(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ];
    }

    /**
     * Get one product by ID.
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getProduct(int $id): ?array
    {
        $cacheKey = $this->getProductCacheKey($id);

        return cache()->remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES),
            function () use ($id) {
            try {
                $product = $this->productRepository->find($id);
                $dto = new ProductShowDTO($product, $this->currencyCalculator);
                $dtoArray = $dto->toArray();
                $dtoArray['image_url'] = $this->getImageUrlWithFallback($product->image_path);
                return $dtoArray;
            } catch (ModelNotFoundException $e) {
                return null;
            }
        });
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
        cache()->forget($this->getProductCacheKey($id));

        return $this->productRepository->delete($id);
    }

    /**
     * Create a new product.
     *
     * @param array $request_validated
     *
     * @return Product
     */
    public function createProduct(array $request_validated): Product
    {
        $dto = new ProductStoreDTO($request_validated);
        $image_path = $this->handleImagePath($dto->image);

        $created_product = $this->productRepository->create([
            'name' => $dto->name,
            'article' => $dto->article,
            'description' => $dto->description,
            'release_date' => $dto->release_date,
            'price' => $dto->price,
            'image_path' => $image_path ?? 'storage/products/fallback_image1.png',
            'manufacturer_id' => $dto->manufacturer_id,
            'category_id' => $dto->category_id,
        ]);
        $this->productRepository->attachMaintenances($created_product, $dto->maintenances);

        $this->cacheProduct($created_product->id);

        return $created_product;
    }

    /**
     * Update an existing product by ID.
     *
     * @param int $id
     * @param array $request_validated
     *
     * @return Product
     */
    public function updateProduct(int $id, array $request_validated): Product
    {
        $product = $this->productRepository->find($id);
        $dto = new ProductUpdateDTO($request_validated);

        $image_path = $this->handleImagePath($dto->image, $product->image_path);

        $data = array_filter([
            'name' => $dto->name,
            'article' => $dto->article,
            'description' => $dto->description,
            'release_date' => $dto->release_date,
            'price' => $dto->price,
            'manufacturer_id' => $dto->manufacturer_id,
            'category_id' => $dto->category_id,
            'image_path' => $image_path ?? 'storage/products/fallback_image1.png',
        ], fn($value) => $value !== null);

        $this->productRepository->update($product, $data);

        if ($dto->maintenances !== null) {
            $this->productRepository->attachMaintenances($product, $dto->maintenances);
        }

        $product = $product->refresh();

        $this->cacheProduct($product->id);

        return $product->refresh();
    }

    /**
     * Store an uploaded file on a filesystem disk and return its path.
     *
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param string|null $oldPath
     *
     * @return string|null
     */
    private function handleImagePath(?\Illuminate\Http\UploadedFile $image, ?string $oldPath = null): ?string
    {
        if ($image && $image->isValid()) {
            try {
                if (
                    $oldPath &&
                    Storage::disk('public')->exists(str_replace('storage/', '', $oldPath))
                ) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $oldPath));
                }

                $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                $path = $image->storeAs('products', $filename, 'public');

                if (!$path) {

                    return $oldPath ?? 'storage/products/fallback_image1.png';
                }

                return 'storage/' . $path;
            } catch (\Exception $e) {
                return $oldPath ?? 'storage/products/fallback_image1.png';
            }
        }

        if ($image === null && $oldPath) {
            if (Storage::disk('public')->exists(str_replace('storage/', '', $oldPath))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldPath));
            }
            return 'storage/products/fallback_image1.png';
        }

        if ($image === null && !$oldPath) {
            return 'storage/products/fallback_image1.png';
        }

        return $oldPath;
    }

    /**
     * Check if the image path exists and return its URL or the fallback image URL.
     *
     * @param string|null $imagePath
     * @return string
     */
    private function getImageUrlWithFallback(?string $imagePath): string
    {
        $fallbackUrl = asset('storage/products/fallback_image1.png');
        if (
            $imagePath && $imagePath !== '/' &&
            Storage::disk('public')->exists(str_replace('storage/', '', $imagePath))
        ) {
            return asset($imagePath);
        }
        return $fallbackUrl;
    }

    /**
     * Save product in cache
     *
     * @param int $id
     *
     * @return void
     */
    private function cacheProduct(int $id): void
    {
        try {
            $product = $this->productRepository->find($id);
            $dto = new ProductShowDTO($product, $this->currencyCalculator);
            $dtoArray = $dto->toArray();
            $dtoArray['image_url'] = $this->getImageUrlWithFallback($product->image_path);

            cache()->put(
                $this->getProductCacheKey($id),
                $dtoArray, now()->addMinutes(self::CACHE_TTL_MINUTES)
            );
        } catch (\Exception $e) {
        }
    }

    /**
     * Get cache key for one product
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

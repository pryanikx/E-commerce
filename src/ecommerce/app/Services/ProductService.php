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

class ProductService
{
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
     * Get all products.
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        $products = $this->productRepository->all();
        return $products->map(function ($product) {
            $dto = new ProductListDTO($product, $this->currencyCalculator);
            $dtoArray = $dto->toArray();
            $dtoArray['image_url'] = $this->getImageUrlWithFallback($product->image_path);
            return $dtoArray;
        })->toArray();
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
        try {
            $product = $this->productRepository->find($id);
            $dto = new ProductShowDTO($product, $this->currencyCalculator);
            $dtoArray = $dto->toArray();
            $dtoArray['image_url'] = $this->getImageUrlWithFallback($product->image_path);
            return $dtoArray;
        } catch (ModelNotFoundException $e) {
            return null;
        }
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
            'image_path' => $image_path,
            'manufacturer_id' => $dto->manufacturer_id,
            'category_id' => $dto->category_id,
        ]);
        $this->productRepository->attachMaintenances($created_product, $dto->maintenances);
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
            'image_path' => $image_path,
        ], fn($value) => $value !== null);

        $this->productRepository->update($product, $data);

        if ($dto->maintenances !== null) {
            $this->productRepository->attachMaintenances($product, $dto->maintenances);
        }

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
            if ($oldPath && Storage::disk('public')->exists(str_replace('storage/', '', $oldPath))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldPath));
            }
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $filename, 'public');
            return 'storage/' . $path;
        }
        if ($image === null && $oldPath) {
            if (Storage::disk('public')->exists(str_replace('storage/', '', $oldPath))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldPath));
            }
            return $this->getImagePathWithFallback(null);
        }

        return $this->getImagePathWithFallback($oldPath);
    }

    /**
     * Check if the image path exists and return its path or the fallback image path.
     *
     * @param string|null $imagePath
     * @return string
     */
    private function getImagePathWithFallback(?string $imagePath): string
    {
        $fallbackPath = 'storage/products/fallback_image1.png';
        if ($imagePath && Storage::disk('public')->exists(str_replace('storage/', '', $imagePath))) {
            return $imagePath;
        }
        return $fallbackPath;
    }

    /**
     * Check if the image path exists and return its URL or the fallback image URL.
     *
     * @param string|null $imagePath
     * @return string
     */
    /**
     * Check if the image path exists and return its URL or the fallback image URL.
     *
     * @param string|null $imagePath
     * @return string
     */
    private function getImageUrlWithFallback(?string $imagePath): string
    {
        $fallbackUrl = asset('storage/products/fallback_image1.png');
        if ($imagePath && $imagePath !== '/' && Storage::disk('public')->exists(str_replace('storage/', '', $imagePath))) {
            return asset($imagePath);
        }
        return $fallbackUrl;
    }
}

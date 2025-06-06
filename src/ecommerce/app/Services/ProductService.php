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
use Psr\Log\LoggerInterface;

class ProductService
{
    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CurrencyCalculator $currencyCalculator
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected CurrencyCalculator $currencyCalculator,
        private readonly LoggerInterface $logger,
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
        return $products->map(fn($product) => (new ProductListDTO($product, $this->currencyCalculator))->toArray())->toArray();
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
        } catch (ModelNotFoundException $e) {
            return null;
        }
        return (new ProductShowDTO($product, $this->currencyCalculator))->toArray();
    }

    /**
     * delete an existing product by ID.
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
        $this->logger->info('Creating product', ['data' => $request_validated]);
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
        $this->logger->info('Product created', ['product_id' => $created_product->id]);
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
        $this->logger->info('Updating product', ['id' => $id, 'data' => $request_validated]);
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

        $this->logger->info('Product updated', ['product_id' => $product->id, 'data' => $data]);
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
            $this->logger->info('Image uploaded', ['path' => 'storage/' . $path]);
            return 'storage/' . $path;
        }
        if ($image === null && $oldPath) {
            if (Storage::disk('public')->exists(str_replace('storage/', '', $oldPath))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldPath));
            }
            $this->logger->info('Image cleared');
            return null;
        }
        $this->logger->info('Image unchanged', ['old_path' => $oldPath]);
        return $oldPath;
    }
}

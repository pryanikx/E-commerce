<?php

namespace App\Services;

use App\DTO\Product\ProductListDTO;
use App\DTO\Product\ProductShowDTO;
use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    public function getAll(): ?array
    {
        $products = $this->productRepository->all();

        return $products->map(fn($product)
            => (new ProductListDTO($product))->toArray())->toArray();
    }

    public function getProduct(int $id): ?array
    {
        try {
            $product = $this->productRepository->find($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }

        return (new ProductShowDTO($product))->toArray();
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

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
            'image_path' => $image_path !== $product->image_path ? $image_path : null,
        ], fn($value) => !is_null($value));

        if (!empty($data)) {
            $this->productRepository->update($product, $data);
        } else {
            Log::warning('No data to update for product', ['id' => $id]);
        }

        if ($dto->maintenances !== null) {
            $this->productRepository->attachMaintenances($product, $dto->maintenances);
        }

        return $product->refresh();
    }

    private function handleImagePath(?\Illuminate\Http\UploadedFile $image, ?string $oldPath = null): ?string
    {
        if (!$image || !$image->isValid()) {
            Log::debug('No valid image uploaded, using old path', ['oldPath' => $oldPath]);
            return $oldPath;
        }

        if ($oldPath && file_exists(public_path($oldPath))) {
            unlink(public_path($oldPath));
        }

        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/products', $filename);

        $newPath = 'storage/products/' . $filename;
        Log::debug('New image path created', ['newPath' => $newPath]);

        return $newPath;
    }
}

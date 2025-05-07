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

        $data = [
            'name' => $dto->name !== null ? $dto->name : $product->name,
            'article' => $dto->article !== null ? $dto->article : $product->article,
            'description' => $dto->description !== null ? $dto->description : $product->description,
            'release_date' => $dto->release_date !== null ? $dto->release_date : $product->release_date,
            'price' => $dto->price !== null ? $dto->price : $product->price,
            'manufacturer_id' => $dto->manufacturer_id !== null ? $dto->manufacturer_id : $product->manufacturer_id,
            'category_id' => $dto->category_id !== null ? $dto->category_id : $product->category_id,
            'image_path' => $image_path !== $product->image_path ? $image_path : null,
        ];

        $this->productRepository->update($product, $data);

        if ($dto->maintenances !== null) {
            $this->productRepository->attachMaintenances($product, $dto->maintenances);
        }

        return $product->refresh();
    }

    private function handleImagePath(?\Illuminate\Http\UploadedFile $image, ?string $oldPath = null): ?string
    {
        if (!$image || !$image->isValid()) {
            return $oldPath;
        }

        if ($oldPath && file_exists(public_path($oldPath))) {
            unlink(public_path($oldPath));
        }

        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/products', $filename);

        return 'storage/products/' . $filename;
    }
}

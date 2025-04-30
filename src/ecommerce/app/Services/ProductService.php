<?php


namespace App\Services;

use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    public function getAll(): ?Collection
    {
        return $this->productRepository->all();
    }

    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function deleteProduct(int $id) {
        return $this->productRepository->delete($id);
    }

    public function updateProduct(int $id, ProductUpdateDTO $dto): Product
    {
        $product = $this->getProduct($id);

        $image_path = $this->handleImagePath($dto->image, $product->image_path);

        $this->productRepository->update($product, [
            'description' => $dto->description,
            'price' => $dto->price,
            'image_path' => $image_path,
        ]);

        $this->productRepository->attachServices($product, $dto->services);

        return $product;
    }

    public function createProduct(ProductStoreDTO $dto): Product
    {
        $image_path = $this->handleImagePath($dto->image);

        $created_product = $this->productRepository->create([
            'name' => $dto->name,
            'article' => $dto->article,
            'description' => $dto->description,
            'release_date' => $dto->release_date,
            'price' => $dto->price,
            'image_path' => $image_path,
            'manufacturer_id' => $dto->manufacturer_id,
            'category_id' => $dto->category_id
        ]);

        $this->productRepository->attachServices($created_product, $dto->services);

        return $created_product;
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

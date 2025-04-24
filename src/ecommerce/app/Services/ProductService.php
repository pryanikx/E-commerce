<?php


namespace App\Services;

use App\Models\Product;
use App\DTO\ProductStoreDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    public function getAll()
    {
        return $this->productRepository->all();
    }

    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(ProductStoreDTO $productStoreDTO): Product
    {
        $image_path = $this->createImagePath($productStoreDTO);

        $created_product = $this->productRepository->create([
            'name' => $productStoreDTO->name,
            'article' => $productStoreDTO->article,
            'description' => $productStoreDTO->description,
            'release_date' => $productStoreDTO->release_date,
            'price' => $productStoreDTO->price,
            'image_path' => $image_path,
            'manufacturer_id' => $productStoreDTO->manufacturer_id,
            'category_id' => $productStoreDTO->category_id
        ]);

        return $this->attachServices($productStoreDTO, $created_product);
    }

    public function createImagePath(ProductStoreDTO $product): string {
        $image_path = null;

        if (!empty($product->image)) {
            $filename = uniqid() . '.' . $product->image->getClientOriginalExtension();
            $product->image->storeAs('public/products', $filename);
            $image_path = 'storage/products/' . $filename;
        }

        return $image_path;
    }

    public function attachServices(ProductStoreDTO $productStoreDTO, Product $created_product): Product
    {
        if (!empty($productStoreDTO->services)) {
            $created_product->services()->attach($productStoreDTO->services); // Probably move to ProductRepository?
        }

        return $created_product;
    }

    public function deleteProduct(int $id) {
        return $this->productRepository->delete($id);
    }
}

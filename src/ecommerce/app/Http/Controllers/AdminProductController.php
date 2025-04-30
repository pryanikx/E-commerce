<?php

namespace App\Http\Controllers;

use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

class AdminProductController extends ProductController
{
    public function store(ProductStoreRequest $request) {
        $dto = new ProductStoreDTO($request);
        $product = $this->productService->createProduct($dto);
        // TODO: сделать добавление НОВОЙ, а не существующей услуги при доабавлении продукта

        return response()->json($product, 201);
    }

    public function updateProduct(int $id, ProductUpdateRequest $request) {
        $dto = new ProductUpdateDTO($request);
        $product = $this->productService->updateProduct($id, $dto);

        return response()->json($product, 200);
    }

    public function deleteProduct(int $id) {
        $this->productService->deleteProduct($id);

        return response()->json("Successfully deleted!", 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\DTO\ProductStoreDTO;

class AdminProductController extends ProductController
{
    public function store(ProductStoreRequest $request) {
        $productStoreDTO = new ProductStoreDTO($request);
        $product = $this->productService->createProduct($productStoreDTO);
        // TODO: сделать добавление НОВОЙ, а не сущетсвующей услуги при доабавлении продукта

        return response()->json($product, 201);
    }

    public function deleteProduct(int $id) {
        $this->productService->deleteProduct($id);

        return response()->json("Successfully deleted!", 204);
    }
}

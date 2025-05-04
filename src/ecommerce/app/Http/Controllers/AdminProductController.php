<?php

namespace App\Http\Controllers;

use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use \Illuminate\Http\JsonResponse;

class AdminProductController extends ProductController
{
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $dto = new ProductStoreDTO($request->validated());
        $product = $this->productService->createProduct($dto);
        // TODO: сделать добавление НОВОЙ, а не существующей услуги при доабавлении продукта

        return response()->json($product, 201);
    }

    public function update(int $id, ProductUpdateRequest $request): JsonResponse
    {
        $dto = new ProductUpdateDTO($request->validated());
        $product = $this->productService->updateProduct($id, $dto);

        return response()->json($product, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);

        return response()->json("Successfully deleted!", 204);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\ProductController;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Http\JsonResponse;

class AdminProductController extends ProductController
{
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return response()->json($product, 201);
    }

    public function update(int $id, ProductUpdateRequest $request): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->validated());

        return response()->json($product, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);

        return response()->json(['message' => 'Successfully deleted!'], 200);
    }
}

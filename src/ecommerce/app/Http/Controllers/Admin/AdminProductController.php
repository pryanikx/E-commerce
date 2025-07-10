<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\ProductController;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Http\JsonResponse;

class AdminProductController extends ProductController
{
    /**
     * Store a new product.
     *
     * @param ProductStoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return response()->json($product, 201);
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param ProductUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(int $id, ProductUpdateRequest $request): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->validated());

        return response()->json($product, 200);
    }

    /**
     * Erase an existing product.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if ($this->productService->deleteProduct($id)) {
            return response()->json(['message' => __('messages.deleted')], 200);
        }

        return response()->json(['message' => __('messages.no_product')], 200);
    }
}

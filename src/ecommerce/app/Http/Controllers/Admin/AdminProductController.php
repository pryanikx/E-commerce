<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\Product\ProductStoreDTO;
use App\DTO\Product\ProductUpdateDTO;
use App\Exceptions\DeleteDataException;
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
        $requestValidated = $request->validated();

        $product = $this->productService->createProduct(
            new ProductStoreDTO(
                $requestValidated['name'],
                $requestValidated['article'],
                $requestValidated['description'],
                $requestValidated['release_date'],
                $requestValidated['price'],
                $requestValidated['image'],
                $requestValidated['manufacturer_id'],
                $requestValidated['category_id'],
                $requestValidated['maintenance_ids'],
            )
        );

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
        $requestValidated = $request->validated();

        $product = $this->productService->updateProduct(
            new ProductUpdateDTO(
                $requestValidated['id'],
                $requestValidated['name'],
                $requestValidated['article'],
                $requestValidated['description'],
                $requestValidated['release_date'],
                $requestValidated['price'],
                $requestValidated['image'],
                $requestValidated['manufacturer_id'],
                $requestValidated['category_id'],
                $requestValidated['maintenance_ids'],
            )
        );

        return response()->json($product, 200);
    }

    /**
     * Erase an existing product.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws DeleteDataException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);

        return response()->json(['message' => __('messages.deleted')], 200);
    }
}

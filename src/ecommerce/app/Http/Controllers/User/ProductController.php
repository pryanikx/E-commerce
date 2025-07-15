<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ProductService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @param ProductService $productService
     */
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * List all products paginated.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        if (empty($products)) {
            return response()->json(['message' => __('messages.empty_products')], 200);
        }

        return response()->json([
            'data' => $products
            ], 200
        );
    }

    /**
     * Show one product by ID.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return response()->json(['message' => __('messages.no_product')], 200);
        }

        return response()->json([
                'data' => $product
            ], 200
        );
    }
}

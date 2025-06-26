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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);

        $products = $this->productService->getAll($page);

        if (empty($products)) {
            return response()->json(['message' => __('messages.empty_products')], 200);
        }

        return response()->json($products, 200);
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

        return response()->json($product, 200);
    }
}

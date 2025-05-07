<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        if (empty($products)) {
            return response()->json(['error' => 'No products found!'], 200);
        }

        return response()->json($products, 200);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found!'], 404);
        }

        return response()->json($product, 200);
    }
}

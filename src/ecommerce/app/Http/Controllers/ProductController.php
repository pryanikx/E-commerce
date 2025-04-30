<?php

namespace App\Http\Controllers;

use App\DTO\Product\ProductListDTO;
use App\DTO\Product\ProductShowDTO;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index() {
        $products = $this->productService->getAll();

        if (!$products) {
            return response()->json("No products found!", 404);
        }

        $result = $products->map(fn($product) => (new ProductListDTO($product))->toArray());

        return response()->json($result);
    }

    public function show(int $id) {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return response()->json("Product not found!", 404);
        }

        return response()->json((new ProductShowDTO($product))->toArray(), 200);
    }

}

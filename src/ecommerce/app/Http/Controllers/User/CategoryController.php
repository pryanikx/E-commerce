<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryProductRequest;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * @param CategoryService $categoryService
     */
    public function __construct(protected CategoryService $categoryService)
    {
    }

    /**
     * List all categories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        if (empty($categories)) {
            return response()->json(['message' => __('messages.empty_categories')], 200);
        }

        return response()->json([
            'data' => $categories
        ], 200);
    }

    /**
     * List products of the specified category.
     *
     * @param CategoryProductRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(CategoryProductRequest $request, int $id): JsonResponse
    {
        $result = $this->categoryService->getProductsForCategory(
            $id,
            $request->getFilters(),
            $request->getSortParams(),
            $request->getPage()
        );

        return response()->json([
            'data' => $result->products,
            'meta' => $result->pagination
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\DTO\Category\CategoryStoreDTO;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class AdminCategoryController extends CategoryController
{
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $dto = new CategoryStoreDTO($request->validated());
        $category = $this->categoryService->createCategory($dto);

        return response()->json($category, 201);
    }

    public function update(int $id, CategoryStoreRequest $request): JsonResponse
    {
        $dto = new CategoryStoreDTO($request->validated());
        $category = $this->categoryService->updateCategory($id, $dto);

        return response()->json($category, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);

        return response()->json(['message' => 'Successfully deleted!'], 204);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\CategoryController;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use Illuminate\Http\JsonResponse;

class AdminCategoryController extends CategoryController
{
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return response()->json($category, 201);
    }

    public function update(int $id, CategoryUpdateRequest $request): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        return response()->json($category, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);

        return response()->json(['message' => 'Successfully deleted!'], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\ActivityLogger;
use App\Services\CategoryService;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = (int) (auth()->id() ?? 1);
        CategoryService::seedDefaultCategoriesForUser($userId);

        $response = new ApiDataResponse;
        $response->setDefaultSortData('id', 'desc');
        $response->searchFromRequest($request, Category::class, CategoryResource::class, ['parent']);

        return $response->display();
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $userId = (int) (auth()->id() ?? 1);
        $data = $request->validated();
        $data['tenant_id'] = $userId;
        $data['icon'] = $data['icon'] ?? 'tag';
        $data['color'] = $data['color'] ?? '#6366f1';
        $data['is_system'] = false;
        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $category = Category::create($data);

        ActivityLogger::logCreated($category, $category->name);

        ApiResponse::addInfoArray(__('Category created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new CategoryResource($category));
    }

    public function show(int $id): JsonResponse
    {
        $category = Category::with('parent')->find($id);

        if (! $category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, new CategoryResource($category));
    }

    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $category = Category::find($id);

        if (! $category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $category->fill($request->validated());
        $category->save();

        ActivityLogger::logUpdated($category, $category->name);

        ApiResponse::addInfoArray(__('Category updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new CategoryResource($category));
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (! $category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        ActivityLogger::logDeleted($category, $category->name);

        $category->delete();

        ApiResponse::addInfoArray(__('Category deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}

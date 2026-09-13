<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ActivityLogger;
use App\Services\CategoryService;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        // Auto-seed default starter categories if this user doesn't have any yet
        CategoryService::seedDefaultCategoriesForUser($tenantId);

        $response = new ApiDataResponse;
        $response->searchFromRequest($request, Category::class, JsonResource::class, [], [], ['tenant_id' => $tenantId]);

        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->where('tenant_id', $tenantId),
            ],
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $category = Category::create([
            'tenant_id' => $tenantId,
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'parent_id' => $request->input('parent_id'),
            'icon' => $request->input('icon', 'tag'),
            'color' => $request->input('color', '#6366f1'),
            'is_system' => false,
            'is_active' => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
        ]);

        ActivityLogger::logCreated($category, $category->name);

        ApiResponse::addInfoArray(__('Category created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $category);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $category = Category::where('tenant_id', $tenantId)->find($id);

        if (! $category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $category);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $category = Category::where('tenant_id', $tenantId)->find($id);

        if (! $category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->where('tenant_id', $tenantId)->ignore($category->id),
            ],
            'type' => 'sometimes|required|in:income,expense',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $category->fill($request->only(['name', 'type', 'parent_id', 'icon', 'color', 'is_active']));
        $category->save();

        ActivityLogger::logUpdated($category, $category->name);

        ApiResponse::addInfoArray(__('Category updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $category);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $category = Category::where('tenant_id', $tenantId)->find($id);

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

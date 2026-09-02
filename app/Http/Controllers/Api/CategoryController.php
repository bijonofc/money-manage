<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse();
        $response->searchFromRequest($request, Category::class, JsonResource::class, [], [], ['tenant_id' => $tenantId]);
        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:100',
            'type'  => 'required|in:income,expense',
            'icon'  => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $category = Category::create([
            'tenant_id' => $tenantId,
            'name'      => $request->input('name'),
            'type'      => $request->input('type'),
            'parent_id' => $request->input('parent_id'),
            'icon'      => $request->input('icon', 'tag'),
            'color'     => $request->input('color', '#6366f1'),
            'is_system' => false,
            'is_active' => $request->input('is_active', true),
        ]);

        ApiResponse::addInfoArray(__('Category created successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $category);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $category = Category::where('tenant_id', $tenantId)->find($id);

        if (!$category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $category);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $category = Category::where('tenant_id', $tenantId)->find($id);

        if (!$category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $category->fill($request->only(['name', 'type', 'parent_id', 'icon', 'color', 'is_active']));
        $category->save();

        ApiResponse::addInfoArray(__('Category updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $category);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $category = Category::where('tenant_id', $tenantId)->find($id);

        if (!$category) {
            ApiResponse::addErrorArray(__('Category not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $category->delete();

        ApiResponse::addInfoArray(__('Category deleted successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }
}

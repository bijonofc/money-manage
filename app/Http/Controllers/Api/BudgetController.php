<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Services\ActivityLogger;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $response = new ApiDataResponse;
        $response->setDefaultSortData('id', 'desc');
        $response->searchFromRequest($request, Budget::class, BudgetResource::class, ['category']);

        return $response->display();
    }

    public function store(BudgetRequest $request): JsonResponse
    {
        $userId = (int) (auth()->id() ?? 1);
        $data = $request->validated();
        $data['tenant_id'] = $userId;
        $data['period'] = $data['period'] ?? 'monthly';
        $data['alert_threshold'] = $data['alert_threshold'] ?? 80.00;
        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $budget = Budget::create($data);

        $catName = $budget->category?->name ?? 'All Categories';
        ActivityLogger::logCreated($budget, "{$catName} ({$budget->period} - ".number_format((float) $budget->amount, 2).')');

        ApiResponse::addInfoArray(__('Budget created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new BudgetResource($budget));
    }

    public function show(int $id): JsonResponse
    {
        $budget = Budget::with('category')->find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, new BudgetResource($budget));
    }

    public function update(BudgetRequest $request, int $id): JsonResponse
    {
        $budget = Budget::find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $budget->fill($request->validated());
        $budget->save();

        $catName = $budget->category?->name ?? 'All Categories';
        ActivityLogger::logUpdated($budget, "{$catName} ({$budget->period} - ".number_format((float) $budget->amount, 2).')');

        ApiResponse::addInfoArray(__('Budget updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new BudgetResource($budget));
    }

    public function destroy(int $id): JsonResponse
    {
        $budget = Budget::find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $catName = $budget->category?->name ?? 'All Categories';
        ActivityLogger::logDeleted($budget, "{$catName} ({$budget->period} - ".number_format((float) $budget->amount, 2).')');

        $budget->delete();

        ApiResponse::addInfoArray(__('Budget deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}

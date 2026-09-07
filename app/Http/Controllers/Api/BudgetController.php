<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Services\ActivityLogger;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse;
        $response->searchFromRequest($request, Budget::class, JsonResource::class, ['category'], [], ['tenant_id' => $tenantId]);

        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'alert_threshold' => 'nullable|numeric|min:1|max:100',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 422);
        }

        $budget = Budget::create([
            'tenant_id' => $tenantId,
            'category_id' => $request->input('category_id'),
            'amount' => $request->input('amount'),
            'period' => $request->input('period', 'monthly'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'alert_threshold' => $request->input('alert_threshold', 80.00),
            'is_active' => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
        ]);

        $catName = $budget->category?->name ?? 'All Categories';
        ActivityLogger::logCreated($budget, "{$catName} ({$budget->period} - ".number_format($budget->amount, 2).')');

        ApiResponse::addInfoArray(__('Budget created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $budget);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $budget = Budget::with('category')->where('tenant_id', $tenantId)->find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, $budget);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $budget = Budget::where('tenant_id', $tenantId)->find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $budget->fill($request->only(['category_id', 'amount', 'period', 'start_date', 'end_date', 'alert_threshold', 'is_active']));
        $budget->save();

        $catName = $budget->category?->name ?? 'All Categories';
        ActivityLogger::logUpdated($budget, "{$catName} ({$budget->period} - ".number_format($budget->amount, 2).')');

        ApiResponse::addInfoArray(__('Budget updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $budget);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $budget = Budget::where('tenant_id', $tenantId)->find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $catName = $budget->category?->name ?? 'All Categories';
        ActivityLogger::logDeleted($budget, "{$catName} ({$budget->period} - ".number_format($budget->amount, 2).')');

        $budget->delete();

        ApiResponse::addInfoArray(__('Budget deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}

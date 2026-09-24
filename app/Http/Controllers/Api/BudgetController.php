<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Http\Resources\TransactionResource;
use App\Models\Budget;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Carbon\Carbon;
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

    public function transactions(Request $request, int $id): JsonResponse
    {
        $budget = Budget::with('category')->find($id);

        if (! $budget) {
            ApiResponse::addErrorArray(__('Budget not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $query = Transaction::with(['category', 'account'])
            ->where('transaction_type', 'expense');

        if ($budget->tenant_id) {
            $query->where('tenant_id', $budget->tenant_id);
        }

        if ($budget->category_id) {
            $query->where('category_id', $budget->category_id);
        }

        $start = $budget->start_date?->toDateString();
        $end = $budget->end_date?->toDateString();

        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        } elseif ($start) {
            $query->where('date', '>=', $start);
        }

        $transactions = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->get();

        $response = new ApiResponse;

        return $response->displayWithResponse(true, [
            'budget' => new BudgetResource($budget),
            'transactions' => TransactionResource::collection($transactions),
        ]);
    }

    public function store(BudgetRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $data = $request->validated();
        $data['tenant_id'] = $userId;
        $data['period'] = $data['period'] ?? 'monthly';
        $data['alert_threshold'] = $data['alert_threshold'] ?? 80.00;
        $data['status'] = (! empty($data['status']) && in_array($data['status'], ['A', 'I'], true)) ? $data['status'] : 'A';

        if (empty($data['end_date']) && ! empty($data['start_date'])) {
            $startDate = Carbon::parse($data['start_date']);
            if ($data['period'] === 'monthly') {
                $data['end_date'] = $startDate->copy()->endOfMonth()->toDateString();
            } elseif ($data['period'] === 'weekly') {
                $data['end_date'] = $startDate->copy()->addDays(6)->toDateString();
            } elseif ($data['period'] === 'yearly') {
                $data['end_date'] = $startDate->copy()->endOfYear()->toDateString();
            }
        }

        $budget = Budget::create($data);
        $budget->load('category');

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
        $budget->load('category');

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

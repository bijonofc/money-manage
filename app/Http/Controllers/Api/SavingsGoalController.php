<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavingsContributionRequest;
use App\Http\Requests\SavingsGoalRequest;
use App\Http\Resources\SavingsGoalResource;
use App\Models\SavingsGoal;
use App\Services\ActivityLogger;
use App\Services\SavingsGoalService;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function __construct(
        private readonly SavingsGoalService $savingsGoalService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $response = new ApiDataResponse;
        $response->setDefaultSortData('id', 'desc');
        $response->searchFromRequest($request, SavingsGoal::class, SavingsGoalResource::class, ['contributions']);

        return $response->display();
    }

    public function store(SavingsGoalRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $data = $request->validated();
        $data['tenant_id'] = $userId;
        $data['current_amount'] = $data['current_amount'] ?? 0.00;
        $data['icon'] = $data['icon'] ?? 'target';
        $data['color'] = $data['color'] ?? '#10b981';
        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $goal = SavingsGoal::create($data);

        ActivityLogger::logCreated($goal, "{$goal->name} (Target: ".number_format((float) $goal->target_amount, 2).')');

        ApiResponse::addInfoArray(__('Savings goal created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new SavingsGoalResource($goal));
    }

    public function show(int $id): JsonResponse
    {
        $goal = SavingsGoal::with('contributions')->find($id);

        if (! $goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, new SavingsGoalResource($goal));
    }

    public function update(SavingsGoalRequest $request, int $id): JsonResponse
    {
        $goal = SavingsGoal::find($id);

        if (! $goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $goal->fill($request->validated());
        $goal->save();

        ActivityLogger::logUpdated($goal, "{$goal->name} (Target: ".number_format((float) $goal->target_amount, 2).')');

        ApiResponse::addInfoArray(__('Savings goal updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new SavingsGoalResource($goal));
    }

    public function destroy(int $id): JsonResponse
    {
        $goal = SavingsGoal::find($id);

        if (! $goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        ActivityLogger::logDeleted($goal, "{$goal->name}");

        $goal->delete();

        ApiResponse::addInfoArray(__('Savings goal deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }

    public function contribute(SavingsContributionRequest $request, int $id): JsonResponse
    {
        $userId = auth()->id();
        $goal = SavingsGoal::find($id);

        if (! $goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $contribution = $this->savingsGoalService->recordContribution($goal, $request->validated(), $userId);

        ApiResponse::addInfoArray(__('Contribution recorded and account updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $contribution);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class SavingsGoalController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse();
        $response->searchFromRequest($request, SavingsGoal::class, JsonResource::class, ['contributions'], [], ['tenant_id' => $tenantId]);
        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount'=> 'nullable|numeric|min:0',
            'deadline'      => 'nullable|date',
            'icon'          => 'nullable|string|max:50',
            'color'         => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $goal = SavingsGoal::create([
            'tenant_id'      => $tenantId,
            'name'           => $request->input('name'),
            'target_amount'  => $request->input('target_amount'),
            'current_amount' => $request->input('current_amount', 0.00),
            'deadline'       => $request->input('deadline'),
            'icon'           => $request->input('icon', 'target'),
            'color'          => $request->input('color', '#10b981'),
            'description'    => $request->input('description'),
            'is_active'      => $request->input('is_active', true),
        ]);

        ApiResponse::addInfoArray(__('Savings goal created successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $goal);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $goal = SavingsGoal::with('contributions')->where('tenant_id', $tenantId)->find($id);

        if (!$goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $goal);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $goal = SavingsGoal::where('tenant_id', $tenantId)->find($id);

        if (!$goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $goal->fill($request->only(['name', 'target_amount', 'current_amount', 'deadline', 'icon', 'color', 'description', 'is_active']));
        $goal->save();

        ApiResponse::addInfoArray(__('Savings goal updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $goal);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $goal = SavingsGoal::where('tenant_id', $tenantId)->find($id);

        if (!$goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $goal->delete();

        ApiResponse::addInfoArray(__('Savings goal deleted successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }

    public function contribute(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $goal = SavingsGoal::where('tenant_id', $tenantId)->find($id);

        if (!$goal) {
            ApiResponse::addErrorArray(__('Savings goal not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $amount = $request->input('amount', 0);
        if ($amount <= 0) {
            ApiResponse::addErrorArray(__('Invalid contribution amount'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $contribution = SavingsContribution::create([
            'goal_id' => $goal->id,
            'amount'  => $amount,
            'note'    => $request->input('note'),
        ]);

        $goal->increment('current_amount', $amount);

        ApiResponse::addInfoArray(__('Contribution added successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $contribution);
    }
}

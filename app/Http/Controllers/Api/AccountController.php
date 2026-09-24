<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\ActivityLogger;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $response = new ApiDataResponse;
        $response->setDefaultSortData('id', 'desc');
        $response->searchFromRequest($request, Account::class, AccountResource::class);

        return $response->display();
    }

    public function store(AccountRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $data = $request->validated();
        $data['tenant_id'] = $userId;
        $data['balance'] = $data['balance'] ?? 0.00;
        $data['currency'] = $data['currency'] ?? 'BDT';
        $data['is_active'] = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $account = Account::create($data);

        ActivityLogger::logCreated($account, $account->name);

        ApiResponse::addInfoArray(__('Account created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new AccountResource($account));
    }

    public function show(int $id): JsonResponse
    {
        $account = Account::find($id);

        if (! $account) {
            ApiResponse::addErrorArray(__('Account not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, new AccountResource($account));
    }

    public function update(AccountRequest $request, int $id): JsonResponse
    {
        $account = Account::find($id);

        if (! $account) {
            ApiResponse::addErrorArray(__('Account not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $account->fill($request->validated());
        $account->save();

        ActivityLogger::logUpdated($account, $account->name);

        ApiResponse::addInfoArray(__('Account updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new AccountResource($account));
    }

    public function destroy(int $id): JsonResponse
    {
        $account = Account::find($id);

        if (! $account) {
            ApiResponse::addErrorArray(__('Account not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        ActivityLogger::logDeleted($account, $account->name);

        $account->delete();

        ApiResponse::addInfoArray(__('Account deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}

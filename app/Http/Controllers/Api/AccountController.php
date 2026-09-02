<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse();
        $response->searchFromRequest($request, Account::class, JsonResource::class, [], [], ['tenant_id' => $tenantId]);
        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:100',
            'account_type'   => 'required|in:cash,bank,mobile,credit_card,other',
            'balance'        => 'nullable|numeric',
            'currency'       => 'nullable|string|max:3',
            'account_number' => 'nullable|string|max:50',
            'description'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $account = Account::create([
            'tenant_id'      => $tenantId,
            'name'           => $request->input('name'),
            'account_type'   => $request->input('account_type'),
            'balance'        => $request->input('balance', 0.00),
            'currency'       => $request->input('currency', 'BDT'),
            'account_number' => $request->input('account_number'),
            'description'    => $request->input('description'),
            'is_active'      => $request->input('is_active', true),
        ]);

        ApiResponse::addInfoArray(__('Account created successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $account);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $account = Account::where('tenant_id', $tenantId)->find($id);

        if (!$account) {
            ApiResponse::addErrorArray(__('Account not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $account);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $account = Account::where('tenant_id', $tenantId)->find($id);

        if (!$account) {
            ApiResponse::addErrorArray(__('Account not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $account->fill($request->only(['name', 'account_type', 'account_number', 'balance', 'currency', 'is_active', 'description', 'meta']));
        $account->save();

        ApiResponse::addInfoArray(__('Account updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $account);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $account = Account::where('tenant_id', $tenantId)->find($id);

        if (!$account) {
            ApiResponse::addErrorArray(__('Account not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $account->delete();

        ApiResponse::addInfoArray(__('Account deleted successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse();
        $response->setDefaultSortData('date', 'desc');
        $response->searchFromRequest(
            $request,
            Transaction::class,
            JsonResource::class,
            ['category', 'account', 'fromAccount'],
            [],
            ['tenant_id' => $tenantId]
        );
        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $userId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:income,expense,transfer',
            'amount'           => 'required|numeric|min:0.01',
            'account_id'       => 'required|exists:accounts,id',
            'category_id'      => 'nullable|exists:categories,id',
            'from_account_id'  => 'nullable|exists:accounts,id',
            'date'             => 'required|date',
            'description'      => 'nullable|string',
            'payment_method'   => 'nullable|in:cash,card,bank_transfer,mobile,check,other',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $transaction = DB::transaction(function () use ($request, $tenantId, $userId) {
            $type = $request->input('transaction_type');
            $amount = $request->input('amount');
            $accountId = $request->input('account_id');
            $fromAccountId = $request->input('from_account_id');

            $tx = Transaction::create([
                'tenant_id'        => $tenantId,
                'user_id'          => $userId,
                'transaction_type' => $type,
                'amount'           => $amount,
                'category_id'      => $request->input('category_id'),
                'account_id'       => $accountId,
                'from_account_id'  => $fromAccountId,
                'date'             => $request->input('date'),
                'description'      => $request->input('description'),
                'reference_number' => $request->input('reference_number'),
                'payment_method'   => $request->input('payment_method'),
                'tags'             => $request->input('tags'),
            ]);

            // Update Account Balances
            $account = Account::find($accountId);
            if ($account) {
                if ($type === 'income') {
                    $account->increment('balance', $amount);
                } elseif ($type === 'expense') {
                    $account->decrement('balance', $amount);
                } elseif ($type === 'transfer' && $fromAccountId) {
                    $account->increment('balance', $amount);
                    $fromAccount = Account::find($fromAccountId);
                    if ($fromAccount) {
                        $fromAccount->decrement('balance', $amount);
                    }
                }
            }

            return $tx;
        });

        ApiResponse::addInfoArray(__('Transaction recorded successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $transaction);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $transaction = Transaction::with(['category', 'account', 'fromAccount'])
            ->where('tenant_id', $tenantId)
            ->find($id);

        if (!$transaction) {
            ApiResponse::addErrorArray(__('Transaction not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $transaction);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $transaction = Transaction::where('tenant_id', $tenantId)->find($id);

        if (!$transaction) {
            ApiResponse::addErrorArray(__('Transaction not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $transaction->fill($request->only([
            'transaction_type', 'amount', 'category_id', 'account_id',
            'from_account_id', 'date', 'description', 'reference_number',
            'payment_method', 'tags',
        ]));
        $transaction->save();

        ApiResponse::addInfoArray(__('Transaction updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $transaction);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $transaction = Transaction::where('tenant_id', $tenantId)->find($id);

        if (!$transaction) {
            ApiResponse::addErrorArray(__('Transaction not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $transaction->delete();

        ApiResponse::addInfoArray(__('Transaction deleted successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }
}

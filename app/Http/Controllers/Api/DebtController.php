<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Transaction;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse();
        $response->searchFromRequest($request, Debt::class, JsonResource::class, ['payments.transaction.account'], [], ['tenant_id' => $tenantId]);
        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $userId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'type'             => 'required|in:owed_to,owed_from',
            'creditor_name'    => 'required|string|max:255',
            'principal_amount' => 'required|numeric|min:0.01',
            'account_id'       => 'nullable|exists:accounts,id',
            'due_date'         => 'nullable|date',
            'interest_rate'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $debt = DB::transaction(function () use ($request, $tenantId, $userId) {
            $type = $request->input('type');
            $amount = (float)$request->input('principal_amount');
            $accountId = $request->input('account_id');

            $debtRecord = Debt::create([
                'tenant_id'        => $tenantId,
                'type'             => $type,
                'creditor_name'    => $request->input('creditor_name'),
                'creditor_contact' => $request->input('creditor_contact'),
                'principal_amount' => $amount,
                'paid_amount'      => $request->input('paid_amount', 0.00),
                'interest_rate'    => $request->input('interest_rate'),
                'due_date'         => $request->input('due_date'),
                'description'      => $request->input('description'),
                'status'           => $request->input('status', 'active'),
            ]);

            // If an account is selected, record the initial cash movement & transaction
            if ($accountId) {
                $account = Account::where('tenant_id', $tenantId)->find($accountId);
                if ($account) {
                    if ($type === 'owed_to') {
                        // Loan taken: cash is deposited into my account
                        $account->increment('balance', $amount);

                        $category = Category::where('tenant_id', $tenantId)
                            ->where('type', 'income')
                            ->where('name', 'like', '%Loan%')
                            ->orWhere('name', 'like', '%Other Income%')
                            ->first();

                        Transaction::create([
                            'tenant_id'        => $tenantId,
                            'user_id'          => $userId,
                            'transaction_type' => 'income',
                            'amount'           => $amount,
                            'account_id'       => $accountId,
                            'category_id'      => $category ? $category->id : null,
                            'date'             => $request->input('date', now()->toDateString()),
                            'time'             => now()->format('H:i'),
                            'description'      => "Loan borrowed from {$debtRecord->creditor_name}" . ($debtRecord->description ? " ({$debtRecord->description})" : ''),
                        ]);
                    } elseif ($type === 'owed_from') {
                        // Loan given to someone: cash is disbursed from my account
                        $account->decrement('balance', $amount);

                        $category = Category::where('tenant_id', $tenantId)
                            ->where('type', 'expense')
                            ->where('name', 'like', '%Debt%')
                            ->orWhere('name', 'like', '%Other Expenses%')
                            ->first();

                        Transaction::create([
                            'tenant_id'        => $tenantId,
                            'user_id'          => $userId,
                            'transaction_type' => 'expense',
                            'amount'           => $amount,
                            'account_id'       => $accountId,
                            'category_id'      => $category ? $category->id : null,
                            'date'             => $request->input('date', now()->toDateString()),
                            'time'             => now()->format('H:i'),
                            'description'      => "Loan given to {$debtRecord->creditor_name}" . ($debtRecord->description ? " ({$debtRecord->description})" : ''),
                        ]);
                    }
                }
            }

            return $debtRecord;
        });

        ApiResponse::addInfoArray(__('Debt record created successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $debt);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $debt = Debt::with(['payments.transaction.account'])->where('tenant_id', $tenantId)->find($id);

        if (!$debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $debt);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $debt = Debt::where('tenant_id', $tenantId)->find($id);

        if (!$debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $debt->fill($request->only(['type', 'creditor_name', 'creditor_contact', 'principal_amount', 'paid_amount', 'interest_rate', 'due_date', 'description', 'status']));
        $debt->save();

        ApiResponse::addInfoArray(__('Debt record updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $debt);
    }

    public function destroy($id)
    {
        $tenantId = auth()->id() ?? 1;
        $debt = Debt::where('tenant_id', $tenantId)->find($id);

        if (!$debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $debt->delete();

        ApiResponse::addInfoArray(__('Debt record deleted successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, null);
    }

    public function pay(Request $request, $id)
    {
        $tenantId = auth()->id() ?? 1;
        $userId = auth()->id() ?? 1;
        $debt = Debt::where('tenant_id', $tenantId)->find($id);

        if (!$debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'amount'       => 'required|numeric|min:0.01',
            'account_id'   => 'required|exists:accounts,id',
            'note'         => 'required|string|max:255',
            'payment_date' => 'nullable|date',
            'time'         => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                ApiResponse::addErrorArray($error);
            }
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $amount = (float)$request->input('amount');
        $accountId = $request->input('account_id');
        $paymentDate = $request->input('payment_date', now()->toDateString());
        $paymentTime = $request->input('time', now()->format('H:i'));
        $note = $request->input('note');

        $payment = DB::transaction(function () use ($debt, $amount, $accountId, $paymentDate, $paymentTime, $note, $tenantId, $userId) {
            $transactionId = null;

            if ($accountId) {
                $account = Account::where('tenant_id', $tenantId)->find($accountId);
                if ($account) {
                    if ($debt->type === 'owed_to') {
                        // Paying back money I owe -> EXPENSE from chosen account
                        $account->decrement('balance', $amount);

                        // Find or assign 'Debt Payments' category
                        $category = Category::where('tenant_id', $tenantId)
                            ->where('type', 'expense')
                            ->where(function ($q) {
                                $q->where('name', 'like', '%Debt%')
                                  ->orWhere('name', 'like', '%Loan%');
                            })
                            ->first();

                        $tx = Transaction::create([
                            'tenant_id'        => $tenantId,
                            'user_id'          => $userId,
                            'transaction_type' => 'expense',
                            'amount'           => $amount,
                            'account_id'       => $accountId,
                            'category_id'      => $category ? $category->id : null,
                            'date'             => $paymentDate,
                            'time'             => $paymentTime,
                            'description'      => "Debt repayment to {$debt->creditor_name}" . ($note ? " - {$note}" : ''),
                        ]);
                        $transactionId = $tx->id;
                    } elseif ($debt->type === 'owed_from') {
                        // Receiving money someone owed me -> INCOME into chosen account
                        $account->increment('balance', $amount);

                        $category = Category::where('tenant_id', $tenantId)
                            ->where('type', 'income')
                            ->where(function ($q) {
                                $q->where('name', 'like', '%Debt%')
                                  ->orWhere('name', 'like', '%Loan%')
                                  ->orWhere('name', 'like', '%Other%');
                            })
                            ->first();

                        $tx = Transaction::create([
                            'tenant_id'        => $tenantId,
                            'user_id'          => $userId,
                            'transaction_type' => 'income',
                            'amount'           => $amount,
                            'account_id'       => $accountId,
                            'category_id'      => $category ? $category->id : null,
                            'date'             => $paymentDate,
                            'time'             => $paymentTime,
                            'description'      => "Debt collection from {$debt->creditor_name}" . ($note ? " - {$note}" : ''),
                        ]);
                        $transactionId = $tx->id;
                    }
                }
            }

            $paymentRecord = DebtPayment::create([
                'debt_id'        => $debt->id,
                'transaction_id' => $transactionId,
                'amount'         => $amount,
                'payment_date'   => $paymentDate,
                'note'           => $note,
            ]);

            $debt->increment('paid_amount', $amount);
            if ($debt->paid_amount >= $debt->principal_amount) {
                $debt->update(['status' => 'paid']);
            }

            return $paymentRecord;
        });

        ApiResponse::addInfoArray(__('Payment recorded and balance updated successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $payment);
    }
}


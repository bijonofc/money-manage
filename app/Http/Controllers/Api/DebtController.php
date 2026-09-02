<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\DebtPayment;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $response = new ApiDataResponse();
        $response->searchFromRequest($request, Debt::class, JsonResource::class, ['payments'], [], ['tenant_id' => $tenantId]);
        return $response->display();
    }

    public function store(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        $validator = Validator::make($request->all(), [
            'type'             => 'required|in:owed_to,owed_from',
            'creditor_name'    => 'required|string|max:255',
            'principal_amount' => 'required|numeric|min:0.01',
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

        $debt = Debt::create([
            'tenant_id'        => $tenantId,
            'type'             => $request->input('type'),
            'creditor_name'    => $request->input('creditor_name'),
            'creditor_contact' => $request->input('creditor_contact'),
            'principal_amount' => $request->input('principal_amount'),
            'paid_amount'      => $request->input('paid_amount', 0.00),
            'interest_rate'    => $request->input('interest_rate'),
            'due_date'         => $request->input('due_date'),
            'description'      => $request->input('description'),
            'status'           => $request->input('status', 'active'),
        ]);

        ApiResponse::addInfoArray(__('Debt record created successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $debt);
    }

    public function show($id)
    {
        $tenantId = auth()->id() ?? 1;
        $debt = Debt::with('payments')->where('tenant_id', $tenantId)->find($id);

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
        $debt = Debt::where('tenant_id', $tenantId)->find($id);

        if (!$debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 404);
        }

        $amount = $request->input('amount', 0);
        if ($amount <= 0) {
            ApiResponse::addErrorArray(__('Invalid payment amount'));
            $response = new ApiResponse();
            return $response->displayWithResponse(false, null, 422);
        }

        $payment = DebtPayment::create([
            'debt_id'      => $debt->id,
            'amount'       => $amount,
            'payment_date' => $request->input('payment_date', now()->toDateString()),
            'note'         => $request->input('note'),
        ]);

        $debt->increment('paid_amount', $amount);
        if ($debt->paid_amount >= $debt->principal_amount) {
            $debt->update(['status' => 'paid']);
        }

        ApiResponse::addInfoArray(__('Payment recorded successfully'));
        $response = new ApiResponse();
        return $response->displayWithResponse(true, $payment);
    }
}

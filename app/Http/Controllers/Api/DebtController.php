<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebtPaymentRequest;
use App\Http\Requests\DebtRequest;
use App\Http\Resources\DebtResource;
use App\Models\Debt;
use App\Services\ActivityLogger;
use App\Services\DebtService;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function __construct(
        private readonly DebtService $debtService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $response = new ApiDataResponse;
        $response->setDefaultSortData('id', 'desc');
        $response->searchFromRequest($request, Debt::class, DebtResource::class, ['payments.transaction.account']);

        return $response->display();
    }

    public function store(DebtRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $debt = $this->debtService->createDebt($request->validated(), $userId);

        $typeLabel = $debt->type === 'owed_to' ? 'Borrowed from' : 'Lent to';
        ActivityLogger::logCreated($debt, "{$typeLabel} {$debt->creditor_name} (".number_format((float) $debt->principal_amount, 2).')');

        ApiResponse::addInfoArray(__('Debt record created successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new DebtResource($debt));
    }

    public function show(int $id): JsonResponse
    {
        $debt = Debt::with(['payments.transaction.account'])->find($id);

        if (! $debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, new DebtResource($debt));
    }

    public function update(DebtRequest $request, int $id): JsonResponse
    {
        $debt = Debt::find($id);

        if (! $debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $debt->fill($request->validated());
        $debt->save();

        $typeLabel = $debt->type === 'owed_to' ? 'Debt to' : 'Loan to';
        ActivityLogger::logUpdated($debt, "{$typeLabel} {$debt->creditor_name} (".number_format((float) $debt->principal_amount, 2).')');

        ApiResponse::addInfoArray(__('Debt record updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new DebtResource($debt));
    }

    public function destroy(int $id): JsonResponse
    {
        $debt = Debt::find($id);

        if (! $debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        ActivityLogger::logDeleted($debt, "{$debt->creditor_name}");

        $debt->delete();

        ApiResponse::addInfoArray(__('Debt record deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }

    public function pay(DebtPaymentRequest $request, int $id): JsonResponse
    {
        $userId = auth()->id();
        $debt = Debt::find($id);

        if (! $debt) {
            ApiResponse::addErrorArray(__('Debt record not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $payment = $this->debtService->recordPayment($debt, $request->validated(), $userId);

        ApiResponse::addInfoArray(__('Payment recorded and debt status updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, $payment);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use appsbd\Libs\ApiDataResponse;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $response = new ApiDataResponse;
        $response->setDefaultSortData('date', 'desc');
        $response->searchFromRequest(
            $request,
            Transaction::class,
            TransactionResource::class,
            ['category', 'account', 'fromAccount']
        );

        return $response->display();
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        $userId = (int) (auth()->id() ?? 1);
        $transaction = $this->transactionService->create($request->validated(), $userId);

        ApiResponse::addInfoArray(__('Transaction recorded successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new TransactionResource($transaction));
    }

    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::with(['category', 'account', 'fromAccount'])->find($id);

        if (! $transaction) {
            ApiResponse::addErrorArray(__('Transaction not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $response = new ApiResponse;

        return $response->displayWithResponse(true, new TransactionResource($transaction));
    }

    public function update(TransactionRequest $request, int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (! $transaction) {
            ApiResponse::addErrorArray(__('Transaction not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $transaction = $this->transactionService->update($transaction, $request->validated());

        ApiResponse::addInfoArray(__('Transaction updated successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, new TransactionResource($transaction));
    }

    public function destroy(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if (! $transaction) {
            ApiResponse::addErrorArray(__('Transaction not found'));
            $response = new ApiResponse;

            return $response->displayWithResponse(false, null, 404);
        }

        $this->transactionService->delete($transaction);

        ApiResponse::addInfoArray(__('Transaction deleted successfully'));
        $response = new ApiResponse;

        return $response->displayWithResponse(true, null);
    }
}

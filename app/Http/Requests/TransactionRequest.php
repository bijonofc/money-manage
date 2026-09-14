<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class TransactionRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        return [
            'transaction_type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'from_account_id' => 'nullable|exists:accounts,id',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,mobile,check,other',
            'tags' => 'nullable|array',
        ];
    }
}

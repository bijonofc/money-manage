<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class DebtPaymentRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'nullable|exists:accounts,id',
            'payment_date' => 'nullable|date',
            'payment_time' => 'nullable|string',
            'note' => 'nullable|string',
        ];
    }
}

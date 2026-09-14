<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class DebtRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        return [
            'type' => 'required|in:owed_to,owed_from',
            'creditor_name' => 'required|string|max:255',
            'creditor_contact' => 'nullable|string|max:255',
            'principal_amount' => 'required|numeric|min:0.01',
            'paid_amount' => 'nullable|numeric|min:0',
            'account_id' => 'nullable|exists:accounts,id',
            'due_date' => 'nullable|date',
            'interest_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,paid,settled,defaulted',
        ];
    }
}

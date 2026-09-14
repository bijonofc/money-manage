<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class AccountRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'account_type' => 'required|in:cash,bank,mobile,credit_card,other',
            'balance' => 'nullable|numeric',
            'currency' => 'nullable|string|max:3',
            'account_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'meta' => 'nullable|array',
        ];
    }
}

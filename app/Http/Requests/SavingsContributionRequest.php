<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class SavingsContributionRequest extends AppFormRequest
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
            'note' => 'nullable|string|max:500',
        ];
    }
}

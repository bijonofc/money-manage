<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class BudgetRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'alert_threshold' => 'nullable|numeric|min:1|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }
}

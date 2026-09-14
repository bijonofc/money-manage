<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;

class SavingsGoalRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}

<?php

namespace App\Http\Requests;

use appsbd\Libs\AppFormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends AppFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function AppRules(): array
    {
        $tenantId = auth()->id();
        $id = $this->route('category')?->id ?? $this->route('category') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($id),
            ],
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ];
    }
}

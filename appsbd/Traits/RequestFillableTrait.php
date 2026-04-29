<?php
namespace appsbd\Traits;

use Illuminate\Foundation\Http\FormRequest;

trait RequestFillableTrait {
    public function fillFromRequest(FormRequest $request) {

        // Use validated() if available
        if (method_exists($request, 'validated')) {
            return parent::fill($request->validated());
        }

        // fallback
        return parent::fill($request->all());
    }
}

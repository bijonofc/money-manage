<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $principal = (float) $this->principal_amount;
        $paid = (float) $this->paid_amount;
        $remaining = max(0, $principal - $paid);

        return [
            'id' => $this->id,
            'type' => $this->type,
            'creditor_name' => $this->creditor_name,
            'creditor_contact' => $this->creditor_contact,
            'principal_amount' => $principal,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'interest_rate' => $this->interest_rate !== null ? (float) $this->interest_rate : null,
            'due_date' => $this->due_date?->format('Y-m-d') ?? (string) $this->due_date,
            'status' => $this->status,
            'description' => $this->description,
            'payments' => $this->whenLoaded('payments'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

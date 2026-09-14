<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_type' => $this->transaction_type,
            'amount' => (float) $this->amount,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'account_id' => $this->account_id,
            'account_name' => $this->account?->name,
            'from_account_id' => $this->from_account_id,
            'from_account_name' => $this->fromAccount?->name,
            'date' => $this->date?->format('Y-m-d') ?? (string) $this->date,
            'time' => $this->time,
            'description' => $this->description,
            'reference_number' => $this->reference_number,
            'payment_method' => $this->payment_method,
            'tags' => $this->tags,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

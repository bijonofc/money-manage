<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $start = $this->start_date?->toDateString();
        $end = $this->end_date?->toDateString();

        $spentQuery = Transaction::where('transaction_type', 'expense');
        if ($this->category_id) {
            $spentQuery->where('category_id', $this->category_id);
        }
        if ($start && $end) {
            $spentQuery->whereBetween('date', [$start, $end]);
        } elseif ($start) {
            $spentQuery->where('date', '>=', $start);
        }

        $spent = (float) $spentQuery->sum('amount');
        $budgetAmount = (float) $this->amount;
        $progress = $budgetAmount > 0 ? min(100, round(($spent / $budgetAmount) * 100, 1)) : 0;

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name ?? 'All Categories',
            'amount' => $budgetAmount,
            'spent_amount' => $spent,
            'remaining_amount' => max(0, $budgetAmount - $spent),
            'progress_percentage' => $progress,
            'period' => $this->period,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'alert_threshold' => (float) $this->alert_threshold,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

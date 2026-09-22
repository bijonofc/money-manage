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
        if ($this->tenant_id) {
            $spentQuery->where('tenant_id', $this->tenant_id);
        }
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
        $progress = $budgetAmount > 0 ? round(($spent / $budgetAmount) * 100, 1) : 0;
        $alertThreshold = (float) ($this->alert_threshold ?? 80);
        $isOver = $spent > $budgetAmount;
        $isAlert = $progress >= $alertThreshold;

        $healthStatus = 'on_track';
        if ($isOver) {
            $healthStatus = 'exceeded';
        } elseif ($isAlert) {
            $healthStatus = 'warning';
        }

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name ?? 'Overall Budget',
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'icon' => $this->category->icon,
                'color' => $this->category->color,
            ] : null,
            'amount' => $budgetAmount,
            'spent_amount' => $spent,
            'remaining_amount' => max(0, $budgetAmount - $spent),
            'over_amount' => max(0, $spent - $budgetAmount),
            'progress_percentage' => min(100, $progress),
            'actual_percentage' => $progress,
            'period' => $this->period,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'alert_threshold' => $alertThreshold,
            'status' => $this->status ?? 'A',
            'health_status' => $healthStatus,
            'is_over_budget' => $isOver,
            'is_alert_reached' => $isAlert,
            'is_active' => ($this->status ?? 'A') === 'A',
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

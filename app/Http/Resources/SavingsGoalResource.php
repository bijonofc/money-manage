<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingsGoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $target = (float) $this->target_amount;
        $current = (float) $this->current_amount;
        $progress = $target > 0 ? min(100, round(($current / $target) * 100, 1)) : 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'target_amount' => $target,
            'current_amount' => $current,
            'remaining_amount' => max(0, $target - $current),
            'progress_percentage' => $progress,
            'deadline' => $this->deadline?->format('Y-m-d') ?? (string) $this->deadline,
            'icon' => $this->icon,
            'color' => $this->color,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'contributions' => $this->whenLoaded('contributions'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

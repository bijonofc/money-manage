<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends AppModel
{
    use BelongsToTenant, HasFactory;

    public static function getDefaultSearchProps(): array
    {
        return ['name', 'description'];
    }

    protected $fillable = [
        'tenant_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'icon',
        'color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'deadline' => 'date',
        'is_active' => 'boolean',
    ];

    public function contributions(): HasMany
    {
        return $this->hasMany(SavingsContribution::class, 'goal_id');
    }

    public function getProgressAttribute(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }

        return round(($this->current_amount / $this->target_amount) * 100, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

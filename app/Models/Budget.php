<?php

namespace App\Models;

use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
        'is_active',
        'alert_threshold',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'alert_threshold' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(BudgetAlert::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

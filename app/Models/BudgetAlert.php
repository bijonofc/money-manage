<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAlert extends AppModel
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'budget_id',
        'tenant_id',
        'percentage',
        'is_notified',
        'notified_at',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}

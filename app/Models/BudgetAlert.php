<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'user_id',
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

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}

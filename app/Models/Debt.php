<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'creditor_name',
        'creditor_contact',
        'principal_amount',
        'paid_amount',
        'interest_rate',
        'due_date',
        'description',
        'status',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function getOutstandingAttribute(): float
    {
        return $this->principal_amount - $this->paid_amount;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOwedTo($query)
    {
        return $query->where('type', 'owed_to');
    }

    public function scopeOwedFrom($query)
    {
        return $query->where('type', 'owed_from');
    }
}

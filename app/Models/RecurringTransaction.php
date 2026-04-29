<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'transaction_type',
        'amount',
        'category_id',
        'account_id',
        'frequency',
        'start_date',
        'end_date',
        'day_of_month',
        'day_of_week',
        'is_active',
        'last_processed_date',
        'next_due_date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'last_processed_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'recurring_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereBetween('next_due_date', [now(), now()->addDays($days)]);
    }
}

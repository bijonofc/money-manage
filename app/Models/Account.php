<?php

namespace App\Models;

use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'account_type',
        'account_number',
        'balance',
        'currency',
        'meta',
        'is_active',
        'description',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function fromTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function recurringTransactions(): HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends AppModel
{
    use BelongsToTenant, HasFactory;

    public static function getDefaultSearchProps(): array
    {
        return ['name', 'account_number', 'account_type', 'description'];
    }

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

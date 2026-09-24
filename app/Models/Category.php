<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends AppModel
{
    use BelongsToTenant, HasFactory;

    public static function getDefaultSearchProps(): array
    {
        return ['name', 'type'];
    }

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'parent_id',
        'icon',
        'color',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

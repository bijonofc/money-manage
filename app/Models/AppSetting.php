<?php

namespace App\Models;

use appsbd\Core\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppSetting extends AppModel
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'tenant_id',
        'group_slug',
        's_key',
        's_val',
        's_type',
    ];

    protected $appends = [
        's_value',
        'setting_value',
    ];

    /**
     * Accessor for s_value with automatic type casting.
     */
    public function getSValueAttribute(): mixed
    {
        return $this->castValue($this->s_val, $this->s_type);
    }

    /**
     * Accessor for setting_value alias.
     */
    public function getSettingValueAttribute(): mixed
    {
        return $this->getSValueAttribute();
    }

    /**
     * Mutator for s_value.
     */
    public function setSValueAttribute(mixed $value): void
    {
        if (is_bool($value)) {
            $this->attributes['s_val'] = $value ? '1' : '0';
            $this->attributes['s_type'] = 'boolean';
        } elseif (is_array($value) || is_object($value)) {
            $this->attributes['s_val'] = json_encode($value);
            $this->attributes['s_type'] = 'json';
        } elseif (is_int($value)) {
            $this->attributes['s_val'] = (string) $value;
            $this->attributes['s_type'] = 'integer';
        } elseif (is_numeric($value)) {
            $this->attributes['s_val'] = (string) $value;
            $this->attributes['s_type'] = 'numeric';
        } else {
            $this->attributes['s_val'] = $value !== null ? (string) $value : null;
            $this->attributes['s_type'] = 'string';
        }
    }

    /**
     * Cast string value from DB into appropriate PHP type.
     */
    protected function castValue(?string $value, ?string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'numeric', 'float' => is_numeric($value) ? (float) $value : $value,
            'json', 'array' => json_decode($value, true) ?? $value,
            default => $value,
        };
    }

    /**
     * Belongs to tenant/user relation.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}

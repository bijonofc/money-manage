<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use appsbd\Core\AppAuthModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends AppAuthModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public static function getDefaultSearchProps(): array
    {
        return ['name', 'email', 'username', 'contact_no'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'contact_no',
        'address',
        'role_id',
        'status',
        'is_sso',
        'google_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['role_name'];

    public function isPending(): bool
    {
        return $this->status === 'P';
    }

    public function isActive(): bool
    {
        return $this->status === 'A';
    }

    public function isInactive(): bool
    {
        return $this->status === 'I';
    }

    public function getRoleNameAttribute()
    {
        return $this->role?->title ?? 'User';
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'tenant_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'tenant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'tenant_id');
    }

    public function recurringTransactions()
    {
        return $this->hasMany(RecurringTransaction::class, 'tenant_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'tenant_id');
    }

    public function savingsGoals()
    {
        return $this->hasMany(SavingsGoal::class, 'tenant_id');
    }

    public function debts()
    {
        return $this->hasMany(Debt::class, 'tenant_id');
    }
}

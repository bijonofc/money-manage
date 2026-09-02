<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use appsbd\Traits\SearchDataTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SearchDataTrait;

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

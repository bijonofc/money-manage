<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $defaultAccounts = [
            ['name' => 'Cash', 'account_type' => 'cash'],
            ['name' => 'Bank Account', 'account_type' => 'bank'],
            ['name' => 'bKash', 'account_type' => 'mobile'],
            ['name' => 'Credit Card', 'account_type' => 'card'],
        ];

        foreach ($defaultAccounts as $account) {
            Account::create([
                'user_id' => 1,
                'name' => $account['name'],
                'account_type' => $account['account_type'],
                'balance' => 0.00,
                'currency' => 'BDT',
                'is_active' => true,
            ]);
        }
    }
}

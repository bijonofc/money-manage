<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        if (Account::count() == 0) {
            $defaultAccounts = [
                [
                    'name' => 'City Bank (Salary)',
                    'account_type' => 'bank',
                    'account_number' => '2201948572001',
                    'meta' => null,
                ],
                [
                    'name' => 'BRAC Bank (Savings)',
                    'account_type' => 'bank',
                    'account_number' => '1501203498001',
                    'meta' => null,
                ],
                [
                    'name' => 'Cash Wallet',
                    'account_type' => 'cash',
                    'account_number' => null,
                    'meta' => null,
                ],
                [
                    'name' => 'bKash Personal',
                    'account_type' => 'mobile',
                    'account_number' => '01721544957',
                    'meta' => null,
                ],
                [
                    'name' => 'City Maxx Credit Card',
                    'account_type' => 'credit_card',
                    'account_number' => '4105-XXXX-XXXX-9821',
                    'meta' => [
                        'credit_limit' => 200000,
                        'billing_cycle_day' => 15,
                        'payment_due_day' => 5,
                    ],
                ],
            ];

            foreach ($defaultAccounts as $account) {
                Account::create([
                    'tenant_id' => 1,
                    'name' => $account['name'],
                    'account_type' => $account['account_type'],
                    'account_number' => $account['account_number'] ?? null,
                    'balance' => 0.00,
                    'currency' => 'BDT',
                    'meta' => $account['meta'] ?? null,
                    'is_active' => true,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetAlert;
use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\RecurringTransaction;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (! $user) {
            return;
        }

        $tenantId = $user->id;
        $userId = $user->id;

        // 1. Seed tenant settings
        app(SettingService::class)->seedDefaults($tenantId);

        // Map categories and accounts for easy reference
        $incomeCategories = Category::where('tenant_id', $tenantId)->where('type', 'income')->pluck('id', 'name');
        $expenseCategories = Category::where('tenant_id', $tenantId)->where('type', 'expense')->pluck('id', 'name');
        $accounts = Account::where('tenant_id', $tenantId)->pluck('id', 'name');

        $cityBank = $accounts['City Bank (Salary)'] ?? Account::where('account_type', 'bank')->first()->id;
        $bracBank = $accounts['BRAC Bank (Savings)'] ?? Account::where('account_type', 'bank')->skip(1)->first()?->id ?? $cityBank;
        $cashWallet = $accounts['Cash Wallet'] ?? Account::where('account_type', 'cash')->first()->id;
        $bkash = $accounts['bKash Personal'] ?? Account::where('account_type', 'mobile')->first()->id;
        $creditCard = $accounts['City Maxx Credit Card'] ?? Account::where('account_type', 'credit_card')->first()->id;

        // 2. Create Recurring Transactions
        $recurringRules = [
            [
                'name' => 'Tech Lead Monthly Salary',
                'transaction_type' => 'income',
                'amount' => 285000.00,
                'category_id' => $incomeCategories['Salary/Wages'] ?? null,
                'account_id' => $cityBank,
                'frequency' => 'monthly',
                'start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
                'day_of_month' => 1,
                'is_active' => true,
                'next_due_date' => Carbon::now()->addMonth()->startOfMonth()->toDateString(),
                'description' => 'Direct bank transfer of executive tech salary',
            ],
            [
                'name' => 'Apartment Rent - Gulshan 2',
                'transaction_type' => 'expense',
                'amount' => 48000.00,
                'category_id' => $expenseCategories['Housing/Rent'] ?? null,
                'account_id' => $bracBank,
                'frequency' => 'monthly',
                'start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
                'day_of_month' => 5,
                'is_active' => true,
                'next_due_date' => Carbon::now()->addMonth()->startOfMonth()->addDays(4)->toDateString(),
                'description' => 'Monthly residential apartment rent',
            ],
            [
                'name' => 'High-Speed Fiber Internet & WiFi',
                'transaction_type' => 'expense',
                'amount' => 2800.00,
                'category_id' => $expenseCategories['Utilities'] ?? null,
                'account_id' => $bkash,
                'frequency' => 'monthly',
                'start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
                'day_of_month' => 7,
                'is_active' => true,
                'next_due_date' => Carbon::now()->addMonth()->startOfMonth()->addDays(6)->toDateString(),
                'description' => '100 Mbps fiber optic connection subscription',
            ],
            [
                'name' => 'Fitness Gym Membership',
                'transaction_type' => 'expense',
                'amount' => 3500.00,
                'category_id' => $expenseCategories['Personal Care'] ?? null,
                'account_id' => $creditCard,
                'frequency' => 'monthly',
                'start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
                'day_of_month' => 10,
                'is_active' => true,
                'next_due_date' => Carbon::now()->addMonth()->startOfMonth()->addDays(9)->toDateString(),
                'description' => 'Monthly gym pass & health club access',
            ],
            [
                'name' => 'Cloud Servers & AI SaaS Tools',
                'transaction_type' => 'expense',
                'amount' => 12500.00,
                'category_id' => $expenseCategories['Education'] ?? null,
                'account_id' => $creditCard,
                'frequency' => 'monthly',
                'start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
                'day_of_month' => 12,
                'is_active' => true,
                'next_due_date' => Carbon::now()->addMonth()->startOfMonth()->addDays(11)->toDateString(),
                'description' => 'AWS, OpenAI, GitHub and Claude Pro subscriptions',
            ],
            [
                'name' => 'Life & Medical Insurance Premium',
                'transaction_type' => 'expense',
                'amount' => 7500.00,
                'category_id' => $expenseCategories['Insurance'] ?? null,
                'account_id' => $bracBank,
                'frequency' => 'monthly',
                'start_date' => Carbon::now()->subMonths(6)->startOfMonth()->toDateString(),
                'day_of_month' => 20,
                'is_active' => true,
                'next_due_date' => Carbon::now()->addMonth()->startOfMonth()->addDays(19)->toDateString(),
                'description' => 'MetLife comprehensive health & term policy',
            ],
        ];

        foreach ($recurringRules as $rule) {
            RecurringTransaction::create(array_merge($rule, ['tenant_id' => $tenantId]));
        }

        // 3. Create Budgets (Active for Current Month & Year)
        $budgetsConfig = [
            [
                'category' => 'Food & Groceries',
                'amount' => 35000.00,
                'period' => 'monthly',
                'alert_threshold' => 80.00,
            ],
            [
                'category' => 'Housing/Rent',
                'amount' => 50000.00,
                'period' => 'monthly',
                'alert_threshold' => 95.00,
            ],
            [
                'category' => 'Utilities',
                'amount' => 14000.00,
                'period' => 'monthly',
                'alert_threshold' => 85.00,
            ],
            [
                'category' => 'Transportation',
                'amount' => 18000.00,
                'period' => 'monthly',
                'alert_threshold' => 80.00,
            ],
            [
                'category' => 'Shopping',
                'amount' => 30000.00,
                'period' => 'monthly',
                'alert_threshold' => 75.00,
            ],
            [
                'category' => 'Entertainment',
                'amount' => 12000.00,
                'period' => 'monthly',
                'alert_threshold' => 80.00,
            ],
            [
                'category' => 'Healthcare',
                'amount' => 15000.00,
                'period' => 'monthly',
                'alert_threshold' => 80.00,
            ],
            [
                'category' => 'Personal Care',
                'amount' => 10000.00,
                'period' => 'monthly',
                'alert_threshold' => 80.00,
            ],
        ];

        $now = Carbon::now();
        foreach ($budgetsConfig as $b) {
            if (isset($expenseCategories[$b['category']])) {
                Budget::create([
                    'tenant_id' => $tenantId,
                    'category_id' => $expenseCategories[$b['category']],
                    'amount' => $b['amount'],
                    'period' => $b['period'],
                    'start_date' => $now->copy()->startOfMonth()->toDateString(),
                    'end_date' => $now->copy()->endOfMonth()->toDateString(),
                    'status' => 'A',
                    'alert_threshold' => $b['alert_threshold'],
                ]);
            }
        }

        // 4. Create Savings Goals
        $goalEmergency = SavingsGoal::create([
            'tenant_id' => $tenantId,
            'name' => 'Emergency Fund Reserve',
            'target_amount' => 500000.00,
            'current_amount' => 325000.00,
            'deadline' => $now->copy()->addMonths(4)->endOfMonth()->toDateString(),
            'icon' => 'shield',
            'color' => '#10b981',
            'description' => 'Target 6 months of complete household emergency reserves',
            'is_active' => true,
        ]);

        $goalJapan = SavingsGoal::create([
            'tenant_id' => $tenantId,
            'name' => 'Japan Autumn Vacation',
            'target_amount' => 400000.00,
            'current_amount' => 260000.00,
            'deadline' => $now->copy()->addMonths(7)->toDateString(),
            'icon' => 'plane',
            'color' => '#f59e0b',
            'description' => 'Tokyo, Kyoto, and Mount Fuji 12-day vacation savings',
            'is_active' => true,
        ]);

        $goalTech = SavingsGoal::create([
            'tenant_id' => $tenantId,
            'name' => 'Home Office & Tech Upgrade',
            'target_amount' => 200000.00,
            'current_amount' => 165000.00,
            'deadline' => $now->copy()->addMonths(2)->toDateString(),
            'icon' => 'laptop',
            'color' => '#3b82f6',
            'description' => 'M3 Max MacBook, 4K Ultrawide monitor, and Herman Miller chair',
            'is_active' => true,
        ]);

        $goalMedical = SavingsGoal::create([
            'tenant_id' => $tenantId,
            'name' => 'Family Health & Medical Buffer',
            'target_amount' => 250000.00,
            'current_amount' => 140000.00,
            'deadline' => $now->copy()->addMonths(5)->toDateString(),
            'icon' => 'heart',
            'color' => '#ec4899',
            'description' => 'Liquid medical buffer for unexpected family health events',
            'is_active' => true,
        ]);

        // 5. Create Debts / Loans
        $debtCarLoan = Debt::create([
            'tenant_id' => $tenantId,
            'type' => 'owed_to',
            'creditor_name' => 'City Bank Auto Loan',
            'creditor_contact' => '16234 / Priority Banking',
            'principal_amount' => 650000.00,
            'paid_amount' => 422500.00,
            'interest_rate' => 8.75,
            'due_date' => $now->copy()->addMonths(14)->toDateString(),
            'description' => 'Toyota sedan auto loan financing with 3-year EMI tenure',
            'status' => 'active',
        ]);

        $debtFriend = Debt::create([
            'tenant_id' => $tenantId,
            'type' => 'owed_to',
            'creditor_name' => 'Farhan Ahmed (Friend)',
            'creditor_contact' => '+8801819234567',
            'principal_amount' => 70000.00,
            'paid_amount' => 45000.00,
            'interest_rate' => 0.00,
            'due_date' => $now->copy()->addMonths(2)->toDateString(),
            'description' => 'Short-term friendly personal loan during house moving',
            'status' => 'active',
        ]);

        $debtColleague = Debt::create([
            'tenant_id' => $tenantId,
            'type' => 'owed_from',
            'creditor_name' => 'Tanvir Hossain (Colleague)',
            'creditor_contact' => '+8801711987654',
            'principal_amount' => 60000.00,
            'paid_amount' => 35000.00,
            'interest_rate' => 0.00,
            'due_date' => $now->copy()->addMonths(3)->toDateString(),
            'description' => 'Lent to Tanvir for urgent medical and travel support',
            'status' => 'active',
        ]);

        // 6. Generate Massive Realistic Multi-Month Transactions
        // Months covered: 3 months ago, 2 months ago, last month, and current month!
        $monthsToSeed = [
            -3 => ['days' => 30, 'bonus' => true],
            -2 => ['days' => 31, 'bonus' => false],
            -1 => ['days' => 31, 'bonus' => false],
            0 => ['days' => min(22, $now->day), 'bonus' => false],
        ];

        $refCounter = 1000;

        foreach ($monthsToSeed as $monthOffset => $monthMeta) {
            $mCarbon = $now->copy()->addMonths($monthOffset);
            $year = $mCarbon->year;
            $month = $mCarbon->month;
            $maxDay = $monthMeta['days'];

            // Helper to get formatted date
            $getDate = function (int $day) use ($year, $month, $maxDay) {
                $actualDay = min($day, $maxDay);

                return sprintf('%04d-%02d-%02d', $year, $month, $actualDay);
            };

            // Helper to get transaction reference
            $getRef = function () use (&$refCounter, $year, $month) {
                $refCounter++;

                return sprintf('TXN-%04d%02d-%05d', $year, $month, $refCounter);
            };

            // A) SALARY (1st of month)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'income',
                'amount' => 285000.00,
                'category_id' => $incomeCategories['Salary/Wages'] ?? null,
                'account_id' => $cityBank,
                'date' => $getDate(1),
                'time' => '09:30:00',
                'description' => 'Tech Lead Monthly Salary credited',
                'reference_number' => $getRef(),
                'payment_method' => 'bank_transfer',
                'is_recurring' => true,
                'tags' => ['salary', 'primary-income', 'corporate'],
            ]);

            // B) FREELANCE / CONSULTING (3rd - 8th)
            $freelanceAmount = ($monthOffset == -3) ? 145000.00 : (($monthOffset == -1) ? 115000.00 : 92000.00);
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'income',
                'amount' => $freelanceAmount,
                'category_id' => $incomeCategories['Freelance'] ?? null,
                'account_id' => $bracBank,
                'date' => $getDate(4),
                'time' => '17:15:00',
                'description' => 'Fintech Architecture Consulting Milestone',
                'reference_number' => $getRef(),
                'payment_method' => 'bank_transfer',
                'tags' => ['consulting', 'freelance', 'us-client'],
            ]);

            // Bonus in older month
            if ($monthMeta['bonus']) {
                Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'income',
                    'amount' => 150000.00,
                    'category_id' => $incomeCategories['Investment Returns'] ?? null,
                    'account_id' => $cityBank,
                    'date' => $getDate(18),
                    'time' => '11:00:00',
                    'description' => 'Semi-annual stock dividend & portfolio returns',
                    'reference_number' => $getRef(),
                    'payment_method' => 'bank_transfer',
                    'tags' => ['dividend', 'investment', 'stocks'],
                ]);
            }

            // C) APARTMENT RENT (5th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 48000.00,
                'category_id' => $expenseCategories['Housing/Rent'] ?? null,
                'account_id' => $bracBank,
                'date' => $getDate(5),
                'time' => '10:00:00',
                'description' => 'Monthly Apartment Rent paid to landlord',
                'reference_number' => $getRef(),
                'payment_method' => 'bank_transfer',
                'is_recurring' => true,
                'tags' => ['rent', 'fixed-expense', 'home'],
            ]);

            // D) TRANSFER TO BKASH FOR MONTHLY EXPENSES (6th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'transfer',
                'amount' => 25000.00,
                'account_id' => $bkash,
                'from_account_id' => $cityBank,
                'date' => $getDate(6),
                'time' => '14:20:00',
                'description' => 'Fund bKash wallet from City Bank for bills & food',
                'reference_number' => $getRef(),
                'payment_method' => 'mobile',
                'tags' => ['transfer', 'bkash-topup'],
            ]);

            // E) INTERNET BILL (7th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 2800.00,
                'category_id' => $expenseCategories['Utilities'] ?? null,
                'account_id' => $bkash,
                'date' => $getDate(7),
                'time' => '16:45:00',
                'description' => 'Carnival Internet Fiber bill payment',
                'reference_number' => $getRef(),
                'payment_method' => 'mobile',
                'is_recurring' => true,
                'tags' => ['internet', 'utilities'],
            ]);

            // F) GROCERY SHOPPING #1 - UNIMART (8th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 14350.00,
                'category_id' => $expenseCategories['Food & Groceries'] ?? null,
                'account_id' => $creditCard,
                'date' => $getDate(8),
                'time' => '19:40:00',
                'description' => 'Monthly bulk grocery shopping at Unimart Gulshan',
                'reference_number' => $getRef(),
                'payment_method' => 'card',
                'tags' => ['groceries', 'supermarket', 'household'],
            ]);

            // G) ATM CASH WITHDRAWAL (9th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'transfer',
                'amount' => 20000.00,
                'account_id' => $cashWallet,
                'from_account_id' => $cityBank,
                'date' => $getDate(9),
                'time' => '11:10:00',
                'description' => 'City Bank FastNet ATM cash withdrawal',
                'reference_number' => $getRef(),
                'payment_method' => 'cash',
                'tags' => ['atm', 'cash-withdrawal'],
            ]);

            // H) LOCAL BAZAAR FRESH PRODUCE (10th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 3850.00,
                'category_id' => $expenseCategories['Food & Groceries'] ?? null,
                'account_id' => $cashWallet,
                'date' => $getDate(10),
                'time' => '08:30:00',
                'description' => 'Fresh fish, beef, and vegetables from local bazaar',
                'reference_number' => $getRef(),
                'payment_method' => 'cash',
                'tags' => ['fresh-bazaar', 'cash'],
            ]);

            // I) CAR LOAN EMI PAYMENT (11th)
            $carLoanTxn = Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 32500.00,
                'category_id' => $expenseCategories['Debt Payments'] ?? null,
                'account_id' => $cityBank,
                'date' => $getDate(11),
                'time' => '10:15:00',
                'description' => 'City Bank Auto Loan Monthly EMI installment',
                'reference_number' => $getRef(),
                'payment_method' => 'bank_transfer',
                'tags' => ['emi', 'auto-loan', 'debt-repayment'],
            ]);

            DebtPayment::create([
                'debt_id' => $debtCarLoan->id,
                'transaction_id' => $carLoanTxn->id,
                'amount' => 32500.00,
                'payment_date' => $getDate(11),
                'note' => 'Monthly scheduled vehicle loan installment',
            ]);

            // J) SAAS TOOLS & SERVER SUBSCRIPTION (12th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 12500.00,
                'category_id' => $expenseCategories['Education'] ?? null,
                'account_id' => $creditCard,
                'date' => $getDate(12),
                'time' => '03:15:00',
                'description' => 'AWS Cloud servers & OpenAI API billing',
                'reference_number' => $getRef(),
                'payment_method' => 'card',
                'is_recurring' => true,
                'tags' => ['saas', 'development', 'tech'],
            ]);

            // K) FUEL & VEHICLE EXPENSE (13th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 5500.00,
                'category_id' => $expenseCategories['Transportation'] ?? null,
                'account_id' => $creditCard,
                'date' => $getDate(13),
                'time' => '18:50:00',
                'description' => 'Octane full tank refill at Trust Filling Station',
                'reference_number' => $getRef(),
                'payment_method' => 'card',
                'tags' => ['fuel', 'car', 'transport'],
            ]);

            // L) ELECTRICITY BILL DESCO (14th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 7650.00,
                'category_id' => $expenseCategories['Utilities'] ?? null,
                'account_id' => $bkash,
                'date' => $getDate(14),
                'time' => '12:40:00',
                'description' => 'DESCO Prepaid electricity recharge via bKash',
                'reference_number' => $getRef(),
                'payment_method' => 'mobile',
                'tags' => ['electricity', 'utilities', 'desco'],
            ]);

            // M) SAVINGS CONTRIBUTION - EMERGENCY FUND (15th)
            $saveTxn1 = Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 30000.00,
                'category_id' => $expenseCategories['Other Expenses'] ?? null,
                'account_id' => $bracBank,
                'date' => $getDate(15),
                'time' => '11:00:00',
                'description' => 'Transfer to Emergency Fund High-Yield Deposit',
                'reference_number' => $getRef(),
                'payment_method' => 'bank_transfer',
                'tags' => ['savings', 'emergency-fund', 'goal'],
            ]);

            SavingsContribution::create([
                'goal_id' => $goalEmergency->id,
                'transaction_id' => $saveTxn1->id,
                'amount' => 30000.00,
                'note' => 'Monthly dedicated contribution to emergency reserves',
            ]);

            // N) DINING OUT / RESTAURANT (16th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 4850.00,
                'category_id' => $expenseCategories['Entertainment'] ?? null,
                'account_id' => $creditCard,
                'date' => $getDate(16),
                'time' => '21:15:00',
                'description' => 'Weekend dinner with family at Butlers Chocolate Cafe',
                'reference_number' => $getRef(),
                'payment_method' => 'card',
                'tags' => ['dining', 'family', 'weekend'],
            ]);

            // O) HEALTHCARE / MEDICINE (17th)
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'expense',
                'amount' => 4200.00,
                'category_id' => $expenseCategories['Healthcare'] ?? null,
                'account_id' => $bkash,
                'date' => $getDate(17),
                'time' => '16:20:00',
                'description' => 'Prescription medicines and vitamins at Lazz Pharma',
                'reference_number' => $getRef(),
                'payment_method' => 'mobile',
                'tags' => ['pharmacy', 'health'],
            ]);

            // P) CREDIT CARD BILL PAYMENT (20th)
            // Transfer from Bank to Credit Card to pay off balance!
            Transaction::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'transaction_type' => 'transfer',
                'amount' => 45000.00,
                'account_id' => $creditCard,
                'from_account_id' => $cityBank,
                'date' => $getDate(20),
                'time' => '15:30:00',
                'description' => 'Credit Card bill full statement settlement',
                'reference_number' => $getRef(),
                'payment_method' => 'bank_transfer',
                'tags' => ['credit-card-payment', 'bill-settlement'],
            ]);

            // Only generate later days if month has >= 22 days
            if ($maxDay >= 22) {
                // Q) SAVINGS CONTRIBUTION - JAPAN VACATION (21st)
                $saveTxn2 = Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'expense',
                    'amount' => 25000.00,
                    'category_id' => $expenseCategories['Other Expenses'] ?? null,
                    'account_id' => $bracBank,
                    'date' => $getDate(21),
                    'time' => '10:45:00',
                    'description' => 'Dedicated savings deposit for Japan tour',
                    'reference_number' => $getRef(),
                    'payment_method' => 'bank_transfer',
                    'tags' => ['savings', 'travel', 'japan-vacation'],
                ]);

                SavingsContribution::create([
                    'goal_id' => $goalJapan->id,
                    'transaction_id' => $saveTxn2->id,
                    'amount' => 25000.00,
                    'note' => 'Allocated travel savings deposit',
                ]);

                // R) UBER RIDES & COMMUTE (22nd)
                Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'expense',
                    'amount' => 1450.00,
                    'category_id' => $expenseCategories['Transportation'] ?? null,
                    'account_id' => $bkash,
                    'date' => $getDate(22),
                    'time' => '19:10:00',
                    'description' => 'Uber Premier rides for meetings in Banani & Gulshan',
                    'reference_number' => $getRef(),
                    'payment_method' => 'mobile',
                    'tags' => ['uber', 'commute'],
                ]);
            }

            if ($maxDay >= 25) {
                // S) SHOPPING & TECH GEAR (25th)
                Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'expense',
                    'amount' => 18500.00,
                    'category_id' => $expenseCategories['Shopping'] ?? null,
                    'account_id' => $creditCard,
                    'date' => $getDate(25),
                    'time' => '17:30:00',
                    'description' => 'Apple original accessories and wireless earbuds',
                    'reference_number' => $getRef(),
                    'payment_method' => 'card',
                    'tags' => ['shopping', 'tech', 'gadgets'],
                ]);

                // T) PERSONAL LOAN REPAYMENT (27th)
                $friendTxn = Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'expense',
                    'amount' => 15000.00,
                    'category_id' => $expenseCategories['Debt Payments'] ?? null,
                    'account_id' => $bracBank,
                    'date' => $getDate(27),
                    'time' => '14:00:00',
                    'description' => 'Repayment installment to Farhan Ahmed',
                    'reference_number' => $getRef(),
                    'payment_method' => 'bank_transfer',
                    'tags' => ['debt', 'friend-loan'],
                ]);

                DebtPayment::create([
                    'debt_id' => $debtFriend->id,
                    'transaction_id' => $friendTxn->id,
                    'amount' => 15000.00,
                    'payment_date' => $getDate(27),
                    'note' => 'Installment transfer to Farhan bank account',
                ]);

                // U) RECEIVED REPAYMENT FROM TANVIR (28th)
                Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'income',
                    'amount' => 10000.00,
                    'category_id' => $incomeCategories['Gifts/Refunds'] ?? null,
                    'account_id' => $bkash,
                    'date' => $getDate(28),
                    'time' => '16:00:00',
                    'description' => 'Received partial debt repayment from Tanvir via bKash',
                    'reference_number' => $getRef(),
                    'payment_method' => 'mobile',
                    'tags' => ['debt-collection', 'inflow'],
                ]);

                // V) WEEKEND LEISURE & COFFEE (29th)
                Transaction::create([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'transaction_type' => 'expense',
                    'amount' => 1850.00,
                    'category_id' => $expenseCategories['Entertainment'] ?? null,
                    'account_id' => $creditCard,
                    'date' => $getDate(29),
                    'time' => '20:00:00',
                    'description' => 'North End specialty coffee and pastries',
                    'reference_number' => $getRef(),
                    'payment_method' => 'card',
                    'tags' => ['coffee', 'weekend'],
                ]);
            }
        }

        // 7. Calculate and Update 100% Mathematically Consistent Account Balances
        // We give healthy initial baselines so all accounts have realistic positive balances:
        $baselineBalances = [
            $cityBank => 180000.00,
            $bracBank => 420000.00,
            $cashWallet => 15000.00,
            $bkash => 12000.00,
            $creditCard => 0.00,
        ];

        foreach ($accounts as $accName => $accId) {
            $base = $baselineBalances[$accId] ?? 0.00;

            // Incomes into this account
            $incomes = (float) Transaction::where('account_id', $accId)
                ->where('transaction_type', 'income')
                ->sum('amount');

            // Expenses from this account
            $expenses = (float) Transaction::where('account_id', $accId)
                ->where('transaction_type', 'expense')
                ->sum('amount');

            // Transfers where account is recipient
            $transfersIn = (float) Transaction::where('account_id', $accId)
                ->where('transaction_type', 'transfer')
                ->sum('amount');

            // Transfers where account is source
            $transfersOut = (float) Transaction::where('from_account_id', $accId)
                ->where('transaction_type', 'transfer')
                ->sum('amount');

            // If account is credit card:
            // balance represents liability (charges decrease it into negative, payments from bank increase it back toward 0)
            if ($accId === $creditCard) {
                // In our design: expense on credit card reduces balance into negative, transfer to credit card increases balance
                $finalBalance = 0.00 - $expenses + $transfersIn;
            } else {
                $finalBalance = $base + $incomes - $expenses + $transfersIn - $transfersOut;
            }

            Account::where('id', $accId)->update(['balance' => round($finalBalance, 2)]);
        }
    }
}

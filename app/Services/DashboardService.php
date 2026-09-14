<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Role;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getInitialData(int $userId): array
    {
        $users = User::select('id', 'name', 'email', 'username', 'role_id', 'contact_no')->get();
        $roles = Role::all();

        $accounts = Account::whereRaw('is_active IS TRUE')->get();
        $totalBalance = (float) $accounts->sum('balance');

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $monthlyIncome = (float) Transaction::where('transaction_type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyExpense = (float) Transaction::where('transaction_type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalIncome = (float) Transaction::where('transaction_type', 'income')
            ->sum('amount');

        $totalExpense = (float) Transaction::where('transaction_type', 'expense')
            ->sum('amount');

        $recentTransactions = Transaction::with(['category', 'account'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $savingsGoals = SavingsGoal::whereRaw('is_active IS TRUE')
            ->limit(5)
            ->get();

        $budgets = Budget::with('category')
            ->whereRaw('is_active IS TRUE')
            ->limit(5)
            ->get();

        $debts = Debt::where('status', 'active')
            ->limit(5)
            ->get();

        $categorySpending = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.transaction_type', 'expense')
            ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth])
            ->select(
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total_amount')
            ->limit(6)
            ->get();

        return [
            'userList' => $users,
            'roleList' => $roles,
            'customerList' => [],
            'packageList' => [],
            'badgeList' => [],
            'stats' => [
                'total_balance' => $totalBalance,
                'monthly_income' => $monthlyIncome,
                'monthly_expense' => $monthlyExpense,
                'net_savings' => $monthlyIncome - $monthlyExpense,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'total_accounts' => $accounts->count(),
                'total_categories' => Category::count(),
                'total_transactions' => Transaction::count(),
            ],
            'accounts' => $accounts,
            'recent_transactions' => $recentTransactions,
            'savings_goals' => $savingsGoals,
            'budgets' => $budgets,
            'debts' => $debts,
            'category_spending' => $categorySpending,
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use appsbd\Libs\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Return initial data and financial dashboard statistics.
     */
    public function initialData(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $users = User::select('id', 'name', 'email', 'username', 'role_id', 'contact_no')->get();
        $roles = Role::all();

        $accounts = Account::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $totalBalance = $accounts->sum('balance');

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $monthlyIncome = Transaction::where('tenant_id', $tenantId)
            ->where('transaction_type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthlyExpense = Transaction::where('tenant_id', $tenantId)
            ->where('transaction_type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalIncome = Transaction::where('tenant_id', $tenantId)
            ->where('transaction_type', 'income')
            ->sum('amount');

        $totalExpense = Transaction::where('tenant_id', $tenantId)
            ->where('transaction_type', 'expense')
            ->sum('amount');

        $recentTransactions = Transaction::with(['category', 'account'])
            ->where('tenant_id', $tenantId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $savingsGoals = \App\Models\SavingsGoal::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        $budgets = \App\Models\Budget::with('category')
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        $debts = \App\Models\Debt::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->limit(5)
            ->get();

        $categorySpending = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.tenant_id', $tenantId)
            ->where('transactions.transaction_type', 'expense')
            ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth])
            ->select(
                'categories.name',
                'categories.color',
                'categories.icon',
                \Illuminate\Support\Facades\DB::raw('SUM(transactions.amount) as total_amount')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total_amount')
            ->limit(6)
            ->get();

        $data = [
            'userList'            => $users,
            'roleList'            => $roles,
            'customerList'        => [],
            'packageList'         => [],
            'badgeList'           => [],
            'stats'               => [
                'total_balance'      => (float)$totalBalance,
                'monthly_income'     => (float)$monthlyIncome,
                'monthly_expense'    => (float)$monthlyExpense,
                'net_savings'        => (float)($monthlyIncome - $monthlyExpense),
                'total_income'       => (float)$totalIncome,
                'total_expense'      => (float)$totalExpense,
                'total_accounts'     => $accounts->count(),
                'total_categories'   => Category::where('tenant_id', $tenantId)->count(),
                'total_transactions' => Transaction::where('tenant_id', $tenantId)->count(),
            ],
            'accounts'            => $accounts,
            'recent_transactions' => $recentTransactions,
            'savings_goals'       => $savingsGoals,
            'budgets'             => $budgets,
            'debts'               => $debts,
            'category_spending'   => $categorySpending,
        ];

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $data);
    }

    /**
     * Return notification count / summary.
     */
    public function notifications(Request $request)
    {
        $response = new ApiResponse();
        return $response->displayWithResponse(true, [
            'unread_count' => 0,
            'list'         => [],
        ]);
    }

    /**
     * Return list of notifications.
     */
    public function notificationList(Request $request)
    {
        $response = new ApiResponse();
        return $response->displayWithResponse(true, [
            'rowdata'       => [],
            'recordsTotal'  => 0,
            'recordsFiltered' => 0,
        ]);
    }
}

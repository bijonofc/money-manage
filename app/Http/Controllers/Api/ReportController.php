<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use appsbd\Libs\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get aggregated financial reports and analytics overview.
     */
    public function overview(Request $request)
    {
        $tenantId = auth()->id() ?? 1;

        // Parse Date Range
        $preset = $request->input('preset', 'this_month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $accountId = $request->input('account_id');

        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($preset) {
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
                    $endDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
                    break;
                case 'last_3_months':
                    $startDate = $now->copy()->subMonths(2)->startOfMonth()->toDateString();
                    $endDate = $now->copy()->endOfMonth()->toDateString();
                    break;
                case 'last_6_months':
                    $startDate = $now->copy()->subMonths(5)->startOfMonth()->toDateString();
                    $endDate = $now->copy()->endOfMonth()->toDateString();
                    break;
                case 'this_year':
                    $startDate = $now->copy()->startOfYear()->toDateString();
                    $endDate = $now->copy()->endOfYear()->toDateString();
                    break;
                case 'all_time':
                    $startDate = '2020-01-01';
                    $endDate = $now->copy()->endOfMonth()->toDateString();
                    break;
                case 'this_month':
                default:
                    $startDate = $now->copy()->startOfMonth()->toDateString();
                    $endDate = $now->copy()->endOfMonth()->toDateString();
                    break;
            }
        }

        // Base Query Builder for the period
        $baseTxQuery = Transaction::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($accountId) {
            $baseTxQuery->where(function ($q) use ($accountId) {
                $q->where('account_id', $accountId)
                  ->orWhere('from_account_id', $accountId);
            });
        }

        // 1. Executive Summary Totals
        $totalIncome = (clone $baseTxQuery)->where('transaction_type', 'income')->sum('amount');
        $totalExpense = (clone $baseTxQuery)->where('transaction_type', 'expense')->sum('amount');
        $netSavings = $totalIncome - $totalExpense;
        $savingsRate = $totalIncome > 0 ? round(($netSavings / $totalIncome) * 100, 1) : 0;

        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $daysCount = max(1, $startCarbon->diffInDays($endCarbon) + 1);
        $dailyAverageExpense = round($totalExpense / $daysCount, 2);
        $totalTransactionsCount = (clone $baseTxQuery)->count();

        // 2. Category Spending Breakdown (Expenses)
        $expenseCategories = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.tenant_id', $tenantId)
            ->where('transactions.transaction_type', 'expense')
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->when($accountId, function ($q) use ($accountId) {
                $q->where('transactions.account_id', $accountId);
            })
            ->select(
                'categories.id',
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(transactions.id) as tx_count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($cat) use ($totalExpense) {
                $cat->total_amount = (float)$cat->total_amount;
                $cat->percentage = $totalExpense > 0 ? round(($cat->total_amount / $totalExpense) * 100, 1) : 0;
                return $cat;
            });

        // 3. Category Incomes Breakdown
        $incomeCategories = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.tenant_id', $tenantId)
            ->where('transactions.transaction_type', 'income')
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->when($accountId, function ($q) use ($accountId) {
                $q->where('transactions.account_id', $accountId);
            })
            ->select(
                'categories.id',
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(transactions.id) as tx_count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($cat) use ($totalIncome) {
                $cat->total_amount = (float)$cat->total_amount;
                $cat->percentage = $totalIncome > 0 ? round(($cat->total_amount / $totalIncome) * 100, 1) : 0;
                return $cat;
            });

        // 4. Monthly Trend (Last 6 Months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $mStart = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $mEnd = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();
            $label = Carbon::now()->subMonths($i)->format('M Y');
            $shortLabel = Carbon::now()->subMonths($i)->format('M');

            $mIncome = Transaction::where('tenant_id', $tenantId)
                ->where('transaction_type', 'income')
                ->whereBetween('date', [$mStart, $mEnd])
                ->when($accountId, function ($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                })
                ->sum('amount');

            $mExpense = Transaction::where('tenant_id', $tenantId)
                ->where('transaction_type', 'expense')
                ->whereBetween('date', [$mStart, $mEnd])
                ->when($accountId, function ($q) use ($accountId) {
                    $q->where('account_id', $accountId);
                })
                ->sum('amount');

            $monthlyTrend[] = [
                'month_key' => Carbon::now()->subMonths($i)->format('Y-m'),
                'label'     => $label,
                'short'     => $shortLabel,
                'income'    => (float)$mIncome,
                'expense'   => (float)$mExpense,
                'savings'   => (float)($mIncome - $mExpense),
            ];
        }

        // 5. Account Cash Flow Breakdown
        $accounts = Account::where('tenant_id', $tenantId)->get();
        $accountFlows = $accounts->map(function ($acc) use ($tenantId, $startDate, $endDate) {
            $inflows = Transaction::where('tenant_id', $tenantId)
                ->where('account_id', $acc->id)
                ->whereIn('transaction_type', ['income', 'transfer'])
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            $outflows = Transaction::where('tenant_id', $tenantId)
                ->where(function ($q) use ($acc) {
                    $q->where(function ($sq) use ($acc) {
                        $sq->where('account_id', $acc->id)
                           ->where('transaction_type', 'expense');
                    })->orWhere(function ($sq) use ($acc) {
                        $sq->where('from_account_id', $acc->id)
                           ->where('transaction_type', 'transfer');
                    });
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            return [
                'id'              => $acc->id,
                'name'            => $acc->name,
                'account_type'    => $acc->account_type,
                'currency'        => $acc->currency ?? 'BDT',
                'current_balance' => (float)$acc->balance,
                'inflows'         => (float)$inflows,
                'outflows'        => (float)$outflows,
                'net_flow'        => (float)($inflows - $outflows),
            ];
        });

        // 6. Top Recent Transactions in this period
        $topExpenses = Transaction::with(['category', 'account'])
            ->where('tenant_id', $tenantId)
            ->where('transaction_type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->when($accountId, function ($q) use ($accountId) {
                $q->where('account_id', $accountId);
            })
            ->orderByDesc('amount')
            ->limit(5)
            ->get();

        $data = [
            'period' => [
                'preset'     => $preset,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'days_count' => $daysCount,
            ],
            'summary' => [
                'total_income'          => (float)$totalIncome,
                'total_expense'         => (float)$totalExpense,
                'net_savings'           => (float)$netSavings,
                'savings_rate'          => $savingsRate,
                'daily_average_expense' => $dailyAverageExpense,
                'transactions_count'    => $totalTransactionsCount,
            ],
            'expense_categories' => $expenseCategories,
            'income_categories'  => $incomeCategories,
            'monthly_trend'      => $monthlyTrend,
            'account_flows'      => $accountFlows,
            'top_expenses'       => $topExpenses,
        ];

        $response = new ApiResponse();
        return $response->displayWithResponse(true, $data);
    }

    /**
     * Export transaction data for report period as JSON / CSV rows.
     */
    public function export(Request $request)
    {
        $tenantId = auth()->id() ?? 1;
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $accountId = $request->input('account_id');

        $query = Transaction::with(['category', 'account', 'fromAccount'])
            ->where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($accountId) {
            $query->where(function ($q) use ($accountId) {
                $q->where('account_id', $accountId)
                  ->orWhere('from_account_id', $accountId);
            });
        }

        $transactions = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->get();

        $rows = $transactions->map(function ($tx) {
            return [
                'ID'               => $tx->id,
                'Date'             => $tx->date,
                'Time'             => $tx->time ?? '',
                'Type'             => strtoupper($tx->transaction_type),
                'Amount (BDT)'     => $tx->amount,
                'Category'         => $tx->category ? $tx->category->name : 'N/A',
                'Account'          => $tx->account ? $tx->account->name : 'N/A',
                'From Account'     => $tx->fromAccount ? $tx->fromAccount->name : '',
                'Description'      => $tx->description ?? '',
                'Payment Method'   => $tx->payment_method ?? '',
                'Reference'        => $tx->reference_number ?? '',
            ];
        });

        $response = new ApiResponse();
        return $response->displayWithResponse(true, [
            'filename' => "financial_report_{$startDate}_to_{$endDate}.csv",
            'rows'     => $rows,
            'count'    => $rows->count(),
        ]);
    }
}

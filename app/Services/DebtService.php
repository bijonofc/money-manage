<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DebtService
{
    public function createDebt(array $data, int $userId): Debt
    {
        return DB::transaction(function () use ($data, $userId) {
            $type = $data['type'];
            $amount = (float) $data['principal_amount'];
            $accountId = ! empty($data['account_id']) ? (int) $data['account_id'] : null;

            $debtRecord = Debt::create([
                'tenant_id' => $userId,
                'type' => $type,
                'creditor_name' => $data['creditor_name'],
                'creditor_contact' => $data['creditor_contact'] ?? null,
                'principal_amount' => $amount,
                'paid_amount' => $data['paid_amount'] ?? 0.00,
                'interest_rate' => $data['interest_rate'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);

            if ($accountId) {
                $account = Account::find($accountId);
                if ($account) {
                    if ($type === 'owed_to') {
                        $account->increment('balance', $amount);

                        $category = Category::where('type', 'income')
                            ->where(function ($q) {
                                $q->where('name', 'like', '%Loan%')
                                    ->orWhere('name', 'like', '%Other Income%');
                            })
                            ->first();

                        Transaction::create([
                            'tenant_id' => $userId,
                            'user_id' => $userId,
                            'transaction_type' => 'income',
                            'amount' => $amount,
                            'account_id' => $accountId,
                            'category_id' => $category?->id,
                            'date' => $data['date'] ?? now()->toDateString(),
                            'time' => now()->format('H:i'),
                            'description' => "Loan borrowed from {$debtRecord->creditor_name}".($debtRecord->description ? " ({$debtRecord->description})" : ''),
                        ]);
                    } elseif ($type === 'owed_from') {
                        $account->decrement('balance', $amount);

                        $category = Category::where('type', 'expense')
                            ->where(function ($q) {
                                $q->where('name', 'like', '%Debt%')
                                    ->orWhere('name', 'like', '%Other Expenses%');
                            })
                            ->first();

                        Transaction::create([
                            'tenant_id' => $userId,
                            'user_id' => $userId,
                            'transaction_type' => 'expense',
                            'amount' => $amount,
                            'account_id' => $accountId,
                            'category_id' => $category?->id,
                            'date' => $data['date'] ?? now()->toDateString(),
                            'time' => now()->format('H:i'),
                            'description' => "Loan given to {$debtRecord->creditor_name}".($debtRecord->description ? " ({$debtRecord->description})" : ''),
                        ]);
                    }
                }
            }

            return $debtRecord;
        });
    }

    public function recordPayment(Debt $debt, array $data, int $userId): DebtPayment
    {
        return DB::transaction(function () use ($debt, $data, $userId) {
            $amount = (float) $data['amount'];
            $accountId = ! empty($data['account_id']) ? (int) $data['account_id'] : null;
            $paymentDate = $data['payment_date'] ?? now()->toDateString();
            $paymentTime = $data['time'] ?? $data['payment_time'] ?? now()->format('H:i');
            $note = $data['note'] ?? null;

            $transactionId = null;

            if ($accountId) {
                $account = Account::find($accountId);
                if ($account) {
                    $isOwedTo = $debt->type === 'owed_to';
                    $txType = $isOwedTo ? 'expense' : 'income';

                    if ($isOwedTo) {
                        $account->decrement('balance', $amount);
                    } else {
                        $account->increment('balance', $amount);
                    }

                    $category = Category::where('type', $txType)
                        ->where(function ($q) {
                            $q->where('name', 'like', '%Debt%')
                                ->orWhere('name', 'like', '%Loan%')
                                ->orWhere('name', 'like', '%Other%');
                        })
                        ->first();

                    $tx = Transaction::create([
                        'tenant_id' => $userId,
                        'user_id' => $userId,
                        'transaction_type' => $txType,
                        'amount' => $amount,
                        'account_id' => $accountId,
                        'category_id' => $category?->id,
                        'date' => $paymentDate,
                        'time' => $paymentTime,
                        'description' => "Debt payment for {$debt->creditor_name}".($note ? " - {$note}" : ''),
                    ]);

                    $transactionId = $tx->id;
                }
            }

            $payment = DebtPayment::create([
                'debt_id' => $debt->id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'note' => $note,
            ]);

            $newPaid = (float) $debt->paid_amount + $amount;
            $debt->paid_amount = $newPaid;
            if ($newPaid >= (float) $debt->principal_amount) {
                $debt->status = 'paid';
            }
            $debt->save();

            ActivityLogger::log(
                event: 'payment',
                des: 'act.debt_pay',
                desParam: [
                    'uname' => auth()->user()?->name ?? 'User',
                    'model' => "Debt Payment ({$debt->creditor_name}: ".number_format($amount, 2).')',
                ],
                subjectType: Debt::class,
                subjectId: $debt->id,
                userId: $userId,
                tenantId: $userId,
                properties: ['amount' => $amount, 'note' => $note, 'new_paid' => $newPaid]
            );

            return $payment;
        });
    }
}

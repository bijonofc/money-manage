<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function create(array $data, int $userId): Transaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $type = $data['transaction_type'];
            $amount = (float) $data['amount'];
            $accountId = (int) $data['account_id'];
            $fromAccountId = ! empty($data['from_account_id']) ? (int) $data['from_account_id'] : null;

            $transaction = Transaction::create([
                'tenant_id' => $userId,
                'user_id' => $userId,
                'transaction_type' => $type,
                'amount' => $amount,
                'category_id' => $data['category_id'] ?? null,
                'account_id' => $accountId,
                'from_account_id' => $fromAccountId,
                'date' => $data['date'],
                'time' => $data['time'] ?? null,
                'description' => $data['description'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'tags' => $data['tags'] ?? null,
            ]);

            $this->applyBalanceAdjustment($type, $amount, $accountId, $fromAccountId);

            ActivityLogger::logCreated($transaction, "{$type}: ".number_format($amount, 2));

            return $transaction;
        });
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // 1. Revert previous balance impact
            $this->revertBalanceAdjustment(
                $transaction->transaction_type,
                (float) $transaction->amount,
                (int) $transaction->account_id,
                $transaction->from_account_id ? (int) $transaction->from_account_id : null
            );

            // 2. Update model
            $transaction->fill($data);
            $transaction->save();

            // 3. Apply new balance impact
            $this->applyBalanceAdjustment(
                $transaction->transaction_type,
                (float) $transaction->amount,
                (int) $transaction->account_id,
                $transaction->from_account_id ? (int) $transaction->from_account_id : null
            );

            ActivityLogger::logUpdated($transaction, "{$transaction->transaction_type}: ".number_format((float) $transaction->amount, 2));

            return $transaction;
        });
    }

    public function delete(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $this->revertBalanceAdjustment(
                $transaction->transaction_type,
                (float) $transaction->amount,
                (int) $transaction->account_id,
                $transaction->from_account_id ? (int) $transaction->from_account_id : null
            );

            ActivityLogger::logDeleted($transaction, "{$transaction->transaction_type}: ".number_format((float) $transaction->amount, 2));

            return (bool) $transaction->delete();
        });
    }

    private function applyBalanceAdjustment(string $type, float $amount, int $accountId, ?int $fromAccountId): void
    {
        $account = Account::find($accountId);
        if ($account) {
            if ($type === 'income') {
                $account->increment('balance', $amount);
            } elseif ($type === 'expense') {
                $account->decrement('balance', $amount);
            } elseif ($type === 'transfer' && $fromAccountId) {
                $account->increment('balance', $amount);
                $fromAccount = Account::find($fromAccountId);
                if ($fromAccount) {
                    $fromAccount->decrement('balance', $amount);
                }
            }
        }
    }

    private function revertBalanceAdjustment(string $type, float $amount, int $accountId, ?int $fromAccountId): void
    {
        $account = Account::find($accountId);
        if ($account) {
            if ($type === 'income') {
                $account->decrement('balance', $amount);
            } elseif ($type === 'expense') {
                $account->increment('balance', $amount);
            } elseif ($type === 'transfer' && $fromAccountId) {
                $account->decrement('balance', $amount);
                $fromAccount = Account::find($fromAccountId);
                if ($fromAccount) {
                    $fromAccount->increment('balance', $amount);
                }
            }
        }
    }
}

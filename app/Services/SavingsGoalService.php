<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class SavingsGoalService
{
    public function recordContribution(SavingsGoal $goal, array $data, int $userId): SavingsContribution
    {
        return DB::transaction(function () use ($goal, $data, $userId) {
            $amount = (float) $data['amount'];
            $accountId = ! empty($data['account_id']) ? (int) $data['account_id'] : null;
            $note = $data['note'] ?? null;

            $transactionId = null;

            if ($accountId) {
                $account = Account::find($accountId);
                if ($account) {
                    $account->decrement('balance', $amount);

                    $category = Category::where('type', 'expense')
                        ->where(function ($q) {
                            $q->where('name', 'like', '%Saving%')
                                ->orWhere('name', 'like', '%Investment%')
                                ->orWhere('name', 'like', '%Other%');
                        })
                        ->first();

                    $tx = Transaction::create([
                        'tenant_id' => $userId,
                        'user_id' => $userId,
                        'transaction_type' => 'expense',
                        'amount' => $amount,
                        'account_id' => $accountId,
                        'category_id' => $category?->id,
                        'date' => now()->toDateString(),
                        'time' => now()->format('H:i'),
                        'description' => "Savings deposit for goal: {$goal->name}".($note ? " - {$note}" : ''),
                    ]);

                    $transactionId = $tx->id;
                }
            }

            $contribution = SavingsContribution::create([
                'goal_id' => $goal->id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'note' => $note,
            ]);

            $goal->increment('current_amount', $amount);

            ActivityLogger::log(
                event: 'updated',
                des: 'act.up',
                desParam: [
                    'uname' => auth()->user()?->name ?? 'User',
                    'model' => "Savings Goal ({$goal->name}: deposited ".number_format($amount, 2).')',
                ],
                subjectType: SavingsGoal::class,
                subjectId: $goal->id,
                userId: $userId,
                tenantId: $userId,
                properties: ['amount' => $amount, 'note' => $note, 'new_total' => $goal->current_amount]
            );

            return $contribution;
        });
    }
}

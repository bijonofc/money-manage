<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_transactions_via_post(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/transactions/list', []);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rowdata',
            'records',
            'total',
            'page',
            'limit',
        ]);
    }

    public function test_transaction_validation_fails_with_422(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/transactions', []);

        $response->assertStatus(422);
        $response->assertJsonPath('status', false);
    }

    public function test_can_create_income_transaction_and_adjust_balance(): void
    {
        $user = User::first();
        $account = Account::create([
            'tenant_id' => $user->id,
            'name' => 'Main Bank',
            'account_type' => 'bank',
            'balance' => 1000.00,
        ]);

        $category = Category::create([
            'tenant_id' => $user->id,
            'name' => 'Salary',
            'type' => 'income',
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/transactions', [
            'transaction_type' => 'income',
            'amount' => 500.00,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'date' => now()->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.amount', 500);

        $this->assertEquals(1500.00, (float) $account->fresh()->balance);
    }

    public function test_can_create_expense_transaction_and_decrement_balance(): void
    {
        $user = User::first();
        $account = Account::create([
            'tenant_id' => $user->id,
            'name' => 'Cash Wallet',
            'account_type' => 'cash',
            'balance' => 1000.00,
        ]);

        $category = Category::create([
            'tenant_id' => $user->id,
            'name' => 'Groceries',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/transactions', [
            'transaction_type' => 'expense',
            'amount' => 200.00,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'date' => now()->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);

        $this->assertEquals(800.00, (float) $account->fresh()->balance);
    }
}

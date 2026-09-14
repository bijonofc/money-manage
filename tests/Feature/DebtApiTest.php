<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_debts_via_post(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/debts/list', []);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rowdata',
            'records',
            'total',
        ]);
    }

    public function test_debt_validation_fails_on_missing_fields(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/debts', [
            'type' => 'owed_to',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', false);
    }

    public function test_can_create_debt_with_linked_account(): void
    {
        $user = User::first();
        $account = Account::create([
            'tenant_id' => $user->id,
            'name' => 'Cash in Hand',
            'account_type' => 'cash',
            'balance' => 500.00,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/debts', [
            'type' => 'owed_to', // Loan taken: cash increases
            'creditor_name' => 'John Doe',
            'principal_amount' => 1000.00,
            'account_id' => $account->id,
            'due_date' => now()->addMonths(1)->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.creditor_name', 'John Doe');
        $response->assertJsonPath('data.principal_amount', 1000);

        $this->assertEquals(1500.00, (float) $account->fresh()->balance);
    }

    public function test_can_record_debt_payment_and_settle(): void
    {
        $user = User::first();
        $account = Account::create([
            'tenant_id' => $user->id,
            'name' => 'Bank Account',
            'account_type' => 'bank',
            'balance' => 5000.00,
        ]);

        $debt = Debt::create([
            'tenant_id' => $user->id,
            'type' => 'owed_to',
            'creditor_name' => 'Supplier Co',
            'principal_amount' => 1000.00,
            'paid_amount' => 0.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->postJson("/api/v1/debts/{$debt->id}/pay", [
            'amount' => 1000.00,
            'account_id' => $account->id,
            'note' => 'Full payment',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);

        $this->assertEquals('paid', $debt->fresh()->status);
        $this->assertEquals(1000.00, (float) $debt->fresh()->paid_amount);
        $this->assertEquals(4000.00, (float) $account->fresh()->balance);
    }
}

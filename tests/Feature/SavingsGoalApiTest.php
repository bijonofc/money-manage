<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Account;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsGoalApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_savings_goals_via_post(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/savings-goals/list', []);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rowdata',
            'records',
            'total',
        ]);
    }

    public function test_can_create_savings_goal(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/savings-goals', [
            'name' => 'Emergency Fund',
            'target_amount' => 100000.00,
            'current_amount' => 10000.00,
            'deadline' => now()->addYear()->toDateString(),
            'color' => '#3b82f6',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.name', 'Emergency Fund');
        $response->assertJsonPath('data.target_amount', 100000);
    }

    public function test_savings_goal_validation_fails_on_missing_name(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/savings-goals', [
            'target_amount' => 50000.00,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', false);
    }

    public function test_can_record_contribution_and_decrement_account(): void
    {
        $user = User::first();
        $account = Account::create([
            'tenant_id' => $user->id,
            'name' => 'Main Account',
            'account_type' => 'bank',
            'balance' => 20000.00,
        ]);

        $goal = SavingsGoal::create([
            'tenant_id' => $user->id,
            'name' => 'Vacation Trip',
            'target_amount' => 50000.00,
            'current_amount' => 5000.00,
        ]);

        $response = $this->actingAs($user)->postJson("/api/v1/savings-goals/{$goal->id}/contribute", [
            'amount' => 3000.00,
            'account_id' => $account->id,
            'note' => 'Monthly deposit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);

        $this->assertEquals(8000.00, (float) $goal->fresh()->current_amount);
        $this->assertEquals(17000.00, (float) $account->fresh()->balance);
    }

    public function test_can_update_savings_goal(): void
    {
        $user = User::first();
        $goal = SavingsGoal::create([
            'tenant_id' => $user->id,
            'name' => 'Old Goal',
            'target_amount' => 10000.00,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/v1/savings-goals/{$goal->id}", [
            'name' => 'New Goal Name',
            'target_amount' => 15000.00,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.name', 'New Goal Name');
    }

    public function test_can_delete_savings_goal(): void
    {
        $user = User::first();
        $goal = SavingsGoal::create([
            'tenant_id' => $user->id,
            'name' => 'To Delete',
            'target_amount' => 5000.00,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/savings-goals/{$goal->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $this->assertNull(SavingsGoal::find($goal->id));
    }
}

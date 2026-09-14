<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_budgets_via_post(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/budgets/list', []);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rowdata',
            'records',
            'total',
        ]);
    }

    public function test_can_create_budget(): void
    {
        $user = User::first();
        $category = Category::create([
            'tenant_id' => $user->id,
            'name' => 'Food & Dining',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/budgets', [
            'category_id' => $category->id,
            'amount' => 5000.00,
            'period' => 'monthly',
            'start_date' => now()->startOfMonth()->toDateString(),
            'alert_threshold' => 75,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.amount', 5000);
        $response->assertJsonPath('data.period', 'monthly');
    }

    public function test_budget_validation_fails_on_invalid_amount(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/budgets', [
            'amount' => -100,
            'period' => 'monthly',
            'start_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', false);
    }

    public function test_can_update_budget(): void
    {
        $user = User::first();
        $budget = Budget::create([
            'tenant_id' => $user->id,
            'amount' => 3000.00,
            'period' => 'monthly',
            'start_date' => now()->startOfMonth()->toDateString(),
        ]);

        $response = $this->actingAs($user)->patchJson("/api/v1/budgets/{$budget->id}", [
            'amount' => 4500.00,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.amount', 4500);
    }

    public function test_can_delete_budget(): void
    {
        $user = User::first();
        $budget = Budget::create([
            'tenant_id' => $user->id,
            'amount' => 2000.00,
            'period' => 'monthly',
            'start_date' => now()->startOfMonth()->toDateString(),
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/budgets/{$budget->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $this->assertNull(Budget::find($budget->id));
    }
}

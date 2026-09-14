<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_accounts_via_post(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/accounts/list', []);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rowdata',
            'records',
            'total',
            'page',
            'limit',
        ]);
    }

    public function test_can_create_account(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/accounts', [
            'name' => 'City Bank Savings',
            'account_type' => 'bank',
            'balance' => 25000.00,
            'currency' => 'BDT',
            'account_number' => '123456789',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.name', 'City Bank Savings');
        $response->assertJsonPath('data.balance', 25000);
    }

    public function test_account_validation_fails_on_missing_name(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/accounts', [
            'account_type' => 'bank',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', false);
    }

    public function test_can_update_account(): void
    {
        $user = User::first();
        $account = Account::create([
            'tenant_id' => $user->id,
            'name' => 'Old Name',
            'account_type' => 'cash',
            'balance' => 500.00,
        ]);

        $response = $this->actingAs($user)->patchJson("/api/v1/accounts/{$account->id}", [
            'name' => 'New Name',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.name', 'New Name');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_settings_via_post(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/settings/list', []);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'rowdata',
                'recordsTotal',
            ],
        ]);
    }

    public function test_can_list_settings_via_get(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->getJson('/api/v1/settings/list');

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
    }

    public function test_can_save_settings(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/settings', [
            'group_slug' => 'general_settings',
            'settings' => [
                'app_name' => 'Money Manage Custom',
                'currency_symbol' => '€',
                'currency_code' => 'EUR',
                'budget_threshold' => 75,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);

        // Verify with helper
        $this->assertEquals('Money Manage Custom', app_setting('app_name'));
        $this->assertEquals('€', app_setting('currency_symbol'));
    }
}

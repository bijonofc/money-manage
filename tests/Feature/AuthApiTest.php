<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }
    public function test_user_can_login_via_api(): void
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email'    => 'test@example.com',
            'password' => '12345',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.user_data.email', 'test@example.com');
    }

    public function test_invalid_login_returns_401(): void
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('status', false);
    }

    public function test_initial_data_returns_expected_structure(): void
    {
        $response = $this->getJson('/api/v1/initial-data');

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'userList',
                'roleList',
            ],
        ]);
    }
}

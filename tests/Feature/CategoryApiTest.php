<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_list_categories_and_auto_seed(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/categories/list', []);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rowdata',
            'records',
            'total',
        ]);
        $this->assertGreaterThan(0, $response->json('records'));
    }

    public function test_can_create_category(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'name' => 'Unique Custom Income',
            'type' => 'income',
            'icon' => 'wallet',
            'color' => '#10b981',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.name', 'Unique Custom Income');
    }

    public function test_duplicate_category_name_fails_validation(): void
    {
        $user = User::first();
        Category::create([
            'tenant_id' => $user->id,
            'name' => 'Existing Category',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'name' => 'Existing Category',
            'type' => 'expense',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', false);
    }

    public function test_can_update_category(): void
    {
        $user = User::first();
        $category = Category::create([
            'tenant_id' => $user->id,
            'name' => 'To Update',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->patchJson("/api/v1/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('data.name', 'Updated Category');
    }

    public function test_can_delete_category(): void
    {
        $user = User::first();
        $category = Category::create([
            'tenant_id' => $user->id,
            'name' => 'To Delete',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $this->assertNull(Category::find($category->id));
    }
}

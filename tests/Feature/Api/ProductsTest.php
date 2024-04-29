<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();

        $this->admin = $this->createUser(isAdmin: true);
    }

    public function test_api_returns_products_list(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson('/api/products');

        $response->assertJson([$product->toArray()]);
    }

    public function test_api_product_store_successful(): void
    {
        $product = [
            'name' => 'Product 1',
            'price' => 123
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(201);
        $response->assertJson($product);
    }

    public function test_api_product_invalid_store_returns_error(): void
    {
        $product = [
            'name' => '',
            'price' => 123
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(422);
    }

    private function createUser(bool $isAdmin = false): User
    {
        return User::factory()->create([
            'is_admin' => $isAdmin,
        ]);
    }
}

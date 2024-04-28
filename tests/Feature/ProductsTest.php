<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();

        $this->admin = $this->createUser(isAdmin: true);
    }

    public function test_products_homepage_contains_empty_table(): void
    {
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);

        $response->assertSee('No products found');
    }

    public function test_products_homepage_contains_non_empty_table(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $products) use ($product) {
            return $products->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record(): void
    {
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $products) use ($lastProduct) {
            return $products->doesntContain($lastProduct);
        });
    }

    public function test_admin_can_see_products_create_button(): void
    {
        $response = $this->actingAs($this->admin)->get('/products');

        $response->assertStatus(200);

        $response->assertSee('Create Product');
    }

    public function test_non_admin_cannot_see_products_create_button(): void
    {
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);

        $response->assertDontSee('Create Product');
    }

    public function test_admin_can_access_product_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_product_create_page(): void
    {
        $response = $this->actingAs($this->user)->get('/products/create');

        $response->assertStatus(403);
    }

    public function test_create_product_successful()
    {
        $product = [
            'name' => 'Product 123',
            'price' => 1234
        ];

        $response = $this->actingAs($this->admin)->post('/products', $product);

        $response->assertStatus(302);

        $response->assertRedirect('products');

        $this->assertDatabaseHas('products', $product);

        $lastProduct = Product::latest()->first();

        $this->assertEquals($product['name'], $lastProduct->name);

        $this->assertEquals($product['price'], $lastProduct->price);
    }

    public function createUser(bool $isAdmin = false ): User
    {
        return User::factory([
            'is_admin' => $isAdmin
        ])->create();
    }
}

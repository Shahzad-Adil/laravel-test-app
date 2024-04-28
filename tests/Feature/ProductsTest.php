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

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
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

    public function createUser(): User
    {
        return User::factory()->create();
    }
}

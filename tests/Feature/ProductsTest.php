<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_homepage_contains_empty_table(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);

        $response->assertSee('No products found');
    }

    public function test_products_homepage_contains_non_empty_table(): void
    {
        $product = Product::factory()->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $products) use ($product) {
            return $products->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record(): void
    {
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $products) use ($lastProduct) {
            return $products->doesntContain($lastProduct);
        });
    }
}

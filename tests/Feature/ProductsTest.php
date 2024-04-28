<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_homepage_contains_empty_table(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);

        $response->assertSee('No products found');
    }

    public function test_products_homepage_contains_non_empty_table(): void
    {
        $product = Product::factory()->create();

        $response = $this->get('/products');

        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $products) use ($product) {
            return $products->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record(): void
    {
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $response = $this->get('/products');

        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $products) use ($lastProduct) {
            return $products->doesntContain($lastProduct);
        });
    }
}

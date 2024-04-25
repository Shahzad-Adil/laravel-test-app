<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

        $response->assertViewHas('products', function ($products) use ($product) {
            return $products->contains($product);
        });
    }
}

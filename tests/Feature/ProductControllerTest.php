<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $exchangeRateServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exchangeRateServiceMock = $this->createMock(ExchangeRateService::class);
        $this->exchangeRateServiceMock->method('get')->willReturn(1.12);
        $this->app->instance(ExchangeRateService::class, $this->exchangeRateServiceMock);
    }

    public function test_it_displays_product_list()
    {

        Product::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('products.list');
        $response->assertViewHas('products');
        $response->assertViewHas('exchangeRate', 1.12);
    }

    public function test_it_shows_a_single_product()
    {

        $product = Product::factory()->create();

        $response = $this->get(route('products.show', ['product_id' => $product->id]));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product', $product);
        $response->assertViewHas('exchangeRate', 1.12);
    }

    public function test_it_returns_404_for_non_existent_product()
    {
        $response = $this->get(route('products.show', ['product_id' => 999]));
        $response->assertStatus(404);
    }
}

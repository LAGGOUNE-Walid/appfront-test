<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UpdateProductCommandTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_update_product_command_with_valid_inputs()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 100.00,
            'description' => 'Test description',
        ]);

        $newName = 'Updated Product';
        $newPrice = 150.00;
        $newDescription = 'Updated description';

        $exitCode = Artisan::call('product:update', [
            'id' => $product->id,
            '--name' => $newName,
            '--price' => $newPrice,
            '--description' => $newDescription,
        ]);

        $this->assertEquals(0, $exitCode);

        $updatedProduct = Product::find($product->id);
        $this->assertEquals($newName, $updatedProduct->name);
        $this->assertEquals($newPrice, $updatedProduct->price);
        $this->assertEquals($newDescription, $updatedProduct->description);

        $this->assertStringContainsString('Product updated successfully.', Artisan::output());
    }

    public function test_update_product_command_with_invalid_inputs()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 100.00,
            'description' => 'Test description',
        ]);

        $exitCode = Artisan::call('product:update', [
            'id' => $product->id,
            '--price' => -50, 
        ]);

        $this->assertEquals(1, $exitCode);

        $this->assertStringContainsString('Validation failed: The price field must be at least 0', Artisan::output());
    }

    public function test_update_product_command_with_no_changes()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 100.00,
            'description' => 'Test description',
        ]);

        $exitCode = Artisan::call('product:update', [
            'id' => $product->id,
            '--name' => null,
            '--price' => null,
            '--description' => null,
        ]);

        $this->assertEquals(0, $exitCode);

        $updatedProduct = Product::find($product->id);
        $this->assertEquals('Test Product', $updatedProduct->name);
        $this->assertEquals(100.00, $updatedProduct->price);
        $this->assertEquals('Test description', $updatedProduct->description);

        $this->assertStringContainsString('No changes provided. Product remains unchanged.', Artisan::output());
    }

    public function test_update_product_command_with_missing_product()
    {
        $exitCode = Artisan::call('product:update', [
            'id' => 999,  
            '--name' => 'Updated Product',
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('Product not found', Artisan::output());
    }
}

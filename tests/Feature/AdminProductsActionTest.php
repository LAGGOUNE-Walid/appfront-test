<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductsActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_login_page_loads_successfully()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_admin_can_login_with_valid_credentials()
    {
        $response = $this->post(route('login'), [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);
        
        $response->assertRedirect(route('admin.products'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_cannot_login_with_invalid_credentials()
    {
        $response = $this->post(route('login'), [
            'email' => $this->admin->email,
            'password' => 'wrongpassword',
        ]);
        
        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_admin_can_logout()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('logout'));
        
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_products_page_loads_correctly()
    {
        $this->actingAs($this->admin);
        
        Product::factory()->count(3)->create();
        
        $response = $this->get(route('admin.products'));
        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_admin_can_add_product()
    {
        $this->actingAs($this->admin);
        Storage::fake('public');

        $response = $this->post(route('admin.add.product.submit'), [
            'name' => 'Test Product',
            'price' => 199.99,
            'description' => 'Test Description',
            'image' => UploadedFile::fake()->image('test.jpg'),
        ]);
        
        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_admin_can_edit_product()
    {
        $this->actingAs($this->admin);
        $product = Product::factory()->create();

        $response = $this->get(route('admin.edit.product', $product->id));
        $response->assertStatus(200);
        $response->assertViewHas('product', $product);
    }

    public function test_admin_can_update_product()
    {
        $this->actingAs($this->admin);
        $product = Product::factory()->create();

        $response = $this->patch(route('admin.update.product', $product->id), [
            'name' => 'Updated Product',
            'price' => 299.99,
            'description' => 'Updated Description',
        ]);
        
        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_admin_can_delete_product()
    {
        $this->actingAs($this->admin);
        $product = Product::factory()->create();

        $response = $this->delete(route('admin.delete.product', $product->id));
        
        $response->assertRedirect(route('admin.products'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}

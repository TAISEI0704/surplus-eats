<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_product_with_valid_data(): void
    {
        Storage::fake('local');

        $seller = Seller::factory()->create();
        Auth::shouldUse('sellers');
        $this->actingAs($seller, 'sellers');

        $image = $this->fakeImage('product.png');

        $response = $this->post(route('post.store'), [
            'name' => 'Test Product',
            'price' => 500,
            'content' => 'Tasty surplus food.',
            'quantity' => '5',
            'category' => 'Bakery',
            'available_time' => '10:00-12:00',
            'image' => $image,
        ]);

        $response->assertRedirect(route('seller.dashboard'));

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'seller_id' => $seller->id,
        ]);

        Storage::disk('local')->assertExists('public/images/product.png');
    }

    public function test_product_store_requires_mandatory_fields(): void
    {
        $seller = Seller::factory()->create();
        Auth::shouldUse('sellers');
        $this->actingAs($seller, 'sellers');

        $response = $this->from(route('post.create'))
            ->post(route('post.store'), [
                'price' => 500,
                'content' => 'Tasty surplus food.',
                'quantity' => '5',
                'category' => 'Bakery',
            ]);

        $response->assertRedirect(route('post.create'));
        $response->assertSessionHasErrors(['name', 'image']);
    }

    public function test_seller_can_update_product_details(): void
    {
        Storage::fake('local');

        $seller = Seller::factory()->create();
        Auth::shouldUse('sellers');
        $this->actingAs($seller, 'sellers');

        $product = Product::create([
            'name' => 'Original Name',
            'price' => 100,
            'content' => 'Original content',
            'quantity' => '3',
            'category' => 'Bakery',
            'image' => 'original.jpg',
            'available_time' => '08:00-10:00',
            'seller_id' => $seller->id,
        ]);

        $newImage = $this->fakeImage('updated.png');

        $response = $this->patch(route('post.update', $product->id), [
            'name' => 'Updated Name',
            'price' => 250,
            'content' => 'Updated content',
            'quantity' => '7',
            'category' => 'Deli',
            'available_time' => '12:00-14:00',
            'image' => $newImage,
        ]);

        $response->assertRedirect(route('seller.dashboard'));

        $product->refresh();

        $this->assertSame('Updated Name', $product->name);
        $this->assertSame(250, (int) $product->price);
        $this->assertSame('Updated content', $product->content);
        $this->assertSame('7', (string) $product->quantity);
        $this->assertSame('Deli', $product->category);
        $this->assertSame('12:00-14:00', $product->available_time);
        $this->assertSame('updated.png', $product->image);

        Storage::disk('local')->assertExists('public/images/updated.png');
    }

    private function fakeImage(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg=='
        ));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}

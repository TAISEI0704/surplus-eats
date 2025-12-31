<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Seller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 販売者登録 Feature Test
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 販売者登録成功のテスト
     */
    public function test_seller_can_register(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/auth/seller/register', [
            'name' => 'Test Shop',
            'email' => 'shop@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '090-1234-5678',
            'address' => 'Tokyo',
            'content' => 'Test shop description',
            'image' => UploadedFile::fake()->create('shop.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'seller' => ['id', 'name', 'email', 'phone', 'address', 'content', 'image'],
                'token',
            ]);

        $this->assertDatabaseHas('sellers', [
            'email' => 'shop@example.com',
        ]);
    }

    /**
     * バリデーションエラーのテスト
     */
    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/auth/seller/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'phone', 'address', 'content']);
    }
}

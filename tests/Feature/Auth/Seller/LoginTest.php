<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Seller;

use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 販売者ログイン Feature Test
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン成功のテスト
     */
    public function test_seller_can_login(): void
    {
        $seller = Seller::factory()->create([
            'email' => 'shop@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/seller/login', [
            'email' => 'shop@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'seller' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    /**
     * 間違ったパスワードでログイン失敗のテスト
     */
    public function test_login_fails_with_wrong_password(): void
    {
        Seller::factory()->create([
            'email' => 'shop@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/seller/login', [
            'email' => 'shop@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }
}

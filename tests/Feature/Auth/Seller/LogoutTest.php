<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Seller;

use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * 販売者ログアウト Feature Test
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログアウト成功のテスト
     */
    public function test_seller_can_logout(): void
    {
        $seller = Seller::factory()->create();
        $token = $seller->createToken('test-device')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/seller/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'ログアウトしました。',
            ]);

        $this->assertCount(0, $seller->fresh()->tokens);
    }

    /**
     * 未認証でログアウト失敗のテスト
     */
    public function test_logout_fails_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/auth/seller/logout');

        $response->assertStatus(401);
    }
}

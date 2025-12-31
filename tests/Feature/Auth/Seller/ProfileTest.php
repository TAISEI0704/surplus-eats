<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Seller;

use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * 販売者プロフィール取得 Feature Test
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィール取得成功のテスト
     */
    public function test_seller_can_get_profile(): void
    {
        $seller = Seller::factory()->create([
            'name' => 'Test Shop',
            'email' => 'shop@example.com',
        ]);

        Sanctum::actingAs($seller, [], 'sellers');

        $response = $this->getJson('/api/auth/seller/profile');

        $response->assertStatus(200)
            ->assertJson([
                'seller' => [
                    'id' => $seller->id,
                    'name' => 'Test Shop',
                    'email' => 'shop@example.com',
                ],
            ]);
    }

    /**
     * 未認証でプロフィール取得失敗のテスト
     */
    public function test_get_profile_fails_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/auth/seller/profile');

        $response->assertStatus(401);
    }
}

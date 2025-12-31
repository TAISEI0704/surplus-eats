<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Seller;

use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * 販売者プロフィール更新 Feature Test
 */
class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィール更新成功のテスト
     */
    public function test_seller_can_update_profile(): void
    {
        $seller = Seller::factory()->create([
            'name' => 'Old Shop',
            'email' => 'old@example.com',
        ]);

        Sanctum::actingAs($seller, [], 'sellers');

        $response = $this->patchJson('/api/auth/seller/profile', [
            'name' => 'New Shop',
            'email' => 'new@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'seller' => [
                    'name' => 'New Shop',
                    'email' => 'new@example.com',
                ],
                'message' => 'プロフィールを更新しました。',
            ]);

        $this->assertDatabaseHas('sellers', [
            'id' => $seller->id,
            'name' => 'New Shop',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * 重複メールアドレスで更新失敗のテスト
     */
    public function test_update_profile_fails_with_duplicate_email(): void
    {
        $existingSeller = Seller::factory()->create(['email' => 'existing@example.com']);
        $seller = Seller::factory()->create(['email' => 'shop@example.com']);

        Sanctum::actingAs($seller, [], 'sellers');

        $response = $this->patchJson('/api/auth/seller/profile', [
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}

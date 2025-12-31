<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Seller;

use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * 販売者パスワード変更 Feature Test
 */
class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * パスワード変更成功のテスト
     */
    public function test_seller_can_update_password(): void
    {
        $seller = Seller::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        Sanctum::actingAs($seller, [], 'sellers');

        $response = $this->patchJson('/api/auth/seller/password', [
            'current_password' => 'oldpassword123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'パスワードを変更しました。',
            ]);
    }

    /**
     * 間違った現在のパスワードで変更失敗のテスト
     */
    public function test_update_password_fails_with_wrong_current_password(): void
    {
        $seller = Seller::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        Sanctum::actingAs($seller, [], 'sellers');

        $response = $this->patchJson('/api/auth/seller/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(401);
    }
}

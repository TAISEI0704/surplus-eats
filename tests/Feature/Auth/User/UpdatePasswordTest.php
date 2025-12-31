<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * ユーザーパスワード変更 Feature Test
 */
class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * パスワード変更成功のテスト
     */
    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/auth/user/password', [
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
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/auth/user/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(401);
    }
}

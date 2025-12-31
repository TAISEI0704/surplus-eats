<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * ユーザーログアウト Feature Test
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログアウト成功のテスト
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/user/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'ログアウトしました。',
            ]);
    }

    /**
     * 未認証でログアウト失敗のテスト
     */
    public function test_logout_fails_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/auth/user/logout');

        $response->assertStatus(401);
    }
}

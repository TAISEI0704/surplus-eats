<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * ユーザープロフィール取得 Feature Test
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィール取得成功のテスト
     */
    public function test_user_can_get_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/user/profile');

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);
    }

    /**
     * 未認証でプロフィール取得失敗のテスト
     */
    public function test_get_profile_fails_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/auth/user/profile');

        $response->assertStatus(401);
    }
}

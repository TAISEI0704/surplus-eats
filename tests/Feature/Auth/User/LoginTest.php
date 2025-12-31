<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ユーザーログイン Feature Test
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン成功のテスト
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/user/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    /**
     * 間違ったパスワードでログイン失敗のテスト
     */
    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/user/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    /**
     * 存在しないユーザーでログイン失敗のテスト
     */
    public function test_login_fails_with_non_existent_user(): void
    {
        $response = $this->postJson('/api/auth/user/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ユーザー登録 Feature Test
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー登録成功のテスト
     */
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/user/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * バリデーションエラーのテスト
     */
    public function test_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/auth/user/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * 重複メールアドレスのテスト
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        // 既存ユーザー作成
        $this->postJson('/api/auth/user/register', [
            'name' => 'Existing User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 同じメールアドレスで登録試行
        $response = $this->postJson('/api/auth/user/register', [
            'name' => 'New User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * ユーザープロフィール更新 Feature Test
 */
class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィール更新成功のテスト
     */
    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/auth/user/profile', [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'name' => 'New Name',
                    'email' => 'new@example.com',
                ],
                'message' => 'プロフィールを更新しました。',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * 重複メールアドレスで更新失敗のテスト
     */
    public function test_update_profile_fails_with_duplicate_email(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $user = User::factory()->create(['email' => 'user@example.com']);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/auth/user/profile', [
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}

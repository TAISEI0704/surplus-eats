<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Services\Auth\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TokenService Unit Test
 */
class TokenServiceTest extends TestCase
{
    use RefreshDatabase;

    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TokenService();
    }

    /**
     * トークン生成のテスト
     */
    public function test_create_token(): void
    {
        $user = User::factory()->create();

        $token = $this->service->createToken($user, 'test-device');

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertCount(1, $user->tokens);
    }

    /**
     * 現在のトークン削除のテスト
     */
    public function test_delete_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-device');

        // withAccessToken を使用して currentAccessToken を設定
        $user->withAccessToken($token->accessToken);

        $this->service->deleteCurrentToken($user);

        $this->assertCount(0, $user->fresh()->tokens);
    }

    /**
     * 全トークン削除のテスト
     */
    public function test_delete_all_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('device-1');
        $user->createToken('device-2');
        $user->createToken('device-3');

        $this->assertCount(3, $user->tokens);

        $this->service->deleteAllTokens($user);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}

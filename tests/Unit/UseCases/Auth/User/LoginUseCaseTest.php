<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\Auth\User;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Queries\Auth\UserQuery;
use App\Services\Auth\PasswordHashService;
use App\Services\Auth\TokenService;
use App\UseCases\Auth\User\LoginUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * LoginUseCase Unit Test
 */
class LoginUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private LoginUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new LoginUseCase(
            new UserQuery(),
            new PasswordHashService(),
            new TokenService()
        );
    }

    /**
     * ログイン成功のテスト
     */
    public function test_login_success(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'test-device',
        ];

        $result = ($this->useCase)($data);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals($user->id, $result['user']->id);
        $this->assertIsString($result['token']);
    }

    /**
     * ログイン失敗のテスト（間違ったパスワード）
     */
    public function test_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $this->expectException(InvalidCredentialsException::class);

        ($this->useCase)($data);
    }

    /**
     * ログイン失敗のテスト（存在しないユーザー）
     */
    public function test_login_with_non_existent_user(): void
    {
        $data = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $this->expectException(InvalidCredentialsException::class);

        ($this->useCase)($data);
    }
}

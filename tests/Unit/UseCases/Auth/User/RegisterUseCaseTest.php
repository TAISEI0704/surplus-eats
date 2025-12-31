<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\Auth\User;

use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\PasswordHashService;
use App\Services\Auth\TokenService;
use App\UseCases\Auth\User\RegisterUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RegisterUseCase Unit Test
 */
class RegisterUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private RegisterUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new RegisterUseCase(
            new UserRepository(new PasswordHashService()),
            new TokenService()
        );
    }

    /**
     * ユーザー登録のテスト
     */
    public function test_register(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'test-device',
        ];

        $result = ($this->useCase)($data);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertIsString($result['token']);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}

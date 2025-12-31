<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\Auth\User;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\PasswordHashService;
use App\UseCases\Auth\User\UpdatePasswordUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UpdatePasswordUseCase Unit Test
 */
class UpdatePasswordUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private UpdatePasswordUseCase $useCase;
    private PasswordHashService $passwordHashService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->passwordHashService = new PasswordHashService();
        $this->useCase = new UpdatePasswordUseCase(
            new UserRepository($this->passwordHashService),
            $this->passwordHashService
        );
    }

    /**
     * パスワード変更成功のテスト
     */
    public function test_update_password_success(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        $data = [
            'current_password' => 'oldpassword123',
            'new_password' => 'newpassword123',
        ];

        $updated = ($this->useCase)($user, $data);

        $this->assertTrue(
            $this->passwordHashService->check('newpassword123', $updated->password)
        );
    }

    /**
     * パスワード変更失敗のテスト（間違った現在のパスワード）
     */
    public function test_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        $data = [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
        ];

        $this->expectException(InvalidCredentialsException::class);

        ($this->useCase)($user, $data);
    }
}

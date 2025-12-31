<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\Auth\User;

use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\PasswordHashService;
use App\UseCases\Auth\User\UpdateProfileUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UpdateProfileUseCase Unit Test
 */
class UpdateProfileUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private UpdateProfileUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new UpdateProfileUseCase(
            new UserRepository(new PasswordHashService())
        );
    }

    /**
     * プロフィール更新のテスト
     */
    public function test_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $data = [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ];

        $updated = ($this->useCase)($user, $data);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals('new@example.com', $updated->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories\Auth;

use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\PasswordHashService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * UserRepository Unit Test
 */
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new PasswordHashService());
    }

    /**
     * ユーザー作成のテスト
     */
    public function test_create(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->repository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNotEquals('password123', $user->password);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * プロフィール更新のテスト
     */
    public function test_update_profile(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $updated = $this->repository->updateProfile($user, [
            'name' => 'New Name',
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    /**
     * パスワード更新のテスト
     */
    public function test_update_password(): void
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $updated = $this->repository->updatePassword($user, 'newpassword123');

        $this->assertNotEquals($oldPassword, $updated->password);
    }
}

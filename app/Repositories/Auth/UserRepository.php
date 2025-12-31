<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Models\User;
use App\Services\Auth\PasswordHashService;

/**
 * User書き込み専用Repository
 * INSERT/UPDATE/DELETEのみ
 */
final class UserRepository
{
    public function __construct(
        private readonly PasswordHashService $passwordHashService
    ) {}

    /**
     * ユーザーを作成
     *
     * @param array{name: string, email: string, password: string} $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->passwordHashService->hash($data['password']),
        ]);
    }

    /**
     * プロフィールを更新
     *
     * @param User $user
     * @param array{name?: string, email?: string} $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    /**
     * パスワードを更新
     *
     * @param User $user
     * @param string $newPassword
     * @return User
     */
    public function updatePassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => $this->passwordHashService->hash($newPassword),
        ]);

        return $user->fresh();
    }
}

<?php

declare(strict_types=1);

namespace App\UseCases\Auth\User;

use App\Models\User;
use App\Repositories\Auth\UserRepository;

/**
 * ユーザープロフィール更新UseCase
 */
final class UpdateProfileUseCase
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * プロフィール更新処理を実行
     *
     * @param User $user
     * @param array{name?: string, email?: string} $data
     * @return User
     */
    public function __invoke(User $user, array $data): User
    {
        return $this->userRepository->updateProfile($user, $data);
    }
}

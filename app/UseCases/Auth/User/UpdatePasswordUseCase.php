<?php

declare(strict_types=1);

namespace App\UseCases\Auth\User;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use App\Services\Auth\PasswordHashService;

/**
 * ユーザーパスワード変更UseCase
 */
final class UpdatePasswordUseCase
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHashService $passwordHashService
    ) {}

    /**
     * パスワード変更処理を実行
     *
     * @param User $user
     * @param array{current_password: string, new_password: string} $data
     * @return User
     * @throws InvalidCredentialsException
     */
    public function __invoke(User $user, array $data): User
    {
        // 現在のパスワードを検証
        if (!$this->passwordHashService->check($data['current_password'], $user->password)) {
            throw new InvalidCredentialsException('現在のパスワードが正しくありません。');
        }

        // 新しいパスワードで更新
        return $this->userRepository->updatePassword($user, $data['new_password']);
    }
}

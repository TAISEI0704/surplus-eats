<?php

declare(strict_types=1);

namespace App\UseCases\Auth\User;

use App\Models\User;
use App\Services\Auth\TokenService;

/**
 * ユーザーログアウトUseCase
 */
final class LogoutUseCase
{
    public function __construct(
        private readonly TokenService $tokenService
    ) {}

    /**
     * ログアウト処理を実行
     *
     * @param User $user
     * @return void
     */
    public function __invoke(User $user): void
    {
        $this->tokenService->deleteCurrentToken($user);
    }
}

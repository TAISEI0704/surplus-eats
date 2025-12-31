<?php

declare(strict_types=1);

namespace App\UseCases\Auth\User;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Queries\Auth\UserQuery;
use App\Services\Auth\PasswordHashService;
use App\Services\Auth\TokenService;

/**
 * ユーザーログインUseCase
 */
final class LoginUseCase
{
    public function __construct(
        private readonly UserQuery $userQuery,
        private readonly PasswordHashService $passwordHashService,
        private readonly TokenService $tokenService
    ) {}

    /**
     * ログイン処理を実行
     *
     * @param array{email: string, password: string, device_name?: string} $data
     * @return array{user: User, token: string}
     * @throws InvalidCredentialsException
     */
    public function __invoke(array $data): array
    {
        // ユーザー取得
        $user = $this->userQuery->findByEmail($data['email']);

        // パスワード検証
        if (!$user || !$this->passwordHashService->check($data['password'], $user->password)) {
            throw new InvalidCredentialsException('認証情報が正しくありません。');
        }

        // トークン生成
        $deviceName = $data['device_name'] ?? 'web';
        $token = $this->tokenService->createToken($user, $deviceName);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

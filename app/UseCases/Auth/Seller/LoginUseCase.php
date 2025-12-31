<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Seller;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\Seller;
use App\Queries\Auth\SellerQuery;
use App\Services\Auth\PasswordHashService;
use App\Services\Auth\TokenService;

/**
 * 販売者ログインUseCase
 */
final class LoginUseCase
{
    public function __construct(
        private readonly SellerQuery $sellerQuery,
        private readonly PasswordHashService $passwordHashService,
        private readonly TokenService $tokenService
    ) {}

    /**
     * ログイン処理を実行
     *
     * @param array{email: string, password: string, device_name?: string} $data
     * @return array{seller: Seller, token: string}
     * @throws InvalidCredentialsException
     */
    public function __invoke(array $data): array
    {
        // 販売者取得
        $seller = $this->sellerQuery->findByEmail($data['email']);

        // パスワード検証
        if (!$seller || !$this->passwordHashService->check($data['password'], $seller->password)) {
            throw new InvalidCredentialsException('認証情報が正しくありません。');
        }

        // トークン生成
        $deviceName = $data['device_name'] ?? 'web';
        $token = $this->tokenService->createToken($seller, $deviceName);

        return [
            'seller' => $seller,
            'token' => $token,
        ];
    }
}

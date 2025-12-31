<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Seller;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\Seller;
use App\Repositories\Auth\SellerRepository;
use App\Services\Auth\PasswordHashService;

/**
 * 販売者パスワード変更UseCase
 */
final class UpdatePasswordUseCase
{
    public function __construct(
        private readonly SellerRepository $sellerRepository,
        private readonly PasswordHashService $passwordHashService
    ) {}

    /**
     * パスワード変更処理を実行
     *
     * @param Seller $seller
     * @param array{current_password: string, new_password: string} $data
     * @return Seller
     * @throws InvalidCredentialsException
     */
    public function __invoke(Seller $seller, array $data): Seller
    {
        // 現在のパスワードを検証
        if (!$this->passwordHashService->check($data['current_password'], $seller->password)) {
            throw new InvalidCredentialsException('現在のパスワードが正しくありません。');
        }

        // 新しいパスワードで更新
        return $this->sellerRepository->updatePassword($seller, $data['new_password']);
    }
}

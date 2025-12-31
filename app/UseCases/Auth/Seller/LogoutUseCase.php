<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Seller;

use App\Models\Seller;
use App\Services\Auth\TokenService;

/**
 * 販売者ログアウトUseCase
 */
final class LogoutUseCase
{
    public function __construct(
        private readonly TokenService $tokenService
    ) {}

    /**
     * ログアウト処理を実行
     *
     * @param Seller $seller
     * @return void
     */
    public function __invoke(Seller $seller): void
    {
        $this->tokenService->deleteCurrentToken($seller);
    }
}

<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Seller;

use App\Models\Seller;
use App\Repositories\Auth\SellerRepository;

/**
 * 販売者プロフィール更新UseCase
 */
final class UpdateProfileUseCase
{
    public function __construct(
        private readonly SellerRepository $sellerRepository
    ) {}

    /**
     * プロフィール更新処理を実行
     *
     * @param Seller $seller
     * @param array{name?: string, email?: string, phone?: string, address?: string, content?: string, image?: \Illuminate\Http\UploadedFile} $data
     * @return Seller
     */
    public function __invoke(Seller $seller, array $data): Seller
    {
        return $this->sellerRepository->updateProfile($seller, $data);
    }
}

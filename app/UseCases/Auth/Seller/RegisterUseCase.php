<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Seller;

use App\Models\Seller;
use App\Repositories\Auth\SellerRepository;
use App\Services\Auth\TokenService;
use Illuminate\Support\Facades\DB;

/**
 * 販売者登録UseCase
 */
final class RegisterUseCase
{
    public function __construct(
        private readonly SellerRepository $sellerRepository,
        private readonly TokenService $tokenService
    ) {}

    /**
     * 販売者登録処理を実行
     *
     * @param array{name: string, email: string, password: string, phone: string, address: string, content: string, image?: \Illuminate\Http\UploadedFile, device_name?: string} $data
     * @return array{seller: Seller, token: string}
     */
    public function __invoke(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 販売者作成
            $seller = $this->sellerRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'content' => $data['content'],
                'image' => $data['image'] ?? null,
            ]);

            // トークン生成
            $deviceName = $data['device_name'] ?? 'web';
            $token = $this->tokenService->createToken($seller, $deviceName);

            return [
                'seller' => $seller,
                'token' => $token,
            ];
        });
    }
}

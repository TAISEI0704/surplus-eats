<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Models\Seller;
use App\Services\Auth\ImageUploadService;
use App\Services\Auth\PasswordHashService;
use Illuminate\Http\UploadedFile;

/**
 * Seller書き込み専用Repository
 * INSERT/UPDATE/DELETEのみ
 */
final class SellerRepository
{
    public function __construct(
        private readonly PasswordHashService $passwordHashService,
        private readonly ImageUploadService $imageUploadService
    ) {}

    /**
     * 販売者を作成
     *
     * @param array{name: string, email: string, password: string, phone: string, address: string, content: string, image?: UploadedFile} $data
     * @return Seller
     */
    public function create(array $data): Seller
    {
        $imageFilename = null;

        if (isset($data['image'])) {
            $imageFilename = $this->imageUploadService->store($data['image'], 'images');
        }

        return Seller::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->passwordHashService->hash($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'content' => $data['content'],
            'image' => $imageFilename,
        ]);
    }

    /**
     * プロフィールを更新
     *
     * @param Seller $seller
     * @param array{name?: string, email?: string, phone?: string, address?: string, content?: string, image?: UploadedFile} $data
     * @return Seller
     */
    public function updateProfile(Seller $seller, array $data): Seller
    {
        $updateData = [];

        foreach (['name', 'email', 'phone', 'address', 'content'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (isset($data['image'])) {
            // 既存画像を削除
            if ($seller->image) {
                $this->imageUploadService->delete($seller->image, 'images');
            }

            // 新しい画像を保存
            $updateData['image'] = $this->imageUploadService->store($data['image'], 'images');
        }

        $seller->update($updateData);

        return $seller->fresh();
    }

    /**
     * パスワードを更新
     *
     * @param Seller $seller
     * @param string $newPassword
     * @return Seller
     */
    public function updatePassword(Seller $seller, string $newPassword): Seller
    {
        $seller->update([
            'password' => $this->passwordHashService->hash($newPassword),
        ]);

        return $seller->fresh();
    }
}

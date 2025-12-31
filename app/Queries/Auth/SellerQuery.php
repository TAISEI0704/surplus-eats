<?php

declare(strict_types=1);

namespace App\Queries\Auth;

use App\Models\Seller;

/**
 * Seller読み取り専用Query
 * SELECT操作のみ
 */
final class SellerQuery
{
    /**
     * メールアドレスで販売者を取得
     *
     * @param string $email
     * @return Seller|null
     */
    public function findByEmail(string $email): ?Seller
    {
        return Seller::where('email', $email)->first();
    }

    /**
     * IDで販売者を取得
     *
     * @param int $id
     * @return Seller|null
     */
    public function findById(int $id): ?Seller
    {
        return Seller::find($id);
    }
}

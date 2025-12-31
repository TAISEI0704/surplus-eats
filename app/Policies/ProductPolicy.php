<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\Seller;
use App\Models\User;

/**
 * Product認可ポリシー
 */
final class ProductPolicy
{
    /**
     * 商品を更新できるか判定
     * 販売者が自分の商品のみ更新可能
     *
     * @param Seller|User $user
     * @param Product $product
     * @return bool
     */
    public function update(Seller|User $user, Product $product): bool
    {
        // Sellerのみ更新可能、かつ自分の商品のみ
        return $user instanceof Seller && $user->id === $product->seller_id;
    }

    /**
     * 商品を削除できるか判定
     * 販売者が自分の商品のみ削除可能
     *
     * @param Seller|User $user
     * @param Product $product
     * @return bool
     */
    public function delete(Seller|User $user, Product $product): bool
    {
        // Sellerのみ削除可能、かつ自分の商品のみ
        return $user instanceof Seller && $user->id === $product->seller_id;
    }

    /**
     * 商品を閲覧できるか判定
     * 全ユーザーが閲覧可能
     *
     * @param Seller|User|null $user
     * @param Product $product
     * @return bool
     */
    public function view(?Seller|?User $user, Product $product): bool
    {
        // 全員が閲覧可能
        return true;
    }
}

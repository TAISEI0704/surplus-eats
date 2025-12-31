<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Review;
use App\Models\Seller;
use App\Models\User;

/**
 * Review認可ポリシー
 */
final class ReviewPolicy
{
    /**
     * レビューを更新できるか判定
     * ユーザーが自分のレビューのみ更新可能
     *
     * @param Seller|User $user
     * @param Review $review
     * @return bool
     */
    public function update(Seller|User $user, Review $review): bool
    {
        // Userのみ更新可能、かつ自分のレビューのみ
        return $user instanceof User && $user->id === $review->user_id;
    }

    /**
     * レビューを削除できるか判定
     * ユーザーが自分のレビューのみ削除可能
     *
     * @param Seller|User $user
     * @param Review $review
     * @return bool
     */
    public function delete(Seller|User $user, Review $review): bool
    {
        // Userのみ削除可能、かつ自分のレビューのみ
        return $user instanceof User && $user->id === $review->user_id;
    }

    /**
     * レビューを閲覧できるか判定
     * 全ユーザーが閲覧可能
     *
     * @param Seller|User|null $user
     * @param Review $review
     * @return bool
     */
    public function view(?Seller|?User $user, Review $review): bool
    {
        // 全員が閲覧可能
        return true;
    }
}

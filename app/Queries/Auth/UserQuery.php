<?php

declare(strict_types=1);

namespace App\Queries\Auth;

use App\Models\User;

/**
 * User読み取り専用Query
 * SELECT操作のみ
 */
final class UserQuery
{
    /**
     * メールアドレスでユーザーを取得
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * IDでユーザーを取得
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }
}

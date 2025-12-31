<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Facades\Hash;

/**
 * パスワードハッシュ化サービス
 */
final class PasswordHashService
{
    /**
     * パスワードをハッシュ化
     *
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * パスワードを検証
     *
     * @param string $password
     * @param string $hashedPassword
     * @return bool
     */
    public function check(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }
}

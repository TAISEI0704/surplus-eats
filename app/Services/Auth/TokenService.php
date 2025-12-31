<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\NewAccessToken;

/**
 * Sanctumトークン管理サービス
 */
final class TokenService
{
    /**
     * トークンを生成
     *
     * @param Model $user User|Seller
     * @param string $deviceName
     * @return string
     */
    public function createToken(Model $user, string $deviceName = 'web'): string
    {
        /** @var NewAccessToken $token */
        $token = $user->createToken($deviceName);

        return $token->plainTextToken;
    }

    /**
     * 現在のトークンを削除（ログアウト）
     *
     * @param Model $user User|Seller
     * @return void
     */
    public function deleteCurrentToken(Model $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * 全てのトークンを削除
     *
     * @param Model $user User|Seller
     * @return void
     */
    public function deleteAllTokens(Model $user): void
    {
        $user->tokens()->delete();
    }
}

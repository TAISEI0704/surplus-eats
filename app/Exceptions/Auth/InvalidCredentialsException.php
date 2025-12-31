<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;

/**
 * 認証情報が不正な場合の例外
 */
class InvalidCredentialsException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '認証情報が正しくありません。')
    {
        parent::__construct($message, 401);
    }
}

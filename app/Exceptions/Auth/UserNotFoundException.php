<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;

/**
 * ユーザーが見つからない場合の例外
 */
class UserNotFoundException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'ユーザーが見つかりません。')
    {
        parent::__construct($message, 404);
    }
}

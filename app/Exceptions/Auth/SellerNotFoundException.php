<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;

/**
 * 販売者が見つからない場合の例外
 */
class SellerNotFoundException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '販売者が見つかりません。')
    {
        parent::__construct($message, 404);
    }
}

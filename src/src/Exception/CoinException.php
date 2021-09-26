<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class CoinException extends Exception
{
    public static function notFoundMessage(): self
    {
        return new self('The inserted coin is not valid!');
    }
}
<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class ItemException extends Exception
{
    public static function notFoundMessage(): self
    {
        return new self('Item(s) not found!');
    }

    public static function notValidParametersMessage(): self
    {
        return new self('Item price or amount should be equal or greater than zero.');
    }
}
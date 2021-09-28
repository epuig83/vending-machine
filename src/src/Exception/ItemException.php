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

    public static function notEnoughMoney(): self
    {
        return new self('Not enough money to buy selected item.');
    }
}
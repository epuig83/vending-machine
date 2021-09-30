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

    public static function notEnoughCoinsMessage(): self
    {
        return new self('Not enough coins to buy item!');
    }

    public static function notValidAmountMessage(): self
    {
        return new self('Amount should be an integer greater than 0');
    }

    public static function notEnoughMoneyMessage(): self
    {
        return new self('Not enough money to buy selected item.');
    }
}
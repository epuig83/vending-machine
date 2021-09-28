<?php

namespace App\Utils;

class CoinHelper
{
    public static function parseString(string $value): float
    {
        return (float) $value;
    }
}
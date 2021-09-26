<?php

namespace App\Utils;

class CoinHelper
{
    public function parseString(string $value): float
    {
        return (float) $value;
    }
}
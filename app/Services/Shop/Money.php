<?php

namespace App\Services\Shop;

class Money
{
    public static function format(int|float|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}

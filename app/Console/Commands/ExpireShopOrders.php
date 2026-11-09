<?php

namespace App\Console\Commands;

use App\Services\Shop\OrderLifecycle;
use Illuminate\Console\Command;

class ExpireShopOrders extends Command
{
    protected $signature = 'shop:expire-orders';

    protected $description = 'Expire unpaid shop orders past their deadline and return their stock';

    public function handle(OrderLifecycle $lifecycle): int
    {
        $count = $lifecycle->expireDue();
        $this->info("{$count} pesanan toko kedaluwarsa.");

        return self::SUCCESS;
    }
}

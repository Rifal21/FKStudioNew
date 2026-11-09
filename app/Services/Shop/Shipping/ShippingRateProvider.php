<?php

namespace App\Services\Shop\Shipping;

use App\Models\SiteSetting;
use App\Models\UserAddress;

/**
 * Source of shipping options for physical orders. The flat-rate provider is the default;
 * an API provider (RajaOngkir/Komerce, Biteship) can be bound in AppServiceProvider later.
 */
interface ShippingRateProvider
{
    /**
     * @return list<array{code: string, service: string, label: string, cost: int, etd: ?string}>
     */
    public function rates(UserAddress $destination, int $weightGrams, SiteSetting $settings): array;
}

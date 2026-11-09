<?php

namespace App\Services\Shop;

use App\Jobs\SendShopNotification;
use App\Models\ShopOrder;

/** Queues customer/admin messages (email + WhatsApp). Failures never affect the order. */
class ShopNotifier
{
    public const PLACED = 'placed';
    public const PROOF_SUBMITTED = 'proof_submitted';
    public const PROOF_REJECTED = 'proof_rejected';
    public const PAID = 'paid';
    public const SHIPPED = 'shipped';
    public const ATTENTION = 'attention';
    public const ACCESS_RESENT = 'access_resent';

    public function orderPlaced(ShopOrder $order): void
    {
        $this->queue($order, self::PLACED);
    }

    public function proofSubmitted(ShopOrder $order): void
    {
        $this->queue($order, self::PROOF_SUBMITTED);
    }

    public function proofRejected(ShopOrder $order, string $reason): void
    {
        $this->queue($order, self::PROOF_REJECTED, ['reason' => $reason]);
    }

    public function orderPaid(ShopOrder $order): void
    {
        $this->queue($order, self::PAID);
    }

    public function orderShipped(ShopOrder $order): void
    {
        $this->queue($order, self::SHIPPED);
    }

    public function needsAttention(ShopOrder $order): void
    {
        $this->queue($order, self::ATTENTION);
    }

    public function accessResent(ShopOrder $order): void
    {
        $this->queue($order, self::ACCESS_RESENT);
    }

    private function queue(ShopOrder $order, string $event, array $context = []): void
    {
        try {
            SendShopNotification::dispatch($order->id, $event, $context);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

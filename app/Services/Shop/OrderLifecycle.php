<?php

namespace App\Services\Shop;

use App\Models\ProductVariant;
use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

/**
 * Every status change of a shop order goes through here, so Duitku callbacks and
 * admin actions share one code path (stock, vouchers, invoice, digital access, notifications).
 */
class OrderLifecycle
{
    public function __construct(
        private readonly DigitalFulfillment $fulfillment,
        private readonly ShopNotifier $notifier,
    ) {
    }

    /**
     * Marks the order paid through the given payment. Idempotent: a second call for an
     * order that is already paid only records the payment and flags a double payment.
     * Returns true when this call is the one that settled the order.
     */
    public function markPaid(ShopOrder $order, ShopPayment $payment, array $paymentAttributes = []): bool
    {
        $settled = DB::transaction(function () use ($order, $payment, $paymentAttributes) {
            $order = ShopOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $payment = ShopPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status === ShopPayment::SUCCESS) {
                return false;
            }

            $payment->update($paymentAttributes + ['status' => ShopPayment::SUCCESS, 'paid_at' => now()]);

            if ($order->payment_status === ShopOrder::PAID) {
                $this->flag($order, 'Pembayaran ganda diterima ('.$payment->channel.', '.Money::format($payment->amount).'). Periksa dan kembalikan dana bila perlu.');

                return false;
            }

            $wasClosed = in_array($order->payment_status, [ShopOrder::EXPIRED, ShopOrder::CANCELLED], true);

            if ($wasClosed && ! $this->reserveAgain($order)) {
                $this->flag($order, 'Pembayaran masuk setelah pesanan '.($order->payment_status === ShopOrder::EXPIRED ? 'kedaluwarsa' : 'dibatalkan').', tetapi stok sudah habis. Lakukan restock atau refund.');
            }

            // The voucher use was given back when the order closed; take it again.
            if ($wasClosed && $order->voucher_code && ! Voucher::reserveUse($order->voucher_code)) {
                Voucher::where('code', $order->voucher_code)->increment('used_count');
                $this->flag($order, 'Pembayaran masuk setelah pesanan ditutup, tetapi kuota voucher '.$order->voucher_code.' sudah habis. Diskon tetap terpakai; periksa apakah perlu ditagih selisihnya.');
            }

            $order->update([
                'payment_status' => ShopOrder::PAID,
                'payment_channel' => $payment->channel,
                'paid_at' => now(),
                'cancelled_at' => null,
            ]);

            $order->invoice?->update(['status' => 'Paid']);

            $missingKeys = $this->fulfillment->grant($order);

            if ($missingKeys) {
                $this->flag($order, 'Kunci lisensi habis untuk: '.implode(', ', $missingKeys).'. Tambahkan kunci lalu klik "Kirim ulang akses".');
            }

            $order->load('items');
            $order->update([
                'fulfillment_status' => $order->hasPhysicalItems() ? ShopOrder::PROCESSING : ShopOrder::FULFILLED,
            ]);

            return true;
        });

        if ($settled) {
            $order->refresh();
            DB::afterCommit(fn () => $this->notifier->orderPaid($order));

            if ($order->needs_attention) {
                DB::afterCommit(fn () => $this->notifier->needsAttention($order));
            }
        }

        return $settled;
    }

    /** Unpaid orders past their deadline. Orders with a transfer proof waiting for review are left alone. */
    public function expireDue(): int
    {
        $count = 0;

        ShopOrder::where('payment_status', ShopOrder::PENDING)
            ->where('expires_at', '<', now())
            ->pluck('id')
            ->each(function ($id) use (&$count) {
                if ($this->close(ShopOrder::find($id), ShopOrder::EXPIRED)) {
                    $count++;
                }
            });

        return $count;
    }

    public function cancel(ShopOrder $order): bool
    {
        return $this->close($order, ShopOrder::CANCELLED);
    }

    /**
     * Refund is done by hand outside the system (Duitku dashboard or bank transfer);
     * this records it, removes digital access and optionally puts stock back.
     */
    public function refund(ShopOrder $order, bool $restock, ?User $admin = null): void
    {
        DB::transaction(function () use ($order, $restock, $admin) {
            $order = ShopOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->payment_status !== ShopOrder::PAID) {
                throw new ShopException('Hanya pesanan lunas yang bisa ditandai refund.');
            }

            $this->fulfillment->revoke($order);

            if ($restock) {
                $this->releaseStock($order);
            }

            $order->update([
                'payment_status' => ShopOrder::REFUNDED,
                'refunded_at' => now(),
                'admin_notes' => trim(($order->admin_notes ? $order->admin_notes."\n" : '').'Refund dicatat '.now()->format('d/m/Y H:i').($admin ? ' oleh '.$admin->name : '').($restock ? ' (stok dikembalikan)' : '').'.'),
            ]);
            $order->invoice?->update(['status' => 'Cancelled']);
        });
    }

    private function close(?ShopOrder $order, string $status): bool
    {
        if (! $order) {
            return false;
        }

        return DB::transaction(function () use ($order, $status) {
            $order = ShopOrder::whereKey($order->id)->lockForUpdate()->firstOrFail();

            $allowed = $status === ShopOrder::EXPIRED
                ? [ShopOrder::PENDING]
                : [ShopOrder::PENDING, ShopOrder::AWAITING_VERIFICATION];

            if (! in_array($order->payment_status, $allowed, true)) {
                return false;
            }

            $this->releaseStock($order);

            if ($order->voucher_code) {
                Voucher::releaseUse($order->voucher_code);
            }

            $order->update([
                'payment_status' => $status,
                'cancelled_at' => $status === ShopOrder::CANCELLED ? now() : $order->cancelled_at,
            ]);
            $order->payments()->whereIn('status', [ShopPayment::PENDING, ShopPayment::SUBMITTED])->update(['status' => ShopPayment::EXPIRED]);
            $order->invoice?->update(['status' => 'Cancelled']);

            return true;
        });
    }

    private function releaseStock(ShopOrder $order): void
    {
        foreach ($order->items()->get() as $item) {
            if ($item->product_variant_id) {
                ProductVariant::withTrashed()->find($item->product_variant_id)?->release($item->quantity);
            }
        }
    }

    /** Stock was given back when the order closed; take it again for a late payment. */
    private function reserveAgain(ShopOrder $order): bool
    {
        $reserved = [];

        foreach ($order->items()->get() as $item) {
            $variant = $item->product_variant_id ? ProductVariant::withTrashed()->find($item->product_variant_id) : null;

            if ($variant && ! $variant->reserve($item->quantity)) {
                foreach ($reserved as [$v, $qty]) {
                    $v->release($qty);
                }

                return false;
            }

            if ($variant) {
                $reserved[] = [$variant, $item->quantity];
            }
        }

        return true;
    }

    private function flag(ShopOrder $order, string $reason): void
    {
        $order->update([
            'needs_attention' => true,
            'attention_reason' => trim(($order->attention_reason ? $order->attention_reason."\n" : '').$reason),
        ]);
    }

    public function resolveAttention(ShopOrder $order): void
    {
        $order->update(['needs_attention' => false]);
    }

    /** Retries license-key assignment after the admin added keys. Returns true when nothing is missing any more. */
    public function regrantDigital(ShopOrder $order): bool
    {
        if ($order->payment_status !== ShopOrder::PAID) {
            return false;
        }

        return DB::transaction(fn () => $this->fulfillment->grant($order) === []);
    }
}

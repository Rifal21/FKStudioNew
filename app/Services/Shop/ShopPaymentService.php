<?php

namespace App\Services\Shop;

use App\Models\ShopOrder;
use App\Models\ShopPayment;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\DuitkuService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopPaymentService
{
    public const PROOF_DISK = 'local';

    public function __construct(
        private readonly DuitkuService $duitku,
        private readonly OrderLifecycle $lifecycle,
        private readonly ShopNotifier $notifier,
    ) {
    }

    /**
     * Opens a Duitku payment for the order (a new attempt each time, so an expired link
     * can be replaced). Returns the payment page URL.
     */
    public function startDuitku(ShopOrder $order, ?string $methodCode = null): string
    {
        $id = app()->getLocale() === 'id';

        if (! $order->canPay()) {
            throw new ShopException($id ? 'Pesanan ini sudah tidak bisa dibayar.' : 'This order can no longer be paid.');
        }

        $attempt = $order->payments()->where('channel', ShopOrder::CHANNEL_DUITKU)->count() + 1;
        $merchantOrderId = $order->order_number.'-'.$attempt;
        $minutesLeft = $order->expires_at ? max(5, (int) now()->diffInMinutes($order->expires_at)) : 1440;

        $payment = $order->payments()->create([
            'channel' => ShopOrder::CHANNEL_DUITKU,
            'merchant_order_id' => $merchantOrderId,
            'method_code' => $methodCode,
            'amount' => $order->total_amount,
            'status' => ShopPayment::PENDING,
        ]);

        $order->loadMissing('items');
        $response = $this->duitku->createInvoice([
            'merchantOrderId' => $merchantOrderId,
            'paymentAmount' => $order->total_amount,
            'productDetails' => 'Pesanan '.$order->order_number.' ('.$order->items->sum('quantity').' item)',
            'email' => $order->customer_email,
            'phoneNumber' => $order->customer_phone ?? '',
            'customerName' => $order->customer_name,
            'paymentMethod' => $methodCode,
            'returnUrl' => route('shop.orders.show', $order),
            'expiryPeriod' => $minutesLeft,
        ]);

        if (! $response['success']) {
            $payment->update(['status' => ShopPayment::FAILED]);

            throw new ShopException($response['message']);
        }

        $payment->update(['payment_url' => $response['paymentUrl'], 'reference' => $response['reference']]);

        if ($order->payment_channel !== ShopOrder::CHANNEL_DUITKU) {
            $order->update(['payment_channel' => ShopOrder::CHANNEL_DUITKU]);
        }

        return $response['paymentUrl'];
    }

    /** Lets the customer pay a Duitku order by bank transfer / QRIS instead. */
    public function switchToManual(ShopOrder $order): ShopPayment
    {
        $id = app()->getLocale() === 'id';

        if (! $order->canPay() || ! SiteSetting::current()->shop_manual_payment_enabled) {
            throw new ShopException($id ? 'Transfer manual tidak tersedia untuk pesanan ini.' : 'Manual transfer is not available for this order.');
        }

        $order->update(['payment_channel' => ShopOrder::CHANNEL_MANUAL]);

        return $order->payments()->where('channel', ShopOrder::CHANNEL_MANUAL)->whereIn('status', [ShopPayment::PENDING, ShopPayment::REJECTED])->first()
            ?? $order->payments()->create([
                'channel' => ShopOrder::CHANNEL_MANUAL,
                'amount' => $order->total_amount,
                'status' => ShopPayment::PENDING,
            ]);
    }

    public function submitProof(ShopOrder $order, UploadedFile $file, array $data): ShopPayment
    {
        $id = app()->getLocale() === 'id';

        if (! $order->canPay()) {
            throw new ShopException($id ? 'Pesanan ini sudah tidak menunggu pembayaran.' : 'This order is no longer awaiting payment.');
        }

        $payment = DB::transaction(function () use ($order, $file, $data) {
            $payment = $this->switchToManual($order);

            if ($payment->hasProof() && $payment->status === ShopPayment::REJECTED) {
                // Keep the rejected attempt for the record and start a fresh one.
                $payment = $order->payments()->create([
                    'channel' => ShopOrder::CHANNEL_MANUAL,
                    'amount' => $order->total_amount,
                    'status' => ShopPayment::PENDING,
                ]);
            }

            $path = $file->storeAs(
                'shop/proofs/'.$order->order_number,
                now()->format('YmdHis').'-'.Str::random(8).'.'.strtolower($file->extension() ?: 'jpg'),
                self::PROOF_DISK,
            );

            $payment->update([
                'status' => ShopPayment::SUBMITTED,
                'proof_disk' => self::PROOF_DISK,
                'proof_path' => $path,
                'submitted_at' => now(),
                'sender_name' => $data['sender_name'] ?? null,
                'sender_bank' => $data['sender_bank'] ?? null,
                'customer_note' => $data['customer_note'] ?? null,
                'method_name' => $data['method_name'] ?? null,
            ]);

            $order->update(['payment_status' => ShopOrder::AWAITING_VERIFICATION]);

            return $payment;
        });

        $this->notifier->proofSubmitted($order->fresh());

        return $payment;
    }

    public function approve(ShopPayment $payment, User $admin): void
    {
        if ($payment->status !== ShopPayment::SUBMITTED) {
            throw new ShopException('Bukti transfer ini tidak sedang menunggu verifikasi.');
        }

        $this->lifecycle->markPaid($payment->order, $payment, [
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);
    }

    public function reject(ShopPayment $payment, User $admin, string $reason): void
    {
        if ($payment->status !== ShopPayment::SUBMITTED) {
            throw new ShopException('Bukti transfer ini tidak sedang menunggu verifikasi.');
        }

        DB::transaction(function () use ($payment, $admin, $reason) {
            $payment->update([
                'status' => ShopPayment::REJECTED,
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $order = $payment->order;
            $minExpiry = now()->addMinutes(max(60, (int) SiteSetting::current()->shop_manual_expiry_minutes));

            $order->update([
                'payment_status' => ShopOrder::PENDING,
                // Give the customer time to send a correct proof.
                'expires_at' => $order->expires_at && $order->expires_at->greaterThan($minExpiry) ? $order->expires_at : $minExpiry,
            ]);
        });

        $this->notifier->proofRejected($payment->order->fresh(), $reason);
    }
}

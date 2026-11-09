<?php

namespace App\Services\Payments;

use App\Models\ShopPayment;
use App\Services\DuitkuService;
use App\Services\Shop\OrderLifecycle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Duitku callbacks for shop orders. merchantOrderId is "<order_number>-<attempt>". */
class ShopPaymentHandler
{
    public const PREFIX = 'SO-';

    public function __construct(
        private readonly DuitkuService $duitku,
        private readonly OrderLifecycle $lifecycle,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        $merchantOrderId = (string) $request->merchantOrderId;
        $payment = ShopPayment::with('order')->where('merchant_order_id', $merchantOrderId)->first();

        if (! $payment || ! $payment->order) {
            Log::error('Duitku Callback: shop payment not found', ['merchantOrderId' => $merchantOrderId]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        $payment->update(['raw_callback' => $request->except('signature')]);

        if ((string) $request->resultCode !== '00') {
            if ($payment->status === ShopPayment::PENDING) {
                $payment->update(['status' => ShopPayment::FAILED]);
            }

            return response()->json(['message' => 'OK']);
        }

        if ((int) $request->amount !== $payment->amount) {
            Log::warning('Duitku Callback: shop amount mismatch', ['merchantOrderId' => $merchantOrderId, 'expected' => $payment->amount, 'received' => $request->amount]);

            return response()->json(['message' => 'Amount mismatch'], 400);
        }

        if (! $this->duitku->confirmsPaid($merchantOrderId, $payment->amount)) {
            return response()->json(['message' => 'Payment not confirmed'], 400);
        }

        $this->lifecycle->markPaid($payment->order, $payment, [
            'reference' => $request->reference ?: $payment->reference,
            'method_code' => $request->paymentCode ?: $payment->method_code,
        ]);

        return response()->json(['message' => 'OK']);
    }
}

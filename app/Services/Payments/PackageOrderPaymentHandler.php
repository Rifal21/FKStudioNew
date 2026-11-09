<?php

namespace App\Services\Payments;

use App\Models\PackageOrder;
use App\Services\DuitkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Duitku callbacks for website package orders. merchantOrderId is the order UUID,
 * with a "-final" suffix for the remaining balance (pelunasan) of a DP order.
 */
class PackageOrderPaymentHandler
{
    public function __construct(private readonly DuitkuService $duitku)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $merchantOrderId = (string) $request->merchantOrderId;
        $isFinalPayment = str_ends_with($merchantOrderId, '-final');
        $orderId = $isFinalPayment ? substr($merchantOrderId, 0, -strlen('-final')) : $merchantOrderId;

        $order = PackageOrder::find($orderId);

        if (! $order) {
            Log::error('Duitku Callback: Order not found', ['order_id' => $orderId]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        if ((string) $request->resultCode !== '00') {
            Log::info('Duitku Callback: Payment not successful', ['merchantOrderId' => $merchantOrderId, 'resultCode' => $request->resultCode]);

            return response()->json(['message' => 'OK']);
        }

        $expected = $isFinalPayment
            ? (int) $order->remaining_balance
            : (int) ($order->payment_scheme === 'dp' ? $order->dp_amount : $order->total_amount);

        if ((int) $request->amount !== $expected) {
            Log::warning('Duitku Callback: amount mismatch', ['merchantOrderId' => $merchantOrderId, 'expected' => $expected, 'received' => $request->amount]);

            return response()->json(['message' => 'Amount mismatch'], 400);
        }

        if (! $this->duitku->confirmsPaid($merchantOrderId, $expected)) {
            return response()->json(['message' => 'Payment not confirmed'], 400);
        }

        DB::transaction(function () use ($order, $request, $isFinalPayment) {
            $order = PackageOrder::whereKey($order->id)->lockForUpdate()->first();

            if ($isFinalPayment) {
                if ($order->status === 'completed') {
                    return;
                }

                $order->update(['status' => 'completed', 'payment_reference' => $request->reference]);
                $order->finalInvoice()->update(['status' => 'Paid']);

                return;
            }

            if (in_array($order->status, ['paid', 'completed'], true)) {
                return;
            }

            $order->update(['status' => 'paid', 'payment_reference' => $request->reference]);
            $order->invoice?->update(['status' => 'Paid']);
            $order->provisionTenant();
        });

        return response()->json(['message' => 'OK']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use App\Models\Invoice;
use App\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DuitkuController extends Controller
{
    protected $duitkuService;

    public function __construct(DuitkuService $duitkuService)
    {
        $this->duitkuService = $duitkuService;
    }

    public function callback(Request $request)
    {
        Log::info('Duitku Callback Received', $request->all());

        if (!$this->duitkuService->validateCallback($request)) {
            return response()->json(['message' => 'Invalid Signature'], 400);
        }

        $orderId = $request->merchantOrderId;
        $resultCode = $request->resultCode; // 00 for success

        $isFinalPayment = str_ends_with($orderId, '-final');
        $cleanOrderId = $isFinalPayment ? str_replace('-final', '', $orderId) : $orderId;

        $order = PackageOrder::find($cleanOrderId);

        if (!$order) {
            Log::error('Duitku Callback: Order not found', ['order_id' => $cleanOrderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($order, $resultCode, $request, $isFinalPayment) {
            if ($resultCode == '00') {
                if ($isFinalPayment) {
                    $order->update([
                        'status' => 'completed',
                        'payment_reference' => $request->reference,
                    ]);

                    if ($order->final_invoice_id) {
                        $order->finalInvoice()->update([
                            'status' => 'Paid',
                        ]);
                    }
                } else {
                    $order->update([
                        'status' => 'paid',
                        'payment_reference' => $request->reference,
                    ]);

                    if ($order->invoice) {
                        $order->invoice->update([
                            'status' => 'Paid',
                        ]);
                    }
                }

                // Automatically provision tenant
                $order->provisionTenant();
            } else {
                Log::info('Duitku Callback: Payment not successful', ['resultCode' => $resultCode]);
            }
        });

        return response()->json(['message' => 'OK'], 200);
    }
}

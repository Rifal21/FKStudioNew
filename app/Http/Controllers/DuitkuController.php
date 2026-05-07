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

        $order = PackageOrder::find($orderId);

        if (!$order) {
            Log::error('Duitku Callback: Order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($order, $resultCode, $request) {
            if ($resultCode == '00') {
                $order->update([
                    'status' => 'paid',
                    'payment_reference' => $request->reference,
                ]);

                if ($order->invoice) {
                    $order->invoice->update([
                        'status' => 'Paid',
                    ]);
                }

                // Automatically provision tenant
                $order->provisionTenant();
            } else {
                // If failed or expired, we might want to update status but usually we wait or handle accordingly
                // For now just log it or update to failed if it's a specific failure code
                Log::info('Duitku Callback: Payment not successful', ['resultCode' => $resultCode]);
            }
        });

        return response()->json(['message' => 'OK'], 200);
    }
}

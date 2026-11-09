<?php

namespace App\Http\Controllers;

use App\Services\DuitkuService;
use App\Services\Payments\PackageOrderPaymentHandler;
use App\Services\Payments\ShopPaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DuitkuController extends Controller
{
    public function __construct(private readonly DuitkuService $duitkuService)
    {
    }

    public function callback(Request $request)
    {
        Log::info('Duitku Callback Received', $request->except('signature'));

        if (! $this->duitkuService->validateCallback($request)) {
            return response()->json(['message' => 'Invalid Signature'], 400);
        }

        // Shop orders are numbered "SO-..."; everything else is a website package order (UUID).
        $handler = str_starts_with((string) $request->merchantOrderId, ShopPaymentHandler::PREFIX)
            ? app(ShopPaymentHandler::class)
            : app(PackageOrderPaymentHandler::class);

        return $handler->handle($request);
    }
}

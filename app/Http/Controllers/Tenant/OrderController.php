<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('tenant.dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('tenant.dashboard.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function downloadInvoice(Order $order)
    {
        $brandingName = tenant('branding_name') ?? 'FKStudio';
        $setting = \App\Models\TenantSetting::first();
        
        $pdf = Pdf::loadView('tenant.templates.sales.invoice-pdf', compact('order', 'brandingName', 'setting'));
        
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }
}

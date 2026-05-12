<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\Ticket;
use App\Models\SiteSetting;
use App\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $duitkuService;

    public function __construct(DuitkuService $duitkuService)
    {
        $this->duitkuService = $duitkuService;
    }
    public function show(Package $package)
    {
        $settings = SiteSetting::first();
        $totalAmount = (float) str_replace(['Rp', '.', ','], '', $package->price);
        $duitkuMethods = $this->duitkuService->getPaymentMethods($totalAmount);
        
        return view('landing.checkout', compact('package', 'settings', 'duitkuMethods'));
    }

    public function process(Request $request, Package $package)
    {
        $request->validate([
            'payment_method' => 'required',
            'branding_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:package_orders,subdomain|regex:/^[a-z0-9-]+$/',
        ], [
            'subdomain.unique' => 'Subdomain ini sudah digunakan oleh pengguna lain.',
            'subdomain.regex' => 'Subdomain hanya boleh berisi huruf kecil, angka, dan strip (-).',
        ]);

        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($package, $request) {
            $order = PackageOrder::create([
                'user_id' => Auth::id(),
                'package_id' => $package->id,
                'package_name' => $package->getTranslation('name'),
                'package_price' => $package->price,
                'payment_method' => $request->payment_method,
                'total_amount' => (float) str_replace(['Rp', '.', ','], '', $package->price),
                'status' => 'pending',
                'branding_name' => $request->branding_name,
                'subdomain' => strtolower($request->subdomain),
            ]);

            // Generate Invoice Automatically
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $lastInvoice = \App\Models\Invoice::where('invoice_number', 'LIKE', 'INV-%')
                ->latest()
                ->first();

            if ($lastInvoice) {
                $parts = explode('-', $lastInvoice->invoice_number);
                $lastNumber = (int) end($parts);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $invoiceNumber = "INV-$year$month$day-FKS-$nextNumber";

            $invoice = \App\Models\Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_name' => Auth::user()->name,
                'client_email' => Auth::user()->email,
                'client_address' => '-',
                'issue_date' => now(),
                'due_date' => now()->addDays(3),
                'status' => 'Unpaid',
                'notes' => 'Generated automatically from Package Order #' . $order->id,
                'total_amount' => $order->total_amount,
                'discount' => 0,
                'tax' => 0,
                'discount_type' => 'fixed',
                'tax_type' => 'fixed',
            ]);

            $invoice->items()->create([
                'description' => 'Package: ' . $order->package_name,
                'quantity' => 1,
                'unit_price' => $order->total_amount,
                'subtotal' => $order->total_amount,
            ]);

            $order->update(['invoice_id' => $invoice->id]);
            
            return $order;
        });

        $paymentMethod = $request->payment_method;
        $duitkuMethod = null;

        if (str_starts_with($paymentMethod, 'Duitku|')) {
            $parts = explode('|', $paymentMethod);
            $paymentMethod = $parts[0];
            $duitkuMethod = $parts[1];
        }

        if ($paymentMethod === 'Duitku') {
            $duitkuResponse = $this->duitkuService->createInvoice([
                'merchantOrderId' => $order->id,
                'paymentAmount' => $order->total_amount,
                'productDetails' => 'Pembelian Paket: ' . $order->package_name,
                'email' => Auth::user()->email,
                'customerName' => Auth::user()->name,
                'paymentMethod' => $duitkuMethod,
            ]);

            if ($duitkuResponse['success']) {
                $order->update([
                    'payment_url' => $duitkuResponse['paymentUrl'],
                    'payment_reference' => $duitkuResponse['reference'],
                ]);

                return redirect($duitkuResponse['paymentUrl']);
            } else {
                return redirect()->back()->with('error', $duitkuResponse['message']);
            }
        }

        return redirect()->route('checkout.success', $order->id)->with('success', 'Order placed successfully!');
    }

    public function success(PackageOrder $order)
    {
        // If payment via Duitku and status is already paid, redirect to my websites
        if ($order->status === 'paid' && str_starts_with($order->payment_method, 'Duitku')) {
            return redirect()->route('user.websites')->with('success', 'Pembayaran berhasil dikonfirmasi secara otomatis! Website Anda sedang disiapkan.');
        }

        $settings = SiteSetting::first();
        return view('landing.checkout-success', compact('order', 'settings'));
    }

    public function confirmPayment(Request $request, PackageOrder $order)
    {
        $request->validate([
            'message' => 'required',
            'attachment' => 'required|image|max:2048',
        ]);

        // Upload to Nextcloud (using the trait from CmsController or similar)
        // For simplicity, I'll use local storage or the user might have Nextcloud trait available
        
        $path = null;
        if ($request->hasFile('attachment')) {
            // Using a simple local upload for now, or I can copy the Nextcloud logic
            $path = $request->file('attachment')->store('payment_proofs', 'public');
        }

        Ticket::create([
            'user_id' => Auth::id(),
            'package_order_id' => $order->id,
            'subject' => 'Konfirmasi Pembayaran - ' . $order->package_name,
            'message' => $request->message,
            'attachment' => $path,
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'Konfirmasi pembayaran telah dikirim. Admin akan segera memprosesnya.');
    }

    public function userOrders()
    {
        $orders = PackageOrder::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $settings = SiteSetting::first();
        
        return view('landing.user.orders', compact('orders', 'settings'));
    }

    public function userWebsites()
    {
        $orders = PackageOrder::where('user_id', Auth::id())
            ->whereNotNull('tenant_id')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $settings = SiteSetting::first();
        
        // For local dev, use fkstudio.test. For production, use env config.
        $baseDomain = env('TENANCY_BASE_DOMAIN', 'fkstudio.id');
        
        return view('landing.user.websites', compact('orders', 'settings', 'baseDomain'));
    }
}

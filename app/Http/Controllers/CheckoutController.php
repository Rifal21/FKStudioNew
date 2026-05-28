<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageOrder;
use App\Models\Ticket;
use App\Models\SiteSetting;
use App\Services\DuitkuService;
use App\Services\IDCloudHostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $duitkuService;
    protected $idchService;

    public function __construct(DuitkuService $duitkuService, IDCloudHostService $idchService)
    {
        $this->duitkuService = $duitkuService;
        $this->idchService = $idchService;
    }

    public function checkDomainAvailability(Request $request)
    {
        $request->validate([
            'domain' => 'required|string'
        ]);

        $result = $this->idchService->checkAvailability($request->domain);
        return response()->json($result);
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'package_id'   => 'required|string',
            'buy_domain'   => 'nullable|string',
            'domain_name'  => 'nullable|string',
        ]);

        $voucher = \App\Models\Voucher::where('code', strtoupper(trim($request->voucher_code)))->first();

        if (!$voucher || !$voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid atau sudah kadaluwarsa.'
            ]);
        }

        $package = Package::find($request->package_id);
        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tidak ditemukan.'
            ]);
        }

        $packagePrice = (float) str_replace(['Rp', '.', ','], '', $package->price);
        $domainPrice = 0.0;

        if ($request->buy_domain == '1' && $request->domain_name) {
            $domainPrice = $this->idchService->getDomainPrice(trim($request->domain_name));
        }

        $settings = SiteSetting::first();
        $taxRate = (float) ($settings->tax_rate ?? 11.00);

        $subtotal = $packagePrice + $domainPrice;
        $discount = (float) $voucher->calculateDiscount($subtotal);
        $taxableAmount = max(0.0, $subtotal - $discount);
        $tax = round($taxableAmount * ($taxRate / 100), 2);
        $total = $taxableAmount + $tax;

        return response()->json([
            'success'         => true,
            'code'            => $voucher->code,
            'type'            => $voucher->type,
            'value'           => $voucher->value,
            'discount_amount' => $discount,
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax,
            'total_amount'    => $total,
            'message'         => 'Voucher berhasil diaplikasikan!'
        ]);
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
            'payment_scheme' => 'required|in:full,dp',
            'website_name'   => 'required|string|max:255',
            'website_url'    => 'nullable|string|max:255',
            'business_type'  => 'required|string|max:100',
            'custom_business_type' => 'required_if:business_type,Lainnya|nullable|string|max:100',
            'client_notes'   => 'nullable|string|max:2000',
            'buy_domain'     => 'nullable|boolean',
            'domain_name'    => 'required_if:buy_domain,1|nullable|string|max:255',
            'voucher_code'   => 'nullable|string',
        ], [
            'website_name.required' => 'Nama website / brand wajib diisi.',
            'business_type.required' => 'Jenis bisnis wajib dipilih.',
            'custom_business_type.required_if' => 'Sebutkan jenis website custom yang ingin Anda buat.',
            'domain_name.required_if' => 'Nama domain wajib diisi jika Anda memilih untuk membeli domain baru.',
        ]);

        $paymentScheme = $request->payment_scheme;

        $order = DB::transaction(function () use ($package, $request, $paymentScheme) {
            $packagePrice = (float) str_replace(['Rp', '.', ','], '', $package->price);
            $domainPrice = 0.0;
            $buyDomain = $request->buy_domain == '1';
            $domainName = $buyDomain ? strtolower(trim($request->domain_name)) : null;

            if ($buyDomain && $domainName) {
                $domainPrice = $this->idchService->getDomainPrice($domainName);
            }

            $subtotal = $packagePrice + $domainPrice;

            // Apply voucher backend-side if code was entered
            $voucherCode = null;
            $discountAmount = 0.0;
            if ($request->voucher_code) {
                $voucher = \App\Models\Voucher::where('code', strtoupper(trim($request->voucher_code)))->first();
                if ($voucher && $voucher->isValid()) {
                    $voucherCode = $voucher->code;
                    $discountAmount = (float) $voucher->calculateDiscount($subtotal);
                    $voucher->increment('used_count');
                }
            }

            // Calculate tax on subtotal after discount
            $settings = SiteSetting::first();
            $taxRate = (float) ($settings->tax_rate ?? 11.00);

            $taxableAmount = max(0.0, $subtotal - $discountAmount);
            $taxAmount = round($taxableAmount * ($taxRate / 100), 2);
            $totalAmount = $taxableAmount + $taxAmount;

            if ($paymentScheme === 'dp') {
                $dpAmount = $totalAmount * 0.5;
                $remainingBalance = $totalAmount - $dpAmount;
            } else {
                $dpAmount = 0.0;
                $remainingBalance = 0.0;
            }

            $websiteUrl = $request->website_url;
            if ($websiteUrl) {
                // Hapus akhiran .fkstudio.id jika terketik lengkap
                $subdomain = str_replace('.fkstudio.id', '', strtolower(trim($websiteUrl)));
                
                // Ganti spasi dan titik menjadi strip (-)
                $subdomain = preg_replace('/[\s\.]+/', '-', $subdomain);
                
                // Hapus karakter ilegal selain huruf, angka, dan strip
                $subdomain = preg_replace('/[^a-z0-9\-]/', '', $subdomain);
                
                // Rapikan strip beruntun
                $subdomain = preg_replace('/-+/', '-', $subdomain);
                
                // Hapus strip di awal/akhir
                $subdomain = trim($subdomain, '-');

                // Gabungkan dengan domain utama
                $websiteUrl = $subdomain ? $subdomain . '.fkstudio.id' : null;
            }

            $businessType = $request->business_type;
            if ($businessType === 'Lainnya' && $request->custom_business_type) {
                $businessType = trim($request->custom_business_type);
            }

            $order = PackageOrder::create([
                'user_id'        => Auth::id(),
                'package_id'     => $package->id,
                'package_name'   => $package->getTranslation('name'),
                'package_price'  => $package->price,
                'subtotal_amount'=> $subtotal,
                'voucher_code'   => $voucherCode,
                'discount_amount'=> $discountAmount,
                'tax_amount'     => $taxAmount,
                'website_name'   => $request->website_name,
                'website_url'    => $buyDomain ? $domainName : $websiteUrl,
                'business_type'  => $businessType,
                'client_notes'   => $request->client_notes,
                'payment_method' => $request->payment_method,
                'payment_scheme' => $paymentScheme,
                'dp_amount'      => $dpAmount,
                'remaining_balance' => $remainingBalance,
                'total_amount'   => $totalAmount,
                'status'         => 'pending',
                'work_status'    => 'pending',
                'buy_domain'     => $buyDomain,
                'domain_name'    => $domainName,
                'domain_price'   => $buyDomain ? $domainPrice : null,
                'domain_status'  => $buyDomain ? 'pending' : null,
            ]);

            // Auto-generate Invoice (for upfront payment)
            $upfrontPayment = $paymentScheme === 'dp' ? $dpAmount : $totalAmount;

            $year      = date('Y');
            $month     = date('m');
            $day       = date('d');
            $lastInv   = \App\Models\Invoice::where('invoice_number', 'LIKE', 'INV-%')->latest()->first();
            $lastNum   = 0;
            if ($lastInv) {
                $parts = explode('-', $lastInv->invoice_number);
                $lastNum = (int) end($parts);
            }
            $nextNum   = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            $invNumber = "INV-$year$month$day-FKS-$nextNum";

            $invoice = \App\Models\Invoice::create([
                'invoice_number' => $invNumber,
                'client_name'    => Auth::user()->name,
                'client_email'   => Auth::user()->email,
                'client_address' => '-',
                'issue_date'     => now(),
                'due_date'       => now()->addDays(3),
                'status'         => 'Unpaid',
                'notes'          => $paymentScheme === 'dp' ? 'Down Payment (50%) for Order #' . $order->id : 'Full Payment (100%) for Order #' . $order->id,
                'total_amount'   => $upfrontPayment,
                'discount'       => $paymentScheme === 'dp' ? $discountAmount * 0.5 : $discountAmount,
                'tax'            => $paymentScheme === 'dp' ? $taxAmount * 0.5 : $taxAmount,
                'discount_type'  => 'fixed',
                'tax_type'       => 'fixed',
            ]);

            // Item 1: Paket Website
            $item1Price = $paymentScheme === 'dp' ? $packagePrice * 0.5 : $packagePrice;
            $item1Desc = $paymentScheme === 'dp' ? 'Down Payment (50%) - Paket Website: ' . $order->package_name : 'Paket Website: ' . $order->package_name;

            $invoice->items()->create([
                'description' => $item1Desc,
                'quantity'    => 1,
                'unit_price'  => $item1Price,
                'subtotal'    => $item1Price,
            ]);

            // Item 2: Tambahan Registrasi Domain (Jika Beli)
            if ($buyDomain && $domainName) {
                $item2Price = $paymentScheme === 'dp' ? $domainPrice * 0.5 : $domainPrice;
                $item2Desc = $paymentScheme === 'dp' ? 'Down Payment (50%) - Registrasi Domain Baru: ' . $domainName : 'Registrasi Domain Baru: ' . $domainName . ' (1 Tahun)';
                
                $invoice->items()->create([
                    'description' => $item2Desc,
                    'quantity'    => 1,
                    'unit_price'  => $item2Price,
                    'subtotal'    => $item2Price,
                ]);
            }

            if ($paymentScheme === 'dp') {
                $order->update([
                    'invoice_id' => $invoice->id,
                    'dp_invoice_id' => $invoice->id
                ]);
            } else {
                $order->update(['invoice_id' => $invoice->id]);
            }

            return $order;
        });

        // Duitku payment gateway
        $paymentMethod = $request->payment_method;
        $duitkuMethod  = null;

        if (str_starts_with($paymentMethod, 'Duitku|')) {
            [$paymentMethod, $duitkuMethod] = explode('|', $paymentMethod);
        }

        if ($paymentMethod === 'Duitku') {
            $upfrontAmount = $order->payment_scheme === 'dp' ? $order->dp_amount : $order->total_amount;
            $productName = $order->payment_scheme === 'dp' 
                ? 'DP (50%) Pemesanan Website: ' . $order->website_name 
                : 'Pemesanan Website: ' . $order->website_name;

            $duitkuResponse = $this->duitkuService->createInvoice([
                'merchantOrderId' => $order->id,
                'paymentAmount'   => $upfrontAmount,
                'productDetails'  => $productName . ' (' . $order->package_name . ')',
                'email'           => Auth::user()->email,
                'customerName'    => Auth::user()->name,
                'paymentMethod'   => $duitkuMethod,
            ]);

            if ($duitkuResponse['success']) {
                $order->update([
                    'payment_url'       => $duitkuResponse['paymentUrl'],
                    'payment_reference' => $duitkuResponse['reference'],
                ]);
                return redirect($duitkuResponse['paymentUrl']);
            }

            return redirect()->back()->with('error', $duitkuResponse['message']);
        }

        return redirect()->route('checkout.success', $order->id)->with('success', 'Pemesanan berhasil!');
    }

    public function success(PackageOrder $order)
    {
        if ($order->status === 'paid' && str_starts_with($order->payment_method ?? '', 'Duitku')) {
            return redirect()->route('user.orders')->with('success', 'Pembayaran berhasil! Pesanan website Anda sedang diproses.');
        }

        $settings = SiteSetting::first();
        return view('landing.checkout-success', compact('order', 'settings'));
    }

    public function confirmPayment(Request $request, PackageOrder $order)
    {
        $request->validate([
            'message'    => 'required',
            'attachment' => 'required|image|max:2048',
        ]);

        $path = $request->file('attachment')->store('payment_proofs', 'public');

        $isPelunasanPayment = ($order->status === 'paid' && $order->payment_scheme === 'dp' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid');
        $subject = $isPelunasanPayment 
            ? 'Konfirmasi Pelunasan - ' . $order->package_name 
            : 'Konfirmasi Pembayaran - ' . $order->package_name;

        Ticket::create([
            'user_id'          => Auth::id(),
            'package_order_id' => $order->id,
            'subject'          => $subject,
            'message'          => $request->message,
            'attachment'       => $path,
            'status'           => 'open',
        ]);

        return redirect()->back()->with('success', 'Konfirmasi pembayaran terkirim. Admin akan segera memprosesnya.');
    }

    public function showPelunasan(PackageOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->payment_scheme !== 'dp' || $order->status !== 'paid' || !$order->final_invoice_id) {
            return redirect()->route('user.orders')->with('error', 'Pesanan ini tidak dalam tahap pelunasan.');
        }

        if ($order->finalInvoice && $order->finalInvoice->status === 'Paid') {
            return redirect()->route('user.orders')->with('success', 'Pelunasan untuk pesanan ini sudah dibayar.');
        }

        $settings = SiteSetting::first();
        $totalAmount = (float) $order->remaining_balance;
        $duitkuMethods = $this->duitkuService->getPaymentMethods($totalAmount);

        return view('landing.checkout-pelunasan', compact('order', 'settings', 'duitkuMethods'));
    }

    public function processPelunasan(Request $request, PackageOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => 'Silakan pilih metode pembayaran.',
        ]);

        $paymentMethod = $request->payment_method;

        $order->update([
            'payment_method' => $paymentMethod
        ]);

        $duitkuMethod = null;
        if (str_starts_with($paymentMethod, 'Duitku|')) {
            [$paymentMethod, $duitkuMethod] = explode('|', $paymentMethod);
        }

        if ($paymentMethod === 'Duitku') {
            $duitkuResponse = $this->duitkuService->createInvoice([
                'merchantOrderId' => $order->id . '-final',
                'paymentAmount'   => (int) $order->remaining_balance,
                'productDetails'  => 'Pelunasan (50%) Pemesanan Website: ' . $order->website_name . ' (' . $order->package_name . ')',
                'email'           => Auth::user()->email,
                'customerName'    => Auth::user()->name,
                'paymentMethod'   => $duitkuMethod,
            ]);

            if ($duitkuResponse['success']) {
                $order->update([
                    'payment_url'       => $duitkuResponse['paymentUrl'],
                    'payment_reference' => $duitkuResponse['reference'],
                ]);
                return redirect($duitkuResponse['paymentUrl']);
            }

            return redirect()->back()->with('error', $duitkuResponse['message']);
        }

        return redirect()->route('checkout.success', $order->id)->with('success', 'Metode pembayaran pelunasan berhasil dipilih!');
    }

    public function userOrders()
    {
        $orders   = PackageOrder::where('user_id', Auth::id())
            ->with(['package', 'invoice', 'dpInvoice', 'finalInvoice', 'tickets'])
            ->orderBy('created_at', 'desc')
            ->get();
        $settings = SiteSetting::first();

        return view('landing.user.orders', compact('orders', 'settings'));
    }

    public function userWebsites()
    {
        $orders = PackageOrder::where('user_id', Auth::id())
            ->where('work_status', 'completed')
            ->whereNotNull('website_url')
            ->orderBy('updated_at', 'desc')
            ->get();
        $settings = SiteSetting::first();

        return view('landing.user.websites', compact('orders', 'settings'));
    }
}

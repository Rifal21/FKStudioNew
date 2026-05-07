<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class CartController extends Controller
{
    public function add(Request $request, Product $product)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => (float)$product->price,
                'image' => $product->getUrl($product->image),
                'quantity' => 1
            ];
        }
        
        Session::put('cart', $cart);
        
        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'message' => 'Produk berhasil ditambahkan ke keranjang.'
        ]);
    }

    public function index()
    {
        $cart = Session::get('cart', []);
        $total = array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
        
        $brandingName = tenant('branding_name') ?? 'FKStudio';
        return view('tenant.templates.sales.cart', compact('cart', 'total', 'brandingName'));
    }

    public function remove($id)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('tenant.home')->with('error', 'Keranjang Anda kosong.');
        }
        
        $total = array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
        
        $brandingName = tenant('branding_name') ?? 'FKStudio';
        return view('tenant.templates.sales.checkout', compact('cart', 'total', 'brandingName'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'payment_method' => 'required'
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) return redirect()->route('tenant.home');

        $total = array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $orderNumber = 'ORD-' . strtoupper(uniqid());

        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $cart, $total, $orderNumber) {
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'status' => 'pending'
            ]);

            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost_price' => $product ? $product->cost_price : 0,
                    'subtotal' => $item['price'] * $item['quantity']
                ]);
            }

            return $order;
        });

        Session::forget('cart');

        return redirect()->route('tenant.order.success', $order->id);
    }

    public function success(Order $order)
    {
        $brandingName = tenant('branding_name') ?? 'FKStudio';
        return view('tenant.templates.sales.order-success', compact('order', 'brandingName'));
    }

    public function downloadInvoice(Order $order)
    {
        $brandingName = tenant('branding_name') ?? 'FKStudio';
        $setting = \App\Models\TenantSetting::first();
        
        $pdf = Pdf::loadView('tenant.templates.sales.invoice-pdf', compact('order', 'brandingName', 'setting'));
        
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }
}

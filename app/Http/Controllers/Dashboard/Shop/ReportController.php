<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/** Revenue and PPN. Only paid orders count; refunded orders drop out. */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $this->month($request);
        $orders = $this->paidOrders($month)->get();

        $topProducts = ShopOrderItem::query()
            ->whereIn('shop_order_id', $orders->pluck('id'))
            ->selectRaw('product_name, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $totals = [
            'orders' => $orders->count(),
            'gross' => (int) $orders->sum('total_amount'),
            'goods' => (int) $orders->sum(fn ($o) => $o->goodsAmount()),
            'shipping' => (int) $orders->sum('shipping_cost'),
            'unique' => (int) $orders->sum('unique_code'),
            'tax_base' => (int) $orders->sum('tax_base'),
            'tax' => (int) $orders->sum('tax_amount'),
        ];

        return view('dashboard.shop.reports.index', compact('month', 'orders', 'totals', 'topProducts'));
    }

    public function taxCsv(Request $request)
    {
        $month = $this->month($request);
        $orders = $this->paidOrders($month)->get();
        $filename = 'laporan-ppn-toko-'.$month->format('Y-m').'.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal lunas', 'No. pesanan', 'Pelanggan', 'Email', 'Nilai barang (termasuk PPN)', 'DPP', 'Tarif PPN (%)', 'PPN', 'Ongkir', 'Kode unik', 'Total dibayar', 'Metode']);

            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->paid_at?->timezone('Asia/Jakarta')->format('Y-m-d H:i'),
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    $order->goodsAmount(),
                    $order->tax_base,
                    $order->tax_rate,
                    $order->tax_amount,
                    $order->shipping_cost,
                    $order->unique_code,
                    $order->total_amount,
                    $order->payment_channel,
                ]);
            }

            fputcsv($out, ['TOTAL', '', '', '', $orders->sum(fn ($o) => $o->goodsAmount()), $orders->sum('tax_base'), '', $orders->sum('tax_amount'), $orders->sum('shipping_cost'), $orders->sum('unique_code'), $orders->sum('total_amount'), '']);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function month(Request $request): CarbonImmutable
    {
        $value = (string) $request->query('month', '');

        return preg_match('/^\d{4}-\d{2}$/', $value)
            ? CarbonImmutable::createFromFormat('!Y-m', $value, 'Asia/Jakarta')
            : CarbonImmutable::now('Asia/Jakarta')->startOfMonth();
    }

    private function paidOrders(CarbonImmutable $month)
    {
        return ShopOrder::where('payment_status', ShopOrder::PAID)
            ->whereBetween('paid_at', [$month->startOfMonth()->utc(), $month->endOfMonth()->utc()])
            ->orderBy('paid_at');
    }
}

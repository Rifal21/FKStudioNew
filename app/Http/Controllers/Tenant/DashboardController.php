<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::whereIn('status', ['paid', 'completed'])->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalProducts = Product::count();

        // Calculate Profit: Total Revenue - Total Cost of Sold Items
        $totalCost = \App\Models\OrderItem::whereHas('order', function($q) {
            $q->whereIn('status', ['paid', 'completed']);
        })->get()->sum(function($item) {
            return $item->cost_price * $item->quantity;
        });

        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        // Monthly Revenue for a simple chart (last 6 months)
        $monthlyRevenue = Order::whereIn('status', ['paid', 'completed'])
            ->select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%M') as month")
            )
            ->groupBy('month')
            ->orderBy('created_at', 'asc')
            ->take(6)
            ->get();

        $siteType = \App\Models\TenantSetting::first()?->site_type;

        return view('tenant.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'totalProfit',
            'profitMargin',
            'recentOrders',
            'monthlyRevenue',
            'siteType'
        ));
    }
}

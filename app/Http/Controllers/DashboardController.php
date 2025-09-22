<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $latestUsers = User::latest()->take(5)->get();

        // Prepare months (for sales chart if enabled later)
        $months = [];
        $salesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $salesData[] = 0; // Placeholder
        }

        // Top 5 products by quantity sold
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $productsChartData = [
            'labels' => $topProducts->pluck('name')->toArray(),
            'data'   => $topProducts->pluck('total_quantity')->toArray(),
        ];

        return view('dashboard', compact(
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'totalCategories',
            'recentOrders',
            'latestUsers',
            'productsChartData'
        ));
    }
}

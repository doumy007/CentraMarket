<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products_count' => Product::count(),
            'categories_count' => Category::count(),
            'users_count' => User::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'orders_count' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];

        $latestProducts = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestProducts'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\ActivityLog;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'customer')->count();
        $totalVendors = Vendor::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalSales = Order::where('order_status', '!=', 'cancelled')->sum('subtotal');

        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $recentUsers = User::latest()->take(5)->get();
        $recentActivity = ActivityLog::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalVendors',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'totalSales',
            'recentOrders',
            'recentUsers',
            'recentActivity'
        ));
    }
}

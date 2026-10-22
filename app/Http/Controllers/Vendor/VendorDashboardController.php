<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\OrderItem;
use App\Models\Product;

class VendorDashboardController extends Controller
{
    private function getVendor(): Vendor
    {
        return Vendor::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'shop_name' => Auth::user()->name . "'s Shop",
                'shop_slug' => \Illuminate\Support\Str::slug(Auth::user()->name . '-' . rand(100, 999)),
                'status' => 'active',
                'kyc_status' => 'verified',
                'commission_rate' => 10.00,
            ]
        );
    }

    public function index()
    {
        $vendor = $this->getVendor();

        $totalProducts = Product::where('vendor_id', $vendor->id)->count();
        $totalOrders = OrderItem::where('vendor_id', $vendor->id)->distinct('order_id')->count();
        $grossSales = OrderItem::where('vendor_id', $vendor->id)->sum('total');

        $pendingShipments = OrderItem::where('vendor_id', $vendor->id)
            ->whereIn('vendor_status', ['pending', 'processing', 'packed'])
            ->count();

        $recentItems = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->take(6)
            ->get();

        $lowStockProducts = Product::where('vendor_id', $vendor->id)
            ->where('stock_quantity', '<=', 5)
            ->get();

        return view('vendor.dashboard', compact(
            'vendor',
            'totalProducts',
            'totalOrders',
            'grossSales',
            'pendingShipments',
            'recentItems',
            'lowStockProducts'
        ));
    }
}

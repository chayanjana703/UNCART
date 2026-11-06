<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Vendor;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;

class VendorAnalyticsController extends Controller
{
    private function getVendor(): Vendor
    {
        return Vendor::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'shop_name' => Auth::user()->name . "'s Shop",
                'shop_slug' => Str::slug(Auth::user()->name . '-' . rand(100, 999)),
                'status' => 'active',
                'kyc_status' => 'verified',
            ]
        );
    }

    public function index()
    {
        $vendor = $this->getVendor();

        $grossSales = OrderItem::where('vendor_id', $vendor->id)->sum('total');
        $totalOrders = OrderItem::where('vendor_id', $vendor->id)->distinct('order_id')->count();

        $topProducts = Product::where('vendor_id', $vendor->id)
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get();

        $recentReviews = ProductReview::whereIn('product_id', function ($query) use ($vendor) {
            $query->select('id')->from('products')->where('vendor_id', $vendor->id);
        })->with(['product', 'user'])->latest()->take(6)->get();

        $avgRating = ProductReview::whereIn('product_id', function ($query) use ($vendor) {
            $query->select('id')->from('products')->where('vendor_id', $vendor->id);
        })->avg('rating') ?: 5.0;

        return view('vendor.analytics.index', compact(
            'vendor',
            'grossSales',
            'totalOrders',
            'topProducts',
            'recentReviews',
            'avgRating'
        ));
    }

    public function exportCsv()
    {
        $vendor = $this->getVendor();

        $items = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order', 'product'])
            ->latest()
            ->get();

        $csv = "Order Number,Product Name,Unit Price,Quantity,Total,Status,Date\n";
        foreach ($items as $item) {
            $date = $item->created_at ? $item->created_at->format('Y-m-d H:i') : '';
            $orderNum = $item->order ? $item->order->order_number : 'N/A';
            $pName = str_replace('"', '""', $item->product_name);
            $csv .= "\"{$orderNum}\",\"{$pName}\",{$item->price},{$item->quantity},{$item->total},\"{$item->vendor_status}\",\"{$date}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendor_sales_report_' . date('Y_m_d') . '.csv"',
        ]);
    }
}

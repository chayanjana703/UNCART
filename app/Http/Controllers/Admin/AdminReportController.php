<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;

class AdminReportController extends Controller
{
    public function index()
    {
        $salesMetrics = [
            'gross_sales' => Order::where('order_status', '!=', 'cancelled')->sum('subtotal'),
            'total_tax' => Order::where('order_status', '!=', 'cancelled')->sum('tax_amount'),
            'total_shipping' => Order::where('order_status', '!=', 'cancelled')->sum('shipping_amount'),
            'total_discounts' => Order::where('order_status', '!=', 'cancelled')->sum('discount_amount'),
            'net_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];

        $topPerformingProducts = Product::orderBy('sales_count', 'desc')->take(10)->get();
        $vendorSummaries = Vendor::withCount('products')->get();
        $customerCount = User::where('role', 'customer')->count();

        return view('admin.reports.index', compact(
            'salesMetrics',
            'topPerformingProducts',
            'vendorSummaries',
            'customerCount'
        ));
    }

    public function exportSales()
    {
        $orders = Order::where('payment_status', 'paid')->with('user')->get();

        $csv = "Order Number,Date,Customer,Subtotal,Discount,Tax,Shipping,Total\n";
        foreach ($orders as $o) {
            $customerName = $o->user ? str_replace('"', '""', $o->user->name) : 'Guest';
            $date = $o->created_at ? $o->created_at->format('Y-m-d') : '';
            $csv .= "\"{$o->order_number}\",\"{$date}\",\"{$customerName}\",{$o->subtotal},{$o->discount_amount},{$o->tax_amount},{$o->shipping_amount},{$o->total_amount}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopicart_sales_report_' . date('Y_m_d') . '.csv"',
        ]);
    }
}

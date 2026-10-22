<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ActivityLog;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('pages.orders', compact('orders'));
    }

    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product.vendor', 'items.vendor'])
            ->firstOrFail();

        return view('pages.order-detail', compact('order'));
    }

    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product'])
            ->firstOrFail();

        return view('pages.order-track', compact('order'));
    }

    public function invoice($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product.vendor', 'user'])
            ->firstOrFail();

        return view('pages.invoice', compact('order'));
    }

    public function cancel(Request $request, $orderNumber)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled as it has already progressed past processing.');
        }

        $order->order_status = 'cancelled';
        $order->cancel_reason = $request->input('cancel_reason');
        $order->save();

        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
            $item->product->decrement('sales_count', $item->quantity);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Order Cancelled',
            'description' => "Order #{$order->order_number} cancelled by customer. Reason: {$order->cancel_reason}",
        ]);

        return back()->with('success', "Order #{$order->order_number} has been cancelled successfully.");
    }

    public function requestReturn(Request $request, $orderNumber)
    {
        $request->validate([
            'return_reason' => 'required|string|max:500',
        ]);

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$order->canBeReturned()) {
            return back()->with('error', 'This order is not eligible for return.');
        }

        $order->return_status = 'requested';
        $order->return_reason = $request->input('return_reason');
        $order->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Return Requested',
            'description' => "Return requested for order #{$order->order_number}. Reason: {$order->return_reason}",
        ]);

        return back()->with('success', 'Your return request has been lodged. Our support team will review it within 24 hours.');
    }
}

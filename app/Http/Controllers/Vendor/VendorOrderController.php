<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;

class VendorOrderController extends Controller
{
    private function getVendor(): Vendor
    {
        return Vendor::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $vendor = $this->getVendor();

        $query = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product']);

        if ($status = $request->input('status')) {
            $query->where('vendor_status', $status);
        }

        $orderItems = $query->latest()->paginate(12)->withQueryString();

        return view('vendor.orders.index', compact('orderItems', 'vendor'));
    }

    public function show($id)
    {
        $vendor = $this->getVendor();

        $orderItem = OrderItem::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->firstOrFail();

        return view('vendor.orders.show', compact('orderItem', 'vendor'));
    }

    public function updateStatus(Request $request, $id)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'status' => 'required|in:processing,packed,shipped,delivered,cancelled',
            'carrier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $orderItem = OrderItem::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $orderItem->vendor_status = $validated['status'];
        $orderItem->save();

        // Also update parent order status if relevant
        $order = $orderItem->order;
        if ($order) {
            if ($validated['status'] === 'shipped') {
                $order->order_status = 'shipped';
                if (!empty($validated['tracking_number'])) {
                    $order->tracking_number = $validated['tracking_number'];
                }
                if (!empty($validated['carrier_name'])) {
                    $order->carrier_name = $validated['carrier_name'];
                }
                $order->save();

                Notification::create([
                    'user_id' => $order->user_id,
                    'title' => "Package Shipped (#{$order->order_number})",
                    'message' => "{$orderItem->product_name} has been dispatched with " . ($order->carrier_name ?: 'Courier'),
                    'type' => 'order',
                    'link' => route('orders.track', $order->order_number),
                ]);
            } elseif ($validated['status'] === 'delivered') {
                $order->order_status = 'delivered';
                $order->delivered_at = now();
                $order->save();
            }
        }

        return back()->with('success', "Order item status changed to " . ucfirst($validated['status']) . ".");
    }

    public function shippingLabel($id)
    {
        $vendor = $this->getVendor();

        $orderItem = OrderItem::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->firstOrFail();

        return view('vendor.orders.shipping-label', compact('orderItem', 'vendor'));
    }
}

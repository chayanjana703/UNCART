<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Notification;
use App\Models\ActivityLog;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($search = $request->input('q')) {
            $query->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('order_status', $status);
        }

        if ($returnStatus = $request->input('return_status')) {
            $query->where('return_status', $returnStatus);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'items.vendor'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'order_status' => 'required|in:placed,confirmed,processing,packed,shipped,out_for_delivery,delivered,cancelled,returned',
            'carrier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order->order_status = $validated['order_status'];
        if (!empty($validated['carrier_name'])) {
            $order->carrier_name = $validated['carrier_name'];
        }
        if (!empty($validated['tracking_number'])) {
            $order->tracking_number = $validated['tracking_number'];
        }
        if ($validated['order_status'] === 'delivered') {
            $order->delivered_at = now();
        }
        $order->save();

        Notification::create([
            'user_id' => $order->user_id,
            'title' => "Order Status Updated (#{$order->order_number})",
            'message' => "Your order status is now: " . ucfirst(str_replace('_', ' ', $validated['order_status'])),
            'type' => 'order',
            'link' => route('orders.show', $order->order_number),
        ]);

        return back()->with('success', "Order #{$order->order_number} status updated.");
    }

    public function processRefund(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->payment_status = 'refunded';
        $order->return_status = 'refunded';
        $order->save();

        Notification::create([
            'user_id' => $order->user_id,
            'title' => "Refund Processed for #{$order->order_number}",
            'message' => "A full refund of $" . number_format($order->total_amount, 2) . " has been credited to your original payment source.",
            'type' => 'order',
            'link' => route('orders.show', $order->order_number),
        ]);

        return back()->with('success', "Refund of $" . number_format($order->total_amount, 2) . " processed successfully.");
    }

    public function disputes(Request $request)
    {
        $query = Order::with(['user', 'items.product'])
            ->whereNotNull('return_status');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('return_reason', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('return_status', $status);
        }

        $disputes = $query->latest('updated_at')->paginate(15)->withQueryString();

        $stats = [
            'total' => Order::whereNotNull('return_status')->count(),
            'requested' => Order::where('return_status', 'requested')->count(),
            'approved' => Order::where('return_status', 'approved')->count(),
            'refunded' => Order::where('return_status', 'refunded')->count(),
            'rejected' => Order::where('return_status', 'rejected')->count(),
        ];

        return view('admin.orders.disputes', compact('disputes', 'stats'));
    }

    public function resolveDispute(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'return_status' => 'required|in:approved,rejected,refunded',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $order->return_status = $validated['return_status'];
        if ($validated['return_status'] === 'refunded') {
            $order->payment_status = 'refunded';
            $order->order_status = 'returned';
        }
        if (!empty($validated['admin_notes'])) {
            $order->notes = ($order->notes ? $order->notes . "\n" : '') . "[Return Resolution: " . now()->format('Y-m-d H:i') . "] " . $validated['admin_notes'];
        }
        $order->save();

        Notification::create([
            'user_id' => $order->user_id,
            'title' => "Dispute / Return Update for #{$order->order_number}",
            'message' => "Your return request status has been updated to: " . ucfirst($validated['return_status']),
            'type' => 'order',
            'link' => route('orders.show', $order->order_number),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Return Dispute Resolved',
            'description' => "Order #{$order->order_number} return marked as {$validated['return_status']}",
        ]);

        return back()->with('success', "Dispute for Order #{$order->order_number} updated to {$validated['return_status']}.");
    }

    public function exportCsv()
    {
        $orders = Order::with('user')->latest()->get();

        $csv = "Order Number,Customer Name,Customer Email,Total Amount,Status,Payment Status,Payment Method,Date\n";
        foreach ($orders as $o) {
            $cName = $o->user ? str_replace('"', '""', $o->user->name) : 'Guest';
            $cEmail = $o->user ? $o->user->email : 'N/A';
            $date = $o->created_at ? $o->created_at->format('Y-m-d H:i') : '';
            $csv .= "\"{$o->order_number}\",\"{$cName}\",\"{$cEmail}\",{$o->total_amount},\"{$o->order_status}\",\"{$o->payment_status}\",\"{$o->payment_method}\",\"{$date}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopicart_orders_' . date('Y_m_d') . '.csv"',
        ]);
    }
}

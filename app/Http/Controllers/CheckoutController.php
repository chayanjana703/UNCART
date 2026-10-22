<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\StoreSetting;
use App\Models\Notification;
use App\Models\ActivityLog;

class CheckoutController extends Controller
{
    private function getCartItems()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())
                ->where('is_saved_for_later', false)
                ->with(['product.vendor'])
                ->get();
        }
        return CartItem::where('session_id', session()->getId())
            ->whereNull('user_id')
            ->where('is_saved_for_later', false)
            ->with(['product.vendor'])
            ->get();
    }

    public function index()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your shopping cart is empty.');
        }

        $subtotal = $cartItems->sum(fn($i) => $i->subtotal);

        // Coupon calculation
        $couponCode = session('coupon_code');
        $discountAmount = 0;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }
        }

        $freeShippingThreshold = (float) StoreSetting::get('free_shipping_threshold', 99.00);
        $standardShippingRate = (float) StoreSetting::get('shipping_standard_rate', 12.00);
        $shippingAmount = ($subtotal >= $freeShippingThreshold) ? 0.00 : $standardShippingRate;
        $taxRate = (float) StoreSetting::get('tax_rate', 8.5);
        $taxAmount = round(($subtotal - $discountAmount) * ($taxRate / 100), 2);
        $totalAmount = max(0, $subtotal - $discountAmount + $shippingAmount + $taxAmount);

        $savedAddresses = Auth::check() ? Auth::user()->addresses : collect();
        $defaultShippingAddress = $savedAddresses->where('is_default', true)->first() ?? $savedAddresses->first();

        return view('pages.checkout', compact(
            'cartItems',
            'subtotal',
            'discountAmount',
            'shippingAmount',
            'taxAmount',
            'totalAmount',
            'savedAddresses',
            'defaultShippingAddress'
        ));
    }

    public function process(Request $request)
    {
        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty.');
        }

        $validated = $request->validate([
            // Shipping Details
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address_line1' => 'required|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',

            // Billing Option
            'billing_same_as_shipping' => 'nullable|boolean',
            'billing_name' => 'nullable|required_if:billing_same_as_shipping,0|string|max:255',
            'billing_phone' => 'nullable|required_if:billing_same_as_shipping,0|string|max:20',
            'billing_address_line1' => 'nullable|required_if:billing_same_as_shipping,0|string|max:255',
            'billing_city' => 'nullable|required_if:billing_same_as_shipping,0|string|max:100',
            'billing_state' => 'nullable|required_if:billing_same_as_shipping,0|string|max:100',
            'billing_postal_code' => 'nullable|required_if:billing_same_as_shipping,0|string|max:20',
            'billing_country' => 'nullable|required_if:billing_same_as_shipping,0|string|max:100',

            // Shipping & Payment Method
            'shipping_method' => 'required|string|in:standard,express,overnight',
            'payment_method' => 'required|string|in:card,upi,wallet,cod',
            'notes' => 'nullable|string|max:500',
        ]);

        $subtotal = $cartItems->sum(fn($i) => $i->subtotal);

        // Calculate discount
        $couponCode = session('coupon_code');
        $discountAmount = 0;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('used_count');
            }
        }

        // Calculate shipping
        $shippingMethod = match ($validated['shipping_method']) {
            'express' => 'Express Courier',
            'overnight' => 'Priority Overnight',
            default => 'Standard Carrier',
        };

        $freeThreshold = (float) StoreSetting::get('free_shipping_threshold', 99.00);
        $shippingCost = match ($validated['shipping_method']) {
            'express' => 25.00,
            'overnight' => 45.00,
            default => ($subtotal >= $freeThreshold ? 0.00 : 12.00),
        };

        $taxRate = (float) StoreSetting::get('tax_rate', 8.5);
        $taxAmount = round(($subtotal - $discountAmount) * ($taxRate / 100), 2);
        $totalAmount = max(0, $subtotal - $discountAmount + $shippingCost + $taxAmount);

        $shippingAddress = [
            'name' => $validated['shipping_name'],
            'phone' => $validated['shipping_phone'],
            'address_line1' => $validated['shipping_address_line1'],
            'address_line2' => $validated['shipping_address_line2'] ?? null,
            'city' => $validated['shipping_city'],
            'state' => $validated['shipping_state'],
            'postal_code' => $validated['shipping_postal_code'],
            'country' => $validated['shipping_country'],
        ];

        $billingAddress = $request->boolean('billing_same_as_shipping', true) ? $shippingAddress : [
            'name' => $validated['billing_name'] ?? $shippingAddress['name'],
            'phone' => $validated['billing_phone'] ?? $shippingAddress['phone'],
            'address_line1' => $validated['billing_address_line1'] ?? $shippingAddress['address_line1'],
            'address_line2' => null,
            'city' => $validated['billing_city'] ?? $shippingAddress['city'],
            'state' => $validated['billing_state'] ?? $shippingAddress['state'],
            'postal_code' => $validated['billing_postal_code'] ?? $shippingAddress['postal_code'],
            'country' => $validated['billing_country'] ?? $shippingAddress['country'],
        ];

        // Ensure user account exists
        $user = Auth::user();
        if (!$user) {
            // Find or create customer account for guest checkout
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'guest-' . session()->getId() . '@shopicart.com'],
                [
                    'name' => $validated['shipping_name'],
                    'phone' => $validated['shipping_phone'],
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'customer',
                ]
            );
        }

        $orderNumber = 'ORD-' . strtoupper(Str::random(5)) . rand(100, 999);
        $txnId = 'TXN-' . strtoupper(Str::random(8)) . '-' . strtoupper($validated['payment_method']);

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_amount' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'coupon_code' => $couponCode,
            'shipping_method' => $shippingMethod,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'payment_method' => $validated['payment_method'],
            'payment_status' => ($validated['payment_method'] === 'cod') ? 'pending' : 'paid',
            'transaction_id' => $txnId,
            'order_status' => 'placed',
            'notes' => $validated['notes'] ?? null,
            'carrier_name' => 'DHL Global Logistics',
            'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
        ]);

        // Create Order Items and decrease product inventory
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'vendor_id' => $item->product->vendor_id,
                'product_name' => $item->product->name,
                'product_image' => $item->product->featured_image,
                'price' => $item->product->current_price,
                'quantity' => $item->quantity,
                'total' => $item->subtotal,
                'options' => $item->options,
                'vendor_status' => 'pending',
            ]);

            // Decrement stock & increment sales count
            $item->product->decrement('stock_quantity', $item->quantity);
            $item->product->increment('sales_count', $item->quantity);
        }

        // Clear active cart items and coupon
        CartItem::whereIn('id', $cartItems->pluck('id'))->delete();
        session()->forget(['coupon_code', 'coupon_discount']);

        // Create in-app notification
        Notification::create([
            'user_id' => $user->id,
            'title' => "Order #{$order->order_number} Confirmed!",
            'message' => "Thank you for your order of $" . number_format($order->total_amount, 2) . ". We are preparing your shipment.",
            'type' => 'order',
            'link' => route('orders.show', $order->order_number),
            'is_read' => false,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Order Placed',
            'description' => "Order #{$order->order_number} placed for $" . number_format($order->total_amount, 2),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['items.product', 'user'])->firstOrFail();
        return view('pages.confirmation', compact('order'));
    }
}

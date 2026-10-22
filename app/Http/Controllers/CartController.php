<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\StoreSetting;

class CartController extends Controller
{
    private function getCartItemsQuery()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id());
        }
        return CartItem::where('session_id', session()->getId())->whereNull('user_id');
    }

    public function index()
    {
        $cartItems = $this->getCartItemsQuery()
            ->where('is_saved_for_later', false)
            ->with(['product.category', 'product.vendor'])
            ->get();

        $savedItems = $this->getCartItemsQuery()
            ->where('is_saved_for_later', true)
            ->with(['product.category', 'product.vendor'])
            ->get();

        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->subtotal;
        }

        // Apply coupon from session
        $couponCode = session('coupon_code');
        $coupon = null;
        $discountAmount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget(['coupon_code', 'coupon_discount']);
                $coupon = null;
            }
        }

        $freeShippingThreshold = (float) StoreSetting::get('free_shipping_threshold', 99.00);
        $standardShippingRate = (float) StoreSetting::get('shipping_standard_rate', 12.00);
        $taxRate = (float) StoreSetting::get('tax_rate', 8.5);

        $shippingAmount = ($subtotal >= $freeShippingThreshold || $subtotal == 0) ? 0.00 : $standardShippingRate;
        $taxAmount = round(($subtotal - $discountAmount) * ($taxRate / 100), 2);
        $totalAmount = max(0, $subtotal - $discountAmount + $shippingAmount + $taxAmount);

        return view('pages.cart', compact(
            'cartItems',
            'savedItems',
            'subtotal',
            'discountAmount',
            'shippingAmount',
            'taxAmount',
            'totalAmount',
            'coupon',
            'freeShippingThreshold'
        ));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'options' => 'nullable|array',
        ]);

        $productId = $validated['product_id'];
        $quantity = (int) ($validated['quantity'] ?? 1);
        $options = $validated['options'] ?? null;

        $product = Product::findOrFail($productId);

        if (!$product->in_stock) {
            return back()->with('error', 'This product is currently out of stock.');
        }

        $query = $this->getCartItemsQuery()
            ->where('product_id', $productId)
            ->where('is_saved_for_later', false);

        $existing = $query->first();

        if ($existing) {
            $existing->quantity += $quantity;
            if ($options) {
                $existing->options = $options;
            }
            $existing->save();
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'options' => $options,
                'is_saved_for_later' => false,
            ]);
        }

        if ($request->wantsJson()) {
            $count = $this->getCartItemsQuery()->where('is_saved_for_later', false)->sum('quantity');
            return response()->json([
                'success' => true,
                'message' => "{$product->name} added to your cart!",
                'cart_count' => $count,
            ]);
        }

        return redirect()->route('cart.index')->with('success', "{$product->name} was added to your cart!");
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $this->getCartItemsQuery()->where('id', $validated['id'])->firstOrFail();
        $item->quantity = $validated['quantity'];
        $item->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item_subtotal' => number_format($item->subtotal, 2),
            ]);
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove($id)
    {
        $item = $this->getCartItemsQuery()->where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with('info', 'Item removed from cart.');
    }

    public function saveForLater($id)
    {
        $item = $this->getCartItemsQuery()->where('id', $id)->firstOrFail();
        $item->is_saved_for_later = true;
        $item->save();

        return back()->with('success', 'Item saved for later review.');
    }

    public function moveToCart($id)
    {
        $item = $this->getCartItemsQuery()->where('id', $id)->firstOrFail();
        $item->is_saved_for_later = false;
        $item->save();

        return back()->with('success', 'Item moved back to your active cart.');
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code')));
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        // Check subtotal
        $cartItems = $this->getCartItemsQuery()->where('is_saved_for_later', false)->get();
        $subtotal = $cartItems->sum(fn($i) => $i->subtotal);

        if (!$coupon->isValidForSubtotal($subtotal)) {
            return back()->with('error', "Coupon {$code} requires a minimum order of $" . number_format($coupon->min_spend, 2));
        }

        session(['coupon_code' => $coupon->code]);
        return back()->with('success', "Coupon '{$coupon->code}' applied successfully!");
    }

    public function removeCoupon()
    {
        session()->forget(['coupon_code', 'coupon_discount']);
        return back()->with('info', 'Coupon removed from order.');
    }
}

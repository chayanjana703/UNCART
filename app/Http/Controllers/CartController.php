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

    public function getDrawerData()
    {
        if (str_contains(session()->previousUrl(), 'drawer-data') || str_contains(session()->previousUrl(), 'live-search')) {
            session()->setPreviousUrl(route('cart.index'));
        }

        $cartItems = $this->getCartItemsQuery()
            ->where('is_saved_for_later', false)
            ->with(['product.category', 'product.vendor'])
            ->get();

        $subtotal = $cartItems->sum(fn($i) => $i->subtotal);
        $freeShippingThreshold = (float) StoreSetting::get('free_shipping_threshold', 99.00);
        $progress = min(100, round(($subtotal / max(1, $freeShippingThreshold)) * 100));
        $remaining = max(0, $freeShippingThreshold - $subtotal);

        $couponCode = session('coupon_code');
        $couponDiscount = 0;
        $couponObj = null;
        if ($couponCode) {
            $couponObj = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($couponObj && $couponObj->isValidForSubtotal($subtotal)) {
                $couponDiscount = $couponObj->calculateDiscount($subtotal);
            } else {
                session()->forget(['coupon_code', 'coupon_discount']);
                $couponObj = null;
            }
        }
        $finalTotal = max(0, $subtotal - $couponDiscount);

        $formattedItems = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'image' => $item->product->primary_image,
                'category' => $item->product->category->name ?? 'Curated',
                'price' => (float) $item->product->price,
                'price_formatted' => '$' . number_format($item->product->price, 2),
                'quantity' => $item->quantity,
                'subtotal_formatted' => '$' . number_format($item->subtotal, 2),
            ];
        });

        return response()->json([
            'items' => $formattedItems,
            'count' => (int) $cartItems->sum('quantity'),
            'subtotal' => '$' . number_format($subtotal, 2),
            'subtotal_raw' => $subtotal,
            'free_shipping_threshold' => $freeShippingThreshold,
            'free_shipping_progress' => $progress,
            'remaining_for_free_shipping' => '$' . number_format($remaining, 2),
            'remaining_for_free_shipping_raw' => $remaining,
            'unlocked_free_shipping' => $subtotal >= $freeShippingThreshold,
            'coupon_code' => $couponObj ? $couponObj->code : null,
            'discount_amount' => $couponDiscount,
            'discount_formatted' => '-$' . number_format($couponDiscount, 2),
            'final_total' => '$' . number_format($finalTotal, 2),
            'final_total_raw' => $finalTotal,
        ]);
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

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getDrawerData();
        }

        return redirect($this->getSafeRedirectUrl($request))->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request, $id)
    {
        $item = $this->getCartItemsQuery()->where('id', $id)->firstOrFail();
        $item->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getDrawerData();
        }

        return redirect($this->getSafeRedirectUrl($request))->with('info', 'Item removed from cart.');
    }

    public function saveForLater(Request $request, $id)
    {
        $item = $this->getCartItemsQuery()->where('id', $id)->firstOrFail();
        $item->is_saved_for_later = true;
        $item->save();

        return redirect($this->getSafeRedirectUrl($request))->with('success', 'Item saved for later review.');
    }

    public function moveToCart(Request $request, $id)
    {
        $item = $this->getCartItemsQuery()->where('id', $id)->firstOrFail();
        $item->is_saved_for_later = false;
        $item->save();

        return redirect($this->getSafeRedirectUrl($request))->with('success', 'Item moved back to your active cart.');
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code')));
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        $redirectUrl = $this->getSafeRedirectUrl($request);

        if (!$coupon) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code.'], 422);
            }
            return redirect($redirectUrl)->with('error', 'Invalid or expired coupon code.');
        }

        // Check subtotal
        $cartItems = $this->getCartItemsQuery()->where('is_saved_for_later', false)->get();
        $subtotal = $cartItems->sum(fn($i) => $i->subtotal);

        if (!$coupon->isValidForSubtotal($subtotal)) {
            $msg = "Coupon {$code} requires a minimum order of $" . number_format($coupon->min_spend, 2);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect($redirectUrl)->with('error', $msg);
        }

        session(['coupon_code' => $coupon->code]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Coupon '{$coupon->code}' applied successfully!",
                'coupon_code' => $coupon->code,
                'drawer' => $this->getDrawerData()->original
            ]);
        }

        return redirect($redirectUrl)->with('success', "Coupon '{$coupon->code}' applied successfully!");
    }

    public function removeCoupon(Request $request)
    {
        session()->forget(['coupon_code', 'coupon_discount']);
        $redirectUrl = $this->getSafeRedirectUrl($request);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed from order.',
                'drawer' => $this->getDrawerData()->original
            ]);
        }

        return redirect($redirectUrl)->with('info', 'Coupon removed from order.');
    }

    protected function getSafeRedirectUrl(Request $request): string
    {
        if ($target = $request->input('redirect_to')) {
            return $target;
        }

        $referer = $request->header('Referer');
        if ($referer && !str_contains($referer, 'drawer-data') && !str_contains($referer, 'live-search') && !str_contains($referer, 'ajax')) {
            return $referer;
        }

        return route('cart.index');
    }
}

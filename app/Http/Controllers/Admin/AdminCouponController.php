<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\PromotionalBanner;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();
        $banners = PromotionalBanner::orderBy('sort_order')->get();

        return view('admin.coupons.index', compact('coupons', 'banners'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:coupons,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:1',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_spend' => $validated['min_spend'] ?? 0.00,
            'max_discount' => $validated['max_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? 100,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', "Coupon '{$validated['code']}' created successfully.");
    }

    public function toggleCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return back()->with('success', "Coupon status toggled.");
    }

    public function destroyCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return back()->with('info', "Coupon removed.");
    }

    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'badge' => 'nullable|string|max:100',
            'image' => 'required|string|url',
            'link' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        PromotionalBanner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'image' => $validated['image'],
            'link' => $validated['link'] ?? '/shop',
            'button_text' => $validated['button_text'] ?? 'Shop Now',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', "Promotional banner created.");
    }

    public function destroyBanner($id)
    {
        $banner = PromotionalBanner::findOrFail($id);
        $banner->delete();

        return back()->with('info', "Banner removed.");
    }
}

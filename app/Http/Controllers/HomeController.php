<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\PromotionalBanner;
use App\Models\ProductReview;

class HomeController extends Controller
{
    public function index()
    {
        $banners = PromotionalBanner::where('is_active', true)->orderBy('sort_order')->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('status', 'approved')
            ->where('is_featured', true)
            ->with(['category', 'vendor'])
            ->take(8)
            ->get();

        $flashSaleProducts = Product::where('is_active', true)
            ->where('status', 'approved')
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->with(['category', 'vendor'])
            ->take(4)
            ->get();

        $newArrivals = Product::where('is_active', true)
            ->where('status', 'approved')
            ->with(['category', 'vendor'])
            ->latest()
            ->take(8)
            ->get();

        $topVendors = Vendor::where('status', 'active')
            ->where('kyc_status', 'verified')
            ->withCount('products')
            ->take(4)
            ->get();

        $featuredReviews = ProductReview::where('status', 'approved')
            ->where('rating', '>=', 4)
            ->with(['product', 'user'])
            ->latest()
            ->take(6)
            ->get();

        return view('pages.home', compact(
            'banners',
            'categories',
            'featuredProducts',
            'flashSaleProducts',
            'newArrivals',
            'topVendors',
            'featuredReviews'
        ));
    }
}

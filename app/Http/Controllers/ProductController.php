<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->where('status', 'approved')
            ->with(['category', 'vendor']);

        // Keyword Search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($catSlug = $request->input('category')) {
            $category = Category::where('slug', $catSlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Vendor Filter
        if ($vendorSlug = $request->input('vendor')) {
            $vendor = Vendor::where('shop_slug', $vendorSlug)->first();
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            }
        }

        // Price Range Filter
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // Rating Filter
        if ($rating = $request->input('rating')) {
            $query->where('rating_avg', '>=', (float) $rating);
        }

        // In Stock Filter
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'popular':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->withCount('products')->get();
        $vendors = Vendor::where('status', 'active')->get();

        return view('pages.shop', compact('products', 'categories', 'vendors', 'sort'));
    }

    public function liveSearch(Request $request)
    {
        if (str_contains(session()->previousUrl(), 'live-search') || str_contains(session()->previousUrl(), 'drawer-data')) {
            session()->setPreviousUrl(route('home'));
        }

        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        $products = Product::where('is_active', true)
            ->where('status', 'approved')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('category')
            ->take(6)
            ->get();

        $total = Product::where('is_active', true)
            ->where('status', 'approved')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->count();

        $results = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'image' => $p->primary_image,
                'price' => '$' . number_format($p->price, 2),
                'compare_price' => ($p->compare_price && $p->compare_price > $p->price) ? '$' . number_format($p->compare_price, 2) : null,
                'category' => $p->category->name ?? 'General',
                'rating' => number_format($p->rating, 1),
            ];
        });

        return response()->json([
            'results' => $results,
            'total' => $total,
        ]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->with(['category', 'vendor.user', 'reviews.user'])
            ->firstOrFail();

        // Increment views
        $product->increment('views_count');

        // Rating breakdown calculations
        $totalReviews = $product->reviews->count();
        $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($product->reviews as $rev) {
            $r = (int) $rev->rating;
            if (isset($ratingDistribution[$r])) {
                $ratingDistribution[$r]++;
            }
        }

        // Related Products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->take(4)
            ->get();

        return view('pages.product-detail', compact(
            'product',
            'relatedProducts',
            'totalReviews',
            'ratingDistribution'
        ));
    }

    public function compare(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $compareProducts = Product::whereIn('id', $ids)
            ->where('is_active', true)
            ->with(['category', 'vendor'])
            ->take(4)
            ->get();

        return view('pages.compare', compact('compareProducts'));
    }
}
